<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:28 PM
 */

namespace Tfboe\FmLib\Entity\CategoryTraits;

/**
 * Interface TableInterface
 * @package Tfboe\FmLib\Entity\CategoryTraits
 */
interface TableInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return int|null
   */
  public function getTable(): ?int;

  /**
   * @param int|null $table
   * @return $this|Table
   */
  public function setTable(?int $table);
//</editor-fold desc="Public Methods">
}