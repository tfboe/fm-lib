<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 10:27 AM
 */

namespace Tfboe\FmLib\Tests\Entity;


use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\Helpers\NumericalPlayerId;
use Tfboe\FmLib\Entity\PlayerInterface;

/**
 * Class Player
 * @package Tfboe\FmLib\Entity
 * @ORM\Entity
 * @ORM\Table(name="players",indexes={@ORM\Index(name="unique_names_birthday",
 *   columns={"first_name","last_name","birthday"})})
 */
class Player extends BaseEntity implements PlayerInterface
{
  use \Tfboe\FmLib\Entity\Traits\Player;
  use NumericalPlayerId;
//</editor-fold desc="Public Methods">
}