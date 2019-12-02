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
  public const DOUBLE = 0;
  public const DYP = 2;
  public const SINGLE = 1;
//</editor-fold desc="Fields">
}