<?php
class CInc_Usg_Mem_Form extends CUsr_Mem_Form {

  public function __construct($aUid) {
    parent::__construct($aUid);

    $this -> mAct = 'usg-mem.sedt';
    $lUsr = CCor_Usr::getInstance();
    $this -> mGruKey = $lUsr->getVal('gadmin');

    if (0 < $this -> mGruKey) {
      $lGru = $this -> mGru[$this -> mGruKey]; // aus der Elternklasse
      $this -> mGru = CCor_Res::get('gru', array('parent_id' => $this -> mGruKey));
      $this->getChildGroups($this -> mGru);
      $this -> mGru[$this -> mGruKey] = $lGru;
    }
    // Quick and Dirty before the 20th of Feb.
    // Get the group membership of the sub groups from "mGruKey" and pass this value to the controller.
    // This to avoid deleting the membership of another groups/another mands from the usg-mem page
    $this -> mIsMemberOf = implode(',', array_keys(array_intersect_key($this -> mMem, $this -> mGru)));
  }

  protected function getChildGroups($aArray) {
  	foreach ($aArray as $key => $val) {
  		$this -> mNewArray = CCor_Res::get('gru', array('parent_id' => $key));
  		$this -> mGru = $this -> mGru + $this -> mNewArray;
  		$lCheck = $this -> hasChildren($key);
  		if ($lCheck) self::getChildGroups($this -> mNewArray);
  	}
  }

  protected function getRow($aRow) {
    if($aRow['admin_level'] <= $this -> mUsrAdminLevel AND $aRow["admin_level"] != 0) return;
    $lId = $aRow['id'];
    $lRoot = (0 == $aRow['parent_id']);
    $lHas = $this -> hasChildren($lId);
    $lNam = 'val['.$lId.']';
    $lRet = '<tr>'.LF;
    $lRet.= '<td class="td2 w16">'.LF;
    if (!$lRoot){
      $lSel = (isset($this -> mMem[$aRow['id']])) ? ' checked="checked"' : '';
      $lRet.= '<input type="checkbox" id="chk'.$aRow['id'].'" name="'.$lNam.'"'.$lSel.' onclick="mem(this)" />'.LF;
    }
    $lRet.= '<input type="hidden" name="isMemberOfThis" value="'.$this -> mIsMemberOf.'" />';
    $lRet.= '</td>'.LF;

    if ($lRoot) {
      $lRet.= '<td class="td2 w100p b">';
    } else {
      $lRet.= '<td class="td1 w100p">';
    }

    if ($lHas) {
      $lRet.= '<a href="javascript:Flow.Std.togTr(\'tr'.$lId.'\')">';
      $lRet.= htm($aRow['name']).' ...';
      $lRet.= '</a>';
    } else {
      $lRet.= htm($aRow['name']);
    }
    $lRet.= '</td>'.LF;
    $lRet.= '</tr>'.LF;

    if ($lHas) {
      $lRet.= '<tr id="tr'.$lId.'" class="togtr" style="display:none">';
      $lRet.= '<td class="td1 tg">&nbsp;</td>';
      $lRet.= '<td class="p0">';
      $lRet.= '<table cellpadding="2" cellspacing="0" border="0" class="w100p">';
      $lRet.= $this -> getGroups($lId);
      $lRet.= '</table>';
      $lRet.= '</td>';
      $lRet.= '</tr>';
    }
    return $lRet;
  }

  protected function getCont() {
    $lRet = '<form action="index.php" method="post">'.LF;
    $lRet.= '  <input type="hidden" name="act" value="'.$this -> mAct.'" />'.LF;
    $lRet.= '  <input type="hidden" name="id" value="'.$this -> mUid.'" />'.LF;
    $lRet.= '  <input type="hidden" name="val['.$this -> mGruKey.']" value="on" />'.LF;
    $lRet.= '  <table cellpadding="2" cellspacing="0" class="tbl w500">'.LF;
    $lRet.= '    <tr>'.LF;
    $lRet.= '      <td class="cap" colspan="3">'.lan('usg-mem.menu').'</td>'.LF;
    $lRet.= '    </tr>'.LF;
    $lRet.= '    <tr>'.LF;
    $lRet.= '      <td class="sub" colspan="3" style="padding:16px;">'.LF;
    $lRet.= btn(lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit').LF;
    $lRet.= btn(lan('lib.reset'), '', 'img/ico/16/cancel.gif', 'reset').LF;
    $lRet.= btn(lan('lib.expandall'), 'Flow.Std.showAllTr()','img/ico/16/nav-down-lo.gif').LF;
    $lRet.= btn(lan('lib.collapseall'), 'Flow.Std.hideAllTr()','img/ico/16/nav-up-lo.gif').LF;
    $lRet.= '      </td>'.LF;
    $lRet.= '    </tr>'.LF;
    $lRet.= '    <tr>'.LF;
    $lRet.= '      <td class="th1 w16">&nbsp;</td>'.LF;
    $lRet.= '      <td class="th1" colspan="2">'.htm(lan('lib.group')).'</td>'.LF;
    $lRet.= '    </tr>';
    $lRet.= $this -> getGroups(0);
    $lRet.= '  </table>'.LF;
    $lRet.= '</form>';
    return $lRet;
  }
}