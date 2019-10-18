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
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;
use Tfboe\FmLib\Entity\CompetitionInterface;
use Tfboe\FmLib\Entity\MatchInterface;
use Tfboe\FmLib\Entity\PhaseInterface;
use Tfboe\FmLib\Entity\Traits\QualificationSystem;
use Tfboe\FmLib\Helpers\Level;
use Tfboe\FmLib\Tests\Entity\Ranking;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;


/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

/**
 * Class Phase
 * @package Tfboe\FmLib\Tests\Unit\Entity\Traits
 */
abstract class Phase implements PhaseInterface
{
  use \Tfboe\FmLib\Entity\Traits\Phase;
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

/**
 * Class TournamentTest
 * @package Tfboe\FmLib\Tests\Unit\Entity
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class PhaseTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Phase::setCompetition
   * @covers \Tfboe\FmLib\Entity\Traits\Phase::getCompetition
   * @covers \Tfboe\FmLib\Entity\Traits\Phase::getParent
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Entity\Traits\Phase::setPhaseNumber
   * @uses   \Tfboe\FmLib\Entity\Traits\Phase::getPhaseNumber
   */
  public function testCompetitionAndParent()
  {
    $phase = $this->phase();
    $competition = $this->createStub(CompetitionInterface::class, ['getPhases' => new ArrayCollection()]);
    $phase->setPhaseNumber(1);
    /** @var CompetitionInterface $competition */
    $phase->setCompetition($competition);
    self::assertEquals($competition, $phase->getCompetition());
    self::assertEquals(1, $phase->getCompetition()->getPhases()->count());
    self::assertEquals($phase, $phase->getCompetition()->getPhases()[$phase->getPhaseNumber()]);
    self::assertEquals($phase->getCompetition(), $phase->getParent());

    $competition2 = $this->createStub(CompetitionInterface::class, ['getPhases' => new ArrayCollection()]);

    /** @var CompetitionInterface $competition2 */
    $phase->setCompetition($competition2);
    self::assertEquals($competition2, $phase->getCompetition());
    self::assertEquals(1, $phase->getCompetition()->getPhases()->count());
    self::assertEquals(0, $competition->getPhases()->count());
    self::assertEquals($phase, $phase->getCompetition()->getPhases()[$phase->getPhaseNumber()]);
    self::assertEquals($phase->getCompetition(), $phase->getParent());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Phase::init
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Entity\Traits\Phase::getPostQualifications
   * @uses   \Tfboe\FmLib\Entity\Traits\Phase::getPreQualifications
   * @uses   \Tfboe\FmLib\Entity\Traits\Phase::getRankings
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::getName
   */
  public function testInit()
  {
    $phase = $this->phase();
    self::callProtectedMethod($phase, 'init');
    self::assertEquals('', $phase->getName());
    self::assertInstanceOf(Collection::class, $phase->getPostQualifications());
    self::assertEquals(0, $phase->getPostQualifications()->count());
    self::assertInstanceOf(Collection::class, $phase->getPreQualifications());
    self::assertEquals(0, $phase->getPreQualifications()->count());
    self::assertInstanceOf(Collection::class, $phase->getRankings());
    self::assertEquals(0, $phase->getRankings()->count());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Phase::getLevel
   * @throws ReflectionException
   */
  public function testLevel()
  {
    self::assertEquals(Level::PHASE, $this->phase()->getLevel());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Phase::getMatches
   * @covers \Tfboe\FmLib\Entity\Traits\Phase::getChildren
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Entity\Traits\Phase::init
   */
  public function testMatchesAndChildren()
  {
    $phase = $this->phase();
    self::callProtectedMethod($phase, 'init');
    $match = $this->createStub(MatchInterface::class, ['getMatchNumber' => 1]);
    self::assertEquals($phase->getMatches(), $phase->getChildren());
    /** @var MatchInterface $match */
    $phase->getMatches()->set($match->getMatchNumber(), $match);
    self::assertEquals(1, $phase->getMatches()->count());
    self::assertEquals($match, $phase->getMatches()[1]);
    self::assertEquals($phase->getMatches(), $phase->getChildren());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Phase::getPostQualifications
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Entity\Traits\QualificationSystem
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\Phase::init
   */
  public function testNextQualificationSystems()
  {
    $phase = $this->phase();
    self::callProtectedMethod($phase, 'init');
    /** @var QualificationSystem $qualificationSystem */
    $qualificationSystem = $this->qualificationSystemWithId();
    $qualificationSystem->setPreviousPhase($phase);
    self::assertEquals(1, $phase->getPostQualifications()->count());
    self::assertEquals($qualificationSystem, $phase->getPostQualifications()["id"]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Phase::setPhaseNumber
   * @covers \Tfboe\FmLib\Entity\Traits\Phase::getPhaseNumber
   * @covers \Tfboe\FmLib\Entity\Traits\Phase::getLocalIdentifier
   * @throws ReflectionException
   */
  public function testPhaseNumberAndLocalIdentifier()
  {
    $phase = $this->phase();
    $phase->setPhaseNumber(5);
    self::assertEquals(5, $phase->getPhaseNumber());
    self::assertEquals($phase->getPhaseNumber(), $phase->getLocalIdentifier());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Phase::getPreQualifications
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Entity\Traits\QualificationSystem
   * @uses   \Tfboe\FmLib\Entity\Traits\Phase::init
   */
  public function testPreviousQualificationSystems()
  {
    $phase = $this->phase();
    self::callProtectedMethod($phase, 'init');
    /** @var QualificationSystem $qualificationSystem */
    $qualificationSystem = $this->qualificationSystemWithId();
    $qualificationSystem->setNextPhase($phase);
    self::assertEquals(1, $phase->getPreQualifications()->count());
    self::assertEquals($qualificationSystem, $phase->getPreQualifications()["id"]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Phase::getRankings
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Entity\Traits\Ranking
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\Phase::init
   */
  public function testRankings()
  {
    $phase = $this->phase();
    self::callProtectedMethod($phase, 'init');
    $ranking = new Ranking();
    $ranking->setUniqueRank(1);
    $phase->getRankings()->set($ranking->getUniqueRank(), $ranking);
    self::assertEquals(1, $phase->getRankings()->count());
    self::assertEquals($ranking, $phase->getRankings()[1]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return Phase|MockObject a new phase
   * @throws ReflectionException
   */
  private function phase(): MockObject
  {
    return $this->getMockForAbstractClass(Phase::class);
  }

  /**
   * @return MockObject|QualificationSystem
   * @throws ReflectionException
   * @throws ReflectionException
   */
  private function qualificationSystemWithId(): MockObject
  {
    return $this->getPartialMockForTrait(QualificationSystem::class, ['getId' => 'id']);
  }
//</editor-fold desc="Private Methods">
}