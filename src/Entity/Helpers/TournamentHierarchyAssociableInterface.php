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
interface TournamentHierarchyAssociableInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return TournamentHierarchyInterface
   */
  public function getHierarchyEntity(): TournamentHierarchyInterface;
//</editor-fold desc="Public Methods">
}