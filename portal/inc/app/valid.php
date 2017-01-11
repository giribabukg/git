<?php
/**
 * Various validation routines
 *
 * @package    Application
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */


class CInc_App_Valid extends CCor_Obj {

  /**
   * Validate a EAN Barcode
   *
   * @param string $aCode The Barcode to check
   * @param int $aDigits Number of digits, e.g. 13 for EAN-13, default 13
   * @return boolean True if $aCode is a valid barcode
   */

  public static function ean($aCode, $aDigits = 13) {
    $lPat = "/[0-9]{".$aDigits."}/";
    if (preg_match($lPat, $aCode) == 0) {
      return false;
    }
    $lSum = 0;
    $aDigits--;
    $lFak = $aDigits;
    for ($i = 0; $i < $aDigits; $i++) {
      $lNum = (int)substr($aCode, $i, 1);
      if (($lFak % 2) == 0) {
        $lSum += $lNum;
      } else {
        $lSum += $lNum * 3;
      }
      $lFak -= 1;
    }
    if (($lSum % 10) == 0) {
      $lSum = 0;
    } else {
      $lSum = 10 - ($lSum % 10);
    }
    return ((int)substr($aCode,$aDigits,1) == $lSum);
  }
  
  public static function int($aVal) {
    $lPat = "/^-?\d*\.?\d*$/";
    
    if (preg_match($lPat, $aVal) == 0) {
      return false;
    } else {
      return true;
    }    
  }

  public static function domain($aVal) {
    $lDom = substr(strrchr(trim($aVal), "@"), 1);
    $lBlackList = CCor_Cfg::get('blacklisted_domains');
    if(in_array($lDom, $lBlackList)) {
      return false;
    }
    else {
      return true;
    }
  }
}