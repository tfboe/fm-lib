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
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Tfboe\FmLib\Entity\CompetitionInterface;
use Tfboe\FmLib\Entity\GameInterface;
use Tfboe\FmLib\Entity\Helpers\AutomaticInstanceGeneration;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;
use Tfboe\FmLib\Entity\MatchInterface;
use Tfboe\FmLib\Entity\PhaseInterface;
use Tfboe\FmLib\Entity\PlayerInterface;
use Tfboe\FmLib\Entity\RankingSystemChangeInterface;
use Tfboe\FmLib\Entity\RankingSystemInterface;
use Tfboe\FmLib\Entity\RankingSystemListEntryInterface;
use Tfboe\FmLib\Entity\RankingSystemListInterface;
use Tfboe\FmLib\Entity\RecalculationInterface;
use Tfboe\FmLib\Entity\TournamentHierarchyEntityRankingTimeInterface;
use Tfboe\FmLib\Entity\TournamentInterface;
use Tfboe\FmLib\Exceptions\PreconditionFailedException;
use Tfboe\FmLib\Helpers\Logging;
use Tfboe\FmLib\Helpers\Logs;
use Tfboe\FmLib\Service\LoadingServiceInterface;
use Tfboe\FmLib\Service\ObjectCreatorServiceInterface;


/**
 * Class RankingSystemService
 * @package Tfboe\FmLib\Service\RankingSystemService
 * @SuppressWarnings(PHPMD) TODO: refactor this class and remove suppress warnings
 */
