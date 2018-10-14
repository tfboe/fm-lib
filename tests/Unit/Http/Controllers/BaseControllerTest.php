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
use PHPUnit\Framework\MockObject\MockObject;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Http\Controllers\BaseController;
use Tfboe\FmLib\Http\Controllers\UserController;
use Tfboe\FmLib\TestHelpers\TestEnum;
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
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($entityManager, self::getProperty(get_class($controller), 'entityManager')
      ->getValue($controller));
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::datetimetzTransformer()
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses   \Tfboe\FmLib\Helpers\Tools::datetimetzTransformer
   */
  public function testDatetimetzTransformer()
  {
    $controller = $this->controller();
    /** @noinspection PhpUnhandledExceptionInspection */
    $closure = self::getMethod(BaseController::class, 'datetimetzTransformer')
      ->invokeArgs($controller, [TestEnum::class]);
    $string = "2017-01-01 00:00:00 Europe/Vienna";
    $datetime = new \DateTime($string);
    /** @var \DateTime $result */
    $result = $closure($string);
    self::assertEquals($datetime, $result);
    self::assertEquals($datetime->getTimezone(), $result->getTimezone());
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::enumTransformer
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses   \Tfboe\FmLib\Helpers\Tools::enumTransformer
   */
  public function testEnumTransformer()
  {
    $controller = $this->controller();
    /** @noinspection PhpUnhandledExceptionInspection */
    $closure = self::getMethod(BaseController::class, 'enumTransformer')->invokeArgs($controller, [TestEnum::class]);
    self::assertEquals(1, $closure('INT_KEY'));
    self::assertEquals('value', $closure('KEY'));
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::getDatetimetzFormat
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses   \Tfboe\FmLib\Helpers\Tools::getDatetimetzFormat
   */
  public function testGetDatetimetzFormat()
  {
    $controller = $this->controller();
    $format = static::callProtectedMethod($controller, "getDatetimetzFormat");
    self::assertEquals('Y-m-d H:i:s e', $format);
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
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::setFromSpecification
   * @uses   \Tfboe\FmLib\Entity\Helpers\BaseEntity::methodExists
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses   \Tfboe\FmLib\Helpers\Tools::setFromSpecification
   */
  public function testSetFromSpecificationWithDefault()
  {
    $value = "test-value";
    $specification['prop'] = ['default' => $value];
    $object = self::getMockForAbstractClass(BaseEntity::class, [], '', true, true, true, ['setProp']);
    $object->expects(static::once())->method('setProp')->with($value)->willReturnSelf();
    $controller = $this->controller();
    /** @noinspection PhpUnhandledExceptionInspection */
    $method = self::getMethod(UserController::class, 'setFromSpecification');
    $method->invokeArgs($controller, [$object, $specification, []]);
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::setFromSpecification
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses   \Tfboe\FmLib\Helpers\Tools::setFromSpecification
   * @uses   \Tfboe\FmLib\Helpers\Tools::transformValue
   */
  public function testSetFromSpecificationWithProperty()
  {
    $value = 'test-value';
    $specification['attr'] = ['property' => 'prop'];
    $object = self::getMockForAbstractClass(BaseEntity::class, [], '', true, true, true, ['setProp']);
    $object->expects(static::once())->method('setProp')->with($value)->willReturnSelf();
    $controller = $this->controller();
    /** @noinspection PhpUnhandledExceptionInspection */
    $method = self::getMethod(UserController::class, 'setFromSpecification');
    $method->invokeArgs($controller, [$object, $specification, ['attr' => $value]]);
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::transformValue
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::getEntityManager
   */
//  public function testTransformValueByReference()
//  {
//    $user = "resultUser";
//    $specification = ['reference' => User::class];
//    $value = 'user-id';
//
//    $entityManager = $this->createMock(EntityManagerInterface::class);
//    $entityManager->expects(static::once())->method('find')->with(User::class, 'user-id')->willReturn($user);
//    $controller = $this->getMockForAbstractClass(BaseController::class, [$entityManager]);
//    /** @noinspection PhpUnhandledExceptionInspection */
//    $method = self::getMethod(UserController::class, 'transformValue');
//    $method->invokeArgs($controller, [&$value, $specification]);
//
//    self::assertTrue($value === $user);
//  }
//
//  /**
//   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::transformValue
//   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
//   */
//  public function testTransformValueByTransformer()
//  {
//    $value = "5";
//    $transformer = function ($input) {
//      self::assertEquals("5", $input);
//      return 6;
//    };
//    $specification = ['transformer' => $transformer];
//
//    $controller = $this->controller();
//    /** @noinspection PhpUnhandledExceptionInspection */
//    $method = self::getMethod(UserController::class, 'transformValue');
//    $method->invokeArgs($controller, [&$value, $specification]);
//
//    self::assertEquals(6, $value);
//  }
//
//  /**
//   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::transformValue
//   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::transformByType
//   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
//   */
//  public function testTransformValueByTypeDateTime()
//  {
//    $value = "2005-02-28 16:35:01";
//    $datetime = new \DateTime($value);
//    $specification = ['type' => 'datetime'];
//
//    $controller = $this->controller();
//    /** @noinspection PhpUnhandledExceptionInspection */
//    $method = self::getMethod(UserController::class, 'transformValue');
//    $method->invokeArgs($controller, [&$value, $specification]);
//
//    self::assertEquals($datetime, $value);
//  }
//
//  /**
//   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::transformValue
//   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::transformByType
//   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
//   */
//  public function testTransformValueByTypeDefault()
//  {
//    $value = "2005-02-28 16:35:01";
//    $specification = ['type' => 'default'];
//
//    $controller = $this->controller();
//    /** @noinspection PhpUnhandledExceptionInspection */
//    $method = self::getMethod(UserController::class, 'transformValue');
//    $method->invokeArgs($controller, [&$value, $specification]);
//
//    self::assertEquals("2005-02-28 16:35:01", $value);
//  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::validateBySpecification
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
    /** @noinspection PhpUnhandledExceptionInspection */
    $method = self::getMethod(UserController::class, 'validateBySpecification');
    $method->invokeArgs($controller, [$request, $specification]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|BaseController
   */
  private function controller(): MockObject
  {
    return $this->getMockForAbstractClass(BaseController::class, [
      $this->createMock(EntityManagerInterface::class)
    ]);
  }
//</editor-fold desc="Private Methods">
}