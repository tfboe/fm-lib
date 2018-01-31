<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 7:06 PM
 */

namespace Tfboe\FmLib\Tests\Helpers;

use Tfboe\FmLib\Service\ObjectCreatorServiceInterface;

/**
 * Class UnitTestCase
 * @package Tfboe\FmLib\Tests\Helpers
 */
class UnitTestCase extends \Tfboe\FmLib\TestHelpers\UnitTestCase
{
//<editor-fold desc="Protected Methods">
  /**
   * Creates an object creator which creates objects from the Tfboe\FmLib\Tests\Entity namespace
   * @return ObjectCreatorServiceInterface
   */
  protected function getObjectCreator(): ObjectCreatorServiceInterface
  {
    $objectCreatorService = $this->createMock(ObjectCreatorServiceInterface::class);
    $objectCreatorService->method('createObjectFromInterface')->willReturnCallback(function ($if, $args) {
      $class = str_replace("\\Entity\\", "\\Tests\\Entity\\", $if);
      $class = str_replace("Interface", "", $class);
      return new $class(...$args);
    });
    /** @var ObjectCreatorServiceInterface $objectCreatorService */
    return $objectCreatorService;
  }
//</editor-fold desc="Protected Methods">
}