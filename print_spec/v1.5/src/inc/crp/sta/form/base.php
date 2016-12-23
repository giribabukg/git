<?php
class CInc_Crp_Sta_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCid) {
    $lCid = intval($aCid);
    parent::__construct($aAct, $aCaption, 'crp-sta&id='.$lCid);
    $this -> setParam('cid', $lCid);

    $this -> addDef(fie('crp_id', '', 'hidden'));
    $this -> addDef(fie('mand', '', 'hidden'));
    $this -> addDef(fie('status', lan('lib.status')));
    $this -> addDef(fie('display', lan('crp.display')));
    $this -> addDef(fie('img', lan('lib.img'), 'string', '', array("data-change" => "autocomplete", "data-source" => "ajx.crpimg", "data-autocomplete-body" => "'<span class=\"informal\"><img src=\"'+item.label+'\" />'+item.value+'</span>'")));

    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('name_'.$lLang, lan('lib.name').' ('.strtoupper($lLang).')'));
      $this -> addDef(fie('desc_'.$lLang, lan('lib.description').' ('.strtoupper($lLang).')', 'memo', NULL, array('rows' => '3')));
    }
    $this -> addDef(fie('apl', lan('apl.display'), 'bitset', array('dom' => 'apl')));
    $this -> setVal('apl', 0);
    $this -> addDef(fie('flags', lan('lib.flags'), 'bitset', array('dom' => 'sta')));
    $this -> addDef(fie('mandbystat', lan('lib.manybystat'), 'jobfieldparams', array('res' => 'fie', 'key' => 'alias', 'val' => 'name_'.LAN)));

    //22651 Project Critical Path Functionality
    $lProCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    if (isset($lProCrp['pro']) AND !empty($lProCrp['pro'])) {
      $lProCrpId = $lProCrp['pro'];
      if ($lCid != $lProCrpId) {
        $lQry = new CCor_Qry('SELECT * FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$lProCrpId.' ORDER BY display');
        $lProCrp = array(0 => '');
        foreach ($lQry as $lRow) {
          $lNam = $lRow['display'].' '.$lRow['name_'.LAN];
          $lProCrp[$lRow['display']] = $lNam;
        }
        $this -> addDef(fie('pro_con', lan('crp.control.project'), 'select', $lProCrp));
      }
    }
    #$this -> addDef(fie('on_enter', 'Enter Event', 'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN)));
    #$this -> addDef(fie('on_exit',  'Exit Event',  'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN)));

    // Extended Report
    if (CCor_Cfg::get('extended.reporting')) {
    	foreach (CCor_Cfg::get('report.map') as $lKey => $lVal) {
    		$lReportMapper[$lVal] = $lKey;
    	}
    	$this->addDef(fie('report_map', 'Report Mapper', 'select', $lReportMapper));
    }

    $this -> setVal('crp_id', $lCid);
    $this -> setVal('mand', MID);
  }

}