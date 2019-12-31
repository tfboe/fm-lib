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
use Tfboe\FmLib\Entity\CompetitionInterface;
use Tfboe\FmLib\Entity\PhaseInterface;
use Tfboe\FmLib\Entity\TournamentInterface;
use Tfboe\FmLib\Entity\Traits\Competition;
use Tfboe\FmLib\Entity\Traits\Phase;
use Tfboe\FmLib\Helpers\Level;
use Tfboe\FmLib\Tests\Entity\Team;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tfboe\FmLib\Tests\Unit\Entity
 */
class CompetitionTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Competition::getLocalIdentifier
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::setName
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::getName
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition::init
   */
  public function testGetLocalIdentifier()
  {
    $entity = $this->competition();
    $entity->setName("Name");
    self::assertEquals($entity->getName(), $entity->getLocalIdentifier());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Competition::init
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition::getTeams
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition::getPhases
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition::init
   */
  public function testInit()
  {
    $competition = $this->competition();
    self::callProtectedMethod($competition, 'init');
    self::assertInstanceOf(Collection::class, $competition->getTeams());
    self::assertInstanceOf(Collection::class, $competition->getPhases());
    self::assertEquals(0, $competition->getTeams()->count());
    self::assertEquals(0, $competition->getPhases()->count());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Competition::getLevel()
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition::init
   */
  public function testLevel()
  {
    self::assertEquals(Level::COMPETITION, $this->competition()->getLevel());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Competition::getPhases
   * @covers \Tfboe\FmLib\Entity\Traits\Competition::getChildren
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition::init
   */
  public function testPhasesAndChildren()
  {
    $competition = $this->competition();
    self::callProtectedMethod($competition, 'init');
    /** @var PhaseInterface $phase */
    $phase = $this->createStubWithId(PhaseInterface::class, "id");
    $phase->setPhaseNumber(1);
    self::assertEquals($competition->getPhases(), $competition->getChildren());
    $competition->getPhases()->set($phase->getId(), $phase);
    self::assertEquals(1, $competition->getPhases()->count());
    self::assertEquals($phase, $competition->getPhases()[$phase->getId()]);
    self::assertEquals($competition->getPhases(), $competition->getChildren());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Competition::getTeams
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition::init
   * @uses   \Tfboe\FmLib\Entity\Traits\Team::init
   * @uses   \Tfboe\FmLib\Entity\Traits\Team::setStartNumber
   */
  public function testTeams()
  {
    $competition = $this->competition();
    self::callProtectedMethod($competition, 'init');
    /** @var Team|MockObject $team */
    $team = $this->getStubbedEntity("Team", ["getId" => "id"]);
    $team->setStartNumber(1);
    $competition->getTeams()->set($team->getId(), $team);
    self::assertEquals(1, $competition->getTeams()->count());
    self::assertEquals($team, $competition->getTeams()[$team->getId()]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Competition::setTournament()
   * @covers \Tfboe\FmLib\Entity\Traits\Competition::getTournament()
   * @covers \Tfboe\FmLib\Entity\Traits\Competition::getParent()
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::getName
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::setName
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition::init
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition::setTournamentWithoutInitializing
   * @uses   \Tfboe\FmLib\Helpers\Tools::isInitialized
   */
  public function testTournamentAndParent()
  {
    $competition = $this->competition();
    /** @var TournamentInterface|MockObject $tournament */
    $tournament = $this->createMock(TournamentInterface::class);
    $competitions = new ArrayCollection();
    $tournament->method('getCompetitions')->willReturn($competitions);
    $competition->setName('test competition');

    $competition->setTournament($tournament);
    self::assertEquals($tournament, $competition->getTournament());
    self::assertEquals($competition->getTournament(), $competition->getParent());
    self::assertEquals(1, $competition->getTournament()->getCompetitions()->count());
    self::assertEquals($competition, $competition->getTournament()->getCompetitions()[$competition->getId()]);

    $tournament2 = $this->createMock(TournamentInterface::class);
    $competitions2 = new ArrayCollection();
    $tournament2->method('getCompetitions')->willReturn($competitions2);
    /** @var TournamentInterface $tournament2 */
    $competition->setTournament($tournament2);

    self::assertEquals($tournament2, $competition->getTournament());
    self::assertEquals($competition->getTournament(), $competition->getParent());
    self::assertEquals(1, $competition->getTournament()->getCompetitions()->count());
    self::assertEquals(0, $tournament->getCompetitions()->count());
    self::assertEquals($competition, $competition->getTournament()->getCompetitions()[$competition->getId()]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return CompetitionInterface|MockObject a new competition
   */
  private function competition(): MockObject
  {
    return $this->getStubbedTournamentHierarchyEntity("Competition", ["getId" => "id"]);
  }
//</editor-fold desc="Private Methods">
}