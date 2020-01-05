<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 2:32 PM
 */

namespace Tfboe\FmLib\Service\RankingSystem;


use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Support\Facades\Config;
use Tfboe\FmLib\Entity\Helpers\AutomaticInstanceGeneration;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;
use Tfboe\FmLib\Entity\PlayerInterface;
use Tfboe\FmLib\Entity\RankingSystemChangeInterface;
use Tfboe\FmLib\Entity\RankingSystemInterface as EntityRankingSystemInterface;
use Tfboe\FmLib\Entity\RankingSystemListEntryInterface;
use Tfboe\FmLib\Entity\RankingSystemListInterface;
use Tfboe\FmLib\Entity\TournamentInterface;
use Tfboe\FmLib\Entity\Traits\RankingSystemChange;
use Tfboe\FmLib\Exceptions\Internal;
use Tfboe\FmLib\Exceptions\PreconditionFailedException;
use Tfboe\FmLib\Helpers\DateTimeHelper;
use Tfboe\FmLib\Service\ObjectCreatorServiceInterface;


/**
 * Class RankingSystemService
 * @package Tfboe\FmLib\Service\RankingSystemService
 * @SuppressWarnings(PHPMD) TODO: refactor this class and remove suppress warnings
 */
abstract class RankingSystemService implements RankingSystemInterface
{
//<editor-fold desc="Fields">
  /** @var EntityManagerInterface */
  private $entityManager;
  /** @var TimeServiceInterface */
  protected $timeService;
  /** @var EntityComparerInterface */
  private $entityComparer;
  /**
   * @var RankingSystemChangeInterface[][]
   * first key: tournament hierarchy entity id
   * second key: player id
   */
  private $changes;
  /**
   * @var RankingSystemChangeInterface[][]
   * first key: tournament hierarchy entity id
   * second key: player id
   */
  private $oldChanges;
  /**
   * List of ranking systems for which update ranking got already called, indexed by id
   * @var RankingSystemService[]
   */
  private $updateRankingCalls;

  /** @var ObjectCreatorServiceInterface */
  private $objectCreatorService;

