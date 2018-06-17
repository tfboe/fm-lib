<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/24/18
 * Time: 2:55 PM
 */

namespace Tfboe\FmLib\Providers;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Irazasyed\JwtAuthGuard\JwtAuthGuardServiceProvider;
use LaravelDoctrine\Extensions\GedmoExtensionsServiceProvider;
use LaravelDoctrine\ORM\DoctrineServiceProvider;
use Tfboe\FmLib\Entity\Helpers\UTCDateTimeType;
use Tfboe\FmLib\Exceptions\Handler;
use Tfboe\FmLib\Http\Middleware\Authenticate;
use Tfboe\FmLib\Service\DeletionService;
use Tfboe\FmLib\Service\DeletionServiceInterface;
use Tfboe\FmLib\Service\DynamicServiceLoadingService;
use Tfboe\FmLib\Service\DynamicServiceLoadingServiceInterface;
use Tfboe\FmLib\Service\LoadingService;
use Tfboe\FmLib\Service\LoadingServiceInterface;
use Tfboe\FmLib\Service\ObjectCreatorService;
use Tfboe\FmLib\Service\ObjectCreatorServiceInterface;
use Tfboe\FmLib\Service\PlayerService;
use Tfboe\FmLib\Service\PlayerServiceInterface;
use Tfboe\FmLib\Service\RankingSystem\EloRanking;
use Tfboe\FmLib\Service\RankingSystem\EloRankingInterface;
use Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier;
use Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService;
use Tfboe\FmLib\Service\RankingSystemService;
use Tfboe\FmLib\Service\RankingSystemServiceInterface;
use Tymon\JWTAuth\Providers\LumenServiceProvider;

/**
 * Class FmLibServiceProvider
 * @package Tfboe\FmLib\Providers
 */
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
    app()->configure('fm-lib');

    /** @noinspection PhpUndefinedMethodInspection */
    Validator::extend('IntegerType', function (/** @noinspection PhpUnusedParameterInspection */
      $attribute, $value, $parameters, $validator) {
      return is_int($value);
    }, 'The :attribute must be an integer.');

    include __DIR__ . '/../routes.php';
  }

  /** @noinspection PhpDocMissingThrowsInspection */ //\Doctrine\DBAL\DBALException
  /**
   * Register the application services.
   *
   * @return void
   */
  public function register()
  {
    //register middleware
    app()->routeMiddleware(['auth' => Authenticate::class]);

    $this->app->singleton(
      ExceptionHandler::class,
      Handler::class
    );

    if ($this->app->environment() !== 'production') {
      if (class_exists('\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider')) {
        $this->app->register('\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider');
      }
    }

    /** @noinspection PhpUnhandledExceptionInspection */ // \Doctrine\DBAL\DBALException datetime is a valid type
    Type::overrideType('datetime', UTCDateTimeType::class);

    $this->app->register(LumenServiceProvider::class);
    $this->app->register(DoctrineServiceProvider::class);
    $this->app->register(GedmoExtensionsServiceProvider::class);
    $this->app->register(JwtAuthGuardServiceProvider::class);

    $this->app->singleton(DynamicServiceLoadingServiceInterface::class, function (Container $app) {
      return new DynamicServiceLoadingService($app);
    });

    $this->app->singleton(RankingSystemServiceInterface::class, function (Container $app) {
      return new RankingSystemService($app->make(DynamicServiceLoadingServiceInterface::class),
        $app->make(EntityManagerInterface::class));
    });

    $this->app->singleton(ObjectCreatorServiceInterface::class, function () {
      return new ObjectCreatorService();
    });

    $this->app->singleton(EloRankingInterface::class, function (Container $app) {
      $timeService = new RecursiveEndStartTimeService();
      return new EloRanking(
        $app->make(EntityManagerInterface::class),
        $timeService,
        new EntityComparerByTimeStartTimeAndLocalIdentifier($timeService),
        $app->make(ObjectCreatorServiceInterface::class));
    });

    $this->app->singleton(LoadingServiceInterface::class, function (Container $app) {
      return new LoadingService($app->make(EntityManagerInterface::class));
    });

    $this->app->singleton(PlayerServiceInterface::class, function (Container $app) {
      return new PlayerService(
        $app->make(EntityManagerInterface::class),
        $app->make(LoadingServiceInterface::class),
        $app->make(RankingSystemServiceInterface::class)
      );
    });
  }
//</editor-fold desc="Public Methods">
}