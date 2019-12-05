<?php
declare(strict_types=1);


namespace Tfboe\FmLib\Helpers;


use DateInterval;
use DateTime;

/**
 * Class DateTime
 * @package Tfboe\FmLib\Helpers
 */
abstract class DateTimeHelper
{
//<editor-fold desc="Public Methods">
  /**
   * @param DateTime|null $d1
   * @param DateTime|null $d2
   * @return bool
   */
  public static function eq(?DateTime $d1, ?DateTime $d2)
  {
    return $d1 == $d2 &&
      ($d1 === null || $d2 === null || $d1->getTimezone()->getName() === $d2->getTimezone()->getName());
  }

  /**
   * Returns a future datetime which is enough far in the future to be larger than all relevant upcoming date times
   * @return DateTime
   * @noinspection PhpDocMissingThrowsInspection
   */
  public static function future(): DateTime
  {
    return (new DateTime())->add(new DateInterval('P100Y'));
  }
//</editor-fold desc="Public Methods">
}