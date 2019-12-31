<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 12:33 AM
 */

namespace Tfboe\FmLib\Tests\Unit\Http\Controllers;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Application;
use PHPUnit\Framework\MockObject\MockObject;
use Tfboe\FmLib\Entity\TermsInterface;
use Tfboe\FmLib\Entity\UserInterface;
use Tfboe\FmLib\Exceptions\AuthenticationException;
use Tfboe\FmLib\Http\Controllers\BaseController;
use Tfboe\FmLib\Http\Controllers\UserController;
use Tfboe\FmLib\Service\ObjectCreatorServiceInterface;
use Tfboe\FmLib\Service\TermsServiceInterface;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Class BaseControllerTest
 * @package Tests\Unit\App\Http\Controllers
 */
class UserControllerTest extends UnitTestCase
{
  //tests also private method disable this tests as soon as all are used in public interfaces
//<editor-fold desc="Public Methods">

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\UserController::__construct
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   */
  public function testConstruct()
  {
    $controller = $this->controller();
    self::assertInstanceOf(BaseController::class, $controller);
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\UserController::login
   * @covers \Tfboe\FmLib\Http\Controllers\UserController::addAdditionalLoginSpecifications
   * @covers \Tfboe\FmLib\Http\Controllers\UserController::getCredentialSpecification
   * @covers \Tfboe\FmLib\Http\Controllers\UserController::preLogin
   * @throws AuthenticationException wrong credentials or errors during creating a token
   * @throws ValidationException
   * @uses   \Tfboe\FmLib\Http\Controllers\UserController::__construct
   * @uses   \Tfboe\FmLib\Exceptions\AuthenticationException::__construct
   * @uses   \Tfboe\FmLib\Helpers\SpecificationHandler::validateBySpecification
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::validateSpec
   */
  public function testFailToCreateToken()
  {
    $controller = $this->controller(null, null, ['validate']);
    /** @var Request|MockObject $request */
    $request = $this->createMock(Request::class);
    $data = ['email' => 'boing@bla.at', 'password' => '12345678'];
    $request->expects(static::any())->method('only')
      ->with('email', 'password')
      ->willReturn($data);

    $controller->method('validate')->willReturnCallback(function ($r, $spec) use ($request) {
      self::assertEquals(['email' => 'required|email', 'password' => 'required|string|min:8'], $spec);
      self::assertTrue($request === $r);
    });

    $app = $this->getStub(Application::class, []);

    Auth::shouldReceive('attempt')
      ->once()
      ->with($data)
      ->andThrow(new JWTException());


    $this->expectException(AuthenticationException::class);
    $this->expectExceptionMessage("could not create token");
    $controller->login($request, $app);
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\UserController::getLatestTerms
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses   \Tfboe\FmLib\Http\Controllers\UserController::__construct
   */
  public function testGetLatestTerms()
  {
    $controller = $this->controller();
    $terms = $this->getStub(TermsInterface::class,
      ['getText' => 'text', 'getMinorVersion' => 1, 'getMajorVersion' => 2]);
    $termsService = $this->getStub(TermsServiceInterface::class, ['getLatestTerms' => $terms]);
    $response = $controller->getLatestTerms($termsService);
    self::assertEquals(200, $response->getStatusCode());
    self::assertEquals(['text' => 'text', 'minorVersion' => 1, 'majorVersion' => 2], $response->getData(true));
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\UserController::login
   * @covers \Tfboe\FmLib\Http\Controllers\UserController::addAdditionalLoginSpecifications
   * @covers \Tfboe\FmLib\Http\Controllers\UserController::getCredentialSpecification
   * @covers \Tfboe\FmLib\Http\Controllers\UserController::preLogin
   * @throws AuthenticationException wrong credentials or errors during creating a token
   * @throws ValidationException
   * @uses   \Tfboe\FmLib\Http\Controllers\UserController::__construct
   * @uses   \Tfboe\FmLib\Exceptions\AuthenticationException::__construct
   * @uses   \Tfboe\FmLib\Helpers\SpecificationHandler::validateBySpecification
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::validateSpec
   */
  public function testLoginInvalidCredentials()
  {
    $controller = $this->controller(null, null, ['validate']);
    /** @var Request|MockObject $request */
    $request = $this->createMock(Request::class);
    $data = ['email' => 'boing@bla.at', 'password' => '12345678'];
    $request->expects(static::any())->method('only')
      ->with('email', 'password')
      ->willReturn($data);

    $controller->method('validate')->willReturnCallback(function ($r, $spec) use ($request) {
      self::assertEquals(['email' => 'required|email', 'password' => 'required|string|min:8'], $spec);
      self::assertTrue($request === $r);
    });

    $app = $this->getStub(Application::class, []);

    Auth::shouldReceive('attempt')
      ->once()
      ->with($data)
      ->andReturn(null);


    $this->expectException(AuthenticationException::class);
    $this->expectExceptionMessage("invalid credentials");
    $controller->login($request, $app);
  }

  /**
   * @covers   \Tfboe\FmLib\Http\Controllers\UserController::login
   * @covers   \Tfboe\FmLib\Http\Controllers\UserController::addAdditionalLoginSpecifications
   * @covers   \Tfboe\FmLib\Http\Controllers\UserController::getCredentialSpecification
   * @covers   \Tfboe\FmLib\Http\Controllers\UserController::getLoginResponse
   * @covers   \Tfboe\FmLib\Http\Controllers\UserController::preLogin
   * @throws AuthenticationException wrong credentials or errors during creating a token
   * @throws ValidationException
   * @uses     \Tfboe\FmLib\Helpers\SpecificationHandler::validateBySpecification
   * @uses     \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses     \Tfboe\FmLib\Http\Controllers\UserController::__construct
   * @uses     \Tfboe\FmLib\Http\Controllers\BaseController::validateSpec
   */
  public function testSuccessfulLogin()
  {
    $controller = $this->controller(null, null, ['validate']);
    /** @var Request|MockObject $request */
    $request = $this->createMock(Request::class);
    $data = ['email' => 'boing@bla.at', 'password' => '12345678'];
    $user = $this->createStubWithId(UserInterface::class, 'uid');
    $request->expects(static::any())->method('user')->willReturn($user);
    $request->expects(static::any())->method('only')
      ->with('email', 'password')
      ->willReturn($data);

    $controller->method('validate')->willReturnCallback(function ($r, $spec) use ($request) {
      self::assertEquals(['email' => 'required|email', 'password' => 'required|string|min:8'], $spec);
      self::assertTrue($request === $r);
    });

    $app = $this->getStub(Application::class, []);

    Auth::shouldReceive('attempt')
      ->once()
      ->with($data)
      ->andReturn('token');

    $response = $controller->login($request, $app);
    self::assertEquals(200, $response->getStatusCode());
    self::assertEquals(["id" => "uid"], $response->getData(true));
    self::assertEquals("token", $response->headers->get("jwt-token"));
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\UserController::register
   * @covers \Tfboe\FmLib\Http\Controllers\UserController::addAdditionalRegisterSpecifications
   * @covers \Tfboe\FmLib\Http\Controllers\UserController::createAdditionalRegisterEntities
   * @covers \Tfboe\FmLib\Http\Controllers\UserController::getCredentialSpecification
   * @covers \Tfboe\FmLib\Http\Controllers\UserController::getRegisterResponse
   * @covers \Tfboe\FmLib\Http\Controllers\UserController::newUser
   * @uses   \Tfboe\FmLib\Entity\Helpers\BaseEntity::methodExists
   * @uses   \Tfboe\FmLib\Entity\Traits\User::init
   * @uses   \Tfboe\FmLib\Helpers\SpecificationHandler::setFromSpecification
   * @uses   \Tfboe\FmLib\Helpers\SpecificationHandler::validateBySpecification
   * @uses   \Tfboe\FmLib\Helpers\Tools::setFromSpecification
   * @uses   \Tfboe\FmLib\Helpers\Tools::transformValue
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::getEntityManager
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::validateSpec
   * @uses   \Tfboe\FmLib\Http\Controllers\UserController::__construct
   * @throws ValidationException
   */
  public function testSuccessfulRegistration()
  {
    $data = ['email' => 'boing@bla.at', 'password' => '12345678', 'confirmedTermsMinorVersion' => 1,
      'confirmedTermsMajorVersion' => 2];

    $user = $this->getMockedEntity("User", ["getId", "setEmail", "setPassword", "setConfirmedTermsMinorVersion",
      "setConfirmedTermsMajorVersion"]);
    $user->method('getId')->willReturn('uid');
    $set = [];
    $getCb = function ($field) use (&$set) {
      return function ($val) use ($field, &$set) {
        $set[$field] = $val;
      };
    };
    $user->expects(self::atLeastOnce())->method('setEmail')->with($data['email'])->willReturnCallback($getCb('email'));
    $user->expects(self::atLeastOnce())->method('setPassword')->with('passwordHash')
      ->willReturnCallback($getCb('password'));
    $user->expects(self::atLeastOnce())->method('setConfirmedTermsMinorVersion')
      ->with($data['confirmedTermsMinorVersion'])->willReturnCallback($getCb('minor'));
    $user->expects(self::atLeastOnce())->method('setConfirmedTermsMajorVersion')
      ->with($data['confirmedTermsMajorVersion'])->willReturnCallback($getCb('major'));
    /** @var ObjectCreatorServiceInterface|MockObject $objectCreatorService */
    $objectCreatorService = $this->createMock(ObjectCreatorServiceInterface::class);
    $objectCreatorService->method('createObjectFromInterface')->with(UserInterface::class)
      ->willReturn($user);

    /** @var EntityManagerInterface|MockObject $em */
    $em = $this->createMock(EntityManagerInterface::class);
    $userPersisted = false;
    $em->expects(self::once())->method('persist')->with($user)->willReturnCallback(
      function () use (&$set, &$userPersisted) {
        self::assertCount(4, $set);
        $userPersisted = true;
      });
    $em->expects(self::atLeastOnce())->method('flush')->willReturnCallback(function () use (&$userPersisted) {
      self::assertTrue($userPersisted);
    });

    $controller = $this->controller($em, $objectCreatorService, ['validate']);
    /** @var Request|MockObject $request */
    $request = $this->createMock(Request::class);

    $request->expects(static::any())->method('user')->willReturn($user);
    $request->expects(static::any())->method('input')
      ->willReturn($data);

    $controller->method('validate')->willReturnCallback(function ($r, $spec) use ($request) {
      self::assertEquals([
        'email' => 'required|email|unique:UserClass,email',
        'password' => 'required|string|min:8',
        'confirmedTermsMinorVersion' => 'required|IntegerType|integer|min:0',
        'confirmedTermsMajorVersion' => 'required|IntegerType|integer|min:1'
      ], $spec);
      self::assertTrue($request === $r);
    });

    /** @var MockObject|Application $app */
    $app = $this->createMock(Application::class);
    $hasher = $this->createMock(Hasher::class);
    $hasher->method('make')->with($data['password'])->willReturn('passwordHash');
    $app->method('offsetGet')->with('hash')->willReturn($hasher);

    Config::shouldReceive('get')
      ->once()
      ->with('fm-lib')
      ->andReturn(['entityMaps' => [UserInterface::class => 'UserClass']]);

    $response = $controller->register($request, $app);
    self::assertEquals(200, $response->getStatusCode());
    self::assertEquals(["id" => "uid"], $response->getData(true));
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\UserController::userId
   * @uses   \Tfboe\FmLib\Http\Controllers\UserController::__construct
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   */
  public function testUserId()
  {
    $auth = $this->getStub(Authenticatable::class, ["getAuthIdentifier" => "uid"]);
    Auth::shouldReceive('user')
      ->once()
      ->andReturn($auth);
    $controller = $this->controller();
    $response = $controller->userId();
    self::assertEquals(200, $response->getStatusCode());
    self::assertEquals(["id" => "uid"], $response->getData(true));
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @param EntityManagerInterface|null $entityManager
   * @param ObjectCreatorServiceInterface|null $objectCreatorService
   * @param array $stubbedMethods
   * @return UserController|MockObject
   */
  private function controller(?EntityManagerInterface $entityManager = null,
                              ?ObjectCreatorServiceInterface $objectCreatorService = null,
                              array $stubbedMethods = []): MockObject
  {
    if ($entityManager === null) {
      $entityManager = $this->createMock(EntityManagerInterface::class);
    }
    if ($objectCreatorService === null) {
      $objectCreatorService = $this->createMock(ObjectCreatorServiceInterface::class);
    }
    return $this->getMockForAbstractClass(UserController::class, [$entityManager, $objectCreatorService], '', true,
      true, true, $stubbedMethods);
  }
//</editor-fold desc="Private Methods">
}