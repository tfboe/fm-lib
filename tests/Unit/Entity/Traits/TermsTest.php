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
use ReflectionException;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\Traits\Terms;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tfboe\FmLib\Tests\Unit\Entity
 */
class TermsTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Terms::getMajorVersion
   * @covers \Tfboe\FmLib\Entity\Traits\Terms::setMajorVersion
   * @throws ReflectionException
   */
  public function testMajorVersion()
  {
    $terms = $this->terms();
    $terms->setMajorVersion(1);
    self::assertEquals(1, $terms->getMajorVersion());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Terms::getMinorVersion
   * @covers \Tfboe\FmLib\Entity\Traits\Terms::setMinorVersion
   * @throws ReflectionException
   */
  public function testMinorVersion()
  {
    $terms = $this->terms();
    $terms->setMinorVersion(1);
    self::assertEquals(1, $terms->getMinorVersion());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Terms::getText
   * @covers \Tfboe\FmLib\Entity\Traits\Terms::setText
   * @throws ReflectionException
   */
  public function testText()
  {
    $terms = $this->terms();
    $terms->setText("text");
    self::assertEquals("text", $terms->getText());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|Terms
   * @throws ReflectionException
   */
  private function terms(): MockObject
  {
    return $this->getStubbedEntity("Terms", [], [], BaseEntity::class, false, false);
  }
//</editor-fold desc="Private Methods">
}