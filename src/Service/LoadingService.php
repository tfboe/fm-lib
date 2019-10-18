<?php
declare(strict_types=1);

namespace Tfboe\FmLib\Service;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Tfboe\FmLib\Entity\CompetitionInterface;
use Tfboe\FmLib\Entity\GameInterface;
use Tfboe\FmLib\Entity\Helpers\IdAble;
use Tfboe\FmLib\Entity\MatchInterface;
use Tfboe\FmLib\Entity\PhaseInterface;
use Tfboe\FmLib\Entity\RankingInterface;
use Tfboe\FmLib\Entity\TeamInterface;
use Tfboe\FmLib\Entity\TeamMembershipInterface;
use Tfboe\FmLib\Entity\TournamentInterface;


/**
 * Class LoadingService
 * @package App\Service
 */
class LoadingService implements LoadingServiceInterface
{
//<editor-fold desc="Fields">
  /**
   * @var string
   */
  protected $defaultPropertiesToLoad;
  /**
   * @var EntityManager
   */
  private $em;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * LoadingService constructor.
   * @param EntityManagerInterface $em
   */
  public function __construct(
    EntityManagerInterface $em
  )
  {
    $this->em = $em;
    $this->defaultPropertiesToLoad = [
      TournamentInterface::class => [["competitions"]],
      CompetitionInterface::class => [["teams"], ["phases"]],
      TeamInterface::class => [["memberships"]],
      TeamMembershipInterface::class => [["player", "team"]],
      PhaseInterface::class => [["preQualifications"], ["postQualifications"], ["rankings"], ["matches"]],
      RankingInterface::class => [["teams"]],
      MatchInterface::class => [["rankingsA", "rankingsB"], ["games"]],
      GameInterface::class => [["playersA", "playersB"]],
    ];
  }
//</editor-fold desc="Constructor">


//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function loadEntities(array $entities, ?array $propertyMap = null)
  {
    if ($propertyMap === null) {
      $propertyMap = $this->defaultPropertiesToLoad;
    }
    //build groups for each type
    $toDoEntityIds = [];
    $done = [];
    $this->collectToDo($entities, $toDoEntityIds, $done, $propertyMap);
    while (count($toDoEntityIds) > 0) {
      $entities = reset($toDoEntityIds);

      $class = key($toDoEntityIds);
      unset($toDoEntityIds[$class]);
      foreach ($propertyMap[$class] as $properties) {
        //eliminate entities which have already loaded the properties
        $ids = array_map(function (IdAble $e) {
          return $e->getEntityId();
        }, array_filter($entities, function (IdAble $e) use ($properties) {
          foreach ($properties as $property) {
            $getter = "get" . ucfirst($property);
            $object = $e->$getter();
            if ($object === null) {
              return true;
            }
            if ($object instanceof AbstractLazyCollection) {
              /** @var AbstractLazyCollection $object */
              if (!$object->isInitialized()) {
                return true;
              }
            } else if (!property_exists($object, '__isInitialized__') || !$object->__isInitialized__) {
              return true;
            }
          }
          return false;
        }));
        if (count($ids) > 0) {
          $this->loadProperties($ids, $class, $properties);
        }
        //check loaded subproperties if they also have subproperties which need to get loaded
        foreach ($entities as $entity) {
          foreach ($properties as $property) {
            $getter = "get" . ucfirst($property);
            $object = $entity->$getter();
            if ($object !== null) {
              if (!$object instanceof Collection) {
                $object = new ArrayCollection([$object]);
              }
              $this->collectToDo($object, $toDoEntityIds, $done, $propertyMap);
            }
          }
        }
      }
    }
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @param Collection|IdAble[] $entities
   * @param array $toDoEntityIds
   * @param array $done
   * @param array $propertyMap
   */
  private function collectToDo(iterable $entities, array &$toDoEntityIds, array &$done, array $propertyMap)
  {
    foreach ($entities as $entity) {
      if (!array_key_exists($entity->getEntityId(), $done)) {
        $done[$entity->getEntityId()] = true;
        $class = $this->keyOfPropertyMap($entity, $propertyMap);
        if ($class !== null) {
          if (!array_key_exists($class, $toDoEntityIds)) {
            $toDoEntityIds[$class] = [];
          }
          $toDoEntityIds[$class][] = $entity;
        }
      }
    }
  }

  /**
   * Computes the key corresponding to the given entity in the given property map, or null if no key was found.
   * @param mixed $entity the entity to search the key for
   * @param string[][][] $propertyMap a property map which maps classes to lists of groups of properties to fetch
   * @return null|string
   */
  private function keyOfPropertyMap($entity, $propertyMap): ?string
  {
    foreach (array_keys($propertyMap) as $class) {
      if ($entity instanceof $class) {
        return $class;
      }
    }
    return null;
  }

  /**
   * @param string[] $ids a list of ids of entities to get the properties for
   * @param string $class the class of the entities
   * @param string[] $properties a list of properties to get for each entity
   */
  private function loadProperties(array $ids, string $class, array $properties): void
  {
    $builder = $this->em->createQueryBuilder()
      ->select("t1")
      ->from($class, "t1");
    $count = 1;
    foreach ($properties as $property) {
      $count++;
      $table = "t" . (string)$count;
      $builder->addSelect($table);
      $builder->leftJoin("t1." . $property, $table);
    }
    $builder->where($builder->expr()->in("t1.id", $ids));
    $builder->getQuery()->getResult();
  }
//</editor-fold desc="Private Methods">
}
