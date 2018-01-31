<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tfboe\FmLib\Entity\CompetitionInterface;
use Tfboe\FmLib\Entity\TeamInterface;
use Tfboe\FmLib\Tests\Entity\Player;
use Tfboe\FmLib\Tests\Entity\Team;
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
    self::assertEquals($team, $team->getCompetition()->getTeams()[$team->getStartNumber()]);

    $competition2 = $this->createStub(CompetitionInterface::class, ['getTeams' => new ArrayCollection()]);

    /** @var CompetitionInterface $competition2 */
    $team->setCompetition($competition2);
    self::assertEquals($competition2, $team->getCompetition());
    self::assertEquals(1, $team->getCompetition()->getTeams()->count());
    self::assertEquals(0, $competition->getTeams()->count());
    self::assertEquals($team, $team->getCompetition()->getTeams()[$team->getStartNumber()]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Team::init
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::getName
   * @uses   \Tfboe\FmLib\Entity\Traits\Team::getPlayers
   */
  public function testConstructor()
  {
    $team = $this->team();
    self::assertInstanceOf(Team::class, $team);
    self::assertInstanceOf(Collection::class, $team->getPlayers());
    self::assertEquals(0, $team->getPlayers()->count());
    self::assertEquals("", $team->getName());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Team::getPlayers
   * @uses   \Tfboe\FmLib\Entity\Traits\Team::init
   */
  public function testPlayers()
  {
    $team = $this->team();
    /** @var Player $player */
    $player = $this->createStubWithId(Player::class, 1, 'getPlayerId');
    $team->getPlayers()->set($player->getPlayerId(), $player);
    self::assertEquals(1, $team->getPlayers()->count());
    self::assertEquals($player, $team->getPlayers()[$player->getPlayerId()]);
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
  /**
   * @return TeamInterface a new team
   */
  private function team(): TeamInterface
  {
    return new Team();
  }
//</editor-fold desc="Private Methods">
}