<?php
class CInc_Usr_Rig_Form extends CCor_Ren {

  public function __construct($aUserId, $aMandatorId, $aRight = '') {
    $this -> mUserId = intval($aUserId);
    $this -> mMandatorId = intval($aMandatorId);
    $this -> mRightCode = $aRight;

    $this -> getDefs();
    $this -> getRights();

    if (0 == $this -> mMandatorId) {
      $this -> mCap = lan('lib.global_rights');
    } elseif ('htg' == $this -> mRightCode) {
      $this -> mCap = lan('htb.menu');
    } elseif ('fie' == $this -> mRightCode) {
      $this -> mCap = lan('fie.menu');
    } else {
      $lMand = CCor_Res::extract('id', 'name_'.LAN, 'mand');
      $this -> mCap = $lMand[$this -> mMandatorId];
    }
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="usr-rig.sedt" />'.LF;
    $lRet.= '<input type="hidden" name="id" value="'.$this -> mUserId.'" />'.LF;
    $lRet.= '<input type="hidden" name="mid" value="'.$this -> mMandatorId.'" />'.LF;
    $lRet.= '<input type="hidden" name="rig" value="'.$this -> mRightCode.'" />'.LF;

    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w500">'.LF;
    $lRet.= '<tr>'.LF;
    $lRet.= '<td colspan="8" class="cap">'.htm($this -> mCap).'</td>'.LF;
    $lRet.= '</tr>'.LF;
    $lRet.= $this -> getLines();
    $lRet.= $this -> getAllLine();
    $lRet.= $this -> getButtons();
    $lRet.= '</table>';
    $lRet.= '</form>';
    return $lRet;
  }
  
  protected function getLines() {
    $this -> mHasLevel[rdRead] = FALSE;
    $this -> mHasLevel[rdEdit] = FALSE;
    $this -> mHasLevel[rdIns]  = FALSE;
    $this -> mHasLevel[rdDel]  = FALSE;

    $lOld = '';
    $lRet = '';
    foreach ($this -> mDef as $lDef) {
      if ($lDef['val'] != $lOld) {
        $lOld = $lDef['val'];
        $lRet.= '<tr>';
        $lRet.= '<td class="th2">'.htm($lOld).'</td>';
        $lRet.= '<td class="th2 w50 ac">'.htm(lan('lib.read')).'</td>';
        $lRet.= '<td class="th2 w50 ac">'.htm(lan('lib.edit')).'</td>';
        $lRet.= '<td class="th2 w50 ac">'.htm(lan('lib.insert')).'</td>';
        $lRet.= '<td class="th2 w50 ac">'.htm(lan('lib.delete')).'</td>';

        $lRet.= '<td class="th2 w16 ac" data-toggle="tooltip" data-tooltip-head="'.lan('lib.sel.all').'" data-tooltip-body></td>';
        $lRet.= '<td class="th2 w16 ac" data-toggle="tooltip" data-tooltip-head="'.lan('lib.desel.all').'" data-tooltip-body></td>';
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
    $lRet.= '<td class="td2">'.htm($aDef['name']).'</td>';
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

    $lHead = htm($aDef['name']).' ('.lan('lib.code').': '.htm($aDef['code']).')';
    $lBody = nl2br(preg_replace("/[\n\r\'|&#0*39;]/", " ", $aDef['description']));

    $lCaption = htm($aDef['name']).' / code: '. htm($aDef['code']);
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
    if ($this -> mHasLevel[$aLevel]) {
      $lRet.= img('img/ico/16/ok.gif', array('class' => 'nav', 'onclick' => 'Flow.checkAllEx(\'['.$aLevel.']\')'));
    } else {
      $lRet.= NB;
    }
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getAllUncheck($aLevel) {
    $lRet = '<td class="td2 ac">';
    if ($this -> mHasLevel[$aLevel]) {
      $lRet.= img('img/ico/16/cancel.gif', array('class' => 'nav', 'onclick' => 'Flow.uncheckAllEx(\'['.$aLevel.']\')'));
    } else {
      $lRet.= NB;
    }
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

    $lLevel='';
    if ('htg' == $this -> mRightCode) {
      $lSql = 'SELECT domain AS code, description AS name, description_'.LAN.' AS description';
      $lSql.= ' FROM al_htb_master;';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lRow['val'] = 'Helptables';
        $lRow['level'] = 15; // all four boxes will be shown
        $this -> mDef[$lRow['code']] = $lRow;
      }
    } elseif ('fie' == $this -> mRightCode) { // read and edit right for jobfields
      $lSql = 'SELECT alias AS code, name_'.LAN.' AS name, desc_'.LAN.' AS description, flags';
      $lSql.= ' FROM al_fie WHERE mand='.MID.' AND (flags & '.ffRead.' OR flags & '.ffEdit.')' ;
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lLevel = '';
        if (bitset($lRow['flags'], ffRead)) {
          $lLevel = ffRead;
        }
        if (bitset($lRow['flags'], ffEdit)) {
          $lLevel =$lLevel + ffEdit;;
        }
        $lRow['val'] = lan('fie.menu');
        $lRow['level'] = $lLevel;
        $lRow['code'] = 'fie_'.$lRow['code'];
        $this -> mDef['fie_'.$lRow['code']] = $lRow;
      }
    } else {
      // g.domain="rgr" must be global: al_htb_itm.mand=0
      $lSql = 'SELECT p.code, p.name_'.LAN.' AS name, p.desc_'.LAN.' AS description, p.level, q.value_'.LAN.' AS val';
      $lSql.= ' FROM al_sys_rig_usr AS p, al_htb_itm AS q WHERE q.domain="rgr" AND p.grp=q.value';
      if (0 == $this -> mMandatorId) {
        $lSql.= ' AND p.mand='.$this -> mMandatorId;
      } else {
        $lSql.= ' AND p.mand IN('.$this -> mMandatorId.',-1)';
      }
      $lSql.= ' ORDER BY q.value_'.LAN.', p.name_'.LAN;
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $this -> mDef[$lRow['code']] = $lRow;
      }
    }
  }

