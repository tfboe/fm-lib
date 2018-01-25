<?php
/** @var \Laravel\Lumen\Routing\Router $router */
$router = app('router');

$router->group(['namespace' => 'Tfboe\FmLib\Http\Controllers'], function () use ($router) {
  /**
   * @api {post} /register Register
   * @apiVersion 0.1.0
   * @apiDescription Register a new user
   * @apiName PostRegister
   * @apiGroup User
   *
   * @apiParam {string} email the unique email address of the user
   * @apiParam {string{8..}} password the password
   * @apiParam {integer{>=0}} confirmedAGBVersion confirmed AGB version
   *
   * @apiSuccess {string} id the id of the newly created user
   * @apiError ValidationException The provided email is malformed or does already exist, or the provided password is too
   *                               short
   */
  $router->post('register', [
    'as' => 'register', 'uses' => 'UserController@register'
  ]);

  /**
   * @api {post} /login Login
   * @apiVersion 0.1.0
   * @apiDescription Logs in a user and gets his authentication token
   * @apiName PostLogin
   * @apiGroup User
   *
   * @apiParam {string} email the email address of the user
   * @apiParam {string{8..}} password the users password
   *
   * @apiSuccess {string} id the id of the user
   * @apiHeader (Response Headers) {string} jwt-token Authorization Bearer token.
   * @apiError ValidationException The provided email is malformed or does already exist, or the provided password is too
   *                               short
   */
  $router->post('login', [
    'as' => 'login', 'uses' => 'UserController@login'
  ]);

  $router->group(['middleware' => 'auth:api'], function () use ($router) {
    /**
     * @api {get} /userId Get User ID
     * @apiUse AuthenticatedRequest
     * @apiVersion 0.1.0
     * @apiDescription Gets the user id of the currently logged in user
     * @apiName GetUserId
     * @apiGroup User
     *
     * @apiSuccess {string} id the id of the user
     */
    $router->get('userId', [
      'as' => 'userId', 'uses' => 'UserController@userId'
    ]);
  });
});