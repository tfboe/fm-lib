<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/28/18
 * Time: 2:41 PM
 */

namespace Tfboe\FmLib\Entity;

use Tfboe\FmLib\Entity\Helpers\BaseEntityInterface;
use Tfboe\FmLib\Entity\Helpers\TimeEntityInterface;

/**
 * Interface LastRecalculationInterface
 * @package Tfboe\FmLib\Entity
 */
interface RecalculationInterface extends BaseEntityInterface, TimeEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return int
   */
  public function getId(): int;

  /**
   * @return RankingSystemInterface
   */
  public function getRankingSystem(): RankingSystemInterface;

  /**
   * @return \DateTime|null
   */
  public function getRecalculateFrom(): ?\DateTime;

  /**
   * @return int
   */
  public function getVersion(): int;

  /**
   * @param RankingSystemInterface $rankingSystem
   * @return mixed
   */
  public function setRankingSystem(RankingSystemInterface $rankingSystem): void;

  /**
   * @param \DateTime|null $recalculateFrom
   */
  public function setRecalculateFrom(?\DateTime $recalculateFrom): void;

  /**
   * @param int $version
   */
  public function setVersion(int $version);
//</editor-fold desc="Public Methods">
}