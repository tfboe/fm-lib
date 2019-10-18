<?php


namespace Tfboe\FmLib\Helpers;


abstract class Random
{
//<editor-fold desc="Public Methods">
  public static function stringToInt($str): int
  {
    $result = unpack('q', md5($str, true));
    return $result[1];
  }
//</editor-fold desc="Public Methods">
}