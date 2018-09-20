<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 8/31/18
 * Time: 9:15 AM
 */

namespace Tfboe\FmLib\Service;


use Tfboe\FmLib\Entity\TermsInterface;

interface TermsServiceInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @return TermsInterface
   */
  public function getLatestTerms(): TermsInterface;
//</editor-fold desc="Public Methods">
}