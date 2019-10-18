<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 10:39 AM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity\Traits;


use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\RankingSystemChangeInterface;
use Tfboe\FmLib\Entity\RankingSystemInterface;
use Tfboe\FmLib\Tests\Entity\Player;
use Tfboe\FmLib\Tests\Entity\RankingSystem;
use Tfboe\FmLib\Tests\Entity\RankingSystemChange;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;


/**
 * Class BaseEntityChangeTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\Helpers
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RankingSystemChangeTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemChange::init
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   */
  public function testConstructor()
  {
    $entity = $this->instance();
    self::assertInstanceOf(RankingSystemChange::class, $entity);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemChange::init
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::hasProperty
   */
  public function testConstructorWithAdditionalKeys()
  {
    $entity = new RankingSystemChange(["key"]);
    self::assertTrue($entity->hasProperty("key"));
    self::assertFalse($entity->hasProperty("other"));
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemChange::getPlayer
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemChange::setPlayer
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemChange::init
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   */
  public function testPlayer()
  {
    $entity = $this->instance();
    /** @var Player $player */
    $player = $this->createMock(Player::class);
    $entity->setPlayer($player);
    self::assertEquals($player, $entity->getPlayer());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemChange::setPointsAfterwards
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemChange::getPointsAfterwards
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemChange::init
   */
  public function testPointsAfterwards()
  {
    $entity = $this->instance();
    $points = 24.333;
    $entity->setPointsAfterwards($points);
    self::assertEquals($points, $entity->getPointsAfterwards());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemChange::setPointsChange
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemChange::getPointsChange
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemChange::init
   */
  public function testPointsChange()
  {
    $entity = $this->instance();
    $points = 24.333;
    $entity->setPointsChange($points);
    self::assertEquals($points, $entity->getPointsChange());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemChange::setRankingSystem
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemChange::getRankingSystem
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemChange::init
   */
  public function testRankingSystem()
  {
    $entity = $this->instance();
    /** @var RankingSystemInterface $rankingSystem */
    $rankingSystem = $this->createMock(RankingSystem::class);
    $entity->setRankingSystem($rankingSystem);
    self::assertEquals($rankingSystem, $entity->getRankingSystem());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemChange::setHierarchyEntity
   * @covers \Tfboe\FmLib\Entity\Traits\RankingSystemChange::getHierarchyEntity
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\Traits\RankingSystemChange::init
   */
  public function testTournamentHierarchyEntity()
  {
    $entity = $this->instance();
    /** @var TournamentHierarchyEntity $tEntity */
    $tEntity = $this->createMock(TournamentHierarchyEntity::class);
    $entity->setHierarchyEntity($tEntity);
    self::assertEquals($tEntity, $entity->getHierarchyEntity());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return RankingSystemChangeInterface
   */
  private function instance(): RankingSystemChangeInterface
  {
    return new RankingSystemChange([]);
  }
//</editor-fold desc="Private Methods">
}