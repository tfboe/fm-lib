<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 12:33 AM
 */

namespace Tfboe\FmLib\Tests\Unit\Http\Middleware;

use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\Request;
use PHPUnit\Framework\MockObject\MockObject;
use Tfboe\FmLib\Entity\UserInterface;
use Tfboe\FmLib\Exceptions\AuthenticationException;
use Tfboe\FmLib\Http\Middleware\Authenticate;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\JWTGuard;
use Tymon\JWTAuth\Payload;

/**
 * Class BaseControllerTest
 * @package Tests\Unit\App\Http\Controllers
 */
class AuthenticateTest extends UnitTestCase
{
  //tests also private method disable this tests as soon as all are used in public interfaces
//<editor-fold desc="Public Methods">

  /**
   * @covers \Tfboe\FmLib\Http\Middleware\Authenticate::__construct
   */
  public function testConstruct()
  {
    $authenticate = $this->authenticate();
    self::assertInstanceOf(Authenticate::class, $authenticate);
  }

  /**
   * @covers \Tfboe\FmLib\Http\Middleware\Authenticate::handle
   * @throws AuthenticationException
   * @uses   \Tfboe\FmLib\Exceptions\AuthenticationException::__construct
   * @uses   \Tfboe\FmLib\Http\Middleware\Authenticate::__construct
   */
  public function testHandleGuest()
  {
    $this->expectException(AuthenticationException::class);
    $this->expectExceptionMessage('Not logged in!');

    $auth = $this->getAuth(true, true, UserInterface::class, 6);
    $authenticate = $this->authenticate($auth);
    $authenticate->handle(null, function () {
    }, "guardName");
  }

  /**
   * @covers \Tfboe\FmLib\Http\Middleware\Authenticate::handle
   * @throws AuthenticationException
   * @uses   \Tfboe\FmLib\Exceptions\AuthenticationException::__construct
   * @uses   \Tfboe\FmLib\Http\Middleware\Authenticate::__construct
   */
  public function testHandleNoVer()
  {
    $this->expectException(AuthenticationException::class);
    $this->expectExceptionMessage('Payload version expired!');

    $auth = $this->getAuth(false, false, UserInterface::class, 6);
    $authenticate = $this->authenticate($auth);
    $authenticate->handle(null, function () {
    }, "guardName");
  }

  /**
   * @covers \Tfboe\FmLib\Http\Middleware\Authenticate::handle
   * @throws AuthenticationException
   * @uses   \Tfboe\FmLib\Exceptions\AuthenticationException::__construct
   * @uses   \Tfboe\FmLib\Http\Middleware\Authenticate::__construct
   */
  public function testHandleOldVersion()
  {
    $this->expectException(AuthenticationException::class);
    $this->expectExceptionMessage('Payload version expired!');

    $auth = $this->getAuth(false, true, UserInterface::class, 8);
    $authenticate = $this->authenticate($auth);
    $authenticate->handle(null, function () {
    }, "guardName");
  }

  /**
   * @covers \Tfboe\FmLib\Http\Middleware\Authenticate::handle
   * @throws AuthenticationException
   * @uses   \Tfboe\FmLib\Http\Middleware\Authenticate::__construct
   */
  public function testHandleSuccess()
  {
    $auth = $this->getAuth(false, true, UserInterface::class, 6);
    /** @var Request $request */
    $request = $this->createMock(Request::class);

    $called = false;
    $next = function ($req) use (&$called, $request) {
      self::assertTrue($request === $req);
      self::assertFalse($called);
      $called = true;
    };

    $authenticate = $this->authenticate($auth);
    self::assertFalse($called);
    $authenticate->handle($request, $next, "guardName");
    self::assertTrue($called);
  }

  /**
   * @covers \Tfboe\FmLib\Http\Middleware\Authenticate::handle
   * @throws AuthenticationException
   * @uses   \Tfboe\FmLib\Exceptions\AuthenticationException::__construct
   * @uses   \Tfboe\FmLib\Http\Middleware\Authenticate::__construct
   */
  public function testHandleWrongUser()
  {
    $this->expectException(AuthenticationException::class);
    $this->expectExceptionMessage('Payload version expired!');

    $auth = $this->getAuth(false, true, JWTSubject::class, 6);
    $authenticate = $this->authenticate($auth);
    $authenticate->handle(null, function () {
    }, "guardName");
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @param Factory|null $auth
   * @param array $stubbedMethods
   * @return Authenticate|MockObject
   */
  private function authenticate(?Factory $auth = null,
                                array $stubbedMethods = []): MockObject
  {
    if ($auth === null) {
      $auth = $this->createMock(Factory::class);
    }
    return $this->getMockForAbstractClass(Authenticate::class, [$auth], '', true,
      true, true, $stubbedMethods);
  }

  /**
   * @param bool $guest
   * @param bool $hasVer
   * @param string $userClass
   * @param int $userConfirmedVersion
   * @return MockObject|Factory
   */
  private function getAuth(bool $guest, bool $hasVer, string $userClass, int $userConfirmedVersion): MockObject
  {
    $guard = $this->createMock(JWTGuard::class);
    $auth = $this->createMock(Factory::class);
    $payload = $this->createMock(Payload::class);
    $user = $this->createMock($userClass);


    $auth->expects(self::once())->method("guard")->with("guardName")->willReturn($guard);

    $guard->method('guest')->willReturn($guest);
    $guard->method('getPayload')->willReturn($payload);
    $guard->method('getUser')->willReturn($user);

    $payload->method('hasKey')->with('ver')->willReturn($hasVer);
    $payload->method('get')->with(['ver'])->willReturn([7]);

    if ($userClass === UserInterface::class) {
      $user->method('getJwtVersion')->willReturn($userConfirmedVersion);
    }

    return $auth;
  }
//</editor-fold desc="Private Methods">
}