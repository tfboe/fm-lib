<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/24/18
 * Time: 2:55 PM
 */

namespace Tfboe\FmLib\Providers;

use Laravel\Lumen\Application;

/**
 * Class FmLibServiceProvider
 * @package Tfboe\FmLib\Providers
 */
abstract class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
//<editor-fold desc="Fields">
  protected $singletons = [];

  /**
   * The application instance.
   *
   * @var Application
   */
  protected $app;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /** @noinspection PhpMissingParentCallCommonInspection */
  /**
   * @inheritDoc
   */
  public function register()
  {
    foreach ($this->singletons as $key => $singleton) {
      if (is_int($key)) {
        if (substr($singleton, strlen($singleton) - strlen("Interface")) === "Interface") {
          $this->app->singleton($singleton, substr($singleton, 0, strlen($singleton) - strlen("Interface")));
        } else {
          $this->app->singleton($singleton);
        }
      } else {
        $this->app->singleton($key, $singleton);
      }
    }
  }
//</editor-fold desc="Public Methods">
}