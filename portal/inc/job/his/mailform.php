<?php
class CInc_Job_His_Mailform extends CHtm_Form {

  public function __construct($aSrc, $aJobId, $aJob, $aFrm = 'his', $aMailRequestId = 0, $stage = 'job') {
    $lUsr = CCor_Usr::getInstance();
    $lRedirectCancel = $stage.'-'.$aSrc.'-his&jobid='.$aJobId;
    if (!($lUsr -> canRead('job-his'))) {
      $lRedirectCancel = $stage.'-'.$aSrc.'.edt&jobid='.$aJobId;
    }

    parent::__construct($stage.'-'.$aSrc.'-his.snewmail', 'New eMail', $lRedirectCancel);

    $this -> setParam('jobid', $aJobId);
    $this -> setParam('src', $aSrc);
    $this -> setParam('frm', $aFrm);

    $this -> setAtt('class', 'tbl w800');

    $this -> mSrc = $aSrc;
    $this -> mJid = $aJobId;
    $this -> mJob = $aJob;
    $this -> mMailRequestId = $aMailRequestId;
    $this -> setParam('emlid', $this -> mMailRequestId);

    $this -> mFieRes = CCor_Res::extract('alias', 'name_'.LAN, 'fie');
    $this -> mUsrRes = CCor_Res::get('usr');

    if ($lUsr -> canRead('notification-center')) {
      #$this -> addDef(fie('responce', lan('lib.need_response'), 'boolean', NULL));
      if ($this -> mMailRequestId != 0) {
        $lMailInfo = $this -> loadEmailinfo($this -> mMailRequestId);
      }
    }

    $lRolePerson = CCor_Cfg::get('job.notifytemplate.to');
    if (!empty($lRolePerson)) {
      foreach ($lRolePerson as $lPerson) {
        $this -> addPerson($lPerson);
      }
    } else {
      $this -> addPerson('per_prj_verantwortlich');
      $this -> addPerson('per_cs');
      $this -> addPerson('per_bm');
      $this -> addPerson('per_ba');
      $this -> addPerson('per_pc');
    }


    $this -> addDef(fie('subject', lan('lib.sbj'), 'string', NULL, array('class' => 'inp', 'style' => 'width: 100%')));

    $this -> addDef(fie('msg', lan('lib.msg'), 'memo', NULL, array('rows' => 24, 'class' => 'inp', 'style' => 'width: 100%')));



    $this -> mTpl = new CApp_Tpl();
    $this -> getTemplate(CCor_Cfg::getFallback('job-'.$aSrc.'.notifytemplate', 'job.notifytemplate', NULL));
    $this -> mTpl -> setJobValues($aJob);

    $this -> mTpl -> setSubject(preg_replace('/\{bez\.[^\}]*\}/', '', $this -> mTpl -> getSubject()));
    $this -> mTpl -> setSubject(preg_replace('/\{val\.[^\}]*\}/', '', $this -> mTpl -> getSubject()));

    $this -> mTpl -> setBody(preg_replace('/\{bez\.[^\}]*\}/', '', $this -> mTpl -> getBody()));
    $this -> mTpl -> setBody(preg_replace('/\{val\.[^\}]*\}/', '', $this -> mTpl -> getBody()));

    $this -> mTpl -> addUserPat(CCor_Usr::getAuthId(), 'from');

    $lJobFiles = new CInc_Utl_Fil_Mod($this -> mJob,$this -> mJid);
    $lTplBody = $this -> mTpl -> getBody();
    $lDocLink = (strpos($lTplBody,'deeplink.files.doc') !== false) ? $lJobFiles->getFolderDeepLinks('doc') : '-';
    $lPdfLink = (strpos($lTplBody,'deeplink.files.pdf') !== false) ? $lJobFiles->getFolderDeepLinks('pdf') : '-';
    $lWecLink = (strpos($lTplBody,'deeplink.files.wec') !== false) ? $lJobFiles->getFolderDeepLinks('wec') : '-';
    $this -> mTpl -> setPat('deeplink.files.doc', $lDocLink);
    $this -> mTpl -> setPat('deeplink.files.pdf', $lPdfLink);
    $this -> mTpl -> setPat('deeplink.files.wec', $lWecLink);

    // START: for Seal only
    if (method_exists('CApp_Sender', 'getSealUrl')) {
      $lSender = new CApp_Sender('usr', array(), $this -> mJob);
      $lSealHome = $lSender -> getSealUrl('act=hom-wel');
      $lSealLink = $lSender -> getSealUrl('act=job-'.$this -> mSrc.'.edt&jobid='.$this -> mJid.'&_mid='.MID);
      $this -> mTpl -> setPat('seal.home', $lSealHome);
      $this -> mTpl -> setPat('seal.link', $lSealLink);
    }
    // STOP: for Seal only

    $this -> mTpl -> setPat('link', CCor_Cfg::get('base.url').'index.php?act=job-'.$this -> mSrc.'.edt&jobid='.$this -> mJid.'&_mid='.MID);

    $this -> setVal('subject', $this -> getSubject());
    $this -> setVal('msg', $this -> getMessage());
  }

