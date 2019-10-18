<?php


namespace Tfboe\FmLib\Helpers;


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
    if ($d1 === null && $d1 === null) {
      return true;
    }
    if ($d1 === null || $d2 === null) {
      return false;
    }
    return $d1->getTimestamp() === $d2->getTimestamp() && $d1->getTimezone() === $d2->getTimezone();
  }
//</editor-fold desc="Public Methods">
}