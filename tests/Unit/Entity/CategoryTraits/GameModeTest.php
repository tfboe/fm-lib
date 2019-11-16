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
use Tfboe\FmLib\Entity\Categories\GameMode;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class GameModeTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\CategoryTraits
 */
class GameModeTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\GameMode::getGameMode
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\GameMode::setGameMode
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   */
  public function testProperty()
  {
    $mock = $this->mock();
    self::assertNull($mock->getGameMode());

    $mock->setGameMode(GameMode::SPEEDBALL);
    self::assertEquals(GameMode::SPEEDBALL, $mock->getGameMode());

    $mock->setGameMode(null);
    self::assertNull($mock->getGameMode());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\GameMode::setGameMode
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
      'Expected a valid value of Enum Tfboe\FmLib\Entity\Categories\GameMode but got 100');

    $mock->setGameMode(100);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|\Tfboe\FmLib\Entity\CategoryTraits\GameMode
   * @throws ReflectionException
   */
  private function mock(): MockObject
  {
    return $this->getMockForTrait(\Tfboe\FmLib\Entity\CategoryTraits\GameMode::class);
  }
//</editor-fold desc="Private Methods">
}