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
use Tfboe\FmLib\Exceptions\ValueNotValid;

/**
 * Interface TimeEntityGetterInterface
 * @package Tfboe\FmLib\Entity\Helpers
 */
interface StartAndFinishableInterface extends TimeEntityGetterInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return int
   */
  public function getStatus(): int;

  /**
   * @return bool
   */
  public function isFinished(): bool;

  /**
   * @return bool
   */
  public function isStarted(): bool;

  /**
   * @param int $status
   * @param DateTime $now
   * @param bool $setStartTime
   * @param bool $setEndTime
   * @throws ValueNotValid
   */
  public function setStatus(int $status, DateTime $now, bool $setStartTime = true, bool $setEndTime = true): void;
//</editor-fold desc="Public Methods">
}