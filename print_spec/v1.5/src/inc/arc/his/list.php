<?php
class CInc_Arc_His_List extends CHtm_List {

  protected $mCrpId;
  protected $mCrp = Array();

  public function __construct($aMod, $aJobId, $aStage = 'arc') {
    parent::__construct($aStage.'-'.$aMod.'-his');
    $this -> mJobId = $aJobId;
    $this -> mTitle = lan('job-his.menu');
    $this -> mCapCls = 'th1';
    $this -> mLpp = 0; // show all, no paging
    $this -> mSrc = $aMod;

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    if (isset($lCrp[$this -> mSrc])){
      $this -> mCrpId = $lCrp[$this -> mSrc];
      $this -> mCrp = CCor_Res::get('crp', $this -> mCrpId);
    }

    $this -> mOrdLnk = 'index.php?act='.$this -> mMod.'.ord&amp;jobid='.$this -> mJobId.'&amp;fie=';
    $this -> mDelLnk = 'index.php?act='.$this -> mMod.'.del&amp;jobid='.$this -> mJobId.'&amp;id=';
    $this -> mStdLnk = '';

    $this -> mDefaultOrder = '-datum';
    $this -> getPriv('job-his');
    $this -> getPrefs('job-his');

    if ($this -> mCanEdit) {
      $this -> mStdLnk = 'index.php?act='.$this -> mMod.'.edt&amp;jobid='.$this -> mJobId.'&amp;id=';
    }

    $this -> addColumn('more', '', FALSE, array('width' => 16));
    $this -> addCtr();
    $this -> addColumn('typ', '', FALSE, array('width' => 16));
    $this -> addColumn('datum', lan('lib.file.date'), TRUE, array('width' => 50));
    $this -> addColumn('user_name',  lan('lib.user'), TRUE);
    $this -> addColumn('subject',  lan('lib.sbj'), TRUE, array('width' => '50%'));
    $this -> addColumn('msg', lan('lib.msg'), TRUE, array('width' => '50%'));
    #$this -> addColumn('color', ' ');

    if ($this -> mCanInsert) {
      $this -> addBtn(lan('lib.msg.new'), 'go("index.php?act='.$this -> mMod.'.new&jobid='.$this -> mJobId.'")', 'img/ico/16/plus.gif');
      #$this -> addBtn(lan('lib.email.new'), 'go("index.php?act='.$this -> mMod.'.newmail&jobid='.$this -> mJobId.'")', 'img/ico/16/plus.gif');
    }

    $this -> addBtn(lan('lib.expandall'), 'Flow.Std.showAllTr()','img/ico/16/nav-down-lo.gif');
    $this -> addBtn(lan('lib.collapseall'), 'Flow.Std.hideAllTr()','img/ico/16/nav-up-lo.gif');
    #$this -> addBtn('Print all', 'go("index.php?act='.$this -> mMod.'.print&jobid='.$this -> mJobId.'")', 'img/ico/16/print.gif');

    $this -> mIte = new CCor_TblIte('al_job_his h,al_usr u');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> addField('h.id');
    $this -> mIte -> addField('h.datum');
    $this -> mIte -> addField('h.subject');
    $this -> mIte -> addField('h.msg');
    $this -> mIte -> addField('h.add_data');
    $this -> mIte -> addField('h.typ');
    $this -> mIte -> addField('h.to_status');
    $this -> mIte -> addField('u.firstname');
    $this -> mIte -> addField('u.lastname AS user_name');
    $this -> mIte -> addCnd('h.user_id=u.id'); // join
    #$this -> mIte -> addCnd('h.src="'.addslashes($aMod).'"');
    $this -> mIte -> addCnd('h.src_id="'.$this -> mJobId.'"');
    $this -> mIte -> addCnd('h.mand='.intval(MID));

	$this -> mPlain = new CHtm_Fie_Plain();
    if ('datum' == $this -> mOrd) {
      $this -> mExpandNext = TRUE;
    }
    $lUsr = CCor_Usr::getInstance();
    $this -> mDateFmt = $lUsr -> getPref('date.fmt.xxl', lan('lib.datetime.short'));
    
    $this -> addJs();
  }

