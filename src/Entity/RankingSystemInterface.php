<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:48 PM
 */

namespace Tfboe\FmLib\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Tfboe\FmLib\Entity\Helpers\BaseEntityInterface;
use Tfboe\FmLib\Entity\Helpers\NameEntityInterface;
use Tfboe\FmLib\Entity\Helpers\SubClassDataInterface;
use Tfboe\FmLib\Entity\Helpers\TimestampableEntityInterface;
use Tfboe\FmLib\Entity\Helpers\UUIDEntityInterface;
use Tfboe\FmLib\Exceptions\ValueNotValid;


/**
 * Interface RankingSystemInterface
 * @package Tfboe\FmLib\Entity
 */
interface RankingSystemInterface extends BaseEntityInterface, SubClassDataInterface, TimestampableEntityInterface,
  UUIDEntityInterface, NameEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return int|null
   */
  public function getDefaultForLevel(): ?int;

  /**
   * @return int
   */
  public function getGenerationInterval(): int;

  /**
   * @return TournamentInterface[]|Collection
   */
  public function getHierarchyEntries();

  /**
   * @return RankingSystemListInterface[]|Collection
   */
  public function getLists(): Collection;

  /**
   * @return DateTime|null
   */
  public function getOpenSyncFrom(): ?DateTime;

  /**
   * @return string
   */
  public function getServiceName(): string;

  /**
   * @param int|null $defaultForLevel
   * @throws ValueNotValid
   */
  public function setDefaultForLevel(?int $defaultForLevel);

  /**
   * @param int $generationInterval
   * @throws ValueNotValid
   */
  public function setGenerationInterval(int $generationInterval);

  /**
   * @param DateTime|null $openSyncFrom
   */
  public function setOpenSyncFrom(?DateTime $openSyncFrom);

  /**
   * @param string $serviceName
   */
  public function setServiceName(string $serviceName);
//</editor-fold desc="Public Methods">
}