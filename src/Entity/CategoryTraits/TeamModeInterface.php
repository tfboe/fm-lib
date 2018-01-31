<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:29 PM
 */

namespace Tfboe\FmLib\Entity\CategoryTraits;


use Tfboe\FmLib\Exceptions\ValueNotValid;

/**
 * Interface TeamModeInterface
 * @package Tfboe\FmLib\Entity\CategoryTraits
 */
interface TeamModeInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return int|null
   */
  public function getTeamMode(): ?int;

  /**
   * @param int|null $teamMode
   * @return $this|TeamMode
   * @throws ValueNotValid
   */
  public function setTeamMode(?int $teamMode);
//</editor-fold desc="Public Methods">
}