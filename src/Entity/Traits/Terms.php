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
 * Trait Terms
 * @package Tfboe\FmLib\Entity\Traits
 */
trait Terms
{
  use UUIDEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="text")
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

//<editor-fold desc="Public Methods">
  /**
   * @return int
   */
  public function getMajorVersion(): int
  {
    return $this->majorVersion;
  }

  /**
   * @return int
   */
  public function getMinorVersion(): int
  {
    return $this->minorVersion;
  }

  /**
   * @return string
   */
  public function getText(): string
  {
    return $this->text;
  }

  /**
   * @param int $majorVersion
   */
  public function setMajorVersion(int $majorVersion): void
  {
    $this->majorVersion = $majorVersion;
  }

  /**
   * @param int $minorVersion
   */
  public function setMinorVersion(int $minorVersion): void
  {
    $this->minorVersion = $minorVersion;
  }

  /**
   * @param string $text
   */
  public function setText(string $text): void
  {
    $this->text = $text;
  }
//</editor-fold desc="Public Methods">
}