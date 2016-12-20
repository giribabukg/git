<?php
class CInc_Sys_Sql_List extends CCor_Ren {

  protected $mArr;
  protected $mCaption;
  protected $mAct;
  protected $mCancel;
  protected $mHidden = array();

  public function __construct($aArr=array(),$aCaption='SQL list',$aAct='',$aCancel='', $aHidden=array()) {
    $this -> mArr = $aArr;
    $this -> mCaption = $aCaption;
    $this -> mAction = $aAct;
    $this -> mCancel = $aCancel;
    $this -> mHidden = $aHidden;
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mAction.'" />'.LF;
    if (!empty($this -> mHidden)) {
      foreach ($this -> mHidden as $lKey => $lVal) {
        $lRet.= '<input type="hidden" name="'.$lKey.'" value="'.$lVal.'" />'.LF;
      }
    }
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0" class="tbl">'.LF;
    $lRet.= '<tr><td colspan="5" class="th1">'.$this -> mCaption.'</tr>';

    $lRet.= '<tr>';
    $lRet.= '<td class="th2">No.</td>';
    $lRet.= '<td class="th2">Execute</td>';
    $lRet.= '<td class="th2 w800">SQL</td>';
    $lRet.= '</tr>';

    foreach ($this -> mArr as $lKey => $lVal) {
      $lRet.= $this -> getRow($lKey, $lVal);
    }

    $lRet.= '<tr><td class="btnPnl" colspan="5">';
    $lRet.= $this -> getButtons();
    $lRet.= '</td></tr>'.LF;

    $lRet.= '</table>';
    $lRet.= '</form>';
    return $lRet;
  }

  protected function getRow($aKey, $aVal) {
    $lRet = '<tr>';
    $lRet.= '<td class="td2" valign="top">'.htm($aKey).'</td>';
    $lRet.= '<td class="td1" valign="top">';
    $lRet.= '<input type="checkbox" name="val['.$aKey.']" value="'.htm($aVal).'" checked="checked" /></td>';
    $lRet.= '<td class="td1" valign="top">'.htm($aVal).'</td>';
    $lRet.= '</tr>';
    return $lRet;
  }

  protected function getButtons() {
    $lRet = '';
    $lRet.= btn(lan('lib.ok'), 'this.form.submit()', '<i class="ico-w16 ico-w16-ok"></i>', 'submit').NB;
    $lRet.= btn(lan('lib.cancel'), "go('index.php?act=".$this -> mCancel."')", '<i class="ico-w16 ico-w16-cancel"></i>');
    return $lRet;
  }


}