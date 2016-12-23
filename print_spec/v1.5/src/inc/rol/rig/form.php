<?php
class CInc_Rol_Rig_Form extends CCor_Ren {

  public function __construct($aRoleId, $lMandatorId) {
    $this -> mRoleId = intval($aRoleId);
    $this -> mMandatorId = intval($lMandatorId);

    $this -> getDefs();
    $this -> getRights();

    if (0 == $this -> mMandatorId) {
      $this -> mCap = lan('lib.global_rights');
    } else {
      $lMand = CCor_Res::extract('id', 'name_'.LAN, 'mand');
      $this -> mCap = $lMand[$this -> mMandatorId];
    }
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="rol-rig.sedt" />'.LF;
    $lRet.= '<input type="hidden" name="id" value="'.$this -> mRoleId.'" />'.LF;
    $lRet.= '<input type="hidden" name="mid" value="'.$this -> mMandatorId.'" />'.LF;

    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w500">'.LF;
    $lRet.= '<tr><td colspan="8" class="cap">'.htm($this -> mCap).'</td></tr>'.LF;
    $lRet.= $this -> getLines();
    $lRet.= $this -> getAllLine();
    $lRet.= $this -> getButtons();
    $lRet.= '</table>';
    $lRet.= '</form>';
    return $lRet;
  }

  protected function getLines() {
    $lOld = '';
    $lRet = '';
    foreach ($this -> mDef as $lDef) {
      if ($lDef['value_'.LAN] != $lOld) {
        $lOld = $lDef['value_'.LAN];
        $lRet.= '<tr>';
        $lRet.= '<td class="th2">'.htm($lOld).'</td>';
        $lRet.= '<td class="th2 w50 ac">'.htm(lan('lib.read')).'</td>';
        $lRet.= '<td class="th2 w50 ac">'.htm(lan('lib.edit')).'</td>';
        $lRet.= '<td class="th2 w50 ac">'.htm(lan('lib.insert')).'</td>';
        $lRet.= '<td class="th2 w50 ac">'.htm(lan('lib.delete')).'</td>';
  
        $lRet.= '<td class="th2 w16 ac" data-toggle="tooltip" data-tooltip-body="'.lan('lib.sel.all').'"></td>';
        $lRet.= '<td class="th2 w16 ac" data-toggle="tooltip" data-tooltip-body="'.lan('lib.desel.all').'"></td>';
        $lRet.= '<td class="th2 w16 ac" data-toggle="tooltip" data-tooltip-head="'.lan('lib.info').'" data-tooltip-body></td>';
        $lRet.= '</tr>';
      }
      $lRet.= $this -> getLine($lDef);
    }
    return $lRet;
  }
  
