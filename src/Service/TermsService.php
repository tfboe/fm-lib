<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 8/31/18
 * Time: 9:15 AM
 */

namespace Tfboe\FmLib\Service;


use Doctrine\ORM\EntityManagerInterface;
use Tfboe\FmLib\Entity\TermsInterface;

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
  /**
   * @return TermsInterface
   */
  public function getLatestTerms(): TermsInterface
  {
    $builder = $this->em->createQueryBuilder();
    $terms = $builder->select('e')
      ->from(TermsInterface::class, 'e')
      ->orderBy('e.majorVersion', 'DESC')
      ->addOrderBy('e.minorVersion', 'DESC')
      ->setMaxResults(1)
      ->getQuery()
      ->getOneOrNullResult();
    return $terms;
  }
//</editor-fold desc="Public Methods">
}