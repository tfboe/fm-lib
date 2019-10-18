<?php
declare(strict_types=1);


namespace Tfboe\FmLib\Helpers;


/**
 * Class DateTime
 * @package Tfboe\FmLib\Helpers
 */
abstract class DateTime
{
//<editor-fold desc="Public Methods">
  /**
   * @param \DateTime|null $d1
   * @param \DateTime|null $d2
   * @return bool
   */
  public static function eq(?\DateTime $d1, ?\DateTime $d2)
  {
    return $d1 == $d2 && ($d1 === null || $d1->getTimezone()->getName() === $d2->getTimezone()->getName());
  }
//</editor-fold desc="Public Methods">
}