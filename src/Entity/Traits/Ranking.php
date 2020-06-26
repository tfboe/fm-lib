<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/20/17
 * Time: 12:30 PM
 */

namespace Tfboe\FmLib\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\NameEntity;
use Tfboe\FmLib\Entity\Helpers\UUIDEntity;
use Tfboe\FmLib\Entity\PhaseInterface;
use Tfboe\FmLib\Entity\TeamInterface;

/**
 * Trait Ranking
 * @package Tfboe\FmLib\Entity\Traits
 */
trait Ranking
{
  use UUIDEntity;
  use NameEntity;

//<editor-fold desc="Fields">

  /**
   * @ORM\ManyToMany(targetEntity="\Tfboe\FmLib\Entity\TeamInterface", indexBy="id")
   * @ORM\JoinTable(name="relation__ranking_teams")
   * @var Collection|TeamInterface[]
   */
  private $teams;

  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\PhaseInterface", inversedBy="rankings")
   * @var PhaseInterface
   */
  private $phase;

  /**
   * @ORM\Column(name="`rank`", type="integer")
   * @var int
   */
  private $rank;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $uniqueRank;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return PhaseInterface
   */
  public function getPhase(): PhaseInterface
  {
    return $this->phase;
  }

  /**
   * @return int
   */
  public function getRank(): int
  {
    return $this->rank;
  }

  /**
   * @return TeamInterface[]|Collection
   */
  public function getTeams(): Collection
  {
    return $this->teams;
  }

  /**
   * @return int
   */
  public function getUniqueRank(): int
  {
    return $this->uniqueRank;
  }

  /**
   * @param PhaseInterface $phase
   */
  public function setPhase(PhaseInterface $phase)
  {
    if ($this->phase !== null) {
      $this->phase->getRankings()->remove($this->getId());
    }
    $this->phase = $phase;
    $phase->getRankings()->set($this->getId(), $this);
  }

  /**
   * @param int $rank
   */
  public function setRank(int $rank)
  {
    $this->rank = $rank;
  }

  /**
   * @param int $uniqueRank
   */
  public function setUniqueRank(int $uniqueRank)
  {
    $this->uniqueRank = $uniqueRank;
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Final Methods">
  /**
   * Ranking init
   */
  final protected function init()
  {
    $this->teams = new ArrayCollection();
    $this->name = "";
  }
//</editor-fold desc="Protected Final Methods">


}