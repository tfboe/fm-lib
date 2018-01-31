<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/30/18
 * Time: 5:31 PM
 */

namespace Tfboe\FmLib\Entity\Helpers;


use Tfboe\FmLib\Exceptions\ValueNotValid;

/**
 * Interface ResultEntityInterface
 * @package Tfboe\FmLib\Entity\Helpers
 */
interface ResultEntityInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return int
   */
  public function getResult(): int;

  /**
   * @return int
   */
  public function getResultA(): int;

  /**
   * @return int
   */
  public function getResultB(): int;

  /**
   * @return bool
   */
  public function isPlayed(): bool;

  /**
   * @param bool $played
   * @return $this|ResultEntity
   */
  public function setPlayed(bool $played);

  /**
   * @param int $result
   * @return $this|ResultEntity
   * @throws ValueNotValid
   */
  public function setResult(int $result);

  /**
   * @param int $resultA
   * @return $this|ResultEntity
   */
  public function setResultA(int $resultA);

  /**
   * @param int $resultB
   * @return $this|ResultEntity
   */
  public function setResultB(int $resultB);
//</editor-fold desc="Public Methods">
}