<?php
declare(strict_types=1);

namespace Tfboe\FmLib\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Throwable;

/**
 * Class Handler
 * @package Tfboe\FmLib\Exceptions
 */
class Handler extends ExceptionHandler
{
//<editor-fold desc="Fields">
  /**
   * A list of the exception types that should not be reported.
   *
   * @var array
   */
  protected $dontReport = [
    ValidationException::class,
  ];
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /** @noinspection PhpMissingParentCallCommonInspection */
  /**
   * Render an exception into an HTTP response.
   *
   * @param  \Illuminate\Http\Request $request
   * @param  \Exception $exception
   * @param  bool $printTrace if true a trace will be appended to the exception message
   * @return \Illuminate\Http\Response
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function render($request, Throwable $throwable, $printTrace = false)
  {
    //don't throw html exceptions always render using json
    $statusCode = $this->getExceptionHTTPStatusCode($throwable);

    return response()->json(
      $this->getJsonMessage($throwable, $statusCode, $printTrace),
      $statusCode
    );
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Methods">
  /**
   * Extracts the status code of an exception
   * @param Throwable $exception the exception to extract from
   * @return int|mixed the status code or 500 if no status code found
   */
  protected function getExceptionHTTPStatusCode(Throwable $exception)
  {
    // Not all Exceptions have a http status code
    // We will give Error 500 if none found
    if ($exception instanceof ValidationException) {
      return $exception->getResponse()->getStatusCode();
    }
    return method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() :
      (method_exists($exception, 'getCode') && $exception->getCode() > 0 ? $exception->getCode() : 500);
  }

  /**
   * Gets the exception name of an exception which is used by clients to identify the type of the error.
   * @param Exception $exception the exception whose name we want
   * @return string the exception name
   */
  protected function getExceptionName(Exception $exception): string
  {
    if ($exception instanceof AuthenticationException) {
      return ExceptionNames::AUTHENTICATION_EXCEPTION;
    }
    if ($exception instanceof DuplicateException) {
      return ExceptionNames::DUPLICATE_EXCEPTION;
    }
    if ($exception instanceof UnorderedPhaseNumberException) {
      return ExceptionNames::UNORDERED_PHASE_NUMBER_EXCEPTION;
    }
    if ($exception instanceof ReferenceException) {
      return ExceptionNames::REFERENCE_EXCEPTION;
    }
    if ($exception instanceof PlayerAlreadyExists) {
      return ExceptionNames::PLAYER_ALREADY_EXISTS_EXCEPTION;
    }
    if ($exception instanceof ValidationException) {
      return ExceptionNames::VALIDATION_EXCEPTION;
    }
    return ExceptionNames::INTERNAL_EXCEPTION;
  }

  /**
   * Extracts the status and the message from the given exception and status code
   * @param Exception $exception the raised exception
   * @param string|null $statusCode the status code or null if unknown
   * @param  bool $printTrace if true a trace will be appended to the exception message
   * @return array containing the infos status and message
   */
  protected function getJsonMessage(Exception $exception, $statusCode = null, $printTrace = false)
  {

    $result = method_exists($exception, 'getJsonMessage') ? $exception->getJsonMessage() :
      ['message' => $exception->getMessage()];

    if ($exception instanceof ValidationException) {
      $result["errors"] = $exception->errors();
    }

    if (!array_key_exists('status', $result)) {
      $result['status'] = $statusCode !== null ? $statusCode : "false";
    }

    if (!array_key_exists('name', $result)) {
      $result['name'] = $this->getExceptionName($exception);
    }

    if ($printTrace || env('APP_DEBUG') === true) {
      //attach trace back
      $result['trace'] = $exception->getTraceAsString();
    }

    return $result;
  }
//</editor-fold desc="Protected Methods">
}
