<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 3:53 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Service\RankingSystem;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Tfboe\FmLib\Helpers\Level;
use Tfboe\FmLib\Service\LoadingService;
use Tfboe\FmLib\Service\ObjectCreatorServiceInterface;
use Tfboe\FmLib\Service\RankingSystem\EntityComparerInterface;
use Tfboe\FmLib\Service\RankingSystem\GameRankingSystemService;
use Tfboe\FmLib\Service\RankingSystem\TimeServiceInterface;
use Tfboe\FmLib\Tests\Entity\RankingSystem;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;

/**
 * Class GameRankingSystemTest
 * @packageTfboe\FmLib\Tests\Unit\Service\RankingSystemService
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GameRankingSystemTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Service\RankingSystem\GameRankingSystemService::getEntitiesQueryBuilder
   * @throws Exception
   * @uses   \Tfboe\FmLib\Service\RankingSystem\RankingSystemService::getEntityManager
   * @uses   \Tfboe\FmLib\Service\RankingSystem\RankingSystemService::__construct
   */
  public function testGetEntitiesQueryBuilder()
  {
    $entityManager = $this->getMockForAbstractClass(EntityManager::class, [], '', false, true, true, ['getClassMetadata']);
    $metaData = $this->createMock(ClassMetadata::class);
    $entityManager->method('getClassMetadata')->willReturn($metaData);
    $reflectionClass = new \ReflectionClass(GameRankingSystemTest::class);
    $metaData->method('getReflectionClass')->willReturn($reflectionClass);
    $system = $this->getMockForAbstractClass(GameRankingSystemService::class, [$entityManager,
      $this->createMock(TimeServiceInterface::class),
      $this->createMock(EntityComparerInterface::class),
      $this->createMock(ObjectCreatorServiceInterface::class),
      $this->createMock(LoadingService::class)]);
    $rankingSystem = $this->createMock(RankingSystem::class);
    $rankingSystem->method('getId')->willReturn('ranking-system-id');
    /** @var QueryBuilder $builder */
    $builder = self::callProtectedMethod($system, "getEntitiesQueryBuilder",
      [$rankingSystem, new DateTime("2000-01-01"), new DateTime("2001-01-01")]);
    self::assertEquals(
      'SELECT g FROM Tfboe\FmLib\Entity\GameInterface g LEFT JOIN g.rankingSystems grs WITH grs = :ranking ' .
      'INNER JOIN g.match m LEFT JOIN m.rankingSystems mrs WITH mrs = :ranking INNER JOIN m.phase p LEFT JOIN ' .
      'p.rankingSystems prs WITH prs = :ranking INNER JOIN p.competition c LEFT JOIN c.rankingSystems crs WITH ' .
      'crs = :ranking INNER JOIN c.tournament t LEFT JOIN t.rankingSystems trs WITH trs = :ranking WHERE ' .
      'COALESCE(g.endTime, g.startTime, m.endTime, m.startTime, p.endTime, p.startTime, c.endTime, c.startTime, ' .
      't.endTime, t.startTime, t.updatedAt) > :from AND COALESCE(g.endTime, g.startTime, m.endTime, m.startTime, ' .
      'p.endTime, p.startTime, c.endTime, c.startTime, t.endTime, t.startTime, t.updatedAt) <= :to AND (grs.id IS ' .
      'NOT NULL OR mrs.id IS NOT NULL OR prs.id IS NOT NULL OR crs.id IS NOT NULL OR trs.id IS NOT NULL)',
      $builder->getDQL());
  }

  /**
   * @covers \Tfboe\FmLib\Service\RankingSystem\GameRankingSystemService::getLevel
   * @uses   \Tfboe\FmLib\Service\RankingSystem\RankingSystemService::__construct
   */
  public function testLevel()
  {
    $entityManager = $this->createMock(EntityManagerInterface::class);
    $metaData = $this->createMock(ClassMetadata::class);
    $entityManager->method('getClassMetadata')->willReturn($metaData);
    $reflectionClass = new \ReflectionClass(GameRankingSystemTest::class);
    $metaData->method('getReflectionClass')->willReturn($reflectionClass);
    $system = $this->getMockForAbstractClass(GameRankingSystemService::class,
      [$entityManager,
        $this->createMock(TimeServiceInterface::class),
        $this->createMock(EntityComparerInterface::class), $this->createMock(ObjectCreatorServiceInterface::class),
        $this->createMock(LoadingService::class)]);
    self::assertEquals(Level::GAME, self::callProtectedMethod($system, "getLevel"));
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
//</editor-fold desc="Private Methods">
}