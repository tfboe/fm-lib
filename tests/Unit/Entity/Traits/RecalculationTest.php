<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity\Traits;

use PHPUnit\Framework\MockObject\MockObject;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\Traits\Recalculation;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tfboe\FmLib\Tests\Unit\Entity
 */
class RecalculationTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Recalculation::getVersion
   * @covers \Tfboe\FmLib\Entity\Traits\Recalculation::setVersion
   */
  public function testVersion()
  {
    $lastRecalculation = $this->lastRecalculation();
    $lastRecalculation->setVersion(1);
    self::assertEquals(1, $lastRecalculation->getVersion());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|Recalculation
   */
  private function lastRecalculation(): MockObject
  {
    return $this->getStubbedEntity("Recalculation", [], [], BaseEntity::class, false, false);
  }
//</editor-fold desc="Private Methods">
}