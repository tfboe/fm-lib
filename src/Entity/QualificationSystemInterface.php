<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:45 PM
 */

namespace Tfboe\FmLib\Entity;

use Tfboe\FmLib\Entity\Helpers\BaseEntityInterface;
use Tfboe\FmLib\Entity\Helpers\UUIDEntityInterface;


/**
 * Interface QualificationSystemInterface
 * @package Tfboe\FmLib\Entity
 */
interface QualificationSystemInterface extends BaseEntityInterface, UUIDEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return PhaseInterface
   */
  public function getNextPhase(): PhaseInterface;

  /**
   * @return PhaseInterface
   */
  public function getPreviousPhase(): PhaseInterface;

  /**
   * @param PhaseInterface $nextPhase
   */
  public function setNextPhase(PhaseInterface $nextPhase);

  /**
   * @param PhaseInterface $previousPhase
   */
  public function setPreviousPhase(PhaseInterface $previousPhase);
//</editor-fold desc="Public Methods">
}