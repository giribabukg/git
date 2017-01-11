<?php
class CInc_Wiz_Itm_Form extends CHtm_Fpr {

  public function __construct($aAct, $aCaption, $aWiz) {
    $this -> mWiz = intval($aWiz);
    parent::__construct($aAct, 'wiz-itm&id='.$this -> mWiz);
    $this -> mTitle = $aCaption;
    $this -> mFie = CCor_Res::extract('id', 'name_'.LAN, 'fie');
    $this -> setSrc($this -> mFie);
    $this -> setSel('');
    $this -> mMain = NULL;
  }

  public function load($aId) {
    $lId = intval($aId);
    $lSql = 'SELECT * FROM al_wiz_items WHERE mand='.intval(MID).' AND id='.$lId;
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry -> getDat();
    $this -> setSel($lRow['secondary_fields']);
    $this -> mId = $lId;
    $this -> mMain = $lRow['mainfield_id'];
  }

  protected function getBeforeSelection() {
    $lRet = '<div class="frm p16">'.LF;

    $lRet.= '<input type="hidden" name="id" value="'.$this -> mWiz.'" />';
    if ($this -> mId) {
      $lRet.= '<input type="hidden" name="sid" value="'.$this -> mId.'" />';
    }
    #$lRet.= '<select name="mainfield_id">';
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0">';
    $lRet.= '<tr>';
    $lRet.= '<td>Main Field</td>';
    $lRet.= '<td>'.getSelect('mainfield_id', $this -> mFie, $this -> mMain).'</td></tr>';
    $lRet.= '</table>';

    $lRet.= '</div>';
    $lRet.= '<div class="th2">Secondary Fields</div>'.LF;
    return $lRet;
  }

}