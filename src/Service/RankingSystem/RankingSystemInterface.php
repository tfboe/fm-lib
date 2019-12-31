<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 2:36 PM
 */

namespace Tfboe\FmLib\Service\RankingSystem;


use DateTime;
use Tfboe\FmLib\Entity\TournamentInterface;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\RecalculationInterface;

/**
 * Interface RankingSystemInterface
 * @package Tfboe\FmLib\Service\RankingSystemService
 */
interface RankingSystemInterface
{
//<editor-fold desc="Public Methods">
  /**
   * The earliest influence date of the given tournament for the ranking system.
   * This method must be called before a tournament changes and the result must then be used for the
   * earliest_old_influence parameter in the updateRankingForTournament method.
   * @param \Tfboe\FmLib\Entity\RankingSystemInterface $ranking
   * @param TournamentHierarchyEntity $entity
   * @param mixed[][]|null $entityChangeSet Maps properties which changed to an array with two elements, the first the
   *                       old value and the second the new value. If this is null that means we consider a new or
   *                       deleted element.
   * @return DateTime|null the earliest influence or null iff the entity/the change has not an influence to the ranking
   */
  public function getEarliestInfluence(\Tfboe\FmLib\Entity\RankingSystemInterface $ranking,
                                       TournamentHierarchyEntity $entity, ?array $entityChangeSet = null): ?\DateTime;

  /**
   * Updates the rankings assuming all changes happened after $from.
   * @param \Tfboe\FmLib\Entity\RankingSystemInterface $ranking
   * @param \DateTime $from
   * @param RecalculationInterface $recalculation
   */
  public function updateRankingFrom(\Tfboe\FmLib\Entity\RankingSystemInterface $ranking, \DateTime $from,
                                    RecalculationInterface $recalculation);
//</editor-fold desc="Public Methods">
}