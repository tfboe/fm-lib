<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:27 PM
 */

namespace Tfboe\FmLib\Entity\CategoryTraits;


use Tfboe\FmLib\Exceptions\ValueNotValid;

/**
 * Interface ScoreModeInterface
 * @package Tfboe\FmLib\Entity\CategoryTraits
 */
interface ScoreModeInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return int|null
   */
  public function getScoreMode(): ?int;

  /**
   * @param int|null $scoreMode
   * @return $this|ScoreMode
   * @throws ValueNotValid
   */
  public function setScoreMode(?int $scoreMode);
//</editor-fold desc="Public Methods">
}