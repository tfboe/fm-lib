<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/15/17
 * Time: 10:48 PM
 */

namespace Tfboe\FmLib\Entity;


use Doctrine\ORM\Mapping as ORM;
use Illuminate\Contracts\Auth\Authenticatable;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\Helpers\TimestampableEntity;
use Tfboe\FmLib\Entity\Helpers\UUIDEntity;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseEntity implements Authenticatable, JWTSubject
{
  use \LaravelDoctrine\ORM\Auth\Authenticatable;
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
  /**
   * User constructor.
   */
  public function __construct()
  {
    $this->jwtVersion = 1;
    $this->confirmedAGBVersion = 0;
  }
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
   * @return $this|User
   */
  public function setConfirmedAGBVersion($confirmedAGBVersion): User
  {
    $this->confirmedAGBVersion = $confirmedAGBVersion;
    return $this;
  }

  /**
   * @param mixed $email
   * @return $this|User
   */
  public function setEmail($email): User
  {
    $this->email = $email;
    return $this;
  }

  /**
   * @param mixed $jwtVersion
   * @return $this|User
   */
  public function setJwtVersion($jwtVersion): User
  {
    $this->jwtVersion = $jwtVersion;
    return $this;
  }
//</editor-fold desc="Public Methods">
}