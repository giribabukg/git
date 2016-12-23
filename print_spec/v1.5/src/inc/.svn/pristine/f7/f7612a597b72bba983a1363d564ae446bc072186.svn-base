<?php
class CInc_Job_Cos_List extends CHtm_List {

  public function __construct($aSrc, $aJobId, $aJob) {
    parent::__construct('job-'.$aSrc.'-cos');
    $this -> mTitle = lan('job-cos.menu');
    $this -> setAtt('class', 'tbl w100p');
    $this -> mJobId = $aJobId;
    $this -> mJob = $aJob;
    $this -> mStdLnk = '';

    $lUsr = CCor_Usr::getInstance();
    $this -> mCanEdit = $lUsr -> canEdit('job-cos');
    $this -> mShowSubHdr = $this -> mCanEdit;

    $this -> addCtr();
    if ($this -> mCanEdit)
    $this -> addColumn('artnr', 'ArtNo.', FALSE);
    $this -> addColumn('name', 'Article', FALSE, array('width' => '100%'));

    if ($this -> mCanEdit) {
      $this -> addColumn('amount', 'Amount', FALSE);
      $this -> addColumn('ppunit', 'P/U', FALSE);
    }
    $this -> addColumn('calc', 'Price', FALSE);
    if ($this -> mCanEdit) {
      $this -> addColumn('sbkostsb', 'SB', FALSE);
      $this -> addPanel('fix', $this -> getFixForm());
    }

    $lSes = CCor_Ses::getInstance();
    $this -> mTpl = $lSes['cos-tpl.'.$this -> mJobId];

    #$this -> mTpl = (empty($this -> mJob['cos_tpl'])) ? 1 : intval($this -> mJob['cos_tpl']);
    $this -> getPrices();

    $this -> mSum = 0;
  }

  protected function getCont() {
    $lRet = parent::getCont();
    if ($this -> mCanEdit) {
      $lRet = $this -> wrapForm($lRet);
    }
    return $lRet;
  }

  protected function wrapForm($aRet) {
    $lRet = '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mMod.'.scos" />'.LF;
    $lRet.= '<input type="hidden" name="jobid" value="'.htm($this -> mJobId).'" />'.LF;
    $lRet.= $aRet;
    $lRet.= '<div class="btnPnl">';
    $lRet.= btn('Save Costs', '', 'img/ico/16/ok.gif', 'submit');
    $lRet.= '</div>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getRow() {
    $lArt = $this -> getVal('artnr');
    if ($lArt < 0) {
      return $this -> getSumRow();
    }
    return parent::getRow();
  }

  protected function getFixForm() {
    $lRet = '';
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0" class="p16"><tr>'.LF;
    $lRet.= '<td class="nw b">Fixed Price</td>';
    $lRet.= '<td><input type="text" class="inp w70" /></td>';
    $lRet.= '<td class="nw b">&euro;</td>';
    $lRet.= '<td>'.btn(lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit').'</td>';
    $lRet.= '</tr></table>'.LF;
    return $lRet;
  }

  protected function getSumRow() {
    $lCol = 1;
    if ($this -> mCanEdit) {
      $lCol = 5;
    }
    $lArt = $this -> getVal('artnr');
    $lCls = 'th3 p2';
    if (-1 == $lArt) {
      $lCls = 'th2 p2';
    }
    $lRet = '<tr>';
    $lRet.= '<td class="'.$lCls.'" colspan="5">';
    $lRet.= htm($this -> getVal('name'));
    $lRet.= '</td>';

    $lRet.= '<td class="'.$lCls.' nw">';
    $lVal = $this -> getVal('price');
    $lVal = fmtCur(strToFloat($lVal));
    $lId = (-1 == $lArt) ? ' id="sum_prc"' : '';
    #$lRet.= '<input type="text" value="'.htm($lVal).'"'.$lId.' class="inp w70 ar b th2" disabled="disabled" />';
    $lRet.= '<div'.$lId.' class="inp w70 ar prcSum" >'.$lVal.'</div>';
    $lRet.= '</td>';
    if ($this -> mCanEdit) {
      $lVal = $this -> getVal('sbkostsb');
      $lRet.= '<td class="'.$lCls.' ar nw">';
      $lRet.= '<div class="inp w70 ar prcSum" >';
      $lRet.= fmtCur(strToFloat($lVal));
      $lRet.= '</div>';
      $lRet.= '</td>';
    }

    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function getPrices() {
    $this -> mIte = new CApi_Alink_Query_Getartcalc($this -> mJobId);
    $this -> dump($this -> mIte -> getArray());
  }

  protected function hideAmount($aArtNr) {
    $lArr = array('67900', '67801');
    return in_array($aArtNr, $lArr);
  }

  protected function getTdPpunit() {
    $lArt = $this -> getVal('artnr');
    $lVal = fmtCur(strToFloat($this -> getCurVal()));
    if ($this -> hideAmount($lArt)) {
      $lVal = '';
    }
    return $this -> tdClass($lVal, 'ar');
  }

  protected function getTdAmount() {
    $lArt = $this -> getVal('artnr');
    $lVal = $this -> getCurVal();
    $lArt = $this -> getVal('artnr');
    $lPpu = floatval($this -> getVal('ppunit'));
    $lFlag = $this -> getVal('tplflag');
    $lTxt = fmtCur(strToFloat($lVal));
    if (('tplvar' == $lFlag) and ($this -> mCanEdit)) {
      $lRet.= '<input type="hidden" name="old['.$lArt.']" value="'.$lTxt.'" />';
      $lRet.= '<input type="text" name="val['.$lArt.']" tabindex="1" value="'.$lTxt.'" class="inp w50 ar" onblur="Flow.Std.calc(this,\''.htm($lArt).'\','.$lPpu.')" />';
    } else {
      if (!$this -> hideAmount($lArt)) {
        $lRet = '<input type="text" disabled="disabled" value="'.$lTxt.'" class="inp w50 ar" />';
      }
      #$lRet = '<div class="ar">'.fmtCur($lVal).'</td>';
    }
    return $this -> td($lRet);
  }

  protected function getTdCalc() {
    $lArt = $this -> getVal('artnr');

    $lVal = $this -> getVal('price');
    $lVal = fmtCur(strToFloat($lVal));

    #$lRet = '<input type="text" id="p'.$lArt.'" value="'.htm($lVal).'" class="inp w70 ar prc" disabled="disabled" />&nbsp;&euro;';
    $lRet = '<div id="p'.$lArt.'" class="inp w70 ar prc" >'.$lVal.'</div>';
    return $this -> td($lRet);
  }

  protected function getTdSbkostsb() {
    return $this -> td('');
  }

}