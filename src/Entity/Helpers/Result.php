<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 12/16/17
 * Time: 1:06 PM
 */

namespace Tfboe\FmLib\Entity\Helpers;

use Tfboe\FmLib\Helpers\BasicEnum;

/**
 * Class Result
 * @package Tfboe\FmLib\Entity\Helpers
 */
abstract class Result extends BasicEnum
{
//<editor-fold desc="Fields">
  public const DRAW = 2;
  public const NOT_YET_FINISHED = 4;
  public const NULLED = 3;
  public const TEAM_A_WINS = 0;
  public const TEAM_B_WINS = 1;
//</editor-fold desc="Fields">
}