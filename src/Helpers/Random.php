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
    $range = $max - $min;
    Internal::assert($range <= PHP_INT_MAX);
    $bits = $this->countBits($range - 1);

    return $min + ($this->extractEntropyByBits($bits) % $range);
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
      $fullExtractLength = 1 + intdiv($bits - $this->remainingBitsFirstChar, 4);
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
  private function countBits(int $n): int
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