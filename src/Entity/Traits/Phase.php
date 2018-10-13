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
use Tfboe\FmLib\Entity\MatchInterface;
use Tfboe\FmLib\Entity\QualificationSystemInterface;
use Tfboe\FmLib\Entity\RankingInterface;
use Tfboe\FmLib\Helpers\Level;

/**
 * Trait Phase
 * @package Tfboe\FmLib\Entity\Traits
 */
trait Phase
{
  use NameEntity;

//<editor-fold desc="Fields">

  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\CompetitionInterface", inversedBy="phases")
   * @var CompetitionInterface
   */
  private $competition;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $phaseNumber;

  /**
   * @ORM\OneToMany(targetEntity="\Tfboe\FmLib\Entity\QualificationSystemInterface", mappedBy="nextPhase")
   * @var Collection|QualificationSystemInterface[]
   */
  private $preQualifications;

  /**
   * @ORM\OneToMany(targetEntity="\Tfboe\FmLib\Entity\QualificationSystemInterface", mappedBy="previousPhase")
   * @var Collection|QualificationSystemInterface[]
   */
  private $postQualifications;

  /**
   * @ORM\OneToMany(targetEntity="\Tfboe\FmLib\Entity\RankingInterface", mappedBy="phase", indexBy="uniqueRank")
   * @var Collection|RankingInterface[]
   */
  private $rankings;

  /**
   * @ORM\OneToMany(targetEntity="\Tfboe\FmLib\Entity\MatchInterface", mappedBy="phase", indexBy="matchNumber")
   * @var Collection|MatchInterface[]
   */
  private $matches;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function getChildren(): Collection
  {
    return $this->getMatches();
  }

  /**
   * @return CompetitionInterface
   */
  public function getCompetition(): CompetitionInterface
  {
    return $this->competition;
  }

  /**
   * @inheritDoc
   */
  public function getLevel(): int
  {
    return Level::PHASE;
  }

  /**
   * @inheritDoc
   */
  public function getLocalIdentifier()
  {
    return $this->getPhaseNumber();
  }

  /**
   * @return MatchInterface[]|Collection
   */
  public function getMatches()
  {
    return $this->matches;
  }

  /**
   * @inheritDoc
   */
  public function getParent(): ?TournamentHierarchyInterface
  {
    return $this->getCompetition();
  }

  /**
   * @return int
   */
  public function getPhaseNumber(): int
  {
    return $this->phaseNumber;
  }

  /**
   * @return QualificationSystemInterface[]|Collection
   */
  public function getPostQualifications(): Collection
  {
    return $this->postQualifications;
  }

  /**
   * @return QualificationSystemInterface[]|Collection
   */
  public function getPreQualifications(): Collection
  {
    return $this->preQualifications;
  }

  /**
   * @return RankingInterface[]|Collection
   */
  public function getRankings()
  {
    return $this->rankings;
  }

  /**
   * @param CompetitionInterface $competition
   */
  public function setCompetition(CompetitionInterface $competition)
  {
    if ($this->competition !== null) {
      $this->competition->getPhases()->remove($this->getPhaseNumber());
    }
    $this->competition = $competition;
    $competition->getPhases()->set($this->getPhaseNumber(), $this);
  }

  /**
   * @param int $phaseNumber
   */
  public function setPhaseNumber(int $phaseNumber)
  {
    $this->phaseNumber = $phaseNumber;
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Final Methods">
  /**
   * Competition constructor.
   */
  protected final function init()
  {
    $this->preQualifications = new ArrayCollection();
    $this->postQualifications = new ArrayCollection();
    $this->name = '';
    $this->rankings = new ArrayCollection();
    $this->matches = new ArrayCollection();
  }
//</editor-fold desc="Protected Final Methods">
}