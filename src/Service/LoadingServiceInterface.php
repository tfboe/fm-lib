<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/1/17
 * Time: 7:37 PM
 */

namespace Tfboe\FmLib\Service;


use Tfboe\FmLib\Entity\Helpers\IdAble;


/**
 * Interface LoadingServiceInterface
 * @package App\Service
 */
interface LoadingServiceInterface
{
//<editor-fold desc="Public Methods">
  /**
   * Loads a list of entities completely from the database with reasonably few queries. The list of given entities must
   * already be loaded.
   * @param IdAble[] $entities the entities to load
   * @param array $propertyMap an optional map which maps a class to all properties which needed to be loaded for this
   *                           class. If null is given a preconfigured default property map is used.
   */
  public function loadEntities(array $entities, ?array $propertyMap = null);
//</editor-fold desc="Public Methods">
}