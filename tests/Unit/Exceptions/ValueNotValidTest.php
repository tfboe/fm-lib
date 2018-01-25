<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Exceptions;


use Tfboe\FmLib\Exceptions\ValueNotValid;
use Tfboe\FmLib\Tests\Helpers\TestEnum;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class ValueNotValidTest
 * @package Tfboe\FmLib\Tests\Unit\Exceptions
 */
class ValueNotValidTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Exceptions\ValueNotValid::__construct
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   */
  public function testConstructor()
  {
    $exc = new ValueNotValid("value");
    self::assertEquals('The following value is not valid: "value"', $exc->getMessage());

    $exc = new ValueNotValid("val", TestEnum::class);
    self::assertEquals('The following value is not valid: "val" in Tfboe\FmLib\Tests\Helpers\TestEnum. Possible values:' .
      ' "value", 1.', $exc->getMessage());

    $exc = new ValueNotValid("val", TestEnum::class, "getNames");
    self::assertEquals('The following value is not valid: "val" in Tfboe\FmLib\Tests\Helpers\TestEnum. Possible values:' .
      ' "KEY", "INT_KEY".', $exc->getMessage());
  }
//</editor-fold desc="Public Methods">
}