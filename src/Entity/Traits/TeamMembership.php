<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 2/27/18
 * Time: 6:33 PM
 */

namespace Tfboe\FmLib\Entity\Traits;


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
   * @var TeamInterface
   */
  private $team;
  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\PlayerInterface")
   * @var PlayerInterface
   */
  private $player;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return PlayerInterface
   */
  public function getPlayer(): PlayerInterface
  {
    return $this->player;
  }

  /**
   * @return TeamInterface
   */
  public function getTeam(): TeamInterface
  {
    return $this->team;
  }

  /**
   * @param PlayerInterface $player
   */
  public function setPlayer(PlayerInterface $player): void
  {
    $this->player = $player;
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