  /** @var bool */
  private $doFlushAndForget;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * RankingSystemService constructor.
   * @param EntityManagerInterface $entityManager
   * @param TimeServiceInterface $timeService
   * @param EntityComparerInterface $entityComparer
   * @param ObjectCreatorServiceInterface $objectCreatorService
   */
  public function __construct(EntityManagerInterface $entityManager, TimeServiceInterface $timeService,
                              EntityComparerInterface $entityComparer,
                              ObjectCreatorServiceInterface $objectCreatorService)
  {
    $this->entityManager = $entityManager;
    $this->timeService = $timeService;
    $this->entityComparer = $entityComparer;
    $this->changes = [];
    $this->oldChanges = [];
    $this->updateRankingCalls = [];
    $this->objectCreatorService = $objectCreatorService;
    $this->doFlushAndForget = Config::get('fm-lib.doFlushAndForgetInRankingCalculations', true);
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function getEarliestInfluence(EntityRankingSystemInterface $ranking,
                                       TournamentInterface $tournament): ?DateTime
  {
    return $this->getEarliestEntityInfluence($ranking, $tournament, false);
  }

  /**
   * @inheritdoc
   * @throws PreconditionFailedException
   */
  public function updateRankingForTournament(EntityRankingSystemInterface $ranking, TournamentInterface $tournament,
                                             ?DateTime $oldInfluence)
  {
    $earliestInfluence = $this->getEarliestInfluence($ranking, $tournament);
    if ($oldInfluence !== null &&
      ($earliestInfluence === null || $oldInfluence < $earliestInfluence)) {
      $earliestInfluence = $oldInfluence;
    }
    if ($earliestInfluence !== null) {
      $this->updateRankingFrom($ranking, $earliestInfluence);
    }
  }

  /**
   * @inheritDoc
   * @throws PreconditionFailedException
   */
  public function updateRankingFrom(EntityRankingSystemInterface $ranking, DateTime $from)
  {
    // can only be called once per ranking system!!!
    if (array_key_exists($ranking->getId(), $this->updateRankingCalls)) {
      throw new PreconditionFailedException();
    }
    $this->updateRankingCalls[$ranking->getId()] = $ranking;
    //find first reusable
    /** @var RankingSystemListInterface[] $lists */
    $lists = array_values($ranking->getLists()->toArray());

    $current = null;
    /** @var RankingSystemListInterface $lastReusable */
    $lastReusable = null;
    $toUpdate = [];

    foreach ($lists as $list) {
      if ($list->isCurrent()) {
        $current = $list;
      } elseif ($list->getLastEntryTime() >= $from) {
        $toUpdate[] = $list;
      } elseif ($lastReusable === null || $list->getLastEntryTime() > $lastReusable->getLastEntryTime()) {
        $lastReusable = $list;
      }
    }

    if ($current !== null && $current->getLastEntryTime() < $from) {
      $lastReusable = $current;
    }

    if ($lastReusable === null) {
      $lastReusable = $this->objectCreatorService->createObjectFromInterface(RankingSystemListInterface::class);
    }

    usort($toUpdate, function (RankingSystemListInterface $list1, RankingSystemListInterface $list2) {
      return $list1->getLastEntryTime() <=> $list2->getLastEntryTime();
    });


    $lastListTime = null;
    foreach ($toUpdate as $list) {
      $entities = $this->getNextEntities($ranking, $lastReusable, $list, $lastListTime);
      $this->recomputeBasedOn($list, $lastReusable, $entities, $lastListTime);
      $lastReusable = $list;
      $lastListTime = $lastReusable->getLastEntryTime();
    }

    if ($current === null) {
      $current = $this->createNewList($ranking, true);
    }

    $entities = $this->getNextEntities($ranking, $lastReusable, $current, $lastListTime);
    $this->recomputeBasedOn($current, $lastReusable, $entities, $lastListTime);
    $this->deleteOldChanges();
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Final Methods">
  /**
   * Computes the average rating of the given entries
   * @param RankingSystemListEntryInterface[] $entries
   * @return float
   */
  final protected function getAverage(array $entries): float
  {
    $sum = 0.0;
    foreach ($entries as $entry) {
      $sum += $entry->getPoints();
    }
    if (count($entries) === 0) {
      return 0.0;
    } else {
      return $sum / count($entries);
    }
  }

  /**
   * Gets the relevant entities for updating
   * @param EntityRankingSystemInterface $ranking the ranking for which to get the entities
   * @param DateTime $from search for entities with a time value LARGER than $from, i.e. don't search for entities
   *                       with time value exactly $from
   * @param DateTime $to search for entities with a time value SMALLER OR EQUAL than $to
   * @return TournamentHierarchyEntity[]
   */
  final protected function getEntities(EntityRankingSystemInterface $ranking, DateTime $from, DateTime $to): array
  {
    $query = $this->getEntitiesQueryBuilder($ranking, $from, $to);
    return $query->getQuery()->getResult();
  }

  /**
   * @return EntityManagerInterface
   */
  final protected function getEntityManager(): EntityManagerInterface
  {
    return $this->entityManager;
  }

  /**
   * @param Collection|PlayerInterface[] $players
   * @param RankingSystemListInterface $list
   * @return RankingSystemListEntryInterface[] $entries
   */
  final protected function getEntriesOfPlayers(Collection $players, RankingSystemListInterface $list): array
  {
    $result = [];
    foreach ($players as $player) {
      $result[] = $this->getOrCreateRankingSystemListEntry($list, $player);
    }
    return $result;
  }

  /**
   * Gets or creates a tournament system change entry for the given entity, ranking and player.
   * @param TournamentHierarchyEntity $entity the tournament hierarchy entity to search for
   * @param EntityRankingSystemInterface $ranking the ranking system to search for
   * @param PlayerInterface $player the player to search for
   * @return RankingSystemChangeInterface|RankingSystemChange the found or newly created ranking system change
   */
  final protected function getOrCreateChange(TournamentHierarchyEntity $entity,
                                             EntityRankingSystemInterface $ranking,
                                             PlayerInterface $player)
  {
    $key1 = $entity->getId();
    $key2 = $player->getId();
    if (!array_key_exists($key1, $this->changes)) {
      $this->changes[$key1] = [];
    }
    if (array_key_exists($key1, $this->oldChanges) && array_key_exists($key2, $this->oldChanges[$key1])) {
      $this->changes[$key1][$key2] = $this->oldChanges[$key1][$key2];
      unset($this->oldChanges[$key1][$key2]);
    }
    if (!array_key_exists($key2, $this->changes[$key1])) {
      //create new change
      /** @var RankingSystemChangeInterface $change */
      $change = $this->objectCreatorService->createObjectFromInterface(RankingSystemChangeInterface::class,
        [array_merge(array_keys($this->getAdditionalFields()), $this->getAdditionalChangeFields())]);
      foreach ($this->getAdditionalFields() as $field => $value) {
        // PropertyNotExistingException => we know for sure that the property exists (see 2 lines above)

        $change->setProperty($field, 0);
      }
      $change->setHierarchyEntity($entity);
      $change->setRankingSystem($ranking);
      $change->setPlayer($player);
      $this->entityManager->persist($change);
      $this->changes[$key1][$key2] = $change;
    }
    return $this->changes[$key1][$key2];
  }

  /**
   * @param RankingSystemListInterface $list the list in which to search for the entry or in which to add it
   * @param PlayerInterface $player the player to search for
   * @return RankingSystemListEntryInterface the found or the new entry
   */
  final protected function getOrCreateRankingSystemListEntry(RankingSystemListInterface $list,
                                                             PlayerInterface $player): RankingSystemListEntryInterface
  {
    $playerId = $player->getId();
    if (!$list->getEntries()->containsKey($playerId)) {
      /** @var RankingSystemListEntryInterface $entry */
      $entry = $this->objectCreatorService->createObjectFromInterface(RankingSystemListEntryInterface::class,
        [array_keys($this->getAdditionalFields())]);
      $entry->setPlayer($player);
      $entry->setRankingSystemList($list);
      $this->resetListEntry($entry);
      $this->entityManager->persist($entry);
    }
    return $list->getEntries()->get($playerId);
  }
//</editor-fold desc="Protected Final Methods">

//<editor-fold desc="Protected Methods">
  /**
   * Gets additional fields for this ranking type mapped to its start value
   * @return string[] list of additional fields
   */
  protected function getAdditionalChangeFields(): array
  {
    return [];
  }

  /**
   * Gets additional fields for this ranking type mapped to its start value
   * @return string[] list of additional fields
   */
  abstract protected function getAdditionalFields(): array;

  /**
   * Gets all ranking changes for the given entity for the given list. Must return a change for each involved player.
   * The field pointsAfterwards get calculated afterwards and can be left empty.
   * @param TournamentHierarchyEntity $entity the entity for which to compute the ranking changes
   * @param RankingSystemListInterface $list the list for which to compute the ranking changes
   * @return RankingSystemChangeInterface[] the changes
   */
  abstract protected function getChanges(TournamentHierarchyEntity $entity, RankingSystemListInterface $list): array;

  /**
   * Gets a query for getting the relevant entities for updating
   * @param EntityRankingSystemInterface $ranking the ranking for which to get the entities
   * @param DateTime $from search for entities with a time value LARGER than $from, i.e. don't search for entities
   *                       with time value exactly $from
   * @param DateTime $to search for entities with a time value SMALLER OR EQUAL than $to
   * @return QueryBuilder
   */
  abstract protected function getEntitiesQueryBuilder(EntityRankingSystemInterface $ranking,
                                                      DateTime $from, DateTime $to): QueryBuilder;

  /**
   * Gets the level of the ranking system service (see Level Enum)
   * @return int
   */
  abstract protected function getLevel(): int;

  /**
   * Gets the start points for a new player in the ranking
   * @return float
   */
  protected function startPoints(): float
  {
    return 0.0;
  }
//</editor-fold desc="Protected Methods">

//<editor-fold desc="Private Methods">
  /**
   * Clones all ranking values from base and inserts them into list, furthermore removes all remaining ranking values
   * of list. After this method was called list and base contain exactly the same rankings.
   * @param RankingSystemListInterface $list the ranking list to change
   * @param RankingSystemListInterface $base the ranking list to use as base list, this doesn't get changed
   */
  private function cloneInto(RankingSystemListInterface $list, RankingSystemListInterface $base)
  {
    /*//first remove all entries from list
    foreach($list->getEntries()->toArray() as $entry)
    {
      $list->getEntries()->removeElement($entry);
      $this->entityManager->remove($entry);
    }*/

    $clonedPlayers = [];

    foreach ($base->getEntries() as $entry) {
      $playerId = $entry->getPlayer()->getId();
      $clonedPlayers[$playerId] = true;
      if (!$list->getEntries()->containsKey($playerId)) {
        //create new entry
        /** @var RankingSystemListEntryInterface $entry */
        $clone = $this->objectCreatorService->createObjectFromInterface(RankingSystemListEntryInterface::class,
          [[]]);
        $this->entityManager->persist($clone);
        $clone->setPlayer($entry->getPlayer());
        $clone->setRankingSystemList($list);
      }
      $foundEntry = $list->getEntries()[$playerId];
      $foundEntry->setNumberRankedEntities($entry->getNumberRankedEntities());
      $foundEntry->setPoints($entry->getPoints());
      $foundEntry->cloneSubClassDataFrom($entry);
    }

    //remove all unused entries from list
    foreach ($list->getEntries()->toArray() as $playerId => $entry) {
      if (!array_key_exists($playerId, $clonedPlayers)) {
        $this->resetListEntry($entry);
        //$list->getEntries()->removeElement($entry);
        //$this->entityManager->remove($entry);
      }
    }
  }

  private function deleteOldChanges()
  {
    foreach ($this->oldChanges as $eId => $changes) {
      foreach ($changes as $pId => $change) {
        $this->entityManager->remove($change);
      }
    }
    $this->oldChanges = [];
  }

  /**
   * @param TournamentHierarchyInterface[] $entities
   * @param int &$current
   */
  private function flushAndForgetEntities(&$entities, &$current)
  {
    if (!$this->doFlushAndForget) {
      return;
    }
    for ($i = 0; $i < $current; $i++) {
      $eId = $entities[$i]->getId();
      if (array_key_exists($eId, $this->oldChanges)) {
        foreach ($this->oldChanges[$eId] as $pId => $change) {
          $this->entityManager->remove($change);
        }
      }
      unset($this->oldChanges[$eId]);
    }
    $this->entityManager->flush();
    for ($i = 0; $i < $current; $i++) {
      $eId = $entities[$i]->getId();
      /** @noinspection PhpDeprecationInspection */
      /** @scrutinizer ignore-deprecated */
      $this->entityManager->detach($entities[$i]);
      if (array_key_exists($eId, $this->changes)) {
        foreach ($this->changes[$eId] as $pId => $change) {
          /** @noinspection PhpDeprecationInspection */
          /** @scrutinizer ignore-deprecated */
          $this->entityManager->detach($change);
        }
        unset($this->changes[$eId]);
      }
    }
    if ($current >= count($entities)) {
      $entities = [];
    } else {
      array_splice($entities, 0, $current);
    }
    $current = 0;
  }

  /**
   * Gets the earliest influence for the given entity
   * @param EntityRankingSystemInterface $ranking the ranking system for which to get the influence
   * @param TournamentHierarchyInterface $entity the entity to analyze
   * @param bool $parentIsRanked true iff a predecessor contained the given ranking in its ranking systems
   * @return DateTime|null the earliest influence or null if $parentIsRanked is false and the entity and all its
   *                        successors do not have the ranking in its ranking systems
   */
  private function getEarliestEntityInfluence(EntityRankingSystemInterface $ranking,
                                              TournamentHierarchyInterface $entity, bool $parentIsRanked): ?DateTime
  {
    $this->timeService->clearTimes();
    $entityIsRanked = $parentIsRanked || $entity->getRankingSystems()->containsKey($ranking->getId());
    if ($entity->getLevel() === $this->getLevel()) {
      if ($entityIsRanked) {
        return $this->timeService->getTime($entity);
      } else {
        return null;
      }
    }
    $result = null;

    foreach ($entity->getChildren() as $child) {
      $earliest = $this->getEarliestEntityInfluence($ranking, $child, $entityIsRanked);
      if ($result === null || ($earliest !== null && $earliest < $result)) {
        $result = $earliest;
      }
    }
    return $result;
  }

   //everything is parsable

  /**
   * @return DateTime
   */
  private function getMaxDate(): DateTime
  {
    return DateTimeHelper::future();
  }

  /**
   * @param EntityRankingSystemInterface $ranking
   * @param RankingSystemListInterface $lastReusable
   * @param RankingSystemListInterface $list
   * @param DateTime|null $lastListTime
   * @return array|TournamentHierarchyEntity[]
   */
  private function getNextEntities(EntityRankingSystemInterface $ranking, RankingSystemListInterface $lastReusable,
                                   RankingSystemListInterface $list, ?DateTime &$lastListTime)
  {
    $this->deleteOldChanges();
    $entities = $this->getEntities($ranking, $lastReusable->getLastEntryTime(),
      $list->isCurrent() ? $this->getMaxDate() : $list->getLastEntryTime());

    //sort entities
    $this->timeService->clearTimes();
    usort($entities, function ($entity1, $entity2) {
      return $this->entityComparer->compareEntities($entity1, $entity2);
    });

    $this->markOldChangesAsDeleted($ranking, $entities);

    if ($lastListTime == null) {
      if (count($entities) > 0) {
        $lastListTime = max($lastReusable->getLastEntryTime(), $this->timeService->getTime($entities[0]));
      } else {
        $lastListTime = $lastReusable->getLastEntryTime();
      }
    }

    return $entities;
  }

  //all DateTimes and DateIntervals are parsable

  /**
   * @param DateTime $time the time of the last list
   * @param int $generationLevel the list generation level
   * @return DateTime the time of the next list generation
   * @noinspection PhpDocMissingThrowsInspection
   */
  private function getNextGenerationTime(DateTime $time, int $generationLevel): DateTime
  {
    if ($generationLevel === AutomaticInstanceGeneration::OFF) {
      return $this->getMaxDate();
    }
    $year = (int)$time->format('Y');
    $month = (int)$time->format('m');
    if ($generationLevel === AutomaticInstanceGeneration::MONTHLY) {
      $month += 1;
      if ($month == 13) {
        $month = 1;
        $year += 1;
      }
    } else {
      $year += 1;
    }
    $dateTime = (new DateTime())->setDate($year, $month, 1);
    $dateTime->setTime(0, 0, 0);
    return $dateTime;
  }

  /**
   * @param EntityRankingSystemInterface $ranking
   * @param TournamentHierarchyEntity[] $entities
   */
  private function markOldChangesAsDeleted(EntityRankingSystemInterface $ranking, array $entities)
  {
    Internal::assert(count($this->oldChanges) == 0);
    $this->changes = [];
    $queryBuilder = $this->entityManager->createQueryBuilder();
    /** @var RankingSystemChangeInterface[] $changes */
    $changes = $queryBuilder
      ->from(RankingSystemChangeInterface::class, 'c')
      ->select('c')
      ->where($queryBuilder->expr()->eq('c.rankingSystem', ':ranking'))
      ->setParameter('ranking', $ranking)
      ->andWhere($queryBuilder->expr()->in('c.hierarchyEntity', ':entities'))
      ->setParameter('entities', $entities)
      ->getQuery()->getResult();
    foreach ($changes as $change) {
      $eId = $change->getHierarchyEntity()->getId();
      $pId = $change->getPlayer()->getId();
      if (array_key_exists($eId, $this->oldChanges) && array_key_exists($pId, $this->oldChanges[$eId])) {
        //duplicate entry
        Internal::assert($this->oldChanges[$eId][$pId]->getRankingSystem()->getId() ===
          $change->getRankingSystem()->getId());
        $this->entityManager->remove($change);
      } else {
        $this->oldChanges[$eId][$pId] = $change;
      }
    }
  }


  /**
   * Recomputes the given ranking list by using base as base list and applying the changes for the given entities
   * starting from the given index. If list is not the current list only the entities up to $list->getLastEntryTime()
   * are applied and the index gets changed accordingly.
   * @param RankingSystemListInterface $list the list to recompute
   * @param RankingSystemListInterface $base the list to use as base
   * @param TournamentHierarchyEntity[] $entities the list of entities to use for the computation
   * @param DateTime $lastListTime the time of the last list or the first entry
   * @param bool $doFlushAndForget if we should flush and forget entities in between
   */
  protected function recomputeBasedOn(RankingSystemListInterface $list, RankingSystemListInterface $base,
                                      array &$entities, DateTime $lastListTime, bool $doFlushAndForget = true)
  {
    $nextGeneration = $this->getNextGenerationTime($lastListTime, $list->getRankingSystem()->getGenerationInterval());
    if ($list !== $base) {
      $this->cloneInto($list, $base);
    }
    for ($i = 0, $c = count($entities); $i < $c; $i++) {
      $time = $this->timeService->getTime($entities[$i]);
      if (!$list->isCurrent() && $time > $list->getLastEntryTime()) {
        if ($doFlushAndForget) {
          $this->flushAndForgetEntities($entities, $i);
        }
        return;
      }
      if ($nextGeneration < $time) {
        /** @var RankingSystemListInterface $newList */
        $newList = $this->createNewList($list->getRankingSystem(), false);
        $newList->setLastEntryTime($nextGeneration);
        $this->cloneInto($newList, $list);
        $nextGeneration = $this->getNextGenerationTime($nextGeneration,
          $list->getRankingSystem()->getGenerationInterval());
        //clear entityManager to save memory
        if ($doFlushAndForget) {
          $this->flushAndForgetEntities($entities, $i);
          $c = count($entities);
        }
      }
      $this->applyEntityToList($entities[$i], $time, $list);
    }
    if ($doFlushAndForget) {
      $this->flushAndForgetEntities($entities, $c);
    }
  }

  /**
   * @param EntityRankingSystemInterface $rankingSystem
   * @param bool $current
   * @return RankingSystemListInterface
   */
  protected function createNewList(EntityRankingSystemInterface $rankingSystem,
                                   bool $current): RankingSystemListInterface
  {
    $newList = $this->objectCreatorService->createObjectFromInterface(RankingSystemListInterface::class);
    $newList->setCurrent($current);
    $this->entityManager->persist($newList);
    $newList->setRankingSystem($rankingSystem);
    return $newList;
  }

  /**
   * @param TournamentHierarchyEntity $entity
   * @param DateTime $entityTime
   * @param RankingSystemListInterface $list
   */
  protected function applyEntityToList(TournamentHierarchyEntity $entity, DateTime $entityTime,
                                       RankingSystemListInterface $list)
  {
    $changes = $this->getChanges($entity, $list);
    foreach ($changes as $change) {
      $entry = $this->getOrCreateRankingSystemListEntry($list, $change->getPlayer());
      $entry->setNumberRankedEntities($entry->getNumberRankedEntities() + 1);
      $pointsAfterwards = $entry->getPoints() + $change->getPointsChange();
      $entry->setPoints($pointsAfterwards);
      $change->setPointsAfterwards($pointsAfterwards);
      //apply further changes
      foreach ($this->getAdditionalFields() as $field => $value) {
        // PropertyNotExistingException => entry and field have exactly the static properties from getAdditionalFields

        $entry->setProperty($field, $entry->getProperty($field) + $change->getProperty($field));
      }
      if ($entityTime > $list->getLastEntryTime()) {
        $list->setLastEntryTime($entityTime);
      }
      $this->entityManager->persist($change);
    }
  }

  /**
   * @param RankingSystemListEntryInterface $entry
   */
  private function resetListEntry(RankingSystemListEntryInterface $entry)
  {
    $entry->setPoints($this->startPoints());
    $entry->setNumberRankedEntities(0);
    foreach ($this->getAdditionalFields() as $field => $value) {
      // PropertyNotExistingException => we know for sure that the property exists (see 2 lines above)

      $entry->setProperty($field, $value);
    }
  }
//</editor-fold desc="Private Methods">
}