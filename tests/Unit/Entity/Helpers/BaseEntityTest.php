<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:52 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity\Helpers;

use ReflectionException;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class BaseEntityTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\Helpers
 */
class BaseEntityTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\BaseEntity::methodExists
   * @throws ReflectionException
   */
  public function testMethodExists()
  {
    /** @var BaseEntity $entity */
    $entity = $this->getMockForAbstractClass(BaseEntity::class);

    self::assertTrue($entity->methodExists("methodExists"));
    self::assertFalse($entity->methodExists("notExistingMethod"));
  }
//</editor-fold desc="Public Methods">
}