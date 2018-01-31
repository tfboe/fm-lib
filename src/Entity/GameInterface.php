<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/29/18
 * Time: 11:34 AM
 */

namespace Tfboe\FmLib\Entity;

use Doctrine\Common\Collections\Collection;
use Tfboe\FmLib\Entity\Helpers\ResultEntityInterface;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;


/**
 * Interface GameInterface
 * @package Tfboe\FmLib\Entity
 */
interface GameInterface extends TournamentHierarchyInterface, ResultEntityInterface
{
//<editor-fold desc="Public Methods">

  /**
   * @return int
   */
  public function getGameNumber(): int;

  /**
   * @return MatchInterface
   */
  public function getMatch(): MatchInterface;

  /**
   * @return PlayerInterface[]|Collection
   */
  public function getPlayersA();

  /**
   * @return PlayerInterface[]|Collection
   */
  public function getPlayersB();

  /**
   * Checks if the given method exists
   * @param string $method the method to search
   * @return bool true if it exists and false otherwise
   */
  public function methodExists(string $method): bool;

  /**
   * @param int $gameNumber
   */
  public function setGameNumber(int $gameNumber);

  /**
   * @param MatchInterface $match
   */
  public function setMatch(MatchInterface $match);
//</editor-fold desc="Public Methods">
}