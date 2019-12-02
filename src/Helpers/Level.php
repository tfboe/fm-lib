<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/4/18
 * Time: 3:10 PM
 */

namespace Tfboe\FmLib\Helpers;


/**
 * Class Level
 * @package Tfboe\FmLib\Helpers
 */
abstract class Level extends BasicEnum
{
//<editor-fold desc="Fields">
  public const COMPETITION = 3;
  public const GAME = 0;
  public const MATCH = 1;
  public const PHASE = 2;
  public const TOURNAMENT = 4;
//</editor-fold desc="Fields">
}