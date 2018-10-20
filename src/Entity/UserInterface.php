<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:54 PM
 */

namespace Tfboe\FmLib\Entity;

use Illuminate\Contracts\Auth\Authenticatable;
use Tfboe\FmLib\Entity\Helpers\BaseEntityInterface;
use Tfboe\FmLib\Entity\Helpers\TimestampableEntityInterface;
use Tfboe\FmLib\Entity\Helpers\UUIDEntityInterface;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Interface UserInterface
 * @package Tfboe\FmLib\Entity
 */
interface UserInterface extends BaseEntityInterface, TimestampableEntityInterface, UUIDEntityInterface, Authenticatable,
  JWTSubject
{
//<editor-fold desc="Public Methods">
  /**
   * @return int
   */
  public function getConfirmedTermsMajorVersion(): int;

  /**
   * @return int
   */
  public function getConfirmedTermsMinorVersion(): int;

  /**
   * @return string
   */
  public function getEmail(): string;

  /**
   * Return a key value array, containing any custom claims to be added to the JWT.
   *
   * @return array
   */
  public function getJWTCustomClaims(): array;

  /**
   * Get the identifier that will be stored in the subject claim of the JWT.
   *
   * @return string
   */
  public function getJWTIdentifier(): string;

  /**
   * @return int
   */
  public function getJwtVersion(): int;

  /**
   * @param int $confirmedTermsMajorVersion
   */
  public function setConfirmedTermsMajorVersion(int $confirmedTermsMajorVersion);

  /**
   * @param int $confirmedTermsMinorVersion
   */
  public function setConfirmedTermsMinorVersion(int $confirmedTermsMinorVersion);

  /**
   * @param mixed $email
   */
  public function setEmail($email);

  /**
   * @param mixed $jwtVersion
   */
  public function setJwtVersion($jwtVersion);
//</editor-fold desc="Public Methods">
}