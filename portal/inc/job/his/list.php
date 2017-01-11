<?php
class CInc_Job_His_List extends CHtm_List {

  /**
   * Object used for translation of job values
   *
   * @var CHtm_Fie_Plain
   */
  protected $mPlain;
  protected $mCrpId;
  protected $mCrp = Array();

  public function __construct($aMod, $aJobId, $aStage = 'job', $aJobHisType = FALSE) {
    parent::__construct($aStage.'-'.$aMod.'-his');
    $this -> mJobId = $aJobId;
    $this -> mStage = $aStage;
    $this -> mTitle = lan('job-his.menu');
    $this -> mJobHisTyp = $aJobHisType;
    $this -> mCapCls = 'th1';
    $this -> mLpp = 0; // show all, no paging
    $this -> mSrc = $aMod;

    $lUsr = CCor_Usr::getInstance();

    $this->mUseTimezone = CCor_Cfg::get('sys.timezone.pref', false);
    $this->mPrefTimezone = $lUsr->getPref('sys.timezone', false);
    if (!$this->mPrefTimezone) {
      $this->mUseTimezone = false;
    }

    if ($this->mUseTimezone) {
      try {
        $date = new Zend_Date();
        $date->setTimezone($this->mPrefTimezone);
      } catch (Exception $ex) {
        $this->mUseTimezone = false;
      }
    }

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
    $this -> addColumn('datum', lan('lib.file.time.modification'), TRUE, array('width' => 50));
    if ($this->mUseTimezone) {
      $this -> addColumn('user_date', lan('lib.tz.date'), TRUE, array('width' => 50));
    }

    $lShowSignature = CCor_Cfg::get('job.his.signature', false);
    if ($lShowSignature) {
      $this -> addColumn('signature_id',  '', false, array('width' => '16'));
    }
    $this -> addColumn('user_name',  lan('lib.user'), TRUE);
    $this -> addColumn('subject',  lan('lib.sbj'), TRUE, array('width' => '50%'));
    $this -> addColumn('msg',  lan('lib.msg'), TRUE, array('width' => '50%'));
    #$this -> addColumn('color', ' ');

    if ($this -> mCanInsert) {
      $this -> addBtn(lan('lib.msg.new'), 'go("index.php?act='.$this -> mMod.'.new&jobid='.$this -> mJobId.'")', '<i class="ico-w16 ico-w16-plus"></i>');
      $this -> addBtn(lan('lib.email.new'), 'go("index.php?act='.$this -> mMod.'.newmail&jobid='.$this -> mJobId.'&src='.$this -> mSrc.'&frm=his")', '<i class="ico-w16 ico-w16-plus"></i>');
    }
    $this -> addBtn(lan('lib.expandall'), 'Flow.Std.showAllTr()', '<i class="ico-w16 ico-w16-nav-down-lo"></i>');
    $this -> addBtn(lan('lib.collapseall'), 'Flow.Std.hideAllTr()', '<i class="ico-w16 ico-w16-nav-up-lo"></i>');
    $lWcViewer = CCor_Cfg::get('wec.available', TRUE);
    if (!in_array($this -> mSrc, array('pro', 'sku')) && $lWcViewer) {
      $this -> addBtn(lan('wec.replication'), 'go("index.php?act='.$this -> mMod.'.updwec&jobid='.$this -> mJobId.'&src='.$this -> mSrc.'&da=true")', '<i class="ico-w16 ico-w16-plus"></i>');
    }

    $this -> addPanel('fil', $this -> getFilterMenu($this -> mJobHisTyp));

    #$this -> addBtn('Print all', 'go("index.php?act='.$this -> mMod.'.print&jobid='.$this -> mJobId.'")', 'img/ico/16/print.gif');

    $this -> mIte = new CCor_TblIte('al_job_his h,al_usr u');
    $lOrd = ('datum' == $this->mOrd) ? 'datum' : $this->mOrd;
    $this -> mIte -> setOrder($lOrd, $this -> mDir);
	$this -> mIte -> set2Order('id', $this -> mDir);
    $this -> mIte -> addField('h.id');
    $this -> mIte -> addField('h.datum');
    $this -> mIte -> addField('h.subject');
	$this -> mIte -> addField('h.src');
    $this -> mIte -> addField('h.msg');
    $this -> mIte -> addField('h.add_data');
    $this -> mIte -> addField('h.typ');
    $this -> mIte -> addField('h.to_status');
    $this -> mIte -> addField('u.firstname');
    $this -> mIte -> addField('u.lastname AS user_name');
    $this -> mIte -> addField('h.signature_id');
    $this -> mIte -> addCnd('h.user_id=u.id'); // join
    $this -> mIte -> addCnd('h.src="'.addslashes($aMod).'"');
    $this -> mIte -> addCnd('h.src_id="'.$this -> mJobId.'"');
    $this -> mIte -> addCnd('h.typ<='.esc(htAnsweredQuestion));
	$this -> mIte -> addCnd('h.mand='.intval(MID));

	if($this -> mJobHisTyp){
	$this -> mIte -> addCnd('h.typ='.$this -> mJobHisTyp);
	}

    if (in_array($this -> mOrd, array('datum', 'user_date'))) {
      $this -> mExpandNext = TRUE;
    }

    $this -> mPlain = new CHtm_Fie_Plain();

    $this -> mDateFmt = $lUsr -> getPref('date.fmt.xxl', lan('lib.datetime.short'));
    $this -> addJs();
  }

