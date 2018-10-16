<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 2:34 PM
 */

namespace Tfboe\FmLib\Service\RankingSystem;


use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Tfboe\FmLib\Entity\CompetitionInterface;
use Tfboe\FmLib\Entity\GameInterface;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\MatchInterface;
use Tfboe\FmLib\Entity\PhaseInterface;
use Tfboe\FmLib\Entity\RankingSystemInterface;
use Tfboe\FmLib\Entity\TournamentHierarchyEntityRankingTimeInterface;
use Tfboe\FmLib\Entity\TournamentInterface;
use Tfboe\FmLib\Helpers\Level;


/**
 * Class GameRankingSystemService
 * @package Tfboe\FmLib\Service\RankingSystemService
 */
abstract class GameRankingSystemService extends RankingSystemService implements GameRankingSystemInterface
{
//<editor-fold desc="Protected Methods">
  /**
   * @inheritDoc
   */
  protected function getEntitiesQueryBuilder(RankingSystemInterface $ranking, \DateTime $from,
                                             \DateTime $to): QueryBuilder
  {
    // query all relevant games
    $query = $this->getEntityManager()->createQueryBuilder();
    $query
      ->from(GameInterface::class, 'g')
      ->select('g')
      ->leftJoin('g.rankingSystems', 'grs', Query\Expr\Join::WITH, $query->expr()->eq('grs', ':ranking'))
      ->innerJoin('g.match', 'm')
      ->leftJoin('m.rankingSystems', 'mrs', Query\Expr\Join::WITH, $query->expr()->eq('mrs', ':ranking'))
      ->innerJoin('m.phase', 'p')
      ->leftJoin('p.rankingSystems', 'prs', Query\Expr\Join::WITH, $query->expr()->eq('prs', ':ranking'))
      ->innerJoin('p.competition', 'c')
      ->leftJoin('c.rankingSystems', 'crs', Query\Expr\Join::WITH, $query->expr()->eq('crs', ':ranking'))
      ->innerJoin('c.tournament', 't')
      ->leftJoin('t.rankingSystems', 'trs', Query\Expr\Join::WITH, $query->expr()->eq('trs', ':ranking'))
      ->setParameter('ranking', $ranking)
      ->andWhere("COALESCE(g.endTime, g.startTime, m.endTime, m.startTime, p.endTime, p.startTime, c.endTime, " .
        "c.startTime, t.endTime, t.startTime, t.updatedAt) > :from")
      ->setParameter('from', $from)
      ->andWhere("COALESCE(g.endTime, g.startTime, m.endTime, m.startTime, p.endTime, p.startTime, c.endTime, " .
        "c.startTime, t.endTime, t.startTime, t.updatedAt) <= :to")
      ->setParameter('to', $to)
      ->andWhere($query->expr()->orX(
        $query->expr()->isNotNull('grs.id'),
        $query->expr()->isNotNull('mrs.id'),
        $query->expr()->isNotNull('prs.id'),
        $query->expr()->isNotNull('crs.id'),
        $query->expr()->isNotNull('trs.id')
      ));

    return $query;
  }

  /**
   * @param array $values
   * @return mixed|null
   */
  private function min(array $values)
  {
    $min = null;
    foreach ($values as $v) {
      if ($min === null || ($v !== null && $v < $min)) {
        $min = $v;
      }
    }
    return $min;
  }

