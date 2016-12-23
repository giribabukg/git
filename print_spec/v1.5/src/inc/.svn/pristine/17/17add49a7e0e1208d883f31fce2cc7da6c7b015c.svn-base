<?php
class CInc_Usg_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $this -> setAtt('class', 'tbl w800');

    #START #23375 "Extended user conditions"
    $lCnd = array();
    $lCnd[0] = '';
    $lQuery = new CCor_Qry('SELECT id,name FROM al_cnd_master WHERE mand='.MID.' AND (flags & 1)'); // 1 = Users
    foreach ($lQuery as $lRow) {
      $lCnd[$lRow['id']] = $lRow['name'];
    }
    #STOP #23375 "Extended user conditions"

    #$this -> addDef(fie('anrede',         lan('lib.salutation'), 'valselect', array('lis' => array('Mr.', 'Miss', 'Mrs.', 'Herr', 'Frau'))));
    $this -> addDef(fie('anrede',         lan('lib.salutation'), 'tselect', array('dom' => 'sl2')));

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
    #START #23375 "Extended user conditions"
    if (false != CCor_Cfg::get('extcnd')) {
      $this -> addDef(fie('cnd',          lan('lib.condition'), 'select', $lCnd)); // darf nicht an letzter Stelle stehen, da dann kein Update
    } else {
    $this -> addDef(fie('cnd',            lan('lib.condition'))); // darf nicht an letzter Stelle stehen, da dann kein Update
    }
    #STOP #23375 "Extended user conditions"
    $this -> addDef(fie('user',           lan('usr.name').'*'));
    #$this -> addDef(fie('gadmin',         lan('usr.group_admin'), 'gselect'));
    $this -> addDef(fie('admlvl',      lan('usr.adminlevel'), '', '', array('disabled' => 'disabled')));
    $this -> addDef(fie('',               lan('fie.req'), 'hidden'));
  }

  protected function getButtons() {
    $lRet = '<div class="btnPnl">'.LF;
    $lRet.= btn(lan('lib.ok'), 'return validateMail("' . ereg_replace("[\r\n]", " ", lan('lib.email.error')) . '");', 'img/ico/16/ok.gif', 'submit').NB;
    $lRet.= '</div>'.LF;
    return $lRet;
  }

}