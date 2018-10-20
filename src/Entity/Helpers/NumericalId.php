<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 2/2/18
 * Time: 7:38 PM
 */

namespace Tfboe\FmLib\Entity\Helpers;


/**
 * Trait NumericalId
 * @package Tfboe\FmLib\Entity\Helpers
 */
trait NumericalId
{
//<editor-fold desc="Fields">
  /**
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer")
   * @var int
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
   * @return int
   */
  public function getId(): int
  {
    return $this->id;
  }
//</editor-fold desc="Public Methods">
}