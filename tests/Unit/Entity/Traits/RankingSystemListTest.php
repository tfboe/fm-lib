<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 10:39 AM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity\Traits;


use DateTime;
use PHPUnit\Framework\Error\Error;
use Tfboe\FmLib\Entity\RankingSystemListInterface;
use Tfboe\FmLib\Tests\Entity\Player;
use Tfboe\FmLib\Tests\Entity\RankingSystem;
use Tfboe\FmLib\Tests\Entity\RankingSystemList;
use Tfboe\FmLib\Tests\Entity\RankingSystemListEntry;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;


/**
 * Class BaseEntityTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\Helpers
 */
class RankingSystemListTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::init
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::getLastEntryTime
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::isCurrent
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::getEntryTimeLimit
   */
  public function testConstructor()
  {
    $entity = $this->instance();
    self::assertEquals(new DateTime("2000-01-01"), $entity->getLastEntryTime());
    self::assertTrue($entity->isCurrent());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::isCurrent
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::init
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::getEntryTimeLimit
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::getLastEntryTime
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::setEntryTimeLimit
   * @uses   \Tfboe\FmLib\Exceptions\Internal::assert
   */
  public function testCurrent()
  {
    $entity = $this->instance();
    self::assertTrue($entity->isCurrent());
    $entity->setEntryTimeLimit(new DateTime("2000-01-02"));
    self::assertFalse($entity->isCurrent());
    $entity->setEntryTimeLimit(null);
    self::assertTrue($entity->isCurrent());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::setLastEntryTime
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::getLastEntryTime
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::init
   * @uses   \Tfboe\FmLib\Helpers\DateTimeHelper::eq
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::getEntryTimeLimit
   */
  public function testLastEntry()
  {
    $instance = $this->instance();
    $instance->setLastEntryTime(new DateTime("2017-01-01"));
    self::assertEquals(new DateTime("2017-01-01"), $instance->getLastEntryTime());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::setLastEntryTime
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::getLastEntryTime
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::init
   * @uses   \Tfboe\FmLib\Helpers\DateTimeHelper::eq
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::getEntryTimeLimit
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::setEntryTimeLimit
   * @uses   \Tfboe\FmLib\Exceptions\Internal::assert
   */
  public function testLastEntryTimeWithEntryTimeLimit()
  {
    $instance = $this->instance();
    $instance->setEntryTimeLimit(new DateTime("2017-01-01 00:00:00.001"));
    $instance->setLastEntryTime(new DateTime("2017-01-01"));
    self::assertEquals(new DateTime("2017-01-01"), $instance->getLastEntryTime());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::setLastEntryTime
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::getLastEntryTime
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::init
   * @uses   \Tfboe\FmLib\Helpers\DateTimeHelper::eq
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::getEntryTimeLimit
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::setEntryTimeLimit
   * @uses   \Tfboe\FmLib\Exceptions\Internal::assert
   * @uses   \Tfboe\FmLib\Exceptions\Internal::error
   */
  public function testLastEntryTimeWithWrongEntryTimeLimit()
  {
    $instance = $this->instance();
    $instance->setEntryTimeLimit(new DateTime("2017-01-01"));
    $this->expectException(Error::class);
    $this->expectExceptionMessage("Assertion failed!");
    $instance->setLastEntryTime(new DateTime("2017-01-01"));
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::setEntryTimeLimit
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::getEntryTimeLimit
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::init
   * @uses   \Tfboe\FmLib\Helpers\DateTimeHelper::eq
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::getLastEntryTime
   * @uses   \Tfboe\FmLib\Exceptions\Internal::assert
   */
  public function testEntryTimeLimit()
  {
    $instance = $this->instance();
    $instance->setEntryTimeLimit(new DateTime("2017-01-01"));
    self::assertEquals(new DateTime("2017-01-01"), $instance->getEntryTimeLimit());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::setLastEntryTime
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::getLastEntryTime
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::init
   * @uses   \Tfboe\FmLib\Helpers\DateTimeHelper::eq
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::getEntryTimeLimit
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::setEntryTimeLimit
   * @uses   \Tfboe\FmLib\Exceptions\Internal::assert
   */
  public function testEntryTimeLimitWithLastEntryTime()
  {
    $instance = $this->instance();
    $instance->setLastEntryTime(new DateTime("2017-01-01"));
    $instance->setEntryTimeLimit(new DateTime("2017-01-01 00:00:00.001"));
    self::assertEquals(new DateTime("2017-01-01 00:00:00.001"), $instance->getEntryTimeLimit());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::setLastEntryTime
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::getLastEntryTime
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::init
   * @uses   \Tfboe\FmLib\Helpers\DateTimeHelper::eq
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::getEntryTimeLimit
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::setEntryTimeLimit
   * @uses   \Tfboe\FmLib\Exceptions\Internal::assert
   * @uses   \Tfboe\FmLib\Exceptions\Internal::error
   */
  public function testEntryTimeLimitWithWrongLastEntryTime()
  {
    $instance = $this->instance();
    $instance->setLastEntryTime(new DateTime("2017-01-01"));
    $this->expectException(Error::class);
    $this->expectExceptionMessage("Assertion failed!");
    $instance->setEntryTimeLimit(new DateTime("2017-01-01"));
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::getEntries
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::init
   */
  public function testLists()
  {
    $entity = $this->instance();
    $entity2 = $this->createMock(RankingSystemListEntry::class);
    $player = $this->createStubWithId(Player::class, 5, 'getId');
    /** @var Player $player */
    $entity2->method('getPlayer')->willReturn($player);
    $entity->getEntries()->set($player->getId(), $entity2);
    self::assertEquals(1, $entity->getEntries()->count());
    self::assertEquals($entity2, $entity->getEntries()[$player->getId()]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::setRankingSystem
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::getRankingSystem
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::init
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystem
   * @uses   \Tfboe\FmLib\Entity\Helpers\UUIDEntity::getId
   */
  public function testRankingSystem()
  {
    $instance = $this->instance();

    self::getProperty(get_class($instance), 'id')->setValue($instance, 'list-id');
    $rankingSystem = new RankingSystem([]);

    $instance->setRankingSystem($rankingSystem);
    self::assertEquals($rankingSystem, $instance->getRankingSystem());
    self::assertEquals(1, $instance->getRankingSystem()->getLists()->count());
    self::assertEquals($instance, $instance->getRankingSystem()->getLists()[$instance->getId()]);

    $rankingSystem2 = new RankingSystem([]);
    $instance->setRankingSystem($rankingSystem2);

    self::assertEquals($rankingSystem2, $instance->getRankingSystem());
    self::assertEquals(1, $instance->getRankingSystem()->getLists()->count());
    self::assertEquals(0, $rankingSystem->getLists()->count());
    self::assertEquals($instance, $instance->getRankingSystem()->getLists()[$instance->getId()]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return RankingSystemListInterface
   */
  private function instance(): RankingSystemListInterface
  {
    return new RankingSystemList();
  }
//</editor-fold desc="Private Methods">
}