<?php
declare(strict_types=1);


namespace Tfboe\FmLib\Service;


use Illuminate\Contracts\Container\BindingResolutionException;
use Tfboe\FmLib\Service\RankingSystem\RankingSystemInterface;

/**
 * Interface DynamicServiceLoadingServiceInterface
 * @package Tfboe\FmLib\Service
 */
interface DynamicServiceLoadingServiceInterface
{

//<editor-fold desc="Public Methods">
  /**
   * Loads the ranking service specified by name
   * @param string $name the name of the ranking service
   * @return RankingSystemInterface the service
   * @throws BindingResolutionException
   */
  public function loadRankingSystemService(string $name): RankingSystemInterface;
//</editor-fold desc="Public Methods">
}