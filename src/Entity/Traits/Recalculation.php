<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/28/18
 * Time: 2:41 PM
 */

namespace Tfboe\FmLib\Entity\Traits;


use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\NumericalId;
use Tfboe\FmLib\Entity\Helpers\TimeEntity;
use Tfboe\FmLib\Entity\RankingSystemInterface;

/**
 * Trait LastRecalculation
 * @package Tfboe\FmLib\Entity\Traits
 */
trait Recalculation
{
  use NumericalId;
  use TimeEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="integer", nullable=false)
   * @var int
   */
  private $version;

  /**
   * @ORM\OneToOne(targetEntity="\Tfboe\FmLib\Entity\RankingSystemInterface")
   * @var RankingSystemInterface
   */
  private $rankingSystem;

  /**
   * @ORM\Column(type="datetime", nullable=true)
   * @var \DateTime|null
   */
  private $recalculateFrom;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return RankingSystemInterface
   */
  public function getRankingSystem(): RankingSystemInterface
  {
    return $this->rankingSystem;
  }

  /**
   * @return \DateTime|null
   */
  public function getRecalculateFrom(): ?\DateTime
  {
    return $this->recalculateFrom;
  }

  /**
   * @return int
   */
  public function getVersion(): int
  {
    return $this->version;
  }

  /**
   * @param RankingSystemInterface $rankingSystem
   */
  public function setRankingSystem(RankingSystemInterface $rankingSystem): void
  {
    $this->rankingSystem = $rankingSystem;
  }

  /**
   * @param \DateTime|null $recalculateFrom
   */
  public function setRecalculateFrom(?\DateTime $recalculateFrom): void
  {
    $this->recalculateFrom = $recalculateFrom;
  }

  /**
   * @param int $version
   */
  public function setVersion(int $version)
  {
    $this->version = $version;
  }
//</editor-fold desc="Public Methods">
}