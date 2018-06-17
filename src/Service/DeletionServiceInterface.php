<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 2/2/18
 * Time: 5:15 PM
 */

namespace Tfboe\FmLib\Service;

use Tfboe\FmLib\Entity\PlayerInterface;


/**
 * Interface DeletionServiceInterface
 * @package App\Services
 */
interface DeletionServiceInterface
{
//<editor-fold desc="Public Methods">

  /**
   * @param PlayerInterface $player
   */
  public function deletePlayer(PlayerInterface $player): void;
//</editor-fold desc="Public Methods">
}