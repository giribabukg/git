<?php
class CInc_Job_Pro_Bar extends CJob_Bar {

  public $mJobId;
  protected $mProStatus = array();
  protected $mJobsAmount = 0;
  protected $mSubAmount = 0;
  protected $mSDisplay = 0;
  public $mViewJoblist = TRUE;

  public function __construct($aSrc, $aJob = NULL, $aCrp = 0) {
    parent::__construct($aSrc, $aJob, $aCrp);
    //22651 Project Critical Path Functionality
    $this -> mJobSrcOrder = CCor_Cfg::get('job.items.order', array());

    $lProCrp = CJob_Pro_Crp::getInstance(array($this -> mJobId));

    $this -> mProStatus = $lProCrp -> getProStatus($this -> mJobId);
    $this -> mProStatusAll = $lProCrp -> getProStatusAll($this -> mJobId); //with "afore & after" jobs
    $this -> mJobsAmount = $lProCrp -> getProjectsAmount($this -> mJobId);
    $this -> mSubAmount = $lProCrp -> getSubAmount($this -> mJobId);
    $this -> mAutoProStatus = $lProCrp -> getAutoProStatus($this -> mJobId);
    $this -> mStatusClosed = $lProCrp -> getStatusClosed($this -> mJobId);
    $this -> mViewJoblist = $lProCrp -> getViewJoblist();
    $this -> mNoStatusToStep = $lProCrp -> getNoStatusToStep();
    $this -> mNoStatusFromStep = $lProCrp -> getNoStatusFromStep();
    #$this -> m1StatusNoStep = $lProCrp -> get1StatusNoStep();
    #$this -> m1LastStatusNoStep = $this -> m1StatusNoStep - 1;
    $this -> m1LastStatusNoStep = $lProCrp -> get1StatusNoFromStep();

    $lCrpStatus = CCor_Res::extract('status', 'display', 'crp', $this -> mCrpId);
    #$this -> mDis = (isset($lInfo['pro_con']) ? $lCrpStatus[$this -> mSta] : 0);
    $this -> mDis = (isset($lCrpStatus[$this -> mSta]) ? $lCrpStatus[$this -> mSta] : 0);

    if (!$this -> mViewJoblist AND 0 < $this -> mDis) {
      if ($this -> mDis >= $this -> mAutoProStatus OR !$this -> mNoStatusFromStep[$this -> mDis]) {
        $this -> mDisplay = $this -> mDis;
      } else {
        $this -> mDisplay = $this -> mAutoProStatus;
      }
    } else {
      $this -> mDisplay = $this -> mDis;
    }
  }

  public function getAutoProStatus() {
    return $this -> mAutoProStatus;
  }

  protected function getDates() {
    $this -> mTim = array();
    $lQry = new CCor_Qry('SELECT * FROM `al_job_pro_'.intval(MID).'` WHERE `id`='.esc($this -> mJobId));
    if ($lRow = $lQry -> getDat()) {
      $this -> mTim = $lRow;
    }
  }

//   protected function getSubState($aRec) {
//     $lPro = $this -> mJobId;
//     $lDis = $aRec['display'];
//     $lStatus = $aRec['status'];

//     if ($lDis == $this -> mDisplay) {
//       $lRet = '<td class="bar2">';
//     } else {
//       $lRet = '<td class="bar1">';
//     }

//     $lSta = $this -> mProStatus[$lDis];
//     $lStaAll = $this -> mProStatusAll[$lDis];

//     if ($lDis <= $this -> mDisplay OR $lDis == $this -> mAutoProStatus) {
//       $lImg = 'b';
//     } elseif (0 < $lSta['count'] OR ( in_array($lDis, $this -> mStatusClosed['pro']) AND (!$lStaAll['afore'] OR $lStaAll['after']) )) {
//       $lImg = 'h';
//     } else {
//       $lImg = 'l';
//     }
//     $lBorder = '';
//     $lRet.= img('img/crp/'.$lDis.$lImg.'.gif',  array('style' => $lBorder));
//     $lRet.= '</td>';
//     return $lRet;
//   }

  protected function getJobId(){
    $lRet = '';
    $lRet = $this -> mJob['id'];
    return $lRet;
  }

  protected function getCont() {
    if ($this -> mViewJoblist OR (empty($this -> mProStatus) AND 0 == $this -> mSubAmount)) {
      $lRet = parent::getCont();
    } else {
      $lRet = $this -> getContCrp();
    }
    return $lRet;
  }

