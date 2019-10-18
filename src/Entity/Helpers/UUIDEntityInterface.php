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
  function getId(): string;

  /**
   * @return bool
   */
  function hasId(): bool;
//</editor-fold desc="Public Methods">
}