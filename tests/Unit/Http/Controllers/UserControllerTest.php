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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Application;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;
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
   * @throws ReflectionException
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
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Http\Controllers\UserController::__construct
   * @uses   \Tfboe\FmLib\Exceptions\AuthenticationException::__construct
   * @uses   \Tfboe\FmLib\Helpers\SpecificationHandler::validateBySpecification
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   */
  public function testFailToCreateToken()
  {
    $controller = $this->controller(null, null, ['validateSpec']);
    $controller->method('validateSpec');
    /** @var Request|MockObject $request */
    $request = $this->createMock(Request::class);
    $data = ['email' => 'boing@bla.at', 'password' => '12345678'];
    $request->expects(static::any())->method('only')
      ->with('email', 'password')
      ->willReturn($data);

    $app = $this->createStub(Application::class, []);

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
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses   \Tfboe\FmLib\Http\Controllers\UserController::__construct
   */
  public function testGetLatestTerms()
  {
    $controller = $this->controller();
    $terms = $this->createStub(TermsInterface::class,
      ['getText' => 'text', 'getMinorVersion' => 1, 'getMajorVersion' => 2]);
    $termsService = $this->createStub(TermsServiceInterface::class, ['getLatestTerms' => $terms]);
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
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Http\Controllers\UserController::__construct
   * @uses   \Tfboe\FmLib\Exceptions\AuthenticationException::__construct
   * @uses   \Tfboe\FmLib\Helpers\SpecificationHandler::validateBySpecification
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   */
  public function testLoginInvalidCredentials()
  {
    $controller = $this->controller(null, null, ['validateSpec']);
    $controller->method('validateSpec');
    /** @var Request|MockObject $request */
    $request = $this->createMock(Request::class);
    $data = ['email' => 'boing@bla.at', 'password' => '12345678'];
    $request->expects(static::any())->method('only')
      ->with('email', 'password')
      ->willReturn($data);

    $app = $this->createStub(Application::class, []);

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
   * @throws ReflectionException
   * @uses     \Tfboe\FmLib\Helpers\SpecificationHandler::validateBySpecification
   * @uses     \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses     \Tfboe\FmLib\Http\Controllers\UserController::__construct
   */
  public function testSuccessfulLogin()
  {
    $controller = $this->controller(null, null, ['validateSpec']);
    $controller->method('validateSpec');
    /** @var Request|MockObject $request */
    $request = $this->createMock(Request::class);
    $data = ['email' => 'boing@bla.at', 'password' => '12345678'];
    $user = $this->createStubWithId(UserInterface::class, 'uid');
    $request->expects(static::any())->method('user')->willReturn($user);
    $request->expects(static::any())->method('only')
      ->with('email', 'password')
      ->willReturn($data);

    $app = $this->createStub(Application::class, []);

    Auth::shouldReceive('attempt')
      ->once()
      ->with($data)
      ->andReturn('token');

    $response = $controller->login($request, $app);
    self::assertEquals(200, $response->getStatusCode());
    self::assertEquals(["id" => "uid"], $response->getData(true));
    self::assertEquals("token", $response->headers->get("jwt-token"));
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @param EntityManagerInterface|null $entityManager
   * @param ObjectCreatorServiceInterface|null $objectCreatorService
   * @param array $stubbedMethods
   * @return UserController|MockObject
   * @throws ReflectionException
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