  protected function getContCrp() {
    $lRet = '';
    $lFla = $this -> getFlagIcons();

    if (THEME === 'default') {
      if (!empty($lFla)) {
        $lRet = '<table cellpadding="0" cellspacing="0" border="0" class="tbl"><tr><td class="frm p8" valign="top">';
        $lRet.= $lFla;
        $lRet.= '</td><td valign="top">';
        $lRet.= '<table cellpadding="2" cellspacing="0">';
      } else {
        $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl">';
      }

      $lRet.= $this -> getImageRow();
      $lRet.= $this -> getNumberIconRow();
      $lRet.= $this -> getSubJobRow();
      $lRet.= $this -> getDeadlinesRow();
      $lRet.= $this -> getActualTimingsRow();

      $lRet.= '</table>';

      if (!empty($lFla)) {
        $lRet.= '</td></tr></table>';
      }
    } else {
      $lRet .= '<table cellpadding="0" cellspacing="0" border="0" class="tbl" width="100%">';
      $lRet .= $this -> getNumberIconRow();
      $lRet .= $this -> getCurrentStatus();
      if (!empty($lFla)) {
        $lRet .= '<tr><td class="frm p8" valign="top" colspan="'.(sizeof($this -> mCrp) * 2).'">'.$lFla.'</td></tr>';
      }
      $lRet .= '</table>';
    }

    return $lRet;
  }

  protected function getImageRow() {
    $lRet = '<tr>';

    if ($this -> mShowCaptions) {
      $lRet.= '<td class="bar1">&nbsp;</td>';
    }

    if (THEME === 'default') {
      $lRet.= '<td class="bar1">&nbsp;</td>';
    }

    foreach ($this -> mCrp as $lSta) {
      $lRet.= $this -> getImgTd($lSta);
    }

    $lRet.= '</tr>';
    return $lRet;
  }

  protected function getNumberIconRow() {
    $lRet = (THEME === 'default' ? '<tr>' : '<tr class="status_icons">');
    if ($this -> mShowCaptions && THEME === 'default') {
      $lRet.= '<td class="bar1">';
      $lRet.= htm(lan('lib.webstatus'));
      $lRet.= '</td>';
    }

    if (THEME === 'default') {
      $lRet.= '<td class="bar1">&nbsp;</td>';
    }

    foreach ($this -> mCrp as $lSta) {
      $lRet.= (THEME === 'default' ? $this -> getIcoTd($lSta) : $this -> getWave8IcoTd($lSta));
    }

    $lRet.= '</tr>';
    return $lRet;
  }

  protected function getSubJobRow() {
    // 3. Zeile: Wieviele Jobs befinden sich in dem zugeordneten Status
    $lRet = '<tr>';
    if ($this->mShowCaptions) {
      $lRet.= '<td class="bar1">';
      $lRet.= htm(lan('job-sub.menu'));
      $lRet.= '</td>';
    }
    $lRet.= '<td class="bar2">'.$this -> mSubAmount.'/'.$this -> mJobsAmount.'</td>';
    if (!empty($this -> mProStatus)) {
      foreach ($this -> mProStatus as $lDis => $lSta) {
        // highlight current status
        $lStatus = $this -> mCrp[$lDis]['status'];
        if ($lDis == $this -> mDisplay) {
          $lRet.= '<td class="bar2">';
        } else {
          $lRet.= '<td class="bar1">';
        }
        $lStaAll = $this -> mProStatusAll[$lDis];
        if (0 < $lSta['count']) {
          $lRet.= $lSta['count'];
        } elseif ( in_array($lDis, $this -> mStatusClosed['pro']) AND (!$lStaAll['afore'] OR $lStaAll['after']) ) {
          $lRet.= '-';
        } else {
          $lRet.= '&nbsp;';
        }
        $lRet.= '</td>';
      }
    } else {
      foreach ($this -> mCrp as $lSta) {
        $lStatus = $lSta['status'];
        if ($lStatus == $this -> mSta) {
          // highlight current status
          $lRet.= '<td class="bar2">';
        } else {
          $lRet.= '<td class="bar1">';
        }
        $lRet.= '&nbsp;</td>';
      }
    }
    $lRet.= '</tr>';
    return $lRet;
  }

