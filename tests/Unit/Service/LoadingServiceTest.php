<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 3:53 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Service;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Tfboe\FmLib\Entity\CompetitionInterface;
use Tfboe\FmLib\Entity\GameInterface;
use Tfboe\FmLib\Entity\PhaseInterface;
use Tfboe\FmLib\Entity\TournamentInterface;
use Tfboe\FmLib\Service\LoadingService;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;


/**
 * Class LoadingServiceTest
 * @package Tfboe\FmLib\Tests\Unit\Service
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LoadingServiceTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Service\LoadingService::__construct
   */
  public function testConstruct()
  {
    /** @var EntityManagerInterface $entityManager */
    $entityManager = $this->getMockForAbstractClass(EntityManagerInterface::class);
    $service = new LoadingService($entityManager);
    self::assertInstanceOf(LoadingService::class, $service);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($entityManager, self::getProperty(get_class($service), 'em')->getValue($service));
  }

  /**
   * @covers \Tfboe\FmLib\Service\LoadingService::loadEntities
   * @covers \Tfboe\FmLib\Service\LoadingService::keyOfPropertyMap
   * @uses   \Tfboe\FmLib\Service\LoadingService::__construct
   */
  public function testLoadEntitiesAlreadyLoaded()
  {
    $service = new LoadingService($this->getEntityManagerMockForQueries([]));

    $tournament = $this->createMock(TournamentInterface::class);
    $tournament->method('getEntityId')->willReturn('t');
    $initializedCollection = new ArrayCollection();
    $initializedCollection->__isInitialized__ = true;
    $tournament->method('getCompetitions')->willReturn($initializedCollection);
    $service->loadEntities([$tournament], [TournamentInterface::class => [["competitions"]]]);
  }

  /**
   * @covers \Tfboe\FmLib\Service\LoadingService::loadEntities
   * @covers \Tfboe\FmLib\Service\LoadingService::loadProperties
   * @covers \Tfboe\FmLib\Service\LoadingService::keyOfPropertyMap
   * @uses   \Tfboe\FmLib\Service\LoadingService::__construct
   */
  public function testLoadEntitiesDefaultPropertiesGame()
  {
    $service = new LoadingService($this->getEntityManagerMockForQuery([],
      'SELECT t1, t2, t3 FROM Tfboe\FmLib\Entity\GameInterface t1 LEFT JOIN t1.playersA t2 LEFT JOIN '
      . 't1.playersB t3 WHERE t1.id IN(\'g\')'
    ));
    $game = $this->createMock(GameInterface::class);
    $game->method('getEntityId')->willReturn('g');

    $uninitializedLazyCollection = $this->createMock(AbstractLazyCollection::class);
    $uninitializedLazyCollection->method('isInitialized')->willReturn(false);

    $game->expects(self::exactly(2))->method('getPlayersA')->willReturnOnConsecutiveCalls($uninitializedLazyCollection,
      new ArrayCollection());
    $game->expects(self::once())->method('getPlayersB')->willReturn(new ArrayCollection());
    $service->loadEntities([$game]);
  }

  /**
   * @covers \Tfboe\FmLib\Service\LoadingService::loadEntities
   * @covers \Tfboe\FmLib\Service\LoadingService::loadProperties
   * @covers \Tfboe\FmLib\Service\LoadingService::keyOfPropertyMap
   * @uses   \Tfboe\FmLib\Service\LoadingService::__construct
   */
  public function testLoadEntitiesMultipleLevels()
  {
    $tournament = $this->createMock(TournamentInterface::class);
    $tournament->method('getEntityId')->willReturn('t');

    $competition1 = $this->createMock(CompetitionInterface::class);
    $competition1->method('getEntityId')->willReturn('c1');

    $competition2 = $this->createMock(CompetitionInterface::class);
    $competition2->method('getEntityId')->willReturn('c1');

    $uninitializedLazyCollection = $this->createMock(AbstractLazyCollection::class);
    $uninitializedLazyCollection->method('isInitialized')->willReturn(false);

    $tournament->method('getCompetitions')->willReturnOnConsecutiveCalls(
      $uninitializedLazyCollection,
      new ArrayCollection([$competition1, $competition2])
    );

    $phase1 = $this->createMock(PhaseInterface::class);
    $phase1->method('getEntityId')->willReturn('p1');

    $competition1->method('getPhases')->willReturn(new ArrayCollection([$phase1]));

    $phase2 = $this->createMock(PhaseInterface::class);
    $phase2->method('getEntityId')->willReturn('p2');

    $competition2->method('getPhases')->willReturn(new ArrayCollection([$phase2]));

    $entityManager = $this->getEntityManagerMockForQueries([[], []], [
      'SELECT t1, t2 FROM Tfboe\FmLib\Entity\TournamentInterface t1 LEFT JOIN t1.competitions t2 WHERE t1.id IN(\'t\')',
      'SELECT t1, t2 FROM Tfboe\FmLib\Entity\CompetitionInterface t1 LEFT JOIN t1.phases t2 WHERE t1.id IN(\'c1\')'
    ]);

    $service = new LoadingService($entityManager);
    $service->loadEntities([$tournament], [
      TournamentInterface::class => [["competitions"]],
      CompetitionInterface::class => [["phases"]]
    ]);
  }

  /**
   * @covers \Tfboe\FmLib\Service\LoadingService::loadEntities
   * @covers \Tfboe\FmLib\Service\LoadingService::loadProperties
   * @covers \Tfboe\FmLib\Service\LoadingService::keyOfPropertyMap
   * @uses   \Tfboe\FmLib\Service\LoadingService::__construct
   */
  public function testLoadEntitiesNullProperty()
  {
    $service = new LoadingService($this->getEntityManagerMockForQuery([],
      'SELECT t1, t2 FROM Tfboe\FmLib\Entity\TournamentInterface t1 LEFT JOIN t1.competitions t2 WHERE t1.id IN(\'t\')'
    ));
    $tournament = $this->createMock(TournamentInterface::class);
    $tournament->method('getEntityId')->willReturn('t');
    $tournament->method('getCompetitions')->willReturn(null);
    $service->loadEntities([$tournament], [TournamentInterface::class => [["competitions"]]]);
  }

  /**
   * @covers \Tfboe\FmLib\Service\LoadingService::loadEntities
   * @covers \Tfboe\FmLib\Service\LoadingService::loadProperties
   * @covers \Tfboe\FmLib\Service\LoadingService::keyOfPropertyMap
   * @uses   \Tfboe\FmLib\Service\LoadingService::__construct
   */
  public function testLoadEntitiesSimpleProperty()
  {
    $service = new LoadingService($this->getEntityManagerMockForQuery([],
      'SELECT t1, t2 FROM Tfboe\FmLib\Entity\CompetitionInterface t1 LEFT JOIN t1.tournament t2 WHERE t1.id IN(\'c\')'
    ));
    $competition = $this->createMock(CompetitionInterface::class);
    $competition->method('getEntityId')->willReturn('c');
    $service->loadEntities([$competition], [CompetitionInterface::class => [["tournament"]]]);
  }
//</editor-fold desc="Public Methods">
}