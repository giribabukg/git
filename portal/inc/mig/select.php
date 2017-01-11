<?php
class CInc_Mig_Select extends CHtm_Form {

  public function __construct($aIdOld, $aIdNew, &$aQry_Old, &$aQry_New) {

    $this -> mIdOld = $aIdOld;
    $this -> mIdNew = $aIdNew;
    $this -> mQry_Old = $aQry_Old;
    $this -> mQry_New = $aQry_New;

		if($this -> mIdOld > 0 AND $this -> mIdNew > 0){
			$lTargetAct = 'mig.JobAddFie';
		} else {
			$lTargetAct = 'mig.JobSelFie';
		}

    $lCap = 'Migration: alte Zusatzfelder in neue Zusatzfelder - Oder eigentlich andersherum?';

    parent::__construct($lTargetAct, $lCap, 'mig.JobSelFie');
    $this -> setAtt('style', 'width:800px');

    $this -> setParam('id_old', $this -> mIdOld);
    $this -> setParam('id_new', $this -> mIdNew);

    if($this -> mIdOld > 0) {
     $lResult = $this -> mQry_Old->getDat();
     $this -> setParam('to_chg', str_replace('Zus.','',$lResult["native"]));
    }

 		$this -> getForm();
  }


  protected function getForm() {
    $lRet = '';
    $lRet.= $this -> getSelection();
    return $lRet;
  }

  protected function getSelection() {
    $lRet = '<div class="frm p8">'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0" width="100%">';
    $lRet.= '<tr>'.LF;

    $lRet.= '<td class="w200 p16">'.LF;
    $lRet.= $this -> get_OldPanel();
    $lRet.= '</td>'.LF;

    $lRet.= '<td  class="w200 p16">'.LF;
    $lRet.= $this -> get_NewPanel();
    $lRet.= '</td>'.LF;

    $lRet.= '</tr>'.LF;
    $lRet.= '</table>';
    $lRet.= '</div>';
    return $lRet;
  }

  protected function get_OldPanel() {
    $lRet = '<b>Zu &auml;nderndes Feld</b>'.BR.LF;
    $lRet.= '<select name="id_old" id="old" size="20" class="inp w400">'.LF;
    foreach ($this -> mQry_Old as $lRow) {
      $lRet.= '<option value="'.$lRow['id'].'" ';
      if ($lRow['id'] == $this -> mIdOld) {
        $lRet.= ' selected="selected"';
      }
      $lRet.= '>'.htm($lRow["name_".LAN]).' ('.str_replace('Zus.','',$lRow["native"]).')</option>'.LF;
    }
    $lRet.= '</select>'.LF;

    return $lRet;
  }

  protected function get_NewPanel() {
    $lRet = '<b>Im System bereits vorhanden:</b>'.BR.LF;
    $lRet.= '<select name="id_new" id="new" size="20" class="inp w300">'.LF;
    foreach ($this -> mQry_New as $lRow) {
      $lRet.= '<option value="'.$lRow['nr'].'" ';
      if ($lRow['nr'] == $this -> mIdNew) {
        $lRet.= ' selected="selected"';
      }
      $lRet.= '>'.htm($lRow["name"]).' ('.$lRow["nr"].')</option>'.LF;
    }
    $lRet.= '</select>'.LF;

    return $lRet;
  }

}