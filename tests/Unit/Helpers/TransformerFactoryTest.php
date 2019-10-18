<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Helpers;

use DateTime;
use Exception;
use Tfboe\FmLib\Helpers\TransformerFactory;
use Tfboe\FmLib\TestHelpers\TestEnum;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class HandlerTest
 * @package Tfboe\FmLib\Tests\Unit\Exceptions
 */
class TransformerFactoryTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Helpers\TransformerFactory::booleanTransformer
   */
  public function testBooleanTransformer()
  {
    $transformer = TransformerFactory::booleanTransformer();
    self::assertEquals(true, $transformer('true'));
    self::assertEquals(false, $transformer('false'));
    self::assertEquals(true, $transformer(true));
    self::assertEquals(false, $transformer(false));
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\TransformerFactory::datetimetzTransformer
   * @throws Exception
   */
  public function testDatetimetzTransformer()
  {
    $string = "2017-01-01 00:00:00 Europe/Vienna";
    $transformer = TransformerFactory::datetimetzTransformer('Y-m-d H:i:s e');
    $datetime = new DateTime($string);
    /** @var DateTime $result */
    $result = $transformer($string);
    self::assertEquals($datetime, $result);
    self::assertEquals($datetime->getTimezone()->getName(), $result->getTimezone()->getName());
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\TransformerFactory::enumNameTransformer
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getName
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getNamesArray
   */
  public function testEnumNameTransformer()
  {
    $transformer = TransformerFactory::enumNameTransformer(TestEnum::class);
    self::assertEquals('INT_KEY', $transformer(1));
    self::assertEquals('KEY', $transformer('value'));
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\TransformerFactory::enumTransformer
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getCaseMapping
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getValue
   */
  public function testEnumTransformer()
  {
    $transformer = TransformerFactory::enumTransformer(TestEnum::class);
    self::assertEquals(1, $transformer('INT_KEY'));
    self::assertEquals('value', $transformer('KEY'));
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\TransformerFactory::finiteMappingTransformation
   */
  public function testFiniteMappingTransformation()
  {
    $transformer = TransformerFactory::finiteMappingTransformation(['x' => 1, 5 => 'y']);
    self::assertEquals(1, $transformer('x'));
    self::assertEquals('y', $transformer(5));
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\TransformerFactory::finiteMappingTransformation
   */
  public function testFiniteMappingTransformationInvalidKey()
  {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage("Unknown source value!");
    $transformer = TransformerFactory::finiteMappingTransformation(['x' => 1, 5 => 'y']);
    $transformer(1);
  }
//</editor-fold desc="Public Methods">
}