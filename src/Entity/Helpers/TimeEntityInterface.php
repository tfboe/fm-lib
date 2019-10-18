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
interface TimeEntityInterface
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