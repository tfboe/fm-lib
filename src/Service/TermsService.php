<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 8/31/18
 * Time: 9:15 AM
 */

namespace Tfboe\FmLib\Service;


use Doctrine\ORM\EntityManagerInterface;
use Tfboe\FmLib\Entity\TermsInterface;
use Tfboe\FmLib\Exceptions\Internal;

/**
 * Class TermsService
 * @package Tfboe\FmLib\Service
 */
class TermsService implements TermsServiceInterface
{
//<editor-fold desc="Fields">
  /**
   * @var EntityManagerInterface
   */
  private $em;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * TermsService constructor.
   * @param EntityManagerInterface $em
   */
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * @return TermsInterface
   */
  public function getLatestTerms(): TermsInterface
  {
    $builder = $this->em->createQueryBuilder();
    /** @noinspection PhpUnhandledExceptionInspection */
    $terms = $builder->select('e')
      ->from(TermsInterface::class, 'e')
      ->orderBy('e.majorVersion', 'DESC')
      ->addOrderBy('e.minorVersion', 'DESC')
      ->setMaxResults(1)
      ->getQuery()
      ->getOneOrNullResult();
    if ($terms === null) {
      Internal::error("The terms table is empty!");
    }
    return $terms;
  }
//</editor-fold desc="Public Methods">
}