  protected function getRights() {
    $this -> mMem = array();

    $lQry = new CCor_Qry('SELECT m.gid,g.name FROM al_usr_mem m, al_gru g WHERE m.gid=g.id AND m.uid='.$this -> mUserId);
    foreach ($lQry as $lRow) {
      $lGid = intval($lRow['gid']);
      $this -> mMem[$lGid] = $lRow['name'];
    }

    $this -> mRig = array();
    $lSql = 'SELECT code,level FROM al_usr_rig WHERE user_id='.$this -> mUserId.' ';
    if(!empty($this -> mRightCode))
      $lSql.= 'AND `right` LIKE "'.$this -> mRightCode.'" ';
    $lSql.= 'AND mand='.$this -> mMandatorId;
    $lQry -> query($lSql);
    foreach ($lQry as $lRow) {
      $this -> mRig[$lRow['code']] = intval($lRow['level']);
    }
    $this -> mMemRig = array();
    if (!empty($this -> mMem)) {
      $lSql = 'SELECT group_id,code,level FROM al_gru_rig WHERE group_id IN ('.implode(',', array_keys($this -> mMem)).') ';
      if(!empty($this -> mRightCode))
        $lSql.= 'AND `right` LIKE "'.$this -> mRightCode.'" ';
      $lSql.= 'AND mand='.$this -> mMandatorId;
      $lQry -> query($lSql);
      $this -> dbg($lSql);
      foreach ($lQry as $lRow) {
        $lLvl = intval($lRow['level']);
        if (bitSet($lLvl, rdRead))
          $this -> mMemRig[$lRow['code']][rdRead][] = $this -> mMem[$lRow['group_id']];
        if (bitSet($lLvl, rdEdit))
          $this -> mMemRig[$lRow['code']][rdEdit][] = $this -> mMem[$lRow['group_id']];
        if (bitSet($lLvl, rdIns))
          $this -> mMemRig[$lRow['code']][rdIns][] = $this -> mMem[$lRow['group_id']];
        if (bitSet($lLvl, rdDel))
          $this -> mMemRig[$lRow['code']][rdDel][] = $this -> mMem[$lRow['group_id']];
      }
    }
  }

  protected function getCheck($aDef, $aLvl) {
    $lLvl = intval($aDef['level']);
    $lRet = '<td class="td1 ac '.$aDef['code'].$aLvl.'">';
    if (bitSet($lLvl, $aLvl)) {
      if (isset($this -> mMemRig[$aDef['code']][$aLvl])) {
        $lMem = implode('<br />', $this -> mMemRig[$aDef['code']][$aLvl]);
        $lRet.= img('img/ico/16/check-hi.gif',array('data-toggle' => 'tooltip', 'data-tooltip-head' => lan('lib.rig.inh'), 'data-tooltip-body' => $lMem)).'</span>';
      } else {
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
        $this -> mHasLevel[$aLvl] = TRUE;
      }
    } else {
      $lRet.= NB;
    }
    $lRet.= '</td>';
    return $lRet;
  }
}