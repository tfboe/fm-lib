<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/25/18
 * Time: 3:27 PM
 */

namespace Tfboe\FmLib\Tests\Helpers;

use Laravel\Lumen\Application;

/**
 * Trait ApplicationGetter
 * @package Tfboe\FmLib\Tests\Helpers
 */
trait ApplicationGetter
{
//<editor-fold desc="Public Methods">
  /**
   * Creates the application.
   *
   * @return Application
   */
  public function createApplication()
  {
    /** @noinspection PhpIncludeInspection */ //this trait is only used in projects which include this package
    return require __DIR__ . '/../../bootstrap/app.php';
  }
//</editor-fold desc="Public Methods">
}