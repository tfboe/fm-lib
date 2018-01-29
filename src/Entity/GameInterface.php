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
use Tfboe\FmLib\Entity\CategoryTraits\GameMode;
use Tfboe\FmLib\Entity\CategoryTraits\OrganizingMode;
use Tfboe\FmLib\Entity\CategoryTraits\ScoreMode;
use Tfboe\FmLib\Entity\CategoryTraits\Table;
use Tfboe\FmLib\Entity\CategoryTraits\TeamMode;
use Tfboe\FmLib\Entity\Helpers\ResultEntity;
use Tfboe\FmLib\Entity\Helpers\TimeEntity;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;
use Tfboe\FmLib\Exceptions\ValueNotValid;

/**
 * Interface GameInterface
 * @package Tfboe\FmLib\Entity
 */
interface GameInterface extends TournamentHierarchyInterface
{
//<editor-fold desc="Constructor">
  /**
   * Match constructor.
   */
  public function __construct();
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return int|null
   */
  public function getGameMode(): ?int;

  /**
   * @return int
   */
  public function getGameNumber(): int;

  /**
   * @return MatchInterface
   */
  public function getMatch(): MatchInterface;

  /**
   * @return int|null
   */
  public function getOrganizingMode(): ?int;

  /**
   * @return Player[]|Collection
   */
  public function getPlayersA();

  /**
   * @return Player[]|Collection
   */
  public function getPlayersB();

  /**
   * @return int
   */
  public function getResult(): int;

  /**
   * @return int
   */
  public function getResultA(): int;

  /**
   * @return int
   */
  public function getResultB(): int;

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
   * @return bool
   */
  public function isPlayed(): bool;

  /**
   * Checks if the given method exists
   * @param string $method the method to search
   * @return bool true if it exists and false otherwise
   */
  public function methodExists(string $method): bool;

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
   * @param int $gameNumber
   */
  public function setGameNumber(int $gameNumber);

  /**
   * @param MatchInterface $match
   */
  public function setMatch(MatchInterface $match);

  /**
   * @param int|null $organizingMode
   * @return $this|OrganizingMode
   * @throws ValueNotValid
   */
  public function setOrganizingMode(?int $organizingMode);

  /**
   * @param bool $played
   * @return $this|ResultEntity
   */
  public function setPlayed(bool $played);

  /**
   * @param int $result
   * @return $this|ResultEntity
   * @throws ValueNotValid
   */
  public function setResult(int $result);

  /**
   * @param int $resultA
   * @return $this|ResultEntity
   */
  public function setResultA(int $resultA);

  /**
   * @param int $resultB
   * @return $this|ResultEntity
   */
  public function setResultB(int $resultB);

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