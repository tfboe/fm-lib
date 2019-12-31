<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 3:53 PM
 */

namespace Tfboe\FmLib\Tests\Unit\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\MockObject\MockObject;
use Tfboe\FmLib\Entity\TermsInterface;
use Tfboe\FmLib\Service\TermsService;
use Tfboe\FmLib\Tests\Helpers\UnitTestCase;


/**
 * Class PlayerServiceTest
 * @package Tfboe\FmLib\Tests\Unit\Service
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TermsServiceTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Service\TermsService::__construct
   */
  public function testConstructor()
  {
    $service = $this->service($this->getStub(EntityManagerInterface::class));
    self::assertInstanceOf(TermsService::class, $service);
  }

  /**
   * @covers \Tfboe\FmLib\Service\TermsService::getLatestTerms
   * @uses   \Tfboe\FmLib\Service\TermsService::__construct
   */
  public function testGetLatestTerms()
  {
    $terms = $this->getStub(TermsInterface::class);
    $em = $this->getEntityManagerMockForQuery($terms, /** @lang DQL */
      'SELECT e FROM Tfboe\FmLib\Entity\TermsInterface e ORDER BY e.majorVersion DESC, e.minorVersion DESC', [], 1,
      'getOneOrNullResult');
    $service = $this->service($em);
    self::assertTrue($terms === $service->getLatestTerms());
  }

  /**
   * @covers \Tfboe\FmLib\Service\TermsService::getLatestTerms
   * @uses   \Tfboe\FmLib\Service\TermsService::__construct
   * @uses   \Tfboe\FmLib\Exceptions\Internal::error
   */
  public function testGetLatestTermsNotExisting()
  {
    $em = $this->getEntityManagerMockForQuery(null, /** @lang DQL */
      'SELECT e FROM Tfboe\FmLib\Entity\TermsInterface e ORDER BY e.majorVersion DESC, e.minorVersion DESC', [], 1,
      'getOneOrNullResult');
    $this->expectException(Error::class);
    $this->expectExceptionMessage("The terms table is empty!");
    $service = $this->service($em);
    $service->getLatestTerms();
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @param EntityManagerInterface|MockObject $em
   * @return TermsService
   */
  private function service($em)
  {
    return new TermsService($em);
  }
//</editor-fold desc="Private Methods">
}
