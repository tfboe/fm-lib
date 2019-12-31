<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 9:22 PM
 */

namespace Tfboe\FmLib\Entity\Helpers;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

/**
 * Class UTCDateTimeType
 * @package Tfboe\FmLib\Entity\Helpers
 */
class UTCDateTimeType extends DateTimeType
{
//<editor-fold desc="Fields">
  private static $utc;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * {@inheritdoc}
   * @throws ConversionException
   */
  public function convertToDatabaseValue($value, AbstractPlatform $platform)
  {
    if ($value instanceof DateTime) {
      $value->setTimezone(self::getUtc());
    }

    return parent::convertToDatabaseValue($value, $platform);
  }

  /**
   * {@inheritdoc}
   */
  public function convertToPHPValue($value, AbstractPlatform $platform)
  {
    if ($value === null || $value instanceof \DateTimeInterface) {
      return $value;
    }

    $val = DateTime::createFromFormat($platform->getDateTimeFormatString(), $value, self::getUtc());

    if (! $val) {
      $val = date_create($value, self::getUtc());
    }

    if (! $val) {
      throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeFormatString());
    }

    return $val;
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * Gets the utc datetime zone
   * @return DateTimeZone
   */
  private static function getUtc(): DateTimeZone
  {
    return self::$utc ? self::$utc : self::$utc = new DateTimeZone('UTC');
  }
//</editor-fold desc="Private Methods">
}