<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/6/19
 * Time: 10:30 PM
 */

namespace Tfboe\FmLib\Helpers;

use Closure;
use DateTime as CoreDateTime;
use Exception;

/**
 * Class TransformerFactory
 * @package App\Helpers
 */
abstract class TransformerFactory
{
//<editor-fold desc="Public Methods">
  /**
   * @return Callable
   */
  public static function booleanTransformer(): Callable
  {
    return function ($value) {
      if ($value === 'true') {
        return true;
      } elseif ($value === 'false') {
        return false;
      } else {
        return $value;
      }
    };
  }

  /**
   * Gets a transformation function which transforms a string in datetime format into a datetime with the given timezone
   * @param string $datetimetzFormat the format to use in the transformation
   * @return Closure the function which transforms a string into a datetime
   */
  public static function datetimetzTransformer(string $datetimetzFormat): Callable
  {
    return function ($dateString) use ($datetimetzFormat) {
      return CoreDateTime::createFromFormat($datetimetzFormat, $dateString);
    };
  }

  /**
   * Gets a transformation function which transforms an enum value into the corresponding name
   * @param string $enumClass the class name of the enum
   * @return Closure the function which transforms a value into the enum name
   */
  public static function enumNameTransformer(string $enumClass): Callable
  {
    return function ($value) use ($enumClass) {
      return call_user_func([$enumClass, "getName"], $value);
    };
  }

  /**
   * Gets a transformation function which transforms an enum name into the corresponding value
   * @param string $enumName the name of the enum
   * @return Closure the function which transforms a name into the enum value
   */
  public static function enumTransformer(string $enumName): Callable
  {
    return function ($name) use ($enumName) {
      return call_user_func([$enumName, "getValue"], $name);
    };
  }

  /**
   * @param array $mapping
   * @return Callable
   */
  public static function finiteMappingTransformation(array $mapping): Callable
  {
    return function ($x) use ($mapping) {
      if (array_key_exists($x, $mapping)) {
        return $mapping[$x];
      } else {
        throw new Exception("Unknown source value!");
      }
    };
  }
//</editor-fold desc="Public Methods">
}