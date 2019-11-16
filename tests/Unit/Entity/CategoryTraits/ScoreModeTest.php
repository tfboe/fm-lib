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
use Tfboe\FmLib\Entity\Categories\ScoreMode;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class ScoreModeTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\CategoryTraits
 */
class ScoreModeTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\ScoreMode::getScoreMode
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\ScoreMode::setScoreMode
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   */
  public function testProperty()
  {
    $mock = $this->mock();
    self::assertNull($mock->getScoreMode());

    $mock->setScoreMode(ScoreMode::BEST_OF_FIVE);
    self::assertEquals(ScoreMode::BEST_OF_FIVE, $mock->getScoreMode());

    $mock->setScoreMode(null);
    self::assertNull($mock->getScoreMode());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\ScoreMode::setScoreMode
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
      'Expected a valid value of Enum Tfboe\FmLib\Entity\Categories\ScoreMode but got 100');

    $mock->setScoreMode(100);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|\Tfboe\FmLib\Entity\CategoryTraits\ScoreMode
   * @throws ReflectionException
   */
  private function mock(): MockObject
  {
    return $this->getMockForTrait(\Tfboe\FmLib\Entity\CategoryTraits\ScoreMode::class);
  }
//</editor-fold desc="Private Methods">
}