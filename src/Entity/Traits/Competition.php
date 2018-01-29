<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/15/17
 * Time: 10:57 AM
 */

namespace Tfboe\FmLib\Entity\Traits;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\CompetitionInterface;
use Tfboe\FmLib\Entity\Helpers\NameEntity;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;
use Tfboe\FmLib\Entity\PhaseInterface;
use Tfboe\FmLib\Entity\Team;
use Tfboe\FmLib\Entity\TournamentInterface;
use Tfboe\FmLib\Helpers\Level;


/**
 * Trait Competition
 * @package Tfboe\FmLib\Entity\Traits
 */
trait Competition
{
  use NameEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\TournamentInterface", inversedBy="competitions")
   * @var TournamentInterface
   */
  private $tournament;

  /**
   * @ORM\OneToMany(targetEntity="\Tfboe\FmLib\Entity\Team", mappedBy="competition", indexBy="startNumber")
   * @var Collection|Team[]
   */
  private $teams;

  /**
   * @ORM\OneToMany(targetEntity="\Tfboe\FmLib\Entity\PhaseInterface", mappedBy="competition", indexBy="phaseNumber")
   * @var Collection|PhaseInterface[]
   */
  private $phases;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function getChildren(): Collection
  {
    return $this->getPhases();
  }

  /**
   * @inheritDoc
   */
  public function getLevel(): int
  {
    return Level::COMPETITION;
  }

  /**
   * @inheritDoc
   */
  public function getLocalIdentifier()
  {
    return $this->getName();
  }

  /**
   * @inheritDoc
   */
  public function getParent(): ?TournamentHierarchyInterface
  {
    return $this->getTournament();
  }

  /**
   * @return PhaseInterface[]|Collection
   */
  public function getPhases()
  {
    return $this->phases;
  }

  /**
   * @return Team[]|Collection
   */
  public function getTeams()
  {
    return $this->teams;
  }

  /**
   * @return TournamentInterface
   */
  public function getTournament(): TournamentInterface
  {
    return $this->tournament;
  }

  /**
   * Competition constructor.
   */
  public function init()
  {
    $this->teams = new ArrayCollection();
    $this->phases = new ArrayCollection();
  }

  /**
   * @param TournamentInterface $tournament
   * @return $this|CompetitionInterface|Competition
   */
  public function setTournament(TournamentInterface $tournament)
  {
    if ($this->tournament !== null) {
      $this->tournament->getCompetitions()->remove($this->getName());
    }
    $this->tournament = $tournament;
    $tournament->getCompetitions()->set($this->getName(), $this);
    return $this;
  }
//</editor-fold desc="Public Methods">
}