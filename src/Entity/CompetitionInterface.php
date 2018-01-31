<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/29/18
 * Time: 11:32 AM
 */

namespace Tfboe\FmLib\Entity;

use Doctrine\Common\Collections\Collection;
use Tfboe\FmLib\Entity\Helpers\NameEntityInterface;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;


/**
 * Interface CompetitionInterface
 * @package Tfboe\FmLib\Entity
 */
interface CompetitionInterface extends TournamentHierarchyInterface, NameEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return PhaseInterface[]|Collection
   */
  public function getPhases();

  /**
   * @return TeamInterface[]|Collection
   */
  public function getTeams();

  /**
   * @return TournamentInterface
   */
  public function getTournament(): TournamentInterface;

  /**
   * Checks if the given method exists
   * @param string $method the method to search
   * @return bool true if it exists and false otherwise
   */
  public function methodExists(string $method): bool;

  /**
   * @param TournamentInterface $tournament
   */
  public function setTournament(TournamentInterface $tournament);
//</editor-fold desc="Public Methods">
}