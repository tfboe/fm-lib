<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:52 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity\Helpers;

use DateTime;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tfboe\FmLib\Entity\Helpers\TimestampableEntity;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class BaseEntityTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\Helpers
 */
class TimestampableEntityTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\TimestampableEntity::setCreatedAt
   * @covers \Tfboe\FmLib\Entity\Helpers\TimestampableEntity::getCreatedAt
   * @throws Exception
   * @uses   \Tfboe\FmLib\Helpers\DateTime::eq
   */
  public function testCreatedAt()
  {
    $entity = $this->mock();
    $createTime = new DateTime();
    $entity->setCreatedAt($createTime);
    self::assertEquals($createTime, $entity->getCreatedAt());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\TimestampableEntity::setUpdatedAt
   * @covers \Tfboe\FmLib\Entity\Helpers\TimestampableEntity::getUpdatedAt
   * @throws Exception
   * @uses   \Tfboe\FmLib\Helpers\DateTime::eq
   */
  public function testUpdateAt()
  {
    $entity = $this->mock();
    $createTime = new DateTime();
    $entity->setUpdatedAt($createTime);
    self::assertEquals($createTime, $entity->getUpdatedAt());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|TimestampableEntity
   */
  private function mock(): MockObject
  {
    return $this->getMockForTrait(TimestampableEntity::class);
  }
//</editor-fold desc="Private Methods">
}