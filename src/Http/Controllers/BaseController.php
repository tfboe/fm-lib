<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/16/17
 * Time: 2:04 AM
 */

namespace Tfboe\FmLib\Http\Controllers;


use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Tfboe\FmLib\Entity\Helpers\BaseEntityInterface;
use Tfboe\FmLib\Helpers\Tools;

/**
 * Class Controllers
 * @package App\Http\Controllers
 */
abstract class BaseController extends Controller
{
//<editor-fold desc="Fields">
  /**
   * @var EntityManagerInterface
   */
  private $entityManager;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * Controllers constructor.
   * @param EntityManagerInterface $entityManager
   */
  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }
//</editor-fold desc="Fields">
//</editor-fold desc="Constructor">

//<editor-fold desc="Protected Final Methods">
  /**
   * @return string
   */
  protected final function getDatetimetzFormat(): string
  {
    return Tools::getDatetimetzFormat();
  }

  /**
   * @return EntityManagerInterface
   */
  protected final function getEntityManager(): EntityManagerInterface
  {
    return $this->entityManager;
  }
//</editor-fold desc="Protected Final Methods">

//<editor-fold desc="Protected Methods">
  /**
   * Gets a transformation function which transforms a string in datetime format into a datetime with the given timezone
   * @return \Closure the function which transforms a string into a datetime
   */
  protected function datetimetzTransformer(): \Closure
  {
    return Tools::datetimetzTransformer();
  }
  /**
   * Gets a transformation function which transforms an enum name into the corresponding value
   * @param string $enumName the name of the enum
   * @return \Closure the function which transforms a name into the enum value
   */
  protected function enumTransformer(string $enumName): \Closure
  {
    return Tools::enumTransformer($enumName);
  }

  /**
   * Fills an object with the information of inputArray
   * @param BaseEntityInterface $object the object to fill
   * @param array $specification the specification how to fill the object
   * @param array $inputArray the input array
   * @return mixed the object
   */
  protected function setFromSpecification(BaseEntityInterface $object, array $specification, array $inputArray)
  {
    return Tools::setFromSpecification($object, $specification, $inputArray, $this->entityManager);
  }

  /**
   * Validates the parameters of a request by the validate fields of the given specification
   * @param Request $request the request
   * @param array $specification the specification
   * @return $this|BaseController
   */
  protected function validateBySpecification(Request $request, array $specification): BaseController
  {
    $arr = [];
    foreach ($specification as $key => $values) {
      if (array_key_exists('validation', $values)) {
        $arr[$key] = $values['validation'];
      }
    }
    $this->validate($request, $arr);
    return $this;
  }
//</editor-fold desc="Protected Methods">
}