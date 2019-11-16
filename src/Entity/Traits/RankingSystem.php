<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 8:47 PM
 */

namespace Tfboe\FmLib\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\AutomaticInstanceGeneration;
use Tfboe\FmLib\Entity\Helpers\NameEntity;
use Tfboe\FmLib\Entity\Helpers\SubClassData;
use Tfboe\FmLib\Entity\Helpers\TimestampableEntity;
use Tfboe\FmLib\Entity\Helpers\UUIDEntity;
use Tfboe\FmLib\Entity\RankingSystemListInterface;
use Tfboe\FmLib\Entity\TournamentInterface;
use Tfboe\FmLib\Helpers\DateTime;
use Tfboe\FmLib\Helpers\Level;

/**
 * Trait RankingSystem
 * @package Tfboe\FmLib\Entity\Traits
 */
trait RankingSystem
{
  use SubClassData;
  use TimestampableEntity;
  use UUIDEntity;
  use NameEntity;

  //<editor-fold desc="Fields">

  /**
   * @ORM\Column(type="string")
   *
   * @var string
   */
  private $serviceName;
  /**
   * @ORM\Column(type="smallint", nullable=true)
   * @var int|null
   */
  private $defaultForLevel;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $generationInterval;

  /**
   * @ORM\ManyToMany(
   *     targetEntity="\Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity",
   *     mappedBy="rankingSystems",
   *     indexBy="id"
   * )
   * @ORM\JoinTable(name="relation__tournament_ranking_systems")
   * @var Collection|TournamentInterface[]
   */
  private $hierarchyEntries;
  /**
   * @ORM\Column(type="datetime", nullable=true)
   * @var \DateTime|null
   */
  private $openSyncFrom;

  /**
   * @ORM\OneToMany(targetEntity="\Tfboe\FmLib\Entity\RankingSystemListInterface", mappedBy="rankingSystem",
   *   indexBy="id")
   * @var Collection|RankingSystemListInterface[]
   */
  private $lists;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return int|null
   */
  public function getDefaultForLevel(): ?int
  {
    return $this->defaultForLevel;
  }

  /**
   * @return int
   */
  public function getGenerationInterval(): int
  {
    return $this->generationInterval;
  }

  /**
   * @return TournamentInterface[]|Collection
   */
  public function getHierarchyEntries()
  {
    return $this->hierarchyEntries;
  }

  /**
   * @return RankingSystemListInterface[]|Collection
   */
  public function getLists(): Collection
  {
    return $this->lists;
  }

  /**
   * @return \DateTime|null
   */
  public function getOpenSyncFrom(): ?\DateTime
  {
    return $this->openSyncFrom;
  }

  /**
   * @return string
   */
  public function getServiceName(): string
  {
    return $this->serviceName;
  }

  /**
   * @param int|null $defaultForLevel
   */
  public function setDefaultForLevel(?int $defaultForLevel)
  {
    if ($defaultForLevel !== null) {
      Level::ensureValidValue($defaultForLevel);
    }
    $this->defaultForLevel = $defaultForLevel;
  }

  /**
   * @param int $generationInterval
   */
  public function setGenerationInterval(int $generationInterval)
  {
    AutomaticInstanceGeneration::ensureValidValue($generationInterval);
    $this->generationInterval = $generationInterval;
  }

  /**
   * @param \DateTime|null $openSyncFrom
   */
  public function setOpenSyncFrom(?\DateTime $openSyncFrom)
  {
    if (!DateTime::eq($this->openSyncFrom, $openSyncFrom)) {
      $this->openSyncFrom = $openSyncFrom;
    }
  }

  /**
   * @param string $serviceName
   */
  public function setServiceName(string $serviceName)
  {
    $this->serviceName = $serviceName;
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Final Methods">
  /**
   * RankingSystem init
   * @param string[] $keys the keys of the subclass properties
   */
  protected final function init(array $keys)
  {
    $this->initSubClassData($keys);
    $this->generationInterval = AutomaticInstanceGeneration::OFF;
    $this->defaultForLevel = null;
    $this->openSyncFrom = null;
    $this->lists = new ArrayCollection();
    $this->hierarchyEntries = new ArrayCollection();
  }
//</editor-fold desc="Protected Final Methods">
}