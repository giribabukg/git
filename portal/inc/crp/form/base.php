<?php
class CInc_Crp_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);
    $this -> setAtt('class', 'tbl w600');

    $this -> addDef(fie('code', lan('lib.code'), 'string', NULL, array('class' => 'inp w70')));

    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addDef(fie('name_'.$lLang, lan('lib.name').' ('.strtoupper($lLang).')'));
    }

    // general
    $this -> addDef(fie('eve_draft',     lan('lib.draftevent'),     'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN)));
    $this -> addDef(fie('eve_comment',   lan('lib.commentevent'),   'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN)));
    $this -> addDef(fie('eve_jobchange', lan('lib.jobchangeevent'), 'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN)));
    $this -> addDef(fie('eve_upload',    lan('lib.uploadevent'),    'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN)));

    // on hold and continue
    $this -> addDef(fie('eve_onhold',    lan('lib.onholdevent'),    'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN)));
    $this -> addDef(fie('eve_continue',  lan('lib.continueevent'),  'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN)));

    // cancel and revive
    $this -> addDef(fie('eve_cancel',    lan('lib.cancelevent'),    'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN)));
    $this -> addDef(fie('eve_revive',    lan('lib.reviveevent'),    'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN)));

    // archive
    $this -> addDef(fie('eve_archive',              lan('lib.archiveevent'),              'resselect', array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN)));
    $this -> addDef(fie('eve_archive_condition',    lan('lib.archiveevent.condition'),    'resselect', array('res' => 'cond', 'key' => 'id', 'val' => 'name')));
    $this -> addDef(fie('eve_archive_numberofjobs', lan('lib.archiveevent.numberofjobs'), 'valselect', array('lis' => array(1, 2, 5, 10, 12, 15, 20, 25, 50, 100))));
    
    //Phrase job type
    $this -> addDef(fie('eve_phrase', lan('lib.phrase.jobtype'), 'select', 'a:3:{s:0:"";s:0:"";s:3:"job";s:3:"Job";s:7:"product";s:7:"Product";}'));
    
    // TODO: is this still needed? when so, where or for what?
    $this -> addDef(fie('mand', '', 'hidden')); 
    $this -> setVal('mand', MID);  
  }
}