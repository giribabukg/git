<?php
class CInc_Job_Fil_Form extends CHtm_Form {

  public function __construct($aSrc, $aJobId, $aSubpath = '', $aDiv = '') {
    parent::__construct('job-fil.supload', lan('lib.upload').' '.lan('job-'.$aSrc.'.menu').' '.lan('lib.file').' '.jid($aJobId, TRUE));

    $this -> addDef(fie('file', lan('lib.file'), 'file'));

    $this -> mSrc = $aSrc;
    $this -> mJid = $aJobId;
    $this -> mSub = $aSubpath;
    $this -> mDiv = $aDiv;

    $this -> setParam('src', $aSrc);
    $this -> setParam('jobid', $aJobId);
    $this -> setParam('sub', $aSubpath);
    $this -> setParam('div', $aDiv);
    $this -> setFormTag($this -> getAjaxFormTag());
    $this -> mCat = $this -> getCatArray();
  }

  protected function getAjaxFormTag() {
    $this -> mTarget = getNum($this -> mDiv);
    $lRet = '<iframe id="'.$this -> mTarget.'" style="display:none" name="'.$this -> mTarget.'"></iframe>';
    $lRet.= '<form id="'.$this -> mFrmId.'" action="index.php" method="post" enctype="multipart/form-data" target="'.$this -> mTarget.'">';
    return $lRet;
  }

  protected function getCatArray() {
    $lRet = array();
    $lQry = new CCor_Qry('SELECT id,value_'.LAN.' as value_en FROM al_htb_itm WHERE mand IN('.MID.',0) AND domain=\'fil\' ORDER BY value');
    foreach ($lQry as $lRow) {
      $lRet[$lRow -> id] = $lRow -> value_en;
    }
    return $lRet;
  }

  protected function getFieldForm() {
    $lRet = '';
    if (!empty($this -> mCat)) {
      $lRet.= '<tr>'.LF;
      $lRet.= '<td class="nw">'.htm(lan('lib.cat')).'</td>'.LF;
      $lRet.= '<td>';
      $lSel = new CHtm_Select('category', $this -> mCat);
      $lRet.= $lSel -> getContent();
      $lRet.= '</td>';
      $lRet.= '</tr>'.LF;
    }
    $lRet.= '<tr>'.LF;
    $lRet.= '<td class="nw">'.htm(lan('lib.file')).'</td>'.LF;
    $lRet.= '<td>'.LF;
    $lRet.= '<input type="file" name="file" />';
    $lRet.= '</td>'.LF;
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function getButtons() {
    $lParams = array(
      'act' => 'job-'.$this -> mSrc.'-fil.get',
      'src' => $this -> mSrc,
      'jid' => $this -> mJid,
      'sub' => $this -> mSub,
      'div' => $this -> mDiv,
      'age' => 'job',
      'loading_screen' => TRUE
    );
    $lParamsJSONEnc = json_encode($lParams);
    $lJs = 'Flow.Std.ajxUpd('.$lParamsJSONEnc.');';

    $lRet = '<div class="btnPnl">'.LF;
    $lRet.= btn(lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit').NB;
    if (!empty($this -> mCancel)) {
      $lRet.= btn(lan('lib.cancel'), $lJs, 'img/ico/16/cancel.gif');
    }
    $lRet.= '</div>'.LF;
    return $lRet;
  }
}