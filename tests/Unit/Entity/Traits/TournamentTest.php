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
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;
use Tfboe\FmLib\Entity\CompetitionInterface;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\TournamentInterface;
use Tfboe\FmLib\Helpers\Level;
use Tfboe\FmLib\Tests\Entity\User;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

/**
 * Class Tournament
 * @package Tfboe\FmLib\Tests\Unit\Entity\Traits
 */
abstract class Tournament extends TournamentHierarchyEntity implements TournamentInterface
{
  use \Tfboe\FmLib\Entity\Traits\Tournament;
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */

/**
 * Class TournamentTest
 * @package Tfboe\FmLib\Tests\Unit\Entity
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class TournamentTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Tournament::getCompetitions
   * @covers \Tfboe\FmLib\Entity\Traits\Tournament::getChildren
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::setName
   * @uses   \Tfboe\FmLib\Entity\Traits\Tournament::init
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::getName
   */
  public function testCompetitionsAndChildren()
  {
    $tournament = $this->tournament();
    self::callProtectedMethod($tournament, 'init');
    $competition = $this->createStub(CompetitionInterface::class, ['getName' => 'comp name']);
    self::assertEquals($tournament->getCompetitions(), $tournament->getChildren());
    /** @var CompetitionInterface $competition */
    $tournament->getCompetitions()->set($competition->getId(), $competition);
    self::assertEquals(1, $tournament->getCompetitions()->count());
    self::assertEquals($competition, $tournament->getCompetitions()[$competition->getId()]);
    self::assertEquals($tournament->getCompetitions(), $tournament->getChildren());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Tournament::init
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Entity\Traits\Tournament::getTournamentListId
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\Tournament::getCompetitions
   */
  public function testConstructor()
  {
    $tournament = $this->tournament();
    self::callProtectedMethod($tournament, 'init');
    self::assertInstanceOf(ArrayCollection::class, $tournament->getCompetitions());
    self::assertEquals(0, $tournament->getCompetitions()->count());
    self::assertEquals("", $tournament->getTournamentListId());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Tournament::setCreator
   * @covers \Tfboe\FmLib\Entity\Traits\Tournament::getCreator
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Traits\User::init
   */
  public function testCreator()
  {
    $tournament = $this->tournament();
    $creator = new User();
    $tournament->setCreator($creator);
    self::assertEquals($creator, $tournament->getCreator());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Tournament::getLocalIdentifier
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\UUIDEntity::getId
   */
  public function testGetLocalIdentifier()
  {
    $tournament = $this->tournament();
    /** @noinspection PhpUnhandledExceptionInspection */
    self::getProperty(get_class($tournament), 'id')->setValue($tournament, 'user-id');
    self::assertEquals($tournament->getId(), $tournament->getLocalIdentifier());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Tournament::getLevel
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testLevel()
  {
    self::assertEquals(Level::TOURNAMENT, $this->tournament()->getLevel());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Tournament::getParent
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testParent()
  {
    self::assertNull($this->tournament()->getParent());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Tournament::setTournamentListId
   * @covers \Tfboe\FmLib\Entity\Traits\Tournament::getTournamentListId
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testTournamentListId()
  {
    $tournament = $this->tournament();
    $tournament->setTournamentListId("Changed");
    self::assertEquals("Changed", $tournament->getTournamentListId());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return Tournament|MockObject a new tournament
   * @throws ReflectionException
   */
  private function tournament(): MockObject
  {
    return $this->getMockForAbstractClass(Tournament::class);
  }
//</editor-fold desc="Private Methods">
}