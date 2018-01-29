<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 12/3/17
 * Time: 5:55 PM
 */

namespace Tfboe\FmLib\Entity;


use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\Helpers\UUIDEntity;

/**
 * Class QualificationSystem
 * @package Tfboe\FmLib\Entity
 * @ORM\Entity
 * @ORM\Table(name="qualificationSystems")
 */
class QualificationSystem extends BaseEntity
{
  use UUIDEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\ManyToOne(targetEntity="PhaseInterface", inversedBy="postQualifications")
   * @var PhaseInterface
   */
  private $previousPhase;

  /**
   * @ORM\ManyToOne(targetEntity="PhaseInterface", inversedBy="preQualifications")
   * @var PhaseInterface
   */
  private $nextPhase;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return PhaseInterface
   */
  public function getNextPhase(): PhaseInterface
  {
    return $this->nextPhase;
  }

  /**
   * @return PhaseInterface
   */
  public function getPreviousPhase(): PhaseInterface
  {
    return $this->previousPhase;
  }

  /**
   * @param PhaseInterface $nextPhase
   * @return $this|QualificationSystem
   */
  public function setNextPhase(PhaseInterface $nextPhase): QualificationSystem
  {
    if ($this->nextPhase !== null) {
      $this->nextPhase->getPreQualifications()->removeElement($this);
    }
    $this->nextPhase = $nextPhase;
    $nextPhase->getPreQualifications()->add($this);
    return $this;
  }

  /**
   * @param PhaseInterface $previousPhase
   * @return $this|QualificationSystem
   */
  public function setPreviousPhase(PhaseInterface $previousPhase): QualificationSystem
  {
    if ($this->previousPhase !== null) {
      $this->previousPhase->getPostQualifications()->removeElement($this);
    }
    $this->previousPhase = $previousPhase;
    $previousPhase->getPostQualifications()->add($this);
    return $this;
  }
//</editor-fold desc="Public Methods">
}