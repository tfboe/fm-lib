<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 10:39 AM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity\Helpers;


use PHPUnit\Framework\MockObject\MockObject;
use Tfboe\FmLib\Entity\Helpers\UUIDEntity;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;


/**
 * Class BaseEntityTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\Helpers
 */
class UUIDEntityTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\UUIDEntity::getEntityId
   */
  public function testGetEntityId()
  {
    $entity = $this->mock();

    self::getProperty(get_class($entity), 'id')->setValue($entity, 'test-id');
    self::assertEquals('test-id', $entity->getEntityId());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\UUIDEntity::getId
   */
  public function testGetId()
  {
    $entity = $this->mock();

    self::getProperty(get_class($entity), 'id')->setValue($entity, 'test-id');
    self::assertEquals('test-id', $entity->getId());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\UUIDEntity::hasId
   */
  public function testHasId()
  {
    $entity = $this->mock();

    self::getProperty(get_class($entity), 'id')->setValue($entity, 'test-id');
    self::assertEquals(true, $entity->hasId());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\UUIDEntity::setId
   * @uses   \Tfboe\FmLib\Entity\Helpers\IdGenerator::createIdFor
   * @uses   \Tfboe\FmLib\Entity\Helpers\IdGenerator::createIdFrom
   * @uses   \Tfboe\FmLib\Helpers\Random::stringToRandom
   * @uses   \Tfboe\FmLib\Entity\Traits\User::getId
   * @uses   \Tfboe\FmLib\Exceptions\Internal::assert
   * @uses   \Tfboe\FmLib\Helpers\Random::__construct
   * @uses   \Tfboe\FmLib\Helpers\Random::extractEntropyByBits
   */
  public function testSetId()
  {
    $entity = $this->mock();
    self::callProtectedMethod($entity, 'setId');
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $entity->getId());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\User::__toString
   */
  public function testToString()
  {
    $entity = $this->mock();

    self::getProperty(get_class($entity), 'id')->setValue($entity, 'test-id');
    self::assertEquals("UUIDEntity:test-id", strval($entity));
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|UUIDEntity
   */
  private function mock(): MockObject
  {
    return $this->getMockForTrait(UUIDEntity::class, [], "UUIDEntity");
  }
//</editor-fold desc="Private Methods">
}