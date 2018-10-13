<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 10:57 AM
 */

namespace Tfboe\FmLib\Entity\Helpers;

/**
 * Trait UUIDEntity
 * @package Tfboe\FmLib\Entity\Helpers
 */
trait UUIDEntity
{
//<editor-fold desc="Fields">
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="CUSTOM")
   * @ORM\CustomIdGenerator(class="Tfboe\FmLib\Entity\Helpers\IdGenerator")
   * @ORM\Column(type="guid")
   * @var string
   */
  private $id;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return string|int
   */
  public function getEntityId()
  {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getId(): string
  {
    return $this->id;
  }
//</editor-fold desc="Public Methods">
}