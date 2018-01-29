<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/29/18
 * Time: 11:38 AM
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
use Tfboe\FmLib\Entity\Helpers\TimestampableEntity;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;
use Tfboe\FmLib\Exceptions\ValueNotValid;

/**
 * Interface TournamentInterface
 * @package Tfboe\FmLib\Entity
 */
interface TournamentInterface extends TournamentHierarchyInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return CompetitionInterface[]|Collection
   */
  public function getCompetitions();

  /**
   * @return \DateTime
   */
  public function getCreatedAt(): \DateTime;

  /**
   * @return User
   */
  public function getCreator(): User;

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
   * @return string
   */
  public function getTournamentListId(): string;

  /**
   * @return \DateTime
   */
  public function getUpdatedAt(): \DateTime;

  /**
   * @return string
   */
  public function getUserIdentifier(): string;

  /**
   * Checks if the given method exists
   * @param string $method the method to search
   * @return bool true if it exists and false otherwise
   */
  public function methodExists(string $method): bool;

  /**
   * @param \DateTime $createdAt
   * @return $this|TimestampableEntity
   */
  public function setCreatedAt(\DateTime $createdAt);

  /**
   * @param User $creator
   * @return $this|TournamentInterface
   */
  public function setCreator(User $creator);

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

  /**
   * @param string $tournamentListId
   * @return $this|TournamentInterface
   */
  public function setTournamentListId(string $tournamentListId);

  /**
   * @param \DateTime $updatedAt
   * @return $this|TimestampableEntity
   */
  public function setUpdatedAt(\DateTime $updatedAt);

  /**
   * @param string $userIdentifier
   * @return $this|TournamentInterface
   */
  public function setUserIdentifier(string $userIdentifier);
//</editor-fold desc="Public Methods">
}