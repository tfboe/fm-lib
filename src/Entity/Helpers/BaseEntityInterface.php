<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:30 PM
 */

namespace Tfboe\FmLib\Entity\Helpers;


/**
 * Interface BaseEntityInterface
 * @package Tfboe\FmLib\Entity\Helpers
 */
interface BaseEntityInterface extends IdAble
{
//<editor-fold desc="Public Methods">
  /**
   * Checks if the given method exists
   * @param string $method the method to search
   * @return bool true if it exists and false otherwise
   */
  public function methodExists(string $method): bool;
//</editor-fold desc="Public Methods">
}