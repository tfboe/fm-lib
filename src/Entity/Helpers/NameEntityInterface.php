<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:30 PM
 */

namespace Tfboe\FmLib\Entity\Helpers;


/**
 * Interface NameEntityInterface
 * @package Tfboe\FmLib\Entity\Helpers
 */
interface NameEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return string
   */
  public function getName(): string;

  /**
   * @param string $name
   * @return $this|NameEntity
   */
  public function setName(string $name);
//</editor-fold desc="Public Methods">
}