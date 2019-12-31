<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/12/18
 * Time: 12:39 PM
 */

namespace Tfboe\FmLib\Entity;


use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;

/**
 * Interface TournamentHierarchyEntityRankingTimeInterface
 * @package Tfboe\FmLib\Entity
 */
interface TournamentHierarchyEntityRankingTimeInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return string|int
   */
  public function getEntityId();

  /**
   * @return TournamentHierarchyEntity
   */
  public function getHierarchyEntity(): TournamentHierarchyEntity;

  /**
   * @return int
   */
  public function getId(): int;

  /**
   * @return RankingSystemInterface
   */
  public function getRankingSystem(): RankingSystemInterface;

  /**
   * @return \DateTime
   */
  public function getRankingTime(): \DateTime;
//</editor-fold desc="Public Methods">
}