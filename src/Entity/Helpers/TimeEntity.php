<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 2:57 PM
 */

namespace Tfboe\FmLib\Entity\Helpers;

/**
 * Trait TimeEntity
 * @package Tfboe\FmLib\Entity\Helpers
 */
trait TimeEntity
{
//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="datetime", nullable=true)
   * @var \DateTime|null
   */
  private $startTime = null;

  /**
   * @ORM\Column(type="datetime", nullable=true)
   * @var \DateTime|null
   */
  private $endTime = null;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $startTimezone = "";

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $endTimezone = "";

  /** @var \DateTime|null */
  private $localizedStartTime = null;

  /** @var \DateTime|null */
  private $localizedEndTime = null;

//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return \DateTime|null
   */
  public function getEndTime(): ?\DateTime
  {
    if ($this->endTime !== null && $this->localizedEndTime === null) {
      $this->localizedEndTime = clone $this->endTime;
      $this->localizedEndTime->setTimezone(new \DateTimeZone($this->endTimezone));
    }
    return $this->localizedEndTime;
  }

  /**
   * @return \DateTime|null
   */
  public function getStartTime(): ?\DateTime
  {
    if ($this->startTime !== null && $this->localizedStartTime === null) {
      $this->localizedStartTime = clone $this->startTime;
      $this->localizedStartTime->setTimezone(new \DateTimeZone($this->startTimezone));
    }
    return $this->localizedStartTime;
  }

  /**
   * @ORM\PostLoad
   */
  public function postLoad()
  {
    $this->localizedEndTime = null;
    $this->localizedStartTime = null;
  }

  /**
   * @param \DateTime|null $endTime
   * @return $this|TimeEntity
   */
  public function setEndTime(?\DateTime $endTime)
  {
    $this->localizedEndTime = $endTime;
    $this->endTimezone = $endTime === null ? "" : $endTime->getTimezone()->getName();
    if ($this->localizedEndTime !== null && $this->localizedEndTime != $this->endTime) {
      $this->endTime = clone $this->localizedEndTime;
      $this->endTime->setTimezone(new \DateTimeZone("UTC"));
    } elseif ($endTime === null) {
      $this->endTime = null;
    }
    return $this;
  }

  /**
   * @param \DateTime|null $startTime
   * @return $this|TimeEntity
   */
  public function setStartTime(?\DateTime $startTime)
  {
    $this->localizedStartTime = $startTime;
    $this->startTimezone = $startTime === null ? "" : $startTime->getTimezone()->getName();
    if ($this->localizedStartTime !== null && $this->localizedStartTime != $this->startTime) {
      $this->startTime = clone $this->localizedStartTime;
      $this->startTime->setTimezone(new \DateTimeZone("UTC"));
    } elseif ($startTime === null) {
      $this->startTime = null;
    }
    return $this;
  }
//</editor-fold desc="Public Methods">
}