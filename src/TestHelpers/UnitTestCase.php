<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/6/18
 * Time: 7:08 PM
 */

namespace Tfboe\FmLib\TestHelpers;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class UnitTestCase
 * @package Tfboe\FmLib\TestHelpers
 */
abstract class UnitTestCase extends TestCase
{
  use ReflectionMethods;
  use OnlyTestLogging;

//<editor-fold desc="Public Methods">
  public function tearDown()
  {
    parent::tearDown();
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Final Methods">
  /**
   * Gets a mock class (with full implementation). The given arguments are used for the arguments for the constructor.
   * If too less arguments are given mocks are created for the rest of the constructor arguments.
   * @param string $className the class to mock
   * @param array $arguments the arguments to use for the constructor
   * @param string[] $mockedMethods the methods to mock in the class
   * @return MockObject the mocked object
   */
  protected final function getMockWithMockedArguments(string $className, array $arguments = [],
                                                      array $mockedMethods = []): MockObject
  {
    /** @noinspection PhpUnhandledExceptionInspection */
    $reflection = new \ReflectionClass($className);
    $params = $reflection->getConstructor()->getParameters();
    $allArguments = $arguments;
    for ($i = count($arguments); $i < count($params); $i++) {
      $allArguments[] = $this->createMock($params[$i]->getClass()->name);
    }
    return $this->getMockForAbstractClass($className, $allArguments, '', true, true, true, $mockedMethods);
  }

  /**
   * Gets a new instance of the given class. The given arguments are used for the arguments for the constructor.
   * If too less arguments are given mocks are created for the rest of the constructor arguments.
   * @param string $className the class for which to create an instance
   * @param array $arguments the arguments to use for the constructor
   * @return mixed an instance of the given class
   */
  protected final function getObjectWithMockedArguments($className, array $arguments = [])
  {
    /** @noinspection PhpUnhandledExceptionInspection */
    $reflection = new \ReflectionClass($className);
    $params = $reflection->getConstructor()->getParameters();
    $allArguments = $arguments;
    for ($i = count($arguments); $i < count($params); $i++) {
      $allArguments[$i] = $this->createMock($params[$i]->getClass()->name);
    }
    return new $className(...$allArguments);
  }
//</editor-fold desc="Protected Final Methods">

//<editor-fold desc="Protected Methods">
  /**
   * Creates a stub with a given set of stubbed methods, which will return the given results
   * @param string $class the class name
   * @param array $methodResults a dictionary mapping method names to results of this methods
   * @return MockObject the configured stub
   */
  protected function createStub(string $class, array $methodResults = []): MockObject
  {
    $entity = $this->createMock($class);
    foreach ($methodResults as $method => $result) {
      $entity->method($method)->willReturn($result);
    }
    return $entity;
  }

  /**
   * Creates an empty mock with a getId method
   * @param string $class the class to mock
   * @param string $entityId the id to assign
   * @param string $getterMethod the name of the getter method
   * @return \PHPUnit\Framework\MockObject\MockObject the mocked instance
   */
  protected function createStubWithId(string $class, $entityId = "entity-id", $getterMethod = 'getId')
  {
    return $this->createStub($class, [$getterMethod => $entityId]);
  }

  /**
   * Gets a mock for an entity manager which creates a query builder which will return a query which will return the
   * given result.
   * @param array $results the result arrays the queries should return
   * @param string[] $expectedQueries the expected queries if set
   * @param string[] $otherMockedMethods list of other methods to mock
   * @return MockObject the mocked entity manager
   */
  protected function getEntityManagerMockForQueries(array $results, array $expectedQueries = [],
                                                    array $otherMockedMethods = [])
  {
    $entityManager = $this->getMockForAbstractClass(EntityManager::class, [], '',
      false, true, true, array_merge($otherMockedMethods, ['createQueryBuilder']));
    assert($expectedQueries == [] || count($results) === count($expectedQueries));
    $entityManager->expects(static::exactly(count($results)))->method('createQueryBuilder')->willReturnCallback(
      function () use ($entityManager, &$results, &$expectedQueries) {
        $queryBuilder = $this->getMockForAbstractClass(QueryBuilder::class, [$entityManager],
          '', true, true, true, ['getQuery']);
        $query = $this->getMockBuilder(AbstractQuery::class)
          ->disableOriginalConstructor()
          ->disableOriginalClone()
          ->disableArgumentCloning()
          ->disallowMockingUnknownTypes()
          ->setMethods(['setLockMode', 'getSQL', '_doExecute', 'getResult'])
          ->getMock();
        $query->expects(static::once())->method('getResult')->willReturn(array_shift($results));
        $query->method('setLockMode')->willReturn($query);
        if ($expectedQueries !== []) {
          $queryBuilder->expects(static::once())->method('getQuery')->willReturnCallback(
            function () use ($queryBuilder, $query, &$expectedQueries) {
              /** @var QueryBuilder $queryBuilder */
              self::assertEquals(array_shift($expectedQueries), $queryBuilder->getDQL());
              return $query;
            });
        } else {
          $queryBuilder->expects(static::once())->method('getQuery')->willReturn($query);
        }
        return $queryBuilder;
      }
    );
    return $entityManager;
  }

  /**
   * Gets a mock for an entity manager which creates a query builder which will return a query which will return the
   * given result.
   * @param array $result the result array the query should return
   * @param string|null $expectedQuery the expected query if set
   * @param string[] $otherMockedMethods list of other methods to mock
   * @return MockObject the mocked entity manager
   */
  protected function getEntityManagerMockForQuery(array $result, ?string $expectedQuery = null,
                                                  array $otherMockedMethods = [], $amount = 1)
  {
    return $this->getEntityManagerMockForQueries(array_fill(0, $amount, $result),
      $expectedQuery === null ? [] : array_fill(0, $amount, $expectedQuery), $otherMockedMethods);
  }
//</editor-fold desc="Protected Methods">
}