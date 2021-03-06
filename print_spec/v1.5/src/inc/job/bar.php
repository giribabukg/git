<?php
/**
 * @copyright 5Flow GmbH
 * @version $Rev: 12100 $
 * @date $Date: 2016-01-18 17:10:48 +0800 (Mon, 18 Jan 2016) $
 * @author $Author: pdohmen $
 */
class CInc_Job_Bar extends CCor_Ren {

  public $mJobId;
  protected $mCrpFlags = array();
  protected $mShowCaptions = false;

  public function __construct($aSrc, $aJob = NULL, $aCrp = 0) {
    $this -> mSrc = $aSrc;
    $this -> mJob = $aJob;
    $this -> mJobId = $this -> getJobId();
    $this -> mFlags = 0;
    $this -> mCrpId = $aCrp;
    $this -> mCrp = array();

    $lCrp = CCor_Res::get('crp', intval($this -> mCrpId));
    foreach ($lCrp as $lSta) {
      $this -> mCrp[ $lSta['display'] ] = $lSta;
    }
    $this -> mSta = (isset($aJob['webstatus'])) ? $aJob['webstatus'] : 0;
    $this -> getDates();
    $this -> getDeadlines();
    $this -> getCrpFlags();

    $lUsr = CCor_Usr::getInstance();
    $this -> mTip = ($lUsr -> getPref('job.bartips', 'Y') == 'Y');
    $this -> mShowCaptions = CCor_Cfg::get('job-bar.captions', false);
  }

  protected function getDeadlines() {
    $this -> mDdl = array();

    $lSql = "SELECT * from al_crp_ddl d left join al_fie f on d.fie_id=f.id  left join al_crp_status s on d.status_id=s.id  where d.crp_id = '".$this -> mCrpId."' and d.mand='".MID."' and d.fie_id >0";
    $lRow = new CCor_Qry($lSql);
    foreach($lRow as $row)
    {
      $this -> setDdl($row['display'], $this -> mJob[$row['alias']]);

    }
    // array mit deadlines aus der db holen
    // ueber die deadlines iterieren
    // und setDdl(status, $this -> mTim[alias]
  }

  public function setDdl($aStatus, $aDeadLine) {
    $this -> mDdl[$aStatus] = $aDeadLine;
  }

  protected function getDates() {
    $this -> mTim = array();
    $lQry = new CCor_Qry('SELECT * FROM al_job_shadow_'.intval(MID).' WHERE jobid='.esc($this -> mJobId));
    if ($lRow = $lQry -> getDat()) {
      $this -> mTim = $lRow;
      $this -> mFlags = $lRow['flags'];
    }
  }

  protected function getCrpFlags() {
    $this -> mCrpFlags = CJob_Cnt::isFlagConfirmed($this -> mJob);
  }

  protected function getImgTd($aRec) {
    $lDis = $aRec['display'];
    $lFnc = 'getImg'.$lDis;
    if ($this -> hasMethod($lFnc)) {
      return $this -> $lFnc($aRec);
    }
    if ($aRec['status'] == $this -> mSta) {
	  $lImg = CApp_Crpimage::getSrcPath($this->mSrc, 'img/crp/big/'.$aRec['img'].'.gif');
    } else {
	  $lImg = CApp_Crpimage::getSrcPath($this->mSrc, 'img/crp/big/'.$aRec['img'].'h.gif');
    }
    $lRet = $this -> getBaseImg($aRec['id'], $lImg);
    return $lRet;
  }

  protected function getBaseImg($aId, $aImg) {
    $lRet = (THEME === 'default' ? '<td class="bar1">' : '<div class="fl">');
    $lDiv = getNum('b');
    $lAtt = array();
    if ($this -> mTip) {
      $lAtt['onmouseover'] = 'Flow.crpTip(this,'.$aId.',false)';
      $lAtt['onmouseout']  = 'Flow.hideTip();';
    }
    $lAtt['id'] = $lDiv;
    $lRet.= img($aImg, $lAtt);
    $lRet.= (THEME === 'default' ? '</td>' : '</div>');
    return $lRet;
  }

