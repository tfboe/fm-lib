<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 11:03 AM
 */

namespace Tfboe\FmLib\Tests\Unit\Helpers;

use DateTime;
use Exception;
use Illuminate\Http\Request;
use PHPUnit\Framework\MockObject\MockObject;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\Helpers\BaseEntityInterface;
use Tfboe\FmLib\Helpers\SpecificationHandler;
use Tfboe\FmLib\Http\Controllers\BaseController;
use Tfboe\FmLib\TestHelpers\TestEnum;
use Tfboe\FmLib\Tests\Entity\User;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class BasicEnumTest
 * @package Tfboe\FmLib\TestHelpers
 */
class SpecificationHandlerTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::datetimetzTransformer()
   * @throws Exception
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses   \Tfboe\FmLib\Helpers\TransformerFactory::datetimetzTransformer
   */
  public function testDatetimetzTransformer()
  {
    $handler = $this->handler();

    $closure = self::getMethod(get_class($handler), 'datetimetzTransformer')
      ->invokeArgs($handler, [TestEnum::class]);
    $string = "2017-01-01 00:00:00 Europe/Vienna";
    $datetime = new DateTime($string);
    /** @var DateTime $result */
    $result = $closure($string);
    self::assertEquals($datetime, $result);
    self::assertEquals($datetime->getTimezone()->getName(), $result->getTimezone()->getName());
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::enumTransformer
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   * @uses   \Tfboe\FmLib\Helpers\TransformerFactory::enumTransformer
   */
  public function testEnumTransformer()
  {
    $handler = $this->handler();

    $closure = self::getMethod(get_class($handler), 'enumTransformer')->invokeArgs($handler, [TestEnum::class]);
    self::assertEquals(1, $closure('INT_KEY'));
    self::assertEquals('value', $closure('KEY'));
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::getDatetimetzFormat
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   */
  public function testGetDatetimetzFormat()
  {
    $handler = $this->handler();
    $format = static::callProtectedMethod($handler, "getDatetimetzFormat");
    self::assertEquals('Y-m-d H:i:s e', $format);
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::setFromSpecification
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\BaseEntity::methodExists
   */
  public function testSetFromSpecificationWithDefault()
  {
    $value = "test-value";
    $specification = ['prop' => ['default' => $value]];
    $object = $this->getMockForAbstractClass(BaseEntity::class, [], '', true, true, true, ['setProp']);
    $object->expects(static::once())->method('setProp')->with($value)->willReturnSelf();
    $handler = $this->handler();

    $method = self::getMethod(get_class($handler), 'setFromSpecification');
    $method->invokeArgs($handler, [$object, $specification, []]);
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::setFromSpecification
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::transformValue
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\BaseEntity::methodExists
   */
  public function testSetFromSpecificationWithProperty()
  {
    $value = 'test-value';
    $specification = ['attr' => ['property' => 'prop']];
    $object = $this->getMockForAbstractClass(BaseEntity::class, [], '', true, true, true, ['setProp']);
    $object->expects(static::once())->method('setProp')->with($value)->willReturnSelf();
    $handler = $this->handler();

    $method = self::getMethod(get_class($handler), 'setFromSpecification');
    $method->invokeArgs($handler, [$object, $specification, ['attr' => $value]]);
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::setFromSpecification
   * @uses   \Tfboe\FmLib\Helpers\SpecificationHandler::transformValue
   */
  public function testSetFromSpecificationWithSetter()
  {
    $value = 'test-value';
    $object = $this->getStub(BaseEntityInterface::class);
    $handler = $this->handler();

    $method = self::getMethod(get_class($handler), 'setFromSpecification');
    $executed = false;
    $specification = ['attr' => ['setter' => function ($entity, $v) use ($object, $value, &$executed) {
      self::assertEquals($object, $entity);
      self::assertEquals($value, $v);
      $executed = true;
    }]];
    $method->invokeArgs($handler, [$object, $specification, ['attr' => $value]]);
    self::assertTrue($executed);
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::transformValue
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::getEntityManager
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::getReference
   */
  public function testTransformValueByReference()
  {
    $user = "resultUser";
    $specification = ['reference' => User::class];
    $value = 'user-id';

    $handler = $this->handler(["getReference" => $user]);


    $method = self::getMethod(get_class($handler), 'transformValue');
    $method->invokeArgs($handler, [&$value, $specification]);

    self::assertTrue($value === $user);
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::transformValue
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   */
  public function testTransformValueByTransformer()
  {
    $value = "5";
    $transformer = function ($input) {
      self::assertEquals("5", $input);
      return 6;
    };
    $specification = ['transformer' => $transformer];

    $handler = $this->handler();

    $method = self::getMethod(get_class($handler), 'transformValue');
    $method->invokeArgs($handler, [&$value, $specification]);

    self::assertEquals(6, $value);
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::transformValue
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::transformByType
   * @throws Exception
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   */
  public function testTransformValueByTypeDateTime()
  {
    $value = "2005-02-28 16:35:01";
    $datetime = new DateTime($value);
    $specification = ['type' => 'datetime'];

    $handler = $this->handler();

    $method = self::getMethod(get_class($handler), 'transformValue');
    $method->invokeArgs($handler, [&$value, $specification]);

    self::assertEquals($datetime, $value);
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::transformValue
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::transformByType
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   */
  public function testTransformValueByTypeDefault()
  {
    $value = "2005-02-28 16:35:01";
    $specification = ['type' => 'default'];

    $handler = $this->handler();

    $method = self::getMethod(get_class($handler), 'transformValue');
    $method->invokeArgs($handler, [&$value, $specification]);

    self::assertEquals("2005-02-28 16:35:01", $value);
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::transformValue
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::transformByType
   * @throws Exception
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   */
  public function testTransformValueByTypeInvalidDateTime()
  {
    $orig = "INVALID DATE";
    $value = "INVALID DATE";
    $specification = ['type' => 'datetime'];

    $handler = $this->handler();

    $method = self::getMethod(get_class($handler), 'transformValue');
    $method->invokeArgs($handler, [&$value, $specification]);

    self::assertEquals($orig, $value);
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::transformValue
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::getEntityManager
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::getReference
   */
  public function testTransformValueNullValue()
  {
    $handler = $this->handler();
    $specification = ['nullValue' => 5];
    $value = null;


    $method = self::getMethod(get_class($handler), 'transformValue');
    $method->invokeArgs($handler, [&$value, $specification]);

    self::assertTrue($value === 5);
  }

  /**
   * @covers \Tfboe\FmLib\Http\Controllers\BaseController::validateBySpecification
   * @uses   \Tfboe\FmLib\Http\Controllers\BaseController::__construct
   */
  public function testValidateBySpecification()
  {
    $handler = $this->getMockForTrait(SpecificationHandler::class, [], '', true, true, true, ['validateSpec']);
    $request = $this->getMockForAbstractClass(Request::class, [], '', true, true, true, ['has', 'get', 'merge']);
    $request->expects(static::once())->method('has')->with('transformed')->willReturn(true);
    $request->expects(static::once())->method('get')->with('transformed')->willReturn(6);
    $request->expects(static::once())->method('merge')->with(['transformed' => 7]);

    $handler->expects(static::once())->method('validateSpec')
      ->with($request, ['withValidation' => 'required|string|min:2', 'transformed' => 'required|int']);
    /** @var BaseController $handler */
    $specification = [
      'noValidation' => ['default' => 5],
      'withValidation' => ['validation' => 'required|string|min:2', 'ignore' => true],
      'transformed' => ['validation' => 'required|int', 'transformBeforeValidation' => function ($x) {
        self::assertEquals(6, $x);
        return $x + 1;
      }]
    ];

    $method = self::getMethod(get_class($handler), 'validateBySpecification');
    $method->invokeArgs($handler, [$request, $specification]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Methods">
//</editor-fold desc="Protected Methods">

//<editor-fold desc="Private Methods">
  /**
   * @param array $methods
   * @return MockObject|SpecificationHandler
   */
  private function handler(array $methods = []): MockObject
  {
    return $this->getPartialMockForTrait(SpecificationHandler::class, $methods);
  }
//</editor-fold desc="Private Methods">
}