<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 3:53 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Service\RankingSystem;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Facades\Config;
use Tfboe\FmLib\Entity\Helpers\Result;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\RankingSystemChangeInterface;
use Tfboe\FmLib\Entity\RankingSystemListEntryInterface;
use Tfboe\FmLib\Entity\RankingSystemListInterface;
use Tfboe\FmLib\Entity\Traits\RankingSystemChange;
use Tfboe\FmLib\Service\ObjectCreatorServiceInterface;
use Tfboe\FmLib\Service\RankingSystem\EloRanking;
use Tfboe\FmLib\Service\RankingSystem\EntityComparerInterface;
use Tfboe\FmLib\Service\RankingSystem\TimeServiceInterface;
use Tfboe\FmLib\Tests\Entity\Game;
use Tfboe\FmLib\Tests\Entity\Player;
use Tfboe\FmLib\Tests\Entity\RankingSystemList;
use Tfboe\FmLib\Tests\Entity\RankingSystemListEntry;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class EloRankingTest
 * @packageTfboe\FmLib\Tests\Unit\Service\RankingSystemService
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EloRankingTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * Provides data for all elo changes tests
   * @return array
   */
  public function providerEloChanges()
  {
    return [
      [false, Result::TEAM_A_WINS, [
        ["points" => 1501.0, "rated" => 53, "played" => 74, "ranked" => 102, "pointChange" => 0.0,
          "ratedGamesChange" => 0, "playedChange" => 0, "teamElo" => 0.0, "opponentElo" => 0.0],
        ["points" => 1300.0, "rated" => 20, "played" => 32, "ranked" => 26, "pointChange" => 0.0,
          "ratedGamesChange" => 0, "playedChange" => 0, "teamElo" => 0.0, "opponentElo" => 0.0],
        ["points" => 1450.0, "rated" => 100, "played" => 100, "ranked" => 100, "pointChange" => 0.0,
          "ratedGamesChange" => 0, "playedChange" => 0, "teamElo" => 0.0, "opponentElo" => 0.0],
        ["points" => 1200.0, "rated" => 60, "played" => 70, "ranked" => 75, "pointChange" => 0.0,
          "ratedGamesChange" => 0, "playedChange" => 0, "teamElo" => 0.0, "opponentElo" => 0.0],
      ]],
      [true, Result::TEAM_A_WINS, [
        ["points" => 1501.0, "rated" => 53, "played" => 74, "ranked" => 102, "pointChange" => 7.8605068927035,
          "ratedGamesChange" => 1, "playedChange" => 1, "teamElo" => 1400.5, "opponentElo" => 1325.0],
        ["points" => 1300.0, "rated" => 20, "played" => 32, "ranked" => 26, "pointChange" => 7.8605068927035,
          "ratedGamesChange" => 1, "playedChange" => 1, "teamElo" => 1400.5, "opponentElo" => 1325.0],
        ["points" => 1450.0, "rated" => 100, "played" => 100, "ranked" => 100, "pointChange" => -7.8605068927035,
          "ratedGamesChange" => 1, "playedChange" => 1, "teamElo" => 1325.0, "opponentElo" => 1400.5],
        ["points" => 1200.0, "rated" => 60, "played" => 70, "ranked" => 75, "pointChange" => 0.0,
          "ratedGamesChange" => 1, "playedChange" => 1, "teamElo" => 1325.0, "opponentElo" => 1400.5],
      ]],
      [true, Result::TEAM_B_WINS, [
        ["points" => 1501.0, "rated" => 53, "played" => 74, "ranked" => 102, "pointChange" => -12.139493107296,
          "ratedGamesChange" => 1, "playedChange" => 1, "teamElo" => 1400.5, "opponentElo" => 1325.0],
        ["points" => 1300.0, "rated" => 20, "played" => 32, "ranked" => 26, "pointChange" => -12.139493107296,
          "ratedGamesChange" => 1, "playedChange" => 1, "teamElo" => 1400.5, "opponentElo" => 1325.0],
        ["points" => 1450.0, "rated" => 100, "played" => 100, "ranked" => 100, "pointChange" => 12.139493107296,
          "ratedGamesChange" => 1, "playedChange" => 1, "teamElo" => 1325.0, "opponentElo" => 1400.5],
        ["points" => 1200.0, "rated" => 60, "played" => 70, "ranked" => 75, "pointChange" => 12.139493107296,
          "ratedGamesChange" => 1, "playedChange" => 1, "teamElo" => 1325.0, "opponentElo" => 1400.5],
      ]],
      [true, Result::DRAW, [
        ["points" => 1501.0, "rated" => 53, "played" => 74, "ranked" => 102, "pointChange" => -2.1394931072965,
          "ratedGamesChange" => 1, "playedChange" => 1, "teamElo" => 1400.5, "opponentElo" => 1325.0],
        ["points" => 1300.0, "rated" => 20, "played" => 32, "ranked" => 26, "pointChange" => -2.1394931072965,
          "ratedGamesChange" => 1, "playedChange" => 1, "teamElo" => 1400.5, "opponentElo" => 1325.0],
        ["points" => 1450.0, "rated" => 100, "played" => 100, "ranked" => 100, "pointChange" => 2.1394931072965,
          "ratedGamesChange" => 1, "playedChange" => 1, "teamElo" => 1325.0, "opponentElo" => 1400.5],
        ["points" => 1200.0, "rated" => 60, "played" => 70, "ranked" => 75, "pointChange" => 2.1394931072965,
          "ratedGamesChange" => 1, "playedChange" => 1, "teamElo" => 1325.0, "opponentElo" => 1400.5],
      ]],
      [true, Result::NULLED, [
        ["points" => 1501.0, "rated" => 53, "played" => 74, "ranked" => 102, "pointChange" => 0.0,
          "ratedGamesChange" => 0, "playedChange" => 0, "teamElo" => 0.0, "opponentElo" => 0.0],
        ["points" => 1300.0, "rated" => 20, "played" => 32, "ranked" => 26, "pointChange" => 0.0,
          "ratedGamesChange" => 0, "playedChange" => 0, "teamElo" => 0.0, "opponentElo" => 0.0],
        ["points" => 1450.0, "rated" => 100, "played" => 100, "ranked" => 100, "pointChange" => 0.0,
          "ratedGamesChange" => 0, "playedChange" => 0, "teamElo" => 0.0, "opponentElo" => 0.0],
        ["points" => 1200.0, "rated" => 60, "played" => 70, "ranked" => 75, "pointChange" => 0.0,
          "ratedGamesChange" => 0, "playedChange" => 0, "teamElo" => 0.0, "opponentElo" => 0.0],
      ]],
      [true, Result::NOT_YET_FINISHED, [
        ["points" => 1501.0, "rated" => 53, "played" => 74, "ranked" => 102, "pointChange" => 0.0,
          "ratedGamesChange" => 0, "playedChange" => 0, "teamElo" => 0.0, "opponentElo" => 0.0],
        ["points" => 1300.0, "rated" => 20, "played" => 32, "ranked" => 26, "pointChange" => 0.0,
          "ratedGamesChange" => 0, "playedChange" => 0, "teamElo" => 0.0, "opponentElo" => 0.0],
        ["points" => 1450.0, "rated" => 100, "played" => 100, "ranked" => 100, "pointChange" => 0.0,
          "ratedGamesChange" => 0, "playedChange" => 0, "teamElo" => 0.0, "opponentElo" => 0.0],
        ["points" => 1200.0, "rated" => 60, "played" => 70, "ranked" => 75, "pointChange" => 0.0,
          "ratedGamesChange" => 0, "playedChange" => 0, "teamElo" => 0.0, "opponentElo" => 0.0],
      ]],
      [true, Result::TEAM_A_WINS, [
        ["points" => 0.0, "rated" => 15, "played" => 19, "ranked" => 20, "pointChange" => 1515.78125,
          "ratedGamesChange" => 1, "playedChange" => 1, "provisoryRanking" => 1501.0, "provisoryChange" => 14.78125,
          "teamElo" => 0.0, "opponentElo" => 0.0],
        ["points" => 1300.0, "rated" => 20, "played" => 20, "ranked" => 26, "pointChange" => 0.0,
          "ratedGamesChange" => 0, "playedChange" => 1, "teamElo" => 0.0, "opponentElo" => 0.0],
        ["points" => 1450.0, "rated" => 100, "played" => 100, "ranked" => 100, "pointChange" => 0.0,
          "ratedGamesChange" => 0, "playedChange" => 1, "teamElo" => 0.0, "opponentElo" => 0.0],
        ["points" => 0.0, "rated" => 10, "played" => 10, "ranked" => 15, "pointChange" => 0.0,
          "ratedGamesChange" => 1, "playedChange" => 1, "provisoryRanking" => 1200.0,
          "provisoryChange" => -20.386363636364, "teamElo" => 0.0, "opponentElo" => 0.0],
      ]],
      [true, Result::TEAM_A_WINS, [
        ["points" => 0.0, "rated" => 15, "played" => 15, "ranked" => 20, "pointChange" => 0.0,
          "ratedGamesChange" => 1, "playedChange" => 1, "provisoryRanking" => 1501.0, "provisoryChange" => 6.1875,
          "teamElo" => 0.0, "opponentElo" => 0.0],
        ["points" => 2000.0, "rated" => 20, "played" => 20, "ranked" => 26, "pointChange" => 0.0,
          "ratedGamesChange" => 0, "playedChange" => 1, "teamElo" => 0.0, "opponentElo" => 0.0],
        ["points" => 1200.0, "rated" => 100, "played" => 100, "ranked" => 100, "pointChange" => 0.0,
          "ratedGamesChange" => 0, "playedChange" => 1, "teamElo" => 0.0, "opponentElo" => 0.0],
        ["points" => 0.0, "rated" => 10, "played" => 10, "ranked" => 15, "pointChange" => 0.0,
          "ratedGamesChange" => 1, "playedChange" => 1, "provisoryRanking" => 1200.0,
          "provisoryChange" => 31.863636363636, "teamElo" => 0.0, "opponentElo" => 0.0],
      ]],
      [true, Result::TEAM_A_WINS, [
        ["points" => 1501.0, "rated" => 53, "played" => 74, "ranked" => 102, "pointChange" => 4.7840781802172,
          "ratedGamesChange" => 1, "playedChange" => 1, "teamElo" => 1501.0, "opponentElo" => 1300.0],
        ["points" => 1300.0, "rated" => 20, "played" => 32, "ranked" => 26, "pointChange" => -4.7840781802172,
          "ratedGamesChange" => 1, "playedChange" => 1, "teamElo" => 1300.0, "opponentElo" => 1501.0],
      ]],
      [true, Result::TEAM_A_WINS, [
        ["points" => 0.0, "rated" => 15, "played" => 15, "ranked" => 20, "pointChange" => 0.0,
          "ratedGamesChange" => 1, "playedChange" => 1, "provisoryRanking" => 1501.0, "provisoryChange" => 12.4375,
          "teamElo" => 0.0, "opponentElo" => 1300.0],
        ["points" => 1300.0, "rated" => 20, "played" => 20, "ranked" => 26, "pointChange" => 0.0,
          "ratedGamesChange" => 0, "playedChange" => 1, "teamElo" => 1300.0, "opponentElo" => 0.0],
      ]],
    ];
  }

  /**
   * @dataProvider providerEloChanges
   * @covers       \Tfboe\FmLib\Service\RankingSystem\EloRanking::getChanges
   * @covers       \Tfboe\FmLib\Service\RankingSystem\EloRanking::computeChanges
   * @covers       \Tfboe\FmLib\Service\RankingSystem\EloRanking::getEloAverage
   * @covers       \Tfboe\FmLib\Service\RankingSystem\EloRanking::hasProvisoryEntry
   * @covers       \Tfboe\FmLib\Service\RankingSystem\EloRanking::addNotRatedChanges
   * @covers       \Tfboe\FmLib\Service\RankingSystem\EloRanking::getAdditionalChangeFields
   * @covers       \Tfboe\FmLib\Service\RankingSystem\EloRanking::getAdditionalFields
   * @covers       \Tfboe\FmLib\Service\RankingSystem\RankingSystemService::getAdditionalChangeFields
   * @covers       \Tfboe\FmLib\Service\RankingSystem\RankingSystemService::getOrCreateChange
   * @param bool $isPlayed if game was played
   * @param int $gameResult the game result
   * @param array $playerInfos all infos about each player and its expected changes
   * @uses         \Tfboe\FmLib\Entity\Helpers\SubClassData::__call
   * @uses         \Tfboe\FmLib\Entity\Helpers\SubClassData::getProperty
   * @uses         \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses         \Tfboe\FmLib\Entity\Helpers\SubClassData::setProperty
   * @uses         \Tfboe\FmLib\Entity\Traits\RankingSystemChange
   * @uses         \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry
   * @uses         \Tfboe\FmLib\Service\RankingSystem\RankingSystemService::__construct
   * @uses         \Tfboe\FmLib\Service\RankingSystem\RankingSystemService::getEntriesOfPlayers
   * @uses         \Tfboe\FmLib\Service\RankingSystem\RankingSystemService::getOrCreateRankingSystemListEntry
   * @SuppressWarnings(PHPMD.CyclomaticComplexity)
   */
  public function testGetChanges(bool $isPlayed, int $gameResult, array $playerInfos)
  {
    $repository = $this->getStub(ObjectRepository::class, ['findBy' => []]);
    /** @var EntityManagerInterface $entityManager */
    $entityManager = $this->getStub(EntityManagerInterface::class, ['getRepository' => $repository]);
    $service = $this->service($entityManager, $this->getObjectCreator());
    /** @var EloRanking $player1 */

    /** @var Player[] $players */
    $players = [];
    $playersAArray = [];
    $playersBArray = [];
    $numPlayerInfos = count($playerInfos);
    for ($i = 0; $i < $numPlayerInfos; $i++) {
      $players[$i] = $this->getStub(Player::class, ['getId' => $i]);
      if ($i < $numPlayerInfos / 2) {
        $playersAArray[] = $players[$i];
      } else {
        $playersBArray[] = $players[$i];
      }
    }

    $playersA = new ArrayCollection($playersAArray);
    $playersB = new ArrayCollection($playersBArray);
    $game = $this->getStub(Game::class, [
      'getPlayersA' => $playersA,
      'getPlayersB' => $playersB,
    ]);

    $entriesArray = [];
    for ($i = 0; $i < $numPlayerInfos; $i++) {
      $entriesArray[$i] = $this->getRankingSystemListEntry($service, $players[$i]);
    }
    $entries = new ArrayCollection($entriesArray);
    $list = $this->getStub(RankingSystemList::class, ['getEntries' => $entries]);
    /** @var RankingSystemListInterface $list */
    foreach ($entries as $entry) {
      /** @var RankingSystemListEntryInterface $entry */
      $entry->setRankingSystemList($list);
    }
    for ($i = 0; $i < $numPlayerInfos; $i++) {
      $info = $playerInfos[$i];
      $entry = $list->getEntries()[$i];
      $entry->setPoints($info['points']);
      $entry->setPlayedGames($info['played']);
      $entry->setNumberRankedEntities($info['ranked']);
      $entry->setRatedGames($info['rated']);
      if (array_key_exists('provisoryRanking', $info)) {
        $list->getEntries()[$i]->setProvisoryRanking($info['provisoryRanking']);
      }
    }
    $game->method('isPlayed')->willReturn($isPlayed);
    $game->method('getResult')->willReturn($gameResult);

    /** @var RankingSystemChangeInterface[] $changes */
    $changes = static::callProtectedMethod($service, 'getChanges', [$game, $list]);
    self::assertEquals(count($playerInfos), count($changes));
    foreach ($players as $player) {
      $exists = false;
      foreach ($changes as $change) {
        if ($change->getPlayer() === $player) {
          $exists = true;
          break;
        }
      }
      self::assertTrue($exists);
    }
    /** @var Game $game */
    $this->assertChanges($changes, $playerInfos, $game);
  }

  /**
   * @covers \Tfboe\FmLib\Service\RankingSystem\EloRanking::startPoints
   * @uses   \Tfboe\FmLib\Service\RankingSystem\RankingSystemService::__construct
   */
  public function testStartPoints()
  {
    $service = $this->service();
    $startPoints = static::callProtectedMethod($service, 'startPoints', []);
    self::assertEquals(0.0, $startPoints);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @param RankingSystemChangeInterface[]|RankingSystemChange $changes
   * @param array $playerInfos
   * @param TournamentHierarchyEntity $entity
   */
  private function assertChanges(array $changes, array $playerInfos, TournamentHierarchyEntity $entity)
  {
    for ($i = 0, $c = count($changes); $i < $c; $i++) {
      $change = $changes[$i];
      self::assertEquals($entity, $change->getHierarchyEntity());
      self::assertEqualsWithDelta($playerInfos[$i]["pointChange"], $change->getPointsChange(), 0.01);
      self::assertEquals($playerInfos[$i]["ratedGamesChange"], $change->getRatedGames());
      self::assertEquals($playerInfos[$i]["playedChange"], $change->getPlayedGames());
      self::assertEqualsWithDelta(
        array_key_exists('provisoryChange', $playerInfos[$i]) ? $playerInfos[$i]['provisoryChange'] : 0,
        $change->getProvisoryRanking(), 0.01);
      self::assertEquals($playerInfos[$i]["teamElo"], $change->getTeamElo());
      self::assertEquals($playerInfos[$i]["opponentElo"], $change->getOpponentElo());
    }
  }

  /**
   * Creates a new ranking system list entry
   * @param EloRanking $service the elo ranking service to get additional fields
   * @param Player $player the player to use for the entry
   * @return RankingSystemListEntryInterface the created ranking system list entry
   */
  private function getRankingSystemListEntry(EloRanking $service, Player $player)
  {
    $entry = new RankingSystemListEntry(array_keys(static::callProtectedMethod($service, 'getAdditionalFields')));
    $entry->setPlayer($player);
    return $entry;
  }

  /**
   * Gets an elo ranking service
   * @param EntityManagerInterface|null $entityManager
   * @param null|ObjectCreatorServiceInterface $objectCreatorService
   * @return EloRanking
   */
  private function service(?EntityManagerInterface $entityManager = null,
                           ?ObjectCreatorServiceInterface $objectCreatorService = null)
  {
    if ($entityManager === null) {
      $entityManager = $this->createMock(EntityManagerInterface::class);
    }
    if ($objectCreatorService === null) {
      $objectCreatorService = $this->createMock(ObjectCreatorServiceInterface::class);
    }
    Config::shouldReceive('get')
      ->once()
      ->with('fm-lib.doFlushAndForgetInRankingCalculations', true)
      ->andReturn(true);
    $eloRanking = new EloRanking(
      $entityManager, $this->createMock(TimeServiceInterface::class),
      $this->createMock(EntityComparerInterface::class), $objectCreatorService
    );
    Config::get('fm-lib.doFlushAndForgetInRankingCalculations', true);
    return $eloRanking;
  }
//</editor-fold desc="Private Methods">
}