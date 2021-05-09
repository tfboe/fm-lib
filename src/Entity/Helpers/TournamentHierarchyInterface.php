<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/4/18
 * Time: 6:33 PM
 */

namespace Tfboe\FmLib\Entity\Helpers;


use Doctrine\Common\Collections\Collection;
use Tfboe\FmLib\Entity\CategoryTraits\GameModeInterface;
use Tfboe\FmLib\Entity\CategoryTraits\OrganizingModeInterface;
use Tfboe\FmLib\Entity\CategoryTraits\ScoreModeInterface;
use Tfboe\FmLib\Entity\CategoryTraits\TableInterface;
use Tfboe\FmLib\Entity\CategoryTraits\TeamModeInterface;
use Tfboe\FmLib\Entity\RankingSystemInterface;

/**
 * Interface TournamentHierarchyInterface
 * @package Tfboe\FmLib\Entity\Helpers
 */
interface TournamentHierarchyInterface extends BaseEntityInterface, GameModeInterface, TeamModeInterface,
  OrganizingModeInterface, ScoreModeInterface, TableInterface, TimeEntityInterface, UUIDEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return Collection|TournamentHierarchyInterface[]
   */
  public function getChildren(): Collection;

  /**
   * Gets the level of the entity (see Level Enum)
   * @return int
   */
  public function getLevel(): int;

  /**
   * @return int|string the local identifier also used as unique identifier in the children array of the parent
   */
  public function getLocalIdentifier();

  /**
   * @return TournamentHierarchyInterface
   */
  public function getParent(): ?TournamentHierarchyInterface;

  /**
   * @return RankingSystemInterface[]|Collection
   */
  public function getRankingSystems();

  /**
   * @return RankingSystemInterface[]
   */
  public function getInfluencingRankingSystems(): array;

  /**
   * Tries this and all ancestors to get a value from a getter
   */
  public function getInherited($method);

  /**
   * @param RankingSystemInterface $rankingSystem
   * @returns true if it got really added and false if it already was added
   */
  public function addInfluencingRankingSystem(RankingSystemInterface $rankingSystem): bool;
//</editor-fold desc="Public Methods">
}