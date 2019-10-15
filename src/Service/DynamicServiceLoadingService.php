<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/17
 * Time: 7:29 PM
 */

namespace Tfboe\FmLib\Service;

use Illuminate\Contracts\Container\Container;
use Tfboe\FmLib\Service\RankingSystem\RankingSystemInterface;

/**
 * Class DynamicLoadingService
 * @package Tfboe\FmLib\Service
 */
class DynamicServiceLoadingService implements DynamicServiceLoadingServiceInterface
{
//<editor-fold desc="Fields">
  /** @var Container */
  private $app;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * DynamicServiceLoadingService constructor.
   * @param Container $app
   */
  public function __construct(Container $app)
  {
    $this->app = $app;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @inheritdoc
   */
  public function loadRankingSystemService(string $name): RankingSystemInterface
  {
    return $this->app->make($this->getClassWithNamespace($name, 'Tfboe\FmLib\Service\RankingSystem'));
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Methods">
  /**
   * Gets the full name of the given class with respect to the given namespace
   * @param string $class the class name
   * @param string $namespace the namespace
   * @return string the full class name (with namespace) and with interface
   */
  protected function getClassWithNamespace(string $class, string $namespace): string
  {
    if (strpos($class, 'Interface') === false) {
      $class .= 'Interface';
    }
    if (substr($class, strlen($namespace)) !== $namespace) {
      return $namespace . '\\' . $class;
    } else {
      return $class;
    }
  }
//</editor-fold desc="Protected Methods">
//<editor-fold desc="Private Methods">
//</editor-fold desc="Private Methods">
}