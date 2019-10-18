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
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use ReflectionException;
use Tfboe\FmLib\Entity\CompetitionInterface;
use Tfboe\FmLib\Entity\PlayerInterface;
use Tfboe\FmLib\Entity\TeamInterface;
use Tfboe\FmLib\Entity\TeamMembershipInterface;
use Tfboe\FmLib\Tests\Entity\Player;
use Tfboe\FmLib\Tests\Entity\TeamMembership;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class TeamMembershipTest
 * @package Tfboe\FmLib\Tests\Unit\Entity\Traits
 */
class TeamMembershipTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Traits\TeamMembership::getEnd
   * @covers \Tfboe\FmLib\Entity\Traits\TeamMembership::setEnd
   * @throws Exception
   * @uses   \Tfboe\FmLib\Helpers\DateTime::eq
   */
  public function testEnd()
  {
    $membership = $this->membership();

    $end = new DateTime("2019-01-01", new DateTimeZone("+00:00"));
    $membership->setEnd($end);
    self::assertEquals($end, $membership->getEnd());

    $endEq = new DateTime("2019-01-01");
    $membership->setEnd($end);
    self::assertTrue($end === $membership->getEnd());
    self::assertTrue($endEq !== $membership->getEnd());
    self::assertEquals($endEq, $membership->getEnd());

    $endEq->setTimezone(new DateTimeZone("+12:00"));
    $membership->setEnd($endEq);
    self::assertTrue($end !== $membership->getEnd());
    self::assertTrue($endEq === $membership->getEnd());
    self::assertEquals($end, $membership->getEnd());
  }

  /**
   * @covers   \Tfboe\FmLib\Entity\Traits\TeamMembership::getPlayer
   * @covers   \Tfboe\FmLib\Entity\Traits\TeamMembership::setPlayer
   */
  public function testPlayer()
  {
    $membership = $this->membership();
    /** @var Player $player */
    $player = $this->createMock(PlayerInterface::class);
    $membership->setPlayer($player);
    self::assertEquals($player, $membership->getPlayer());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\TeamMembership::getStart
   * @covers \Tfboe\FmLib\Entity\Traits\TeamMembership::setStart
   * @throws Exception
   * @uses   \Tfboe\FmLib\Helpers\DateTime::eq
   */
  public function testStart()
  {
    $membership = $this->membership();

    $start = new DateTime("2019-01-01", new DateTimeZone("+00:00"));
    $membership->setStart($start);
    self::assertEquals($start, $membership->getStart());

    $startEq = new DateTime("2019-01-01");
    $membership->setStart($start);
    self::assertTrue($start === $membership->getStart());
    self::assertTrue($startEq !== $membership->getStart());
    self::assertEquals($startEq, $membership->getStart());

    $startEq->setTimezone(new DateTimeZone("+12:00"));
    $membership->setStart($startEq);
    self::assertTrue($start !== $membership->getStart());
    self::assertTrue($startEq === $membership->getStart());
    self::assertEquals($start, $membership->getStart());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Traits\TeamMembership::getTeam
   * @covers \Tfboe\FmLib\Entity\Traits\TeamMembership::setTeam
   * @throws ReflectionException
   * @uses   \Tfboe\FmLib\Entity\Helpers\UUIDEntity::getId
   */
  public function testTeam()
  {
    $membership = $this->membership();
    /** @var TeamInterface $team */
    $team = $this->createStub(TeamInterface::class, ['getMemberships' => new ArrayCollection()]);
    self::getProperty(get_class($membership), 'id')->setValue($membership, 'membership-id');
    /** @var CompetitionInterface $competition */
    $membership->setTeam($team);
    self::assertEquals($team, $membership->getTeam());
    self::assertEquals(1, $membership->getTeam()->getMemberships()->count());
    self::assertEquals($membership, $membership->getTeam()->getMemberships()[$membership->getId()]);

    /** @var TeamInterface $team2 */
    $team2 = $this->createStub(TeamInterface::class, ['getMemberships' => new ArrayCollection()]);

    /** @var CompetitionInterface $competition2 */
    $membership->setTeam($team2);
    self::assertEquals($team2, $membership->getTeam());
    self::assertEquals(1, $membership->getTeam()->getMemberships()->count());
    self::assertEquals(0, $team->getMemberships()->count());
    self::assertEquals($membership, $membership->getTeam()->getMemberships()[$membership->getId()]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return TeamMembershipInterface a new team
   */
  private function membership(): TeamMembershipInterface
  {
    return new TeamMembership();
  }
//</editor-fold desc="Private Methods">
}