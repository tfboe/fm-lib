<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/31/18
 * Time: 1:22 PM
 */

namespace Tfboe\FmLib\Exceptions;

/**
 * Class Internal
 * @package App\Exceptions
 */
class Internal
{
//<editor-fold desc="Public Methods">
  /**
   * @param bool $result
   */
  public static function assert(bool $result)
  {
    if (!$result) {
      self::error("Assertion failed!");
    }
  }

  /**
   * Reports an internal error
   * @param $message
   * @return mixed;
   */
  public static function error($message)
  {
    trigger_error($message, E_USER_ERROR);
    return null;
  }
//</editor-fold desc="Public Methods">
}