  protected function getDeadlinesRow() {
    $lObj = new CCor_Date();
    $lFmt = lan('lib.datetime.md');

    $lRet = '<tr>';

    if ($this -> mShowCaptions) {
      $lRet.= '<td class="bar1">';
      $lRet.= htm(lan('job.bar.deadline'));
      $lRet.= '</td>';
    }

    if (THEME === 'default') {
      $lRet.= '<td class="bar1">&nbsp;</td>';
    }

    foreach ($this -> mCrp as $lSta) {
      $lDis = $lSta['display'];
      $lSta = $lSta['status'];
      if ($lSta == $this -> mSta) {
        $lRet.= '<td class="bar2">';
      } else {
        $lRet.= '<td class="bar1">';
      }
      $lTim = (isset($this -> mDdl[$lDis])) ? $this -> mDdl[$lDis] : '';
      $lObj -> setSql($lTim);
      $lTxt = $lObj -> getFmt($lFmt);
      if (empty($lTxt)) {
        $lRet.= NB;
      } else {
        $lRet.= $lTxt;
      }
      $lRet.= '</td>';
    }

    $lRet.= '</tr>';
    return $lRet;
  }

  protected function getActualTimingsRow() {
    // 5. Zeile: Wann hat der letzte Job diesen Status verlassen?
    $lObj = new CCor_Date();
    $lRet = '<tr>';
    if ($this -> mShowCaptions) {
      $lRet.= '<td class="bar1">';
      $lRet.= htm(lan('job.bar.actual'));
      $lRet.= '</td>';
    }

    if (THEME === 'default') {
      $lRet.= '<td class="bar1">&nbsp;</td>';
    }

    if (!empty($this -> mProStatus)) {
      foreach ($this -> mProStatus as $lDis => $lSta) {
        // highlight current status
        $lStatus = $this -> mCrp[$lDis]['status'];
        if ($lDis == $this -> mDisplay) {
          $lRet.= '<td class="bar2">';
        } elseif (1 < $lDis AND !$lSta['used'] AND $lStatus <= $this -> mSta) {
          // grey status above current status
          $lRet.= '<td class="bar1 da">';// Datum in grauer Schrift
        } else {
          $lRet.= '<td class="bar1">';
        }
        $lTim = (1== $lDis AND isset($this -> mTim['lti_'.$lDis])) ? $this -> mTim['lti_'.$lDis] : ''; // setzt das Erstellungs-Datum
        $lStaAll = $this -> mProStatusAll[$lDis];
        if ( in_array($lDis, $this -> mStatusClosed['pro']) AND (!$lStaAll['afore'] OR $lStaAll['after']) ) {//old: $lSta['pass'] AND 0 == $lSta['count']
          $lTim = $lSta['date'];
        } elseif ($lStatus <= $this -> mSta) {//!$lSta['used'] AND $lStatus <= $this -> mSta
          $lTim = (isset($this -> mTim['lti_'.$lDis])) ? $this -> mTim['lti_'.$lDis] : '';
        }
        $lObj -> setSql($lTim);
        $lTxt = $lObj -> getFmt(lan('lib.datetime.md'));
        if (empty($lTxt)) {
          $lRet.= NB;
        } else {
          $lRet.= $lTxt;
        }
        $lRet.= '</td>';
      }
    } else {
      if ('FROM' == CCor_Cfg::get('ddl.view', 'TO')) {
        $lView = true;
      } else {
        $lView = false;
      }
      foreach ($this -> mCrp as $lSta) {
        $lDis = $lSta['display'];
        $lStatus = $lSta['status'];
        if ($lStatus == $this -> mSta) {
          // highlight current status
          $lRet.= '<td class="bar2'.($lView ? ' da' : '').'">';
        } else {
          if ($lSta > $this -> mSta) {
            // grey status above current status
            $lRet.= '<td class="bar1 da">';
          } else {
            $lRet.= '<td class="bar1">';
          }
        }
        $lTim = (isset($this -> mTim['lti_'.$lDis])) ? $this -> mTim['lti_'.$lDis] : '';
        $lObj -> setSql($lTim);
        $lTxt = $lObj -> getFmt(lan('lib.datetime.md'));
        if (empty($lTxt)) {
          $lRet.= NB;
        } else {
          $lRet.= $lTxt;
        }
        $lRet.= '</td>';
      }
    }
    $lRet.= '</tr>';
    $lRet.= '</table>';

    if (!empty($lFla)) {
      $lRet.= '</td></tr></table>';
    }
    return $lRet;
  }
}