<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/28/18
 * Time: 2:41 PM
 */

namespace Tfboe\FmLib\Entity\Traits;


use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\NumericalId;
use Tfboe\FmLib\Entity\Helpers\TimeEntity;

/**
 * Trait LastRecalculation
 * @package Tfboe\FmLib\Entity\Traits
 */
trait LastRecalculation
{
  use NumericalId;
  use TimeEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="integer", nullable=false)
   * @var int
   */
  private $version;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return int
   */
  public function getVersion(): int
  {
    return $this->version;
  }

  /**
   * @param int $version
   */
  public function setVersion(int $version)
  {
    $this->version = $version;
  }
//</editor-fold desc="Public Methods">
}