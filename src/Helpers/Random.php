<?php
declare(strict_types=1);


namespace Tfboe\FmLib\Helpers;


/**
 * Class Random
 * @package Tfboe\FmLib\Helpers
 */
abstract class Random
{
//<editor-fold desc="Public Methods">
  /**
   * @param $str
   * @return int
   */
  public static function stringToInt($str): int
  {
    $result = unpack('q', md5($str, true));
    return $result[1];
  }
//</editor-fold desc="Public Methods">
}