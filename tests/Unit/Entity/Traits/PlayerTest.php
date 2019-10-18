<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Entity\Traits;

use DateTime;
use Tfboe\FmLib\Tests\Entity\Player;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class PlayerTest
 * @package Tfboe\FmLib\Tests\Unit\Entity
 */
class PlayerTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Player::setBirthday
   * @covers \Tfboe\FmLib\Entity\Traits\Player::getBirthday
   */
  public function testBirthday()
  {
    $player = $this->player();
    $player->setBirthday(new DateTime('1992-02-02 02:02:02'));
    self::assertEquals(new DateTime('1992-02-02 02:02:02'), $player->getBirthday());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Player::setFirstName
   * @covers \Tfboe\FmLib\Entity\Traits\Player::getFirstName
   */
  public function testFirstName()
  {
    $player = $this->player();
    $player->setFirstName("First");
    self::assertEquals("First", $player->getFirstName());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\Player::setLastName
   * @covers \Tfboe\FmLib\Entity\Traits\Player::getLastName
   */
  public function testLastName()
  {
    $player = $this->player();
    $player->setLastName("Last");
    self::assertEquals("Last", $player->getLastName());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return Player a new player
   */
  private function player(): Player
  {
    return new Player();
  }
//</editor-fold desc="Private Methods">
}