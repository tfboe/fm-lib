<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 2/2/18
 * Time: 5:15 PM
 */

namespace Tfboe\FmLib\Service;


use Doctrine\ORM\EntityManagerInterface;
use Tfboe\FmLib\Entity\PlayerInterface;

/**
 * Class DeletionService
 * @package App\Services
 */
class DeletionService implements DeletionServiceInterface
{
//<editor-fold desc="Fields">
  /**
   * @var EntityManagerInterface
   */
  private $entityManager;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * DeletionService constructor.
   * @param EntityManagerInterface $entityManager
   */
  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function deletePlayer(PlayerInterface $player): void
  {
    $this->entityManager->remove($player);
  }

  //</editor-fold desc="Public Methods">
}