  protected function getIcoTd($aRec) {
    $lDis = $aRec['display'];
    //22651 Project Critical Path Functionality
    $lFnc = 'getSubState';
    if (!empty($this -> mProStatus) AND $this -> hasMethod($lFnc)) {
      return $this -> $lFnc($aRec);
    }
    $lSta = $aRec['status'];
    if ($lSta == $this -> mSta) {
      $lRet = '<td class="bar2">';
    } else {
      $lRet = '<td class="bar1">';
    }
    if ($lSta <= $this -> mSta) {
      $lPath = CApp_Crpimage::getSrcPath($this->mSrc, 'img/crp/'.$aRec['display'].'b.gif');
      $lRet.= img($lPath);
    } else {
      $lPath = CApp_Crpimage::getSrcPath($this->mSrc, 'img/crp/'.$aRec['display'].'l.gif');
      $lRet.= img($lPath);
    }
    $lRet.= '</td>';

    return $lRet;
  }

  protected function getWave8IcoTd($aRec) {
    $lDiv = getNum('b');

    $lObj = new CCor_Date();
    $lFmt = lan('lib.datetime.md');

    $lDis = $aRec['display'];
    $lDisImg = $aRec['img'];
    // 22651 Project Critical Path Functionality
    $lFnc = 'getSubState';
    if(!empty($this->mProStatus) and $this->hasMethod($lFnc)){return $this->$lFnc($aRec);}
    $lSta = $aRec['status'];

    $lCls = "al";
    $lAtt = array('style' => 'width:13px;');
    $lAttDdl = array('style' => 'width:13px;margin: 3px 0 -3px 0;');
    $lTimings = '';
    if($lSta == $this -> mSta){
      $lPath = CApp_Crpimage::getSrcPath($this->mSrc, 'img/crp/'.$lDis.'b.gif');
      $lIco = $lDis."b";
    } else {
      $lPath = CApp_Crpimage::getSrcPath($this->mSrc, 'img/crp/'.$lDis.'l.gif');
      $lIco = $lDis."l";
    }
    $lTimings .= img($lPath, $lAtt) . "<br/>";

    $lTitle = $aRec['name_'.LAN];
    //deadline
    $lTim = (isset($this->mDdl[$lDis])) ? $this->mDdl[$lDis] : '';
    $lObj->setSql($lTim);
    $lTxt = $lObj->getFmt($lFmt);
    $lTimings .= img('img/crp/ddl.png', $lAttDdl) . "&nbsp;";
    $lTimings .= (!empty($lTxt) ? $lTxt : "&nbsp;") . "<br/>";

    //actual
    $lTim = (isset($this->mTim['lti_' . $lDis])) ? $this->mTim['lti_' . $lDis] : '';
    $lObj->setSql($lTim);
    $lTxt = $lObj->getFmt($lFmt);
    $lTimings .= img('img/crp/atl.png', $lAtt) . "&nbsp;";
    $lTimings .= (!empty($lTxt) ? $lTxt : "&nbsp;") . "<br/>";

    $lAtt = array();
    if ($this -> mTip) {
      $lAtt['onmouseover'] = 'Flow.crpTip(this,'.$aRec['id'].',false);';
      $lAtt['onmouseout']  = 'Flow.hideTip();';
    }
    $lAtt['id'] = $lDiv;

    $lRet = '<td class="'.$lCls.' w45">';
    $lImg = ($lSta == $this -> mSta ? 'img/crp/big/'.$lDisImg.'.gif' : 'img/crp/big/'.$lDisImg.'h.gif');

	$lPath = CApp_Crpimage::getSrcPath($this->mSrc, $lImg);
    $lRet .= img($lPath, $lAtt) . '</td>';

    $lRet .= '<td class="'.$lCls.' w100">' . $lTimings . '</td>';

    $lRet .= '<td>&nbsp;</td>';

    return $lRet;
  }

  protected function getFlagIcons() {
    if (empty($this -> mFlags) AND empty($this -> mCrpFlags)) return '';
    $lRet = '';
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0">';
    $lJfl = CCor_Res::extract('val', 'name_'.LAN, 'jfl');
    foreach ($lJfl as $lBit => $lNam) {
      if (bitset($this -> mFlags, $lBit)) {
        $lRet.= '<tr>';
        $lRet.= '<td class="w16">';
        $lRet.= img('img/jfl/'.$lBit.'.gif');
        $lRet.= '</td>';
        $lRet.= '<td class="nw">';
        $lRet.= htm($lNam);
        $lRet.= '</td>';
        $lRet.= '</tr>';
      }
    }
    if (!empty($this -> mCrpFlags)) {
      $lAllFlags = CCor_Res::get('fla');
      foreach ($this -> mCrpFlags as $lFlagId => $lIsConfirmed) {
        if (isset($lAllFlags[$lFlagId])) {
          $lFlagEve = $lAllFlags[$lFlagId];
          $lRet.= '<tr>';
          $lRet.= '<td class="w16">';
          $lNr = ($lIsConfirmed ? flEve_conf : flEve_act) ;
          $lImg = $lFlagEve['eve_'.$lNr.'_ico'];
          $lRet.= img('img/flag/'.$lImg.'.gif');
          $lRet.= '</td>';
          $lRet.= '<td class="nw">';
          $lRet.= htm($lFlagEve['name_'.LAN]);
          $lRet.= '</td>';
          $lRet.= '</tr>';
        }
      }
    }
    $lRet.= '</table>';
    return $lRet;
  }

