<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:46 PM
 */

namespace Tfboe\FmLib\Entity;

use Doctrine\Common\Collections\Collection;
use Tfboe\FmLib\Entity\Helpers\BaseEntityInterface;
use Tfboe\FmLib\Entity\Helpers\NameEntityInterface;
use Tfboe\FmLib\Entity\Helpers\UUIDEntityInterface;


/**
 * Interface RankingInterface
 * @package Tfboe\FmLib\Entity
 */
interface RankingInterface extends BaseEntityInterface, UUIDEntityInterface, NameEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return PhaseInterface
   */
  public function getPhase(): PhaseInterface;

  /**
   * @return int
   */
  public function getRank(): int;

  /**
   * @return TeamInterface[]|Collection
   */
  public function getTeams(): Collection;

  /**
   * @return int
   */
  public function getUniqueRank(): int;

  /**
   * @param PhaseInterface $phase
   */
  public function setPhase(PhaseInterface $phase);

  /**
   * @param int $rank
   */
  public function setRank(int $rank);

  /**
   * @param int $uniqueRank
   */
  public function setUniqueRank(int $uniqueRank);
//</editor-fold desc="Public Methods">
}