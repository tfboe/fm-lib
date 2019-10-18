<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity\Helpers;

use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;
use Tfboe\FmLib\Entity\Helpers\NumericalId;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class PlayerTest
 * @package Tfboe\FmLib\Tests\Unit\Entity
 */
class NumericalIdTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\NumericalId::getEntityId
   * @throws ReflectionException
   */
  public function testEntityId()
  {
    $player = $this->player();
    /** @noinspection PhpUnhandledExceptionInspection */
    $idProperty = self::getProperty(get_class($player), 'id');
    $idProperty->setValue($player, 1);
    self::assertEquals(1, $player->getEntityId());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\NumericalId::getId
   * @throws ReflectionException
   */
  public function testId()
  {
    $player = $this->player();
    /** @noinspection PhpUnhandledExceptionInspection */
    $idProperty = self::getProperty(get_class($player), 'id');
    $idProperty->setValue($player, 0);
    self::assertEquals(0, $player->getId());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return NumericalId|MockObject a new player
   * @throws ReflectionException
   */
  private function player(): MockObject
  {
    return $this->getMockForTrait(NumericalId::class);
  }
//</editor-fold desc="Private Methods">
}