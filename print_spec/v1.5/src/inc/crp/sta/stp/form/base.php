<?php
class CInc_Crp_Sta_Stp_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCid) {
    $lCid = intval($aCid);
    parent::__construct($aAct, $aCaption, 'crp-sta&id='.$lCid);
    $this -> setParam('cid', $lCid);

    $this -> addDef(fie('crp_id','','hidden'));
    $this -> addDef(fie('mand','','hidden'));
    
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('name_'.$lLang, lan('lib.name').' ('.strtoupper($lLang).')'));
    }

    $lCrp = array();
    $lQry = new CCor_Qry('SELECT * FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$lCid.' ORDER BY display');
    foreach ($lQry as $lRow) {
      $lNam = $lRow['display'].' '.$lRow['name_'.LAN];
      $lCrp[$lRow['id']] = $lNam;
    }
    $this -> addDef(fie('from_id', lan('lib.from'), 'select', $lCrp));
    $this -> addDef(fie('to_id', lan('lib.to'), 'select', $lCrp));

    $lArr = array('dom' => 'sfl');
    $this -> addDef(fie('flags', lan('lib.flags'), 'bitset', $lArr));
    $lPar = array('res' => 'apltypes', 'key' => 'code', 'val' => 'name');
    $this -> addDef(fie('apl_type', lan('apl-types.item'), 'resselect', $lPar));
    $lPar = array('res' => 'cond', 'key' => 'id', 'val' => 'name');
    $this -> addDef(fie('cond', lan('lib.condition'), 'resselect', $lPar));
    $this -> addDef(fie('event', lan('lib.event'), 'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN)));
    $this -> addDef(fie('trans', lan('crp.transition'), 'tselect', array('dom' => 'trn')));


    $lCrp = CCor_Res::extract('id', 'code', 'crpmaster');
    $lCrpCode = ((!empty($lCrp) AND isset($lCrp[$lCid])) ? $lCrp[$lCid] : '' );
    #if (!in_array($lCrpCode, array('pro','sku'))) {
      $lFlags = array(0 => '');
      $lFlags+= CCor_Res::extract('id', 'name_'.LAN, 'fla');
      $this -> addDef(fie('flag_act', lan('flag.activate'), 'multipleselect', $lFlags));
      $this -> addDef(fie('flag_stp', lan('flag.deactiv'), 'multipleselect', $lFlags));
    #}
    $this -> addDef(fie('desc_de', lan('lib.description').' (DE)', 'memo'));
    $this -> addDef(fie('desc_en', lan('lib.description').' (EN)', 'memo'));
      
    $this -> setVal('crp_id', $lCid);
    $this -> setVal('mand', MID);
  }
  
  public function setIndependent() {
    unset($this->mFie['from_id']);
    unset($this->mFie['to_id']);
    unset($this->mFie['flag_act']);
    unset($this->mFie['flag_stp']);
    unset($this->mFie['trans']);
    $lArr = array('dom' => 'sfli');
    $this -> addDef(fie('flags', lan('lib.flags'), 'bitset', $lArr));
  }
}