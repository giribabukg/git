<?php
class CInc_Htm_Cal extends CCor_Ren {

  protected $mMod;

  public function __construct($aMod, $aDate = NULL, $aDate2 = NULL) {
    $this -> mMod = $aMod;
    $this -> setDate($aDate);
    $this -> mDate2 = $aDate2;
    $this -> mColCnt = 8;
    $this -> mHol = array();
    $this -> mShowHolidays = TRUE;

    $this -> mSelectMonth = TRUE;
    $this -> mSelectWeek  = TRUE;
    $this -> mMarkSelection = TRUE;
    $this -> mPick = FALSE;

    $this -> mLeftNav = -1;
    $this -> mRightNav = +1;
  }

  public function setPickMode($aFlag = TRUE) {
    $this -> mSelectMonth = !$aFlag;
    $this -> mSelectWeek  = !$aFlag;
    $this -> mPick = $aFlag;
    $this -> mMarkSelection = !$aFlag;
  }

  public function setNav($aLeft, $aRight) {
    $this -> mLeftNav = $aLeft;
    $this -> mRightNav = $aRight;
  }

  public function setWeekSelectEnabled($aFlag = TRUE) {
    $this -> mSelectWeek = $aFlag;
  }

  public function setMonthSelectEnabled($aFlag = TRUE) {
    $this -> mSelectMonth = $aFlag;
  }

  public function setDate($aDate = NULL) {
    $this -> mDate = (NULL == $aDate) ? date('Y-m-d') : substr($aDate, 0, 10);
    $lLis = explode('-', $this -> mDate);
    $lY = intval($lLis[0]);
    $lM = intval($lLis[1]);
    $lD = intval($lLis[2]);

    $this -> mYear = $lY;
    $this -> mMon  = $lM;
    $this -> mDay  = $lD;

    #$this -> mTime = mktime(0, 0, 0, $lM, $lD, $lY);

    $lTim = mktime(0, 0, 0, $lM, 1, $lY);
    $this -> mName = lan('moy.long.'.$lM);

    $lDow = date('w', $lTim);
    $lDif = $lDow - 1;
    if ($lDif == -1) $lDif = 6;
    $this -> mTim = mktime(0,0,0, $lM, 1 - $lDif, $lY);

    $this -> mFom = date('Y-m-d', $lTim);
    $lTim = mktime(0, 0, 0, $lM +1, 0, $lY);
    $this -> mLom = date('Y-m-d', $lTim);

    #$this -> mFday = date('d', $this -> mTim);
  }

