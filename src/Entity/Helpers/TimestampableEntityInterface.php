<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:34 PM
 */

namespace Tfboe\FmLib\Entity\Helpers;


use DateTime;

/**
 * Interface TimestampableEntityInterface
 * @package Tfboe\FmLib\Entity\Helpers
 */
interface TimestampableEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return DateTime
   */
  public function getCreatedAt(): DateTime;

  /**
   * @return DateTime
   */
  public function getUpdatedAt(): DateTime;

  /**
   * @param DateTime $createdAt
   * @return $this|TimestampableEntity
   */
  public function setCreatedAt(DateTime $createdAt);

  /**
   * @param DateTime $updatedAt
   * @return $this|TimestampableEntity
   */
  public function setUpdatedAt(DateTime $updatedAt);
//</editor-fold desc="Public Methods">
}