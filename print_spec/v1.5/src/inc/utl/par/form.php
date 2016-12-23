<?php
class CInc_Utl_Par_Form extends CHtm_Form {

  public function __construct($aSel) {
    try {
      #$aSel = str_replace("\\", '' , $aSel);
      $this -> mSel = unserialize($aSel);
    } catch (Exception $e) {
      $this -> mSel = array();
    }
    $i = 0;
    if (!empty($this -> mSel)) {
      foreach ($this -> mSel as $lKey => $lVal) {
        $this -> setVal('key'.$i, $lKey);
        $this -> setVal('val'.$i, $lVal);
        $i++;
      }
    }
    parent::__construct('utl-par.pick', 'Parameters');
  }

  protected function getFieldForm() {
    $lRet = '<tr>'.LF;
    $lRet.= '<td></td>'.LF;
    $lRet.= '<td class="c b">Key</td>'.LF;
    $lRet.= '<td class="b">Value</td>'.LF;
    $lRet.= '</tr>'.LF;
    for ($i=0; $i<12; $i++) {
      $lRet.= '<tr>'.LF;
      $lRet.= '<td class="nw ar">'.($i + 1).'.</td>'.LF;
      $lRet.= '<td>'.LF;
      $lAli = 'key'.$i;
      $lDef = fie($lAli, '', 'string', '', array('class' => 'inp w70'));
      $lRet.= $this -> mFac -> getInput($lDef, $this -> getVal($lAli));
      $lRet.= '</td>'.LF;
      $lRet.= '<td>'.LF;
      $lAli = 'val'.$i;
      $lVal = $this -> getVal($lAli);
      if (is_array($lVal)) {
        $lDef = fie($lAli, '', 'params');
        $lVal = serialize($lVal);
        $lRet.= $this -> mFac -> getInput($lDef, $lVal);
      } else {
        $lDef = fie($lAli);
        $lRet.= $this -> mFac -> getInput($lDef, $lVal);
      }
      $lRet.= '</td>'.LF;
      $lRet.= '</tr>'.LF;
    }
    return $lRet;
  }

  protected function getButtons() {
    $lRet = '<div class="btnPnl">'.LF;
    $lRet.= btn(lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit').NB;
    if (!empty($this -> mCancel)) {
      $lRet.= btn(lan('lib.cancel'), "window.close()", 'img/ico/16/cancel.gif');
    }
    $lRet.= '</div>'.LF;
    return $lRet;
  }

}