  protected function getForm() {
    $lRet = '<div class="frm" style="padding:16px;">'.LF;
    $lRet.= '<table cellpadding="4" cellspacing="0" border="0" id="table_form" style="width: 100%">'.LF;

    $lRet.= $this -> getFieldForm();

    $lRet.= '</table>'.LF;
    $lRet.= '</div>'.LF;

    return $lRet;
  }

  protected function getFieldForm() {
    $lRet = '';
    if (!empty($this -> mPer)) {
      $lRet.= '<tr id="tr_form">'.LF;
      $lRet.= '<td style="width: 10%; vertical-align: top;">To...</td>'.LF;
      $lRet.= '<td style="width: 90%; padding: 0px;">'.LF;
      $lRet.= '<table cellpadding="2" cellspacing="0" border="0">'.LF;

      if ($this -> mMailRequestId > 0 AND $this -> mMailInfo['response'] == 1) {
        $lSenderNameNeedResponse = htm($this -> mUsrRes[$this -> mMailInfo['sender_id']]['fullname']);
        $lRet.= '<tr><td><input type="radio" name="val[uid][]" disabled="disabled" checked="checked" value="'.$this -> mMailInfo['sender_id'].'" /></td><td></td><td></td><td>'.$lSenderNameNeedResponse.'</td></tr>'.LF;
        $lRet.= '<input type="hidden" value="'.$this -> mMailInfo['sender_id'].'" name="val[uid][]">';
      }

      foreach ($this -> mPer as $lAli => $lUid) {
        $lFie = $this -> mFieRes[$lAli];
        $lUsr = $this -> mUsrRes[$lUid];
        $lResponse = $this -> mMailInfo['response'];
        $lRet.= '<tr>'.LF;
        $lCheck = ($this -> isChecked($lAli, $lUid)) ? ' checked="checked"' : '';
        $lRet.= '<td>'.LF;
        $lRet.= '<input type="checkbox" name="val[uid][]"'.$lCheck.' value="'.$lUid.'" />'.LF;
        $lRet.= '</td>'.LF;
        $lRet.= '<td>'.htm($lFie).'</td>'.LF;
        $lRet.= '<td>&nbsp;</td>'.LF;
        $lRet.= '<td>'.htm($lUsr['fullname']).'</td>'.LF;
        $lRet.= '<td>'.lan('lib.need_response').'<input type="checkbox" name="val[response]" /></td>';
        $lRet.= '</tr>'.LF;
      }
      $lRet.= '</tr>'.LF;
      $lRet.= '</table>'.LF;

      $lRet.= '</td>'.LF;
      $lRet.= '</tr>'.LF;
    }
//     $lRet.= parent::getFieldForm();
    if (!empty($this -> mFie)) {
      foreach ($this -> mFie as $lAlias => $lDefInfo) {
        $lDef = $lDefInfo;
        $lRet.= '<tr id="'.$lAlias.'">'.LF;
        if ($this -> mAltLan == FALSE) {
          $lRet.= '<td class="nw">'.htm($lDef['name_'.LAN]).'</td>'.LF;
        } ELSE {
          $lRet.= '<td class="nw">'.htm(lan($lAlias)).'</td>'.LF;
        }
        $lRet.= '<td>'.LF;
        $lRetImg = '';
        if (isset($lDef['_img'])) {
          if (!empty($lDef['_img'])) {
            $lRetImg = ' '.$lDef['_img'].LF;
          }
          $lDef -> offsetUnset('_img');
        }
        $lRetHr = '';
        if (isset($lDef['_hr'])) {
          if (isset($lDef['_hr'])) {
            $lRetHr = '<tr><td colspan="2"><p><hr /></p></td></tr>'.LF;
          }
          $lDef -> offsetUnset('_hr');
        }
        $lRet.= $this -> mFac -> getInput($lDef, $this -> getVal($lAlias), $this -> mReadOnly);
        $lRet.= $lRetImg;
        $lRet.= '</td>'.LF;
        $lRet.= '</tr>'.LF;
        $lRet.= $lRetHr;
      }
    }

    return $lRet;
  }

