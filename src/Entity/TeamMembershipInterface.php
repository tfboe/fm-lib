<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 2/27/18
 * Time: 6:33 PM
 */

namespace Tfboe\FmLib\Entity;


use DateTime;
use Tfboe\FmLib\Entity\Helpers\UUIDEntityInterface;

/**
 * Interface TeamMembershipInterface
 * @package Tfboe\FmLib\Entity
 */
interface TeamMembershipInterface extends UUIDEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return DateTime|null
   */
  public function getEnd(): ?DateTime;

  /**
   * @return PlayerInterface
   */
  public function getPlayer(): PlayerInterface;

  /**
   * @return DateTime|null
   */
  public function getStart(): ?DateTime;

  /**
   * @return TeamInterface
   */
  public function getTeam(): TeamInterface;

  /**
   * @param DateTime|null $end
   */
  public function setEnd(?DateTime $end): void;

  /**
   * @param PlayerInterface $player
   */
  public function setPlayer(PlayerInterface $player): void;

  /**
   * @param DateTime|null $start
   */
  public function setStart(?DateTime $start): void;

  /**
   * @param TeamInterface $team
   */
  public function setTeam(TeamInterface $team): void;
//</editor-fold desc="Public Methods">
}