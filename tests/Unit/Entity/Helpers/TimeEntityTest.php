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
use DateTimeZone;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;
use ReflectionObject;
use Tfboe\FmLib\Entity\Helpers\TimeEntity;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class BaseEntityTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\Helpers
 */
class TimeEntityTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\TimeEntity::setEndTime
   * @covers \Tfboe\FmLib\Entity\Helpers\TimeEntity::getEndTime
   * @throws Exception
   */
  public function testEndTime()
  {
    $entity = $this->mock();
    $time = new DateTime('2017-12-31 16:00', new DateTimeZone('Europe/Vienna'));
    $entity->setEndTime($time);
    self::assertEquals($time, $entity->getEndTime());
    $entity->setEndTime(null);
    self::assertNull($entity->getEndTime());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\TimeEntity::getEndTime
   * @throws ReflectionException
   * @throws Exception
   */
  public function testGetEndTimeNotLocalized()
  {
    $entity = $this->mock();
    $parentClass = (new ReflectionObject($entity))->getParentClass();
    $property = $parentClass->getProperty('endTime');
    $property->setAccessible(true);


    self::getProperty(get_class($entity), 'endTime')->setValue($entity,
      new DateTime("2017-01-01 05:00:00", new DateTimeZone("UTC")));

    self::getProperty(get_class($entity), 'endTimezone')->setValue($entity, "+02:00");

    $time = $entity->getEndTime();
    self::assertEquals("2017-01-01 07:00:00 +0200", $time->format("Y-m-d H:i:s O"));
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\TimeEntity::postLoad
   * @throws ReflectionException
   * @throws Exception
   * @uses   \Tfboe\FmLib\Entity\Traits\Recalculation::getEndTime
   * @uses   \Tfboe\FmLib\Entity\Traits\Recalculation::setEndTime
   * @uses   \Tfboe\FmLib\Entity\Traits\Recalculation::getStartTime
   * @uses   \Tfboe\FmLib\Entity\Traits\Recalculation::setStartTime
   */
  public function testPostLoadEndTime()
  {
    $entity = $this->mock();
    $parentClass = (new ReflectionObject($entity))->getParentClass();
    $propertyStart = $parentClass->getProperty('startTime');
    $propertyStart->setAccessible(true);
    $propertyEnd = $parentClass->getProperty('endTime');
    $propertyEnd->setAccessible(true);

    $time1 = new DateTime('2017-12-31 16:00', new DateTimeZone('Europe/Vienna'));
    $entity->setStartTime($time1);
    self::assertEquals($time1, $entity->getStartTime());

    $time2 = new DateTime('2017-12-31 18:00', new DateTimeZone('Europe/Vienna'));
    $entity->setEndTime($time2);
    self::assertEquals($time2, $entity->getEndTime());

    //simulate a database update
    $newUTCTime1 = new DateTime("2017-01-01 05:00:00", new DateTimeZone("UTC"));
    $newTimeZone1 = "+04:00";
    $newTime1 = new DateTime("2017-01-01 09:00:00", new DateTimeZone($newTimeZone1));

    $newUTCTime2 = new DateTime("2017-01-01 10:00:00", new DateTimeZone("UTC"));
    $newTimeZone2 = "+04:00";
    $newTime2 = new DateTime("2017-01-01 14:00:00", new DateTimeZone($newTimeZone1));

    self::getProperty(get_class($entity), 'startTime')->setValue($entity, $newUTCTime1);
    self::getProperty(get_class($entity), 'startTimezone')->setValue($entity, $newTimeZone1);
    self::getProperty(get_class($entity), 'endTime')->setValue($entity, $newUTCTime2);
    self::getProperty(get_class($entity), 'endTimezone')->setValue($entity, $newTimeZone2);

    //without postLoad event the end time will still be the same
    self::assertNotEquals($newTime1, $entity->getStartTime());
    self::assertNotEquals($newTime2, $entity->getEndTime());

    //execute postLoad event
    $entity->postLoad();

    self::assertEquals($newTime1, $entity->getStartTime());
    self::assertEquals($newTime2, $entity->getEndTime());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\TimeEntity::getStartTime
   * @throws ReflectionException
   * @throws Exception
   * @throws ReflectionException
   */
  public function testGetStartTimeNotLocalized()
  {
    $entity = $this->mock();
    $parentClass = (new ReflectionObject($entity))->getParentClass();
    $property = $parentClass->getProperty('startTime');
    $property->setAccessible(true);
    $property->setValue($entity, new DateTime("2017-01-01 05:00:00", new DateTimeZone("UTC")));

    $property = $parentClass->getProperty('startTimezone');
    $property->setAccessible(true);
    $property->setValue($entity, "+02:00");

    $time = $entity->getStartTime();
    self::assertEquals("2017-01-01 07:00:00 +0200", $time->format("Y-m-d H:i:s O"));
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\TimeEntity
   */
  public function testInitialState()
  {
    $entity = $this->mock();
    self::assertNull($entity->getStartTime());
    self::assertNull($entity->getEndTime());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\TimeEntity::setStartTime
   * @covers \Tfboe\FmLib\Entity\Helpers\TimeEntity::getStartTime
   * @throws Exception
   */
  public function testStartTime()
  {
    $entity = $this->mock();
    $time = new DateTime('2017-12-31 16:00', new DateTimeZone('Europe/Vienna'));
    $entity->setStartTime($time);
    self::assertEquals($time, $entity->getStartTime());
    $entity->setStartTime(null);
    self::assertNull($entity->getStartTime());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|TimeEntity
   */
  private function mock(): MockObject
  {
    return $this->getMockForTrait(TimeEntity::class);
  }
//</editor-fold desc="Private Methods">
}