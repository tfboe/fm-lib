<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/24/18
 * Time: 2:55 PM
 */

namespace Tfboe\FmLib\Providers;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Validator;
use Irazasyed\JwtAuthGuard\JwtAuthGuardServiceProvider;
use LaravelDoctrine\Extensions\GedmoExtensionsServiceProvider;
use LaravelDoctrine\ORM\DoctrineServiceProvider;
use Tfboe\FmLib\Entity\Helpers\UTCDateTimeType;
use Tfboe\FmLib\Exceptions\Handler;
use Tfboe\FmLib\Http\Middleware\Authenticate;
use Tfboe\FmLib\Service\AsyncExecutorServiceInterface;
use Tfboe\FmLib\Service\DynamicServiceLoadingServiceInterface;
use Tfboe\FmLib\Service\LoadingServiceInterface;
use Tfboe\FmLib\Service\ObjectCreatorServiceInterface;
use Tfboe\FmLib\Service\PlayerServiceInterface;
use Tfboe\FmLib\Service\RankingSystem\EloRanking;
use Tfboe\FmLib\Service\RankingSystem\EloRankingInterface;
use Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier;
use Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService;
use Tfboe\FmLib\Service\RankingSystemServiceInterface;
use Tfboe\FmLib\Service\TermsServiceInterface;
use Tymon\JWTAuth\Providers\LumenServiceProvider;

/**
 * Class FmLibServiceProvider
 * @package Tfboe\FmLib\Providers
 */
class FmLibServiceProvider extends ServiceProvider
{

//<editor-fold desc="Fields">
  protected $singletons = [
    ExceptionHandler::class => Handler::class,
    DynamicServiceLoadingServiceInterface::class,
    RankingSystemServiceInterface::class,
    ObjectCreatorServiceInterface::class,
    LoadingServiceInterface::class,
    PlayerServiceInterface::class,
    AsyncExecutorServiceInterface::class,
    TermsServiceInterface::class
  ];
//</editor-fold desc="Fields">
//<editor-fold desc="Public Methods">

  /**
   * Bootstrap the application services.
   *
   * @return void
   */
  public function boot()
  {
    $this->app->configure('fm-lib');

    Validator::extend('IntegerType', function (/** @noinspection PhpUnusedParameterInspection */
      $attribute, $value, $parameters, $validator) {
      return is_int($value);
    }, 'The :attribute must be an integer.');
  }

  /**
   * Register the application services.
   *
   * @return void
   * @throws DBALException
   */
  public function register()
  {
    $this->app->register(LumenServiceProvider::class);
    parent::register();
    //register middleware
    $this->app->routeMiddleware(['auth' => Authenticate::class]);

    if ($this->app->environment() !== 'production') {
      if (class_exists('\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider')) {
        $this->app->register('\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider');
      }
    }

    // \Doctrine\DBAL\DBALException datetime is a valid type
    Type::overrideType('datetime', UTCDateTimeType::class);

    $this->app->register(DoctrineServiceProvider::class);
    $this->app->register(GedmoExtensionsServiceProvider::class);
    $this->app->register(JwtAuthGuardServiceProvider::class);

    $this->app->singleton(EloRankingInterface::class, function (Container $app) {
      $timeService = new RecursiveEndStartTimeService();
      return new EloRanking(
        $app->make(EntityManagerInterface::class),
        $timeService,
        new EntityComparerByTimeStartTimeAndLocalIdentifier($timeService),
        $app->make(ObjectCreatorServiceInterface::class));
    });

    include __DIR__ . '/../routes.php';
  }
//</editor-fold desc="Public Methods">
}