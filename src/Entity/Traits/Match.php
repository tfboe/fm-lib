<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 12/4/17
 * Time: 10:49 PM
 */

namespace Tfboe\FmLib\Entity\Traits;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\GameInterface;
use Tfboe\FmLib\Entity\Helpers\ResultEntity;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;
use Tfboe\FmLib\Entity\PhaseInterface;
use Tfboe\FmLib\Entity\RankingInterface;
use Tfboe\FmLib\Helpers\Level;

/**
 * Trait Match
 * @package Tfboe\FmLib\Entity\Traits
 */
trait Match
{
  use ResultEntity;

//<editor-fold desc="Fields">

  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\PhaseInterface", inversedBy="matches")
   * @var PhaseInterface
   */
  private $phase;

  /**
   * @ORM\ManyToMany(targetEntity="\Tfboe\FmLib\Entity\RankingInterface", indexBy="uniqueRank")
   * @ORM\JoinTable(name="relation__match_rankingA")
   * @var Collection|RankingInterface[]
   */
  private $rankingsA;

  /**
   * @ORM\ManyToMany(targetEntity="\Tfboe\FmLib\Entity\RankingInterface", indexBy="uniqueRank")
   * @ORM\JoinTable(name="relation__match_rankingB")
   * @var Collection|RankingInterface[]
   */
  private $rankingsB;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $matchNumber;

  /**
   * @ORM\OneToMany(targetEntity="\Tfboe\FmLib\Entity\GameInterface", mappedBy="match", indexBy="gameNumber")
   * @var Collection|GameInterface[]
   */
  private $games;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function getChildren(): Collection
  {
    return $this->getGames();
  }

  /**
   * @return GameInterface[]|Collection
   */
  public function getGames()
  {
    return $this->games;
  }

  /**
   * @inheritDoc
   */
  public function getLevel(): int
  {
    return Level::MATCH;
  }

  /**
   * @inheritDoc
   */
  public function getLocalIdentifier()
  {
    return $this->getMatchNumber();
  }

  /**
   * @return int
   */
  public function getMatchNumber(): int
  {
    return $this->matchNumber;
  }

  /**
   * @inheritDoc
   */
  public function getParent(): ?TournamentHierarchyInterface
  {
    return $this->phase;
  }

  /**
   * @return PhaseInterface
   */
  public function getPhase(): PhaseInterface
  {
    return $this->phase;
  }

  /**
   * @return RankingInterface[]|Collection
   */
  public function getRankingsA()
  {
    return $this->rankingsA;
  }

  /**
   * @return RankingInterface[]|Collection
   */
  public function getRankingsB()
  {
    return $this->rankingsB;
  }

  /**
   * @param int $matchNumber
   */
  public function setMatchNumber(int $matchNumber)
  {
    $this->matchNumber = $matchNumber;
  }

  /**
   * @param PhaseInterface $phase
   */
  public function setPhase(PhaseInterface $phase)
  {
    if ($this->phase !== null) {
      $this->phase->getMatches()->remove($this->getMatchNumber());
    }
    $this->phase = $phase;
    $phase->getMatches()->set($this->getMatchNumber(), $this);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Final Methods">
  /**
   * Match constructor.
   */
  protected final function init()
  {
    $this->rankingsA = new ArrayCollection();
    $this->rankingsB = new ArrayCollection();
    $this->games = new ArrayCollection();
  }
//</editor-fold desc="Protected Final Methods">
}