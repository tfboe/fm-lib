<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:27 PM
 */

namespace Tfboe\FmLib\Entity\CategoryTraits;

/**
 * Interface OrganizingModeInterface
 * @package Tfboe\FmLib\Entity\CategoryTraits
 */
interface OrganizingModeInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return int|null
   */
  public function getOrganizingMode(): ?int;

  /**
   * @param int|null $organizingMode
   * @return $this|OrganizingMode
   */
  public function setOrganizingMode(?int $organizingMode);
//</editor-fold desc="Public Methods">
}