<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/6/16
 * Time: 11:18 AM
 */

namespace Tfboe\FmLib\Entity\Helpers;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Tfboe\FmLib\Helpers\Random;

/**
 * Class IdGenerator. Generator for unique ids.
 * @package Tfboe\FmLib\Entity
 */
class IdGenerator extends AbstractIdGenerator
{
//<editor-fold desc="Public Methods">
  /**
   * @param $entity
   * @return string
   */
  public static function createIdFor($entity): string
  {
    /** @var UUIDEntityInterface $entity */
    if (is_subclass_of($entity, UUIDEntityInterface::class) && $entity->hasId()) {
      return $entity->getId();
    }
    $mixByString = get_class($entity);
    $useRandomness = true;
    if (is_subclass_of($entity, IdentifiableInterface::class)) {
      /** @var IdentifiableInterface $entity */
      $mixByString .= "|" . $entity->getIdentifiableId();
      $useRandomness = !$entity->isUnique();
    }
    $mixBy = Random::stringToRandom($mixByString);
    return self::createIdFrom($mixBy, $useRandomness);
  }

  /**
   * creates a new id
   * @param Random|null $mixBy
   * @param bool $useRandomness
   * @return string the new id
   */
  public static function createIdFrom(?Random $mixBy = null, bool $useRandomness = true)
  {
    $vs = [];
    for ($i = 0; $i < 8; $i++) {
      $binDigits = 16;
      if ($i === 3) {
        $binDigits = 12; //the first 4 bits are fixed;
      }
      if ($i === 4) {
        $binDigits = 14; //the first 2 bits are fixed
      }
      $max = (1 << $binDigits) - 1;
      $vs[$i] = $useRandomness ? mt_rand(0, $max) : 0;
      if ($mixBy !== null) {
        $vs[$i] = $vs[$i] ^ $mixBy->extractEntropyByBits($binDigits);
      }
    }

    return strtolower(vsprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', $vs));
  }

  /**
   * Generates an identifier for an entity.
   *
   * @param EntityManager $entityManager
   * @param Entity $entity
   * @return string
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function generate(EntityManager $entityManager, $entity): string
  {
    return self::createIdFor($entity);
  }
//</editor-fold desc="Public Methods">

}