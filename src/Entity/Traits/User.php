<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/15/17
 * Time: 10:48 PM
 */

namespace Tfboe\FmLib\Entity\Traits;


use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\ORM\Auth\Authenticatable;
use Tfboe\FmLib\Entity\Helpers\TimestampableEntity;
use Tfboe\FmLib\Entity\Helpers\UUIDEntity;


/**
 * Trait User
 * @package Tfboe\FmLib\Entity\Traits
 */
trait User
{
  use Authenticatable;
  use TimestampableEntity;
  use UUIDEntity;

//<editor-fold desc="Fields">

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $email;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $jwtVersion;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $confirmedAGBVersion;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return int
   */
  public function getConfirmedAGBVersion(): int
  {
    return $this->confirmedAGBVersion;
  }

  /**
   * @return string
   */
  public function getEmail(): string
  {
    return $this->email;
  }

  /**
   * Return a key value array, containing any custom claims to be added to the JWT.
   *
   * @return array
   */
  public function getJWTCustomClaims(): array
  {
    return [
      'ver' => $this->jwtVersion
    ];
  }

  /**
   * Get the identifier that will be stored in the subject claim of the JWT.
   *
   * @return string
   */
  public function getJWTIdentifier(): string
  {
    return $this->getId();
  }

  /**
   * @return int
   */
  public function getJwtVersion(): int
  {
    return $this->jwtVersion;
  }

  /**
   * @param mixed $confirmedAGBVersion
   */
  public function setConfirmedAGBVersion($confirmedAGBVersion)
  {
    $this->confirmedAGBVersion = $confirmedAGBVersion;
  }

  /**
   * @param mixed $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }

  /**
   * @param mixed $jwtVersion
   */
  public function setJwtVersion($jwtVersion)
  {
    $this->jwtVersion = $jwtVersion;
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Final Methods">
  /**
   * User init
   */
  protected final function init()
  {
    $this->jwtVersion = 1;
    $this->confirmedAGBVersion = 0;
  }
//</editor-fold desc="Protected Final Methods">
}