  /**
   * @inheritDoc
   */
  public function getEarliestInfluence(RankingSystemInterface $ranking,
                                       TournamentHierarchyEntity $entity, ?array $entityChangeSet = null): ?\DateTime
  {
    if ($entityChangeSet !== null) {
      if (array_key_exists('endTime', $entityChangeSet)) {
        return $this->min($entityChangeSet['endTime']);
      }
      if ($entity->getEndTime() !== null) {
        if ($entity instanceof GameInterface) {
          return $entity->getEndTime();
        } else {
          return null;
        }
      }
      if (array_key_exists('startTime', $entityChangeSet)) {
        return $this->min($entityChangeSet['startTime']);
      }
      if ($entity->getStartTime() !== null) {
        if ($entity instanceof GameInterface) {
          return $entity->getStartTime();
        } else {
          return null;
        }
      }
      if ($entity instanceof TournamentInterface) {
        if (array_key_exists('updatedAt', $entityChangeSet)) {
          return $this->min($entityChangeSet['updatedAt']);
        }
        return null;
      } else if ($entity instanceof GameInterface) {
        //do the same as without a change set, see below
      } else {
        return null;
      }
    }

    if ($entity->getEndTime() !== null) {
      return $entity->getEndTime();
    } else if ($entity->getStartTime() !== null) {
      return $entity->getStartTime();
    }
    while ($entity->getParent() !== null) {
      $entity = $entity->getParent();
      if ($entity->getEndTime() !== null) {
        return $entity->getEndTime();
      } else if ($entity->getStartTime() !== null) {
        return $entity->getStartTime();
      }
    }
    if ($entity instanceof TournamentInterface) {
      /** @var $entity TournamentInterface */
      if ($entity->getUpdatedAt() !== null) {
        return $entity->getUpdatedAt();
      }
    }
    return null;
  }


  /**
   * @inheritDoc
   */
  protected function getEntriesClass(): string
  {
    return GameInterface::class;
  }

  /**
   * @inheritDoc
   */
  protected function getLevel(): int
  {
    return Level::GAME;
  }

  /**
   * @inheritDoc
   */
  protected function getPlayersOfEntity(TournamentHierarchyEntity $entity): array
  {
    if ($entity instanceof GameInterface) {
      /** @var GameInterface $entity */
      return array_merge($entity->getPlayersA()->toArray(), $entity->getPlayersB()->toArray());
    }
    return [];
  }

