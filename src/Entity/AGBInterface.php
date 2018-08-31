<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/29/18
 * Time: 11:34 AM
 */

namespace Tfboe\FmLib\Entity;

use Tfboe\FmLib\Entity\Helpers\BaseEntityInterface;
use Tfboe\FmLib\Entity\Helpers\UUIDEntityInterface;


/**
 * Interface GameInterface
 * @package Tfboe\FmLib\Entity
 */
interface AGBInterface extends BaseEntityInterface, UUIDEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return int
   */
  public function getMajorVersion(): int;

  /**
   * @return int
   */
  public function getMinorVersion(): int;

  /**
   * @return string
   */
  public function getText(): string;

  /**
   * @param int $majorVersion
   */
  public function setMajorVersion(int $majorVersion): void;

  /**
   * @param int $minorVersion
   */
  public function setMinorVersion(int $minorVersion): void;

  /**
   * @param string $text
   */
  public function setText(string $text): void;
//</editor-fold desc="Public Methods">
}