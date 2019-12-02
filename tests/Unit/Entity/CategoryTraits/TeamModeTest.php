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
use Tfboe\FmLib\Entity\Categories\TeamMode;
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
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   */
  public function testProperty()
  {
    $mock = $this->mock();
    self::assertNull($mock->getTeamMode());

    $mock->setTeamMode(TeamMode::DOUBLE);
    self::assertEquals(TeamMode::DOUBLE, $mock->getTeamMode());

    $mock->setTeamMode(null);
    self::assertNull($mock->getTeamMode());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\TeamMode::setTeamMode
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   * @uses   \Tfboe\FmLib\Exceptions\Internal::assert
   * @uses   \Tfboe\FmLib\Exceptions\Internal::error
   */
  public function testPropertyException()
  {
    $mock = $this->mock();
    $this->expectException(Error::class);
    $this->expectExceptionMessage(
      'Expected a valid value of Enum Tfboe\FmLib\Entity\Categories\TeamMode but got 100');

    $mock->setTeamMode(100);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|\Tfboe\FmLib\Entity\CategoryTraits\TeamMode
   */
  private function mock(): MockObject
  {
    return $this->getMockForTrait(\Tfboe\FmLib\Entity\CategoryTraits\TeamMode::class);
  }
//</editor-fold desc="Private Methods">
}