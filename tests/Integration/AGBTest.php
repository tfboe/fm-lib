<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 8/31/18
 * Time: 10:38 AM
 */

namespace Tfboe\FmLib\Tests\Integration;


use Tfboe\FmLib\TestHelpers\LumenTestCase;
use Tfboe\FmLib\Tests\Entity\AGB;
use Tfboe\FmLib\Tests\Helpers\ApplicationGetter;

/**
 * Class AGBTest
 * @package Tfboe\FmLib\Tests\Integration
 */
class AGBTest extends LumenTestCase
{
  use ApplicationGetter;

//<editor-fold desc="Public Methods">
  public function testGetLatestAGB()
  {
    $agb1 = entity(AGB::class)->create(['text' => 'AGB1', 'minorVersion' => 1, 'majorVersion' => 1]);
    $agb2 = entity(AGB::class)->create(['text' => 'AGB2', 'minorVersion' => 5, 'majorVersion' => 1]);
    $agb3 = entity(AGB::class)->create(['text' => 'AGB3', 'minorVersion' => 1, 'majorVersion' => 2]);
    $expected = ['text' => 'AGB4', 'minorVersion' => 2, 'majorVersion' => 2];
    $agb4 = entity(AGB::class)->create($expected);

    $this->json('GET', '/getLatestAGB')->seeJsonEquals($expected);
  }
//</editor-fold desc="Public Methods">
}