   protected function getFilterMenu($aJobHisType) {
    $this -> addBtn(lan('lib.searchFieldType'), '', '<i class="ico-w16 ico-w16-search"></i>');
    $lRet = '';
    $lRet.= '<form action="index.php" method="GET">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mMod.'.filter" />'.LF;
    $lRet.= '<input type="hidden" name="jobid" value="'.$this -> mJobId.'" />'.LF;
    $lRet.= '<select name="filterBy" size="1" onchange="this.form.submit();">'.LF;

    $lArr = array('' => '['.lan('lib.all').']');
    $lSql = 'SELECT DISTINCT(typ) FROM al_job_his WHERE src_id="'.$this->mJobId.'"';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lArr[$lRow['typ']] = lan('job.his.typ.'.$lRow['typ']);
    }
    $lFil = $aJobHisType;
    foreach ($lArr as $lKey => $lVal) {
      $lRet.= '<option value="'.htm($lKey).'" ';
      if ($lKey == $lFil) {
        $lRet.= 'selected="selected"';
      }
      $lRet.= '>'.htm($lVal).'</option>'.LF;
    }
    $lRet.= '</select>'.LF;
    $lRet.= '</form>';
    return $lRet;
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
	$lSrc = $this->getVal('src');
    $lImg = 'img/his/'.$lTyp.'.gif';

    $lCrpId = CCor_Res::extract('code', 'id', 'crpmaster');

    if (htStatus == $lTyp) { // If Status Change, Show target Status['display']
      $lTo = $this -> getInt('to_status');
      if (empty($this->mCrp)){
        $this -> dbg('No CriticalPath defined',mlError);
      } else{
		$lId = $lCrpId[$lSrc];
		$lCrp = CCor_Res::get('crp', $lId);

		foreach($lCrp  as $lRow){
          if ($lTo == $lRow['status']){
            $lImg = CApp_Crpimage::getSrcPath($lSrc, 'img/crp/'.$lRow['display'].'b.gif');
            break;
          }
        }
      }
    }

    $lVal = $this -> getVal('subject');
    if (!empty($lTo)) {
      return $this -> td(img($lImg, array('onmouseover' => 'Flow.crpTip(this, '.$lTo.', true)', 'onmouseout' => 'Flow.hideTip()')));
    } else {
      return $this -> td(img($lImg, array('onmouseover' => 'Flow.hisTip(this, '.$lTyp.', \''.$lVal.'\')', 'onmouseout' => 'Flow.hideTip()')));

    }
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

  protected function getTdUser_date() {
    $lVal = $this -> getVal('datum');

    if ($this->mUseTimezone) {
      $date = new Zend_Date($lVal, Zend_Date::ISO_8601);
      $date->setTimezone($this->mPrefTimezone);
      $lVal = $date->get('YYYY-MM-dd HH:mm:ss');
    }
    $lDat = new CCor_Datetime($lVal);

    return $this -> tda($lDat -> getFmt($this -> mDateFmt));
  }


  protected function getTdSubject() {
    $lVal = $this -> getVal('subject');

    $lTpl = new CCor_Tpl();
    $lTpl -> setDoc($lVal);
    $lTpl -> setLang(LAN);
    return $this -> tda(htm($lTpl -> getContent()));
  }

