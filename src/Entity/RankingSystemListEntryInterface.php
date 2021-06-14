<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:52 PM
 */

namespace Tfboe\FmLib\Entity;

use Tfboe\FmLib\Entity\Helpers\BaseEntityInterface;
use Tfboe\FmLib\Entity\Helpers\SubClassDataInterface;
use Tfboe\FmLib\Entity\Helpers\UUIDEntityInterface;

/**
 * Interface RankingSystemListEntryInterface
 * @package Tfboe\FmLib\Entity
 *
 * Dynamic method hints for Elo ranking
 * @method int getPlayedGames()
 * @method RankingSystemListEntryInterface setPlayedGames(int $playedGames)
 * @method int getRatedGames()
 * @method RankingSystemListEntryInterface setRatedGames(int $ratedGames)
 * @method float getProvisoryRanking()
 * @method RankingSystemListEntryInterface setProvisoryRanking(float $provisoryRanking)
 * @method int getWeightedGames()
 * @method setWeightedGames(int $weightedGames)
 */
interface RankingSystemListEntryInterface extends BaseEntityInterface, UUIDEntityInterface, SubClassDataInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return int
   */
  public function getNumberRankedEntities(): int;

  /**
   * @return PlayerInterface
   */
  public function getPlayer(): PlayerInterface;

  /**
   * @return float
   */
  public function getPoints(): float;

  /**
   * @return RankingSystemListInterface
   */
  public function getRankingSystemList(): RankingSystemListInterface;

  /**
   * @param int $numberRankedEntities
   */
  public function setNumberRankedEntities(int $numberRankedEntities);

  /**
   * @param PlayerInterface $player
   */
  public function setPlayer(PlayerInterface $player);

  /**
   * @param float $points
   */
  public function setPoints(float $points);

  /**
   * @param RankingSystemListInterface $rankingSystemList
   */
  public function setRankingSystemList(RankingSystemListInterface $rankingSystemList);

  /**
   * @param RankingSystemListInterface $rankingSystemList
   */
  public function setRankingSystemListWithoutInitializing(RankingSystemListInterface $rankingSystemList);
//</editor-fold desc="Public Methods">
}