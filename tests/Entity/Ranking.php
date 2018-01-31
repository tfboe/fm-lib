<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/20/17
 * Time: 12:30 PM
 */

namespace Tfboe\FmLib\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\RankingInterface;

/**
 * Class Ranking
 * @package Tfboe\FmLib\Entity
 * @ORM\Entity
 * @ORM\Table(name="rankings")
 *
 * Method hint for getName, since it will never throw an exception (name gets initialized empty)
 * @method string getName()
 */
class Ranking extends BaseEntity implements RankingInterface
{
  use \Tfboe\FmLib\Entity\Traits\Ranking;


  /**
   * Ranking constructor.
   */
  public function __construct()
  {
    $this->init();
  }
}