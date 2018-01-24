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
 * Class TeamMode
 * @package Tfboe\FmLib\Entity\Categories
 */
abstract class TeamMode extends BasicEnum
{
//<editor-fold desc="Fields">
  const DOUBLE = 0;
  const DYP = 2;
  const SINGLE = 1;
//</editor-fold desc="Fields">
}