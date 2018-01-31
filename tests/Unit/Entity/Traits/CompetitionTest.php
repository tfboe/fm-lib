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
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::getName
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::setName
   */
  public function testGetLocalIdentifier()
  {
    $entity = $this->competition();
    $entity->setName("Name");
    self::assertEquals($entity->getName(), $entity->getLocalIdentifier());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Competition::init
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition::getPhases
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition::getTeams
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
   */
  public function testLevel()
  {
    self::assertEquals(Level::COMPETITION, $this->competition()->getLevel());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Competition::getPhases
   * @covers \Tfboe\FmLib\Entity\Traits\Competition::getChildren
   * @uses   \Tfboe\FmLib\Entity\Traits\Phase
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition::init
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition::init
   */
  public function testPhasesAndChildren()
  {
    $competition = $this->competition();
    self::callProtectedMethod($competition, 'init');
    /** @var Phase $phase */
    $phase = $this->getMockForTrait(Phase::class);
    $phase->setPhaseNumber(1);
    self::assertEquals($competition->getPhases(), $competition->getChildren());
    $competition->getPhases()->set($phase->getPhaseNumber(), $phase);
    self::assertEquals(1, $competition->getPhases()->count());
    self::assertEquals($phase, $competition->getPhases()[1]);
    self::assertEquals($competition->getPhases(), $competition->getChildren());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Competition::getTeams
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition::init
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\Team
   */
  public function testTeams()
  {
    $competition = $this->competition();
    self::callProtectedMethod($competition, 'init');
    $team = new Team();
    $team->setStartNumber(1);
    $competition->getTeams()->set($team->getStartNumber(), $team);
    self::assertEquals(1, $competition->getTeams()->count());
    self::assertEquals($team, $competition->getTeams()[1]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Competition::setTournament()
   * @covers \Tfboe\FmLib\Entity\Traits\Competition::getTournament()
   * @covers \Tfboe\FmLib\Entity\Traits\Competition::getParent()
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::getName
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::setName
   */
  public function testTournamentAndParent()
  {
    $competition = $this->competition();
    $tournament = $this->createMock(TournamentInterface::class);
    $competitions = new ArrayCollection();
    $tournament->method('getCompetitions')->willReturn($competitions);
    $competition->setName('test competition');

    /** @var TournamentInterface $tournament */
    $competition->setTournament($tournament);
    self::assertEquals($tournament, $competition->getTournament());
    self::assertEquals($competition->getTournament(), $competition->getParent());
    self::assertEquals(1, $competition->getTournament()->getCompetitions()->count());
    self::assertEquals($competition, $competition->getTournament()->getCompetitions()[$competition->getName()]);

    $tournament2 = $this->createMock(TournamentInterface::class);
    $competitions2 = new ArrayCollection();
    $tournament2->method('getCompetitions')->willReturn($competitions2);
    /** @var TournamentInterface $tournament2 */
    $competition->setTournament($tournament2);

    self::assertEquals($tournament2, $competition->getTournament());
    self::assertEquals($competition->getTournament(), $competition->getParent());
    self::assertEquals(1, $competition->getTournament()->getCompetitions()->count());
    self::assertEquals(0, $tournament->getCompetitions()->count());
    self::assertEquals($competition, $competition->getTournament()->getCompetitions()[$competition->getName()]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return Competition|MockObject a new competition
   */
  private function competition(): MockObject
  {
    return $this->getMockForTrait(Competition::class);
  }
//</editor-fold desc="Private Methods">
}