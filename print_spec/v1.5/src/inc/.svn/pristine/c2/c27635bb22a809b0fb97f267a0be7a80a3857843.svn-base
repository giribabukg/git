<?php
class CInc_Cor_Date extends CCor_Obj {

  public function __construct($aDate = NULL) {
    $lDat = (NULL === $aDate) ? date('Y-m-d') : $aDate;

    $lDatFormat = lan('lib.date.long');
    if (!empty($lDatFormat)) {
      $this -> mDatFormat = $lDatFormat;
    } else {
      $this -> mDatFormat = 'Y-m-d';
    }
    $this -> mDelimiter = '-';
    if (FALSE !== strpos($this -> mDatFormat, '.')) {
      $lArr = explode('.', $this -> mDatFormat);
      $this -> mDelimiter = '.';
    } elseif (FALSE !== strpos($this -> mDatFormat, '/')) {
      $lArr = explode('/', $this -> mDatFormat);
      $this -> mDelimiter = '/';
    } elseif (FALSE !== strpos($this -> mDatFormat, '-')) {
      $lArr = explode('-', $this -> mDatFormat);
    } else {
      $lArr = array();
    }
    if (!empty($lArr) AND count($lArr) == 3) {
      $lArr = array_flip($lArr);
      $this -> mDayNr   = $lArr['d'];
      $this -> mMonthNr = (isset($lArr['m']) ? $lArr['m'] : $lArr['M']);
      $this -> mYearNr  = (isset($lArr['Y']) ? $lArr['Y'] : $lArr['y']);
    }

    if (FALSE !== strtotime($lDat)) {
      $lDat = date('Y-m-d', strtotime($lDat));
    }
    $this -> setSql($lDat);
  }

  public function setSql($aDate) {
    $aDate = substr(trim($aDate), 0, 10);
    $this -> clear();

    if (empty($aDate) or (substr($aDate, 0, 4) == '0000')) {
      $this -> clear();
      return;
    }
    $lDatum = explode('-', $aDate);
    if (3 == count($lDatum)) {
      list($lY, $lM, $lD) = $lDatum;
    } else {
      $this -> clear();
      return;
    }
    $this -> mYear  = intval($lY);
    $this -> mMonth = intval($lM);
    $this -> mDay   = intval($lD);
    $this -> mTim = mktime(0, 0, 0, $this -> mMonth, $this -> mDay, $this -> mYear);
    $this -> mDat = date('Y-m-d', $this -> mTim);
  }

  public function clear() {
    $this -> mDat = '';
    $this -> mTim = 0;
    $this -> mYear  = 0;
    $this -> mMonth = 0;
    $this -> mDay   = 0;
  }

  public function setInp($aStr) {
    $aStr = substr(trim($aStr), 0, 10);
    $this -> clear();
    if (empty($aStr)) {
      return;
    }

    $lArr = explode($this -> mDelimiter, $aStr);
    if (count($lArr) == 3) {
      $this -> mDay   = $lArr[$this -> mDayNr];
      $this -> mMonth = $lArr[$this -> mMonthNr];
      $this -> mYear  = $lArr[$this -> mYearNr];
    }

    if (FALSE !== strtotime($aStr)) {
      $this -> mDat = date('Y-m-d', strtotime($aStr));
    } else {
      $lStr = $this->mDay . '-' . $this->mMonth . '-' . $this->mYear;
      $this -> mDat = date('Y-m-d', strtotime($lStr));
    }
  }

  public function setTime($aTime) {
    if (empty($aTime)) {
      $this -> mDat = '';
      $this -> mTim = 0;
      return;
    }
    $this -> mTim = intval($aTime);
    $this -> mDat = date('Y-m-d', $this -> mTim);
  }

  public function getString() {
    return date($this -> mDatFormat, $this -> mTim);
  }

  public function getLong() {
    return date('D M j, Y', $this -> mTim);
  }
  

