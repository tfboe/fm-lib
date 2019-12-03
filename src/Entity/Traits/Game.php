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
use Tfboe\FmLib\Entity\Helpers\ResultEntity;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;
use Tfboe\FmLib\Entity\MatchInterface;
use Tfboe\FmLib\Entity\PlayerInterface;
use Tfboe\FmLib\Helpers\Level;


/**
 * Trait Game
 * @package Tfboe\FmLib\Entity\Traits
 */
trait Game
{
//<editor-fold desc="Fields">
  use ResultEntity {
    init as resultInit;
  }
  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\MatchInterface", inversedBy="games")
   * @var MatchInterface
   */
  private $match;
  /**
   * @ORM\ManyToMany(targetEntity="\Tfboe\FmLib\Entity\PlayerInterface", indexBy="id")
   * @ORM\JoinTable(name="relation__game_playersA",
   *      joinColumns={@ORM\JoinColumn(name="game_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="id")})
   * @var Collection|PlayerInterface[]
   */
  private $playersA;
  /**
   * @ORM\ManyToMany(targetEntity="\Tfboe\FmLib\Entity\PlayerInterface", indexBy="id")
   * @ORM\JoinTable(name="relation__game_playersB",
   *      joinColumns={@ORM\JoinColumn(name="game_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="id")})
   * @var Collection|PlayerInterface[]
   */
  private $playersB;
  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $gameNumber;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function getChildren(): Collection
  {
    return new ArrayCollection();
  }

  /**
   * @return int
   */
  public function getGameNumber(): int
  {
    return $this->gameNumber;
  }

  /**
   * @return string
   */
  abstract public function getId(): string;

  /**
   * @inheritDoc
   */
  public function getLevel(): int
  {
    return Level::GAME;
  }

  /**
   * @inheritDoc
   */
  public function getLocalIdentifier()
  {
    return $this->getGameNumber();
  }

  /**
   * @return MatchInterface
   */
  public function getMatch(): MatchInterface
  {
    return $this->match;
  }

  /**
   * @inheritDoc
   */
  public function getParent(): ?TournamentHierarchyInterface
  {
    return $this->getMatch();
  }

  /**
   * @return PlayerInterface[]|Collection
   */
  public function getPlayersA()
  {
    return $this->playersA;
  }

  /**
   * @return PlayerInterface[]|Collection
   */
  public function getPlayersB()
  {
    return $this->playersB;
  }

  /**
   * @param int $gameNumber
   */
  public function setGameNumber(int $gameNumber)
  {
    $this->gameNumber = $gameNumber;
  }

  /**
   * @param MatchInterface $match
   */
  public function setMatch(MatchInterface $match)
  {
    if ($this->match !== null) {
      $this->match->getGames()->remove($this->getId());
    }
    $this->match = $match;
    $match->getGames()->set($this->getId(), $this);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Final Methods">
  /**
   * Match constructor.
   */
  final protected function init()
  {
    $this->playersA = new ArrayCollection();
    $this->playersB = new ArrayCollection();
    $this->resultInit();
  }
//</editor-fold desc="Protected Final Methods">
}