<?php
class CInc_Job_Apl_Mailform extends CHtm_Form {

  public function __construct($aSrc, $aJobId, $aJob, $aFrm = 'his') {
    parent::__construct('job-apl.snewmail', 'New eMail', 'job-apl&src='.$aSrc.'&jobid='.$aJobId);

    $this -> setParam('jobid', $aJobId);
    $this -> setParam('src', $aSrc);
    $this -> setParam('frm', $aFrm);

    $this -> setAtt('class', 'tbl w800');

    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
    $this -> mJob = $aJob;

    $this -> mFieRes = CCor_Res::extract('alias', 'name_'.LAN, 'fie');
    $this -> mUsrRes = CCor_Res::get('usr');

    $lSql = 'SELECT * FROM al_job_apl_loop WHERE 1';
    $lSql.= ' AND src='.esc($this -> mSrc);
    $lSql.= ' AND typ LIKE "apl%"';
    $lSql.= ' AND mand='.intval(MID);
    $lSql.= ' AND jobid='.esc($this -> mJobId);

    $this -> mQry = new CCor_Qry($lSql);
    foreach ($this -> mQry as $lRow) {
      $this -> addUser($lRow);
    }

    $this -> addDef(fie('subject', lan('lib.sbj'), 'string', NULL, array('class' => 'inp', 'style' => 'width: 100%')));
    $this -> addDef(fie('msg', lan('lib.msg'), 'memo', NULL, array('rows' => 24, 'class' => 'inp', 'style' => 'width: 100%')));

    $this -> mTpl = new CApp_Tpl();
    $this -> getTemplate(CCor_Cfg::get('apl.notifytemplate', NULL));
    $this -> mTpl -> setJobValues($aJob);

    $this -> mTpl -> setSubject(preg_replace('/\{bez\.[^\}]*\}/', '', $this -> mTpl -> getSubject()));
    $this -> mTpl -> setSubject(preg_replace('/\{val\.[^\}]*\}/', '', $this -> mTpl -> getSubject()));

    $this -> mTpl -> setBody(preg_replace('/\{bez\.[^\}]*\}/', '', $this -> mTpl -> getBody()));
    $this -> mTpl -> setBody(preg_replace('/\{val\.[^\}]*\}/', '', $this -> mTpl -> getBody()));

    $this -> mTpl -> addUserPat(CCor_Usr::getAuthId(), 'from');

    // START: for Seal only
    if (method_exists('CApp_Sender', 'getSealUrl')) {
      $lSender = new CApp_Sender('usr', array(), $this -> mJob);
      $lSealHome = $lSender -> getSealUrl('act=hom-wel');
      $lSealLink = $lSender -> getSealUrl('act=job-'.$this -> mSrc.'.edt&jobid='.$this -> mJobId.'&_mid='.MID);
      $this -> mTpl -> setPat('seal.home', $lSealHome);
      $this -> mTpl -> setPat('seal.link', $lSealLink);
    }
    // STOP: for Seal only

    $this -> mTpl -> setPat('link', CCor_Cfg::get('base.url').'index.php?act=job-'.$this -> mSrc.'.edt&jobid='.$this -> mJobId.'&_mid='.MID);

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
      foreach ($this -> mPer as $lAli => $lUid) {
        if (isset($this -> mFieRes[$lAli])) {
          $lFie = $this -> mFieRes[$lAli];
        } else {
          $lFie = '';
        }
        $lUsr = $this -> mUsrRes[$lUid];
        $lRet.= '<tr>'.LF;
        $lCheck = ($this -> isChecked($lAli, $lUid)) ? ' checked="checked"' : '';
        $lRet.= '<td>'.LF;
        $lRet.= '<input type="checkbox" name="val[uid][]"'.$lCheck.' value="'.$lUid.'" />'.LF;
        $lRet.= '</td>'.LF;
        $lRet.= '<td>'.htm($lFie).'</td>'.LF;
        $lRet.= '<td>&nbsp;</td>'.LF;
        $lRet.= '<td>'.htm($lUsr['fullname']).'</td>'.LF;
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

  public function addUser($aRow) {
    $lSta = $aRow['status'];

    if ('open' == $lSta) {
      $aId = intval($aRow['id']);
      $lSql = 'SELECT * FROM al_job_apl_states WHERE 1';
      $lSql.= ' AND typ LIKE "apl%"';
      $lSql.= ' AND loop_id='.$aId;

      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lSta = $lRow['status'];
        if (0 == $lSta) {
          $this -> mPer[$lRow['name']] = $lRow['user_id'];
        }
      }
    }
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
    return false;
  }

  protected function getDefaultSubject() {
    $lRet = lan('app.remind.pre').' "';
    $lRet.= lan('job-'.$this -> mSrc.'.menu').' '.jid($this -> mJobId, TRUE).' '.$this -> mJob['stichw'];
    $lRet.= '" '.lan('app.remind.post');
    return $lRet;
  }

  protected function getDefaultMessage() {
    $lUsr = CCor_Usr::getInstance();
    $lRet = LF.LF.LF;
    $lRet.= 'Link '.CCor_Cfg::get('base.url').'index.php?act=job-'.$this -> mSrc.'.edt';
    $lRet.= '&jobid='.$this -> mJobId.LF.LF.LF;
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
}