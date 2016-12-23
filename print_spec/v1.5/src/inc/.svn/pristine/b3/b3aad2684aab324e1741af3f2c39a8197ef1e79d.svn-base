<?php
class CInc_Job_Pro_Sub_Wiz extends CHtm_Form {

  public function __construct($aJobId, $aStep, $aWizId, $aData) {
    $this -> mJobId = intval($aJobId);
    $this -> mStep  = intval($aStep);
    $this -> mWid   = intval($aWizId);
    $this -> mDat   = $aData;
    $this -> dump($aData, 'DATA');

    $lCap = CCor_Qry::getStr('SELECT name_'.LAN.' FROM al_wiz_master WHERE id='.$this -> mWid);
    parent::__construct('job-pro-sub.wiznext', 'Wizard '.$lCap, 'job-pro-sub&jobid='.$this -> mJobId);
    $this -> setAtt('class', 'tbl w100p');

    $this -> setParam('jobid', $this -> mJobId);
    $this -> setParam('step',  $this -> mStep);
    $this -> setParam('wiz',   $this -> mWid);

    $this -> getWizard();
  }

  protected function getFac() {
    if (isset($this -> mFac)) {
      return;
    }
    $this -> mFac = new CJob_Pro_Sub_Wizfac();
  }

  function getWizard() {
    $lSql = 'SELECT COUNT(*) AS steps FROM al_wiz_items WHERE mand='.intval(MID).' AND wiz_id='.$this -> mWid;
    $this -> mMaxStep = CCor_Qry::getInt($lSql);
    $this -> dbg('Max '.$this -> mMaxStep.' Step '.$this -> mStep);

    $lFie = CCor_Res::get('fie');
    $lSql = 'SELECT mainfield_id,secondary_fields FROM al_wiz_items WHERE 1 AND mand='.intval(MID);
    $lSql.= ' AND wiz_id='.$this -> mWid.' ';
    $lSql.= ' AND hierarchy='.$this -> mStep.' ';
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry -> getDat();

    $lMain = $lRow['mainfield_id'];
    if (isset($lFie[$lMain])) {
      $this -> addDef($lFie[$lMain]);
    }
    $lSec = $lRow['secondary_fields'];
    if (!empty($lSec)) {
      $lArr = explode(',', $lSec);
      foreach ($lArr as $lFid) {
        if (isset($lFie[$lFid])) {
          $this -> addDef($lFie[$lFid]);
        }
      }
    }
  }

  protected function getIndexValue($aAlias, $aIndex) {
    $lRet = '';
    if (isset($this -> mDat[$this -> mStep][$aIndex][$aAlias])) {
      $lRet = $this -> mDat[$this -> mStep][$aIndex][$aAlias];
    }
    return $lRet;
  }

  protected function getForm() {
    $lRet = '';
    $lRet.= '<div class="frm" style="padding:16px;">'.LF;
    for ($i=1; $i < 11; $i++) {
      $lRet.= '<span class="cap w50 p4 ac" style="float:left; margin-right:16px;">'.$i.'.</span>';

      $lRet.= '<table cellpadding="4" cellspacing="0" border="0" class="box p8">'.LF;

      $this -> mFac -> setIndex($i);
      $lRet.= $this -> getFieldForm();

      $lRet.= '</table>'.LF;
      $lRet.= '<br style="clear:both" />'.LF;
    }
    $lRet.= '</div>';
    return $lRet;
  }

  protected function getFieldForm() {
    $lRet = '';
    if (!empty($this -> mFie)) {
      foreach ($this -> mFie as $lAlias => $lDef) {
        $lRet.= '<tr>'.LF;
        $lRet.= '<td class="nw">'.htm($lDef['name_'.LAN]).'</td>'.LF;
        $lRet.= '<td>'.LF;
        $lRet.= $this -> mFac -> getInput($lDef, $this -> getVal($lAlias));
        $lRet.= '</td>'.LF;
        $lRet.= '</tr>'.LF;
      }
    }
    return $lRet;
  }

  protected function getButtons() {
    $lRet = '<div class="btnPnl">'.LF;
    if ($this -> mStep > 0) {
      $lRet.= btn('Back', "setAct(this,'job-pro-sub.wizprev')", 'img/ico/16/nav-prev-lo.gif');
      $lRet.= NB;
    }
    if ($this -> mStep < $this -> mMaxStep -1) {
      $lRet.= btn('Next', "setAct(this,'job-pro-sub.wiznext')", 'img/ico/16/nav-next-lo.gif');
      $lRet.= NB;
    } else {
      $lRet.= btn('Finish', "setAct(this,'job-pro-sub.wizfinish')", 'img/ico/16/nav-next-lo.gif');
      $lRet.= NB;
    }
    $lRet.= btn(lan('lib.cancel'), "go('index.php?act=job-pro-sub&jobid=".$this -> mJobId."')", 'img/ico/16/cancel.gif');

    $lRet.= '</div>'.LF;
    return $lRet;
  }

}