<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 2/4/18
 * Time: 1:56 AM
 */

namespace Tfboe\FmLib\Entity\Helpers;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Exceptions\Internal;

/**
 * Trait StartAndFinishable
 * @package App\Entity\Helpers
 */
trait StartAndFinishable
{
  use TimeEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="smallint")
   * @var int
   */
  private $status = 0;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Final Methods">
  /**
   * @return int
   */
  final public function getStatus(): int
  {
    return $this->status;
  }

  /**
   * @return bool
   */
  final public function isFinished(): bool
  {
    return $this->statusIsFinished($this->status);
  }

  /**
   * @return bool
   */
  final public function isStarted(): bool
  {
    return $this->statusIsStarted($this->status);
  }

  /**
   * @param StartAndFinishableInterface $entity
   */
  final public function cloneFrom(StartAndFinishableInterface $entity): void
  {
    $this->status = $entity->getStatus();
    $this->setStartTime($entity->getStartTime());
    $this->setEndTime($entity->getEndTime());
  }

  /**
   * @param int $status
   * @param DateTime|null $now
   * @param bool $setStartTime
   * @param bool $setEndTime
   */
  final public function setStatus(int $status, ?DateTime $now, bool $setStartTime = true, bool $setEndTime = true): void
  {
    if ($status === $this->status) {
      //nothing to do
      return;
    }
    $this->ensureValidValue($status);
    if (!$this->changeIsValid($this->status, $status)) {
      Internal::error("Invalid status change!");
    }
    //set reset start/end times
    if ($this->statusIsFinished($status)) {
      if ($setEndTime && !$this->statusIsFinished($this->status)) {
        $this->setEndTime($now);
      }
      if ($setStartTime && !$this->statusIsStarted($this->status)) {
        $this->setStartTime($now);
      }
    } elseif ($this->statusIsStarted($status)) {
      if ($setEndTime) {
        $this->setEndTime(null);
      }
      if ($setStartTime && !$this->statusIsStarted($this->status)) {
        $this->setStartTime($now);
      }
    } else {
      if ($setEndTime) {
        $this->setEndTime(null);
      }
      if ($setStartTime) {
        $this->setStartTime(null);
      }
    }

    $this->status = $status;
  }
//</editor-fold desc="Public Final Methods">

//<editor-fold desc="Public Methods">
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Methods">
  /**
   * @param int|null $oldStatus
   * @param int $newStatus
   * @return bool whether it is allowed to change the status from old to new in one step
   */
  protected function changeIsValid(?int $oldStatus, int $newStatus): bool
  {
    return abs($newStatus - $oldStatus) <= 1;
  }

  /**
   * Ensures that the given status is valid
   * @param int $status
   */
  protected function ensureValidValue(int $status): void
  {
    StartFinishStatus::ensureValidValue($status);
  }

  /**
   * Checks if the given status is finished
   * @param int $status
   * @return bool
   */
  protected function statusIsFinished(int $status): bool
  {
    return $status >= StartFinishStatus::FINISHED;
  }

  /**
   * Checks if the given status is started
   * @param int $status
   * @return bool
   */
  protected function statusIsStarted(int $status): bool
  {
    return $status >= StartFinishStatus::STARTED;
  }
//</editor-fold desc="Protected Methods">

}