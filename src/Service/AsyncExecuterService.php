<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/28/18
 * Time: 5:41 PM
 */

namespace Tfboe\FmLib\Service;


class AsyncExecuterService implements AsyncExecuterServiceInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @param $command
   */
  public function runBashCommand($command)
  {
    if (substr(php_uname(), 0, 7) == "Windows") {
      pclose(popen("start /B " . $command, "r"));
    } else {
      exec($command . " > /dev/null &");
    }
  }
//</editor-fold desc="Public Methods">
}