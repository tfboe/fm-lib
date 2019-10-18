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
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\Helpers\IdGenerator;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class IdGeneratorTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\Helpers
 */
class IdGeneratorTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\IdGenerator::createIdFrom
   */
  public function testCreateIdFrom()
  {
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', IdGenerator::createIdFrom());
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