  public function getTime() {
    return $this -> mTim;
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

  public function isPast() {
    return (date('Y-m-d') > $this -> mDat);
  }

  public function isToday() {
    return (date('Y-m-d') == $this -> mDat);
  }

  public function isHoliday() {
    $lDow = $this -> getDow();
    if (5 == $lDow) {
      return TRUE;
    }
    if (6 == $lDow) {
      return TRUE;
    }
  }

  public function getDaysDif($aDays) {
    $lTim = mktime(0,0,0, $this -> mMonth, ($this -> mDay + $aDays), $this -> mYear);
    $lRet = new CCor_Date(date('Y-m-d', $lTim));
    return $lRet;
  }

  public function calcDaysDif($aSqlDate) {
    if (empty($aSqlDate)) return 0;
    if ($this -> isEmpty()) return 0;
    if ($aSqlDate == $this -> mDat) return 0;

    @list($lYear,$lMonth,$lDay) = @explode('-', $aSqlDate);
    if ($aSqlDate > $this -> mDat) {
      $lRet = 0;
      while ($aSqlDate > $this -> mDat) {
        $lRet--;
        $lTim = mktime(0,0,0, $lMonth, ($lDay + $lRet), $lYear);
        $aSqlDate = date('Y-m-d', $lTim);
        #echo $this -> mDat.' '.$aSqlDate.' '.$lRet.BR;
      }
      return -$lRet;
    } else {
      $lRet = 0;
      while ($aSqlDate < $this -> mDat) {
        $lRet++;
        $lTim = mktime(0,0,0, $lMonth, ($lDay + $lRet), $lYear);
        $aSqlDate = date('Y-m-d', $lTim);
        #echo $this -> mDat.' '.$aSqlDate.' '.$lRet.BR;
      }
      return -$lRet;
    }
  }

  public function getFirstOfWeek() {
    $lDow = $this -> getDow();
    $lTim = mktime(0,0,0, $this -> mMonth, $this -> mDay - $lDow, $this -> mYear);
    return new CCor_Date(date('Y-m-d', $lTim));
  }

  public function getLastOfWeek() {
    $lDow = $this -> getDow();
    $lDat = $this -> mYear.'-'.$this -> mMonth.'-'.($this -> mDay + 6 - $lDow);
    $lRet = new CCor_Date($lDat);
    return $lRet;
  }

  public function getFirstOfMonth() {
    $lDat = $this -> mYear.'-'.$this -> mMonth.'-01';
    return new CCor_Date($lDat);
  }

  public function getLastOfMonth() {
    $lDat = $this -> mYear.'-'.($this -> mMonth+1).'-00';
    return new CCor_Date($lDat);
  }

  public function getFirstOfMonthPlus($aDif = 1) {
    $lDat = $this -> mYear.'-'.($this -> mMonth + $aDif).'-01';
    return new CCor_Date($lDat);
  }

  public function getFirstOfYear() {
    $lDat = $this -> mYear.'-01-01';
    return new CCor_Date($lDat);
  }

  public function getLastOfYear() {
    $lDat = $this -> mYear.'-12-31';
    return new CCor_Date($lDat);
  }

  public function getWeekdays2Numbers() {
    $lRet = array(
      'sunday'    => 0,
      'monday'    => 1,
      'tuesday'   => 2,
      'wednesday' => 3,
      'thursday'  => 4,
      'friday'    => 5,
      'saturday'  => 6
    );
    return $lRet;
  }

  /*
   * z.B. suche das Datum ab $aFromDay in $aAmountDays Werktagen
   *
   * used in job/dialog.php
   *
   * @access public
   * @param  integer $aAmountDays how many workdays in future
   * @param  boolean Calculate new Dates: Yes=default/No
   * @param  integer $aFromDay timestamp of startday
   * @return array (all workdays <= $aAmountDays, all calculated weekdays)
   */
  public function getWorkdays($aAmountDays = 1, $aWithDate = TRUE, $aFromDay = '') {

    $lAmountDays = $aAmountDays;//$this -> mDurationTime;

    if ($aWithDate) {
      if (empty($aFromDay)) {
        $lFromDay = time();
      } else {
        $lFromDay = $aFromDay;
      }
      /*
       * Offset der Zeitzone in Sekunden. Der Offset für Zeitzonen westlich von UTC ist immer negativ und für Zeitzonen oestlich von UTC immer positiv.
      */
      $lOffset = date('Z', $lFromDay);
      /*
       * 86400sec = 60sec * 60min * 24h = 1day
      */
      $l1Day = 86400;
      /*
       * if $lFromDay = 2012-03-22 15:12:03, then $lStartDay = 2012-03-23 00:00:00
      */
      $lStartDay = ceil($lFromDay / $l1Day) * $l1Day - $lOffset;
    }
    /*
     * Numerischer Tag einer Woche: 0 (fuer Sonntag) bis 6 (fuer Samstag)
    */
    $lWeekDay = (int)date('w', $lStartDay);

    $lWeekend = CCor_Cfg::get('hol.weekend', array('sunday', 'saturday'));
    $lWeekdays2Numbers = CCor_Date::getWeekdays2Numbers();
    $lNoWork = array();
    foreach ($lWeekend as $lD) {
      if (isset($lWeekdays2Numbers[$lD])) {
        $lNoWork[] = $lWeekdays2Numbers[$lD];
      }
    }
    /*
     * no working days: So=0, Sa=6
     */
    $lNoWorkdays = $lNoWork;
    /*
     * delivers an array (all workdays, all calculated weekdays) with all workdays <= $aAmountDays
     */
    $lDurInDays = array();
    /*
     * all workdays <= $aAmountDays
     */
    $lDuration = 1;
    /*
     * all calculated weekdays
     */
    $lPlusDays = 0;
    /*
     * Calculate the 'distance' of workdays in weekdays
     */
    for ($i = 0; $i < $lAmountDays; $i++) {
      $lPlusDays++;
      if (in_array(($i + $lWeekDay) % 7, $lNoWorkdays)) {// if 'nextday' $i = Sa or So, increment $lAmountDays: add to workday amount of weekend days
        $lAmountDays++;
        continue;
      } else {
        $lDurInDays[$lDuration] = array('newDur' => $lPlusDays);
        if ($aWithDate) {
          $lNewDate = $lFromDay + $lPlusDays * $l1Day - $lOffset;
          $lDurInDays[$lDuration]['date'] = $lNewDate;
        }
        $lDuration++;
      }
    }
    #echo '<pre>---date.php---'.get_class().'---';var_dump($lAmountDays,$lDurInDays,'#############');echo '</pre>';
    return $lDurInDays;
  }

}