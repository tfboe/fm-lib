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
use Tfboe\FmLib\Entity\Helpers\NumericalPlayerId;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class PlayerTest
 * @package Tfboe\FmLib\Tests\Unit\Entity
 */
class NumericalPlayerIdTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\NumericalPlayerId::getPlayerId
   */
  public function testId()
  {
    $player = $this->player();
    /** @noinspection PhpUnhandledExceptionInspection */
    $idProperty = self::getProperty(get_class($player), 'playerId');
    $idProperty->setValue($player, 0);
    self::assertEquals(0, $player->getPlayerId());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return NumericalPlayerId|MockObject a new player
   */
  private function player(): MockObject
  {
    return $this->getMockForTrait(NumericalPlayerId::class);
  }
//</editor-fold desc="Private Methods">
}