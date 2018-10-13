<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/12/18
 * Time: 9:53 PM
 */

namespace Tfboe\FmLib\Entity\Helpers;


use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Trait BaseTrait
 * @package Tfboe\FmLib\Entity\Helpers
 */
trait BaseTrait
{
//<editor-fold desc="Protected Methods">
  /**
   * @param Collection $collection
   * @return bool
   */
  protected function isInitialized(Collection $collection): bool
  {
    if ($collection instanceof AbstractLazyCollection) {
      /** @var $collection AbstractLazyCollection */
      return $collection->isInitialized();
    } else {
      return true;
    }
  }
//</editor-fold desc="Protected Methods">
}