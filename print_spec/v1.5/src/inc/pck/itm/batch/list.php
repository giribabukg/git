<?php
class CInc_Pck_Itm_Batch_List extends CCor_Ren {

  protected $mArr;
  protected $mOldArr;

  protected $mCaption;
  protected $mAct;
  protected $mCancel;
  protected $mFields;
  protected $mReq;
  protected $mDom;

  public function __construct($aAct, $aCancel, $aCaption, $aFields, $aReq) {
    $this -> mReq = $aReq;
    $this -> mAct = $aAct;
    $this -> mDom = $aReq->getVal("dom");
    $this -> mCancel = $aCancel;
    $this -> mCaption = $aCaption;
    $this -> mFields = $aFields;
    $this -> mVal = $aReq->getVal("val");

    $lBatchData = array();
    $lLines = explode("\n", $this->mVal["batch"]);
    $set=1;
    foreach($lLines as $line) {
      $lineArr = explode(";", $line);
      $lBatchData[$set]["num"] = $set;
      foreach($lineArr as $key => $value) {
        $lBatchData[$set][$key] = $value;
      }
      $set++;
    }

    $this->mArr = $lBatchData;
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mAct.'" />'.LF;
    $lRet.= '<input type="hidden" name="dom" value="'.$this -> mDom.'" />'.LF;
    $lRet.= '<input type="hidden" name="mand" value="'.$this->mVal["mand"].'" />'.LF;
    $lRet.= '<input type="hidden" name="batch" value="'.$this->mVal["batch"].'" />'.LF;

    $lRet.= '<table cellpadding="2" cellspacing="0" border="0" class="tbl">'.LF;
    $lRet.= '<tr><td colspan="5" class="th1">'.$this -> mCaption.'</tr>';

    $lRet.= '<tr>';
    $lRet.= '<td class="th2">'.lan("lib.nr").'</td>';
    $lRet.= '<td class="th2">'.lan("check.ok").'</td>';

    //Print Table Head
    foreach($this->mFields as $row) {
      $lRet.= '<td class="th2">'.$row["alias"].'</td>';
    }
    $lRet.= '</tr>';

    //Print Table Rows
    foreach ($this->mArr as $value) {
      $lRet .= $this->getRow($value);
    }

    //Print Buttons
    $lRet.= '<tr><td class="btnPnl" colspan="5">';
    $lRet.= $this -> getButtons();
    $lRet.= '</td></tr>'.LF;

    $lRet.= '</table>';
    $lRet.= '</form>';
    return $lRet;
  }

  protected function getRow($lVal) {
    $lRet = '<tr>';
      $lRet.= '<td class="td2" valign="top">'.htm($lVal["num"]).'</td>';
      $lRet.= '<td class="td2" valign="top">';
      $lRet.= '<input type="checkbox" name="val['.$lVal["num"].']" checked="checked" />';
      $lRet.= "</td>";
      unset($lVal["num"]);
      foreach($lVal as $row) {
        $lRet.= '<td class="td2" valign="top">'.htm($row).'</td>';
      }
    $lRet.= '</tr>';
    return $lRet;
  }

  protected function getButtons() {
    $lRet = '';
    $lRet.= btn(lan('lib.ok'), 'this.form.submit()', 'img/ico/16/ok.gif', 'submit').NB;
    $lRet.= btn(lan('lib.cancel'), "go('index.php?act=".$this -> mCancel."')", 'img/ico/16/cancel.gif');
    return $lRet;
  }


}