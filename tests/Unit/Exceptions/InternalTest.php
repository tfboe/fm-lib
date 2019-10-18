<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Exceptions;

use PHPUnit\Framework\Error\Error;
use Tfboe\FmLib\Exceptions\Internal;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class HandlerTest
 * @package Tfboe\FmLib\Tests\Unit\Exceptions
 */
class InternalTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Exceptions\Internal::error
   */
  public function testError()
  {
    $message = "ErrorMessage";
    $this->expectException(Error::class);
    $this->expectExceptionMessage($message);
    Internal::error($message);
  }

  /**
   * @covers \Tfboe\FmLib\Exceptions\Internal::assert
   * @uses   \Tfboe\FmLib\Exceptions\Internal::error
   */
  public function testFailedAssert()
  {
    $this->expectException(Error::class);
    $this->expectExceptionMessage("Assertion failed!");
    Internal::assert(false);
  }

  /**
   * @covers \Tfboe\FmLib\Exceptions\Internal::assert
   */
  public function testSuccessfulAssert()
  {
    Internal::assert(true);
    //dummy
    self::assertTrue(true);
  }
//</editor-fold desc="Public Methods">
}