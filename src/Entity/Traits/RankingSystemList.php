<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/5/18
 * Time: 10:54 PM
 */

namespace Tfboe\FmLib\Entity\Traits;


use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\UUIDEntity;
use Tfboe\FmLib\Entity\RankingSystemInterface;
use Tfboe\FmLib\Entity\RankingSystemListEntryInterface;
use Tfboe\FmLib\Exceptions\Internal;
use Tfboe\FmLib\Helpers\DateTimeHelper;


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
   * @ORM\Column(type="datetime")
   * @var DateTime
   */
  private $lastEntryTime;

  /**
   * @ORM\OneToMany(targetEntity="\Tfboe\FmLib\Entity\RankingSystemListEntryInterface", mappedBy="rankingSystemList",
   *   indexBy="player_id")
   * @var RankingSystemListEntryInterface[]|Collection
   */
  private $entries;

  /**
   * For non-current lists this gives the time up to which entries get added to this list. Note that only entries with a
   * strictly smaller time get added. For example if entryTimeLimit = 2020-01-01 00:00:00 then all entries with times in
   * the year 2019 or earlier get added and an entry with the exact time 2020-01-01 00:00:00 does not get added to this
   * list!
   * @ORM\Column(type="datetime", nullable=true)
   * @var DateTime|null
   */
  private $entryTimeLimit;

  /**
   * @return DateTime|null
   */
  public function getEntryTimeLimit(): ?DateTime
  {
    return $this->entryTimeLimit;
  }

  /**
   * @param DateTime|null $entryTimeLimit
   */
  public function setEntryTimeLimit(?DateTime $entryTimeLimit): void
  {
    if ($entryTimeLimit !== null) {
      Internal::assert($entryTimeLimit > $this->getLastEntryTime());
    }
    $this->entryTimeLimit = $entryTimeLimit;
  }
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
   * @return DateTime
   */
  public function getLastEntryTime(): DateTime
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
    return $this->getEntryTimeLimit() === null;
  }

  /**
   * @param DateTime $lastEntryTime
   */
  public function setLastEntryTime(DateTime $lastEntryTime)
  {
    if (!DateTimeHelper::eq($this->lastEntryTime, $lastEntryTime)) {
      if ($this->getEntryTimeLimit() !== null) {
        Internal::assert($this->getEntryTimeLimit() > $lastEntryTime);
      }
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
  final protected function init()
  {
    $this->lastEntryTime = new DateTime("2000-01-01");
    $this->entryTimeLimit = null;
    $this->entries = new ArrayCollection();
  }
//</editor-fold desc="Protected Final Methods">
}