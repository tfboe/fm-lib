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
use Tfboe\FmLib\Entity\CategoryTraits\GameMode;
use Tfboe\FmLib\Entity\CategoryTraits\Table;
use Tfboe\FmLib\Entity\Helpers\NameEntity;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;
use Tfboe\FmLib\Exceptions\ValueNotValid;

/**
 * Interface CompetitionInterface
 * @package Tfboe\FmLib\Entity
 */
interface CompetitionInterface extends TournamentHierarchyInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return int|null
   */
  public function getGameMode(): ?int;

  /**
   * @return string
   */
  public function getName(): string;

  /**
   * @return int|null
   */
  public function getOrganizingMode(): ?int;

  /**
   * @return PhaseInterface[]|Collection
   */
  public function getPhases();

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
   * @return Team[]|Collection
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
   * @param \DateTime|null $endTime
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
   * @throws ValueNotValid
   */
  public function setOrganizingMode(?int $organizingMode);

  /**
   * @param int|null $scoreMode
   * @throws ValueNotValid
   */
  public function setScoreMode(?int $scoreMode);

  /**
   * @param \DateTime|null $startTime
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
   * @throws ValueNotValid
   */
  public function setTeamMode(?int $teamMode);

  /**
   * @param TournamentInterface $tournament
   */
  public function setTournament(TournamentInterface $tournament);
//</editor-fold desc="Public Methods">
}