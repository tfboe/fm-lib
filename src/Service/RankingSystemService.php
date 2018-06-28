<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/4/18
 * Time: 4:09 PM
 */

namespace Tfboe\FmLib\Service;


use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;
use Tfboe\FmLib\Entity\LastRecalculationInterface;
use Tfboe\FmLib\Entity\RankingSystemInterface;
use Tfboe\FmLib\Entity\TournamentInterface;

/**
 * Class RankingSystemService
 * @package Tfboe\FmLib\Service
 */
class RankingSystemService implements RankingSystemServiceInterface
{
//<editor-fold desc="Fields">
  /**
   * @var DynamicServiceLoadingServiceInterface
   */
  private $dsls;

  /** @var EntityManagerInterface */
  private $entityManager;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">

  /**
   * RankingSystemService constructor.
   * @param DynamicServiceLoadingServiceInterface $dsls
   * @param EntityManagerInterface $entityManager
   */
  public function __construct(DynamicServiceLoadingServiceInterface $dsls, EntityManagerInterface $entityManager)
  {
    $this->dsls = $dsls;
    $this->entityManager = $entityManager;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function adaptOpenSyncFromValues(TournamentInterface $tournament, array $oldInfluences): void
  {
    $earliestInfluences = $this->getRankingSystemsEarliestInfluences($tournament);
    foreach ($oldInfluences as $id => $arr) {
      if (array_key_exists($id, $earliestInfluences)) {
        if ($oldInfluences[$id]["earliestInfluence"] < $earliestInfluences[$id]["earliestInfluence"]) {
          $earliestInfluences[$id]["earliestInfluence"] = $oldInfluences[$id]["earliestInfluence"];
        }
      } else {
        $earliestInfluences[$id] = $oldInfluences[$id];
      }
    }
    foreach ($earliestInfluences as $arr) {
      /** @var RankingSystemInterface $ranking */
      $ranking = $arr["rankingSystem"];
      $earliestInfluence = $arr["earliestInfluence"];
      if ($ranking->getOpenSyncFrom() === null || $ranking->getOpenSyncFrom() > $earliestInfluence) {
        $ranking->setOpenSyncFrom($earliestInfluence);
      }
    }
  }

  /**
   * @inheritDoc
   */
  public function applyRankingSystems(TournamentInterface $tournament, array $earliestInfluences): void
  {
    $rankingSystems = $this->getRankingSystems($tournament);
    foreach ($rankingSystems as $sys) {
      if (!array_key_exists($sys->getId(), $earliestInfluences)) {
        $earliestInfluences[$sys->getId()] = [
          "rankingSystem" => $sys,
          "earliestInfluence" => null
        ];
      }
    }
    foreach ($earliestInfluences as $arr) {
      /** @var RankingSystemInterface $ranking */
      $ranking = $arr["rankingSystem"];
      $earliestInfluence = $arr["earliestInfluence"];
      $service = $this->dsls->loadRankingSystemService($ranking->getServiceName());
      $service->updateRankingForTournament($ranking, $tournament, $earliestInfluence);
    }
  }

  /**
   * @inheritDoc
   */
  public function getRankingSystemsEarliestInfluences(TournamentInterface $tournament): array
  {
    $rankingSystems = $this->getRankingSystems($tournament);

    $result = [];
    //compute earliest influences
    foreach ($rankingSystems as $sys) {
      $service = $this->dsls->loadRankingSystemService($sys->getServiceName());
      $result[$sys->getId()] = [
        "rankingSystem" => $sys,
        "earliestInfluence" => $service->getEarliestInfluence($sys, $tournament)
      ];
    }

    return $result;
  }

  /**
   * @inheritDoc
   */
  public function recalculateRankingSystems(): void
  {
    //clear entityManager to save memory
    $this->entityManager->flush();
    $this->entityManager->clear();
    $rankingSystemOpenSyncFroms = [];
    /** @var RankingSystemInterface[] $rankingSystems */
    $rankingSystems = [];
    $this->entityManager->transactional(
      function (EntityManager $em) use (&$rankingSystems, &$rankingSystemOpenSyncFroms) {
        $em->find(LastRecalculationInterface::class, 1, LockMode::PESSIMISTIC_WRITE);
        $query = $em->createQueryBuilder();
        $query
          ->from(RankingSystemInterface::class, 's')
          ->select('s')
          ->where($query->expr()->isNotNull('s.openSyncFrom'))
          ->orWhere($query->expr()->isNotNull('s.openSyncFromInProcess'));
        /** @var RankingSystemInterface[] $rankingSystems */
        $rankingSystems = $query->getQuery()->setLockMode(LockMode::PESSIMISTIC_WRITE)->getResult();
        foreach ($rankingSystems as $rankingSystem) {
          if ($rankingSystem->getOpenSyncFrom() !== null && ($rankingSystem->getOpenSyncFromInProcess() === null ||
              $rankingSystem->getOpenSyncFrom() < $rankingSystem->getOpenSyncFromInProcess())) {
            $rankingSystemOpenSyncFroms[$rankingSystem->getId()] = $rankingSystem->getOpenSyncFrom();
            $rankingSystem->setOpenSyncFrom(null);
          }
        }
      }
    );

    $this->entityManager->transactional(
      function (EntityManager $em) use (&$rankingSystems, &$rankingSystemOpenSyncFroms) {
        /** @var LastRecalculationInterface $lastRecalculation */
        $lastRecalculation = $em->find(LastRecalculationInterface::class, 1, LockMode::PESSIMISTIC_WRITE);
        foreach ($rankingSystems as $rankingSystem) {
          if (array_key_exists($rankingSystem->getId(), $rankingSystemOpenSyncFroms) &&
            $rankingSystemOpenSyncFroms[$rankingSystem->getId()] < $rankingSystem->getOpenSyncFromInProcess()) {
            $rankingSystem->setOpenSyncFromInProcess($rankingSystemOpenSyncFroms[$rankingSystem->getId()]);
          }
        }
        $em->flush();
        $lastRecalculation->setStartTime(new \DateTime());
        foreach ($rankingSystems as $rankingSystem) {
          $service = $this->dsls->loadRankingSystemService($rankingSystem->getServiceName());
          $service->updateRankingFrom($rankingSystem, $rankingSystem->getOpenSyncFrom());
          $rankingSystem->setOpenSyncFromInProcess(null);
        }
        $lastRecalculation->setEndTime(new \DateTime());
        $lastRecalculation->setVersion($lastRecalculation->getVersion() + 1);
      }
    );
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @param TournamentHierarchyInterface $entity
   * @return RankingSystemInterface[]
   */
  private function getRankingSystems(TournamentHierarchyInterface $entity): array
  {
    $result = $entity->getRankingSystems()->toArray();
    foreach ($entity->getChildren() as $child) {
      $result = array_merge($result, $this->getRankingSystems($child));
    }
    return $result;
  }
//</editor-fold desc="Private Methods">
}