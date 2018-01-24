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
  const COMPETITION = 3;
  const GAME = 0;
  const MATCH = 1;
  const PHASE = 2;
  const TOURNAMENT = 4;
//</editor-fold desc="Fields">
}