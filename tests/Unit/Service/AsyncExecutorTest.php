<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 3:53 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Service {

  use Tfboe\FmLib\Service\AsyncExecutorService;
  use Tfboe\FmLib\Tests\Helpers\UnitTestCase;


  /**
   * Class PlayerServiceTest
   * @package Tfboe\FmLib\Tests\Unit\Service
   * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
   */
  class AsyncExecutorTest extends UnitTestCase
  {
//<editor-fold desc="Public Methods">
    /**
     * @covers \Tfboe\FmLib\Service\AsyncExecutorService::runBashCommand
     */
    public function testRunBashCommandOnLinux()
    {
      global $uname;
      global $lastExecCommand;
      $uname = "Non-Windows";
      $service = $this->service();
      $lastExecCommand = null;
      $service->runBashCommand("linux-command");
      self::assertEquals("linux-command > /dev/null &", $lastExecCommand);
    }

    /**
     * @covers \Tfboe\FmLib\Service\AsyncExecutorService::runBashCommand
     */
    public function testRunBashCommandOnWindows()
    {
      global $uname;
      global $lastExecCommand;
      global $lastExecFlag;
      $uname = "Windows 7.1";
      $service = $this->service();
      $lastExecCommand = null;
      $lastExecFlag = null;
      $service->runBashCommand("windows-command");
      self::assertEquals("start /B windows-command", $lastExecCommand);
      self::assertEquals("r", $lastExecFlag);
    }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Methods">
    /**
     * @inheritDoc
     */
    protected function setUp()
    {
      parent::setUp();

      global $mockGlobalFunctions;
      $mockGlobalFunctions = true;
    }
//</editor-fold desc="Protected Methods">

//<editor-fold desc="Private Methods">
    /**
     * @return AsyncExecutorService
     */
    private function service()
    {
      return new AsyncExecutorService();
    }
//</editor-fold desc="Private Methods">
  }
}

namespace Tfboe\FmLib\Service {

  $GLOBALS['mockGlobalFunctions'] = false;
  $GLOBALS['lastExecCommand'] = null;
  $GLOBALS['uname'] = null;
  $GLOBALS['lastExecFlag'] = null;


  /**
   * @param $command
   */
  function exec($command)
  {
    global $mockGlobalFunctions;
    global $lastExecCommand;
    if (isset($mockGlobalFunctions) && $mockGlobalFunctions === true) {
      $lastExecCommand = $command;
    } else {
      call_user_func_array('\exec', func_get_args());
    }
  }

  /**
   * @return mixed
   */
  function php_uname()
  {
    global $mockGlobalFunctions;
    global $uname;
    if (isset($mockGlobalFunctions) && $mockGlobalFunctions === true) {
      return $uname;
    } else {
      return call_user_func_array('\php_uname', func_get_args());
    }
  }

  function pclose()
  {
    global $mockGlobalFunctions;
    if (!isset($mockGlobalFunctions) || $mockGlobalFunctions !== true) {
      call_user_func_array('\pclose', func_get_args());
    }
  }

  /**
   * @param $command
   * @param $flag
   * @return mixed|null
   */
  function popen($command, $flag)
  {
    global $mockGlobalFunctions;
    global $lastExecCommand;
    global $lastExecFlag;
    if (isset($mockGlobalFunctions) && $mockGlobalFunctions === true) {
      $lastExecCommand = $command;
      $lastExecFlag = $flag;
      return null;
    } else {
      return call_user_func_array('\popen', func_get_args());
    }
  }
}