abstract class RankingSystemService implements \Tfboe\FmLib\Service\RankingSystem\RankingSystemInterface
{
//<editor-fold desc="Fields">
  /** @var LoadingServiceInterface */
  protected $loadingService;
  /** @var EntityManagerInterface */
  private $entityManager;
  /** @var TimeServiceInterface */
  private $timeService;
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
  /** @var string[] */
  private $toForgetClasses;

//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * RankingSystemService constructor.
   * @param EntityManagerInterface $entityManager
   * @param TimeServiceInterface $timeService
   * @param EntityComparerInterface $entityComparer
   * @param ObjectCreatorServiceInterface $objectCreatorService
   * @param LoadingServiceInterface $loadingService
   */
  public function __construct(EntityManagerInterface $entityManager, TimeServiceInterface $timeService,
                              EntityComparerInterface $entityComparer,
                              ObjectCreatorServiceInterface $objectCreatorService,
                              LoadingServiceInterface $loadingService)
  {
    $this->entityManager = $entityManager;
    $this->timeService = $timeService;
    $this->entityComparer = $entityComparer;
    $this->changes = [];
    $this->oldChanges = [];
    $this->updateRankingCalls = [];
    $this->objectCreatorService = $objectCreatorService;
    $this->loadingService = $loadingService;
    $this->toForgetClasses = [RankingSystemListEntryInterface::class, RankingSystemChangeInterface::class,
      PlayerInterface::class, TournamentInterface::class, CompetitionInterface::class, PhaseInterface::class,
      MatchInterface::class, GameInterface::class, TournamentHierarchyEntity::class];
    foreach ($this->toForgetClasses as &$class) {
      $class = $this->entityManager->getClassMetadata($class)->getReflectionClass()->getName();
    }
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * @inheritDoc
   * @throws PreconditionFailedException
   * @throws \Doctrine\DBAL\DBALException
   */
  public function updateRankingFrom(RankingSystemInterface $ranking, \DateTime $from,
                                    ?RecalculationInterface $recalculation)
  {
    /** @noinspection PhpUnhandledExceptionInspection */
    Logging::log("Starting updating ranking " . $ranking->getName() . " from " . $from->format('Y-m-d H:i:s'),
      Logs::RANKING_COMPUTATION);
    // can only be called once per ranking system!!!
    if (array_key_exists($ranking->getId(), $this->updateRankingCalls)) {
      throw new PreconditionFailedException();
    }
    $earliestTime = new \DateTime(env('EARLIEST_TIME', '2017-01-01 00:00:00'));
    if ($from < $earliestTime) {
      $from = $earliestTime;
    }
    $this->updateRankingCalls[$ranking->getId()] = $ranking;
    //find first reusable
    /** @var RankingSystemListInterface[] $lists */
    $lists = array_values($ranking->getLists()->toArray());

    $current = null;
    /** @var RankingSystemListInterface $lastReusable */
    $lastReusable = null;
    /** @var RankingSystemListInterface[] $toUpdate */
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
      $date = clone $earliestTime;
      $date->modify('-1 second');
      $lastReusable->setLastEntryTime($date);
      $this->getEntityManager()->persist($lastReusable);
      $lastReusable->setRankingSystem($ranking);
    }

    usort($toUpdate, function (RankingSystemListInterface $list1, RankingSystemListInterface $list2) {
      return $list1->getLastEntryTime() <=> $list2->getLastEntryTime();
    });

    $this->updateRankingTimes($lastReusable->getLastEntryTime(), $ranking);

    foreach ($toUpdate as $list) {
      $this->recomputeBasedOn($list, $lastReusable);
      $recalculation->setRecalculateFrom($list->getLastEntryTime());
      $this->entityManager->flush();
      $lastReusable = $list;
    }

    if ($current === null) {
      /** @var RankingSystemListInterface $current */
      $current = $this->objectCreatorService->createObjectFromInterface(RankingSystemListInterface::class);
      $current->setCurrent(true);
      $this->entityManager->persist($current);
      $current->setRankingSystem($ranking);
    }

    $this->recomputeBasedOn($current, $lastReusable);
    /** @noinspection PhpUnhandledExceptionInspection */
    Logging::log("Finished updating ranking " . $ranking->getName() . " from " . $from->format('Y-m-d H:i:s')
      . ", peak memory usage: " . memory_get_peak_usage(), Logs::RANKING_COMPUTATION);
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
   * @return EntityManagerInterface
   */
  protected final function getEntityManager(): EntityManagerInterface
  {
    return $this->entityManager;
  }

  /**
   * Gets the relevant entities for updating
   * @param RankingSystemInterface $ranking the ranking for which to get the entities
   * @param \DateTime $from search for entities with a time value LARGER than $from, i.e. don't search for entities with
   *                        time value exactly $from
   * @param \DateTime to search for entities with a time value SMALLER OR EQUAL than $to
   * @return TournamentHierarchyEntity[]
   */
  /*protected final function getEntities(RankingSystemInterface $ranking, \DateTime $from, \DateTime $to): array
  {
    $query = $this->getEntitiesQueryBuilder($ranking, $from, $to);
    return $query->getQuery()->getResult();
  }*/

  /**
   * @param Collection|PlayerInterface[] $players
   * @param RankingSystemListInterface $list
   * @param RankingSystemListEntryInterface[] $entries a dictionary of available entries indexed by player id
   * @return RankingSystemListEntryInterface[] $entries
   */
  protected final function getEntriesOfPlayers(Collection $players, RankingSystemListInterface $list,
                                               array &$entries): array
  {
    $result = [];
    foreach ($players as $player) {
      $result[] = $this->getOrCreateRankingSystemListEntry($list, $player, $entries);
    }
    return $result;
  }

  /** @noinspection PhpDocMissingThrowsInspection */ //PropertyNotExistingException
  /**
   * Gets or creates a tournament system change entry for the given entity, ranking and player.
   * @param TournamentHierarchyInterface $entity the tournament hierarchy entity to search for
   * @param RankingSystemInterface $ranking the ranking system to search for
   * @param PlayerInterface $player the player to search for
   * @param RankingSystemChangeInterface[] $oldChanges the dictionary of old changes of this entity indexed by player id
   * @return RankingSystemChangeInterface the found or newly created ranking system change
   */
  protected final function getOrCreateChange(TournamentHierarchyInterface $entity, RankingSystemInterface $ranking,
                                             PlayerInterface $player, array $oldChanges)
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
    if (!array_key_exists($player->getId(), $oldChanges)) {
      //create new change
      /** @var RankingSystemChangeInterface $change */
      $change = $this->objectCreatorService->createObjectFromInterface(RankingSystemChangeInterface::class,
        [array_merge(array_keys($this->getAdditionalFields()), $this->getAdditionalChangeFields())]);
      foreach ($this->getAdditionalFields() as $field => $value) {
        // PropertyNotExistingException => we know for sure that the property exists (see 2 lines above)
        /** @noinspection PhpUnhandledExceptionInspection */
        $change->setProperty($field, 0);
      }
      $change->setHierarchyEntity($entity);
      $change->setRankingSystem($ranking);
      $change->setPlayer($player);
      $this->entityManager->persist($change);
      return $change;
    } else {
      return $oldChanges[$player->getId()];
    }
  }

