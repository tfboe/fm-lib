<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/18/17
 * Time: 10:22 PM
 */

namespace Tfboe\FmLib\Entity\Categories;

use Tfboe\FmLib\Helpers\BasicEnum;


/**
 * Class GameMode
 * @package Tfboe\FmLib\Entity\Categories
 */
abstract class GameMode extends BasicEnum
{
//<editor-fold desc="Fields">
  const CLASSIC = 2;
  const OFFICIAL = 0;
  const SPEEDBALL = 1;
//</editor-fold desc="Fields">
}