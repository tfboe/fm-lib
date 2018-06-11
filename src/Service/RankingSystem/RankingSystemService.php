<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 2:32 PM
 */

namespace Tfboe\FmLib\Service\RankingSystem;


use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Tfboe\FmLib\Entity\Helpers\AutomaticInstanceGeneration;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;
use Tfboe\FmLib\Entity\PlayerInterface;
use Tfboe\FmLib\Entity\RankingSystemChangeInterface;
use Tfboe\FmLib\Entity\RankingSystemInterface;
use Tfboe\FmLib\Entity\RankingSystemListEntryInterface;
use Tfboe\FmLib\Entity\RankingSystemListInterface;
use Tfboe\FmLib\Entity\TournamentInterface;
use Tfboe\FmLib\Exceptions\PreconditionFailedException;
use Tfboe\FmLib\Service\ObjectCreatorServiceInterface;


/**
 * Class RankingSystemService
 * @package Tfboe\FmLib\Service\RankingSystemService
 * @SuppressWarnings(PHPMD) TODO: refactor this class and remove suppress warnings
 */
abstract class RankingSystemService implements \Tfboe\FmLib\Service\RankingSystem\RankingSystemInterface
{
//<editor-fold desc="Fields">
  /** @var EntityManagerInterface */
  private $entityManager;
  /** @var TimeServiceInterface */
  private $timeService;
  /** @var EntityComparerInterface */
  private $entityComparer;
  /**
   * @var RankingSystemChangeInterface[][][]
   * first key: tournament hierarchy entity id
   * second key: ranking system id
   * third key: player id
   */
  private $changes;
  /**
   * @var RankingSystemChangeInterface[][][]
   * first key: tournament hierarchy entity id
   * second key: ranking system id
   * third key: player id
   */
  private $deletedChanges;
  /**
   * List of ranking systems for which update ranking got already called, indexed by id
   * @var RankingSystemService[]
   */
  private $updateRankingCalls;

  /** @var ObjectCreatorServiceInterface */
  private $objectCreatorService;
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
    $this->deletedChanges = [];
    $this->updateRankingCalls = [];
    $this->objectCreatorService = $objectCreatorService;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function getEarliestInfluence(RankingSystemInterface $ranking, TournamentInterface $tournament): ?\DateTime
  {
    return $this->getEarliestEntityInfluence($ranking, $tournament, false);
  }

