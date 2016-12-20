<?php
class CInc_Hom_Pic_Form extends CHtm_Form {

  public function __construct($aUid, $aAct = 'hom-pic.sedt', $aCancel = NULL) {
    $lCaption = lan('hom.pic');
    $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE id='.$aUid);
    if ($lRow = $lQry -> getDat()) {
      $lCaption = $lCaption . ": " . htm($lRow['firstname']) . " " . htm($lRow['lastname']);
    }
    parent::__construct($aAct, $lCaption, $aCancel);
    $this -> setAtt('class', 'tbl w100p');
    $this -> mUid = intval($aUid);
    $this -> mMnd = intval($lRow['mand']);
    $this -> setParam('val[id]', $this -> mUid);
    $this -> setParam('old[id]', $this -> mUid);
    $this -> setParam('val[mand]', $this -> mMnd);
    $this -> setParam('old[mand]', $this -> mMnd);
  }

  protected function getFieldForm() {
    $lRet = '<tr>';
    $lRet.= '<td>';
    $lImage = 'img/usr/usr-'.$this -> mUid.'.gif';
    $lRet.= img($lImage);
    $lRet.= '</td>';
    $lRet.= '<td><input type="file" name="photogif" onchange="RefreshImage(this.value, \'photoimage\')" /></td>';
    $lRet.= '</tr>'.LF;
    $lRet.= '<tr>';
    $lRet.= '<td></td>';
    $lRet.= '<td>'.lan('hom.pic.spec').'</td>';
    $lRet.= '</tr>'.LF;

    return $lRet;
  }

}