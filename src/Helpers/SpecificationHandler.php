<?php
declare(strict_types=1);


namespace Tfboe\FmLib\Helpers;


use Closure;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tfboe\FmLib\Entity\Helpers\BaseEntityInterface;

/**
 * Trait SpecificationHandler
 * @package Tfboe\FmLib\Helpers
 */
trait SpecificationHandler
{
//<editor-fold desc="Fields">
  /**
   * @var string
   */
  private $datetimetzFormat = 'Y-m-d H:i:s e';
//</editor-fold desc="Fields">

//<editor-fold desc="Protected Final Methods">
  /**
   * @return string
   */
  final protected function getDatetimetzFormat(): string
  {
    return Tools::getDatetimetzFormat();
  }
//</editor-fold desc="Protected Final Methods">

//<editor-fold desc="Protected Methods">
  /**
   * Gets a transformation function which transforms a string in datetime format into a datetime with the given timezone
   * @return Closure the function which transforms a string into a datetime
   */
  protected function datetimetzTransformer(): Closure
  {
    return Tools::datetimetzTransformer();
  }

  /**
   * Gets a transformation function which transforms an enum name into the corresponding value
   * @param string $enumName the name of the enum
   * @return Closure the function which transforms a name into the enum value
   */
  protected function enumTransformer(string $enumName): Closure
  {
    return Tools::enumTransformer($enumName);
  }

  /**
   * @param $class
   * @param $id
   * @return mixed
   */
  abstract protected function getReference($class, $id);

  /**
   * Fills an object with the information of inputArray
   * @param BaseEntityInterface $object the object to fill
   * @param array $specification the specification how to fill the object
   * @param array $inputArray the input array
   * @param bool $useDefaults if false defaults are never used
   * @return mixed the object
   */
  protected function setFromSpecification(BaseEntityInterface $object, array $specification, array $inputArray,
                                          bool $useDefaults = true)
  {
    return Tools::setFromSpecification($object, $specification, $inputArray, [$this, 'getReference'], $useDefaults);
  }

  /**
   * Validates the parameters of a request by the validate fields of the given specification
   * @param Request $request the request
   * @param array $specification the specification
   * @throws ValidationException
   */
  protected function validateBySpecification(Request $request, array $specification): void
  {
    $arr = [];
    foreach ($specification as $key => $values) {
      if (array_key_exists('validation', $values)) {
        $arr[$key] = $values['validation'];
      }
      if (array_key_exists('transformBeforeValidation', $values) && $request->has($key)) {
        $request->merge([$key => $values['transformBeforeValidation']($request->get($key))]);
      }
    }
    $this->validateSpec($request, $arr);
  }

  /**
   * @param Request $request
   * @param array $spec
   * @return mixed
   * @throws ValidationException
   */
  abstract protected function validateSpec(Request $request, array $spec);
//</editor-fold desc="Protected Methods">
//</editor-fold desc="Protected Methods">
}