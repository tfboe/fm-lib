<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 3:53 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Service;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tfboe\FmLib\Entity\CompetitionInterface;
use Tfboe\FmLib\Entity\GameInterface;
use Tfboe\FmLib\Entity\MatchInterface;
use Tfboe\FmLib\Entity\PhaseInterface;
use Tfboe\FmLib\Entity\PlayerInterface;
use Tfboe\FmLib\Entity\TeamInterface;
use Tfboe\FmLib\Entity\TeamMembershipInterface;
use Tfboe\FmLib\Entity\TournamentInterface;
use Tfboe\FmLib\Service\DeletionServiceInterface;
use Tfboe\FmLib\Service\LoadingServiceInterface;
use Tfboe\FmLib\Service\PlayerService;
use Tfboe\FmLib\Service\PlayerServiceInterface;
use Tfboe\FmLib\Service\RankingSystemServiceInterface;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;


/**
 * Class PlayerServiceTest
 * @package Tfboe\FmLib\Tests\Unit\Service
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PlayerServiceTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Service\PlayerService::__construct
   */
  public function testConstruct()
  {
    /** @var EntityManagerInterface $em */
    $em = $this->createMock(EntityManagerInterface::class);
    /** @var LoadingServiceInterface $ls */
    $ls = $this->createMock(LoadingServiceInterface::class);
    /** @var RankingSystemServiceInterface $rankingSystemService */
    $rankingSystemService = $this->createMock(RankingSystemServiceInterface::class);
    $service = new PlayerService($em, $ls, $rankingSystemService);
    self::assertInstanceOf(PlayerService::class, $service);
  }

  /**
   * @covers \Tfboe\FmLib\Service\PlayerService::mergePlayers
   * @throws Exception
   * @uses   \Tfboe\FmLib\Service\PlayerService::__construct
   */
  public function testMergeBothPlayersInSameTournament()
  {
    /** @var PlayerInterface $player1 */
    $player1 = $this->createStubWithId(PlayerInterface::class, "p1");
    /** @var PlayerInterface $player2 */
    $player2 = $this->createStubWithId(PlayerInterface::class, "p2");
    $team1 = $this->getStub(TeamInterface::class, [
      'getMemberships' => new ArrayCollection([$this->getStub(TeamMembershipInterface::class, [
        'getPlayer' => $player1
      ])])
    ]);
    $team2 = $this->getStub(TeamInterface::class, [
      'getMemberships' => new ArrayCollection([$this->getStub(TeamMembershipInterface::class, [
        'getPlayer' => $player2
      ])])
    ]);

    $competition = $this->getStub(CompetitionInterface::class, [
      'getTeams' => new ArrayCollection([$team1, $team2])
    ]);

    $tournament = $this->getStub(TournamentInterface::class, [
      'getCompetitions' => new ArrayCollection([$competition]),
      'getName' => 'Tournament',
      'getId' => 't',
      'getStartTime' => new DateTime("2000-01-01")
    ]);

    /** @var EntityManagerInterface $em */
    $em = $this->getEntityManagerMockForQuery([$tournament], /** @lang DQL */
      "SELECT t FROM Tfboe\FmLib\Entity\TournamentInterface t INNER JOIN t.competitions c INNER JOIN c.teams te " .
      "INNER JOIN te.memberships m WHERE m.player = (:id)");

    /** @var LoadingServiceInterface $loadingService */
    $loadingService = $this->getStub(LoadingServiceInterface::class);
    /** @var RankingSystemServiceInterface $rankingSystemService */
    $rankingSystemService = $this->getStub(RankingSystemServiceInterface::class);
    /** @var PlayerServiceInterface $service */
    $service = new PlayerService($em,
      $loadingService,
      $rankingSystemService
    );

    self::assertEquals('Player 1 and player 2 both attended the tournament Tournament(01.01.2000 00:00, id=\'t\')',
      $service->mergePlayers($player1, $player2));
  }

  /**
   * @covers \Tfboe\FmLib\Service\PlayerService::mergePlayers
   * @uses   \Tfboe\FmLib\Service\PlayerService::__construct
   */
  public function testMergeIdenticalPlayer()
  {
    $player1 = $this->createStubWithId(PlayerInterface::class, "p1");
    $player2 = $this->createStubWithId(PlayerInterface::class, "p1");

    $service = new PlayerService(
      $this->getStub(EntityManagerInterface::class),
      $this->getStub(LoadingServiceInterface::class),
      $this->getStub(RankingSystemServiceInterface::class)
    );

    self::assertEquals('Players are identical!', $service->mergePlayers($player1, $player2));
  }

  /**
   * @covers \Tfboe\FmLib\Service\PlayerService::mergePlayers
   * @uses   \Tfboe\FmLib\Service\PlayerService::__construct
   */
  public function testMergePlayer()
  {
    $tournament = $this->getStub(TournamentInterface::class);
    $player1 = $this->createStubWithId(PlayerInterface::class, "p1");
    $player2 = $this->createStubWithId(PlayerInterface::class, "p2");
    $otherPlayer = $this->createStubWithId(PlayerInterface::class, "oP");
    $team1Membership = $this->getStub(TeamMembershipInterface::class, ['getPlayer' => $player2]);
    $team1Membership->method('setPlayer')->with($player1);
    $team1 = $this->getStub(TeamInterface::class, [
      'getMemberships' => new ArrayCollection([$team1Membership])
    ]);
    $otherTeam = $this->getStub(TeamInterface::class, [
      'getMemberships' => new ArrayCollection([$this->getStub(TeamMembershipInterface::class, [
        'getPlayer' => $otherPlayer
      ])])
    ]);

    $game1PlayersB = new ArrayCollection([$player2->getId() => $player2]);
    $game1 = $this->getStub(GameInterface::class, [
      'getPlayersA' => new ArrayCollection([$otherPlayer->getId() => $otherPlayer]),
      'getPlayersB' => $game1PlayersB
    ]);

    $game2PlayersA = new ArrayCollection([$player2->getId() => $player2]);
    $game2 = $this->getStub(GameInterface::class, [
      'getPlayersA' => $game2PlayersA,
      'getPlayersB' => new ArrayCollection([$otherPlayer->getId() => $otherPlayer])
    ]);

    $competition = $this->getStub(CompetitionInterface::class, [
      'getTeams' => new ArrayCollection([$team1, $otherTeam]),
      'getPhases' => new ArrayCollection([$this->getStub(PhaseInterface::class, [
        'getMatches' => new ArrayCollection([$this->getStub(MatchInterface::class, [
          'getGames' => new ArrayCollection([$game1, $game2])
        ])])
      ])])
    ]);


    /** @var EntityManagerInterface $em */
    $em = $this->getEntityManagerMockForQuery([$tournament], /** @lang DQL */
      "SELECT t FROM Tfboe\FmLib\Entity\TournamentInterface t INNER JOIN t.competitions c INNER JOIN c.teams te " .
      "INNER JOIN te.memberships m WHERE m.player = (:id)");
    /** @var LoadingServiceInterface|MockObject $ls */
    $ls = $this->createMock(LoadingServiceInterface::class);
    $ls->expects(self::once())->method('loadEntities')->with([$tournament])->willReturnCallback(
      function ($a) use ($competition) {
        /** @var MockObject $t */
        $t = $a[0];
        $t->method('getCompetitions')->willReturn(new ArrayCollection([$competition]));
      });

    $service = new PlayerService($em, $ls);

    self::assertEquals(true, $service->mergePlayers($player1, $player2));

    self::assertEquals($game1PlayersB, new ArrayCollection([$player1->getId() => $player1]));
    self::assertEquals($game2PlayersA, new ArrayCollection([$player1->getId() => $player1]));
  }
