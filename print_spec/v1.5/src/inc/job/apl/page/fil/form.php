<?php
class CInc_Job_Apl_Page_Fil_Form extends CHtm_Form {

  public function __construct($aSrc, $aJobId, $aSubpath = '', $aDiv = '', $aFid = '') {
    parent::__construct('job-apl-page-fil.supload', lan('lib.upload').' '.lan('job-'.$aSrc.'.menu').' '.lan('lib.file').' '.jid($aJobId, TRUE));
    $this -> addDef(fie('file',lan('lib.file'),'file'));

    $this -> mSrc = $aSrc;
    $this -> mJid = $aJobId;
    $this -> mSub = $aSubpath;
    $this -> mDiv = $aDiv;
    $this -> mTyp = 'apl';

    $this -> setParam('src', $aSrc);
    $this -> setParam('jobid', $aJobId);
    $this -> setParam('sub', $aSubpath);
    $this -> setParam('div', $aDiv);
    $this -> setParam('typ', $this -> mTyp);
    $this -> setParam('uid', CCor_Usr::getAuthId());
    $this -> setParam('fid', $aFid);
    $this -> setFormTag($this -> getAjaxFormTag());
  }

  protected function xxxgetEndTag() {
    return '';
  }

  protected function getAjaxFormTag() {
    $this -> mTarget = getNum($this -> mDiv); // ensure unique id
    $lRet = '<iframe id="'.$this -> mTarget.'" style="display:none" name="'.$this -> mTarget.'"></iframe>';
    $lRet.= '<form id="'.$this -> mFrmId.'" action="index.php" method="post" enctype="multipart/form-data" target="'.$this -> mTarget.'">';
    return $lRet;
  }

  protected function getFieldForm() {
    $lRet = '';
    $lRet.= '<tr>'.LF;
    $lRet.= '<td class="nw">'.htm(lan('lib.file')).'</td>'.LF;
    $lRet.= '<td>'.LF;
    $lRet.= '<input type="file" name="file" />';
    $lRet.= '</td>'.LF;
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function getButtons() {
    $lRet = '<div class="btnPnl">'.LF;
    $lRet.= btn(lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit').NB;
    if (!empty($this -> mCancel)) {
      $lUrl = 'job-apl-page-fil.get';
      $lRet.= btn(lan('lib.cancel'), "Flow.Std.ajxAplPageFil('".$this -> mDiv."','".$this -> mSrc."','".$this -> mJid."','".$lUrl."','".$this -> mTyp."','".$this -> mSub."')", 'img/ico/16/cancel.gif');
    }
    $lRet.= '</div>'.LF;
    return $lRet;
  }
}