<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 11:35 AM
 */

namespace Tfboe\FmLib\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\NameEntity;
use Tfboe\FmLib\Entity\Helpers\TimestampableEntity;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;
use Tfboe\FmLib\Helpers\Level;

/**
 * Class Tournament
 * @package Tfboe\FmLib\Entity
 * @ORM\Entity
 * @ORM\Table(name="tournaments",indexes={@ORM\Index(name="user_id_idx", columns={"user_identifier","creator_id"})})
 */
class Tournament extends TournamentHierarchyEntity
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
   * @ORM\ManyToOne(targetEntity="User")
   * @var User
   */
  private $creator;
  /**
   * @ORM\OneToMany(targetEntity="Competition", mappedBy="tournament",indexBy="name")
   * @var Collection|Competition[]
   */
  private $competitions;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * Tournament constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->tournamentListId = "";
    $this->competitions = new ArrayCollection();
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function getChildren(): Collection
  {
    return $this->getCompetitions();
  }

  /**
   * @return Competition[]|Collection
   */
  public function getCompetitions()
  {
    return $this->competitions;
  }

  /**
   * @return User
   */
  public function getCreator(): User
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
   * @param User $creator
   * @return $this|Tournament
   */
  public function setCreator(User $creator): Tournament
  {
    $this->creator = $creator;
    return $this;
  }

  /**
   * @param string $tournamentListId
   * @return $this|Tournament
   */
  public function setTournamentListId(string $tournamentListId): Tournament
  {
    $this->tournamentListId = $tournamentListId;
    return $this;
  }

  /**
   * @param string $userIdentifier
   * @return $this|Tournament
   */
  public function setUserIdentifier(string $userIdentifier): Tournament
  {
    $this->userIdentifier = $userIdentifier;
    return $this;
  }
//</editor-fold desc="Public Methods">
}