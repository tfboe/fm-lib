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
use Tfboe\FmLib\Entity\Helpers\NameEntityInterface;
use Tfboe\FmLib\Entity\Helpers\TimestampableEntityInterface;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;


/**
 * Interface TournamentInterface
 * @package Tfboe\FmLib\Entity
 */
interface TournamentInterface extends TournamentHierarchyInterface, TimestampableEntityInterface, NameEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return CompetitionInterface[]|Collection
   */
  public function getCompetitions();

  /**
   * @return UserInterface
   */
  public function getCreator(): UserInterface;

  /**
   * @return string
   */
  public function getTournamentListId(): string;

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
   * @param UserInterface $creator
   * @return $this|TournamentInterface
   */
  public function setCreator(UserInterface $creator);

  /**
   * @param string $tournamentListId
   * @return $this|TournamentInterface
   */
  public function setTournamentListId(string $tournamentListId);

  /**
   * @param string $userIdentifier
   * @return $this|TournamentInterface
   */
  public function setUserIdentifier(string $userIdentifier);
//</editor-fold desc="Public Methods">
}