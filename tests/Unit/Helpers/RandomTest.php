<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Helpers;

use Tfboe\FmLib\Helpers\Random;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class HandlerTest
 * @package Tfboe\FmLib\Tests\Unit\Exceptions
 */
class RandomTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Helpers\Random::stringToRandom
   * @uses   \Tfboe\FmLib\Helpers\Random::__construct
   */
  public function testStringToRandom()
  {
    $message = "message";
    $i1 = Random::stringToRandom($message);
    self::assertEquals($i1, Random::stringToRandom($message));
    self::assertNotEquals($i1, Random::stringToRandom("Message"));
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\Random::__construct
   */
  public function testConstruct()
  {
    $hex = "0abce081f";
    $random = new Random($hex);
    self::assertInstanceOf(Random::class, $random);
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\Random::extractEntropyByBits
   * @uses   \Tfboe\FmLib\Helpers\Random::__construct
   * @uses   \Tfboe\FmLib\Exceptions\Internal::assert
   */
  public function testExtractEntropyByBits()
  {
    $hex = "0abce081f";
    $random = new Random($hex);
    $res1 = $random->extractEntropyByBits(10);
    self::assertGreaterThanOrEqual(0, $res1);
    self::assertLessThan(1024, $res1);
    $res2 = $random->extractEntropyByBits(1);
    self::assertGreaterThanOrEqual(0, $res2);
    self::assertLessThan(2, $res2);
    $random2 = new Random($hex);
    $res = $random2->extractEntropyByBits(11);
    self::assertEquals($res1 * 2 + $res2, $res);

    self::assertEquals($random->extractEntropyByBits(100), $random2->extractEntropyByBits(100));

    //now the entropy must be empty
    self::assertEquals(0, $random->extractEntropyByBits(100));
    self::assertEquals(0, $random2->extractEntropyByBits(100));

    $random3 = new Random("123456789abcdef0");

    //if we have bad luck the following may be wrong (probability 1/1024)
    self::assertNotEquals($res, $random3->extractEntropyByBits(11));
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\Random::extractEntropy
   * @covers \Tfboe\FmLib\Helpers\Random::countBits
   * @uses   \Tfboe\FmLib\Exceptions\Internal::assert
   * @uses   \Tfboe\FmLib\Helpers\Random::__construct
   * @uses   \Tfboe\FmLib\Helpers\Random::extractEntropyByBits
   */
  public function testEntropy()
  {
    $hex = "0abce081f";
    $random = new Random($hex);
    $res1 = $random->extractEntropy(799);
    $random2 = new Random($hex);
    $res2 = $random2->extractEntropy(1023);
    self::assertEquals($res1, $res2 % 800);
    $random3 = new Random($hex);
    $res3 = $random3->extractEntropy(523, -500);
    self::assertEquals($res2 - 500, $res3);
    $r1 = $random->extractEntropy(1000000000);
    $r2 = $random2->extractEntropy(1000000000);
    $r3 = $random3->extractEntropy(1000000000);
    self::assertEquals($r1, $r2);
    self::assertEquals($r2, $r3);
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\Random::extractEntropy
   * @covers \Tfboe\FmLib\Helpers\Random::countBits
   * @uses   \Tfboe\FmLib\Exceptions\Internal::assert
   * @uses   \Tfboe\FmLib\Helpers\Random::__construct
   * @uses   \Tfboe\FmLib\Helpers\Random::extractEntropyByBits
   */
  public function testFullRandomInteger()
  {
    $random = new Random("08791234abcdef778899aeef87224effffffeeee123234641aaa");
    self::assertIsInt($random->extractEntropy(PHP_INT_MAX, PHP_INT_MIN));
    self::assertIsInt($random->extractEntropy(PHP_INT_MAX - 1, PHP_INT_MIN + 1));
  }
//</editor-fold desc="Public Methods">
}