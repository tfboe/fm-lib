<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 12/3/17
 * Time: 5:55 PM
 */

namespace Tfboe\FmLib\Entity\Traits;


use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\UUIDEntity;
use Tfboe\FmLib\Entity\PhaseInterface;


/**
 * Trait QualificationSystem
 * @package Tfboe\FmLib\Entity\Traits
 */
trait QualificationSystem
{
  use UUIDEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\PhaseInterface", inversedBy="postQualifications")
   * @var PhaseInterface
   */
  private $previousPhase;

  /**
   * @ORM\ManyToOne(targetEntity="\Tfboe\FmLib\Entity\PhaseInterface", inversedBy="preQualifications")
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
   */
  public function setNextPhase(PhaseInterface $nextPhase)
  {
    if ($this->nextPhase !== null) {
      $this->nextPhase->getPreQualifications()->removeElement($this);
    }
    $this->nextPhase = $nextPhase;
    $nextPhase->getPreQualifications()->set($this->getId(), $this);
  }

  /**
   * @param PhaseInterface $previousPhase
   */
  public function setPreviousPhase(PhaseInterface $previousPhase)
  {
    if ($this->previousPhase !== null) {
      $this->previousPhase->getPostQualifications()->removeElement($this);
    }
    $this->previousPhase = $previousPhase;
    $previousPhase->getPostQualifications()->set($this->getId(), $this);
  }
//</editor-fold desc="Public Methods">
}