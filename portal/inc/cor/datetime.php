<?php
class CInc_Cor_Datetime extends CCor_Obj {
  
  public function __construct($aDate = NULL) {
    $lDat = (NULL === $aDate) ? date('Y-m-d H:i:s') : $aDate;
    $this -> setSql($lDat); 
  }
  
  public function setSql($aDate) {
    $aDate = substr(trim($aDate), 0, 19);
    $this -> clear();
    
    if (empty($aDate) or (substr($aDate, 0, 4) == '0000')) {
      $this -> clear();
      return;
    }
    list($lDay, $lTime) = explode(' ', $aDate);
    list($lY, $lM, $lD) = explode('-', $lDay);
    $this -> mYear  = intval($lY);
    $this -> mMonth = intval($lM);
    $this -> mDay   = intval($lD);
    
    list($lHrs, $lMin, $lSec) = explode(':', $lTime);
    $this -> mHour   = intval($lHrs);
    $this -> mMin    = intval($lMin);
    $this -> mSec    = intval($lSec);
    
    $this -> mTim = mktime($this -> mHour, $this -> mMin, $this -> mSec, 
      $this -> mMonth, $this -> mDay, $this -> mYear);
    $this -> mDat = date('Y-m-d H:i:s', $this -> mTim); 
  }
  
  public function clear() {
    $this -> mDat   = '';
    $this -> mTim   = 0;
    $this -> mYear  = 0;
    $this -> mMonth = 0;
    $this -> mDay   = 0;

    $this -> mHour  = 0;
    $this -> mMin   = 0;
    $this -> mSec   = 0;
  }
  
  public function getTime() {
    return $this -> mTim;
  }
  
  public function setTime($aTime) {
    if (empty($aTime)) {
      $this -> mDat = '';
      $this -> mTim = 0;
      return;
    }
    $this -> mTim = intval($aTime);
    $this -> mDat = date('Y-m-d H:i:s', $this -> mTim);
  }
  
  public function getString() {
    return date(lan('lib.datetime.long'), $this -> mTim);
  }
  
  public function getLong() {
    return date('D M j, Y', $this -> mTim);
  }
  
  public function getSql() {
    return $this -> mDat;
  }
  
  public function getDowString() {
    return date('W', $this -> mTim);
  }
  
  public function getDow() {
    $lRet = date('w', $this -> mTim) -1;
    if (-1 == $lRet) {
      $lRet = 6;
    }  
    return $lRet;
  }
  
  public function getFmt($aFormat) {
    if ($this -> isEmpty()) {
      return '';
    }
    return date($aFormat, $this -> mTim);
  }
  
  public function isEmpty() {
    $lDat = $this -> mDat;
    if (empty($lDat)) return TRUE;
    if (substr($lDat,0,4) == '0000') return TRUE;
    return FALSE;
  }
  
  public function isBeforeNoon() {
    return ($this -> mHour < 12);
  }
  
  public function getHalfDay() {
    if ($this -> mHour < 12) {
      return 0;
    } else {
      return .5;
    }
  }
  
  public function advance() {
    $lOrg = $this -> mDay;
    $lDay = $this -> mDay + 1;
    $lTim = mktime($this -> mHour, $this -> mMin, $this -> mSec, 
      $this -> mMonth, $lDay, $this -> mYear);
    $lDat = date('Y-m-d H:i:s', $lTim);
    $this -> setSql($lDat);
    #if ($lOrg == $this -> mDay) {
    #  $this -> dbg('Error in advancing');
    #}
  }
  
  public function isHoliday() {
    return ($this -> getDow() > 5); 
  }
  
  public static function calcReportDif($aStart, $aEnd) {
    if (empty($aStart)) {
      return '-';
    }
    if (empty($aEnd)) {
      return '-';
    }
    $lSta = new CCor_Datetime($aStart);
    if ($lSta -> isEmpty()) {
      return '-';
    }
    $lEnd = new CCor_Datetime($aEnd);
    if ($lEnd -> isEmpty()) {
      return '-';
    }
    $lStaDay = $lSta -> getFmt('Y-m-d'); 
    $lEndDay = $lEnd -> getFmt('Y-m-d');
    if ($lStaDay > $lEndDay) {
      return '-';
    }
    
    if ($lStaDay == $lEndDay) {
      $lSta = $lSta -> getHalfDay();
      $lEnd = $lEnd -> getHalfDay();
      return ($lSta == $lEnd) ? .25 : .5; 
    }
    
    $lDays = 0;
    while ($lStaDay < $lEndDay) {
      $lSta -> advance();
      $lStaDay = $lSta -> getFmt('Y-m-d');
      if (!$lSta -> isHoliday()) {
        $lDays ++;
      }
    }
    $lSta = $lSta -> getHalfDay();
    $lEnd = $lEnd -> getHalfDay();
    $lDays = $lDays + $lEnd - $lSta;
    return $lDays;
  }
  
}