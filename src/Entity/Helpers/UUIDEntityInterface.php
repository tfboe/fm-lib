<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:35 PM
 */

namespace Tfboe\FmLib\Entity\Helpers;


/**
 * Interface UUIDEntityInterface
 * @package Tfboe\FmLib\Entity\Helpers
 */
interface UUIDEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return string
   */
  public function getId(): string;

  /**
   * @return bool
   */
  public function hasId(): bool;

  /**
   * @param string $id
   */
  public function setId(string $id);
//</editor-fold desc="Public Methods">
}