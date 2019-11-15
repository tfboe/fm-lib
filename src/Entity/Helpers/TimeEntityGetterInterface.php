<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:33 PM
 */

namespace Tfboe\FmLib\Entity\Helpers;


use DateTime;

/**
 * Interface TimeEntityGetterInterface
 * @package Tfboe\FmLib\Entity\Helpers
 */
interface TimeEntityGetterInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return DateTime|null
   */
  public function getEndTime(): ?DateTime;

  /**
   * @return DateTime|null
   */
  public function getStartTime(): ?DateTime;
//</editor-fold desc="Public Methods">
}