<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/21/17
 * Time: 8:44 AM
 */

namespace Tfboe\FmLib\Exceptions;


use Tfboe\FmLib\Helpers\BasicEnum;

/**
 * Class ExceptionNames
 * @package Tfboe\FmLib\Exceptions
 */
abstract class ExceptionNames extends BasicEnum
{
//<editor-fold desc="Fields">
  public const AUTHENTICATION_EXCEPTION = "AuthenticationException";
  public const DUPLICATE_EXCEPTION = "DuplicateException";
  public const INTERNAL_EXCEPTION = "InternalException";
  public const PLAYER_ALREADY_EXISTS_EXCEPTION = "PlayerAlreadyExistsException";
  public const REFERENCE_EXCEPTION = "ReferenceException";
  public const UNORDERED_PHASE_NUMBER_EXCEPTION = "UnorderedPhaseNumberException";
  public const VALIDATION_EXCEPTION = "ValidationException";
//</editor-fold desc="Fields">
}