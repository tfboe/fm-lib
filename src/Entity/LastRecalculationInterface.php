<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/28/18
 * Time: 2:41 PM
 */

namespace Tfboe\FmLib\Entity;

use Tfboe\FmLib\Entity\Helpers\BaseEntityInterface;
use Tfboe\FmLib\Entity\Helpers\TimeEntityInterface;

/**
 * Interface LastRecalculationInterface
 * @package Tfboe\FmLib\Entity
 */
interface LastRecalculationInterface extends BaseEntityInterface, TimeEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return int
   */
  public function getId(): int;

  /**
   * @return int
   */
  public function getVersion(): int;

  /**
   * @param int $version
   */
  public function setVersion(int $version);
//</editor-fold desc="Public Methods">
}