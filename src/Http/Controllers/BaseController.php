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
use Tfboe\FmLib\Helpers\SpecificationHandler;

/**
 * Class Controllers
 * @package App\Http\Controllers
 */
abstract class BaseController extends Controller
{
  use SpecificationHandler;

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
   * @return EntityManagerInterface
   */
  final protected function getEntityManager(): EntityManagerInterface
  {
    return $this->entityManager;
  }
//</editor-fold desc="Protected Final Methods">

//<editor-fold desc="Protected Methods">
  /**
   * @inheritDoc
   */
  protected function getReference($class, $id)
  {
    return $this->getEntityManager()->find($class, $id);
  }

  /**
   * @inheritDoc
   */
  protected function validateSpec(Request $request, array $spec)
  {
    return $this->validate($request, $spec);
  }
//</editor-fold desc="Protected Methods">
}