  protected function getCont() {
    $lRet = '';
    $lFla = $this -> getFlagIcons();

    if(THEME === 'default'){
      if (!empty($lFla)) {
        $lRet = '<table cellpadding="0" cellspacing="0" border="0" class="tbl"><tr><td class="frm p8" valign="top">';
        $lRet.= $lFla;
        $lRet.= '</td><td valign="top">';
        $lRet.= '<table cellpadding="2" cellspacing="0">';
      } else {
        $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl">';
      }

      $lRet.= $this->getImageRow();
      $lRet.= $this->getNumberIconRow();
      $lRet.= $this->getDeadlinesRow();
      $lRet.= $this->getActualTimingsRow();

      $lRet.= '</table>';

      if (!empty($lFla)) {
        $lRet.= '</td></tr></table>';
      }
    } else {
      $lRet .= '<table cellpadding="0" cellspacing="0" border="0" class="tbl" width="100%">';
      $lRet .= $this->getNumberIconRow();
      $lRet .= $this->getCurrentStatus();
      if(!empty($lFla)){
        $lRet .= '<tr><td class="frm p8" valign="top" colspan="' . (sizeof($this->mCrp) * 2) . '">' . $lFla . '</td></tr>';
      }

      $lRet .= '</table>';
    }

    return $lRet;
  }

  protected function getImageRow() {
    $lRet = '<tr>';
    if ($this->mShowCaptions) {
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
    if ($this->mShowCaptions && THEME === 'default') {
      $lRet.= '<td class="bar1">';
      $lRet.= htm(lan('lib.webstatus'));
      $lRet.= '</td>';
    }
    foreach ($this -> mCrp as $lSta) {
      $lRet.= (THEME === 'default' ? $this -> getIcoTd($lSta) : $this -> getWave8IcoTd($lSta));
    }
    $lRet.= '</tr>';
    return $lRet;
  }

  protected function getCurrentStatus() {
    $lRet = '<tr><td class="full_status" colspan="'.(sizeof($this -> mCrp) * 3).'">&nbsp;</td></tr>';
    $lRet .= '<tr class="current">';

    $lSize = sizeof($this -> mCrp);
    foreach ($this -> mCrp as $lSta) {
      $lDis = $lSta['display'];
      $lSta = $lSta['status'];

      $lNotHalf = array("10","200", $lSize);
      $lCls = CApp_Crpimage::getColourForSrc($this->mSrc);
      if ($lSta < $this -> mSta) {
        $lRet.= '<td colspan="3" class="'.$lCls.'">';
      } else if($lSta == $this -> mSta){
        $lRet.= '<td class="'.$lCls.'">&nbsp;</td><td>';
      } else {
        $lRet.= '<td colspan="3">';
      }

      $lRet.= '&nbsp;</td>';
    }
    $lRet.= '</tr>';

    return $lRet;
  }

  protected function getDeadlinesRow() {
    $lObj = new CCor_Date();
    $lFmt = lan('lib.datetime.md');

    $lRet = '<tr>';

    if ($this->mShowCaptions) {
      $lRet.= '<td class="bar1">';
      $lRet.= htm(lan('job.bar.deadline'));
      $lRet.= '</td>';
    }

    foreach ($this -> mCrp as $lSta) {
      $lDis = $lSta['display'];
      $lSta = $lSta['status'];
      if ($lSta == $this -> mSta) {
        // highlight current status
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
    $lObj = new CCor_Date();
    $lFmt = lan('lib.datetime.md');

    $lRet = '<tr>';

    if ($this->mShowCaptions) {
      $lRet.= '<td class="bar1">';
      $lRet.= htm(lan('job.bar.actual'));
      $lRet.= '</td>';
    }

    if ('FROM' == CCor_Cfg::get('ddl.view', 'TO')) {
      $lView = true;
    } else {
      $lView = false;
    }
    foreach ($this -> mCrp as $lSta) {
      $lDis = $lSta['display'];
      $lSta = $lSta['status'];
      if ($lSta == $this -> mSta) {
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

  protected function getJobId(){
    return $this -> mJob['jobid'];
  }

}