<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 12:33 AM
 */

namespace Tfboe\FmLib\Tests\Unit\Listeners;

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\MockObject\MockObject;
use Tfboe\FmLib\Listeners\ResolveTargetEntityExtension;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class BaseControllerTest
 * @package Tests\Unit\App\Http\Controllers
 */
class ResolveTargetEntityExtensionTest extends UnitTestCase
{
  //tests also private method disable this tests as soon as all are used in public interfaces
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Listeners\ResolveTargetEntityExtension::addSubscribers
   */
  public function testAddSubscribers()
  {
    $mapping = ['EntityInterface1' => 'RealEntity1', 'EntityInterface2' => 'RealEntity2'];

    /** @var MockObject|EventManager $manager */
    $manager = $this->createMock(EventManager::class);
    /** @var MockObject|EntityManagerInterface $em */
    $em = $this->createMock(EntityManagerInterface::class);
    $manager->expects(self::once())->method('addEventSubscriber')->willReturnCallback(
      function (ResolveTargetEntityListener $listener) use ($mapping) {
        $prop = static::getProperty(ResolveTargetEntityListener::class, 'resolveTargetEntities');
        $entities = $prop->getValue($listener);
        foreach ($mapping as $key => $val) {
          self::assertArrayHasKey($key, $entities);
          self::assertArrayIsSubset(['targetEntity' => $val], $entities[$key]);
        }
      });

    Config::shouldReceive('get')
      ->once()
      ->with('fm-lib.entityMaps', [])
      ->andReturn($mapping);

    $listener = $this->listener();
    $listener->addSubscribers($manager, $em);
  }

  /**
   * @covers \Tfboe\FmLib\Listeners\ResolveTargetEntityExtension::getFilters
   */
  public function testGetFilters()
  {
    $listeners = $this->listener();
    self::assertEquals([], $listeners->getFilters());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return ResolveTargetEntityExtension
   */
  private function listener(): ResolveTargetEntityExtension
  {
    return new ResolveTargetEntityExtension();
  }
//</editor-fold desc="Private Methods">
}