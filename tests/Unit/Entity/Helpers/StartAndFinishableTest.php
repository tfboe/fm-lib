<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:52 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity\Helpers;

use DateTime;
use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;
use Tfboe\FmLib\Entity\Helpers\StartAndFinishable;
use Tfboe\FmLib\Entity\Helpers\StartAndFinishableInterface;
use Tfboe\FmLib\Entity\Helpers\StartFinishStatus;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class BaseEntityTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\Helpers
 */
class StartAndFinishableTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::cloneFrom
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Entity\Traits\LastRecalculation::getEndTime
   * @uses   \Tfboe\FmLib\Entity\Traits\LastRecalculation::getStartTime
   * @uses   \Tfboe\FmLib\Entity\Traits\LastRecalculation::setEndTime
   * @uses   \Tfboe\FmLib\Entity\Traits\LastRecalculation::setStartTime
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::getStatus
   */
  public function testCloneFrom()
  {
    $entity = $this->mock();
    self::assertEquals(StartFinishStatus::NOT_STARTED, $entity->getStatus());
    self::assertNull($entity->getStartTime());
    self::assertNull($entity->getEndTime());

    $start = new DateTime("2019-01-01");
    $end = new DateTime("2019-02-01");
    $status = StartFinishStatus::FINISHED;
    $clone = $this->createStub(StartAndFinishableInterface::class,
      ['getStatus' => $status, 'getStartTime' => $start, 'getEndTime' => $end]);

    $entity->cloneFrom($clone);
    self::assertEquals($status, $entity->getStatus());
    self::assertEquals($start, $entity->getStartTime());
    self::assertEquals($end, $entity->getEndTime());
  }

  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::setStatus
   * @covers \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::changeIsValid
   * @covers \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::ensureValidValue
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::ensureValidValue
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getValues
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::isValidValue
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::statusIsFinished
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::statusIsStarted
   * @uses   \Tfboe\FmLib\Exceptions\Internal::error
   */
  public function testStatusFromFinishedToNotStarted()
  {
    $entity = $this->mock();
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::STARTED, new DateTime(), false, false);
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::FINISHED, new DateTime(), false, false);
    $this->expectException(Error::class);
    $this->expectExceptionMessage('Invalid status change!');
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::NOT_STARTED, new DateTime(), false, false);
  }

  /** @noinspection PhpDocMissingThrowsInspection */

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::setStatus
   * @covers \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::changeIsValid
   * @covers \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::ensureValidValue
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::ensureValidValue
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getValues
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::isValidValue
   * @uses   \Tfboe\FmLib\Exceptions\Internal::error
   */
  public function testStatusFromNotRunningToFinished()
  {
    $entity = $this->mock();
    $this->expectException(Error::class);
    $this->expectExceptionMessage('Invalid status change!');
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::FINISHED, new DateTime(), false, false);
  }

  /** @noinspection PhpDocMissingThrowsInspection */

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::getStatus
   * @covers \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::setStatus
   * @covers \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::ensureValidValue
   * @covers \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::changeIsValid
   * @covers \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::isFinished
   * @covers \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::isStarted
   * @covers \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::statusIsFinished
   * @covers \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::statusIsStarted
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getValues
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::isValidValue
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::ensureValidValue
   */
  public function testStatusWOTimes()
  {
    $entity = $this->mock();
    self::assertEquals(StartFinishStatus::NOT_STARTED, $entity->getStatus());
    self::assertFalse($entity->isStarted());
    self::assertFalse($entity->isFinished());
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::STARTED, new DateTime(), false, false);
    self::assertEquals(StartFinishStatus::STARTED, $entity->getStatus());
    self::assertTrue($entity->isStarted());
    self::assertFalse($entity->isFinished());
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::FINISHED, new DateTime(), false, false);
    self::assertEquals(StartFinishStatus::FINISHED, $entity->getStatus());
    self::assertTrue($entity->isStarted());
    self::assertTrue($entity->isFinished());
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::FINISHED, new DateTime(), false, false);
    self::assertEquals(StartFinishStatus::FINISHED, $entity->getStatus());
    self::assertTrue($entity->isStarted());
    self::assertTrue($entity->isFinished());
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::STARTED, new DateTime(), false, false);
    self::assertEquals(StartFinishStatus::STARTED, $entity->getStatus());
    self::assertTrue($entity->isStarted());
    self::assertFalse($entity->isFinished());
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::NOT_STARTED, new DateTime(), false, false);
    self::assertEquals(StartFinishStatus::NOT_STARTED, $entity->getStatus());
    self::assertFalse($entity->isStarted());
    self::assertFalse($entity->isFinished());
  }

  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::setStatus
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::changeIsValid
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::ensureValidValue
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::getStatus
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::ensureValidValue
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getValues
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::isValidValue
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::statusIsFinished
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::statusIsStarted
   * @uses   \Tfboe\FmLib\Entity\Helpers\TimeEntity::getEndTime
   * @uses   \Tfboe\FmLib\Entity\Helpers\TimeEntity::getStartTime
   * @uses   \Tfboe\FmLib\Entity\Helpers\TimeEntity::setEndTime
   * @uses   \Tfboe\FmLib\Entity\Helpers\TimeEntity::setStartTime
   * @uses   \Tfboe\FmLib\Helpers\DateTime::eq
   */
  public function testTimeUpdates()
  {
    /** @var MockObject|StartAndFinishable $entity */
    $entity = $this->getMockForTrait(StartAndFinishable::class, [], '', true, true, true, ['changeIsValid']);
    //allow all changes
    $entity->method('changeIsValid')->willReturn(true);
    self::assertEquals(StartFinishStatus::NOT_STARTED, $entity->getStatus());
    self::assertNull($entity->getStartTime());
    self::assertNull($entity->getEndTime());

    $date1 = new DateTime("2019-01-01");
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::STARTED, $date1);
    self::assertEquals($date1, $entity->getStartTime());

    $date2 = new DateTime("2019-02-01");
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::NOT_STARTED, $date2);
    self::assertNull($entity->getStartTime());

    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::STARTED, $date1, false);
    self::assertNull($entity->getStartTime());

    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::NOT_STARTED, $date2);
    self::assertNull($entity->getStartTime());

    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::STARTED, $date1);
    self::assertEquals($date1, $entity->getStartTime());
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::NOT_STARTED, $date2, false);
    self::assertEquals($date1, $entity->getStartTime());

    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::STARTED, $date2);
    self::assertEquals($date2, $entity->getStartTime());

    $date3 = new DateTime("2019-03-01");
    self::assertNull($entity->getEndTime());
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::FINISHED, $date3);
    self::assertEquals($date2, $entity->getStartTime());
    self::assertEquals($date3, $entity->getEndTime());

    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::STARTED, $date1);
    self::assertEquals($date2, $entity->getStartTime());
    self::assertNull($entity->getEndTime());

    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::FINISHED, $date3, true, false);
    self::assertEquals($date2, $entity->getStartTime());
    self::assertNull($entity->getEndTime());

    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::STARTED, $date1);
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::FINISHED, $date3);
    self::assertEquals($date2, $entity->getStartTime());
    self::assertEquals($date3, $entity->getEndTime());

    //test skipping
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::NOT_STARTED, $date1);
    self::assertNull($entity->getStartTime());
    self::assertNull($entity->getEndTime());
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::FINISHED, $date3);
    self::assertEquals($date3, $entity->getStartTime());
    self::assertEquals($date3, $entity->getEndTime());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|StartAndFinishable
   * @throws ReflectionException
   */
  private function mock(): MockObject
  {
    return $this->getMockForTrait(StartAndFinishable::class);
  }
//</editor-fold desc="Private Methods">
}