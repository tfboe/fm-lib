<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/24/18
 * Time: 2:55 PM
 */

namespace Tfboe\FmLib\Providers;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\MigrationServiceProvider;
use Illuminate\Support\ServiceProvider;
use LaravelDoctrine\Extensions\GedmoExtensionsServiceProvider;
use LaravelDoctrine\ORM\DoctrineServiceProvider;
use Tfboe\FmLib\Exceptions\Handler;
use Tfboe\FmLib\Service\DynamicServiceLoadingService;
use Tfboe\FmLib\Service\DynamicServiceLoadingServiceInterface;
use Tfboe\FmLib\Service\RankingSystem\EloRanking;
use Tfboe\FmLib\Service\RankingSystem\EloRankingInterface;
use Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier;
use Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService;
use Tfboe\FmLib\Service\RankingSystemService;
use Tfboe\FmLib\Service\RankingSystemServiceInterface;
use Tymon\JWTAuth\Providers\LumenServiceProvider;

class FmLibServiceProvider extends ServiceProvider
{
//<editor-fold desc="Public Methods">
  /**
   * Bootstrap the application services.
   *
   * @return void
   */
  public function boot()
  {
    //
  }

  /**
   * Register the application services.
   *
   * @return void
   */
  public function register()
  {
    $this->app->singleton(
      ExceptionHandler::class,
      Handler::class
    );

    if ($this->app->environment() !== 'production') {
      $this->app->register('\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider');
    }

    $this->app->register(LumenServiceProvider::class);
    $this->app->register(DoctrineServiceProvider::class);
    $this->app->register(GedmoExtensionsServiceProvider::class);
    try {
      //optional service providers
      $this->app->register(MigrationServiceProvider::class);
    } catch (\Exception $e) {
    }

    $this->app->singleton(DynamicServiceLoadingServiceInterface::class, function (Container $app) {
      return new DynamicServiceLoadingService($app);
    });

    $this->app->singleton(RankingSystemServiceInterface::class, function (Container $app) {
      return new RankingSystemService($app->make(DynamicServiceLoadingServiceInterface::class),
        $app->make(EntityManagerInterface::class));
    });

    $this->app->singleton(EloRankingInterface::class, function (Container $app) {
      $timeService = new RecursiveEndStartTimeService();
      $entityComparer = new EntityComparerByTimeStartTimeAndLocalIdentifier($timeService);
      return new EloRanking($app->make(EntityManagerInterface::class), $timeService, $entityComparer);
    });
  }
//</editor-fold desc="Public Methods">
}