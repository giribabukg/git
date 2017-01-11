<?php
class CInc_Gru_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL, $aGid = 0) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> mGid = $aGid;
    $this -> setAtt('class', 'tbl w600');
    $this ->setForm();
  }

  protected function setForm() {
    $lOpt['res'] = 'gru';
    $lOpt['key'] = 'id';
    $lOpt['val'] = 'name';
    $lOpt['fil'] = array('parent_id' => 0);
    
    if (0 === $this->mGid) {
      $this -> addDef(fie('parent_id', lan('gru.parent'), 'resselect', $lOpt));
    }
    else if (NULL == $this->mGid) {
      // no hidden field
    }
    else {
      echo "HIT";
      $lGruArr = CCor_Res::extract('id', 'name', 'gru');
      if (isset($lGruArr[$this->mGid])) {
        $lGruNam = $lGruArr[$this->mGid];
        $this -> mCap = lan('gru.new.sub').' "'.$lGruNam.'"';
      }
      $this -> addDef(fie('parent_id', '', 'hidden'));
      $this -> setVal('parent_id', $this->mGid);
    }
    $this -> addDef(fie('name', lan('lib.name'), 'string', NULL, array('class' => 'inp w350')));
    $this -> addDef(fie('code', lan('lib.code'), 'string', NULL, array('class' => 'inp w175')));

    #START #23375 "Extended user conditions"
    if (false != CCor_Cfg::get('extcnd')) {
      $this -> addDef(fie('cnd', lan('lib.condition'), 'select', $this->extendedUsrConditions(2), array('class' => 'inp w350'))); // darf nicht an letzter Stelle stehen, da dann kein Update
      $this -> addDef(fie('procnd', lan('lib.project_condition'), 'select', $this->extendedUsrConditions(4), array('class' => 'inp w350'))); // darf nicht an letzter Stelle stehen, da dann kein Update
    }
    #STOP #23375 "Extended user conditions"

    $this -> addDef(fie('kundenId', lan('lib.NetworkerId'), 'string', NULL, array('class' => 'inp w175')));

    // CheckLists
    if (CCor_Cfg::get('use.checklist')) {
      $lSql = 'SELECT * FROM al_chk_master';
      $lQry = new CCor_Qry($lSql);
      $lChk = array(''=>'');
      foreach ($lQry as $lRow) {
        $lChk[$lRow['id']] = $lRow['name_'.LAN];
      }
      $this -> addDef(fie('chk_master_src', 'Checklists', 'select', $lChk, array('class' => 'inp w350')));
    }

    $lAdmLvl = CCor_Res::get('htb', 'admlvl');
    $lAdmLvl[0] = '';
    ksort($lAdmLvl);

    $this -> addDef(fie('admin_level', 'Admin Level', 'select', $lAdmLvl, array('class' => 'inp w350')));
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

  public function setExternal() {
    $this->addDef(fie('comp_name', lan('gru.compName'), 'string', NULL, array('class' => 'inp w350')));
    $this->addDef(fie('comp_dom', lan('gru.compDom'), 'domain', NULL, array('placeholder' => '@Company.com', 'class' => 'inp w350')));
    $this->addDef(fie('max_usr', lan('gru.maxUsr'), 'int', NULL, array('class' => 'inp w175')));

    $lExp = CCor_Cfg::get('group.expiryDates', array('0' => lan('lib.disabled'), '30' => '30 '.lan('lib.days'), '90' => '90 '.lan('lib.days'), '180' => '180 '.lan('lib.days'), '360' => '360 '.lan('lib.days')));
    $lCfgExpDays = CCor_Cfg::get('password.expire.days', 0);
    if($lCfgExpDays != 0) {
      $lExp['0'] = "Default: ". $lCfgExpDays;
    }

    if(!isset($lExp[$this->mVal['pass_exp']])) {
      $lExp[$this->mVal['pass_exp']] = $this->mVal['pass_exp'] . " " .lan('lib.days');
    }
    $this->addDef(fie('pass_exp', lan('gru.passExp'), 'select', $lExp, array('class' => 'inp w350')));

    $this -> addDef(fie('typ', '', 'hidden'));
    $this -> setVal('typ', 'ext');
  }
}