<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/16/17
 * Time: 2:04 PM
 */

namespace Tfboe\FmLib\TestHelpers;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Illuminate\Support\Facades\Auth;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tfboe\FmLib\Entity\UserInterface;

/**
 * Class AuthenticatedTestCase
 * @package Tfboe\FmLib\TestHelpers
 */
abstract class AuthenticatedTestCase extends DatabaseTestCase
{
//<editor-fold desc="Fields">
  /**
   * Authentication token if logged in
   * @var string
   */
  protected $token;

  /**
   * User corresponding to authentication token if logged in
   * @var UserInterface
   */
  protected $user;

  /**
   * Password corresponding to logged in user
   * @var string
   */
  protected $userPassword;
//</editor-fold desc="Fields">

//<editor-fold desc="Protected Methods">
  /**
   * @return string[]
   */
  protected function getClassesToClear(): array
  {
    return [UserInterface::class];
  }

  /**
   * sends a json request with an authentication token
   * @param string $method the method to use (GET, POST, ...)
   * @param string $uri the uri of the request
   * @param array $data the post data
   * @param array $headers the request headers
   * @return $this
   */
  protected function jsonAuth(string $method, string $uri, array $data = [], array $headers = [])
  {
    $headers['Authorization'] = 'Bearer ' . $this->token;
    return $this->json($method, $uri, $data, $headers);
  }

  /**
   * @throws DBALException
   */
  protected function workOnDatabaseDestroy()
  {
    $this->clearUsers();
    parent::workOnDatabaseDestroy();
  }

  /**
   * @throws DBALException
   */
  protected function workOnDatabaseSetUp()
  {
    $this->clearUsers();
    parent::workOnDatabaseSetUp();

    $userInfo = $this->createUser();
    $this->user = $userInfo['user'];
    $this->userPassword = $userInfo['password'];

    $this->token = (string)Auth::attempt(['email' => $this->user->getEmail(), 'password' => $userInfo['password']]);
    $this->refreshApplication();
    /** @noinspection PhpUndefinedMethodInspection */
    $this->user = EntityManager::find(UserInterface::class, $this->user->getId());
  }
//</editor-fold desc="Protected Methods">

//<editor-fold desc="Private Methods">
  /**
   * @throws DBALException
   */
  private function clearUsers()
  {
    /** @var Connection $connection */
    /** @noinspection PhpUndefinedMethodInspection */
    $connection = EntityManager::getConnection();
    $this->clearClassTables($this->getClassesToClear());
    /** @noinspection PhpUndefinedMethodInspection */
    $sql = sprintf('SET FOREIGN_KEY_CHECKS=0;TRUNCATE TABLE %s;SET FOREIGN_KEY_CHECKS=1;',
      EntityManager::getClassMetadata(UserInterface::class)->getTableName());

    $connection->query($sql);
  }
//</editor-fold desc="Private Methods">
}