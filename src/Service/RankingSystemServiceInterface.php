<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/4/18
 * Time: 4:10 PM
 */

namespace Tfboe\FmLib\Service;

/**
 * Interface RankingSystemServiceInterface
 * @package Tfboe\FmLib\Service
 */
interface RankingSystemServiceInterface
{
//<editor-fold desc="Public Methods">
  /**
   * Recalculates all ranking systems which have an open sync from value.
   */
  public function recalculateRankingSystems(): void;
//</editor-fold desc="Public Methods">
}