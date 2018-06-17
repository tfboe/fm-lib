<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 2/4/18
 * Time: 4:33 PM
 */

namespace Tfboe\FmLib\Entity\Helpers;


/**
 * Interface IdAble
 * @package App\Entity\Helpers
 */
interface IdAble
{
//<editor-fold desc="Public Methods">
  /**
   * Gets an id for this entity
   * @return string|int
   */
  public function getEntityId();
//</editor-fold desc="Public Methods">
}