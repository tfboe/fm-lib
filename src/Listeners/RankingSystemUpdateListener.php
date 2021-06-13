<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/4/17
 * Time: 6:33 PM
 */

namespace Tfboe\FmLib\Listeners;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\TeamInterface;
use Tfboe\FmLib\Entity\TeamMembershipInterface;
use Tfboe\FmLib\Service\DynamicServiceLoadingServiceInterface;

/**
 * Class UpdateTournamentListener
 * @package App\Listeners
 */
class RankingSystemUpdateListener
{
//<editor-fold desc="Constructor">
  /**
   * UpdateTournamentListener constructor.
   */
  public function __construct()
  {
  }

//<editor-fold desc="Public Methods">

  /**
   * OnFlush event handler of Doctrine. Checks recursively what has changed, updates updatedAt values and calls
   * callbacks.
   * @param OnFlushEventArgs $eventArgs
   */
  public function onFlush(OnFlushEventArgs $eventArgs)
  {
    $dsls = app(DynamicServiceLoadingServiceInterface::class);
    $uow = $eventArgs->getEntityManager()->getUnitOfWork();
    foreach ($uow->getScheduledEntityInsertions() as $entity) {
      $this->updateRankingSystems($entity, $dsls);
    }

    foreach ($uow->getScheduledEntityUpdates() as $entity) {
      $this->updateRankingSystems($entity, $dsls, $uow->getEntityChangeSet($entity));
    }

    foreach ($uow->getScheduledEntityDeletions() as $entity) {
      $this->updateRankingSystems($entity, $dsls);
    }

    foreach ($uow->getScheduledCollectionDeletions() as $entity) {
      $this->updateRankingSystems($entity, $dsls);
    }

    foreach ($uow->getScheduledCollectionUpdates() as $entity) {
      $this->updateRankingSystems($entity, $dsls);
    }
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * Recomputes the change set of the given entity
   * @param mixed $entity the entity to recompute
   * @param DynamicServiceLoadingServiceInterface $dsls
   * @param mixed[][]|null $entityChangeSet maps properties which changed to an array with two elements, the first the
   *                       old value and the second the new value
   */
  private function updateRankingSystems($entity, DynamicServiceLoadingServiceInterface $dsls,
                                        ?array $entityChangeSet = null)
  {
    if ($entity instanceof TournamentHierarchyEntity) {
      /** @var TournamentHierarchyEntity $entity */
      foreach ($entity->getInfluencingRankingSystems() as $rankingSystem) {
        $service = $dsls->loadRankingSystemService($rankingSystem->getServiceName());
        $earliestInfluence = $service->getEarliestInfluence($rankingSystem, $entity, $entityChangeSet);
        if ($earliestInfluence !== null &&
          ($rankingSystem->getOpenSyncFrom() === null || $rankingSystem->getOpenSyncFrom() > $earliestInfluence)) {
          $rankingSystem->setOpenSyncFrom($earliestInfluence);
        }
      }
    }
  }
//</editor-fold desc="Private Methods">
}