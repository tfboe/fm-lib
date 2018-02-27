<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Tfboe\FmLib\Entity\PhaseInterface;
use Tfboe\FmLib\Entity\QualificationSystemInterface;
use Tfboe\FmLib\Tests\Entity\QualificationSystem;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tfboe\FmLib\Tests\Unit\Entity
 */
class QualificationSystemTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Traits\QualificationSystem::setNextPhase
   * @covers \Tfboe\FmLib\Entity\Traits\QualificationSystem::getNextPhase
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testNextPhase()
  {
    $system = $this->system();
    $phase = $this->createStub(PhaseInterface::class, ['getPreQualifications' => new ArrayCollection()]);

    /** @var PhaseInterface $phase */
    $system->setNextPhase($phase);
    self::assertEquals($phase, $system->getNextPhase());
    self::assertEquals(1, $system->getNextPhase()->getPreQualifications()->count());
    self::assertEquals($system, $system->getNextPhase()->getPreQualifications()[0]);

    $phase2 = $this->createStub(PhaseInterface::class, ['getPreQualifications' => new ArrayCollection()]);

    /** @var PhaseInterface $phase2 */
    $system->setNextPhase($phase2);
    self::assertEquals($phase2, $system->getNextPhase());
    self::assertEquals(1, $system->getNextPhase()->getPreQualifications()->count());
    self::assertEquals(0, $phase->getPreQualifications()->count());
    self::assertEquals($system, $system->getNextPhase()->getPreQualifications()[0]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\QualificationSystem::setPreviousPhase
   * @covers \Tfboe\FmLib\Entity\Traits\QualificationSystem::getPreviousPhase
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testPreviousPhase()
  {
    $system = $this->system();
    $phase = $this->createStub(PhaseInterface::class, ['getPostQualifications' => new ArrayCollection()]);

    /** @var PhaseInterface $phase */
    $system->setPreviousPhase($phase);
    self::assertEquals($phase, $system->getPreviousPhase());
    self::assertEquals(1, $system->getPreviousPhase()->getPostQualifications()->count());
    self::assertEquals($system, $system->getPreviousPhase()->getPostQualifications()[0]);

    $phase2 = $this->createStub(PhaseInterface::class, ['getPostQualifications' => new ArrayCollection()]);

    /** @var PhaseInterface $phase2 */
    $system->setPreviousPhase($phase2);
    self::assertEquals($phase2, $system->getPreviousPhase());
    self::assertEquals(1, $system->getPreviousPhase()->getPostQualifications()->count());
    self::assertEquals(0, $phase->getPostQualifications()->count());
    self::assertEquals($system, $system->getPreviousPhase()->getPostQualifications()[0]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return QualificationSystemInterface a new qualification system
   */
  private function system(): QualificationSystemInterface
  {
    return new QualificationSystem();
  }
//</editor-fold desc="Private Methods">
}