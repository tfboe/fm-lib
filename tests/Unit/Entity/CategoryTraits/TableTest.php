<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:05 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity\CategoryTraits;

use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\MockObject\MockObject;
use Tfboe\FmLib\Entity\Categories\Table;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class TableTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\CategoryTraits
 */
class TableTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\Table::getTable
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\Table::setTable
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   */
  public function testProperty()
  {
    $mock = $this->mock();
    self::assertNull($mock->getTable());

    $mock->setTable(Table::ROBERTO_SPORT);
    self::assertEquals(Table::ROBERTO_SPORT, $mock->getTable());

    $mock->setTable(null);
    self::assertNull($mock->getTable());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\Table::setTable
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   * @uses   \Tfboe\FmLib\Exceptions\Internal::assert
   * @uses   \Tfboe\FmLib\Exceptions\Internal::error
   */
  public function testPropertyException()
  {
    $mock = $this->mock();
    $this->expectException(Error::class);
    $this->expectExceptionMessage(
      'Expected a valid value of Enum Tfboe\FmLib\Entity\Categories\Table but got 100');

    $mock->setTable(100);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|\Tfboe\FmLib\Entity\CategoryTraits\Table
   */
  private function mock(): MockObject
  {
    return $this->getMockForTrait(\Tfboe\FmLib\Entity\CategoryTraits\Table::class);
  }
//</editor-fold desc="Private Methods">
}