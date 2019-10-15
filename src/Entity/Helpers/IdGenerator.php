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
   * creates a new id
   * @param int|null $mixBy
   * @return string the new id
   */
  public static function createIdFrom(?int $mixBy = null)
  {
    $v10 = mt_rand(0, 1);
    if ($mixBy !== null && $mixBy < 0) {
      $v10 = $v10 ^ 1;
      $mixBy = -($mixBy + 1);
    }
    $v11 = mt_rand(0, 0x7FFF);
    if ($mixBy !== null) {
      $v11 = $v11 ^ ($mixBy & 0x7FFF);
      $mixBy = $mixBy >> 15;
    }
    $vs = [];
    $vs[] = ($v10 << 15) | $v11;
    for ($i = 1; $i < 8; $i++) {
      $binDigits = 16;
      if ($i === 3) {
        $binDigits = 12; //the first 4 bytes are fixed;
      }
      if ($i === 4) {
        $binDigits = 14; //the first 2 bytes are fixed
      }
      $max = (1 << $binDigits) - 1;
      $vs[$i] = mt_rand(0, $max);
      if ($mixBy !== null && $mixBy > 0) {
        $vs[$i] = $vs[$i] ^ ($mixBy & $max);
        $mixBy = $mixBy >> $binDigits;
      }
    }

    return strtolower(vsprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', $vs));
  }

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
    $mixBy = Random::stringToInt(get_class($entity));
    if (is_subclass_of($entity, IdentifiableInterface::class)) {
      /** @var IdentifiableInterface $entity */
      $mixBy = $mixBy ^ $entity->getIdentifiableId();
    }
    return self::createIdFrom($mixBy);
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