  /**
   * @inheritDoc
   */
  protected function loadAllPlayersOfEntities(array $entities): void
  {
    $this->loadingService->loadEntities($entities, [
      GameInterface::class => [['playersA', 'playersB']]
    ]);
  }

  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * @param \DateTime $from
   * @param RankingSystemInterface $rankingSystem
   * @throws \Doctrine\DBAL\DBALException
   */
  protected function updateRankingTimes(\DateTime $from, RankingSystemInterface $rankingSystem)
  {
    $rankingTimesMeta = $this->getEntityManager()
      ->getClassMetadata(TournamentHierarchyEntityRankingTimeInterface::class);
    $rankingTimesTable = $rankingTimesMeta->getTableName();
    /** @noinspection PhpUnhandledExceptionInspection */
    $rankingTimesRankingSystemCol = $rankingTimesMeta->getSingleAssociationJoinColumnName('rankingSystem');
    /** @noinspection PhpUnhandledExceptionInspection */
    $rankingTimesHierarchyEntityCol =
      $rankingTimesMeta->getSingleAssociationJoinColumnName('hierarchyEntity');
    $rankingTimesRankingTimeCol = $rankingTimesMeta->getColumnName('rankingTime');

    $hierarchyEntityMeta = $this->getEntityManager()->getClassMetadata(TournamentHierarchyEntity::class);
    $hierarchyEntityTable = $hierarchyEntityMeta->getTableName();
    $hierarchyEntityIdCol = $hierarchyEntityMeta->getColumnName('id');

    $gameMeta = $this->getEntityManager()->getClassMetadata(GameInterface::class);
    $gameTable = $gameMeta->getTableName();
    $gameIdCol = $gameMeta->getColumnName('id');
    $gameStartTimeCol = $gameMeta->getColumnName('startTime');
    $gameEndTimeCol = $gameMeta->getColumnName('endTime');
    /** @noinspection PhpUnhandledExceptionInspection */
    $gameMatchCol = $gameMeta->getSingleAssociationJoinColumnName('match');
    /** @noinspection PhpUnhandledExceptionInspection */
    $gameRankingSystemsAssociation = $gameMeta->getAssociationMapping('rankingSystems');
    $gameRankingSystemsAssociationTable = $gameRankingSystemsAssociation['joinTable']['name'];
    $gameRankingSystemsAssociationGameIdCol =
      array_keys($gameRankingSystemsAssociation['relationToSourceKeyColumns'])[0];
    $gameRankingSystemsAssociationRankingSystemIdCol =
      array_keys($gameRankingSystemsAssociation['relationToTargetKeyColumns'])[0];

    $matchMeta = $this->getEntityManager()->getClassMetadata(MatchInterface::class);
    $matchTable = $matchMeta->getTableName();
    $matchIdCol = $matchMeta->getColumnName('id');
    $matchStartTimeCol = $matchMeta->getColumnName('startTime');
    $matchEndTimeCol = $matchMeta->getColumnName('endTime');
    /** @noinspection PhpUnhandledExceptionInspection */
    $matchPhaseCol = $matchMeta->getSingleAssociationJoinColumnName('phase');
    /** @noinspection PhpUnhandledExceptionInspection */
    $matchRankingSystemsAssociation = $matchMeta->getAssociationMapping('rankingSystems');
    $matchRankingSystemsAssociationTable = $matchRankingSystemsAssociation['joinTable']['name'];
    $matchRankingSystemsAssociationMatchIdCol =
      array_keys($matchRankingSystemsAssociation['relationToSourceKeyColumns'])[0];
    $matchRankingSystemsAssociationRankingSystemIdCol =
      array_keys($matchRankingSystemsAssociation['relationToTargetKeyColumns'])[0];

    $phaseMeta = $this->getEntityManager()->getClassMetadata(PhaseInterface::class);
    $phaseTable = $phaseMeta->getTableName();
    $phaseIdCol = $phaseMeta->getColumnName('id');
    $phaseStartTimeCol = $phaseMeta->getColumnName('startTime');
    $phaseEndTimeCol = $phaseMeta->getColumnName('endTime');
    /** @noinspection PhpUnhandledExceptionInspection */
    $phaseCompetitionCol = $phaseMeta->getSingleAssociationJoinColumnName('competition');
    /** @noinspection PhpUnhandledExceptionInspection */
    $phaseRankingSystemsAssociation = $phaseMeta->getAssociationMapping('rankingSystems');
    $phaseRankingSystemsAssociationTable = $phaseRankingSystemsAssociation['joinTable']['name'];
    $phaseRankingSystemsAssociationPhaseIdCol =
      array_keys($phaseRankingSystemsAssociation['relationToSourceKeyColumns'])[0];
    $phaseRankingSystemsAssociationRankingSystemIdCol =
      array_keys($phaseRankingSystemsAssociation['relationToTargetKeyColumns'])[0];

    $competitionMeta = $this->getEntityManager()->getClassMetadata(CompetitionInterface::class);
    $competitionTable = $competitionMeta->getTableName();
    $competitionIdCol = $competitionMeta->getColumnName('id');
    $competitionStartTimeCol = $competitionMeta->getColumnName('startTime');
    $competitionEndTimeCol = $competitionMeta->getColumnName('endTime');
    /** @noinspection PhpUnhandledExceptionInspection */
    $competitionTournamentCol = $competitionMeta->getSingleAssociationJoinColumnName('tournament');
    /** @noinspection PhpUnhandledExceptionInspection */
    $competitionRankingSystemsAssociation = $competitionMeta->getAssociationMapping('rankingSystems');
    $competitionRankingSystemsAssociationTable = $competitionRankingSystemsAssociation['joinTable']['name'];
    $competitionRankingSystemsAssociationCompetitionIdCol =
      array_keys($competitionRankingSystemsAssociation['relationToSourceKeyColumns'])[0];
    $competitionRankingSystemsAssociationRankingSystemIdCol =
      array_keys($competitionRankingSystemsAssociation['relationToTargetKeyColumns'])[0];

    $tournamentMeta = $this->getEntityManager()->getClassMetadata(TournamentInterface::class);
    $tournamentTable = $tournamentMeta->getTableName();
    $tournamentIdCol = $tournamentMeta->getColumnName('id');
    $tournamentStartTimeCol = $tournamentMeta->getColumnName('startTime');
    $tournamentEndTimeCol = $tournamentMeta->getColumnName('endTime');
    $tournamentUpdatedAtCol = $tournamentMeta->getColumnName('updatedAt');
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournamentRankingSystemsAssociation = $tournamentMeta->getAssociationMapping('rankingSystems');
    $tournamentRankingSystemsAssociationTable = $tournamentRankingSystemsAssociation['joinTable']['name'];
    $tournamentRankingSystemsAssociationTournamentIdCol =
      array_keys($tournamentRankingSystemsAssociation['relationToSourceKeyColumns'])[0];
    $tournamentRankingSystemsAssociationRankingSystemIdCol =
      array_keys($tournamentRankingSystemsAssociation['relationToTargetKeyColumns'])[0];

    $rankingTimePart = <<<SQL
COALESCE(
  gh.$gameEndTimeCol, gh.$gameStartTimeCol, 
  mh.$matchEndTimeCol, mh.$matchStartTimeCol, 
  ph.$phaseEndTimeCol, ph.$phaseStartTimeCol, 
  ch.$competitionEndTimeCol, ch.$competitionStartTimeCol, 
  th.$tournamentEndTimeCol, th.$tournamentStartTimeCol, t.$tournamentUpdatedAtCol)
SQL;


    $query = <<<SQL
INSERT INTO $rankingTimesTable ($rankingTimesRankingSystemCol, $rankingTimesHierarchyEntityCol, 
                                $rankingTimesRankingTimeCol)
SELECT ?, g.$gameIdCol, $rankingTimePart AS rankingTime
FROM $gameTable AS g
INNER JOIN $hierarchyEntityTable AS gh
  ON g.$gameIdCol = gh.$hierarchyEntityIdCol
LEFT JOIN $gameRankingSystemsAssociationTable AS grs
  ON grs.$gameRankingSystemsAssociationGameIdCol = g.$gameIdCol 
    AND grs.$gameRankingSystemsAssociationRankingSystemIdCol = ?
INNER JOIN $matchTable AS m
  ON m.$matchIdCol = g.$gameMatchCol
INNER JOIN $hierarchyEntityTable AS mh
  ON m.$matchIdCol = mh.$hierarchyEntityIdCol
LEFT JOIN $matchRankingSystemsAssociationTable AS mrs
  ON mrs.$matchRankingSystemsAssociationMatchIdCol = m.$matchIdCol
    AND mrs.$matchRankingSystemsAssociationRankingSystemIdCol = ?
INNER JOIN $phaseTable AS p
  ON p.$phaseIdCol = m.$matchPhaseCol
INNER JOIN $hierarchyEntityTable AS ph
  ON p.$phaseIdCol = ph.$hierarchyEntityIdCol
LEFT JOIN $phaseRankingSystemsAssociationTable AS prs
  ON prs.$phaseRankingSystemsAssociationPhaseIdCol = p.$phaseIdCol
    AND prs.$phaseRankingSystemsAssociationRankingSystemIdCol = ?
INNER JOIN $competitionTable AS c
  ON c.$competitionIdCol = p.$phaseCompetitionCol
INNER JOIN $hierarchyEntityTable AS ch
  ON c.$competitionIdCol = ch.$hierarchyEntityIdCol
LEFT JOIN $competitionRankingSystemsAssociationTable AS crs
  ON crs.$competitionRankingSystemsAssociationCompetitionIdCol = c.$competitionIdCol
    AND crs.$competitionRankingSystemsAssociationRankingSystemIdCol = ?
INNER JOIN $tournamentTable AS t
  ON t.$tournamentIdCol = c.$competitionTournamentCol
INNER JOIN $hierarchyEntityTable AS th
  ON t.$tournamentIdCol = th.$hierarchyEntityIdCol
LEFT JOIN $tournamentRankingSystemsAssociationTable AS trs
  ON trs.$tournamentRankingSystemsAssociationTournamentIdCol = t.$tournamentIdCol
    AND trs.$tournamentRankingSystemsAssociationRankingSystemIdCol = ?
WHERE ($rankingTimePart > ?) AND 
      (grs.$gameRankingSystemsAssociationGameIdCol IS NOT NULL OR 
      mrs.$matchRankingSystemsAssociationMatchIdCol IS NOT NULL OR 
      prs.$phaseRankingSystemsAssociationPhaseIdCol IS NOT NULL OR 
      crs.$competitionRankingSystemsAssociationCompetitionIdCol IS NOT NULL OR 
      trs.$tournamentRankingSystemsAssociationTournamentIdCol IS NOT NULL)
ORDER BY rankingTime ASC, g.$gameIdCol ASC
SQL;
    $this->getEntityManager()->getConnection()->executeUpdate("TRUNCATE TABLE $rankingTimesTable");
    $this->getEntityManager()->getConnection()->executeUpdate($query, [
      $rankingSystem->getId(), $rankingSystem->getId(), $rankingSystem->getId(), $rankingSystem->getId(),
      $rankingSystem->getId(), $rankingSystem->getId(), $from->format('Y-m-d H:i:s')
    ]);

    /* "INSERT INTO $rankingTimesTable ($rankingTimesRankingSystemCol, $rankingTimesHierarchyEntityCol, " .
     "$rankingTimesRankingTimeCol)"


   $query = $this->getEntityManager()->createQueryBuilder();
   $query
     ->update(GameInterface::class, 'g')
     ->leftJoin('g.rankingSystems', 'grs', Query\Expr\Join::WITH, $query->expr()->eq('grs', ':ranking'))
     ->innerJoin('g.match', 'm')
     ->leftJoin('m.rankingSystems', 'mrs', Query\Expr\Join::WITH, $query->expr()->eq('mrs', ':ranking'))
     ->innerJoin('m.phase', 'p')
     ->leftJoin('p.rankingSystems', 'prs', Query\Expr\Join::WITH, $query->expr()->eq('prs', ':ranking'))
     ->innerJoin('p.competition', 'c')
     ->leftJoin('c.rankingSystems', 'crs', Query\Expr\Join::WITH, $query->expr()->eq('crs', ':ranking'))
     ->innerJoin('c.tournament', 't')
     ->leftJoin('t.rankingSystems', 'trs', Query\Expr\Join::WITH, $query->expr()->eq('trs', ':ranking'))
     ->setParameter('ranking', $rankingSystem)
     ->andWhere($query->expr()->orX(
       $query->expr()->isNull('g.rankingTime'),
       $query->expr()->lte('g.rankingTime', ':from')
     ))
     ->andWhere($query->expr()->orX(
       $query->expr()->isNotNull('grs.id'),
       $query->expr()->isNotNull('mrs.id'),
       $query->expr()->isNotNull('prs.id'),
       $query->expr()->isNotNull('crs.id'),
       $query->expr()->isNotNull('trs.id')
     ))
     ->set('g.rankingTime', "COALESCE(g.endTime, g.startTime, m.endTime, m.startTime, p.endTime, p.startTime, " .
       "c.endTime, c.startTime, t.endTime, t.startTime, t.updatedAt)")
     ->setParameter('from', $from)
     ->getQuery();*/
  }
//</editor-fold desc="Protected Methods">

}