 protected function getLink() {
    // Webcenter Comments (typ = 1024 ) can not be edit.
    $lVal = $this -> getVal('typ');
    if ($lVal == htWecComment) {
      return "";
    } else {
      return parent::getLink();
    }
  }

 protected function getTdTyp() {
    $lTyp = $this -> getVal('typ');
    $lImg = 'img/his/'.$lTyp.'.gif';

    if (htStatus == $lTyp) { // If Status Change, Show target Status['display']
      $lTo = $this -> getInt('to_status');
      if (empty($this->mCrp)){
        $this -> dbg('No CriticalPath defined',mlError);
      } else{
        foreach ($this-> mCrp as $lRow){
          if ($lTo == $lRow['status']){
			$lImg = CApp_Crpimage::getSrcPath($this->mSrc, 'img/crp/'.$lRow['display'].'b.gif');
            break;
          }
        }
      }
    }
    return $this -> td(img($lImg));
 }

  protected function getTdUser_name() {
    $lVal = cat($this -> getVal('user_name'), $this -> getVal('firstname'), ', ');
    return $this -> tda(htm($lVal));
  }

  protected function getTdDatum() {
    $lVal = $this -> getVal('datum');
    $lDat = new CCor_Datetime($lVal);
    return $this -> tda($lDat -> getFmt($this -> mDateFmt));
  }

  protected function getTdSubject() {
    $lVal = $this -> getVal('subject');
    return $this -> tda(htm($lVal));
  }

  protected function getTdMsg() {
    $lVal = $this -> getVal('msg');
    if (strlen($lVal) > 20) {
      $lVal = substr($lVal, 0, 20).'...';
    }
    return $this -> tda(htm($lVal));
  }

  protected function hasMore() {
    $lVal = trim($this -> getVal('msg'));
    if ('' != $lVal) {
      return true;
    }
    $lVal = trim($this -> getVal('add_data'));
    if ('' != $lVal) {
      return true;
    }
    return false;
  }

  protected function getTdMore() {
    $lRet = '';
    if ($this -> hasMore()) {
      $this -> mMoreId = getNum('t');
      $lRet = '<a class="nav" onclick="Flow.Std.togTr(\''.$this -> mMoreId.'\')">';
      $lRet.= '...</a>';
    }
    return $this -> td($lRet);
  }

  protected function getMoreJob($aVal) {
    if (!isset($aVal['src'])) return;
    if (!isset($aVal['jid'])) return;

    $lSrc = $aVal['src'];
    $lJid = $aVal['jid'];

    $lRet = '<a href="index.php?act=job-'.$lSrc.'.edt&amp;jobid='.$lJid.'" class="nav">';
    $lRet.= htm(lan('job-'.$lSrc.'.menu').' '.$lJid);
    $lRet.= '</a>'.BR;
    return $lRet;
  }

  protected function getMoreAmt($aVal) {
    $lRet = '';
    $lHtb = CCor_Res::get('htb', 'amt');
    if (isset($lHtb[$aVal])) {
      $lRet = '<b>'.htm(lan('job.amend.typ')).'</b> : '.htm($lHtb[$aVal]).BR;
    }
    return $lRet;
  }

  protected function getMoreFil($aVal) {
    $lRet = '<b>'.htm(lan('lib.up.file')).'</b> : '.htm($aVal).BR;
    return $lRet;
  }

  protected function getMoreCause($aVal) {
    $lRet = '<b>'.htm(lan('job.amend.root')).'</b> : '.nl2br(htm($aVal)).BR;
    return $lRet;
  }

