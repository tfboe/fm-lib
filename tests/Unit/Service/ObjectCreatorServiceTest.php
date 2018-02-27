<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 3:53 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Service;

use Tfboe\FmLib\Entity\PlayerInterface;
use Tfboe\FmLib\Service\ObjectCreatorService;
use Tfboe\FmLib\Tests\Entity\Player;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;


/**
 * Class EloRankingTest
 * @package Tfboe\FmLib\Tests\Unit\Service
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ObjectCreatorServiceTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Service\ObjectCreatorService::createObjectFromInterface
   */
  public function testCreateObjectFromInterface()
  {
    $service = new ObjectCreatorService();
    $player = $service->createObjectFromInterface(PlayerInterface::class, [],
      ["entityMaps" => [PlayerInterface::class => Player::class]]);
    self::assertInstanceOf(PlayerInterface::class, $player);
  }
//</editor-fold desc="Public Methods">
}