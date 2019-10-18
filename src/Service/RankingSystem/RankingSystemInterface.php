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
   * @param TournamentInterface $tournament
   * @return DateTime|null the earliest influence or null iff no entity above the level of this ranking has the
   *                       ranking in its rankingSystems
   */
  public function getEarliestInfluence(\Tfboe\FmLib\Entity\RankingSystemInterface $ranking,
                                       TournamentInterface $tournament): ?DateTime;

  /**
   * Updates the rankings for this tournament
   * @param \Tfboe\FmLib\Entity\RankingSystemInterface $ranking
   * @param TournamentInterface $tournament
   * @param DateTime|null $oldInfluence if the tournament changed this is the earliest influence of the
   *                       tournament before the change
   */
  public function updateRankingForTournament(\Tfboe\FmLib\Entity\RankingSystemInterface $ranking,
                                             TournamentInterface $tournament, ?DateTime $oldInfluence);

  /**
   * Updates the rankings assuming all changes happened after $from.
   * @param \Tfboe\FmLib\Entity\RankingSystemInterface $ranking
   * @param DateTime $from
   */
  public function updateRankingFrom(\Tfboe\FmLib\Entity\RankingSystemInterface $ranking, DateTime $from);
//</editor-fold desc="Public Methods">
}