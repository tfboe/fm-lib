<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 8/30/18
 * Time: 7:03 PM
 */

namespace Tfboe\FmLib\Entity\Traits;

use Tfboe\FmLib\Entity\Helpers\UUIDEntity;

/**
 * Trait AGB
 * @package Tfboe\FmLib\Entity\Traits
 */
trait AGB
{
  use UUIDEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="blob")
   * @var string
   */
  private $text;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $minorVersion;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $majorVersion;
//</editor-fold desc="Fields">
}