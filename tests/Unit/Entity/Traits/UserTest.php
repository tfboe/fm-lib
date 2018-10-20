<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity\Traits;

use Tfboe\FmLib\Entity\UserInterface;
use Tfboe\FmLib\Tests\Entity\User;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class UserTest
 * @package Tfboe\FmLib\Tests\Unit\Entity
 */
class UserTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Traits\User::init
   * @uses   \Tfboe\FmLib\Entity\Traits\User::getJWTCustomClaims
   * @uses   \Tfboe\FmLib\Entity\Traits\User::getJwtVersion
   * @uses   \Tfboe\FmLib\Entity\Traits\User::getConfirmedTermsMinorVersion
   * @uses   \Tfboe\FmLib\Entity\Traits\User::getConfirmedTermsMajorVersion
   */
  public function testConstructor()
  {
    $user = $this->user();
    self::assertInstanceOf(User::class, $user);
    self::assertEquals(['ver' => 1], $user->getJWTCustomClaims());
    self::assertEquals(1, $user->getJwtVersion());
    self::assertEquals(0, $user->getConfirmedTermsMajorVersion());
    self::assertEquals(0, $user->getConfirmedTermsMinorVersion());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\User::setEmail
   * @covers \Tfboe\FmLib\Entity\Traits\User::getEmail
   * @uses   \Tfboe\FmLib\Entity\Traits\User::init
   */
  public function testEmail()
  {
    $user = $this->user();
    $user->setEmail("test@a1.net");
    self::assertEquals("test@a1.net", $user->getEmail());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\User::getJWTCustomClaims
   * @uses   \Tfboe\FmLib\Entity\Traits\User::setJwtVersion
   * @uses   \Tfboe\FmLib\Entity\Traits\User::init
   */
  public function testJWTCustomClaims()
  {
    $user = $this->user();
    $user->setJwtVersion(5);
    self::assertEquals(['ver' => 5], $user->getJWTCustomClaims());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\User::getJWTIdentifier
   * @uses   \Tfboe\FmLib\Entity\Traits\User::init
   * @uses   \Tfboe\FmLib\Entity\Helpers\UUIDEntity::getId
   */
  public function testJWTIdentifier()
  {
    $user = $this->user();
    /** @noinspection PhpUnhandledExceptionInspection */
    self::getProperty(get_class($user), 'id')->setValue($user, 'user-id');
    self::assertEquals('user-id', $user->getJWTIdentifier());
    self::assertEquals($user->getId(), $user->getJWTIdentifier());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\User::getJwtVersion()
   * @covers \Tfboe\FmLib\Entity\Traits\User::setJwtVersion
   * @uses   \Tfboe\FmLib\Entity\Traits\User::init
   */
  public function testJwtVersion()
  {
    $user = $this->user();
    $user->setJwtVersion(5);
    self::assertEquals(5, $user->getJwtVersion());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\User::getConfirmedTermsMajorVersion
   * @covers \Tfboe\FmLib\Entity\Traits\User::setConfirmedTermsMajorVersion
   * @uses   \Tfboe\FmLib\Entity\Traits\User::init
   */
  public function testLastConfirmedTermsMajorVersion()
  {
    $user = $this->user();
    $user->setConfirmedTermsMajorVersion(5);
    self::assertEquals(5, $user->getConfirmedTermsMajorVersion());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\User::getConfirmedTermsMinorVersion
   * @covers \Tfboe\FmLib\Entity\Traits\User::setConfirmedTermsMinorVersion
   * @uses   \Tfboe\FmLib\Entity\Traits\User::init
   */
  public function testLastConfirmedTermsMinorVersion()
  {
    $user = $this->user();
    $user->setConfirmedTermsMinorVersion(5);
    self::assertEquals(5, $user->getConfirmedTermsMinorVersion());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return UserInterface a new user
   */
  private function user(): UserInterface
  {
    return new User();
  }
//</editor-fold desc="Private Methods">
}