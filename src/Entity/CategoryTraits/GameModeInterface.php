<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:26 PM
 */

namespace Tfboe\FmLib\Entity\CategoryTraits;

/**
 * Interface GameModeInterface
 * @package Tfboe\FmLib\Entity\CategoryTraits
 */
interface GameModeInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return int|null
   */
  public function getGameMode(): ?int;

  /**
   * @param int|null $gameMode
   * @return $this|GameMode
   */
  public function setGameMode(?int $gameMode);
//</editor-fold desc="Public Methods">
}