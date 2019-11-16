<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:52 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity\Helpers;

use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;
use Tfboe\FmLib\Entity\Helpers\Result;
use Tfboe\FmLib\Entity\Helpers\ResultEntity;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class BaseEntityTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\Helpers
 */
class ResultEntityTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::setPlayed
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::isPlayed
   * @throws ReflectionException
   */
  public function testPlayed()
  {
    $entity = $this->mock();
    $played = true;
    $entity->setPlayed($played);
    self::assertEquals($played, $entity->isPlayed());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::setResult
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::getResult
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   */
  public function testResult()
  {
    $mock = $this->mock();

    $mock->setResult(Result::DRAW);
    self::assertEquals(Result::DRAW, $mock->getResult());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::setResultA
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::getResultA
   * @throws ReflectionException
   */
  public function testResultA()
  {
    $entity = $this->mock();
    $res = 1;
    $entity->setResultA($res);
    self::assertEquals($res, $entity->getResultA());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::setResultB
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::getResultB
   * @throws ReflectionException
   */
  public function testResultB()
  {
    $entity = $this->mock();
    $res = 1;
    $entity->setResultB($res);
    self::assertEquals($res, $entity->getResultB());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::init
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::getResult
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::getResultA
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::getResultB
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::isPlayed
   */
  public function testInit()
  {
    $entity = $this->mock();
    self::callProtectedMethod($entity, 'init');
    self::assertEquals(0, $entity->getResultA());
    self::assertEquals(0, $entity->getResultB());
    self::assertEquals(Result::NOT_YET_FINISHED, $entity->getResult());
    self::assertEquals(false, $entity->isPlayed());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\ResultEntity::setResult
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   * @uses   \Tfboe\FmLib\Exceptions\Internal::assert
   * @uses   \Tfboe\FmLib\Exceptions\Internal::error
   */
  public function testResultNotValidException()
  {
    $mock = $this->mock();
    $this->expectException(Error::class);
    $this->expectExceptionMessage(
      'Expected a valid value of Enum Tfboe\FmLib\Entity\Helpers\Result but got 100');

    $mock->setResult(100);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|ResultEntity
   * @throws ReflectionException
   */
  private function mock(): MockObject
  {
    return $this->getMockForTrait(ResultEntity::class);
  }
//</editor-fold desc="Private Methods">
}