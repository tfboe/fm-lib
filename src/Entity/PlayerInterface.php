<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:44 PM
 */

namespace Tfboe\FmLib\Entity;

use Tfboe\FmLib\Entity\Helpers\BaseEntityInterface;
use Tfboe\FmLib\Entity\Helpers\TimestampableEntityInterface;


/**
 * Interface PlayerInterface
 * @package Tfboe\FmLib\Entity
 */
interface PlayerInterface extends BaseEntityInterface, TimestampableEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return \DateTime
   */
  public function getBirthday(): \DateTime;

  /**
   * @return string
   */
  public function getFirstName(): string;

  /**
   * @return string
   */
  public function getLastName(): string;

  /**
   * @return int|string
   */
  public function getId();

  /**
   * @param \DateTime $birthday
   */
  public function setBirthday(\DateTime $birthday);

  /**
   * @param string $firstName
   */
  public function setFirstName(string $firstName);

  /**
   * @param string $lastName
   */
  public function setLastName(string $lastName);
//</editor-fold desc="Public Methods">
}