<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 12:33 AM
 */

namespace Tfboe\FmLib\Tests\Unit\Providers;

use Illuminate\Contracts\Foundation\Application;
use PHPUnit\Framework\MockObject\MockObject;
use Tfboe\FmLib\Providers\ServiceProvider;
use Tfboe\FmLib\TestHelpers\UnitTestCase;

/**
 * Class BaseControllerTest
 * @package Tests\Unit\App\Http\Controllers
 */
class ServiceProviderTest extends UnitTestCase
{
  //tests also private method disable this tests as soon as all are used in public interfaces
//<editor-fold desc="Public Methods">

  /**
   * @covers \Tfboe\FmLib\Providers\ServiceProvider::register
   */
  public function testRegister()
  {
    $app = $this->createMock(Application::class);

    $app->expects(self::exactly(3))->method('singleton')->withConsecutive(
      ['Interface', 'Implementation'],
      ['ClassInterface', 'Class'],
      ['OtherClass']
    );

    $provider = $this->provider($app);
    $prop = static::getProperty(ServiceProvider::class, 'singletons');
    $prop->setValue($provider, ['Interface' => 'Implementation', 'ClassInterface', 'OtherClass']);

    $provider->register();
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @param null|Application|MockObject $app
   * @return MockObject|ServiceProvider
   */
  private function provider($app = null): MockObject
  {
    if ($app === null) {
      $app = $this->createMock(Application::class);
    }
    return $this->getMockForAbstractClass(ServiceProvider::class, [$app]);
  }
//</editor-fold desc="Private Methods">
}