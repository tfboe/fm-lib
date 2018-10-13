<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 10:39 AM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity\Traits;


use Tfboe\FmLib\Entity\RankingSystemListEntryInterface;
use Tfboe\FmLib\Tests\Entity\Player;
use Tfboe\FmLib\Tests\Entity\RankingSystemList;
use Tfboe\FmLib\Tests\Entity\RankingSystemListEntry;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;


/**
 * Class BaseEntityChangeTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\Helpers
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RankingSystemListEntryTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::init
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   */
  public function testConstructor()
  {
    $entity = $this->instance();
    self::assertInstanceOf(RankingSystemListEntry::class, $entity);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::init
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::hasProperty
   */
  public function testConstructorWithAdditionalKeys()
  {
    $entity = new RankingSystemListEntry(["key"]);
    self::assertTrue($entity->hasProperty("key"));
    self::assertFalse($entity->hasProperty("other"));
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::setNumberRankedEntities
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::getNumberRankedEntities
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::init
   */
  public function testNumberOfRankedEntities()
  {
    $entity = $this->instance();
    $points = 5;
    $entity->setNumberRankedEntities($points);
    self::assertEquals($points, $entity->getNumberRankedEntities());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::getPlayer
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::setPlayer
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::init
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   */
  public function testPlayer()
  {
    $entity = $this->instance();
    /** @var \Tfboe\FmLib\Tests\Entity\Player $player */
    $player = $this->createMock(Player::class);
    $entity->setPlayer($player);
    self::assertEquals($player, $entity->getPlayer());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::setPoints
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::getPoints
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::init
   */
  public function testPoints()
  {
    $entity = $this->instance();
    $points = 24.333;
    $entity->setPoints($points);
    self::assertEquals($points, $entity->getPoints());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::setRankingSystemList
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::getRankingSystemList
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::init
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::getPlayer
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::setPlayer
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::isInitialized
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::setRankingSystemListWithoutInitializing
   */
  public function testRankingSystemList()
  {
    $entity = $this->instance();
    $rankingSystemList = new RankingSystemList();
    $player = $this->createMock(Player::class);
    $player->method('getId')->willReturn(5);
    /** @var \Tfboe\FmLib\Tests\Entity\Player $player */
    $entity->setPlayer($player);

    $entity->setRankingSystemList($rankingSystemList);
    self::assertEquals($rankingSystemList, $entity->getRankingSystemList());
    self::assertEquals(1, $rankingSystemList->getEntries()->count());
    self::assertEquals($entity, $rankingSystemList->getEntries()[5]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::setRankingSystemList
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::getRankingSystemList
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::init
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::getPlayer
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::setPlayer
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemList
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::isInitialized
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemListEntry::setRankingSystemListWithoutInitializing
   */
  public function testRankingSystemListRemoveFromOld()
  {
    $entity = $this->instance();
    $rankingSystemList = new RankingSystemList();
    $player = $this->createMock(Player::class);
    $player->method('getId')->willReturn(5);
    /** @var Player $player */
    $entity->setPlayer($player);

    $entity->setRankingSystemList($rankingSystemList);
    self::assertEquals($rankingSystemList, $entity->getRankingSystemList());
    self::assertEquals(1, $rankingSystemList->getEntries()->count());

    $rankingSystemList2 = new RankingSystemList();
    $entity->setRankingSystemList($rankingSystemList2);
    self::assertEquals(0, $rankingSystemList->getEntries()->count());
    self::assertEquals(1, $rankingSystemList2->getEntries()->count());
    self::assertEquals($entity, $rankingSystemList2->getEntries()[5]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return RankingSystemListEntryInterface
   */
  private function instance(): RankingSystemListEntryInterface
  {
    return new RankingSystemListEntry([]);
  }
//</editor-fold desc="Private Methods">
}