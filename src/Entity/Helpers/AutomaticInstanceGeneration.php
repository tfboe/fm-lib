<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 8:53 PM
 */

namespace Tfboe\FmLib\Entity\Helpers;


use Tfboe\FmLib\Helpers\BasicEnum;

/**
 * Class AutomaticInstanceGeneration
 * @package Tfboe\FmLib\Entity\Helpers
 */
class AutomaticInstanceGeneration extends BasicEnum
{
//<editor-fold desc="Fields">
  public const MONTHLY = 1;
  public const OFF = 0;
//</editor-fold desc="Fields">
}