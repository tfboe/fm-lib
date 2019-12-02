<?php
declare(strict_types=1);


namespace Tfboe\FmLib\Entity\Helpers;

use Tfboe\FmLib\Helpers\BasicEnum;

/**
 * Class StartFinishStatus
 * @package Tfboe\FmLib\Entity\Helpers
 */
abstract class StartFinishStatus extends BasicEnum
{
//<editor-fold desc="Fields">
  public const FINISHED = 2;
  public const NOT_STARTED = 0;
  public const STARTED = 1;
//</editor-fold desc="Fields">
}