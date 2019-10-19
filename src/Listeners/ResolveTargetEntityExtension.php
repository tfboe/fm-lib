<?php
declare(strict_types=1);

namespace Tfboe\FmLib\Listeners;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use Illuminate\Support\Facades\Config;
use LaravelDoctrine\ORM\Extensions\Extension;

/**
 * Class ResolveTargetEntityExtension
 * @package Tfboe\FmLib\Listeners
 */
class ResolveTargetEntityExtension implements Extension
{
//<editor-fold desc="Public Methods">
  /**
   * @param EventManager $manager
   * @param EntityManagerInterface $em
   * @param Reader|null $reader
   */
  public function addSubscribers(EventManager $manager, EntityManagerInterface $em, Reader $reader = null)
  {
    $resolveTargetEntityListener = new ResolveTargetEntityListener();

    foreach (Config::get('fm-lib.entityMaps', []) as $interface => $concrete) {
      $resolveTargetEntityListener->addResolveTargetEntity($interface, $concrete, []);
    }

    $manager->addEventSubscriber(
      $resolveTargetEntityListener
    );
  }

  /**
   * @return array
   */
  public function getFilters()
  {
    return [];
  }
//</editor-fold desc="Public Methods">
}