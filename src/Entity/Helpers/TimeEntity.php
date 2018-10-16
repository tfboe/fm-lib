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

  /** @var @var \DateTime|null */
  private $_localizedStartTime = null;

  /** @var @var \DateTime|null */
  private $_localizedEndTime = null;

//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return \DateTime|null
   */
  public function getEndTime(): ?\DateTime
  {
    if ($this->endTime !== null && $this->_localizedEndTime === null) {
      $this->_localizedEndTime = clone $this->endTime;
      $this->_localizedEndTime->setTimezone(new \DateTimeZone($this->endTimezone));
    }
    return $this->_localizedEndTime;
  }

  /**
   * @return \DateTime|null
   */
  public function getStartTime(): ?\DateTime
  {
    if ($this->startTime !== null && $this->_localizedStartTime === null) {
      $this->_localizedStartTime = clone $this->startTime;
      $this->_localizedStartTime->setTimezone(new \DateTimeZone($this->startTimezone));
    }
    return $this->_localizedStartTime;
  }


  /**
   * @param \DateTime|null $endTime
   * @return $this|TimeEntity
   */
  public function setEndTime(?\DateTime $endTime)
  {
    $this->_localizedEndTime = $endTime;
    $this->endTimezone = $endTime === null ? "" : $endTime->getTimezone()->getName();
    $this->endTime = clone $endTime;
    $this->endTime->setTimezone(new \DateTimeZone("UTC"));
    return $this;
  }

  /**
   * @param \DateTime|null $startTime
   * @return $this|TimeEntity
   */
  public function setStartTime(?\DateTime $startTime)
  {
    $this->_localizedStartTime = $startTime;
    $this->startTimezone = $startTime === null ? "" : $startTime->getTimezone()->getName();
    $this->startTime = clone $startTime;
    $this->startTime->setTimezone(new \DateTimeZone("UTC"));
    return $this;
  }
//</editor-fold desc="Public Methods">
}