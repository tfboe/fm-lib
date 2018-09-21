<?php
declare(strict_types=1);

namespace Tfboe\FmLib\Tests\Integration;

use LaravelDoctrine\ORM\Facades\EntityManager;
use Tfboe\FmLib\Entity\UserInterface;
use Tfboe\FmLib\TestHelpers\DatabaseTestCase;
use Tfboe\FmLib\Tests\Entity\User;
use Tfboe\FmLib\Tests\Helpers\ApplicationGetter;

/**
 * Class UserUnauthenticatedTest
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class UserUnauthenticatedTest extends DatabaseTestCase
{
  use ApplicationGetter;

//<editor-fold desc="Public Methods">
  public function testAuthenticationError()
  {
    $this->json('GET', '/userId')->seeStatusCode(401)->seeJsonEquals(
      ["status" => 401, "message" => "Not logged in!",
        "name" => "AuthenticationException"]);
  }

  public function testCannotRecognizeExistingUsername()
  {
    $password = $this->newPassword();
    /** @var UserInterface $user */
    $user = entity(User::class)->create(['originalPassword' => $password]);
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => $password . "wrong-password"
    ]);
    $headers1 = $this->response->headers->all();
    if (array_key_exists("date", $headers1)) {
      unset($headers1["date"]);
    }
    $content1 = $this->response->content();

    $this->json('POST', '/login', [
      'email' => $user->getEmail() . "wrong-email",
      'password' => $password . "wrong-password"
    ]);

    $headers2 = $this->response->headers->all();
    if (array_key_exists("date", $headers2)) {
      unset($headers2["date"]);
    }
    $content2 = $this->response->content();
    self::assertEquals(json_encode($headers1), json_encode($headers2));
    self::assertEquals($content1, $content2);
  }

  public function testDoubleEmail()
  {
    $user = entity(User::class)->create(['originalPassword' => 'testPassword']);
    $this->json('POST', '/register', [
      'email' => $user->getEmail(),
      'password' => 'testPassword2',
      'confirmedTermsMinorVersion' => 0,
      'confirmedTermsMajorVersion' => 1,
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["email" => ["The email has already been taken."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testEmailRequiredValidation()
  {
    $this->json('POST', '/register', [
      'password' => 'testPassword',
      'confirmedTermsMinorVersion' => 0,
      'confirmedTermsMajorVersion' => 1,
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["email" => ["The email field is required."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testEmailRequiredValidationLogin()
  {
    entity(User::class)->create(['originalPassword' => 'testPassword']);
    $this->json('POST', '/login', [
      'password' => 'testPassword',
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["email" => ["The email field is required."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testInvalidCredentials()
  {
    $password = $this->newPassword();
    /** @var UserInterface $user */
    $user = entity(User::class)->create(['originalPassword' => $password]);
    /** @noinspection PhpUnhandledExceptionInspection */
    $property = self::getProperty(User::class, 'id');
    $property->setValue($user, "\x84invalid");
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => $password . "wrong-password"
    ])->seeStatusCode(401);
    self::assertNull($this->response->headers->get('jwt-token'));
  }

  public function testInvalidEmailValidation()
  {
    $this->json('POST', '/register', [
      'email' => 'invalidEmail',
      'password' => 'testPassword',
      'confirmedTermsMinorVersion' => 0,
      'confirmedTermsMajorVersion' => 1,
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["email" => ["The email must be a valid email address."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testInvalidEmailValidationLogin()
  {
    entity(User::class)->create(['originalPassword' => 'testPassword']);
    $this->json('POST', '/login', [
      'email' => 'invalidEmail',
      'password' => 'testPassword'
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["email" => ["The email must be a valid email address."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testInvalidId()
  {
    $password = $this->newPassword();
    /** @var UserInterface $user */
    $user = entity(User::class)->create(['originalPassword' => $password]);
    /** @noinspection PhpUnhandledExceptionInspection */
    $property = self::getProperty(User::class, 'id');
    $property->setValue($user, "\x84invalid");
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => $password
    ])->seeStatusCode(401);
    self::assertNull($this->response->headers->get('jwt-token'));
  }

  public function testInvalidLastConfirmedTermsMinorVersion()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'password' => 'testPassword',
      'confirmedTermsMinorVersion' => 'noInt',
      'confirmedTermsMajorVersion' => 1,
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["confirmedTermsMinorVersion" =>
      ["The confirmed terms minor version must be an integer."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testInvalidLastConfirmedTermsMajorVersion()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'password' => 'testPassword',
      'confirmedTermsMajorVersion' => 'noInt',
      'confirmedTermsMinorVersion' => 0,
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["confirmedTermsMajorVersion" =>
      ["The confirmed terms major version must be an integer."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testLogin()
  {
    $password = $this->newPassword();
    /** @var UserInterface $user */
    $user = entity(User::class)->create(['originalPassword' => $password]);
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => $password
    ])->seeJsonEquals(['id' => $user->getId()])->seeHeader('jwt-token');
    self::assertNotNull($this->response->headers->get('jwt-token'));
  }

  public function testMultipleValidationErrors()
  {
    $this->json('POST', '/register', [
      'password' => 5,
      'confirmedTermsMinorVersion' => 0,
      'confirmedTermsMajorVersion' => 1,
    ])->seeStatusCode(422)->seeJsonEquals(
      ["errors" => [
        "email" => ["The email field is required."],
        "password" => ["The password must be a string.", "The password must be at least 8 characters."]
      ], "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testNegativeLastConfirmedTermsMinorVersion()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'password' => 'testPassword',
      'confirmedTermsMinorVersion' => -1,
      'confirmedTermsMajorVersion' => 1,
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["confirmedTermsMinorVersion" =>
      ["The confirmed terms minor version must be at least 0."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testZeroLastConfirmedTermsMajorVersion()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'password' => 'testPassword',
      'confirmedTermsMajorVersion' => 0,
      'confirmedTermsMinorVersion' => 0
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["confirmedTermsMajorVersion" =>
      ["The confirmed terms major version must be at least 1."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testNoStringPassword()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'password' => 16511233,
      'confirmedTermsMinorVersion' => 0,
      'confirmedTermsMajorVersion' => 1
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["password" => ["The password must be a string."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testNoStringPasswordLogin()
  {
    /** @var UserInterface $user */
    $user = entity(User::class)->create(['originalPassword' => 'testPassword']);
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => 16511233
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["password" => ["The password must be a string."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testPasswordRequiredValidation()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'confirmedTermsMinorVersion' => 0,
      'confirmedTermsMajorVersion' => 1
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["password" => ["The password field is required."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testPasswordRequiredValidationLogin()
  {
    /** @var UserInterface $user */
    $user = entity(User::class)->create(['originalPassword' => 'testPassword']);
    $this->json('POST', '/login', [
      'email' => $user->getEmail()
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["password" => ["The password field is required."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testRegisterUser()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'password' => 'testPassword',
      'confirmedTermsMinorVersion' => 0,
      'confirmedTermsMajorVersion' => 1
    ])->seeJsonStructure(['id']);
  }

  public function testRegistrationWithTermsVersions()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'password' => 'testPassword',
      'confirmedTermsMinorVersion' => 5,
      'confirmedTermsMajorVersion' => 7
    ])->seeJsonStructure(['id']);
    $result = json_decode($this->response->getContent(), true);
    /** @var UserInterface $user */
    /** @noinspection PhpUndefinedMethodInspection */
    $user = EntityManager::find(User::class, $result['id']);
    self::assertEquals(5, $user->getConfirmedTermsMinorVersion());
    self::assertEquals(7, $user->getConfirmedTermsMajorVersion());
  }

  //TODO: handle this test case (related to integer-type validation which is at the moment deactivated due to a
  //      incompatibility to lumen 5.7, see src/Providers/FmLibServiceProvider.php
  /*public function testRegistrationWithStringAsConfirmedTermsMinorVersion()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'password' => 'testPassword',
      'confirmedTermsMinorVersion' => "5",
      'confirmedTermsMajorVersion' => 1
    ])->seeStatusCode(422)->seeJsonEquals(["errors" =>
      ["confirmedTermsMinorVersion" => ["The confirmed terms minor version must be an integer."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }*/

  public function testTooShortPassword()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'password' => 'short',
      'confirmedTermsMinorVersion' => 0,
      'confirmedTermsMajorVersion' => 1
    ])->seeStatusCode(422)->seeJsonEquals(["errors" =>
      ["password" => ["The password must be at least 8 characters."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testTooShortPasswordLogin()
  {
    /** @var UserInterface $user */
    $user = entity(User::class)->create(['originalPassword' => 'testPassword']);
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => 'short'
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => [
      "password" => ["The password must be at least 8 characters."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testWrongPassword()
  {
    $password = $this->newPassword();
    /** @var UserInterface $user */
    $user = entity(User::class)->create(['originalPassword' => $password]);
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => $password . "wrong-password"
    ])->seeStatusCode(401);
    self::assertNull($this->response->headers->get('jwt-token'));
  }

  public function testWrongUsername()
  {
    $password = $this->newPassword();
    /** @var UserInterface $user */
    $user = entity(User::class)->create(['originalPassword' => $password]);
    $this->json('POST', '/login', [
      'email' => $user->getEmail() . "wrong-email",
      'password' => $password
    ])->seeStatusCode(401);
    self::assertNull($this->response->headers->get('jwt-token'));
  }
//</editor-fold desc="Public Methods">
}
