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
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\TermsInterface;

/**
 * Class Competition
 * @package Tfboe\FmLib\Tests\Entity
 * @ORM\Entity
 * @ORM\Table(name="terms",indexes={@ORM\Index(name="unique_version", columns={"minor_version","major_version"})})
 */
class Terms extends BaseEntity implements TermsInterface
{
  use \Tfboe\FmLib\Entity\Traits\Terms;
}