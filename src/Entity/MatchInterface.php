<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/29/18
 * Time: 11:35 AM
 */

namespace Tfboe\FmLib\Entity;

use Doctrine\Common\Collections\Collection;
use Tfboe\FmLib\Entity\Helpers\ResultEntityInterface;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;


/**
 * Interface MatchInterface
 * @package Tfboe\FmLib\Entity
 */
interface MatchInterface extends TournamentHierarchyInterface, ResultEntityInterface
{
//<editor-fold desc="Public Methods">

  /**
   * @return GameInterface[]|Collection
   */
  public function getGames();

  /**
   * @return int
   */
  public function getMatchNumber(): int;

  /**
   * @return PhaseInterface
   */
  public function getPhase(): PhaseInterface;

  /**
   * @return RankingInterface|Collection
   */
  public function getRankingsA();

  /**
   * @return RankingInterface|Collection
   */
  public function getRankingsB();

  /**
   * Checks if the given method exists
   * @param string $method the method to search
   * @return bool true if it exists and false otherwise
   */
  public function methodExists(string $method): bool;

  /**
   * @param int $matchNumber
   * @return $this|MatchInterface
   */
  public function setMatchNumber(int $matchNumber);

  /**
   * @param PhaseInterface $phase
   */
  public function setPhase(PhaseInterface $phase);
//</editor-fold desc="Public Methods">
}