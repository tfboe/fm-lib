<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/16/17
 * Time: 2:04 PM
 */

namespace Tfboe\FmLib\Tests\Integration;

use LaravelDoctrine\ORM\Facades\EntityManager;
use Tfboe\FmLib\Entity\UserInterface;
use Tfboe\FmLib\TestHelpers\AuthenticatedTestCase;
use Tfboe\FmLib\Tests\Entity\User;
use Tfboe\FmLib\Tests\Helpers\ApplicationGetter;

/**
 * Class UserAuthenticatedTest
 * @package Tests\Integration
 */
class UserAuthenticatedTest extends AuthenticatedTestCase
{
  use ApplicationGetter;

//<editor-fold desc="Public Methods">
  public function testInvalidateToken()
  {
    /** @var UserInterface $user */
    $user = EntityManager::find(User::class, $this->user->getId());
    $user->setJwtVersion(2);
    EntityManager::flush();
    $this->jsonAuth('GET', '/userId')->seeStatusCode(401);
    self::assertNull($this->response->headers->get('jwt-token'));
  }

  public function testUserId()
  {
    $this->jsonAuth('GET', '/userId')->seeJsonEquals(['id' => $this->user->getId()]);
  }
//</editor-fold desc="Public Methods">
}