  /**
   * @inheritdoc
   */
  public function updateRankingForTournament(RankingSystemInterface $ranking, TournamentInterface $tournament,
                                             ?\DateTime $oldInfluence)
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
   */
  public function updateRankingFrom(RankingSystemInterface $ranking, \DateTime $from)
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
      } else if ($list->getLastEntryTime() >= $from) {
        $toUpdate[] = $list;
      } else if ($lastReusable === null || $list->getLastEntryTime() > $lastReusable->getLastEntryTime()) {
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

    $entities = $this->getEntities($ranking, $lastReusable->getLastEntryTime());
    //sort entities
    $this->timeService->clearTimes();
    usort($entities, function ($entity1, $entity2) {
      return $this->entityComparer->compareEntities($entity1, $entity2);
    });

    $this->deleteOldChanges($ranking, $entities);

    $nextEntityIndex = 0;
    foreach ($toUpdate as $list) {
      $lastListTime = $lastReusable->getLastEntryTime();
      if (count($entities) > 0) {
        $lastListTime = max($lastListTime, $this->timeService->getTime($entities[0]));
      }
      $this->recomputeBasedOn($list, $lastReusable, $entities, $nextEntityIndex, $lastListTime);
      $lastReusable = $list;
    }

    $lastListTime = $lastReusable->getLastEntryTime();
    if (count($entities) > 0) {
      $lastListTime = max($lastListTime, $this->timeService->getTime($entities[0]));
    }

    if ($current === null) {
      /** @var RankingSystemListInterface $current */
      $current = $this->objectCreatorService->createObjectFromInterface(RankingSystemListInterface::class);
      $current->setCurrent(true);
      $this->entityManager->persist($current);
      $current->setRankingSystem($ranking);
    }
    $this->recomputeBasedOn($current, $lastReusable, $entities, $nextEntityIndex, $lastListTime);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Final Methods">
  /**
   * Computes the average rating of the given entries
   * @param RankingSystemListEntryInterface[] $entries
   * @return float
   */
  protected final function getAverage(array $entries): float
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
   * @param RankingSystemInterface $ranking the ranking for which to get the entities
   * @param \DateTime $from search for entities with a time value LARGER than $from, i.e. don't search for entities with
   *                        time value exactly $from
   * @return TournamentHierarchyEntity[]
   */
  protected final function getEntities(RankingSystemInterface $ranking, \DateTime $from): array
  {
    $query = $this->getEntitiesQueryBuilder($ranking, $from);
    return $query->getQuery()->getResult();
  }

  /**
   * @return EntityManagerInterface
   */
  protected final function getEntityManager(): EntityManagerInterface
  {
    return $this->entityManager;
  }

  /**
   * @param Collection|PlayerInterface[] $players
   * @param RankingSystemListInterface $list
   * @return RankingSystemListEntryInterface[] $entries
   */
  protected final function getEntriesOfPlayers(Collection $players, RankingSystemListInterface $list): array
  {
    $result = [];
    foreach ($players as $player) {
      $result[] = $this->getOrCreateRankingSystemListEntry($list, $player);
    }
    return $result;
  }

  /** @noinspection PhpDocMissingThrowsInspection */ //PropertyNotExistingException
  /**
   * Gets or creates a tournament system change entry for the given entity, ranking and player.
   * @param TournamentHierarchyInterface $entity the tournament hierarchy entity to search for
   * @param RankingSystemInterface $ranking the ranking system to search for
   * @param PlayerInterface $player the player to search for
   * @return RankingSystemChangeInterface the found or newly created ranking system change
   */
  protected final function getOrCreateChange(TournamentHierarchyInterface $entity, RankingSystemInterface $ranking,
                                             PlayerInterface $player)
  {
    $key1 = $entity->getId();
    $key2 = $ranking->getId();
    $key3 = $player->getId();
    if (!array_key_exists($key1, $this->changes) || !array_key_exists($key2, $this->changes[$key1]) ||
      !array_key_exists($key3, $this->changes[$key1][$key2])) {
      /** @var RankingSystemChangeInterface[] $changes */
      $changes = $this->entityManager->getRepository(RankingSystemChangeInterface::class)->findBy(
        ['hierarchyEntity' => $entity]);
      $this->changes[$key1] = [];
      foreach ($changes as $change) {

        $newKey2 = $change->getRankingSystem()->getId();
        $newKey3 = $change->getPlayer()->getId();
        if (!array_key_exists($key1, $this->deletedChanges) || !array_key_exists($key2, $this->deletedChanges[$key1]) ||
          !array_key_exists($key3, $this->deletedChanges[$key1][$key2])) {
          if (!array_key_exists($newKey2, $this->changes)) {
            $this->changes[$key1][$newKey2] = [];
          }
          $this->changes[$key1][$newKey2][$newKey3] = $change;
        }
      }
    }
    if (!array_key_exists($key2, $this->changes[$key1]) || !array_key_exists($key3, $this->changes[$key1][$key2])) {
      //create new change
      /** @var RankingSystemChangeInterface $change */
      $change = $this->objectCreatorService->createObjectFromInterface(RankingSystemChangeInterface::class,
        [array_keys($this->getAdditionalFields())]);
      foreach ($this->getAdditionalFields() as $field => $value) {
        // PropertyNotExistingException => we know for sure that the property exists (see 2 lines above)
        /** @noinspection PhpUnhandledExceptionInspection */
        $change->setProperty($field, 0);
      }
      $change->setHierarchyEntity($entity);
      $change->setRankingSystem($ranking);
      $change->setPlayer($player);
      $this->entityManager->persist($change);
      $this->changes[$key1][$key2][$key3] = $change;
      if (array_key_exists($key1, $this->deletedChanges) && array_key_exists($key2, $this->deletedChanges[$key1]) &&
        array_key_exists($key3, $this->deletedChanges[$key1][$key2])) {
        unset($this->deletedChanges[$key1][$key2][$key3]);
      }
    }
    return $this->changes[$key1][$key2][$key3];
  }

  /** @noinspection PhpDocMissingThrowsInspection */ //PropertyNotExistingException
  /**
   * @param RankingSystemListInterface $list the list in which to search for the entry or in which to add it
   * @param PlayerInterface $player the player to search for
   * @return RankingSystemListEntryInterface the found or the new entry
   */
  protected final function getOrCreateRankingSystemListEntry(RankingSystemListInterface $list,
                                                             PlayerInterface $player): RankingSystemListEntryInterface
  {
    $playerId = $player->getId();
    if (!$list->getEntries()->containsKey($playerId)) {
      /** @var RankingSystemListEntryInterface $entry */
      $entry = $this->objectCreatorService->createObjectFromInterface(RankingSystemListEntryInterface::class,
        [array_keys($this->getAdditionalFields())]);
      $entry->setPoints($this->startPoints());
      $entry->setPlayer($player);
      $entry->setRankingSystemList($list);
      foreach ($this->getAdditionalFields() as $field => $value) {
        // PropertyNotExistingException => we know for sure that the property exists (see 2 lines above)
        /** @noinspection PhpUnhandledExceptionInspection */
        $entry->setProperty($field, $value);
      }
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
  protected abstract function getAdditionalFields(): array;

  /**
   * Gets all ranking changes for the given entity for the given list. Must return a change for each involved player.
   * The field pointsAfterwards get calculated afterwards and can be left empty.
   * @param TournamentHierarchyEntity $entity the entity for which to compute the ranking changes
   * @param RankingSystemListInterface $list the list for which to compute the ranking changes
   * @return RankingSystemChangeInterface[] the changes
   */
  protected abstract function getChanges(TournamentHierarchyEntity $entity, RankingSystemListInterface $list): array;

  /**
   * Gets a query for getting the relevant entities for updating
   * @param RankingSystemInterface $ranking the ranking for which to get the entities
   * @param \DateTime $from search for entities with a time value LARGER than $from, i.e. don't search for entities with
   *                        time value exactly $from
   * @return QueryBuilder
   */
  protected abstract function getEntitiesQueryBuilder(RankingSystemInterface $ranking,
                                                      \DateTime $from): QueryBuilder;

  /**
   * Gets the level of the ranking system service (see Level Enum)
   * @return int
   */
  protected abstract function getLevel(): int;

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
   * Clones all ranking values from base and inserts them into list, furthermore removes all remaining ranking values of
   * list. After this method was called list and base contain exactly the same rankings.
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
        $clone->cloneSubClassDataFrom($entry);
        $this->entityManager->persist($clone);
        $clone->setPlayer($entry->getPlayer());
        $clone->setRankingSystemList($list);
      }
      $foundEntry = $list->getEntries()[$playerId];
      $foundEntry->setNumberRankedEntities($entry->getNumberRankedEntities());
      $foundEntry->setPoints($entry->getPoints());
    }

    //remove all unused entries from list
    foreach ($list->getEntries()->toArray() as $playerId => $entry) {
      if (!array_key_exists($playerId, $clonedPlayers)) {
        $list->getEntries()->removeElement($entry);
        $this->entityManager->remove($entry);
      }
    }
  }


  /**
   * @param RankingSystemInterface $ranking
   * @param TournamentHierarchyEntity[] $entities
   */
  private function deleteOldChanges(RankingSystemInterface $ranking, array $entities)
  {
    //delete old changes
    $queryBuilder = $this->entityManager->createQueryBuilder();
    /** @var RankingSystemChangeInterface[] $changes */
    $changes = $queryBuilder
      ->from(RankingSystemChangeInterface::class, 'c')
      ->select('c')
      ->where($queryBuilder->expr()->eq('c.rankingSystem', $ranking))
      ->where($queryBuilder->expr()->in('c.hierarchyEntity', ':entities'))
      ->setParameter('entities', $entities)
      ->getQuery()->getResult();
    foreach ($changes as $change) {
      $this->deletedChanges[$change->getHierarchyEntity()->getId()][$ranking
        ->getId()][$change->getPlayer()->getId()] = $change;
      $this->entityManager->remove($change);
    }
  }

  /**
   * Gets the earliest influence for the given entity
   * @param RankingSystemInterface $ranking the ranking system for which to get the influence
   * @param TournamentHierarchyInterface $entity the entity to analyze
   * @param bool $parentIsRanked true iff a predecessor contained the given ranking in its ranking systems
   * @return \DateTime|null the earliest influence or null if $parentIsRanked is false and the entity and all its
   *                        successors do not have the ranking in its ranking systems
   */
  private function getEarliestEntityInfluence(RankingSystemInterface $ranking, TournamentHierarchyInterface $entity,
                                              bool $parentIsRanked): ?\DateTime
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

  /**
   * @param \DateTime $time the time of the last list
   * @param int $generationLevel the list generation level
   * @return \DateTime the time of the next list generation
   */
  private function getNextGenerationTime(\DateTime $time, int $generationLevel): \DateTime
  {
    $year = (int)$time->format('Y');
    $month = (int)$time->format('m');
    if ($generationLevel === AutomaticInstanceGeneration::MONTHLY) {
      $month += 1;
      if ($month == 13) {
        $month = 1;
        $year += 1;
      }
    } else if ($generationLevel === AutomaticInstanceGeneration::OFF) {
      return (new \DateTime())->add(new \DateInterval('P100Y'));
    } else {
      $year += 1;
    }
    return (new \DateTime())->setDate($year, $month, 1)->setTime(0, 0, 0);
  }

  /** @noinspection PhpDocMissingThrowsInspection */ //PropertyNotExistingException
  /**
   * Recomputes the given ranking list by using base as base list and applying the changes for the given entities
   * starting from the given index. If list is not the current list only the entities up to $list->getLastEntryTime()
   * are applied and the index gets changed accordingly.
   * @param RankingSystemListInterface $list the list to recompute
   * @param RankingSystemListInterface $base the list to use as base
   * @param TournamentHierarchyEntity[] $entities the list of entities to use for the computation
   * @param int $nextEntityIndex the first index in the entities list to consider
   * @param \DateTime $lastListTime the time of the last list or the first entry
   */
  private function recomputeBasedOn(RankingSystemListInterface $list, RankingSystemListInterface $base, array $entities,
                                    int &$nextEntityIndex, \DateTime $lastListTime)
  {
    $nextGeneration = $this->getNextGenerationTime($lastListTime, $list->getRankingSystem()->getGenerationInterval());
    $this->cloneInto($list, $base);
    for ($i = $nextEntityIndex; $i < count($entities); $i++) {
      $time = $this->timeService->getTime($entities[$i]);
      if (!$list->isCurrent() && $time > $list->getLastEntryTime()) {
        $nextEntityIndex = $i;
        return;
      }
      if ($nextGeneration < $time) {
        /** @var RankingSystemListInterface $newList */
        $newList = $this->objectCreatorService->createObjectFromInterface(RankingSystemListInterface::class);
        $newList->setCurrent(false);
        $newList->setLastEntryTime($nextGeneration);
        $this->entityManager->persist($newList);
        $newList->setRankingSystem($list->getRankingSystem());
        $this->cloneInto($newList, $list);
        $nextGeneration = $this->getNextGenerationTime($nextGeneration,
          $list->getRankingSystem()->getGenerationInterval());
      }
      $changes = $this->getChanges($entities[$i], $list);
      foreach ($changes as $change) {
        $entry = $this->getOrCreateRankingSystemListEntry($list, $change->getPlayer());
        $entry->setNumberRankedEntities($entry->getNumberRankedEntities() + 1);
        $pointsAfterwards = $entry->getPoints() + $change->getPointsChange();
        $entry->setPoints($pointsAfterwards);
        $change->setPointsAfterwards($pointsAfterwards);
        //apply further changes
        foreach ($this->getAdditionalFields() as $field => $value) {
          // PropertyNotExistingException => entry and field have exactly the static properties from getAdditionalFields
          /** @noinspection PhpUnhandledExceptionInspection */
          $entry->setProperty($field, $entry->getProperty($field) + $change->getProperty($field));
        }
        if ($time > $list->getLastEntryTime()) {
          $list->setLastEntryTime($time);
        }
        $this->entityManager->persist($change);
      }
    }
    $nextEntityIndex = count($entities);
  }
//</editor-fold desc="Private Methods">
}