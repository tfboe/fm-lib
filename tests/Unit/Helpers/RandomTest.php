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
   * @covers \Tfboe\FmLib\Helpers\Random::stringToInt
   */
  public function testStringToInt()
  {
    $message = "message";
    $i1 = Random::stringToInt($message);
    self::assertIsInt($i1);
    self::assertEquals($i1, Random::stringToInt($message));
    self::assertNotEquals($i1, Random::stringToInt("Message"));
  }
//</editor-fold desc="Public Methods">
}