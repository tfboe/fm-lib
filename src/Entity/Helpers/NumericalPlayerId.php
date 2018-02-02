<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 2/2/18
 * Time: 7:38 PM
 */

namespace Tfboe\FmLib\Entity\Helpers;


/**
 * Trait NumericalPlayerId
 * @package Tfboe\FmLib\Entity\Helpers
 */
trait NumericalPlayerId
{
//<editor-fold desc="Fields">
  /**
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer")
   * @var int
   */
  private $playerId;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return int
   */
  public function getPlayerId(): int
  {
    return $this->playerId;
  }
//</editor-fold desc="Public Methods">
}