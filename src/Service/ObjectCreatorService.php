<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 6:26 PM
 */

namespace Tfboe\FmLib\Service;

use Illuminate\Support\Facades\Config;

/**
 * Class ObjectCreatorService
 * @package Tfboe\FmLib\Service
 */
class ObjectCreatorService implements ObjectCreatorServiceInterface
{

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   * @param array|null config used config array, if null config('fm-lib') is used. Mainly used for testing purposes
   */
  public function createObjectFromInterface(string $interface, $args = [], $config = null)
  {
    $class = ($config == null ? Config::get('fm-lib') : $config)['entityMaps'][$interface];
    return new $class(...$args);
  }
//</editor-fold desc="Public Methods">

}