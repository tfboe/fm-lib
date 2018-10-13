<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/5/18
 * Time: 10:54 PM
 */

namespace Tfboe\FmLib\Entity\Traits;


use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\BaseTrait;
use Tfboe\FmLib\Entity\Helpers\SubClassData;
use Tfboe\FmLib\Entity\Helpers\UUIDEntity;
use Tfboe\FmLib\Entity\PlayerInterface;
use Tfboe\FmLib\Entity\RankingSystemListInterface;

/**
 * Trait RankingSystemListEntry
 * @package Tfboe\FmLib\Entity\Traits
 */
trait RankingSystemListEntry
{
  use UUIDEntity;
  use SubClassData;
  use BaseTrait;

//<editor-fold desc="Fields">
  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\RankingSystemListInterface", inversedBy="entries")
   * @var RankingSystemListInterface
   */
  private $rankingSystemList;

  /**
   * @ORM\Column(type="float")
   * @var float
   */
  private $points;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $numberRankedEntities;

  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\PlayerInterface")
   * @var PlayerInterface
   */
  private $player;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return int
   */
  public function getNumberRankedEntities(): int
  {
    return $this->numberRankedEntities;
  }

  /**
   * @return PlayerInterface
   */
  public function getPlayer(): PlayerInterface
  {
    return $this->player;
  }

  /**
   * @return float
   */
  public function getPoints(): float
  {
    return $this->points;
  }

  /**
   * @return RankingSystemListInterface
   */
  public function getRankingSystemList(): RankingSystemListInterface
  {
    return $this->rankingSystemList;
  }

  /**
   * @param int $numberRankedEntities
   */
  public function setNumberRankedEntities(int $numberRankedEntities)
  {
    $this->numberRankedEntities = $numberRankedEntities;
  }

  /**
   * @param PlayerInterface $player
   */
  public function setPlayer(PlayerInterface $player)
  {
    $this->player = $player;
  }

  /**
   * @param float $points
   */
  public function setPoints(float $points)
  {
    $this->points = $points;
  }

  /**
   * @param RankingSystemListInterface $rankingSystemList
   */
  public function setRankingSystemList(RankingSystemListInterface $rankingSystemList)
  {
    if ($this->rankingSystemList !== null && $this->isInitialized($this->rankingSystemList->getEntries())) {
      $this->rankingSystemList->getEntries()->remove($this->getPlayer()->getId());
    }
    $this->setRankingSystemListWithoutInitializing($rankingSystemList);
    if ($this->isInitialized($rankingSystemList->getEntries())) {
      $rankingSystemList->getEntries()->set($this->getPlayer()->getId(), $this);
    }
  }

  /**
   * @param RankingSystemListInterface $rankingSystemList
   */
  public function setRankingSystemListWithoutInitializing(RankingSystemListInterface $rankingSystemList)
  {
    $this->rankingSystemList = $rankingSystemList;
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Final Methods">
  /**
   * RankingSystemListEntry init
   * @param string[] $keys list of additional fields
   */
  protected final function init(array $keys)
  {
    $this->numberRankedEntities = 0;
    $this->initSubClassData($keys);
  }
//</editor-fold desc="Protected Final Methods">


}