  protected function getLine($aDef) {
    $this -> mHas = FALSE;

    $lRet = '<tr>';
    $lRet.= '<td class="td2">'.htm($aDef['name_'.LAN]).'</td>';
    $lRet.= $this -> getCheck($aDef, rdRead);
    $lRet.= $this -> getCheck($aDef, rdEdit);
    $lRet.= $this -> getCheck($aDef, rdIns);
    $lRet.= $this -> getCheck($aDef, rdDel);

    if ($this -> mHas) {
      $lRet.= '<td class="td2 ac">'.img('img/ico/16/ok.gif', array('class' => 'nav', 'onclick' => 'Flow.checkAllEx("['.$aDef['code'].']");')).'</td>';
      $lRet.= '<td class="td2 ac">'.img('img/ico/16/cancel.gif', array('class' => 'nav', 'onclick' => 'Flow.uncheckAllEx("['.$aDef['code'].']");')).'</td>';
    } else {
      $lRet.= '<td class="td2 p4"><img src="img/d.gif" alt="" class="w16" /></td>';
      $lRet.= '<td class="td2 p4"><img src="img/d.gif" alt="" class="w16" /></td>';
    }

    $lHead = htm($aDef['name_'.LAN]).' ('.lan('lib.code').': '.htm($aDef['code']).')';
    $lBody = nl2br(preg_replace("/[\n\r\'|&#0*39;]/", " ", $aDef['descr']));

    $lRet.= '<td class="td2 ac">'.img('img/jfl/1024.gif', array('data-toggle' => 'tooltip', 'data-tooltip-head' => $lHead, 'data-tooltip-body' => $lBody)).'</td>';
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function getAllLine() {
    $lRet = '<tr>';
    $lRet.= '<td class="td2">&nbsp;</td>';
    $lRet.= $this -> getAllCheck(rdRead);
    $lRet.= $this -> getAllCheck(rdEdit);
    $lRet.= $this -> getAllCheck(rdIns);
    $lRet.= $this -> getAllCheck(rdDel);
    $lRet.= '<td class="td2">&nbsp;</td>';
    $lRet.= '<td class="td2">&nbsp;</td>';
    $lRet.= '<td class="td2">&nbsp;</td>';
    $lRet.= '</tr>';

    $lRet.= '<tr>';
    $lRet.= '<td class="td2">&nbsp;</td>';
    $lRet.= $this -> getAllUncheck(rdRead);
    $lRet.= $this -> getAllUncheck(rdEdit);
    $lRet.= $this -> getAllUncheck(rdIns);
    $lRet.= $this -> getAllUncheck(rdDel);
    $lRet.= '<td class="td2">&nbsp;</td>';
    $lRet.= '<td class="td2">&nbsp;</td>';
    $lRet.= '<td class="td2">&nbsp;</td>';
    $lRet.= '</tr>';
    return $lRet;
  }

  protected function getAllCheck($aLevel) {
    $lRet = '<td class="td2 ac">';
    $lRet.= img('img/ico/16/ok.gif', array('class' => 'nav', 'onclick' => 'Flow.checkAllEx(\'['.$aLevel.']\')'));
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getAllUncheck($aLevel) {
    $lRet = '<td class="td2 ac">';
    $lRet.= img('img/ico/16/cancel.gif', array('class' => 'nav', 'onclick' => 'Flow.uncheckAllEx(\'['.$aLevel.']\')'));
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getButtons() {
    $lRet = '<tr>';
    $lRet.= '<td class="btnPnl" colspan="8">';
    $lRet.= btn(lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit').NB;
    $lRet.= btn('Reset', 'this.form.reset()', 'img/ico/16/cancel.gif');
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function getDefs() {
    $this -> mDef = array();
    // g.domain="rgr" must be global: al_htb_itm.mand=0
    $lSql = 'SELECT r.code,r.name_'.LAN.',r.level,g.value_'.LAN;
    $lSql.= ' FROM al_sys_rig_usr r, al_htb_itm g';
    $lSql.= ' WHERE g.domain="rgr"';
    $lSql.= ' AND r.grp=g.value ';
    $lSql.= ' AND r.mand='.$this -> mMandatorId;
    $lSql.= ' ORDER BY g.value_'.LAN.',r.name_'.LAN;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mDef[$lRow['code']] = $lRow;
    }
  }

  protected function getRights() {
    $this -> mRig = array();

    $lSql = 'SELECT code, level FROM al_rol_rig WHERE role_id='.$this -> mRoleId.' ';
    $lSql.= ' AND mand='.$this -> mMandatorId;

    $lQry = new CCor_Qry();
    $lQry -> query($lSql);
    foreach ($lQry as $lRow) {
      $this -> mRig[$lRow['code']] = intval($lRow['level']);
    }
  }

  protected function getCheck($aDef, $aLvl) {
    $lLvl = intval($aDef['level']);
    $lRet = '<td class="td1 ac">';
    if (bitSet($lLvl, $aLvl)) {
      $lCod = $aDef['code'];
      $lRig = (isset($this -> mRig[$lCod])) ? $this -> mRig[$lCod] : 0;
      if (bitSet($lRig, $aLvl)) {
        $lRet.= '<input type="hidden" name="old['.$aDef['code'].']['.$aLvl.']" value="'.$aLvl.'" />';
        $lRet.= '<input type="checkbox" name="val['.$aDef['code'].']['.$aLvl.']" value="'.$aLvl.'" checked="checked" />';
      } else {
        $lRet.= '<input type="hidden" name="old['.$aDef['code'].']['.$aLvl.']" value="0" />';
        $lRet.= '<input type="checkbox" name="val['.$aDef['code'].']['.$aLvl.']" value="'.$aLvl.'" />';
      }
      $this -> mHas = TRUE;
    } else {
      $lRet.= NB;
    }
    $lRet.= '</td>';
    return $lRet;
  }
}