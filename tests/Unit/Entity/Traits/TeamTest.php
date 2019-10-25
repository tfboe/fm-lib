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
use Tfboe\FmLib\Entity\TeamInterface;
use Tfboe\FmLib\Entity\TeamMembershipInterface;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tfboe\FmLib\Tests\Unit\Entity
 */
class TeamTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Team::setCompetition
   * @covers \Tfboe\FmLib\Entity\Traits\Team::getCompetition
   * @uses   \Tfboe\FmLib\Entity\Traits\Team::init
   * @uses   \Tfboe\FmLib\Entity\Traits\Team::getStartNumber
   * @uses   \Tfboe\FmLib\Entity\Traits\Team::setStartNumber
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testCompetition()
  {
    $team = $this->team();
    $competition = $this->createStub(CompetitionInterface::class, ['getTeams' => new ArrayCollection()]);
    $team->setStartNumber(1);
    /** @var CompetitionInterface $competition */
    $team->setCompetition($competition);
    self::assertEquals($competition, $team->getCompetition());
    self::assertEquals(1, $team->getCompetition()->getTeams()->count());
    self::assertEquals($team, $team->getCompetition()->getTeams()[$team->getId()]);

    $competition2 = $this->createStub(CompetitionInterface::class, ['getTeams' => new ArrayCollection()]);

    /** @var CompetitionInterface $competition2 */
    $team->setCompetition($competition2);
    self::assertEquals($competition2, $team->getCompetition());
    self::assertEquals(1, $team->getCompetition()->getTeams()->count());
    self::assertEquals(0, $competition->getTeams()->count());
    self::assertEquals($team, $team->getCompetition()->getTeams()[$team->getId()]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Team::init
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::getName
   * @uses   \Tfboe\FmLib\Entity\Traits\Team::getMemberships
   */
  public function testInit()
  {
    $team = $this->team();
    self::assertInstanceOf(TeamInterface::class, $team);
    self::assertInstanceOf(Collection::class, $team->getMemberships());
    self::assertEquals(0, $team->getMemberships()->count());
    self::assertEquals("", $team->getName());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Team::getMemberships
   * @uses   \Tfboe\FmLib\Entity\Traits\Team::init
   */
  public function testPlayers()
  {
    $team = $this->team();
    /** @var TeamMembershipInterface $membership */
    $membership = $this->createStubWithId(TeamMembershipInterface::class, 1, 'getId');
    $team->getMemberships()->set($membership->getId(), $membership);
    self::assertEquals(1, $team->getMemberships()->count());
    self::assertEquals($membership, $team->getMemberships()[$membership->getId()]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Team::setRank
   * @covers \Tfboe\FmLib\Entity\Traits\Team::getRank
   * @uses   \Tfboe\FmLib\Entity\Traits\Team::init
   */
  public function testRank()
  {
    $team = $this->team();
    $team->setRank(1);
    self::assertEquals(1, $team->getRank());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Team::setStartNumber
   * @covers \Tfboe\FmLib\Entity\Traits\Team::getStartNumber
   * @uses   \Tfboe\FmLib\Entity\Traits\Team::init
   */
  public function testStartNumber()
  {
    $team = $this->team();
    $team->setStartNumber(1);
    self::assertEquals(1, $team->getStartNumber());
  }

//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * @return TeamInterface|MockObject
   */
  private function team(): MockObject
  {
    /** @noinspection PhpUnhandledExceptionInspection */ //will never throw
    return $this->getStubbedEntity("Team", ["getId" => "id"]);
  }
//</editor-fold desc="Private Methods">
}