<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/5/18
 * Time: 10:54 PM
 */

namespace Tfboe\FmLib\Entity\Traits;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\UUIDEntity;
use Tfboe\FmLib\Entity\RankingSystemInterface;
use Tfboe\FmLib\Entity\RankingSystemListEntryInterface;
use Tfboe\FmLib\Helpers\DateTime;


/**
 * Trait RankingSystemList
 * @package Tfboe\FmLib\Entity\Traits
 */
trait RankingSystemList
{
  use UUIDEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\RankingSystemInterface", inversedBy="lists")
   * @var RankingSystemInterface
   */
  private $rankingSystem;
  /**
   * @ORM\Column(type="boolean")
   * @var bool
   */
  private $current;
  /**
   * @ORM\Column(type="datetime")
   * @var \DateTime
   */
  private $lastEntryTime;

  /**
   * @ORM\OneToMany(targetEntity="\Tfboe\FmLib\Entity\RankingSystemListEntryInterface", mappedBy="rankingSystemList",
   *   indexBy="player_id")
   * @var RankingSystemListEntryInterface[]|Collection
   */
  private $entries;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return RankingSystemListEntryInterface[]|Collection
   */
  public function getEntries(): Collection
  {
    return $this->entries;
  }

  /**
   * @return \DateTime
   */
  public function getLastEntryTime(): \DateTime
  {
    return $this->lastEntryTime;
  }

  /**
   * @return RankingSystemInterface
   */
  public function getRankingSystem(): RankingSystemInterface
  {
    return $this->rankingSystem;
  }

  /**
   * @return bool
   */
  public function isCurrent(): bool
  {
    return $this->current;
  }

  /**
   * @param bool $current
   */
  public function setCurrent(bool $current)
  {
    $this->current = $current;
  }

  /**
   * @param \DateTime $lastEntryTime
   */
  public function setLastEntryTime(\DateTime $lastEntryTime)
  {
    if (!DateTime::eq($this->lastEntryTime, $lastEntryTime)) {
      $this->lastEntryTime = $lastEntryTime;
    }
  }

  /**
   * @param RankingSystemInterface $rankingSystem
   */
  public function setRankingSystem(RankingSystemInterface $rankingSystem)
  {
    if ($this->rankingSystem !== null) {
      $this->rankingSystem->getLists()->remove($this->getId());
    }
    $this->rankingSystem = $rankingSystem;
    $rankingSystem->getLists()->set($this->getId(), $this);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Final Methods">
  /**
   * RankingSystemList init
   */
  protected final function init()
  {
    $this->lastEntryTime = new \DateTime("2000-01-01");
    $this->current = false;
    $this->entries = new ArrayCollection();
  }
//</editor-fold desc="Protected Final Methods">
}