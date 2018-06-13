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
use Tfboe\FmLib\Entity\GameInterface;
use Tfboe\FmLib\Entity\RankingSystemInterface;
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
  protected function getEntitiesQueryBuilder(RankingSystemInterface $ranking, \DateTime $from, \DateTime $to): QueryBuilder
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
   * @inheritDoc
   */
  protected function getLevel(): int
  {
    return Level::GAME;
  }
//</editor-fold desc="Protected Methods">

}