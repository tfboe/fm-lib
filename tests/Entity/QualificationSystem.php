<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 12/3/17
 * Time: 5:55 PM
 */

namespace Tfboe\FmLib\Tests\Entity;


use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\QualificationSystemInterface;

/**
 * Class QualificationSystem
 * @package Tfboe\FmLib\Entity
 * @ORM\Entity
 * @ORM\Table(name="qualificationSystems")
 */
class QualificationSystem extends BaseEntity implements QualificationSystemInterface
{
  use \Tfboe\FmLib\Entity\Traits\QualificationSystem;
}