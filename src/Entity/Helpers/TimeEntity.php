<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 2:57 PM
 */

namespace Tfboe\FmLib\Entity\Helpers;

use DateTime;
use DateTimeZone;
use Tfboe\FmLib\Helpers\DateTimeHelper;

/**
 * Trait TimeEntity
 * @package Tfboe\FmLib\Entity\Helpers
 */
trait TimeEntity
{
//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="datetime", nullable=true)
   * @var DateTime|null
   */
  private $startTime = null;

  /**
   * @ORM\Column(type="datetime", nullable=true)
   * @var DateTime|null
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

  /**
   * @var bool
   */
  private $startLocalized = false;

  /**
   * @var bool
   */
  private $endLocalized = false;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return DateTime|null
   */
  public function getEndTime(): ?DateTime
  {
    if ($this->endTime !== null && !$this->endLocalized) {
      $this->endTime->setTimezone(new DateTimeZone($this->endTimezone));
      $this->endLocalized = true;
    }
    return $this->endTime;
  }

  /**
   * @return DateTime|null
   */
  public function getStartTime(): ?DateTime
  {
    if ($this->startTime !== null && !$this->startLocalized) {
      $this->startTime->setTimezone(new DateTimeZone($this->startTimezone));
      $this->startLocalized = true;
    }
    return $this->startTime;
  }


  /**
   * @param DateTime|null $endTime
   * @return $this|TimeEntity
   */
  public function setEndTime(?DateTime $endTime)
  {
    if ($this->endTime === null || !DateTimeHelper::eq($endTime, $this->getEndTime())) {
      $this->endTime = $endTime;
      $this->endTimezone = $endTime === null ? "" : $endTime->getTimezone()->getName();
      $this->endLocalized = true;
    }
    return $this;
  }

  /**
   * @param DateTime|null $startTime
   * @return $this|TimeEntity
   */
  public function setStartTime(?DateTime $startTime)
  {
    if ($this->startTime === null || !DateTimeHelper::eq($startTime, $this->getStartTime())) {
      $this->startTime = $startTime;
      $this->startTimezone = $startTime === null ? "" : $startTime->getTimezone()->getName();
      $this->startLocalized = true;
    }
    return $this;
  }
//</editor-fold desc="Public Methods">
}