  protected function getTdMsg() {
    $lVal = $this -> getVal('msg');
    if (mb_strlen($lVal, 'UTF-8') > 20) {
      $lVal = mb_substr($lVal, 0, 20, 'UTF-8').'...';
    }
    return $this -> tda($lVal);
  }

  protected function getTdSignature_Id() {
    $lRet = '';
    if ($this->getCurInt() >0) {
      $lRet = img('img/ico/16/edit.gif');
    }
    return $this -> tda($lRet);
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
      $lRet = '<b>'.htm(lan('job.amend.type')).'</b> : '.htm($lHtb[$aVal]).BR;
    }
    return $lRet;
  }

  protected function getMoreFil($aVal) {
    $lRet = '<b>'.htm(lan('lib.file.name')).'</b> : '.htm($aVal).BR;
    return $lRet;
  }

  protected function getMoreCause($aVal) {
    $lRet = '<b>'.htm(lan('job.amend.root')).'</b> : '.nl2br(htm($aVal)).BR;
    return $lRet;
  }

  protected function getMorePer($aVal) {
    $lRet = '<b>'.htm(lan('lib.initiator')).'</b> : '.nl2br(htm($aVal)).BR;
    return $lRet;
  }

  protected function getMoreAk($aVal) {
    $lRet = '<b>'.htm(lan('job.amend.job')).'</b> : '.substr($aVal,-3).BR;
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

  protected function getMoreJobQuest($aVal) {
    $lRet = '<table><tr><td style="padding-right:10px"><i class="ico-w16 ico-w16-flag-0'.$aVal['state'].'"></i></td>';
    $lRet .= '<td><b>'.htm(lan('lib.question')).'</b> : '.nl2br(htm($aVal["quest"])).BR;
    $lRet .= '<b>'.htm(lan('lib.answer')).'</b> : '.nl2br(htm($aVal["answ"])).'</td></tr></table>';

    return $lRet;
  }

  protected function getMoreUpdTo($aVal) {
    if (empty($aVal)) return '';
    $lFie = CCor_Res::getByKey('alias', 'fie');
    $lFmt = lan('job.changes.format.to');

    $lRet = '<b>'.htm(lan('job.changes')).'</b> :'.BR;
    foreach ($aVal as $lKey => $lRow) {
      if (!isset($lFie[$lKey])) {
        $this -> dbg('Unknown Alias '.$lKey);
        continue;
      }
      $lDef = $lFie[$lKey];
      $lNew = $this -> mPlain -> getPlain($lDef, $lRow['new']);
      if ('' == $lNew) $lNew = '""';
      $lCap = $lDef['name_'.LAN];
      $lRet.= '- '.htm(sprintf($lFmt, $lCap, $lNew)).BR;
    }
    return $lRet;
  }

  protected function afterRow() {
    $lRet = parent::afterRow();
    if ($this -> hasMore()) {
      if ($this -> mExpandNext) {
        $lRet.= '<tr id="'.$this -> mMoreId.'" class="togtr" style="display:table-row">'.LF;
      } else {
        $lRet.= '<tr id="'.$this -> mMoreId.'" class="togtr" style="display:none">'.LF;
      }
      $lRet.= '<td class="td1 tg">&nbsp;</td>'.LF;
      $lRet.= '<td class="td1 p8"'.$this -> getColspan().'>';

      $lVal = $this -> getVal('add_data');
      if (!empty($lVal)) {
        $lVal = unserialize($lVal);
        foreach ($lVal as $lKey => $lValue) {
          $lFnc = 'getMore'.$lKey;
          if ($this -> hasMethod($lFnc)) {
            $lRet.= $this -> $lFnc($lValue);
          }
        }
        $lRet.= BR;
      }

      $lVal = trim($this -> getVal('msg'));
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

      $lRet.= '<div class="w100">';
      $lRet.= '<a href="index.php?act='.$this->mStage.'-'.$this -> mSrc.'-his.prnitm';
      $lRet.= '&amp;id='.$this -> getInt('id');
      $lRet.= '&amp;jobid='.$this -> mJobId;
      $lRet.= '" target="_blank" class="nav">';
      $lRet.= '<i class="ico-w16 ico-w16-print" style="float:left;"></i>';
      $lRet.= NB.htm(lan('lib.print'));
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