<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/28/18
 * Time: 5:41 PM
 */

namespace Tfboe\FmLib\Service;

/**
 * Interface AsyncExecuterServiceInterface
 * @package Tfboe\FmLib\Service
 */
interface AsyncExecuterServiceInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @param $command
   */
  public function runBashCommand($command);
//</editor-fold desc="Public Methods">
}