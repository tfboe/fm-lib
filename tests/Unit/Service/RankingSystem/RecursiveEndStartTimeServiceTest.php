<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/9/18
 * Time: 1:56 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Service\RankingSystem;


use DateTime;
use Tfboe\FmLib\Entity\Helpers\StartAndFinishableInterface;
use Tfboe\FmLib\Entity\Helpers\StartFinishStatus;
use Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService;
use Tfboe\FmLib\Tests\Entity\Competition;
use Tfboe\FmLib\Tests\Entity\Game;
use Tfboe\FmLib\Tests\Entity\Match;
use Tfboe\FmLib\Tests\Entity\Phase;
use Tfboe\FmLib\Tests\Entity\Tournament;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;


/**
 * Class RecursiveEndStartTimeServiceTest
 * @packageTfboe\FmLib\Tests\Unit\Service\RankingSystemListService
 */
class RecursiveEndStartTimeServiceTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService::clearTimes
   * @uses   \Tfboe\FmLib\Entity\Helpers\TimeEntity
   * @uses   \Tfboe\FmLib\Entity\Helpers\UUIDEntity::getId
   * @uses   \Tfboe\FmLib\Entity\Traits\Tournament
   * @uses   \Tfboe\FmLib\Entity\Traits\Tournament
   * @uses   \Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService::getTime
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Helpers\DateTime::eq
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::changeIsValid
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::ensureValidValue
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::setStatus
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::statusIsFinished
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::statusIsStarted
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::ensureValidValue
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getValues
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::isValidValue
   */
  public function testClearTimes()
  {
    $tournament = new Tournament();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Tournament::class, 'id')->setValue($tournament, 't1');
    $service = new RecursiveEndStartTimeService();
    $endedAt = new DateTime("2017-03-01");
    $this->setEndTime($tournament, $endedAt);
    self::assertEquals($endedAt, $service->getTime($tournament));

    $newEndedAt = new DateTime("2017-06-01");
    $this->setEndTime($tournament, $newEndedAt);
    self::assertEquals($endedAt, $service->getTime($tournament));

    $service->clearTimes();
    self::assertEquals($newEndedAt, $service->getTime($tournament));
  }

  /** @noinspection PhpDocMissingThrowsInspection */

  /**
   * @covers \Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService::getTime
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition
   * @uses   \Tfboe\FmLib\Entity\Traits\Game
   * @uses   \Tfboe\FmLib\Entity\Traits\Game
   * @uses   \Tfboe\FmLib\Entity\Traits\Match
   * @uses   \Tfboe\FmLib\Entity\Traits\Match
   * @uses   \Tfboe\FmLib\Entity\Traits\Phase
   * @uses   \Tfboe\FmLib\Entity\Traits\Phase
   * @uses   \Tfboe\FmLib\Entity\Traits\Tournament
   * @uses   \Tfboe\FmLib\Entity\Traits\Tournament
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity
   * @uses   \Tfboe\FmLib\Entity\Helpers\TimeEntity
   * @uses   \Tfboe\FmLib\Entity\Helpers\UUIDEntity::getId
   * @uses   \Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService::clearTimes
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Helpers\DateTime::eq
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::changeIsValid
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::ensureValidValue
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::setStatus
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::statusIsFinished
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::statusIsStarted
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::ensureValidValue
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getValues
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::isValidValue
   */
  public function testGetTimeGame()
  {
    $tournament = new Tournament();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Tournament::class, 'id')->setValue($tournament, 't1');
    $endedAt = new DateTime("2017-05-01");
    $this->setEndTime($tournament, $endedAt);
    $competition = new Competition();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Competition::class, 'id')->setValue($competition, 'c1');
    $competition->setName("TestCompetition")->setTournament($tournament);
    $phase = new Phase();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Phase::class, 'id')->setValue($phase, 'p1');
    $phase->setPhaseNumber(1);
    $phase->setCompetition($competition);
    $match = new Match();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Match::class, 'id')->setValue($match, 'm1');
    $match->setMatchNumber(1);
    $match->setPhase($phase);
    $game = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Game::class, 'id')->setValue($game, 'g1');
    $game->setGameNumber(1);
    $game->setMatch($match);
    $service = new RecursiveEndStartTimeService();
    self::assertEquals($endedAt, $service->getTime($game));

    $service->clearTimes();
    $phaseEndedAt = new DateTime("2017-04-01");
    $this->setEndTime($phase, $phaseEndedAt);
    self::assertEquals($phaseEndedAt, $service->getTime($game));

    $service->clearTimes();
    $matchEndTime = new DateTime("2017-04-02");
    $this->setEndTime($match, $matchEndTime);
    self::assertEquals($matchEndTime, $service->getTime($game));

    $service->clearTimes();
    $gameStartedAt = new DateTime("2017-02-01");
    /** @noinspection PhpUnhandledExceptionInspection */
    $game->setStatus(StartFinishStatus::STARTED, $gameStartedAt);
    self::assertEquals($gameStartedAt, $service->getTime($game));

    $service->clearTimes();
    $gameEndedAt = new DateTime("2017-03-01");
    $this->setEndTime($game, $gameEndedAt);
    self::assertEquals($gameEndedAt, $service->getTime($game));
  }

  /**
   * @covers \Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService::getTime
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition
   * @uses   \Tfboe\FmLib\Entity\Traits\Competition
   * @uses   \Tfboe\FmLib\Entity\Traits\Phase
   * @uses   \Tfboe\FmLib\Entity\Traits\Phase
   * @uses   \Tfboe\FmLib\Entity\Traits\Tournament
   * @uses   \Tfboe\FmLib\Entity\Traits\Tournament
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity
   * @uses   \Tfboe\FmLib\Entity\Helpers\TimeEntity
   * @uses   \Tfboe\FmLib\Entity\Helpers\UUIDEntity::getId
   * @uses   \Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService::clearTimes
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::changeIsValid
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::ensureValidValue
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::setStatus
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::statusIsFinished
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::statusIsStarted
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::ensureValidValue
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getValues
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::isValidValue
   */
  public function testGetTimePhase()
  {
    $tournament = new Tournament();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Tournament::class, 'id')->setValue($tournament, 't1');
    $endedAt = new DateTime("2017-04-01");
    $this->setEndTime($tournament, $endedAt);
    $competition = new Competition();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Competition::class, 'id')->setValue($competition, 'c1');
    $competition->setName("TestCompetition")->setTournament($tournament);
    $phase = new Phase();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Phase::class, 'id')->setValue($phase, 'p1');
    $phase->setPhaseNumber(1);
    $phase->setCompetition($competition);
    $service = new RecursiveEndStartTimeService();
    self::assertEquals($endedAt, $service->getTime($phase));

    $service->clearTimes();
    $startedAt = new DateTime("2017-02-01");
    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->setStatus(StartFinishStatus::STARTED, $startedAt);
    self::assertEquals($startedAt, $service->getTime($phase));

    $service->clearTimes();
    $endedAt = new DateTime("2017-03-01");
    $this->setEndTime($phase, $endedAt);
    self::assertEquals($endedAt, $service->getTime($phase));
  }

  /**
   * @covers \Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService::getTime
   * @uses   \Tfboe\FmLib\Entity\Traits\Tournament
   * @uses   \Tfboe\FmLib\Entity\Traits\Tournament
   * @uses   \Tfboe\FmLib\Entity\Helpers\TimeEntity
   * @uses   \Tfboe\FmLib\Entity\Helpers\TimestampableEntity
   * @uses   \Tfboe\FmLib\Entity\Helpers\UUIDEntity::getId
   * @uses   \Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService::clearTimes
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Helpers\DateTime::eq
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::changeIsValid
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::ensureValidValue
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::setStatus
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::statusIsFinished
   * @uses   \Tfboe\FmLib\Entity\Helpers\StartAndFinishable::statusIsStarted
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::ensureValidValue
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getConstants
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::getValues
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum::isValidValue
   */
  public function testGetTimeTournament()
  {
    $tournament = new Tournament();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Tournament::class, 'id')->setValue($tournament, 't1');
    $updatedAt = new DateTime("2017-04-01");
    $tournament->setUpdatedAt($updatedAt);
    $service = new RecursiveEndStartTimeService();
    self::assertEquals($updatedAt, $service->getTime($tournament));

    $service->clearTimes();
    $startedAt = new DateTime("2017-02-01");
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->setStatus(StartFinishStatus::STARTED, $startedAt);
    self::assertEquals($startedAt, $service->getTime($tournament));

    $service->clearTimes();
    $endedAt = new DateTime("2017-03-01");
    $this->setEndTime($tournament, $endedAt);
    self::assertEquals($endedAt, $service->getTime($tournament));
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * @param StartAndFinishableInterface $entity
   * @param DateTime $time
   */
  private function setEndTime(StartAndFinishableInterface $entity, DateTime $time)
  {
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::STARTED, $time);
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setStatus(StartFinishStatus::FINISHED, $time);
  }
//</editor-fold desc="Private Methods">
}