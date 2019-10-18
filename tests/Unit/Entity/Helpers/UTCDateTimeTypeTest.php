<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 9:46 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity\Helpers;


use DateTime;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\ConversionException;
use ReflectionException;
use Tfboe\FmLib\Entity\Helpers\UTCDateTimeType;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class UTCDateTimeTypeTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\Helpers
 */
class UTCDateTimeTypeTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\UTCDateTimeType::convertToDatabaseValue
   * @covers \Tfboe\FmLib\Entity\Helpers\UTCDateTimeType::getUtc
   * @throws ConversionException
   * @throws ConversionException
   * @throws ReflectionException
   */
  public function testConvertToDatabaseValue()
  {
    //$type = Type::getType("datetime");
    /** @var UTCDateTimeType $type */
    $type = $this->getMockForAbstractClass(UTCDateTimeType::class, [], '', false);
    $platform = new MySqlPlatform();
    $datetime = new DateTime("2017-12-31 15:23:20 +02:00");
    $value = $type->convertToDatabaseValue($datetime, $platform);
    self::assertEquals("2017-12-31 13:23:20", $value);

    $datetime = new DateTime("2017-12-31 15:23:20");
    $value = $type->convertToDatabaseValue($datetime, $platform);
    self::assertEquals("2017-12-31 15:23:20", $value);
  }
//</editor-fold desc="Public Methods">
}