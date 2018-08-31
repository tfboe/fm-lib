<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 8/31/18
 * Time: 9:15 AM
 */

namespace Tfboe\FmLib\Service;


use Doctrine\ORM\EntityManagerInterface;
use Tfboe\FmLib\Entity\AGBInterface;

class AGBService implements AGBServiceInterface
{
//<editor-fold desc="Fields">
  /**
   * @var EntityManagerInterface
   */
  private $em;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * AGBService constructor.
   * @param EntityManagerInterface $em
   */
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return AGBInterface
   */
  public function getLatestAGB(): AGBInterface
  {
    $builder = $this->em->createQueryBuilder();
    $agb = $builder->select('e')
      ->from(AGBInterface::class, 'e')
      ->orderBy('e.majorVersion', 'DESC')
      ->addOrderBy('e.minorVersion', 'DESC')
      ->setMaxResults(1)
      ->getQuery()
      ->getOneOrNullResult();
    return $agb;
  }
//</editor-fold desc="Public Methods">
}