  protected function onBeforeContent() {
    if ($this -> mShowHolidays) {
      $lSql = 'SELECT datum, name_'.LAN.' FROM al_sys_holidays ';
      $lSql.= 'WHERE datum BETWEEN "'.$this -> mFom.'" AND "'.$this -> mLom.'" ';
      $lSql.= 'ORDER BY datum';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $this -> mHol[$lRow['datum']] = $lRow['name_'.LAN];
      }
    }
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<table cellpadding="2" cellspacing="0" class="cal">'.LF;
    $lRet.= $this -> getHeader();
    $lRet.= $this -> getDays();
    if (!empty($this -> mHol)) {
      $lRet.= $this -> getHolidays();
    }
    $lRet.= '</table>'.LF;
    return $lRet;
  }

  protected function getNav($aDay, $aImg) {
    $lRet = '';
    $lRet.= '<td class="th2 ac">';
    $lRet.= '<a href="'.$this -> getLink($aDay).'" class="nav">';
    $lRet.= img('img/ico/16/nav-'.$aImg.'-lo.gif');
    $lRet.= '</a>';
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getMonthLink($aDif, $aImg) {
    $lTim = mktime(0, 0, 0, $this -> mMon + $aDif, 1, $this -> mYear);
    $lDay = date('Y-m-d', $lTim);
    $lDay2 = NULL;
    if ($this -> mLom == $this -> mDate2) {
      $lLom = mktime(0, 0, 0, $this -> mMon + $aDif+1, 0, $this -> mYear);
      $lDay2 = date('Y-m-d', $lLom);
    }

    $lRet = '<td class="cap ac">';
    $lRet.= '<a href="'.$this -> getLink($lDay, $lDay2).'" class="nav">';
    $lRet.= img('img/ico/16/nav-'.$aImg.'-lo.gif');
    $lRet.= '</a>';
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getBlindNav() {
    $lRet = '<td class="cap">';
    $lRet.= '<span class="nav">';
    $lRet.= '<img src="img/d.gif" width="16"  alt="" />';
    $lRet.= '</span>';
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getHeader() {
    $lRet = '<tr>'.LF;
    $lCol = $this -> mColCnt-2;
    #if ($this -> mLeftNav) {
      $lRet.= $this -> getMonthLink($this -> mLeftNav, 'first');
    #} else {
    #  $lRet.= $this -> getBlindNav();
    #}
    $lRet.= '<td class="cap ac" colspan="'.($lCol).'" style="padding:0px;">';
    if ($this -> mSelectMonth) {
      $lRet.= '<a href="'.$this -> getLink($this -> mFom, $this -> mLom).'" class="nav" style="display:inline; padding:2px;">';
      $lRet.= $this -> mName.' '.$this -> mYear;
      $lRet.= '</a>';
    } else {
      $lRet.= $this -> mName.' '.$this -> mYear;
    }
    $lRet.= '</td>'.LF;
    #if ($this -> mRightNav) {
      $lRet.= $this -> getMonthLink($this -> mRightNav, 'last');
    #} else {
    #  $lRet.= $this -> getBlindNav();
    #}

    $lRet.= '</tr>'.LF;

    $lRet.= '<tr>'.LF;
    $lRet.= '<td class="th2 w30 ac">&nbsp;</td>'.LF;
    for ($i=0; $i<7; $i++) {
      $lRet.= '<td class="th2 w30 ac">'.htm(lan('dow.'.$i)).'</td>'.LF;
    }
    $lRet.= '</tr>'.LF;

    return $lRet;
  }

  protected function getLink($aDay, $aDay2 = NULL) {
    $lRet = 'index.php?act='.$this -> mMod.'.day&amp;d='.$aDay;
    if (!empty($aDay2)) {
      $lRet.= '&amp;d2='.$aDay2;
    }
    return $lRet;
  }

  protected function getDayLink($aDay) {
    if ($this -> mPick) {
      return $this -> getPickLink($aDay);
    }
    $lRet = 'index.php?act='.$this -> mMod.'.day&amp;d='.$aDay;
    return $lRet;
  }

  protected function getPickLink($aDay) {
    $lDat = explode('-', $aDay);
    $lRet = 'javascript:Flow.Std.selCal(\'';
    $lRet.= $lDat[2].'.'.$lDat[1].'.'.$lDat[0];
    $lRet.= '\')';
    return $lRet;
  }

  protected function getDays() {
    $lRet = '';
    $lTim = $this -> mTim;
    for ($i=0; $i< 6; $i++) {
      $lRet.= '<tr>'.LF;
      // calendar week
      $lRet.= '<td class="th2 ac">';
      if ($this -> mSelectWeek) {
        $lD1 = date('Y-m-d', $lTim);
        $lD2 = date('Y-m-d', $lTim + 6 * 24 * 60 * 60);
        $lLnk = $this -> getLink($lD1, $lD2);
        $lRet.= '<a href="'.$lLnk.'" class="nav">';
        $lRet.= date('W', $lTim);
        $lRet.= '</a>';
      } else {
        $lRet.= date('W', $lTim);
      }
      $lRet.= '</td>'.LF;

      for ($j = 0; $j < 7; $j++) {
        $lDay = date('Y-m-d', $lTim);
        $lCls = 'td1';

        #$l_Dat = new CCor_Date($lDay);
        #echo $l_Dat -> getFmt(lan('lib.date.long')).' is '.$l_Dat -> getPeriodString(TRUE).BR;
        $lTime = time();
        $lDate = date("Y-m-d", $lTime);

        // weekends and holidays
        if ($j > 4) {
          $lCls = 'td2';
        } else if (isset($this -> mHol[$lDay])) {
          $lCls = 'td2';
        } else if ($lDay == $lDate) {
          $lCls = 'td5';
        }

        // selected?
        if ($this -> mMarkSelection) {
          if (empty($this -> mDate2)) {
            if ($lDay == $this -> mDate) {
              $lCls = 'act';
            }
          } else {
            if (($lDay >= $this -> mDate) and ($lDay <= $this -> mDate2)) {
              $lCls = 'act';
            }
          }
        }
        list($lY, $lM, $lD) = explode('-', $lDay);

        $lRet.= '<td class="'.$lCls.' ac">';

        // day link
        $lCls = 'nav';
        if (intval($lM) != $this -> mMon) {
          $lCls.= ' da';
        }
        $lLnk = $this -> getDayLink($lDay);

        $lRet.= '<a href="'.$lLnk.'" class="'.$lCls.'">';
        $lRet.= intval($lD);
        $lRet.= '</a>';

        $lRet.= '</td>'.LF;

        $lTim = mktime(0,0,0, intval($lM), intval($lD) + 1, intval($lY));
      }
      $lRet.= '</tr>'.LF;
      if (intval($lM) > $this -> mMon) {
        BREAK;
      }
      if (intval($lY) > $this -> mYear) {
        BREAK;
      }
    }
    return $lRet;
  }

  protected function getHolidays() {
    $lRet = '';
    $lRet.= '<tr>';
    $lRet.= '<td class="th2 ac" colspan="'.$this -> mColCnt.'">'.htm(lan('hol.menu')).'</td>';
    $lRet.= '</tr>';
    foreach ($this -> mHol as $lDay => $lName) {
      $lRet.= '<tr>'.LF;
      $lRet.= '<td class="td2 ac">';
      $lRet.= intval(substr($lDay,-2));
      $lRet.= '</td>';
      $lRet.= '<td class="td1" colspan="'.$this -> mColCnt.'">';
      $lRet.= htm($lName);
      $lRet.= '</td>';
      $lRet.= '</tr>'.LF;
    }
    return $lRet;
  }

}