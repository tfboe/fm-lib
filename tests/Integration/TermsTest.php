<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 8/31/18
 * Time: 10:38 AM
 */

namespace Tfboe\FmLib\Tests\Integration;


use Tfboe\FmLib\TestHelpers\LumenTestCase;
use Tfboe\FmLib\Tests\Entity\Terms;
use Tfboe\FmLib\Tests\Helpers\ApplicationGetter;

/**
 * Class TermsTest
 * @package Tfboe\FmLib\Tests\Integration
 */
class TermsTest extends LumenTestCase
{
  use ApplicationGetter;

//<editor-fold desc="Public Methods">
  public function testGetLatestTerms()
  {
    entity(Terms::class)->create(['text' => 'Terms1', 'minorVersion' => 1, 'majorVersion' => 1]);
    entity(Terms::class)->create(['text' => 'Terms2', 'minorVersion' => 5, 'majorVersion' => 1]);
    entity(Terms::class)->create(['text' => 'Terms3', 'minorVersion' => 1, 'majorVersion' => 2]);
    $expected = ['text' => 'Terms4', 'minorVersion' => 2, 'majorVersion' => 2];
    entity(Terms::class)->create($expected);

    $this->json('GET', '/getLatestTerms')->seeJsonEquals($expected);
  }
//</editor-fold desc="Public Methods">
}