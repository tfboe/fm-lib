<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/15/17
 * Time: 10:57 AM
 */

namespace Tfboe\FmLib\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\NameEntity;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;
use Tfboe\FmLib\Helpers\Level;

/**
 * Class Competition
 * @package Tfboe\FmLib\Entity
 * @ORM\Entity
 * @ORM\Table(name="competitions",indexes={@ORM\Index(name="unique_name_idx", columns={"tournament_id","name"})})
 */
class Competition extends TournamentHierarchyEntity
{
  use NameEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="competitions")
   * @var Tournament
   */
  private $tournament;

  /**
   * @ORM\OneToMany(targetEntity="Team", mappedBy="competition", indexBy="startNumber")
   * @var Collection|Team[]
   */
  private $teams;

  /**
   * @ORM\OneToMany(targetEntity="Phase", mappedBy="competition", indexBy="phaseNumber")
   * @var Collection|Phase[]
   */
  private $phases;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * Competition constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->teams = new ArrayCollection();
    $this->phases = new ArrayCollection();
  }
//</editor-fold desc="Constructor">

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
   * @return Phase[]|Collection
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
   * @return Tournament
   */
  public function getTournament(): Tournament
  {
    return $this->tournament;
  }

  /**
   * @param Tournament $tournament
   * @return $this|Competition
   */
  public function setTournament(Tournament $tournament): Competition
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