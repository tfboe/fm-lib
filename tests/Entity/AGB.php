<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/28/18
 * Time: 11:37 PM
 */

namespace Tfboe\FmLib\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\AGBInterface;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;

/**
 * Class Competition
 * @package Tfboe\FmLib\Tests\Entity
 * @ORM\Entity
 * @ORM\Table(name="agbs",indexes={@ORM\Index(name="unique_version", columns={"minor_version","major_version"})})
 */
class AGB extends BaseEntity implements AGBInterface
{
  use \Tfboe\FmLib\Entity\Traits\AGB;
}