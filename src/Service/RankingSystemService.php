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
use Tfboe\FmLib\Entity\RankingSystemInterface;
use Tfboe\FmLib\Entity\RecalculationInterface;
use Tfboe\FmLib\Entity\TournamentInterface;
use Tfboe\FmLib\Exceptions\PreconditionFailedException;

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

  /**
   * @var ObjectCreatorServiceInterface
   */
  private $ocs;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">

  /**
   * RankingSystemService constructor.
   * @param DynamicServiceLoadingServiceInterface $dsls
   * @param EntityManagerInterface $entityManager
   * @param ObjectCreatorServiceInterface $ocs
   */
  public function __construct(DynamicServiceLoadingServiceInterface $dsls, EntityManagerInterface $entityManager,
                              ObjectCreatorServiceInterface $ocs)
  {
    $this->dsls = $dsls;
    $this->entityManager = $entityManager;
    $this->ocs = $ocs;
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

    $query = $this->entityManager->createQueryBuilder();
    $query
      ->from(RankingSystemInterface::class, 's')
      ->select('s')
      ->where($query->expr()->isNotNull('s.openSyncFrom'));
    /** @var RankingSystemInterface[] $rankingSystems */
    $rankingSystems = $query->getQuery()->getResult();

    foreach ($rankingSystems as $rankingSystem) {
      $recalculateFrom = null;
      $this->entityManager->transactional(
        function (EntityManager $em) use ($rankingSystem, &$recalculateFrom) {
          $em->lock($rankingSystem, LockMode::PESSIMISTIC_WRITE);
          $recalculation = $this->getRecalculation($em, $rankingSystem);
          $em->refresh($rankingSystem);
          $recalculateFrom = $rankingSystem->getOpenSyncFrom();
          $rankingSystem->setOpenSyncFrom(null);
          if ($recalculation->getRecalculateFrom() === null ||
            $recalculation->getRecalculateFrom() > $recalculateFrom) {
            $recalculation->setRecalculateFrom($recalculateFrom);
          } else {
            $recalculateFrom = $recalculation->getRecalculateFrom();
          }
        }
      );
      if ($recalculateFrom !== null) {
        $this->entityManager->transactional(
          function (EntityManager $em) use (&$rankingSystem) {
            $recalculation = $this->getRecalculation($em, $rankingSystem);
            $recalculation->setVersion($recalculation->getVersion() + 1);
            $recalculation->setStartTime(new \DateTime());
            $recalculation->setEndTime(null);
            $em->flush();
            $service = $this->dsls->loadRankingSystemService($rankingSystem->getServiceName());
            $service->updateRankingFrom($rankingSystem, $recalculation->getRecalculateFrom(), $recalculation);
            $recalculation->setEndTime(new \DateTime());
            $recalculation->setRecalculateFrom(null);
          }
        );
      }
    }
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

  /**
   * @param EntityManagerInterface $em
   * @param RankingSystemInterface $rankingSystem
   * @return RecalculationInterface
   * @throws \Doctrine\ORM\OptimisticLockException
   * @throws \Doctrine\ORM\PessimisticLockException
   * @throws \Doctrine\ORM\TransactionRequiredException
   * @throws PreconditionFailedException
   */
  private function getRecalculation(EntityManagerInterface $em,
                                    RankingSystemInterface $rankingSystem): RecalculationInterface
  {
    $query = $em->createQueryBuilder();
    $query
      ->from(RecalculationInterface::class, 'r')
      ->select('r')
      ->where($query->expr()->eq('r.rankingSystem', ':rankingSystem'))
      ->setParameter('rankingSystem', $rankingSystem);
    $recalculations = $query->getQuery()->setLockMode(LockMode::PESSIMISTIC_WRITE)->getResult();
    if (count($recalculations) > 1) {
      throw new PreconditionFailedException("Can't have multiple recalculations for one ranking system in parallel!");
    }
    if (count($recalculations) == 0) {
      $recalculation = $this->ocs->createObjectFromInterface(RecalculationInterface::class);
      $recalculation->setVersion(1);
      $recalculation->setRankingSystem($rankingSystem);
      $recalculation->setStartTime(new \DateTime());
      $em->persist($recalculation);
      $em->flush();
      $em->lock($recalculation, LockMode::PESSIMISTIC_WRITE);
      return $recalculation;
    } else {
      return $recalculations[0];
    }
  }
//</editor-fold desc="Private Methods">
}