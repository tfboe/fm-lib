<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:53 PM
 */

namespace Tfboe\FmLib\Entity;

use Doctrine\Common\Collections\Collection;
use Tfboe\FmLib\Entity\Helpers\BaseEntityInterface;
use Tfboe\FmLib\Entity\Helpers\NameEntityInterface;
use Tfboe\FmLib\Entity\Helpers\UUIDEntityInterface;


/**
 * Interface TeamInterface
 * @package Tfboe\FmLib\Entity
 */
interface TeamInterface extends BaseEntityInterface, UUIDEntityInterface, NameEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return CompetitionInterface
   */
  public function getCompetition(): CompetitionInterface;

  /**
   * @return PlayerInterface[]|Collection
   */
  public function getPlayers();

  /**
   * @return int
   */
  public function getRank(): int;

  /**
   * @return int
   */
  public function getStartNumber(): int;

  /**
   * @param CompetitionInterface $competition
   */
  public function setCompetition(CompetitionInterface $competition);

  /**
   * @param int $rank
   */
  public function setRank(int $rank);

  /**
   * @param int $startNumber
   */
  public function setStartNumber(int $startNumber);
//</editor-fold desc="Public Methods">
}