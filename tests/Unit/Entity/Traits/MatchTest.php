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
use Tfboe\FmLib\Entity\GameInterface;
use Tfboe\FmLib\Entity\PhaseInterface;
use Tfboe\FmLib\Entity\Traits\Match;
use Tfboe\FmLib\Helpers\Level;
use Tfboe\FmLib\Tests\Entity\Ranking;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tfboe\FmLib\Tests\Unit\Entity
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class MatchTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Match::getGames
   * @covers \Tfboe\FmLib\Entity\Traits\Match::getChildren
   * @uses   \Tfboe\FmLib\Entity\GameInterface
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::init
   */
  public function testGamesAndChildren()
  {
    $match = $this->match();
    self::callProtectedMethod($match, 'init');
    $game = $this->createMock(GameInterface::class);
    $game->method('getGameNumber')->willReturn(1);
    /** @var GameInterface $game */
    self::assertEquals($match->getGames(), $match->getChildren());
    $match->getGames()->set($game->getGameNumber(), $game);
    self::assertEquals(1, $match->getGames()->count());
    self::assertEquals($game, $match->getGames()[1]);
    self::assertEquals($match->getGames(), $match->getChildren());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Match::init
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::getGames
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::getRankingsA
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::getRankingsB
   */
  public function testInit()
  {
    $match = $this->match();
    self::callProtectedMethod($match, 'init');
    self::assertInstanceOf(Collection::class, $match->getRankingsA());
    self::assertInstanceOf(Collection::class, $match->getRankingsB());
    self::assertInstanceOf(Collection::class, $match->getGames());
    self::assertEquals(0, $match->getRankingsA()->count());
    self::assertEquals(0, $match->getRankingsB()->count());
    self::assertEquals(0, $match->getGames()->count());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Match::getLevel
   */
  public function testLevel()
  {
    self::assertEquals(Level::MATCH, $this->match()->getLevel());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Match::setMatchNumber
   * @covers \Tfboe\FmLib\Entity\Traits\Match::getMatchNumber
   * @covers \Tfboe\FmLib\Entity\Traits\Match::getLocalIdentifier
   */
  public function testMatchNumberAndLocalIdentifier()
  {
    $match = $this->match();
    $matchNumber = 1;
    $match->setMatchNumber($matchNumber);
    self::assertEquals($matchNumber, $match->getMatchNumber());
    self::assertEquals($match->getMatchNumber(), $match->getLocalIdentifier());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Match::setPhase
   * @covers \Tfboe\FmLib\Entity\Traits\Match::getPhase
   * @covers \Tfboe\FmLib\Entity\Traits\Match::getParent
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::getMatchNumber
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::setMatchNumber
   */
  public function testPhaseAndParent()
  {
    $match = $this->match();
    $phase = $this->createStub(PhaseInterface::class, ["getMatches" => new ArrayCollection()]);
    $match->setMatchNumber(1);
    /** @var PhaseInterface $phase */
    $match->setPhase($phase);
    self::assertEquals($phase, $match->getPhase());
    self::assertEquals(1, $match->getPhase()->getMatches()->count());
    self::assertEquals($match, $match->getPhase()->getMatches()[$match->getMatchNumber()]);
    self::assertEquals($match->getPhase(), $match->getParent());

    $phase2 = $this->createStub(PhaseInterface::class, ["getMatches" => new ArrayCollection()]);

    /** @var PhaseInterface $phase2 */
    $match->setPhase($phase2);
    self::assertEquals($phase2, $match->getPhase());
    self::assertEquals(1, $match->getPhase()->getMatches()->count());
    self::assertEquals(0, $phase->getMatches()->count());
    self::assertEquals($match, $match->getPhase()->getMatches()[$match->getMatchNumber()]);
    self::assertEquals($match->getPhase(), $match->getParent());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Match::getRankingsA
   * @uses   \Tfboe\FmLib\Entity\Traits\Ranking
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::init
   */
  public function testRankingsA()
  {
    $match = $this->match();
    self::callProtectedMethod($match, 'init');
    $ranking = new Ranking();
    $ranking->setUniqueRank(1);
    $match->getRankingsA()->set($ranking->getUniqueRank(), $ranking);
    self::assertEquals(1, $match->getRankingsA()->count());
    self::assertEquals($ranking, $match->getRankingsA()[1]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Match::getRankingsB
   * @uses   \Tfboe\FmLib\Entity\Traits\Ranking
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::init
   */
  public function testRankingsB()
  {
    $match = $this->match();
    self::callProtectedMethod($match, 'init');
    $ranking = new Ranking();
    $ranking->setUniqueRank(1);
    $match->getRankingsB()->set($ranking->getUniqueRank(), $ranking);
    self::assertEquals(1, $match->getRankingsB()->count());
    self::assertEquals($ranking, $match->getRankingsB()[1]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return Match|MockObject a new match
   */
  private function match(): MockObject
  {
    return $this->getMockForTrait(Match::class);
  }
//</editor-fold desc="Private Methods">
}