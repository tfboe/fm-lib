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
use Tfboe\FmLib\Entity\CompetitionInterface;
use Tfboe\FmLib\Entity\Helpers\NameEntity;
use Tfboe\FmLib\Entity\Helpers\UUIDEntity;
use Tfboe\FmLib\Entity\TeamMembershipInterface;


/**
 * Trait Team
 * @package Tfboe\FmLib\Entity\Traits
 */
trait Team
{
  use UUIDEntity;
  use NameEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\OneToMany(targetEntity="\Tfboe\FmLib\Entity\TeamMembershipInterface", mappedBy="team", indexBy="id")
   * @var Collection|TeamMembershipInterface[]
   */
  private $memberships;
  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\CompetitionInterface", inversedBy="teams")
   * @var CompetitionInterface
   */
  private $competition;
  /**
   * @ORM\Column(name="`rank`", type="integer")
   * @var int
   */
  private $rank;
  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $startNumber;
//</editor-fold desc="Fields">
//<editor-fold desc="Constructor">
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return CompetitionInterface
   */
  public function getCompetition(): CompetitionInterface
  {
    return $this->competition;
  }

  /**
   * @return Collection|TeamMembershipInterface[]
   */
  public function getMemberships(): Collection
  {
    return $this->memberships;
  }

  /**
   * @return int
   */
  public function getRank(): int
  {
    return $this->rank;
  }

  /**
   * @return int
   */
  public function getStartNumber(): int
  {
    return $this->startNumber;
  }

  /**
   * @param CompetitionInterface $competition
   */
  public function setCompetition(CompetitionInterface $competition)
  {
    if ($this->competition !== null) {
      $this->competition->getTeams()->remove($this->getStartNumber());
    }
    $this->competition = $competition;
    $this->competition->getTeams()->set($this->getStartNumber(), $this);
  }

  /**
   * @param int $rank
   */
  public function setRank(int $rank)
  {
    $this->rank = $rank;
  }

  /**
   * @param int $startNumber
   */
  public function setStartNumber(int $startNumber)
  {
    $this->startNumber = $startNumber;
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Final Methods">
  /**
   * Team init
   */
  protected final function init()
  {
    $this->memberships = new ArrayCollection();
    $this->name = "";
  }
//</editor-fold desc="Protected Final Methods">
}
