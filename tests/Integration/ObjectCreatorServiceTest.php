<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 2/27/18
 * Time: 7:47 PM
 */

namespace Tfboe\FmLib\Tests\Integration;


use Tfboe\FmLib\Entity\PlayerInterface;
use Tfboe\FmLib\Service\ObjectCreatorService;
use Tfboe\FmLib\TestHelpers\LumenTestCase;
use Tfboe\FmLib\Tests\Helpers\ApplicationGetter;

/**
 * Class ObjectCreatorServiceTest
 * @package Tfboe\FmLib\Tests\Integration
 */
class ObjectCreatorServiceTest extends LumenTestCase
{
  use ApplicationGetter;

//<editor-fold desc="Public Methods">
  public function testCreateObjectFromInterface()
  {
    $service = app()->make(ObjectCreatorService::class);
    $player = $service->createObjectFromInterface(PlayerInterface::class);
    self::assertInstanceOf(PlayerInterface::class, $player);
  }
//</editor-fold desc="Public Methods">
}