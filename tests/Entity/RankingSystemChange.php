<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/5/18
 * Time: 10:54 PM
 */

namespace Tfboe\FmLib\Tests\Entity;


use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\RankingSystemChangeInterface;

/**
 * Class RankingSystemList
 * @package Tfboe\FmLib\Entity
 * @ORM\Entity
 * @ORM\Table(name="rankingSystemChanges")
 */
class RankingSystemChange extends BaseEntity implements RankingSystemChangeInterface
{
  use \Tfboe\FmLib\Entity\Traits\RankingSystemChange;

//<editor-fold desc="Constructor">

  /**
   * RankingSystem constructor.
   * @param array $keys
   */
  public function __construct(array $keys)
  {
    $this->init($keys);
  }
//</editor-fold desc="Constructor">
}