  protected function addPerson($aAlias) {
    $lPer = $this -> mJob[$aAlias];
    if (empty($lPer)) return;
    $this -> mPer[$aAlias] = $lPer;
  }

  protected function getTemplate($aTplId) {
    $lTid = intval($aTplId);
    if (!empty($lTid)) {
      $this -> mTpl -> loadTemplate($lTid);
    } else {
      $this -> mTpl -> setSubject($this -> getDefaultSubject());
      $this -> mTpl -> setBody($this -> getDefaultMessage());
    }
  }

  protected function getSubject() {
    return $this -> mTpl -> getSubject();
  }

  protected function getMessage() {
    return $this -> mTpl -> getBody();
  }

  protected function isChecked($aAlias, $aUserId) {
    return true;
  }

  protected function getDefaultSubject() {
    $lRet = lan('job-'.$this -> mSrc.'.menu').' '.jid($this -> mJid, TRUE).' '.$this -> mJob['stichw'];
    return $lRet;
  }

  protected function getDefaultMessage() {
    $lUsr = CCor_Usr::getInstance();
    $lRet = LF.LF.LF;
    $lRet.= 'Link '.CCor_Cfg::get('base.url').'index.php?act=job-'.$this -> mSrc.'.edt';
    $lRet.= '&jobid='.$this -> mJid.'&_mid='.MID.LF.LF.LF;
    $lRet.= 'Kind regards,'.LF.LF;
    $lRet.= $lUsr -> getVal('first_lastname').LF;
    $lRet.= '----------'.LF;
    $lCom = $lUsr -> getVal('company');
    if (!empty($lCom)) {
      $lRet.= $lCom.LF;
    }
    $lTel = $lUsr -> getVal('phone');
    if (!empty($lTel)) {
      $lRet.= 'Phone '.$lTel;
    }
    return $lRet;
  }

  protected function getJs() {
    $lRet = '<script type="text/javascript">'.LF;
    $lRet.= 'jQuery(document).ready('.LF;
    $lRet.= 'function () {'.LF;
    $lRet.= 'Flow.mail.load();'.LF;
    $lRet.= '}'.LF;
    $lRet.= ');'.LF;
    $lRet.= '</script>'.LF;
    return $lRet;
  }

  protected function getButtons($aBtnAtt = array(), $aBtnTyp = 'button') {
    $lRet = '<div class="btnPnl">'.LF;
    $lRet.= btn(lan('lib.send'), '', '<i class="ico-w16 ico-w16-ok"></i>', 'submit', $aBtnAtt).NB;
    if (!empty($this -> mCancel)) {
      $lRet.= btn(lan('lib.discard'), "go('index.php?act=".$this -> mCancel."')", '<i class="ico-w16 ico-w16-cancel"></i>', $aBtnTyp, $aBtnAtt);
    }
    $lRet.= '</div>'.LF;
    return $lRet;
  }

  protected function loadEmailinfo($aMailId) {
    if (empty($aMailId)) return;
    $lSql = 'SELECT * FROM al_sys_mails WHERE id='.esc($aMailId);
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> mMailInfo = $lRow;
    }
  }
}
