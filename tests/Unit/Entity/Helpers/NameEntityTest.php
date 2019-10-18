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
use ReflectionException;
use Tfboe\FmLib\Entity\Helpers\NameEntity;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class BaseEntityTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\Helpers
 */
class NameEntityTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\NameEntity::getName
   * @covers \Tfboe\FmLib\Entity\Helpers\NameEntity::setName
   * @throws ReflectionException
   */
  public function testName()
  {
    $entity = $this->mock();
    $entity->setName("Name");
    self::assertEquals("Name", $entity->getName());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|NameEntity
   * @throws ReflectionException
   */
  private function mock(): MockObject
  {
    return $this->getMockForTrait(NameEntity::class);
  }
//</editor-fold desc="Private Methods">
}