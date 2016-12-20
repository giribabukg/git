<?php
class CInc_Htb_Itm_Batch_List extends CCor_Ren {

  protected $mArr;
  protected $mOldArr;
  protected $mCaption;
  protected $mAct;
  protected $mCancel;
  protected $mHidden = array();

  public function __construct($aAct, $aCancel, $aReq, $aCaption, $aHidden = array()) {
    $this->mVal = $aReq->getVal("val");
    $this->mDom = $aReq->getVal("dom");

    //Generate Array (Lines with duplicate or empty keys will be ignored)
    $lBatchData = array();
    $lLines = explode("\n", $this->mVal["batch"]);
    $i=1;
    foreach ($lLines as $line) {
      list($key, $value_de, $value_en) = explode(";", $line);
      if(trim($key) !== "" && !isset($lBatchData[$key]["num"])) {
        $lBatchData[$key]["num"] = $i;
        $lBatchData[$key]["key"] = trim($key);
        $lBatchData[$key]["de"]  = trim($value_de);
        $lBatchData[$key]["en"]  = trim($value_en);
        $i++;
      }
    }

    //Alte Einträge laden
    $lDb = new CCor_Qry("SELECT * FROM al_htb_itm WHERE mand = '". MID . "' AND domain = '" . $this->mDom . "'");
    $this ->mOldArr = $lDb->getAssocs("value");

    $this -> mArr = $lBatchData;
    $this -> mCaption = $aCaption;
    $this -> mAction = $aAct;
    $this -> mCancel = $aCancel;
    $this -> mHidden = $aHidden;
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mAction.'" />'.LF;
    $lRet.= '<input type="hidden" name="dom" value="'.$this -> mDom.'" />'.LF;
    $lRet.= '<input type="hidden" name="mand" value="'.$this->mVal["mand"].'" />'.LF;
    $lRet.= '<input type="hidden" name="batch" value="'.$this->mVal["batch"].'" />'.LF;
    if (!empty($this -> mHidden)) {
      foreach ($this -> mHidden as $lKey => $lVal) {
        $lRet.= '<input type="hidden" name="'.$lKey.'" value="'.$lVal.'" />'.LF;
      }
    }
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0" class="tbl">'.LF;
    $lRet.= '<tr><td colspan="5" class="th1">'.$this -> mCaption.'</tr>';

    $lRet.= '<tr>';
    $lRet.= '<td class="th2">'.lan("lib.nr").'</td>';
    $lRet.= '<td class="th2">'.lan("check.ok").'</td>';
    $lRet.= '<td class="th2">'.lan("lib.key").'</td>';
    $lRet.= '<td class="th2">'.lan("cnd-itm.value").' (DE)</td>';
    $lRet.= '<td class="th2">'.lan("cnd-itm.value").' (EN)</td>';
    $lRet.= '</tr>';

    foreach ($this->mArr as $key => $value) {
      $lRet .= $this->getRow($value["num"], $value["key"], $value["de"], $value["en"]);
    }

    $lRet.= '<tr><td class="btnPnl" colspan="5">';
    $lRet.= $this -> getButtons();
    $lRet.= '</td></tr>'.LF;

    $lRet.= '</table>';
    $lRet.= '</form>';
    return $lRet;
  }

  protected function getRow($aNum, $aKey, $aValDe, $aValEn) {
    $lRet = '<tr>';
    //If in Old var -> Disabled Checkbox
    if(isset($this->mOldArr[$aKey]["value"])) {
      $lRet.= '<td class="td2" valign="top" style="background-color:#ff7777" >'.htm($aNum).'</td>';
      $lRet.= '<td class="td2" valign="top" style="background-color:#ff7777" >';
      $lRet.= '<input type="checkbox" name="val['.$aKey.']" disabled />';
      $lRet.= "</td>";
      $lRet.= '<td class="td2" valign="top" style="background-color:#ff7777" >'.htm($aKey).'</td>';
      $lRet.= '<td class="td2" valign="top" style="background-color:#ff7777" >'.htm($aValDe).'</td>';
      $lRet.= '<td class="td2" valign="top" style="background-color:#ff7777" >'.htm($aValEn).'</td>';
    }
    //If not in Old Var -> Enabled checked Checkbox
    else {
      $lRet.= '<td class="td2" valign="top">'.htm($aNum).'</td>';
      $lRet.= '<td class="td2" valign="top">';
      $lRet.= '<input type="checkbox" name="val['.$aKey.']" checked="checked" />';
      $lRet.= "</td>";
      $lRet.= '<td class="td2" valign="top">'.htm($aKey).'</td>';
      $lRet.= '<td class="td2" valign="top">'.htm($aValDe).'</td>';
      $lRet.= '<td class="td2" valign="top">'.htm($aValEn).'</td>';
    }
    $lRet.= '</tr>';
    return $lRet;
  }

  protected function getButtons() {
    $lRet = '';
    $lRet.= btn(lan('lib.ok'), 'this.form.submit()', '<i class="ico-w16 ico-w16-ok"></i>', 'submit').NB;
    $lRet.= btn(lan('lib.cancel'), "go('index.php?act=".$this -> mCancel."&dom=".$this->mDom."')", '<i class="ico-w16 ico-w16-cancel"></i>');
    return $lRet;
  }


}