  /** @noinspection PhpDocMissingThrowsInspection */ //PropertyNotExistingException
  /**
   * @param RankingSystemListInterface $list the list in which to search for the entry or in which to add it
   * @param PlayerInterface $player the player to search for
   * @param RankingSystemListEntryInterface[] $entries a dictionary of available entries indexed by player id
   * @return RankingSystemListEntryInterface the found or the new entry
   */
  protected final function getOrCreateRankingSystemListEntry(RankingSystemListInterface $list, PlayerInterface $player,
                                                             array &$entries): RankingSystemListEntryInterface
  {
    $playerId = $player->getId();
    if (!array_key_exists($playerId, $entries)) {
      /** @var RankingSystemListEntryInterface $entry */
      $entry = $this->objectCreatorService->createObjectFromInterface(RankingSystemListEntryInterface::class,
        [array_keys($this->getAdditionalFields())]);
      $entry->setPlayer($player);
      $entry->setRankingSystemListWithoutInitializing($list);
      $this->resetListEntry($entry);
      $this->entityManager->persist($entry);
      $entries[$playerId] = $entry;
    }
    //echo("A:" . $player->getId() . ";");
    //echo($list->getEntries()->get($playerId)->getPlayer()->getId());
    return $entries[$playerId];
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
  protected abstract function getAdditionalFields(): array;

  /**
   * Gets all ranking changes for the given entity for the given list. Must return a change for each involved player.
   * The field pointsAfterwards get calculated afterwards and can be left empty.
   * @param TournamentHierarchyEntity $entity the entity for which to compute the ranking changes
   * @param RankingSystemListInterface $list the list for which to compute the ranking changes
   * @param RankingSystemChangeInterface[] $oldChanges the dictionary of old changes of this entity indexed by player id
   * @param RankingSystemListEntryInterface[] $entries a dictionary of available entries indexed by player id
   * @return RankingSystemChangeInterface[] the changes
   */
  protected abstract function getChanges(TournamentHierarchyEntity $entity, RankingSystemListInterface $list,
                                         array $oldChanges, array &$entries): array;

  /**
   * Gets a query for getting the relevant entities for updating
   * @param RankingSystemInterface $ranking the ranking for which to get the entities
   * @param \DateTime $from search for entities with a time value LARGER than $from, i.e. don't search for entities with
   *                        time value exactly $from
   * @param \DateTime to search for entities with a time value SMALLER OR EQUAL than $to
   * @return QueryBuilder
   */
  protected abstract function getEntitiesQueryBuilder(RankingSystemInterface $ranking,
                                                      \DateTime $from, \DateTime $to): QueryBuilder;

  /**
   * @return TournamentHierarchyEntity[][]|\DateTime[][]|int[][]
   */
  protected abstract function getEntriesClass(): string;

  /**
   * Gets the level of the ranking system service (see Level Enum)
   * @return int
   */
  protected abstract function getLevel(): int;

  /**
   * @param TournamentHierarchyEntity $entity
   * @return PlayerInterface[]
   */
  protected abstract function getPlayersOfEntity(TournamentHierarchyEntity $entity): array;

  /**
   * @param TournamentHierarchyEntity[] $entities
   */
  protected abstract function loadAllPlayersOfEntities(array $entities): void;

  /**
   * Gets the start points for a new player in the ranking
   * @return float
   */
  protected function startPoints(): float
  {
    return 0.0;
  }

  /**
   * @param \DateTime $from
   * @param RankingSystemInterface $rankingSystem
   */
  protected abstract function updateRankingTimes(\DateTime $from, RankingSystemInterface $rankingSystem);
  /** @noinspection PhpDocMissingThrowsInspection */
//</editor-fold desc="Protected Methods">

//<editor-fold desc="Private Methods">
  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * Clones all ranking values from base and inserts them into list, furthermore removes all remaining ranking values of
   * list. After this method was called list and base contain exactly the same rankings.
   * @param RankingSystemListInterface $list the ranking list to change
   * @param RankingSystemListInterface $base the ranking list to use as base list, this doesn't get changed
   * @throws \Doctrine\DBAL\DBALException
   */
  private function cloneInto(RankingSystemListInterface $list, RankingSystemListInterface $base)
  {
    //we use native queries for everything, flush entityManager before doing anything
    $this->getEntityManager()->flush();

    $meta = $this->getEntityManager()->getClassMetadata(RankingSystemListEntryInterface::class);
    $table = $meta->getTableName();
    /** @noinspection PhpUnhandledExceptionInspection */
    $rankingSystemListCol = $meta->getSingleAssociationJoinColumnName('rankingSystemList');
    /** @noinspection PhpUnhandledExceptionInspection */
    $playerCol = $meta->getSingleAssociationJoinColumnName('player');
    $pointsCol = $meta->getColumnName('points');
    $lastChangedCol = $meta->getColumnName('lastChanged');
    $idCol = $meta->getColumnName('id');
    $numberRankedEntitiesCol = $meta->getColumnName('numberRankedEntities');
    $subClassDataCol = $meta->getColumnName('subClassData');

    //remove entries which are in list but not in base
    $query = <<<SQL
DELETE l
FROM $table AS l
LEFT JOIN $table AS b ON b.$playerCol = l.$playerCol AND b.$rankingSystemListCol = ?
WHERE b.$idCol IS NULL AND l.$rankingSystemListCol = ?
SQL;
    $this->getEntityManager()->getConnection()->executeUpdate($query, [$base->getId(), $list->getId()]);

    //update entries which are in list and in base
    $query = <<<SQL
UPDATE $table AS l
INNER JOIN $table AS b ON b.$playerCol = l.$playerCol AND b.$rankingSystemListCol = ?
SET l.$subClassDataCol = b.$subClassDataCol, 
    l.$pointsCol = b.$pointsCol,
    l.$lastChangedCol = b.$lastChangedCol,
    l.$numberRankedEntitiesCol = b.$numberRankedEntitiesCol
WHERE l.$rankingSystemListCol = ?
SQL;
    $this->getEntityManager()->getConnection()->executeUpdate($query, [$base->getId(), $list->getId()]);

    //last but not least insert entries into list which are in base but not in list, we do this with a native query
    $query = <<<SQL
INSERT INTO $table ($idCol, $rankingSystemListCol, $playerCol, $pointsCol, $lastChangedCol, $numberRankedEntitiesCol, $subClassDataCol)
SELECT UUID(), ?, b.$playerCol, b.$pointsCol, b.$lastChangedCol, b.$numberRankedEntitiesCol, b.$subClassDataCol
FROM $table AS b
LEFT JOIN $table AS l
ON l.$playerCol = b.$playerCol AND l.$rankingSystemListCol = ?
WHERE l.$idCol IS NULL AND b.$rankingSystemListCol = ?
SQL;
    $this->getEntityManager()->getConnection()->executeUpdate($query, [$list->getId(), $list->getId(), $base->getId()]);
  }

  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * @param RankingSystemInterface $rankingSystem
   * @param \DateTime $from
   * @param \DateTime $to
   * @param array $usedIds
   * @param bool $includeTo
   * @throws \Doctrine\DBAL\DBALException
   */
  private function deleteOldChanges(RankingSystemInterface $rankingSystem, \DateTime $from, \DateTime $to,
                                    array $usedIds, bool $includeTo)
  {
    $changeMeta = $this->getEntityManager()->getClassMetadata(RankingSystemChangeInterface::class);
    $changeTable = $changeMeta->getTableName();
    /** @noinspection PhpUnhandledExceptionInspection */
    $changeHierarchyEntityCol = $changeMeta->getSingleAssociationJoinColumnName('hierarchyEntity');
    $changeIdCol = $changeMeta->getColumnName('id');
    $changeRankingSystemCol = $changeMeta->getSingleAssociationJoinColumnName('rankingSystem');
    $rankingTimeMeta =
      $this->getEntityManager()->getClassMetadata(TournamentHierarchyEntityRankingTimeInterface::class);
    $rankingTimeTable = $rankingTimeMeta->getTableName();
    /** @noinspection PhpUnhandledExceptionInspection */
    $rankingTimeHierarchyEntityCol = $rankingTimeMeta->getSingleAssociationJoinColumnName('hierarchyEntity');
    /** @noinspection PhpUnhandledExceptionInspection */
    $rankingTimeRankingSystemCol = $rankingTimeMeta->getSingleAssociationJoinColumnName('rankingSystem');
    $rankingTimeTimeCol = $rankingTimeMeta->getColumnName('rankingTime');
    $rankingTimeIdCol = $rankingTimeMeta->getColumnName('id');

    $toComparison = $includeTo ? "<=" : "<";
    $idList = "";
    for ($i = 0; $i < count($usedIds); $i++) {
      if ($idList != "") {
        $idList .= ",";
      }
      $idList .= '?';
    }
    $idListCondition = "";
    if (count($usedIds) > 0) {
      $idListCondition = "AND c.$changeIdCol NOT IN ($idList)";
    }
    $query = <<<SQL
DELETE c
FROM $changeTable AS c
INNER JOIN $rankingTimeTable t
  ON t.$rankingTimeHierarchyEntityCol = c.$changeHierarchyEntityCol AND t.$rankingTimeRankingSystemCol = ?
WHERE c.$changeRankingSystemCol = ? AND t.$rankingTimeTimeCol >= ? AND t.$rankingTimeTimeCol $toComparison ?
  $idListCondition
SQL;
    $this->getEntityManager()->getConnection()->executeUpdate($query,
      array_merge([$rankingSystem->getId(), $rankingSystem->getId(),
        $from->format('Y-m-d H:i:s'), $to->format('Y-m-d H:i:s')], $usedIds));
  }

  /**
   *
   */
  private function flushAndForgetEntities()
  {
    $this->entityManager->flush();
    //manually detaching TournamentHierarchyEntities (TODO change this)
    $identityMap = $this->getEntityManager()->getUnitOfWork()->getIdentityMap();
    foreach ($this->toForgetClasses as $class) {
      if (array_key_exists($class, $identityMap)) {
        foreach ($identityMap[$class] as $entity) {
          $this->entityManager->detach($entity);
        }
      }
    }


    /*for ($i = 0; $i < $current; $i++) {
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
      $this->entityManager->detach($entities[$i]);
      if (array_key_exists($eId, $this->changes)) {
        foreach ($this->changes[$eId] as $pId => $change) {
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
    $current = 0;*/
  }

  /**
   * @param RankingSystemListInterface $list
   * @param mixed[][] $entities
   * @return RankingSystemChangeInterface[][]
   */
  private function getChangesOfEntities(RankingSystemListInterface $list, array $entities)
  {
    if (count($entities) == 0) {
      return [];
    }
    $entityIds = array_map(function (array $a) {
      return $a[0]->getId();
    }, $entities);
    $qb = $this->getEntityManager()->createQueryBuilder();
    /** @var RankingSystemChangeInterface[] $changes */
    $changes = $qb->from(RankingSystemChangeInterface::class, 'e')
      ->select('e')
      ->where('e.rankingSystem = :rankingSystem')
      ->andWhere($qb->expr()->in('e.hierarchyEntity', $entityIds))
      ->setParameter('rankingSystem', $list->getRankingSystem())
      ->getQuery()
      ->getResult();
    $result = [];
    foreach ($changes as $change) {
      if (!array_key_exists($change->getHierarchyEntity()->getId(), $result)) {
        $result[$change->getHierarchyEntity()->getId()] = [];
      }
      if (!array_key_exists($change->getPlayer()->getId(), $result[$change->getHierarchyEntity()->getId()])) {
        $result[$change->getHierarchyEntity()->getId()][$change->getPlayer()->getId()] = $change;
      }
    }
    return $result;
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
   * @param RankingSystemListInterface $list
   * @param \DateTime $lastTime
   * @param int|null $lastId
   * @param int $pageSize
   * @return TournamentHierarchyEntity[][]|\DateTime[][]|int[][]
   */
  private function getEntities(RankingSystemListInterface $list, \DateTime $lastTime, ?int $lastId,
                               int $pageSize): array
  {
    $qb = $this->getEntityManager()->createQueryBuilder();
    $where = null;
    if ($lastId !== null) {
      $where = $qb->expr()->orX(
        't.rankingTime > :lastTime',
        't.id > :lastId'
      );
      $qb->setParameter('lastId', $lastId);
    } else {
      $where = 't.rankingTime > :lastTime';
    }
    $qb = $qb->from($this->getEntriesClass(), 'e')
      ->select('e')
      ->addSelect('t.rankingTime AS time')
      ->addSelect('t.id AS id')
      ->innerJoin(TournamentHierarchyEntityRankingTimeInterface::class, 't', Join::WITH,
        $qb->expr()->eq('t.hierarchyEntity', 'e'))
      ->where('t.rankingTime >= :lastTime')
      ->andWhere($where)
      ->andWhere($qb->expr()->eq('t.rankingSystem', ':rankingSystem'))
      ->orderBy('t.rankingTime')
      ->addOrderBy('t.id')
      ->setMaxResults($pageSize)
      ->setParameter('lastTime', $lastTime)
      ->setParameter('rankingSystem', $list->getRankingSystem());
    if (!$list->isCurrent()) {
      $qb->andWhere('t.rankingTime <= :maxTime')
        ->setParameter('maxTime', $list->getLastEntryTime());
    }
    return $qb->getQuery()->getResult();
  }

  /**
   * @param RankingSystemListInterface $list
   * @param TournamentHierarchyEntity[][]|\DateTime[][]|int[][] $entities
   * @return RankingSystemListEntryInterface[]
   */
  private function getEntries(RankingSystemListInterface $list, array $entities)
  {
    $playerIds = [];
    $entities = array_map(function ($a) {
      return $a[0];
    }, $entities);
    $this->loadAllPlayersOfEntities($entities);
    foreach ($entities as $entity) {
      foreach ($this->getPlayersOfEntity($entity) as $player) {
        if (!array_key_exists($player->getId(), $playerIds)) {
          $playerIds[$player->getId()] = true;
        }
      }
    }
    if (count($playerIds) == 0) {
      return [];
    }
    $qb = $this->getEntityManager()->createQueryBuilder();
    /** @var RankingSystemListEntryInterface[] $entries */
    $entries = $qb->from(RankingSystemListEntryInterface::class, 'e')
      ->select('e')
      ->where('e.rankingSystemList = :list')
      ->andWhere($qb->expr()->in('e.player', array_keys($playerIds)))
      ->setParameter('list', $list)
      ->getQuery()
      ->getResult();
    $result = [];
    foreach ($entries as $entry) {
      $result[$entry->getPlayer()->getId()] = $entry;
    }
    return $result;
  }

  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * @return \DateTime
   */
  private function getMaxDate(): \DateTime
  {
    /** @noinspection PhpUnhandledExceptionInspection */
    return (new \DateTime())->add(new \DateInterval('P100Y'));
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
      return $this->getMaxDate();
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
   * @throws \Doctrine\DBAL\DBALException
   */
  private function recomputeBasedOn(RankingSystemListInterface $list, RankingSystemListInterface $base)
  {
    $lastListTime = $base->getLastEntryTime();
    $nextGeneration = $this->getNextGenerationTime($lastListTime, $list->getRankingSystem()->getGenerationInterval());
    $this->cloneInto($list, $base);
    $finished = false;
    $lastId = null;
    $lastTime = $base->getLastEntryTime();
    $pageSize = env('RANKING_PAGE_SIZE', 100);
    //echo("Identity Map:\n");
    $i = 0;
    $oldUsedChangeIds = [];
    while (!$finished) {
      $i++;
      //get next page of entities
      $entities = $this->getEntities($list, $lastTime, $lastId, $pageSize);
      $finished = count($entities) < $pageSize;
      //getEntries
      $entries = $this->getEntries($list, $entities);
      //getChanges
      $changes = $this->getChangesOfEntities($list, $entities);
      $usedChangesIds = [];
      $lastTimeBefore = $lastTime;
      foreach ($entities as $i => $res) {
        /** @var TournamentHierarchyEntity $entity */
        $entity = $res[0];
        /** @var \DateTime $time */
        $time = $res['time'];
        if ($lastTimeBefore <= $base->getLastEntryTime()) {
          $lastTimeBefore = $time;
        }
        $id = $res['id'];
        assert($list->isCurrent() || $time < $list->getLastEntryTime());
        assert($time > $lastTime || ($time == $lastTime && $id > $lastId));
        $lastTime = $time;
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
        $oldChanges = [];
        if (array_key_exists($entity->getId(), $changes)) {
          $oldChanges = $changes[$entity->getId()];
        }

        $newChanges = $this->getChanges($entity, $list, $oldChanges, $entries);
        foreach ($newChanges as $change) {
          $usedChangesIds[] = $change->getId();
          $entry = $this->getOrCreateRankingSystemListEntry($list, $change->getPlayer(), $entries);
          $entry->setNumberRankedEntities($entry->getNumberRankedEntities() + 1);
          $pointsAfterwards = $entry->getPoints() + $change->getPointsChange();
          $entry->setPoints($pointsAfterwards);
          $entry->setLastChange($time);
          $change->setPointsAfterwards($pointsAfterwards);
          //apply further changes
          foreach ($this->getAdditionalFields() as $field => $value) {
            // PropertyNotExistingException => entry and field have exactly the static properties from
            // getAdditionalFields
            /** @noinspection PhpUnhandledExceptionInspection */
            $entry->setProperty($field, $entry->getProperty($field) + $change->getProperty($field));
          }
          if ($time > $list->getLastEntryTime()) {
            $list->setLastEntryTime($time);
          }
        }
      }
      //delete unused changes
      $this->deleteOldChanges($list->getRankingSystem(), $lastTimeBefore, $lastTime,
        array_merge($oldUsedChangeIds, $usedChangesIds), $finished);
      if ($lastTimeBefore == $lastTime) {
        $oldUsedChangeIds = array_merge($oldUsedChangeIds, $usedChangesIds);
      } else {
        $oldUsedChangeIds = $usedChangesIds;
      }
      $this->flushAndForgetEntities();
    }
  }

  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * @param RankingSystemListEntryInterface $entry
   */
  private function resetListEntry(RankingSystemListEntryInterface $entry)
  {
    $entry->setPoints($this->startPoints());
    $entry->setNumberRankedEntities(0);
    $entry->setLastChange(new \DateTime("2000-01-01"));
    foreach ($this->getAdditionalFields() as $field => $value) {
      // PropertyNotExistingException => we know for sure that the property exists (see 2 lines above)
      /** @noinspection PhpUnhandledExceptionInspection */
      $entry->setProperty($field, $value);
    }
  }
//</editor-fold desc="Private Methods">
}