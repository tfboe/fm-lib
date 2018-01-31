<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 6:26 PM
 */

namespace Tfboe\FmLib\Service;

/**
 * Class ObjectCreatorService
 * @package Tfboe\FmLib\Service
 */
class ObjectCreatorService implements ObjectCreatorServiceInterface
{

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function createObjectFromInterface(string $interface, $args = [])
  {
    $class = config('fm-lib')['entityMaps'][$interface];
    return new $class(...$args);
  }
//</editor-fold desc="Public Methods">

}