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
use Tfboe\FmLib\Entity\PhaseInterface;
use Tfboe\FmLib\Entity\RankingInterface;
use Tfboe\FmLib\Entity\TeamInterface;
use Tfboe\FmLib\Entity\Traits\Ranking;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tfboe\FmLib\Tests\Unit\Entity
 */
class RankingTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Ranking::init
   * @uses   \Tfboe\FmLib\Entity\Traits\Ranking::getTeams
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::getName
   */
  public function testConstructor()
  {
    $ranking = $this->ranking();
    self::assertInstanceOf(RankingInterface::class, $ranking);
    self::assertEquals('', $ranking->getName());
    self::assertInstanceOf(Collection::class, $ranking->getTeams());
    self::assertEquals(0, $ranking->getTeams()->count());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Ranking::setPhase
   * @covers \Tfboe\FmLib\Entity\Traits\Ranking::getPhase
   * @uses   \Tfboe\FmLib\Entity\Traits\Ranking::getUniqueRank
   * @uses   \Tfboe\FmLib\Entity\Traits\Ranking::setUniqueRank
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\Ranking::init
   */
  public function testPhase()
  {
    $ranking = $this->ranking();
    $phase = $this->getStub(PhaseInterface::class, ['getRankings' => new ArrayCollection()]);
    $ranking->setUniqueRank(1);
    /** @var PhaseInterface $phase */
    $ranking->setPhase($phase);
    self::assertEquals($phase, $ranking->getPhase());
    self::assertEquals(1, $ranking->getPhase()->getRankings()->count());
    self::assertEquals($ranking, $ranking->getPhase()->getRankings()[$ranking->getId()]);

    $phase2 = $this->getStub(PhaseInterface::class, ['getRankings' => new ArrayCollection()]);

    /** @var PhaseInterface $phase2 */
    $ranking->setPhase($phase2);
    self::assertEquals($phase2, $ranking->getPhase());
    self::assertEquals(1, $ranking->getPhase()->getRankings()->count());
    self::assertEquals(0, $phase->getRankings()->count());
    self::assertEquals($ranking, $ranking->getPhase()->getRankings()[$ranking->getId()]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Ranking::setRank
   * @covers \Tfboe\FmLib\Entity\Traits\Ranking::getRank
   * @uses   \Tfboe\FmLib\Entity\Traits\Ranking::init
   */
  public function testRank()
  {
    $ranking = $this->ranking();
    $ranking->setRank(5);
    self::assertEquals(5, $ranking->getRank());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Ranking::getTeams
   * @uses   \Tfboe\FmLib\Entity\Traits\Team
   * @uses   \Tfboe\FmLib\Entity\Traits\Ranking::init
   */
  public function testTeams()
  {
    $ranking = $this->ranking();
    $team = $this->getStub(TeamInterface::class, ["getId" => "teamId"]);
    $team->setStartNumber(1);
    $ranking->getTeams()->set($team->getId(), $team);
    self::assertEquals(1, $ranking->getTeams()->count());
    self::assertEquals($team, $ranking->getTeams()[$team->getId()]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Ranking::setUniqueRank
   * @covers \Tfboe\FmLib\Entity\Traits\Ranking::getUniqueRank
   * @uses   \Tfboe\FmLib\Entity\Traits\Ranking::init
   */
  public function testUniqueRank()
  {
    $ranking = $this->ranking();
    $ranking->setUniqueRank(5);
    self::assertEquals(5, $ranking->getUniqueRank());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|Ranking a new ranking
   */
  private function ranking(): MockObject
  {
    return $this->getStubbedEntity("Ranking", ['getId' => 'id']);
  }
//</editor-fold desc="Private Methods">
}