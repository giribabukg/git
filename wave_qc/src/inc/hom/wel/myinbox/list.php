<?php
class CInc_Hom_Wel_Myinbox_List extends CHtm_List {

  public function __construct() {
    parent::__construct('hom-wel-myinbox');

    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('lib.email').' '.lan('lib.list');
    $this -> mStdLnk = 'index.php?act=job-';
    $this -> getPriv('notification-center');

    $this -> addCtr();
    $this -> addColumn('mail_state', '', TRUE);
    $this -> addColumn('mail_entry', lan('lib.createdate'), TRUE);
    $this -> addColumn('mail_date', lan('lib.emaildate'), TRUE);
    $this -> addColumn('from_name', lan('lib.from'), TRUE);
    $this -> addColumn('mail_subject', lan('lib.sbj'), TRUE);
    $this -> addColumn('mail_type', lan('lib.email.type'), TRUE);
    $this -> addColumn('response', lan('lib.email.response'), TRUE);
    $this -> addColumn('more', '', FALSE);
    $this -> addColumn('open', '', FALSE);

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_sys_mails');
    $this -> mIte -> addCnd('mand='.MID);
    $lUid = CCor_Usr::getAuthId();
    $this -> mIte -> addCnd('receiver_id = '. $lUid);
    $this -> mIte -> addCnd('mail_type NOT IN (0, 1) ');

    if (!empty($this -> mSer)) {
      if(!empty($this ->mSer['msg'])){
        $lVal = ' LIKE "%'.addslashes($this -> mSer['msg']).'%" ';
        $lCnd = '(mail_subject'.$lVal.' OR ';
        $lCnd.= 'from_name'.$lVal.' OR ';
        $lCnd.= 'to_name'.$lVal.' OR ';
        $lCnd.= 'mail_body'.$lVal.')';
      }
      if(!empty($this -> mSer['type'])){
        if($this -> mSer['type'] == 1){
          $this -> mIte -> addCnd('response = "1"');
        }else{
          $this -> mIte -> addCnd('mail_type = "'.addslashes($this -> mSer['type']).'"');
        }
      }
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
    $lRet.= '<input type="hidden" name="act" value="hom-wel-myinbox.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;

    $lVal = (isset($this -> mSer['msg'])) ? htm($this -> mSer['msg']) : '';
    $lRet.= '<td><input type="text" name="val[msg]" class="inp" value="'.$lVal.'" /></td>'.LF;

    $lTyp = (isset($this -> mSer['type'])) ? $this -> mSer['type'] : '';
    $lArr = array('1' => lan('lib.email.response'), mailJobEvents => htm(lan('lib.email.mailJobEvents')), mailAplInvite => htm(lan('lib.email.mailAplInvite')), mailAplReminder => htm(lan('lib.email.mailAplReminder')), mailJobNotification => htm(lan('lib.email.mailJobNotification')));
    $lRet.= '<td>&nbsp;'.htm(lan('lib.flags')).'</td>';
    $lRet.= '<td>';
    $lRet.= '<select name="val[type]">';
    $lRet.= '<option value="">&nbsp;</option>';
    foreach ($lArr as $lKey => $lVal) {
      $lRet.= '<option value="'.htm($lKey).'"';
      if ($lKey == $lTyp) $lRet.= ' selected="selected"';
      $lRet.='>'.htm($lVal).'</option>';
    }
    $lRet.= '</select>';
    $lRet.= '</td>';

    $lRet.= '<td>&nbsp;'.btn(lan('lib.search'), '', '', 'submit').'</td>';

    if (!empty($this -> mSer) || $this -> mSer['type']) {
      $lRet.= '<td>'.btn(lan('lib.all'), 'go("index.php?act=hom-wel-myinbox.clser")').'</td>';
    }

    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getTdMail_entry() {
    $lDat = $this -> getVal('mail_entry');
    $lRet = substr($lDat, 8, 2).'.'.substr($lDat, 5, 2).'.'.substr($lDat, 0, 4).' '.substr($lDat, 11, 5);
    $lRet = '<a href="'.$this -> mStdLnk.$this -> getVal('src').'.edt&amp;jobid='.$this -> getVal('jobid').'" class="td">'.htm($lRet).'</a>';
    return $this -> td($lRet);
  }

  protected function getTdMail_date() {
    $lDat = $this -> getVal('mail_date');
    $lRet = substr($lDat, 8, 2).'.'.substr($lDat, 5, 2).'.'.substr($lDat, 0, 4).' '.substr($lDat, 11, 5);
    $lRet = '<a href="'.$this -> mStdLnk.$this -> getVal('src').'.edt&amp;jobid='.$this -> getVal('jobid').'" class="td">'.htm($lRet).'</a>';
    return $this -> td($lRet);
  }

  protected function getTdFrom_name() {
    $lNam = $this -> getVal('from_name');
    $lAdr = $this -> getVal('from_mail');
    if (empty($lNam)) {
      $lNam = $lAdr;
    }
    $lRet = '<a href="'.$this -> mStdLnk.$this -> getVal('src').'.edt&amp;jobid='.$this -> getVal('jobid').'" class="td">'.htm($lNam).'</a>';
    return $this -> td($lRet);
  }

  protected function getTdMail_subject() {
    $lSub = $this -> getVal('mail_subject');
    $lRet = '<a href="'.$this -> mStdLnk.$this -> getVal('src').'.edt&amp;jobid='.$this -> getVal('jobid').'" class="td">'.htm($lSub).'</a>';
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

  protected function getTdResponse() {
    $lVal = $this -> getVal('response');
    $lRet = '<a href="'.$this -> mStdLnk.$this -> getVal('src').'.edt&amp;jobid='.$this -> getVal('jobid').'" class="td ac">';
    if ($lVal == 1 ){
      $lRet.= img('img/ico/16/response.png').'</a>';
    }else{
      $lRet.= '&nbsp;</a>';
    }
    return $this -> td($lRet);

  }

  protected function getTdMail_type() {
    $lVal = $this -> getVal('mail_type');
    $lRet = '<a href="'.$this -> mStdLnk.$this -> getVal('src').'.edt&amp;jobid='.$this -> getVal('jobid').'" class="td ac">';
    switch ($lVal){
      case 2: $lRet.= img('img/ico/16/jobevent.png').'</a>'; break;
      case 3: $lRet.= img('img/ico/16/apl.png').'</a>'; break;
      case 4: $lRet.= img('img/ico/16/aplreminder.png').'</a>'; break;
      case 5: $lRet.= img('img/ico/16/jobinfo.png').'</a>'; break;
      default:$lRet.= '</a>'; break;
    }

    return $this -> td($lRet);

  }

  protected function getTdOpen() {
    $lVal = $this -> getVal('response');
    $lPara = ($lVal == 1) ? '&emlid='.$this -> getVal('id') : '';
    $lRet = '<a class="nav" href="index.php?act=job-rep-his.newmail&jobid='.$this -> getVal('jobid').'&src='.$this -> getVal('src').'&frm=hom'.$lPara.'">';
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
    $lVal = trim($this -> getVal('mail_body'));
    $lRet.= '<pre style="white-space:pre-wrap">'.htm($lVal).'</pre>';
    $lRet.= '</td>'.LF;
    $lRet.= '</tr>'.LF;
    return $lRet;
  }




}