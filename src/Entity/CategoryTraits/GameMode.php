<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/22/17
 * Time: 5:38 PM
 */

namespace Tfboe\FmLib\Entity\CategoryTraits;

/**
 * Trait GameMode
 * @package Tfboe\FmLib\Entity\CategoryTraits
 */
trait GameMode
{
//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="smallint", nullable=true)
   * @var int|null
   */
  private $gameMode;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return int|null
   */
  public function getGameMode(): ?int
  {
    return $this->gameMode;
  }

  /**
   * @param int|null $gameMode
   * @return $this|GameMode
   */
  public function setGameMode(?int $gameMode)
  {
    if ($gameMode !== null) {
      \Tfboe\FmLib\Entity\Categories\GameMode::ensureValidValue($gameMode);
    }
    $this->gameMode = $gameMode;
    return $this;
  }
//</editor-fold desc="Public Methods">
}