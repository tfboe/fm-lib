<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 2/27/18
 * Time: 6:33 PM
 */

namespace Tfboe\FmLib\Entity\Traits;


use DateTime;
use Tfboe\FmLib\Entity\Helpers\UUIDEntity;
use Tfboe\FmLib\Entity\PlayerInterface;
use Tfboe\FmLib\Entity\TeamInterface;

/**
 * Trait TeamMembership
 * @package Tfboe\FmLib\Entity\Traits
 */
trait TeamMembership
{
  use UUIDEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\TeamInterface", inversedBy="memberships")
   * @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=false)
   * @var TeamInterface
   */
  private $team;
  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\PlayerInterface")
   * @ORM\JoinColumn(name="player_id", referencedColumnName="id", nullable=false)
   * @var PlayerInterface
   */
  private $player;

  /**
   * @ORM\Column(type="datetime", nullable=true)
   * @var DateTime|null
   */
  private $start;

  /**
   * @ORM\Column(type="datetime", nullable=true)
   * @var DateTime|null
   */
  private $end;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return DateTime|null
   */
  public function getEnd(): ?DateTime
  {
    return $this->end;
  }

  /**
   * @return PlayerInterface
   */
  public function getPlayer(): PlayerInterface
  {
    return $this->player;
  }

  /**
   * @return DateTime|null
   */
  public function getStart(): ?DateTime
  {
    return $this->start;
  }

  /**
   * @return TeamInterface
   */
  public function getTeam(): TeamInterface
  {
    return $this->team;
  }

  /**
   * @param DateTime|null $end
   */
  public function setEnd(?DateTime $end): void
  {
    if (!\Tfboe\FmLib\Helpers\DateTime::eq($this->end, $end)) {
      $this->end = $end;
    }
  }

  /**
   * @param PlayerInterface $player
   */
  public function setPlayer(PlayerInterface $player): void
  {
    $this->player = $player;
  }

  /**
   * @param DateTime|null $start
   */
  public function setStart(?DateTime $start): void
  {
    if (!\Tfboe\FmLib\Helpers\DateTime::eq($this->start, $start)) {
      $this->start = $start;
    }
  }

  /**
   * @param TeamInterface $team
   */
  public function setTeam(TeamInterface $team): void
  {
    if ($this->team !== null) {
      $this->team->getMemberships()->remove($this->getId());
    }
    $this->team = $team;
    $this->team->getMemberships()->set($this->getId(), $this);
  }
//</editor-fold desc="Public Methods">
}