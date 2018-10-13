<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/12/18
 * Time: 11:45 AM
 */

namespace Tfboe\FmLib\Entity\Helpers;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\CategoryTraits\GameMode;
use Tfboe\FmLib\Entity\CategoryTraits\OrganizingMode;
use Tfboe\FmLib\Entity\CategoryTraits\ScoreMode;
use Tfboe\FmLib\Entity\CategoryTraits\Table;
use Tfboe\FmLib\Entity\CategoryTraits\TeamMode;
use Tfboe\FmLib\Entity\RankingSystemInterface;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 */
abstract class TournamentHierarchyEntity extends BaseEntity implements TournamentHierarchyInterface
{
  use GameMode;
  use TeamMode;
  use OrganizingMode;
  use ScoreMode;
  use Table;
  use TimeEntity;
  use UUIDEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\ManyToMany(
   *     targetEntity="\Tfboe\FmLib\Entity\RankingSystemInterface",
   *     inversedBy="hierarchyEntries",
   *     indexBy="id"
   * )
   * @ORM\JoinTable(name="relation__hierarchy_entities_ranking_systems")
   * @var Collection|RankingSystemInterface[]
   */
  private $rankingSystems;

  /**
   * @var RankingSystemInterface[]
   */
  private $influencingRankingSystems;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * Competition constructor.
   */
  public function __construct()
  {
    $this->rankingSystems = new ArrayCollection();
    $this->influencingRankingSystems = [];
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return RankingSystemInterface[]|Collection
   */
  public function getRankingSystems()
  {
    return $this->rankingSystems;
  }

  /**
   * @return RankingSystemInterface[]
   */
  public function getInfluencingRankingSystems(): array
  {
    return $this->influencingRankingSystems;
  }

  /**
   * @param RankingSystemInterface $rankingSystem
   */
  public function addInfluencingRankingSystem(RankingSystemInterface $rankingSystem)
  {
    $this->influencingRankingSystems[] = $rankingSystem;
  }
//</editor-fold desc="Public Methods">
}