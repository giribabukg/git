<?php
class CInc_Sys_Mail_List extends CHtm_List {

  public function __construct() {
    parent::__construct('sys-mail');

    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('lib.email').' '.lan('lib.list');
    $this -> mStdLnk = '';

    $this -> getPriv('log');

    $this -> addCtr();
    $this -> addColumn('mail_state', '', TRUE);
    $this -> addColumn('mail_entry', lan('lib.createdate'), TRUE);
    $this -> addColumn('mail_date', lan('lib.file.time.modification'), TRUE);
    $this -> addColumn('from_name', lan('lib.from'), TRUE);
    $this -> addColumn('to_name', lan('lib.to'), TRUE);
    $this -> addColumn('mail_subject', lan('lib.sbj'), TRUE, array('width' => '100%'));
    $this -> addColumn('open', '', FALSE);
    $this -> addColumn('more', '', FALSE);
    if ($this -> mCanDelete) {
      $this -> addDel();
      $this -> addBtn(lan('lib.del.all'), 'javascript:Flow.Std.cnf("index.php?act=sys_mail.delall", "cnfDel")', '<i class="ico-w16 ico-w16-del"></i>');
    }
    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_sys_mails');
    $this -> mIte -> addCnd('mand='.MID);

    if (!empty($this -> mSer['msg'])) {
      $lVal = ' LIKE "%'.addslashes($this -> mSer['msg']).'%" ';
      $lCnd = '(mail_subject'.$lVal.' OR ';
      $lCnd.= 'from_name'.$lVal.' OR ';
      $lCnd.= 'to_name'.$lVal.' OR ';
      $lCnd.= 'mail_body'.$lVal.')';
      $lCnd.= 'AND mand='.MID;
      $this -> mIte -> addCnd($lCnd);
    }

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);

    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
    $this -> addPanel('sca', '| '.htmlan('lib.search'));
    $this -> addPanel('ser', $this -> getSearchForm());
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="sys-mail.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['msg'])) ? htm($this -> mSer['msg']) : '';
    $lRet.= '<td><input type="text" name="val[msg]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'), '', '', 'submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.all'), 'go("index.php?act=sys-mail.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getTdMail_entry() {
    $lDat = $this -> getVal('mail_entry');
    $lRet = substr($lDat, 8, 2).'.'.substr($lDat, 5, 2).'.'.substr($lDat, 0, 4).' '.substr($lDat, 11, 5);
    return $this -> td($lRet);
  }

  protected function getTdMail_date() {
    $lDat = $this -> getVal('mail_date');
    $lRet = substr($lDat, 8, 2).'.'.substr($lDat, 5, 2).'.'.substr($lDat, 0, 4).' '.substr($lDat, 11, 5);
    return $this -> td($lRet);
  }

  protected function getTdFrom_name() {
    $lNam = $this -> getVal('from_name');
    $lAdr = $this -> getVal('from_mail');
    if (empty($lNam)) {
      $lNam = $lAdr;
    }
    $lRet = '<a href="mailto:'.htm($lAdr).'" class="nav">'.htm($lNam).'</a>';
    return $this -> td($lRet);
  }

  protected function getTdTo_name() {
    $lNam = $this -> getVal('to_name');
    $lAdr = $this -> getVal('to_mail');
    if (empty($lNam)) {
      $lNam = $lAdr;
    }
    $lRet = '<a href="mailto:'.htm($lAdr).'" class="nav">'.htm($lNam).'</a>';
    return $this -> td($lRet);
  }

  protected function getTdMail_state() {
    $lVal = $this -> getVal('mail_state');
    if (1 == $lVal) {
      $lImg = 'ok';
    } else {
      $lImg = 'ml-'.$lVal;
    }
    return $this -> td(img('img/ico/16/'.$lImg.'.gif'));
  }

  protected function getTdOpen() {
    $lRet = '';
    $lRet = '<a class="nav" href="index.php?act=utl-eml.get&amp;id='.$this -> getVal('id').'&amp;fn=email.eml">';
    $lRet.= img('img/ico/16/email.gif').'</a>';
    return $this -> td($lRet);
  }

  protected function getTdMore() {
    $lRet = '';
    $this -> mMoreId = getNum('t');
    $lRet = '<a class="nav" onclick="Flow.Std.togTr(\''.$this -> mMoreId.'\')">';
    $lRet.= '...</a>';
    return $this -> td($lRet);
  }

  protected function afterRow() {
    $lRet = parent::afterRow();
    $lRet.= '<tr id="'.$this -> mMoreId.'" style="display:none">'.LF;
    $lRet.= '<td class="td1 ca">&nbsp;</td>'.LF;
    $lRet.= '<td class="td1 p8"'.$this -> getColspan().'>';
    $lRet.= '<p><b>State</b> ['.htm($this -> getVal('mail_errno').'] '.$this -> getVal('mail_errmsg'));
    $lSta = $this -> getVal('mail_state');
    $lRet.= ', ';
    switch($lSta) {
      case mlInfo:
        $lRet.= lan('lib.email.sent'); // Sent
        break;
      case mlWarn:
        $lRet.= lan('lib.email.queued'); // Queued
        break;
      case mlError:
        $lRet.= lan('lib.email.tmperr'); // Temporary Error
        break;
      case mlFatal:
        $lRet.= lan('lib.email.faterr'); // Fatal, will not resend
        break;
      case mlNoSending:
        $lRet.= lan('lib.email.nosending'); // Emaildelivery disabled: Email will not be sent
        break;
      case mlWaiting:
        $lRet.= lan('lib.email.waiting'); // Email will be sent later
        break;
    }
    $lRet.= '</p>';

    $lVal = trim($this -> getVal('mail_header').$this -> getVal('mail_body'));
    $lRet.= '<pre>'.htm($lVal).'</pre>';
    $lRet.= '</td>'.LF;
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

}