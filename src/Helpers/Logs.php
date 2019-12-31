<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/5/18
 * Time: 11:17 PM
 */

namespace Tfboe\FmLib\Helpers;

/**
 * Class Logs
 * @package Tfboe\FmLib\Helpers
 */
abstract class Logs extends BasicEnum
{
//<editor-fold desc="Fields">
  public const CHANGES = "changes";
  public const GENERAL = "general";
  public const TESTING = "testing";
  public const RANKING_COMPUTATION = "ranking_computation";
//</editor-fold desc="Fields">
}