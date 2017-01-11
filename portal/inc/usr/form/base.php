<?php
class CInc_Usr_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> setAtt('class', 'tbl w800');

    $this -> addDef(fie('anrede',         lan('lib.salutation'), 'tselect', array('dom' => 'sl1')));
    $this -> addDef(fie('firstname',      lan('lib.firstname').'*'));
    $this -> addDef(fie('lastname',       lan('lib.lastname').'*'));
    $this -> addDef(fie('company',        lan('lib.company').'*'));
    $this -> addDef(fie('location',       lan('lib.location').'*'));
    $this -> addDef(fie('department',     lan('usr.department')));
    $this -> addDef(fie('pnr',            lan('usr.pnr')));
    $this -> addDef(fie('email',          lan('lib.email').'*'));
    $this -> addDef(fie('email_from',     lan('lib.email.from')));
    $this -> addDef(fie('email_replyto',  lan('lib.email.replyto')));
    $this -> addDef(fie('phone',          lan('lib.phone')));

    if (FALSE != CCor_Cfg::get('extcnd')) {
      $this -> addDef(fie('cnd',          lan('lib.condition'), 'select', $this -> extendedUsrConditions(1))); // this line must not be the last line, otherwise it will not update 
      $this -> addDef(fie('procnd',       lan('lib.project_condition'), 'select', $this -> extendedUsrConditions(4))); // this line must not be the last line, otherwise it will not update
    } else {
      $this -> addDef(fie('cnd',          lan('lib.condition'))); // this line must not be the last line, otherwise it will not update
    }

    $this -> addDef(fie('user',           lan('usr.name').'*'));
    $this -> addDef(fie('gadmin',         lan('usr.group_admin'), 'gselect', '', '', array('NoChoice' => 0)));
    $this -> addDef(fie('admlvl',         lan('usr.adminlevel'), '', '', array('disabled' => 'disabled')));
    if (CCor_Cfg::get('pbox.available')) {
      $lArr = $this->getWorkflowConditions('Elements%');
      $this -> addDef(fie('elements_cond',  'Elements Condition', 'select', $lArr));
    }
    $this -> addDef(fie('',               lan('fie.req'), 'hidden'));
  }

  protected function getWorkflowConditions($aPrefix = '') {
    $lCnd = array();
    $lCnd[0] = '';
    $lSql = 'SELECT id,name FROM al_cond WHERE mand='.MID.' ';
    if (!empty($aPrefix)) {
      $lSql.= 'AND name LIKE '.esc($aPrefix).' ';
    }
    $lSql.= 'ORDER BY name';
    $lQuery = new CCor_Qry($lSql);
    
    foreach ($lQuery as $lRow) {
      $lCnd[$lRow['id']] = $lRow['name'];
    }
    return $lCnd;
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

  protected function getButtons() {
    $lRet = '<div class="btnPnl">'.LF;
    $lRet.= btn(lan('lib.ok'), 'validateMail("' . ereg_replace("[\r\n]", " ", lan('lib.email.error')) . '");', '<i class="ico-w16 ico-w16-ok">', 'submit').NB;
    if ($this->mCancel) {
      $lRet.= btn(lan('lib.cancel'), 'go("index.php?act='.$this->mCancel.'")', '<i class="ico-w16 ico-w16-cancel">', 'button').NB;
    }
    $lRet.= '</div>'.LF;
    return $lRet;
  }

  protected function getJs() {
    $lRet = '<script>';
    $lRet.= 'jQuery(document).on("click", "form button[type=submit]", function(e) {';
    $lRet.= 'var lMandatory = ["firstname", "lastname", "company", "location", "email", "user"];';
    $lRet.= 'var lCurrentJobField;';
    $lRet.= 'var lIsValid = 0;';
    $lRet.= 'for (lCurrentJobField = 0; lCurrentJobField < lMandatory.length; lCurrentJobField++) {';
    $lRet.= '  var lValue = jQuery("input[class~=\"field_" + lMandatory[lCurrentJobField] + "\"]").val();';
    $lRet.= '  if (typeof(lValue) === "undefined" || lValue === null || lValue === "") {';
    $lRet.= '    lIsValid+=1;';
    $lRet.= '    jQuery("input[class~=\"field_" + lMandatory[lCurrentJobField] + "\"]").css({"border": "1px solid red"});';
    $lRet.= '  } else {';
    $lRet.= '    jQuery("input[class~=\"field_" + lMandatory[lCurrentJobField] + "\"]").removeAttr("style")';
    $lRet.= '  }';
    $lRet.= 'console.log("field_" + lMandatory[lCurrentJobField] + lIsValid);';
    $lRet.= '}';
    $lRet.= 'if (lIsValid > 0) {';
    $lRet.= '  e.preventDefault();';
    $lRet.= '}';
    $lRet.= '});';
    $lRet.= '</script>';
    return $lRet;
  }
}