//  public function testMergePlayer()
//  {
//    $tournament = $this->createStub(TournamentInterface::class);
//    $player1 = $this->createStubWithId(PlayerInterface::class, "p1");
//    $player2 = $this->createStubWithId(PlayerInterface::class, "p2");
//    $otherPlayer = $this->createStubWithId(PlayerInterface::class, "oP");
//    $team1Membership = $this->createStub(TeamMembershipInterface::class, ['getPlayer' => $player2]);
//    $team1Membership->method('setPlayer')->with($player1);
//    $team1 = $this->createStub(TeamInterface::class, [
//      'getMemberships' => new ArrayCollection([$team1Membership])
//    ]);
//    $otherTeam = $this->createStub(TeamInterface::class, [
//      'getMemberships' => new ArrayCollection([$this->createStub(TeamMembershipInterface::class, [
//        'getPlayer' => $otherPlayer
//      ])])
//    ]);
//
//    $game1PlayersB = new ArrayCollection([$player2->getId() => $player2]);
//    $game1 = $this->createStub(GameInterface::class, [
//      'getPlayersA' => new ArrayCollection([$otherPlayer->getId() => $otherPlayer]),
//      'getPlayersB' => $game1PlayersB
//    ]);
//
//    $game2PlayersA = new ArrayCollection([$player2->getId() => $player2]);
//    $game2 = $this->createStub(GameInterface::class, [
//      'getPlayersA' => $game2PlayersA,
//      'getPlayersB' => new ArrayCollection([$otherPlayer->getId() => $otherPlayer])
//    ]);
//
//    $competition = $this->createStub(CompetitionInterface::class, [
//      'getTeams' => new ArrayCollection([$team1, $otherTeam]),
//      'getPhases' => new ArrayCollection([$this->createStub(PhaseInterface::class, [
//        'getMatches' => new ArrayCollection([$this->createStub(MatchInterface::class, [
//          'getGames' => new ArrayCollection([$game1, $game2])
//        ])])
//      ])])
//    ]);
//
//
//    /** @var EntityManagerInterface $em */
//    /** @noinspection SyntaxError */
//    $em = $this->getEntityManagerMockForQuery([$tournament],
//      'SELECT t FROM Tfboe\FmLib\Entity\TournamentInterface t INNER JOIN t.competitions c INNER JOIN c.teams te ' .
//      'INNER JOIN te.memberships m WHERE m.player = (:id)');
//    /** @var LoadingServiceInterface|MockObject $ls */
//    $ls = $this->createMock(LoadingServiceInterface::class);
//    $ls->expects(self::once())->method('loadEntities')->with([$tournament])->willReturnCallback(
//      function ($a) use ($competition) {
//        /** @var TournamentInterface|MockObject $t */
//        $t = $a[0];
//        $t->method('getCompetitions')->willReturn(new ArrayCollection([$competition]));
//      });
//
//    /** @var RankingSystemServiceInterface|MockObject $rankingSystemService */
//    $rankingSystemService = $this->createMock(RankingSystemServiceInterface::class);
//    $rankingSystemService->method('adaptOpenSyncFromValues')->with($tournament, []);
//
//    $service = new PlayerService($em, $ls, $rankingSystemService);
//
//    self::assertEquals(true, $service->mergePlayers($player1, $player2));
//
//    self::assertEquals($game1PlayersB, new ArrayCollection([$player1->getId() => $player1]));
//    self::assertEquals($game2PlayersA, new ArrayCollection([$player1->getId() => $player1]));
//  }
//</editor-fold desc="Public Methods">
}