<?php


namespace Tfboe\FmLib\Helpers;


class Random
{
//<editor-fold desc="Public Methods">
  public static function StringToInt($str): int
  {
    $result = unpack('q', md5($str, true));
    return $result[1];
  }
//</editor-fold desc="Public Methods">
}