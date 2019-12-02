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
use Tfboe\FmLib\Http\Controllers\BaseController;
use Tfboe\FmLib\Http\Controllers\UserController;
use Tfboe\FmLib\Tests\Entity\User;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class BaseControllerTest
 * @package Tests\Unit\App\Http\Controllers
 */
class BaseControllerTest extends UnitTestCase
{
  //tests also private method disable this tests as soon as all are used in public interfaces
//<editor-fold desc="Public Methods">

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   */
  public function testConstruct()
  {
    $entityManager = $this->createMock(EntityManagerInterface::class);
    $controller = $this->getMockForAbstractClass(BaseController::class, [
      $entityManager
    ]);
    self::assertInstanceOf(BaseController::class, $controller);

    self::assertEquals($entityManager, self::getProperty(get_class($controller), 'entityManager')
      ->getValue($controller));
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::getEntityManager
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   */
  public function testGetEntityManager()
  {
    $entityManager = $this->createMock(EntityManagerInterface::class);
    $controller = $this->getMockForAbstractClass(BaseController::class, [$entityManager]);
    $em = static::callProtectedMethod($controller, "getEntityManager");
    self::assertEquals($entityManager, $em);
  }

  /**
   * @covers   \Tfboe\FmLib\Http\Controllers\BaseController::transformValue
   * @covers   \Tfboe\FmLib\Http\Controllers\BaseController::getReference
   * @uses     \Tfboe\FmLib\Http\Controllers\BaseController::getEntityManager
   * @uses     \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   */
  public function testTransformValueByReference()
  {
    $user = "resultUser";
    $specification = ['reference' => User::class];
    $value = 'user-id';

    $entityManager = $this->createMock(EntityManagerInterface::class);
    $entityManager->expects(static::once())->method('find')->with(User::class, 'user-id')->willReturn($user);
    $controller = $this->getMockForAbstractClass(BaseController::class, [$entityManager]);

    $method = self::getMethod(UserController::class, 'transformValue');
    $method->invokeArgs($controller, [&$value, $specification]);

    self::assertTrue($value === $user);
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::validateBySpecification
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::validateSpec
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   */
  public function testValidateBySpecification()
  {
    $controller = $this->getMockForAbstractClass(BaseController::class,
      [$this->createMock(EntityManagerInterface::class)], '', true, true, true, ['validate']);
    $request = $this->createMock(Request::class);
    $controller->expects(static::once())->method('validate')
      ->with($request, ['withValidation' => 'required|string|min:2']);
    /** @var BaseController $controller */
    $specification = [
      'noValidation' => ['default' => 5],
      'withValidation' => ['validation' => 'required|string|min:2']
    ];

    $method = self::getMethod(UserController::class, 'validateBySpecification');
    $method->invokeArgs($controller, [$request, $specification]);
  }
//</editor-fold desc="Public Methods">
}