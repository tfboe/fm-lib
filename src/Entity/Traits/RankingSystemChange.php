<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/5/18
 * Time: 10:54 PM
 */

namespace Tfboe\FmLib\Entity\Traits;


use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\SubClassData;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;
use Tfboe\FmLib\Entity\Helpers\UUIDEntity;
use Tfboe\FmLib\Entity\PlayerInterface;
use Tfboe\FmLib\Entity\RankingSystemInterface;

/**
 * Trait RankingSystemChange
 * @package Tfboe\FmLib\Entity\Traits
 */
trait RankingSystemChange
{
  use UUIDEntity;
  use SubClassData;

//<editor-fold desc="Fields">
  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\RankingSystemInterface", inversedBy="changes")
   * @var RankingSystemInterface
   */
  private $rankingSystem;

  /**
   * @ORM\Column(type="float")
   * @var float
   */
  private $pointsChange;

  /**
   * @ORM\Column(type="float")
   * @var float
   */
  private $pointsAfterwards;

  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\PlayerInterface")
   * @var PlayerInterface
   */
  private $player;

  /**
   * @ORM\ManyToOne(
   *   targetEntity="\Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity",
   *   inversedBy="rankingSystemChanges"
   * )
   * @var TournamentHierarchyEntity
   */
  private $hierarchyEntity;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return TournamentHierarchyEntity
   */
  public function getHierarchyEntity(): TournamentHierarchyEntity
  {
    return $this->hierarchyEntity;
  }

  /**
   * @return PlayerInterface
   */
  public function getPlayer(): PlayerInterface
  {
    return $this->player;
  }

  /**
   * @return float
   */
  public function getPointsAfterwards(): float
  {
    return $this->pointsAfterwards;
  }

  /**
   * @return float
   */
  public function getPointsChange(): float
  {
    return $this->pointsChange;
  }

  /**
   * @return RankingSystemInterface
   */
  public function getRankingSystem(): RankingSystemInterface
  {
    return $this->rankingSystem;
  }

  /**
   * @param TournamentHierarchyInterface $hierarchyEntity
   */
  public function setHierarchyEntity(TournamentHierarchyInterface $hierarchyEntity)
  {
    $this->hierarchyEntity = $hierarchyEntity;
  }

  /**
   * @param PlayerInterface $player
   */
  public function setPlayer(PlayerInterface $player)
  {
    $this->player = $player;
  }

  /**
   * @param float $pointsAfterwards
   */
  public function setPointsAfterwards(float $pointsAfterwards)
  {
    $this->pointsAfterwards = $pointsAfterwards;
  }

  /**
   * @param float $pointsChange
   */
  public function setPointsChange(float $pointsChange)
  {
    $this->pointsChange = $pointsChange;
  }

  /**
   * @param RankingSystemInterface $rankingSystem
   */
  public function setRankingSystem(RankingSystemInterface $rankingSystem)
  {
    $this->rankingSystem = $rankingSystem;
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Final Methods">
  /**
   * RankingSystemChange init
   * @param string[] $keys list of additional fields
   */
  protected final function init(array $keys)
  {
    $this->initSubClassData($keys);
  }
//</editor-fold desc="Protected Final Methods">

}