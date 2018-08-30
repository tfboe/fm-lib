<?php
declare(strict_types=1);

namespace Tfboe\FmLib\Http\Controllers;

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Application;
use Tfboe\FmLib\Entity\UserInterface;
use Tfboe\FmLib\Exceptions\AuthenticationException;
use Tfboe\FmLib\Service\ObjectCreatorServiceInterface;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends BaseController
{
//<editor-fold desc="Public Methods">

  /** @var ObjectCreatorServiceInterface $objectCreatorService */
  private $objectCreatorService;

  /**
   * @inheritDoc
   */
  public function __construct(EntityManagerInterface $entityManager,
                              ObjectCreatorServiceInterface $objectCreatorService)
  {
    parent::__construct($entityManager);
    $this->objectCreatorService = $objectCreatorService;
  }


  /**
   * login action, checks credentials and returns token
   * @param Request $request the http request
   * @param Application $app
   * @return JsonResponse
   * @throws AuthenticationException wrong credentials or errors during creating a token
   */
  public function login(Request $request, Application $app): JsonResponse
  {
    $specification = $this->getCredentialSpecification($app);
    $this->addAdditionalLoginSpecifications($specification);
    $this->validateBySpecification($request, $specification);

    $this->preLogin($request);

    // grab credentials from the request
    $credentials = $request->only('email', 'password');

    /** @var string $token */
    $token = null;
    try {
      // attempt to verify the credentials and create a token for the user
      $token = Auth::attempt($credentials);
      if (!$token) {
        throw new AuthenticationException('invalid credentials');
      }
    } /** @noinspection PhpRedundantCatchClauseInspection */ catch (JWTException $e) {
      // something went wrong whilst attempting to encode the token
      throw new AuthenticationException('could not create token');
    }
    return $this->getLoginResponse($request, $token);

  }

  /**
   * Method called before login token gets generated.
   * Can be used to modify token generation parameters.
   * @param Request $request
   */
  protected function preLogin(Request $request)
  {
  }

  /**
   * register action, registers a new user with email and password
   *
   * @param Request $request the http request
   * @param Application $app
   * @return JsonResponse
   */
  public function register(Request $request, Application $app): JsonResponse
  {
    $userClass = config('fm-lib')['entityMaps']['Tfboe\FmLib\Entity\UserInterface'];
    $specification = [];
    $specification['user'] = $this->getCredentialSpecification($app);
    $specification['user']['email']['validation'] .= '|unique:' . $userClass . ',email';
    $specification['user']['confirmedAGBVersion'] = ['validation' => 'integer-type|integer|min:0'];

    $this->addAdditionalRegisterSpecifications($specification);

    $this->validateBySpecification($request, array_merge(...array_values($specification)));

    $input = $request->input();
    /** @var UserInterface $user */

    $user = $this->setFromSpecification($this->newUser(), $specification['user'], $input);
    $this->getEntityManager()->persist($user); //sets the user id

    $this->createAdditionalRegisterEntities($user, $specification, $input);

    $this->getEntityManager()->flush();

    return $this->getRegisterResponse($request, $app, $user);
  }

  /**
   * Creates a new user
   * @return UserInterface
   */
  protected function newUser(): UserInterface
  {
    return $this->objectCreatorService->createObjectFromInterface(UserInterface::class);
  }

  /**
   * Gets the response for a successful register action
   * @param Request $request the request
   * @param Application $app the application
   * @param UserInterface $user the newly registered user
   * @return JsonResponse the json response
   */
  protected function getRegisterResponse(/** @noinspection PhpUnusedParameterInspection */
    Request $request, /** @noinspection PhpUnusedParameterInspection */
    Application $app, UserInterface $user)
  {
    return response()->json(['id' => $user->getId()]);
  }

  /**
   * @return JsonResponse
   */
  public function userId(): JsonResponse
  {
    /** @noinspection PhpUnhandledExceptionInspection */
    return response()->json(['id' => Auth::user()->getAuthIdentifier()]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Methods">
  /**
   * Gets additional input specifications for the login action
   * @param array $specification the specification to add to / modify
   */
  protected function addAdditionalLoginSpecifications(array &$specification)
  {
    //do nothing by default
  }

  /**
   * adds additional register specifications
   * @param array $specification the specification to add to / modify
   */
  protected function addAdditionalRegisterSpecifications(array &$specification)
  {
    //do nothing by default
  }

  /**
   * creates additional entities after registration using the specification and the given input
   * @param UserInterface $user the newly registered user
   * @param array $specification the specification
   * @param array $input the given request input
   */
  protected function createAdditionalRegisterEntities(UserInterface $user, array $specification, array $input)
  {
    //do nothing by default
  }

  /**
   * Gets the response for a successful login action
   * @param Request $request the request
   * @param string $token the login token
   * @return JsonResponse the response
   */
  protected function getLoginResponse(Request $request, string $token): JsonResponse
  {
    $user = $request->user();
    return response()->json(['id' => $user->getId()], 200, ['jwt-token' => $token]);
  }
//</editor-fold desc="Protected Methods">

//<editor-fold desc="Private Methods">
  /**
   * Gets the specification for the login credentials
   * @param Application $app
   * @return array
   */
  private function getCredentialSpecification(Application $app)
  {
    /** @var Hasher $hasher */
    return [
      'email' => ['validation' => 'required|email'],
      'password' => ['validation' => 'required|string|min:8',
        'transformer' => function ($value) use ($app) {
          return $app['hash']->make($value);
        }]
    ];
  }
//</editor-fold desc="Private Methods">
}
