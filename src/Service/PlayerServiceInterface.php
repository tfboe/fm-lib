<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/29/17
 * Time: 11:09 PM
 */

namespace Tfboe\FmLib\Service;


use Tfboe\FmLib\Entity\PlayerInterface;

/**
 * Interface PlayerServiceInterface
 * @package App\Service
 */
interface PlayerServiceInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @param PlayerInterface $player
   * @param PlayerInterface $toMerge
   * @return mixed
   */
  public function mergePlayers(PlayerInterface $player, PlayerInterface $toMerge);
//</editor-fold desc="Public Methods">
}