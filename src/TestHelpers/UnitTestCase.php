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
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;

/**
 * Class UnitTestCase
 * @package Tfboe\FmLib\TestHelpers
 */
abstract class UnitTestCase extends TestCase
{
  use ReflectionMethods;
  use OnlyTestLogging;

//<editor-fold desc="Public Methods">

  /**
   * @param array|ArrayAccess $subset
   * @param array|ArrayAccess $array
   */
  public static function assertArrayIsSubset($subset, $array): void
  {
    foreach ($subset as $key => $value) {
      self::assertArrayHasKey($key, $array);
      self::assertEquals($value, $array[$key]);
    }
  }

  /**
   * @inheritDoc
   */
  public function tearDown(): void
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
   * Creates a stub with a given set of stubbed methods, which will return the given results
   * @param string $class the class name
   * @param array $methodResults a dictionary mapping method names to results of this methods
   * @return MockObject|mixed the configured stub
   */
  protected function getStub(string $class, array $methodResults = []): Stub
  {
    return $this->createConfiguredMock($class, $methodResults);
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
                                                    array $otherMockedMethods = [], $resultMethod = 'getResult')
  {
    $entityManager = $this->getMockForAbstractClass(EntityManager::class, [], '',
      false, true, true, array_merge($otherMockedMethods, ['createQueryBuilder']));
    assert($expectedQueries == [] || count($results) === count($expectedQueries));
    $entityManager->expects(static::exactly(count($results)))->method('createQueryBuilder')->willReturnCallback(
      function () use ($entityManager, &$results, &$expectedQueries, $resultMethod) {
        $queryBuilder = $this->getMockForAbstractClass(QueryBuilder::class, [$entityManager],
          '', true, true, true, ['getQuery']);
        $query = $this->getMockBuilder(AbstractQuery::class)
          ->disableOriginalConstructor()
          ->disableOriginalClone()
          ->disableArgumentCloning()
          ->disallowMockingUnknownTypes()
          ->setMethods(['setLockMode', 'getSQL', '_doExecute', $resultMethod])
          ->getMock();
        $query->expects(static::once())->method($resultMethod)->willReturn(array_shift($results));
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
   * @param mixed $result the result array the query should return
   * @param string|null $expectedQuery the expected query if set
   * @param string[] $otherMockedMethods list of other methods to mock
   * @return MockObject the mocked entity manager
   */
  protected function getEntityManagerMockForQuery($result, ?string $expectedQuery = null,
                                                  array $otherMockedMethods = [], $amount = 1,
                                                  $resultMethod = 'getResult')
  {
    return $this->getEntityManagerMockForQueries(array_fill(0, $amount, $result),
      $expectedQuery === null ? [] : array_fill(0, $amount, $expectedQuery), $otherMockedMethods, $resultMethod);
  }

  /**
   * @param string $className
   * @param array $methodNames
   * @param array $additionalInterfaces
   * @param string|null $baseClass
   * @param bool $callParentConstructor
   * @param bool $hasInit
   * @return MockObject
   * @noinspection PhpTooManyParametersInspection
   */
  protected function getMockedEntity(string $className, array $methodNames = [], array $additionalInterfaces = [],
                                     ?string $baseClass = BaseEntity::class,
                                     bool $callParentConstructor = false,
                                     bool $hasInit = true): MockObject
  {
    $dynNamespace = "Dynamic\\Generated";
    $dynClassName = $dynNamespace . "\\" . $className;
    if (!class_exists($dynClassName, false)) {
      $base = $baseClass === null ? "" : "extends \\$baseClass ";
      $namespace = "\\Tfboe\\FmLib\\Entity\\";
      $additionalInterfaces[] = $namespace . $className . "Interface";
      $interfaces = implode(", \\", $additionalInterfaces);
      $parentConstructor = $callParentConstructor ? "parent::__construct();" : "";
      $init = $hasInit ? "\$this->init();" : "";
      $class = <<<CLASS
namespace $dynNamespace;
class $className ${base}implements $interfaces
{
  use ${namespace}Traits\\${className};
  public function __construct()
  {
    $parentConstructor
    $init
  }
}
CLASS;
      eval($class);
    }
    return $this->getMockForAbstractClass($dynClassName, [], '', true, true, true, $methodNames);
  }

  /**
   * @param string $className
   * @param array $methods
   * @param array $additionalInterfaces
   * @return MockObject
   */
  protected function getStubbedTournamentHierarchyEntity(string $className, array $methods = [],
                                                         array $additionalInterfaces = [])
  {
    return $this->getStubbedEntity($className, $methods, $additionalInterfaces, TournamentHierarchyEntity::class, true);
  }

  /**
   * @param $traitName
   * @param array $methods
   * @param array $arguments
   * @param string $mockClassName
   * @param bool $callOriginalConstructor
   * @param bool $callOriginalClone
   * @param bool $callAutoload
   * @param bool $cloneArguments
   * @return MockObject
   * @noinspection PhpTooManyParametersInspection
   */
  protected function getPartialMockForTrait($traitName, array $methods, array $arguments = [],
                                            $mockClassName = '', $callOriginalConstructor = true,
                                            $callOriginalClone = true, $callAutoload = true,
                                            $cloneArguments = false): MockObject
  {
    $o = $this->getMockForTrait($traitName, $arguments, $mockClassName, $callOriginalConstructor,
      $callOriginalClone, $callAutoload, array_keys($methods), $cloneArguments);
    $this->stubMethods($o, $methods);
    return $o;
  }

  /**
   * @param string $className
   * @param array $methods
   * @param array $additionalInterfaces
   * @param string|null $baseClass
   * @param bool $callParentConstructor
   * @param bool $hasInit
   * @return MockObject
   * @noinspection PhpTooManyParametersInspection
   */
  protected function getStubbedEntity(string $className, array $methods = [], array $additionalInterfaces = [],
                                      ?string $baseClass = BaseEntity::class,
                                      bool $callParentConstructor = false,
                                      bool $hasInit = true): MockObject
  {
    $o = $this->getMockedEntity($className, array_keys($methods), $additionalInterfaces, $baseClass,
      $callParentConstructor, $hasInit);
    $this->stubMethods($o, $methods);
    return $o;
  }

  /**
   * @param MockObject $o
   * @param $methods
   */
  protected function stubMethods(MockObject $o, $methods)
  {
    foreach ($methods as $method => $return) {
      $o->method($method)->willReturn($return);
    }
  }
//</editor-fold desc="Protected Methods">
}