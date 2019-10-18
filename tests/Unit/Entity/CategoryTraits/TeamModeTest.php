<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:05 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity\CategoryTraits;

use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;
use Tfboe\FmLib\Entity\Categories\TeamMode;
use Tfboe\FmLib\Exceptions\ValueNotValid;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class TeamModeTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\CategoryTraits
 */
class TeamModeTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\TeamMode::getTeamMode
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\TeamMode::setTeamMode
   * @throws ReflectionException
   * @throws ValueNotValid
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   */
  public function testProperty()
  {
    $mock = $this->mock();
    self::assertNull($mock->getTeamMode());
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setTeamMode(TeamMode::DOUBLE);
    self::assertEquals(TeamMode::DOUBLE, $mock->getTeamMode());
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setTeamMode(null);
    self::assertNull($mock->getTeamMode());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\TeamMode::setTeamMode
   * @throws ReflectionException
   * @throws ValueNotValid
   * @uses   \Tfboe\FmLib\Exceptions\ValueNotValid::__construct
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   */
  public function testPropertyException()
  {
    $mock = $this->mock();
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage(
      'The following value is not valid: 100 in Tfboe\FmLib\Entity\Categories\TeamMode. Possible values: 0, 1, 2.');
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setTeamMode(100);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|\Tfboe\FmLib\Entity\CategoryTraits\TeamMode
   * @throws ReflectionException
   */
  private function mock(): MockObject
  {
    return $this->getMockForTrait(\Tfboe\FmLib\Entity\CategoryTraits\TeamMode::class);
  }
//</editor-fold desc="Private Methods">
}