  protected function getMoreUpd($aVal) {
    if (empty($aVal)) return '';
    $lFie = CCor_Res::getByKey('alias', 'fie');
    $lFmt = lan('job.changes.format');

    $lRet = '<b>'.htm(lan('job.changes')).'</b> :'.BR;
    foreach ($aVal as $lKey => $lRow) {
      if (!isset($lFie[$lKey])) {
        $this -> dbg('Unknown Alias '.$lKey);
        continue;
      }
      $lDef = $lFie[$lKey];
      $lOld = $this -> mPlain -> getPlain($lDef, $lRow['old']);
      $lNew = $this -> mPlain -> getPlain($lDef, $lRow['new']);
      if ('' == $lOld) $lOld = '""';
      if ('' == $lNew) $lNew = '""';
      $lCap = $lDef['name_'.LAN];
      $lRet.= '- '.htm(sprintf($lFmt, $lCap, $lOld, $lNew)).BR;
    }
    return $lRet;
  }

  protected function afterRow() {
    $lRet = parent::afterRow();
    if ($this -> hasMore()) {
      $lVal = trim($this -> getVal('msg'));
      if ($this -> mExpandNext) {
        $lRet.= '<tr id="'.$this -> mMoreId.'" class="togtr" style="display:table-row">'.LF;
      } else {
        $lRet.= '<tr id="'.$this -> mMoreId.'" class="togtr" style="display:none">'.LF;
      }
      $lRet.= '<td class="td1 ca">&nbsp;</td>'.LF;
      $lRet.= '<td class="td1 p8"'.$this -> getColspan().'>';
      if (!empty($lVal)) {
        $lRet.= nl2br(htm($lVal)).BR;
      }

      $lUsr = CCor_Usr::getInstance();
      $lTyp = $this -> getVal('typ');
      if ($lUsr->canRead('job-his-mails.list') && $lTyp == 1) {
        $lRet.= '<table><tr>---------</tr>'.BR;
        $lRet.= '<tr>'.$this -> getTdMails().'</tr>';
        $lRet.= $this -> afterSubRow();
        $lRet.= '</table>';
      }
      
      
      $lVal = $this -> getVal('add_data');
      if (!empty($lVal)) {
        $lVal = unserialize($lVal);
        foreach ($lVal as $lKey => $lValue) {
          $lFnc = 'getMore'.$lKey;
          if ($this -> hasMethod($lFnc)) {
            $lRet.= $this -> $lFnc($lValue);
          }
        }
      }
      $lRet.= BR.'<div class="w100">';
      $lRet.= '<a href="index.php?act=arc-'.$this -> mSrc.'-his.prnitm';
      $lRet.= '&amp;id='.$this -> getInt('id');
      $lRet.= '&amp;jobid='.$this -> mJobId;
      $lRet.= '" target="_blank" class="nav">';
      $lRet.= img('img/ico/16/print.gif', array('style' => 'float:left'));
      $lRet.= NB.'Print this';
      $lRet.= '</a></span>';
      $lRet.= '</td>'.LF;
      $lRet.= '</tr>'.LF;
    }
    $this -> mExpandNext = FALSE;
    return $lRet;
  }
  
  protected function afterSubRow() {
    $lRet = '<tr style="display:none" id="'.$this -> mMoreId.'">';
    $lRet.= '<td><div id="'.$this->mMoreId.'r"><img src="img/pag/ajx.gif" alt="" /></div></td>';
    $lRet.= '</tr>'.LF;
    return $lRet;
  }
  
  protected function getTdMails() {
    $lPid = $this -> getInt('id');
    $this -> mMoreId = getNum('t');
    $lRet = '<a class="nav" onclick="loadSub(\''.$this -> mMoreId.'\','.$lPid.')">';
    $lTitel = lan('lib.email').' '.lan('lib.list');
    $lRet.= '<u>'.$lTitel.'...</u></a>';
    return $lRet;
  }
  
  protected function addJs() {
    $lJs = 'function loadSub(aId,aJid){';
    $lJs.= 'Flow.Std.togTr(aId);';
    $lJs.= 'lDiv = $(aId).down("div");';
    $lJs.= 'if (!lDiv.hasClassName("loaded")) {';
    $lJs.= 'lDiv.addClassName("loaded");';
    $lJs.= 'lDiv = aId + "r";';
    $lJs.= 'Flow.Std.ajxUpd.defer({div:lDiv,act:"job-'.$this -> mSrc.'-his.sub",hisid:aJid});';
    $lJs.= '}';
    $lJs.= '}';
    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lJs);
  }
  

}