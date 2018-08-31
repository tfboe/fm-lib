<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 8/31/18
 * Time: 9:15 AM
 */

namespace Tfboe\FmLib\Service;


use Tfboe\FmLib\Entity\AGBInterface;

interface AGBServiceInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return AGBInterface
   */
  public function getLatestAGB(): AGBInterface;
//</editor-fold desc="Public Methods">
}