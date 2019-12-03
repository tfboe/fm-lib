<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/5/18
 * Time: 11:16 PM
 */

namespace Tfboe\FmLib\Helpers;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class Logging
 * @package Tfboe\FmLib\Helpers
 */
abstract class Logging
{
//<editor-fold desc="Fields">
  /** @var bool */
  public static $testing = false;
  public static $storagePathFunction = 'storage_path';
  /** @var Logger[] */
  private static $loggers = [];
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">

  /**
   * Logs the given message in the given logger
   * @param string $message the message to log
   * @param string $logger the logger to use
   * @param int $type the type of the message
   * @noinspection PhpDocMissingThrowsInspection
   */
  public static function log(string $message, string $logger = Logs::GENERAL, int $type = Logger::INFO): void
  {
    if (self::$testing && $logger !== Logs::TESTING) {
      //do nothing
      return;
    }
    Logs::ensureValidValue($logger);
    if (!array_key_exists($logger, self::$loggers)) {
      self::$loggers[$logger] = new Logger($logger);
      // InvalidArgumentException => stream is a string
      // Exception => path is static and it is ensured that it is valid

      $path = (self::$storagePathFunction)() . '/logs/' . $logger . '.log';
      /** @noinspection PhpUnhandledExceptionInspection */ //$path is always a string
      self::$loggers[$logger]->pushHandler(
        new StreamHandler($path));
    }
    self::$loggers[$logger]->log($type, $message);
  }
//</editor-fold desc="Public Methods">
}