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
use Tfboe\FmLib\Entity\MatchInterface;
use Tfboe\FmLib\Helpers\Level;
use Tfboe\FmLib\Tests\Entity\Player;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;


/**
 * Class GameTest
 * @package Tfboe\FmLib\Tests\Unit\Entity
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class GameTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Game::getChildren
   * @uses   \Tfboe\FmLib\Entity\Traits\Game::init
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::resultInit
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testChildren()
  {
    $game = $this->game();
    self::callProtectedMethod($game, 'init');
    self::assertEmpty($game->getChildren());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Game::setGameNumber
   * @covers \Tfboe\FmLib\Entity\Traits\Game::getGameNumber
   * @covers \Tfboe\FmLib\Entity\Traits\Game::getLocalIdentifier
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::resultInit
   * @uses   \Tfboe\FmLib\Entity\Traits\Game::init
   */
  public function testGameNumberAndLocalIdentifier()
  {
    $game = $this->game();
    $gameNumber = 1;
    $game->setGameNumber($gameNumber);
    self::assertEquals($gameNumber, $game->getGameNumber());
    self::assertEquals($game->getGameNumber(), $game->getLocalIdentifier());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Game::init
   * @uses   \Tfboe\FmLib\Entity\Traits\Game::getPlayersB
   * @uses   \Tfboe\FmLib\Entity\Traits\Game::getPlayersA
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::resultInit
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testInit()
  {
    $game = $this->game();
    self::callProtectedMethod($game, 'init');
    self::assertInstanceOf(Collection::class, $game->getPlayersA());
    self::assertInstanceOf(Collection::class, $game->getPlayersB());
    self::assertEquals(0, $game->getPlayersA()->count());
    self::assertEquals(0, $game->getPlayersB()->count());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Game::getLevel
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::resultInit
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\Game::init
   */
  public function testLevel()
  {
    self::assertEquals(Level::GAME, $this->game()->getLevel());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Game::setMatch
   * @covers \Tfboe\FmLib\Entity\Traits\Game::getMatch
   * @covers \Tfboe\FmLib\Entity\Traits\Game::getParent
   * @uses   \Tfboe\FmLib\Entity\Traits\Game::setGameNumber
   * @uses   \Tfboe\FmLib\Entity\Traits\Game::getGameNumber
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::resultInit
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\Game::init
   */
  public function testMatchAndParent()
  {
    $game = $this->game();
    $match = $this->createMock(MatchInterface::class);
    $games = new ArrayCollection();
    $match->method('getGames')->willReturn($games);
    $game->setGameNumber(1);

    /** @var MatchInterface $match */
    $game->setMatch($match);
    self::assertEquals($match, $game->getMatch());
    self::assertEquals(1, $game->getMatch()->getGames()->count());
    self::assertEquals($game, $game->getMatch()->getGames()[$game->getId()]);
    self::assertEquals($game->getMatch(), $game->getParent());

    $match2 = $this->createMock(MatchInterface::class);
    $games2 = new ArrayCollection();
    $match2->method('getGames')->willReturn($games2);

    /** @var MatchInterface $match2 */
    $game->setMatch($match2);
    self::assertEquals($match2, $game->getMatch());
    self::assertEquals(1, $game->getMatch()->getGames()->count());
    self::assertEquals(0, $match->getGames()->count());
    self::assertEquals($game, $game->getMatch()->getGames()[$game->getId()]);
    self::assertEquals($game->getMatch(), $game->getParent());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Game::getPlayersA
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\Game::init
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::resultInit
   */
  public function testPlayersA()
  {
    $game = $this->game();
    self::callProtectedMethod($game, 'init');
    /** @var Player $player */
    $player = $this->createStubWithId(Player::class, 1, 'getId');
    $game->getPlayersA()->set($player->getId(), $player);
    self::assertEquals(1, $game->getPlayersA()->count());
    self::assertEquals($player, $game->getPlayersA()[$player->getId()]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Game::getPlayersB
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\Game::init
   * @uses   \Tfboe\FmLib\Entity\Traits\Match::resultInit
   */
  public function testPlayersB()
  {
    $game = $this->game();
    self::callProtectedMethod($game, 'init');
    /** @var Player $player */
    $player = $this->createStubWithId(Player::class, 1, 'getId');
    $game->getPlayersB()->set($player->getId(), $player);
    self::assertEquals(1, $game->getPlayersB()->count());
    self::assertEquals($player, $game->getPlayersB()[$player->getId()]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return GameInterface|MockObject a new game
   */
  private function game(): MockObject
  {
    return $this->getStubbedTournamentHierarchyEntity("Game", ["getId" => "id"]);
  }
//</editor-fold desc="Private Methods">
}