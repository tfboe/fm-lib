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
 * Interface ObjectCreatorServiceInterface
 * @package Tfboe\FmLib\Service
 */
interface ObjectCreatorServiceInterface
{
//<editor-fold desc="Public Methods">
  /**
   * Creates an object of the given interface with the given constructor arguments. The interface must be registered
   * in the fm-lib config.
   * @param string $interface the full namespace path to the interface
   * @param array $args the constructor arguments
   * @return mixed the new instance
   */
  public function createObjectFromInterface(string $interface, $args = []);
//</editor-fold desc="Public Methods">
}