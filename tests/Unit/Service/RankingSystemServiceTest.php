<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 3:53 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Service;

use Doctrine\ORM\EntityManagerInterface;
use Tfboe\FmLib\Service\DynamicServiceLoadingServiceInterface;
use Tfboe\FmLib\Service\ObjectCreatorServiceInterface;
use Tfboe\FmLib\Service\RankingSystemService;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;


/**
 * Class EloRankingTest
 * @packageTfboe\FmLib\Tests\Unit\Service
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RankingSystemServiceTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Service\RankingSystemService::__construct
   */
  public function testConstruct()
  {
    $dsls = $this->getMockForAbstractClass(DynamicServiceLoadingServiceInterface::class);
    $entityManager = $this->getMockForAbstractClass(EntityManagerInterface::class);
    /** @var DynamicServiceLoadingServiceInterface $dsls */
    /** @var EntityManagerInterface $entityManager */
    $system = new RankingSystemService($dsls, $entityManager,
      $this->createMock(ObjectCreatorServiceInterface::class));
    self::assertInstanceOf(RankingSystemService::class, $system);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($entityManager, self::getProperty(get_class($system), 'entityManager')->getValue($system));
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($dsls, self::getProperty(get_class($system), 'dsls')->getValue($system));
  }

  /**
   * @covers \Tfboe\FmLib\Service\RankingSystemService::recalculateRankingSystems
   * @uses   \Tfboe\FmLib\Service\RankingSystemService::__construct
   */
//  public function testRecalculateRankingSystems()
//  {
//    $rs1 = $this->createMock(\Tfboe\FmLib\Entity\RankingSystemInterface::class);
//    $rs1->expects(self::once())->method('getServiceName')->willReturn('service');
//    $rs1->expects(self::exactly(3))->method('getOpenSyncFrom')->willReturn(new \DateTime("2017-02-01"));
//    $rs1->expects(self::once())->method('setOpenSyncFrom')->with(null);
//    $rs1->expects(self::once())->method('setOpenSyncFromInProcess')->with(null);
//    $rs2 = $this->createMock(\Tfboe\FmLib\Entity\RankingSystemInterface::class);
//    $rs2->expects(self::once())->method('getServiceName')->willReturn('service');
//    $rs2->expects(self::exactly(3))->method('getOpenSyncFrom')->willReturn(new \DateTime("2017-05-01"));
//    $rs2->expects(self::once())->method('setOpenSyncFrom')->with(null);
//    $rs2->expects(self::once())->method('setOpenSyncFromInProcess')->with(null);
//    $slash = '\\';
//    $first = 'SELECT s';
//    $second = ' FROM Tfboe';
//    $third = 'FmLib';
//    $rest = 'RankingSystemInterface s WHERE s.openSyncFrom IS NOT NULL OR s.openSyncFromInProcess IS NOT NULL';
//    $entityManager = $this->getEntityManagerMockForQuery([$rs1, $rs2],
//      $first . $second . $slash . $third . $slash . 'Entity' . $slash . $rest, ['flush', 'clear', 'transactional',
//        'find']);
//    $entityManager->method('transactional')->willReturnCallback(function ($f) use ($entityManager) {
//      return $f($entityManager);
//    });
//    $lastRecalculation = $this->createMock(RecalculationInterface::class);
//    $entityManager->method('find')->willReturn($lastRecalculation);
//    $dsls = $this->getMockForAbstractClass(DynamicServiceLoadingServiceInterface::class);
//    $service = $this->getMockForAbstractClass(RankingSystemInterface::class);
//    $service->expects(self::exactly(2))->method('updateRankingFrom')
//      ->withConsecutive([$rs1, new \DateTime("2017-02-01")], [$rs2, new \DateTime("2017-05-01")]);
//    $dsls->expects(self::exactly(2))->method('loadRankingSystemService')->with('service')->willReturn($service);
//    /** @var DynamicServiceLoadingServiceInterface $dsls */
//    /** @var EntityManagerInterface $entityManager */
//    $system = new RankingSystemService($dsls, $entityManager);
//    $system->recalculateRankingSystems();
//  }
//</editor-fold desc="Public Methods">
}