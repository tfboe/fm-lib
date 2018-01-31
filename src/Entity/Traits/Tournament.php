<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 11:35 AM
 */

namespace Tfboe\FmLib\Entity\Traits;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\CompetitionInterface;
use Tfboe\FmLib\Entity\Helpers\NameEntity;
use Tfboe\FmLib\Entity\Helpers\TimestampableEntity;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;
use Tfboe\FmLib\Entity\TournamentInterface;
use Tfboe\FmLib\Entity\UserInterface;
use Tfboe\FmLib\Helpers\Level;

/**
 * Trait Tournament
 * @package Tfboe\FmLib\Entity\Traits
 */
trait Tournament
{
  use TimestampableEntity;
  use NameEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $userIdentifier;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $tournamentListId;
  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\UserInterface")
   * @var UserInterface
   */
  private $creator;
  /**
   * @ORM\OneToMany(targetEntity="\Tfboe\FmLib\Entity\CompetitionInterface", mappedBy="tournament",indexBy="name")
   * @var Collection|CompetitionInterface[]
   */
  private $competitions;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function getChildren(): Collection
  {
    return $this->getCompetitions();
  }

  /**
   * @return CompetitionInterface[]|Collection
   */
  public function getCompetitions()
  {
    return $this->competitions;
  }

  /**
   * @return UserInterface
   */
  public function getCreator(): UserInterface
  {
    return $this->creator;
  }

  /**
   * @inheritDoc
   */
  public function getLevel(): int
  {
    return Level::TOURNAMENT;
  }

  /**
   * @inheritDoc
   */
  public function getLocalIdentifier()
  {
    return $this->getId();
  }

  /**
   * @inheritDoc
   */
  public function getParent(): ?TournamentHierarchyInterface
  {
    return null;
  }

  /**
   * @return string
   */
  public function getTournamentListId(): string
  {
    return $this->tournamentListId;
  }

  /**
   * @return string
   */
  public function getUserIdentifier(): string
  {
    return $this->userIdentifier;
  }

  /**
   * Tournament constructor.
   */
  protected final function init()
  {
    $this->tournamentListId = "";
    $this->competitions = new ArrayCollection();
  }

  /**
   * @param UserInterface $creator
   * @return TournamentInterface|Tournament
   */
  public function setCreator(UserInterface $creator)
  {
    $this->creator = $creator;
    return $this;
  }

  /**
   * @param string $tournamentListId
   * @return TournamentInterface|Tournament
   */
  public function setTournamentListId(string $tournamentListId)
  {
    $this->tournamentListId = $tournamentListId;
    return $this;
  }

  /**
   * @param string $userIdentifier
   * @return TournamentInterface|Tournament
   */
  public function setUserIdentifier(string $userIdentifier)
  {
    $this->userIdentifier = $userIdentifier;
    return $this;
  }
//</editor-fold desc="Public Methods">
}