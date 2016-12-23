<?php
class CInc_Utl_Jfp_Form extends CHtm_Form {

  public function __construct($aSel) {
    try {
      $aSel = str_replace("\\", '', $aSel);
      $this -> mSel = explode(',', $aSel);
      $this -> mSel = $this -> SwapKeysValues($this -> mSel);
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
    $this -> mFilter = array();
    parent::__construct('utl-jfp.pick', lan('lib.fie'));
  }

  public function addFilter($aField, $aValue) {
    $this -> mFilter[$aField] = $aValue;
  }

  protected function getFieldSelection() {
    $lRet = CCor_Res::extract('alias', 'name_'.LAN, 'fie', $this -> mFilter);
    return array('' => ' ') + $lRet;
  }

  protected function getFieldForm() {
    $lRet = '<tr>'.LF;
    $lRet.= '<td></td>'.LF;
    $lRet.= '<td class="b">Value</td>'.LF;
    $lRet.= '</tr>'.LF;

    $lArr = $this->getFieldSelection();

    for ($i = 0; $i < 30; $i++) {
      $lRet.= '<tr>'.LF;
      $lRet.= '<td class="nw ar">'.($i + 1).'.</td>'.LF;
      $lRet.= '<td>'.LF;
      $lAli = 'key'.$i;
      $lDef = fie($lAli, '', 'select', $lArr);
      $lRet.= $this -> mFac -> getInput($lDef, $this -> getVal($lAli));
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

  protected function SwapKeysValues($aArray) {
    $lValues = array();
    while (list($lKey, $lVal) = each($aArray))
        $lValues[$lVal] = $lKey;
    return $lValues;
  }

}