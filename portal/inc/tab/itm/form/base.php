<?php
class CInc_Tab_Itm_Form_Base extends CHtm_Form {

  protected $mModule = 'tab_slave';

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $lVisibility[0] = '['.lan('lib.all').']';
    $lVisibility[MID] = MANDATOR_NAME;

    $this -> addDef(fie('mand', lan($this -> mModule.'.mand'), 'select', $lVisibility));

    $lType = $_REQUEST['type'];
    if ($lType == 'job') {
      $lAllJobTypesOutgoing = array();
      $lAllJobTypesIncoming = CCor_Cfg::get('menu-aktivejobs');
      foreach ($lAllJobTypesIncoming as $lKey => $lValue) {
        $lAllJobTypesOutgoing[ltrim($lValue, 'job-')] = ltrim($lValue, 'job-');
      }
      $this -> addDef(fie('subtype', lan($this -> mModule.'.subtype'), 'select', $lAllJobTypesOutgoing));
    }

    $this -> addDef(fie('name', lan($this -> mModule.'.name')));
    $this -> addDef(fie('link', lan($this -> mModule.'.link')));
    $this -> addDef(fie('code', lan($this -> mModule.'.code')));

    $lTarget['formtpl'] = lan($this -> mModule.'.target.formtpl');
    $lTarget['iframe'] = lan($this -> mModule.'.target.iframe');

    $this -> addDef(fie('target', lan($this -> mModule.'.target'), 'select', $lTarget));

    $this -> setVal('mand', MID);
  }

  public function setTabType($aTabType) {
    $this -> setParam('type', $aTabType);
    $this -> setParam('val[type]', $aTabType);
    $this -> setParam('old[type]', $aTabType);

    $lSql = 'SELECT name FROM al_tab_master WHERE type="'.addslashes($aTabType).'"';
    if ($lCap = CCor_Qry::getStr($lSql)) {
      $this -> mCap.= ' ('.$lCap.')';
    }
  }

  public function setTabSubType($aTabSubType) {
    $this -> setParam('subtype', $aTabSubType);
    $this -> setParam('val[subtype]', $aTabSubType);
    $this -> setParam('old[subtype]', $aTabSubType);
  }
}