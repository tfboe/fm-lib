<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/22/17
 * Time: 6:28 PM
 */

namespace Tfboe\FmLib\Entity\Categories;

use Tfboe\FmLib\Helpers\BasicEnum;


/**
 * Class Table
 * @package Tfboe\FmLib\Entity\Categories
 */
abstract class Table extends BasicEnum
{
  //<editor-fold desc="Fields">
  public const BONZINI = 5; // only used if real game table is unknown / forgotten in multi table tournament
  public const GARLANDO = 1;
  public const LEONHART = 2;
  public const MULTITABLE = 0;
  public const ROBERTO_SPORT = 4;
  public const TORNADO = 3;
//</editor-fold desc="Fields">
}