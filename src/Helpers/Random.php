<?php
declare(strict_types=1);


namespace Tfboe\FmLib\Helpers;


use Tfboe\FmLib\Exceptions\Internal;

/**
 * Class Random
 * @package Tfboe\FmLib\Helpers
 */
class Random
{
//<editor-fold desc="Fields">
  private const ENTROPY = [
    1 => 2,
    2 => 4,
    3 => 8
  ];

  /**
   * @var string
   */
  private $hexString;

  /**
   * @var int
   */
  private $remainingBitsFirstChar;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * Random constructor.
   * @param string $hexString
   */
  public function __construct(string $hexString)
  {
    $this->hexString = $hexString;
    $this->remainingBitsFirstChar = 4;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @param int $max
   * @param int $min
   * @return int
   */
  public function extractEntropy(int $max, int $min = 0): int
  {
    Internal::assert($max <= PHP_INT_MAX && $min >= PHP_INT_MIN && $min <= $max);
    $maxInt = (PHP_INT_MAX >> 1);
    if (($max > $maxInt && ($min <= 0 || $max - $min > $maxInt)) ||
      ($min < -$maxInt && ($max >= 0 || $max - $min > $maxInt)) || ($max - $min) > $maxInt) {
      //calculate floor(($max - $min) / 16) considering overflows
      $max16 = $max % 16;
      $min16 = $min % 16;
      $max1 = (($max - $max16) >> 4) - (($min - $min16) >> 4);
      $rest = $max16 - $min16;
      if ($rest >= 16) {
        $max1++;
        $rest -= 16;
      }

      $z1 = $this->extractEntropy($max1);
      $max2 = 15;
      if ($z1 == $max1) {
        $max2 = $rest;
      }
      $z2 = $this->extractEntropy($max2);
      return (($min + 8 * $z1) + 8 * $z1) + $z2;
    }
    $range = $max - $min;
    Internal::assert($range <= PHP_INT_MAX);
    $bits = $this->countBits($range);

    return $min + ($this->extractEntropyByBits($bits) % ($range + 1));
  }

  /**
   * @param int $bits
   * @return int
   */
  public function extractEntropyByBits(int $bits): int
  {
    Internal::assert($bits >= 0);
    if ($bits === 0 || empty($this->hexString)) {
      return 0; //no entropy
    }
    $value = 0;
    if ($bits >= $this->remainingBitsFirstChar) {
      //extract full positions
      $fullExtractLength = 1 + (($bits - $this->remainingBitsFirstChar) >> 2);
      $value = hexdec(substr($this->hexString, 0, $fullExtractLength));
      $bits -= $this->remainingBitsFirstChar + ($fullExtractLength - 1) * 4;
      $this->remainingBitsFirstChar = 4;
      $this->hexString = substr($this->hexString, $fullExtractLength);
      if (empty($this->hexString)) {
        return $value;
      }
    }
    if ($bits > 0) {
      $digit = hexdec(substr($this->hexString, 0, 1));
      $r = self::ENTROPY[$bits];
      $value *= $r;
      $this->remainingBitsFirstChar -= $bits;
      $value += $digit >> $this->remainingBitsFirstChar;
      $this->hexString = dechex($digit & (self::ENTROPY[$this->remainingBitsFirstChar] - 1))
        . substr($this->hexString, 1);
    }
    return $value;
  }

  /**
   * @param $str
   * @return Random
   */
  public static function stringToRandom($str): Random
  {
    return new Random(hash("sha256", $str));
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @param $n
   * @return int
   */
  private static function countBits(int $n): int
  {
    $count = 0;
    while ($n > 0) {
      $count++;
      $n >>= 1;
    }
    return $count;
  }
//</editor-fold desc="Private Methods">
}