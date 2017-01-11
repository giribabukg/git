<?php
class CInc_Gru_Invite_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aGruId, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);
    $this -> mGru = CCor_Res::get('gru', array('id'=>$aGruId));

    $this -> setAtt('class', 'tbl w800');
    $this -> mVal['gruID'] = $aGruId;
    $lGru = CCor_Res::get('gru', array('id'=>$this -> mVal['gruID']));
    $lCompDom = explode(',', $lGru[$aGruId]["comp_dom"]);
    $lDom ="";
    foreach ($lCompDom as $lRow) {
      $lDom .= '"'. trim(str_replace("@", "", $lRow)) . '", ';
    }
    $lDom = '['.substr($lDom, 0, -2).']';

    $this -> addDef(fie('to_emails', lan('lib.email'), 'string','', array('class' => 'inp w500', 'onkeyup' => 'javascript:checkDomain('.$lDom.');')));
    $this -> addDef(fie('gruID', '', 'hidden','', array('class' => 'inp w500')));
  }

  protected function getJs() {
    parent::getJs();
    $lRet = '<script type="text/javascript">';
    $lRet .= 'function checkDomain(aBlackList) {';
      $lRet .= 'var aEl = jQuery("[name=\'val[to_emails]\']");';
      $lRet .= 'var lChk = 0;';
      $lRet .= 'var lVal = aEl.val().split(",");';
      $lRet .= 'var lLength = jQuery(aEl).val().length;';
      $lRet .= 'var lRegEx = new RegExp("^[A-Za-z0-9_.-]+[.][a-zA-z]{2,6}$");'; //searches for Domains like: 5flow.com
      $lRet .= 'for(var i = 0;i< lVal.length; i++) {';
        $lRet .= 'lVal[i].slice(lVal[i].indexOf("@"),lVal[i].length);';
        $lRet .= 'lVal[i] = aEl.val().trim().replace(/.*@/, "");';
        $lRet .= 'if(!lRegEx.test(lVal[i]) || aBlackList.indexOf(lVal[i]) == -1) {';
          $lRet .= 'lChk = 1';
        $lRet .= '}';
      $lRet .= '}';
      
      $lRet .= 'if(lChk === 0 || lLength === 0) {';
        $lRet .= 'jQuery(":submit").removeAttr("disabled");';
      $lRet .= '} else {';
        if (count($this -> mGru) !=0) {
          $lRet .= 'jQuery(":submit").attr("disabled",true);';
        }
      $lRet .= '}';
    $lRet .= '}';
    $lRet .= '</script>';
    return $lRet;
  }
  
  protected function getButtons() {
    $lRet = '<div class="frm" style="padding:16px; text-align:right">'.LF;
    $lRet.= btn(lan('lib.ok'), 'javascript:Flow.checkUsrAvail(); return false;', '<i class="ico-w16 ico-w16-ok"></i>', 'submit').NB;
    $lRet.= btn(lan('lib.cancel'), "go('index.php?act=gru')", '<i class="ico-w16 ico-w16-cancel"></i>');
    $lRet.= '</div>'.LF;
    return $lRet;
  }
}