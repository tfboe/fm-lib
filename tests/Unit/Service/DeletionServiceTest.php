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
use Tfboe\FmLib\Entity\PlayerInterface;
use Tfboe\FmLib\Service\DeletionService;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;


/**
 * Class DeletionServiceTest
 * @package Tfboe\FmLib\Tests\Unit\Service
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DeletionServiceTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Service\DeletionService::__construct
   */
  public function testConstruct()
  {
    /** @var EntityManagerInterface $entityManager */
    $entityManager = $this->getMockForAbstractClass(EntityManagerInterface::class);
    $service = new DeletionService($entityManager);
    self::assertInstanceOf(DeletionService::class, $service);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($entityManager, self::getProperty(get_class($service), 'entityManager')->getValue($service));
  }

  /**
   * @covers \Tfboe\FmLib\Service\DeletionService::deletePlayer
   * @uses   \Tfboe\FmLib\Service\DeletionService::__construct
   */
  public function testDeletePlayer()
  {
    /** @var PlayerInterface $player */
    $player = $this->createMock(PlayerInterface::class);
    $entityManager = $this->createMock(EntityManagerInterface::class);
    $entityManager->expects(self::once())->method('remove')->with($player);
    /** @var EntityManagerInterface $entityManager */
    $service = new DeletionService($entityManager);
    $service->deletePlayer($player);
  }
//</editor-fold desc="Public Methods">
}