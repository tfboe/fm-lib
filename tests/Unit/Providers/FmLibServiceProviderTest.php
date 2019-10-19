<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 12:33 AM
 */

namespace Tfboe\FmLib\Tests\Unit\Providers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\Router;
use Laravel\Lumen\Application;
use PHPUnit\Framework\MockObject\MockObject;
use Tfboe\FmLib\Http\Middleware\Authenticate;
use Tfboe\FmLib\Providers\FmLibServiceProvider;
use Tfboe\FmLib\Service\RankingSystem\EloRankingInterface;
use Tfboe\FmLib\TestHelpers\UnitTestCase;

/**
 * Class BaseControllerTest
 * @package Tests\Unit\App\Http\Controllers
 */
class FmLibServiceProviderTest extends UnitTestCase
{
  //tests also private method disable this tests as soon as all are used in public interfaces
//<editor-fold desc="Public Methods">

  /**
   * @covers \Tfboe\FmLib\Providers\FmLibServiceProvider::boot
   */
  public function testBoot()
  {
    $app = $this->createMock(Application::class);
    $app->expects(self::once())->method('configure')->with('fm-lib');
    $provider = $this->provider($app);
    $provider->boot();
  }

  /**
   * @covers \Tfboe\FmLib\Providers\FmLibServiceProvider::register
   * @uses   \Tfboe\FmLib\Providers\ServiceProvider::register
   * @uses   \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier::__construct
   * @uses   \Tfboe\FmLib\Service\RankingSystem\RankingSystemService::__construct
   */
  public function testRegister()
  {
    $app = $this->createMock(Application::class);
    $router = $this->createMock(Router::class);
    $app->expects(self::once())->method('make')->with('router')->willReturn($router);
    $app->expects(self::once())->method('routeMiddleware')->with(['auth' => Authenticate::class]);
    $app->expects(self::atLeastOnce())->method('singleton')->willReturnCallback(function ($x, $y) {
      if ($x === EloRankingInterface::class) {
        $app2 = $this->createMock(Container::class);
        $app2->method('make')->willReturnCallback(function ($c) {
          return $this->createStub($c);
        });
        $inst = $y($app2);
        self::assertInstanceOf(EloRankingInterface::class, $inst);
      }
    });
    $provider = $this->provider($app);
    $provider->register();
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @param null|Application|MockObject|\Illuminate\Contracts\Foundation\Application $app
   * @return FmLibServiceProvider
   */
  private function provider($app = null): FmLibServiceProvider
  {
    if ($app === null) {
      $app = $this->createMock(Application::class);
    }
    return new FmLibServiceProvider($app);
  }
//</editor-fold desc="Private Methods">
}