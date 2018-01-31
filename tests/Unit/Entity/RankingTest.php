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
use Tfboe\FmLib\Entity\PhaseInterface;
use Tfboe\FmLib\Entity\RankingInterface;
use Tfboe\FmLib\Tests\Entity\Ranking;
use Tfboe\FmLib\Tests\Entity\Team;
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
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::getName
   * @uses   \Tfboe\FmLib\Entity\Traits\Ranking::getTeams
   */
  public function testConstructor()
  {
    $ranking = $this->ranking();
    self::assertInstanceOf(Ranking::class, $ranking);
    self::assertEquals('', $ranking->getName());
    self::assertInstanceOf(Collection::class, $ranking->getTeams());
    self::assertEquals(0, $ranking->getTeams()->count());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Ranking::setPhase
   * @covers \Tfboe\FmLib\Entity\Traits\Ranking::getPhase
   * @uses   \Tfboe\FmLib\Entity\Traits\Ranking::init
   * @uses   \Tfboe\FmLib\Entity\Traits\Ranking::getUniqueRank
   * @uses   \Tfboe\FmLib\Entity\Traits\Ranking::setUniqueRank
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testPhase()
  {
    $ranking = $this->ranking();
    $phase = $this->createStub(PhaseInterface::class, ['getRankings' => new ArrayCollection()]);
    $ranking->setUniqueRank(1);
    /** @var PhaseInterface $phase */
    $ranking->setPhase($phase);
    self::assertEquals($phase, $ranking->getPhase());
    self::assertEquals(1, $ranking->getPhase()->getRankings()->count());
    self::assertEquals($ranking, $ranking->getPhase()->getRankings()[$ranking->getUniqueRank()]);

    $phase2 = $this->createStub(PhaseInterface::class, ['getRankings' => new ArrayCollection()]);

    /** @var PhaseInterface $phase2 */
    $ranking->setPhase($phase2);
    self::assertEquals($phase2, $ranking->getPhase());
    self::assertEquals(1, $ranking->getPhase()->getRankings()->count());
    self::assertEquals(0, $phase->getRankings()->count());
    self::assertEquals($ranking, $ranking->getPhase()->getRankings()[$ranking->getUniqueRank()]);
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
   * @uses   \Tfboe\FmLib\Entity\Traits\Ranking::init
   * @uses   \Tfboe\FmLib\Entity\Traits\Team
   */
  public function testTeams()
  {
    $ranking = $this->ranking();
    $team = new Team();
    $team->setStartNumber(1);
    $ranking->getTeams()->set($team->getStartNumber(), $team);
    self::assertEquals(1, $ranking->getTeams()->count());
    self::assertEquals($team, $ranking->getTeams()[1]);
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
   * @return RankingInterface a new ranking
   */
  private function ranking(): RankingInterface
  {
    return new Ranking();
  }
//</editor-fold desc="Private Methods">
}