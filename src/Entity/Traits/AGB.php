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

  /**
   * @return string
   */
  public function getText(): string
  {
    return $this->text;
  }

  /**
   * @param string $text
   */
  public function setText(string $text): void
  {
    $this->text = $text;
  }

  /**
   * @return int
   */
  public function getMinorVersion(): int
  {
    return $this->minorVersion;
  }

  /**
   * @param int $minorVersion
   */
  public function setMinorVersion(int $minorVersion): void
  {
    $this->minorVersion = $minorVersion;
  }

  /**
   * @return int
   */
  public function getMajorVersion(): int
  {
    return $this->majorVersion;
  }

  /**
   * @param int $majorVersion
   */
  public function setMajorVersion(int $majorVersion): void
  {
    $this->majorVersion = $majorVersion;
  }
//</editor-fold desc="Fields">
}