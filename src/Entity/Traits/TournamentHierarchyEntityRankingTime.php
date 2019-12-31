<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/12/18
 * Time: 12:39 PM
 */

namespace Tfboe\FmLib\Entity\Traits;

use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\RankingSystemInterface;

/**
 * Trait TournamentHierarchyEntityRankingTime
 * @package Tfboe\FmLib\Entity\Traits
 */
trait TournamentHierarchyEntityRankingTime
{
//<editor-fold desc="Fields">
  /**
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer")
   * @var integer
   */
  private $id;

  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\RankingSystemInterface")
   * @var RankingSystemInterface
   */
  private $rankingSystem;

  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity")
   * @var TournamentHierarchyEntity
   */
  private $hierarchyEntity;

  /**
   * @ORM\Column(type="datetime")
   * @var \DateTime
   */
  private $rankingTime;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return string|int
   */
  public function getEntityId()
  {
    return $this->id;
  }

  /**
   * @return TournamentHierarchyEntity
   */
  public function getHierarchyEntity(): TournamentHierarchyEntity
  {
    return $this->hierarchyEntity;
  }

  /**
   * @return int
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * @return RankingSystemInterface
   */
  public function getRankingSystem(): RankingSystemInterface
  {
    return $this->rankingSystem;
  }

  /**
   * @return \DateTime
   */
  public function getRankingTime(): \DateTime
  {
    return $this->rankingTime;
  }
//</editor-fold desc="Public Methods">
}