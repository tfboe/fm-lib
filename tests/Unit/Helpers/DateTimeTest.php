<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 11:03 AM
 */

namespace Tfboe\FmLib\Tests\Unit\Helpers;

use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use Tfboe\FmLib\Helpers\DateTimeHelper;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class BasicEnumTest
 * @package Tfboe\FmLib\TestHelpers
 */
class DateTimeTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \Tfboe\FmLib\Helpers\DateTimeHelper::eq
   * @throws Exception
   */
  public function testDateTimeEq()
  {
    $d1 = new DateTime("2019-01-01 00:00:00", new DateTimeZone("+00:00"));
    $d2 = new DateTime("2019-01-01 00:00:00", new DateTimeZone("+00:00"));
    $d3 = new DateTime("2019-01-01 00:00:01", new DateTimeZone("+00:00"));
    $d4 = new DateTime("2019-01-01 00:00:00", new DateTimeZone("+00:30"));

    self::assertTrue(DateTimeHelper::eq($d1, $d1));
    self::assertTrue(DateTimeHelper::eq($d1, $d2));
    self::assertEquals($d1, $d2);
    self::assertFalse($d1 === $d2);
    self::assertFalse(DateTimeHelper::eq($d1, $d3));
    self::assertFalse(DateTimeHelper::eq($d1, $d4));


    $d2->setTimezone(new DateTimeZone("+00:30"));
    self::assertFalse(DateTimeHelper::eq($d1, $d2));
    self::assertFalse(DateTimeHelper::eq($d4, $d2));
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\DateTimeHelper::future
   * @throws Exception
   */
  public function testFuture()
  {
    $d1 = new DateTime();
    $d1->add(new DateInterval('P50Y')); //50 years should be enough future for a while ;)
    self::assertGreaterThan($d1, DateTimeHelper::future());
  }
//</editor-fold desc="Public Methods">
}