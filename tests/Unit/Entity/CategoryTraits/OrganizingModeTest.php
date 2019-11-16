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
use ReflectionException;
use Tfboe\FmLib\Entity\Categories\OrganizingMode;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class OrganizingModeTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\CategoryTraits
 */
class OrganizingModeTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\OrganizingMode::getOrganizingMode
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\OrganizingMode::setOrganizingMode
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   */
  public function testProperty()
  {
    $mock = $this->mock();
    self::assertNull($mock->getOrganizingMode());

    $mock->setOrganizingMode(OrganizingMode::ELIMINATION);
    self::assertEquals(OrganizingMode::ELIMINATION, $mock->getOrganizingMode());

    $mock->setOrganizingMode(null);
    self::assertNull($mock->getOrganizingMode());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\OrganizingMode::setOrganizingMode
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   * @uses   \Tfboe\FmLib\Exceptions\Internal::assert
   * @uses   \Tfboe\FmLib\Exceptions\Internal::error
   */
  public function testPropertyException()
  {
    $mock = $this->mock();
    $this->expectException(Error::class);
    $this->expectExceptionMessage(
      'Expected a valid value of Enum Tfboe\FmLib\Entity\Categories\OrganizingMode but got 100');
    $mock->setOrganizingMode(100);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|\Tfboe\FmLib\Entity\CategoryTraits\OrganizingMode
   * @throws ReflectionException
   */
  private function mock(): MockObject
  {
    return $this->getMockForTrait(\Tfboe\FmLib\Entity\CategoryTraits\OrganizingMode::class);
  }
//</editor-fold desc="Private Methods">
}