<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 8:47 PM
 */

namespace Tfboe\FmLib\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\AutomaticInstanceGeneration;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\Helpers\NameEntity;
use Tfboe\FmLib\Entity\Helpers\SubClassData;
use Tfboe\FmLib\Entity\Helpers\TimestampableEntity;
use Tfboe\FmLib\Entity\Helpers\UUIDEntity;
use Tfboe\FmLib\Exceptions\ValueNotValid;
use Tfboe\FmLib\Helpers\Level;

/**
 * Class RankingSystemService
 * @package Tfboe\FmLib\Entity
 * @ORM\Entity
 * @ORM\Table(name="rankingSystems")
 */
class RankingSystem extends BaseEntity
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
   * @var Collection|Tournament[]
   */
  private $hierarchyEntries;
  /**
   * @ORM\Column(type="datetime", nullable=true)
   * @var \DateTime|null
   */
  private $openSyncFrom;
  /**
   * @ORM\OneToMany(targetEntity="RankingSystemList", mappedBy="rankingSystem", indexBy="id")
   * @var Collection|RankingSystemList[]
   */
  private $lists;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * TournamentRankingSystem constructor.
   * @param string[] $keys the keys of the subclass properties
   */
  public function __construct(array $keys)
  {
    $this->initSubClassData($keys);
    $this->generationInterval = AutomaticInstanceGeneration::OFF;
    $this->defaultForLevel = null;
    $this->openSyncFrom = null;
    $this->lists = new ArrayCollection();
    $this->hierarchyEntries = new ArrayCollection();
  }
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
   * @return Tournament[]|Collection
   */
  public function getHierarchyEntries()
  {
    return $this->hierarchyEntries;
  }

  /**
   * @return RankingSystemList[]|Collection
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
   * @return $this|RankingSystem
   * @throws ValueNotValid
   */
  public function setDefaultForLevel(?int $defaultForLevel): RankingSystem
  {
    if ($defaultForLevel !== null) {
      Level::ensureValidValue($defaultForLevel);
    }
    $this->defaultForLevel = $defaultForLevel;
    return $this;
  }

  /**
   * @param int $generationInterval
   * @return $this|RankingSystem
   * @throws \Tfboe\FmLib\Exceptions\ValueNotValid
   */
  public function setGenerationInterval(int $generationInterval): RankingSystem
  {
    AutomaticInstanceGeneration::ensureValidValue($generationInterval);
    $this->generationInterval = $generationInterval;
    return $this;
  }

  /**
   * @param \DateTime|null $openSyncFrom
   * @return $this|RankingSystem
   */
  public function setOpenSyncFrom(?\DateTime $openSyncFrom): RankingSystem
  {
    $this->openSyncFrom = $openSyncFrom;
    return $this;
  }

  /**
   * @param string $serviceName
   * @return $this|RankingSystem
   */
  public function setServiceName(string $serviceName): RankingSystem
  {
    $this->serviceName = $serviceName;
    return $this;
  }
//</editor-fold desc="Public Methods">
}