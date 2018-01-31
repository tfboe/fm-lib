<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/29/18
 * Time: 11:36 AM
 */

namespace Tfboe\FmLib\Entity;

use Doctrine\Common\Collections\Collection;
use Tfboe\FmLib\Entity\Helpers\NameEntityInterface;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;


/**
 * Interface PhaseInterface
 * @package Tfboe\FmLib\Entity
 */
interface PhaseInterface extends TournamentHierarchyInterface, NameEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return CompetitionInterface
   */
  public function getCompetition(): CompetitionInterface;

  /**
   * @return MatchInterface[]|Collection
   */
  public function getMatches();

  /**
   * @return int
   */
  public function getPhaseNumber(): int;

  /**
   * @return QualificationSystemInterface[]|Collection
   */
  public function getPostQualifications(): Collection;

  /**
   * @return QualificationSystemInterface[]|Collection
   */
  public function getPreQualifications(): Collection;

  /**
   * @return RankingInterface[]|Collection
   */
  public function getRankings();

  /**
   * Checks if the given method exists
   * @param string $method the method to search
   * @return bool true if it exists and false otherwise
   */
  public function methodExists(string $method): bool;

  /**
   * @param CompetitionInterface $competition
   */
  public function setCompetition(CompetitionInterface $competition);

  /**
   * @param int $phaseNumber
   */
  public function setPhaseNumber(int $phaseNumber);
//</editor-fold desc="Public Methods">
}