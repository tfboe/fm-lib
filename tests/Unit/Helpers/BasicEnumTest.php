<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 11:03 AM
 */

namespace Tfboe\FmLib\Tests\Unit\Helpers;

use PHPUnit\Framework\Error\Error;
use Tfboe\FmLib\Helpers\BasicEnum;
use Tfboe\FmLib\TestHelpers\TestEnum;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class BasicEnumTest
 * @package Tfboe\FmLib\TestHelpers
 */
class BasicEnumTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Helpers\BasicEnum::ensureValidValue
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getValues
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::isValidValue
   * @uses   \Tfboe\FmLib\Exceptions\Internal::assert
   * @uses   \Tfboe\FmLib\Exceptions\Internal::error
   */
  public function testEnsureValidValueException()
  {

    TestEnum::ensureValidValue(TestEnum::INT_KEY);
    $this->expectException(Error::class);
    $this->expectExceptionMessage(
      'Expected a valid value of Enum Tfboe\FmLib\TestHelpers\TestEnum but got 1');

    TestEnum::ensureValidValue('1');
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\BasicEnum::getName
   * @covers \Tfboe\FmLib\Helpers\BasicEnum::getNamesArray
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   */
  public function testGetName()
  {

    self::assertEquals("KEY", TestEnum::getName(TestEnum::KEY));

    self::assertEquals("INT_KEY", TestEnum::getName(TestEnum::INT_KEY));
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\BasicEnum::getName
   * @covers \Tfboe\FmLib\Helpers\BasicEnum::getNamesArray
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getValues
   * @uses   \Tfboe\FmLib\Exceptions\Internal::assert
   * @uses   \Tfboe\FmLib\Exceptions\Internal::error
   */
  public function testGetNameException()
  {
    $this->expectException(Error::class);
    $this->expectExceptionMessage(
      'Expected a valid value of Enum Tfboe\FmLib\TestHelpers\TestEnum but got int_key');

    TestEnum::getName('int_key');
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\BasicEnum::getNames
   * @covers \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   */
  public function testGetNames()
  {
    self::assertEquals(['KEY', 'INT_KEY'], TestEnum::getNames());
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\BasicEnum::getValue
   * @covers \Tfboe\FmLib\Helpers\BasicEnum::getCaseMapping
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getNames
   */
  public function testGetValue()
  {

    self::assertEquals("value", TestEnum::getValue('KEY'));

    self::assertEquals(1, TestEnum::getValue('int_key'));

    self::assertEquals(1, TestEnum::getValue('INT_KEY', True));
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\BasicEnum::getValue
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getValues
   * @uses   \Tfboe\FmLib\Exceptions\Internal::assert
   * @uses   \Tfboe\FmLib\Exceptions\Internal::error
   */
  public function testGetValueException()
  {
    $this->expectException(Error::class);
    $this->expectExceptionMessage(
      'Expected a valid name of Enum Tfboe\FmLib\TestHelpers\TestEnum but got int_key');

    TestEnum::getValue('int_key', True);
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\BasicEnum::getValues
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   */
  public function testGetValues()
  {
    self::assertEquals(['value', 1], TestEnum::getValues());
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\BasicEnum::isValidName
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   */
  public function testIsValidName()
  {
    self::assertTrue(TestEnum::isValidName('KEY'));
    self::assertTrue(TestEnum::isValidName('int_key'));
    self::assertFalse(TestEnum::isValidName('INT-KEY'));

    self::assertTrue(TestEnum::isValidName('INT_KEY', True));
    self::assertFalse(TestEnum::isValidName('int_key', True));
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\BasicEnum::isValidValue
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getValues
   */
  public function testIsValidValue()
  {
    self::assertTrue(TestEnum::isValidValue('value'));
    self::assertTrue(TestEnum::isValidValue(1));
    self::assertFalse(TestEnum::isValidValue('1'));
    self::assertFalse(TestEnum::isValidValue('VALUE'));

    self::assertTrue(TestEnum::isValidValue('1', False));
    self::assertFalse(TestEnum::isValidValue('VALUE', False));
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Methods">
  /**
   * @before
   */
  protected function clearStaticVariables()
  {

    self::getProperty(BasicEnum::class, 'constCacheArray')->setValue(NULL);

    self::getProperty(BasicEnum::class, 'constCacheArrayCaseMapping')->setValue(NULL);
  }
//</editor-fold desc="Protected Methods">
}