<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 10:27 AM
 */

namespace Tfboe\FmLib\Entity\Traits;


use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\TimestampableEntity;
use Tfboe\FmLib\Helpers\DateTime;


/**
 * Trait Player
 * @package Tfboe\FmLib\Entity
 */
trait Player
{
  use TimestampableEntity;

//<editor-fold desc="Fields">

  /**
   * @ORM\Column(type="string", nullable=false)
   * @var string
   */
  private $firstName;

  /**
   * @ORM\Column(type="string", nullable=false)
   * @var string
   */
  private $lastName;

  /**
   * @ORM\Column(type="date", nullable=true)
   * @var \DateTime
   */
  private $birthday;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return \DateTime
   */
  public function getBirthday(): \DateTime
  {
    return $this->birthday;
  }

  /**
   * @return string
   */
  public function getFirstName(): string
  {
    return $this->firstName;
  }

  /**
   * @return string
   */
  public function getLastName(): string
  {
    return $this->lastName;
  }

  /**
   * @param \DateTime $birthday
   */
  public function setBirthday(\DateTime $birthday)
  {
    if (!DateTime::eq($this->birthday, $birthday)) {
      $this->birthday = $birthday;
    }
  }

  /**
   * @param string $firstName
   */
  public function setFirstName(string $firstName)
  {
    $this->firstName = $firstName;
  }

  /**
   * @param string $lastName
   */
  public function setLastName(string $lastName)
  {
    $this->lastName = $lastName;
  }
//</editor-fold desc="Public Methods">
}