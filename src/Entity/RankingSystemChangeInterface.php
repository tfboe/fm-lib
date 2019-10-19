<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:51 PM
 */

namespace Tfboe\FmLib\Entity;

use Tfboe\FmLib\Entity\Helpers\BaseEntityInterface;
use Tfboe\FmLib\Entity\Helpers\SubClassDataInterface;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\Helpers\UUIDEntityInterface;

/**
 * Interface RankingSystemChangeInterface
 * @package Tfboe\FmLib\Entity
 */
interface RankingSystemChangeInterface extends BaseEntityInterface, UUIDEntityInterface, SubClassDataInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return TournamentHierarchyEntity
   */
  public function getHierarchyEntity(): TournamentHierarchyEntity;

  /**
   * @return PlayerInterface
   */
  public function getPlayer(): PlayerInterface;

  /**
   * @return float
   */
  public function getPointsAfterwards(): float;

  /**
   * @return float
   */
  public function getPointsChange(): float;

  /**
   * @return RankingSystemInterface
   */
  public function getRankingSystem(): RankingSystemInterface;

  /**
   * @param TournamentHierarchyEntity $hierarchyEntity
   */
  public function setHierarchyEntity(TournamentHierarchyEntity $hierarchyEntity);

  /**
   * @param PlayerInterface $player
   */
  public function setPlayer(PlayerInterface $player);

  /**
   * @param float $pointsAfterwards
   */
  public function setPointsAfterwards(float $pointsAfterwards);

  /**
   * @param float $pointsChange
   */
  public function setPointsChange(float $pointsChange);

  /**
   * @param RankingSystemInterface $rankingSystem
   */
  public function setRankingSystem(RankingSystemInterface $rankingSystem);
//</editor-fold desc="Public Methods">
}