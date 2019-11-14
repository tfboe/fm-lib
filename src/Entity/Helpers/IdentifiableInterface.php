<?php
declare(strict_types=1);


namespace Tfboe\FmLib\Entity\Helpers;


/**
 * Interface IdentifiableInterface
 * @package Tfboe\FmLib\Entity\Helpers
 */
interface IdentifiableInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return string
   */
  public function getIdentifiableId(): string;

  /**
   * @return bool
   */
  public function isUnique(): bool;
//</editor-fold desc="Public Methods">
}