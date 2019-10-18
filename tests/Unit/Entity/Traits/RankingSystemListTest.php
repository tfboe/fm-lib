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
   */
  public function testConstructor()
  {
    $entity = $this->instance();
    self::assertEquals(new DateTime("2000-01-01"), $entity->getLastEntryTime());
    self::assertFalse($entity->isCurrent());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::setCurrent
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::isCurrent
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::init
   */
  public function testCurrent()
  {
    $entity = $this->instance();
    $entity->setCurrent(true);
    self::assertTrue($entity->isCurrent());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::setLastEntryTime
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemList::getLastEntryTime
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList::init
   */
  public function testLastEntry()
  {
    $instance = $this->instance();
    $instance->setLastEntryTime(new DateTime("2017-01-01"));
    self::assertEquals(new DateTime("2017-01-01"), $instance->getLastEntryTime());
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
    /** @noinspection PhpUnhandledExceptionInspection */
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