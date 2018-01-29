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
use Tfboe\FmLib\Entity\CategoryTraits\GameMode;
use Tfboe\FmLib\Entity\CategoryTraits\OrganizingMode;
use Tfboe\FmLib\Entity\CategoryTraits\ScoreMode;
use Tfboe\FmLib\Entity\CategoryTraits\Table;
use Tfboe\FmLib\Entity\CategoryTraits\TeamMode;
use Tfboe\FmLib\Entity\Helpers\NameEntity;
use Tfboe\FmLib\Entity\Helpers\TimeEntity;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;
use Tfboe\FmLib\Exceptions\ValueNotValid;

/**
 * Interface PhaseInterface
 * @package Tfboe\FmLib\Entity
 */
interface PhaseInterface extends TournamentHierarchyInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return CompetitionInterface
   */
  public function getCompetition(): CompetitionInterface;

  /**
   * @return int|null
   */
  public function getGameMode(): ?int;

  /**
   * @return MatchInterface[]|Collection
   */
  public function getMatches();

  /**
   * @return string
   */
  public function getName(): string;

  /**
   * @return int|null
   */
  public function getOrganizingMode(): ?int;

  /**
   * @return int
   */
  public function getPhaseNumber(): int;

  /**
   * @return QualificationSystem[]|Collection
   */
  public function getPostQualifications(): Collection;

  /**
   * @return QualificationSystem[]|Collection
   */
  public function getPreQualifications(): Collection;

  /**
   * @return Ranking[]|Collection
   */
  public function getRankings();

  /**
   * @return int|null
   */
  public function getScoreMode(): ?int;

  /**
   * @return int|null
   */
  public function getTable(): ?int;

  /**
   * @return int|null
   */
  public function getTeamMode(): ?int;

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
   * @param \DateTime|null $endTime
   * @return $this|TimeEntity
   */
  public function setEndTime(?\DateTime $endTime);

  /**
   * @param int|null $gameMode
   * @return $this|GameMode
   * @throws ValueNotValid
   */
  public function setGameMode(?int $gameMode);

  /**
   * @param string $name
   * @return $this|NameEntity
   */
  public function setName(string $name);

  /**
   * @param int|null $organizingMode
   * @return $this|OrganizingMode
   * @throws ValueNotValid
   */
  public function setOrganizingMode(?int $organizingMode);

  /**
   * @param int $phaseNumber
   */
  public function setPhaseNumber(int $phaseNumber);

  /**
   * @param int|null $scoreMode
   * @return $this|ScoreMode
   * @throws ValueNotValid
   */
  public function setScoreMode(?int $scoreMode);

  /**
   * @param \DateTime|null $startTime
   * @return $this|TimeEntity
   */
  public function setStartTime(?\DateTime $startTime);

  /**
   * @param int|null $table
   * @return $this|Table
   * @throws ValueNotValid
   */
  public function setTable(?int $table);

  /**
   * @param int|null $teamMode
   * @return $this|TeamMode
   * @throws ValueNotValid
   */
  public function setTeamMode(?int $teamMode);
//</editor-fold desc="Public Methods">
}