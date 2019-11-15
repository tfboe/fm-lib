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
 * Interface TimeEntityInterface
 * @package Tfboe\FmLib\Entity\Helpers
 */
interface TimeEntityInterface extends TimeEntityGetterInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @param DateTime|null $endTime
   * @return $this|TimeEntity
   */
  public function setEndTime(?DateTime $endTime);

  /**
   * @param DateTime|null $startTime
   * @return $this|TimeEntity
   */
  public function setStartTime(?DateTime $startTime);
//</editor-fold desc="Public Methods">
}