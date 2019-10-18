<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:52 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity\Helpers;

use Doctrine\ORM\EntityManager;
use Iterator;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\Helpers\IdentifiableInterface;
use Tfboe\FmLib\Entity\Helpers\IdGenerator;
use Tfboe\FmLib\Entity\Helpers\UUIDEntityInterface;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class IdGeneratorTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\Helpers
 */
class IdGeneratorTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\IdGenerator::createIdFor
   * @uses   \Tfboe\FmLib\Entity\Helpers\IdGenerator::createIdFrom
   * @uses   \Tfboe\FmLib\Helpers\Random::stringToInt
   */
  public function testCreateIdFor()
  {
    $e1 = $this->getMockBuilder(Iterator::class)
      ->setMockClassName("CreateIdForA")
      ->getMock();
    $e2 = $this->getMockBuilder(Iterator::class)
      ->setMockClassName("CreateIdForA")
      ->getMock();
    $e3 = $this->getMockBuilder(Iterator::class)
      ->setMockClassName("CreateIdForB")
      ->getMock();
    srand(10);
    $id1 = IdGenerator::createIdFor($e1);
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $id1);

    $id2 = IdGenerator::createIdFor($e1);
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $id2);
    self::assertNotEquals($id1, $id2);

    $id3 = IdGenerator::createIdFor($e2);
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $id3);
    self::assertNotEquals($id1, $id3);
    self::assertNotEquals($id2, $id3);

    srand(10);
    $id2 = IdGenerator::createIdFor($e2);
    self::assertEquals($id1, $id2);

    srand(10);
    $id3 = IdGenerator::createIdFor($e3);
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $id3);
    self::assertNotEquals($id1, $id3);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\IdGenerator::createIdFor
   * @uses   \Tfboe\FmLib\Entity\Helpers\IdGenerator::createIdFrom
   * @uses   \Tfboe\FmLib\Helpers\Random::stringToInt
   */
  public function testCreateIdForIdAble()
  {
    $e1 = $this->getMockBuilder(IdentifiableInterface::class)
      ->setMockClassName("CreateIdForIdAbleA")
      ->getMock();
    $e1->method("getIdentifiableId")->willReturn(1);

    $e2 = $this->getMockBuilder(IdentifiableInterface::class)
      ->setMockClassName("CreateIdForIdAbleA")
      ->getMock();
    $e2->method("getIdentifiableId")->willReturn(1);

    $e3 = $this->getMockBuilder(IdentifiableInterface::class)
      ->setMockClassName("CreateIdForIdAbleB")
      ->getMock();
    $e3->method("getIdentifiableId")->willReturn(1);

    $e4 = $this->getMockBuilder(IdentifiableInterface::class)
      ->setMockClassName("CreateIdForIdAbleA")
      ->getMock();
    $e4->method("getIdentifiableId")->willReturn(2);

    srand(10);
    $id1 = IdGenerator::createIdFor($e1);
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $id1);

    $id2 = IdGenerator::createIdFor($e1);
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $id2);
    self::assertNotEquals($id1, $id2);

    $id3 = IdGenerator::createIdFor($e2);
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $id3);
    self::assertNotEquals($id1, $id3);
    self::assertNotEquals($id2, $id3);

    srand(10);
    $id2 = IdGenerator::createIdFor($e2);
    self::assertEquals($id1, $id2);

    srand(10);
    $id3 = IdGenerator::createIdFor($e3);
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $id3);
    self::assertNotEquals($id1, $id3);

    srand(10);
    $id4 = IdGenerator::createIdFor($e4);
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $id4);
    self::assertNotEquals($id1, $id4);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\IdGenerator::createIdFrom
   */
  public function testCreateIdFrom()
  {
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', IdGenerator::createIdFrom());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\IdGenerator::createIdFrom
   */
  public function testCreateIdFromDeterminism()
  {
    srand(10);
    $id1 = IdGenerator::createIdFrom();
    $id2 = IdGenerator::createIdFrom(-10);
    $id3 = IdGenerator::createIdFrom(10);
    self::assertNotEquals($id1, $id2);
    self::assertNotEquals($id2, $id3);
    self::assertNotEquals($id1, $id3);
    srand(10);
    self::assertEquals($id1, IdGenerator::createIdFrom());
    self::assertEquals($id2, IdGenerator::createIdFrom(-10));
    self::assertEquals($id3, IdGenerator::createIdFrom(10));
    srand(10);
    self::assertNotEquals($id2, IdGenerator::createIdFrom(-10));
    self::assertNotEquals($id3, IdGenerator::createIdFrom(10));
    self::assertNotEquals($id1, IdGenerator::createIdFrom());
    srand(10);
    self::assertNotEquals($id1, IdGenerator::createIdFrom(-10));
    self::assertNotEquals($id2, IdGenerator::createIdFrom(10));
    self::assertNotEquals($id3, IdGenerator::createIdFrom());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\IdGenerator::createIdFrom
   */
  public function testCreateIdFromMixByExtremes()
  {
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', IdGenerator::createIdFrom(PHP_INT_MAX));
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', IdGenerator::createIdFrom(PHP_INT_MIN));
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\IdGenerator::createIdFrom
   */
  public function testCreateIdFromMixByNegative()
  {
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', IdGenerator::createIdFrom(-10));
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\IdGenerator::createIdFrom
   */
  public function testCreateIdFromMixByPositive()
  {
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', IdGenerator::createIdFrom(10));
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\IdGenerator::createIdFor
   * @uses   \Tfboe\FmLib\Entity\Helpers\IdGenerator::createIdFrom
   * @uses   \Tfboe\FmLib\Helpers\Random::stringToInt
   */
  public function testCreateIdWithExistingId()
  {
    $e1 = $this->getMockBuilder(UUIDEntityInterface::class)
      ->setMockClassName("CreateIdWithExistingIdA")
      ->setMethods(['getId', 'hasId', 'setId'])
      ->getMock();
    $e1->method("getId")->willReturn("1");
    $e1->method("hasId")->willReturn(true);

    self::assertEquals('1', IdGenerator::createIdFor($e1));

    $e2 = $this->getMockBuilder(UUIDEntityInterface::class)
      ->setMockClassName("CreateIdWithExistingIdA")
      ->setMethods(['getId', 'hasId', 'setId'])
      ->getMock();
    $e2->method("hasId")->willReturn(false);

    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', IdGenerator::createIdFor($e2));
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\IdGenerator::generate
   * @uses   \Tfboe\FmLib\Entity\Helpers\IdGenerator::createIdFrom
   * @uses   \Tfboe\FmLib\Entity\Helpers\IdGenerator::createIdFor
   * @uses   \Tfboe\FmLib\Helpers\Random::stringToInt
   */
  public function testGenerate()
  {
    $generator = new IdGenerator();
    $entityManager = $this->createMock(EntityManager::class);
    $entity = $this->createStub(BaseEntity::class);
    /** @var EntityManager $entityManager */
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $generator->generate($entityManager, $entity));
  }
//</editor-fold desc="Public Methods">
}