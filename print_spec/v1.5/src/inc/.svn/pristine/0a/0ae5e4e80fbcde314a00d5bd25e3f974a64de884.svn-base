<?php
class CInc_Gru_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL, $aGid = 0) {
    parent::__construct($aAct, $aCaption, $aCancel);
    
    $lOpt['res'] = 'gru';
    $lOpt['key'] = 'id';
    $lOpt['val'] = 'name';
    $lOpt['fil'] = array('parent_id' => 0);

    if (0 === $aGid) {
      $this -> addDef(fie('parent_id', lan('gru.parent'), 'resselect', $lOpt));
    } else if (NULL == $aGid) {
      // no hidden field
    } else {
      $lGruArr = CCor_Res::extract('id', 'name', 'gru');
      if (isset($lGruArr[$aGid])) {
        $lGruNam = $lGruArr[$aGid];
        $this -> mCap = lan('gru.new.sub').' "'.$lGruNam.'"';
      }
      $this -> addDef(fie('parent_id', '', 'hidden'));
      $this -> setVal('parent_id', $aGid);
    }
    $this -> addDef(fie('name', lan('lib.name')));
    $this -> addDef(fie('code', lan('lib.code'), 'string', NULL, array('class' => 'inp w70')));

    #START #23375 "Extended user conditions"
    if (false != CCor_Cfg::get('extcnd')) {
      $this -> addDef(fie('cnd', lan('lib.condition'), 'select', $this->extendedUsrConditions(2))); // darf nicht an letzter Stelle stehen, da dann kein Update
      $this -> addDef(fie('procnd', lan('lib.project_condition'), 'select', $this->extendedUsrConditions(4))); // darf nicht an letzter Stelle stehen, da dann kein Update
    }
    #STOP #23375 "Extended user conditions"

    $this -> addDef(fie('kundenId', 'Networker Id', 'string', NULL, array('class' => 'inp w70')));
    
    // CheckLists
    if (CCor_Cfg::get('use.checklist')) {
      $lSql = 'SELECT * FROM al_chk_master';
      $lQry = new CCor_Qry($lSql);
      $lChk = array(''=>'');
      foreach ($lQry as $lRow) {
        $lChk[$lRow['id']] = $lRow['name_'.LAN];
      }
      $this -> addDef(fie('chk_master_src', 'Checklists', 'select', $lChk));        
    }
    
    $lAdmLvl = CCor_Res::get('htb', 'admlvl');
    $lAdmLvl[0] = '';
    ksort($lAdmLvl);
    
    $this -> addDef(fie('admin_level', 'Admin Level', 'select', $lAdmLvl));
    $this -> addDef(fie('mand', '', 'hidden'));  //Neu Gruppe wird nur mit aktuelles MandantId gespeichert.
    $this -> setVal('mand', MID); // als Default bei neuem Eintrag (0 = Für alle Mandanten)
  }
  
  protected function extendedUsrConditions($aFlag) {
    $lCnd = array();
    $lCnd[0] = '';
    $lQuery = new CCor_Qry('SELECT id,name FROM al_cnd_master WHERE mand='.MID.' AND (flags & '.$aFlag.')'); // $aFlag = 1 = Users # $aFlag = 4 = Projects
    foreach ($lQuery as $lRow) {
    		$lCnd[$lRow['id']] = $lRow['name'];
    }
    return $lCnd;
  }

}