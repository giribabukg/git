<?php
class CInc_Usr_Agent_List extends CHtm_List {

  private $mUserId;

  public function __construct($aUserId) {
    parent::__construct('usr-agent');

    $this -> mUserId = $aUserId;

    $this -> setAtt('class', 'tbl w800');
    $this -> mTitle = lan('usr.menu').' '.lan('lib.useragent');

    $this -> mStdLnk = 'index.php?act=usr-agent&amp;id='.$this -> mUserId;
    $this -> mOrdLnk = 'index.php?act=usr-agent.ord&amp;id='.$this -> mUserId.'&amp;fie=';
    $this -> mLppLnk = 'index.php?act=usr-agent.lpp&amp;id='.$this -> mUserId.'&amp;lpp=';

    $this -> addCtr();
    $this -> addColumn('datetime',  lan('lib.datetime'),  TRUE, array('width' => '10%'));
    $this -> addColumn('useragent', lan('lib.useragent'), TRUE, array('width' => '90%'));

    $this -> mDefaultLpp = 25;
    $this -> mDefaultOrder = 'datetime';
    $this -> mDir = 'desc';
    $this -> getPrefs('usr-agent');

    $this -> mIte = new CCor_TblIte('al_usr p, al_usr_useragent q');
    $this -> mIte -> addField('q.datetime');
    $this -> mIte -> addField('q.useragent');
    $this -> mIte -> addCnd('p.id=q.userid');
    $this -> mIte -> addCnd('p.id="'.$this -> mUserId.'"');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
  }

  protected function getTdDatetime() {
    $lVal = $this -> getVal('datetime');
    $lDat = new CCor_DateTime($lVal);
    return $this -> td($lDat -> getFmt(lan('lib.datetime.long')));
  }

  protected function getTdUseragent() {
    $lVal = $this -> getVal('useragent');

    $lZend = str_replace('_', DS, 'Zend_Http_UserAgent_AbstractDevice').'.php';
    if (stream_resolve_include_path($lZend) !== FALSE) {
      $lExtract = Zend_Http_UserAgent_AbstractDevice::extractFromUserAgent($lVal);

      $lRet = lan('lib.useragent.device_os_name').': '.$lExtract['device_os_name'].BR;
      $lRet.= lan('lib.useragent.device_os_token').': '.$lExtract['device_os_token'].BR;
      $lRet.= lan('lib.useragent.browser_name').': '.$lExtract['browser_name'].BR;
      $lRet.= lan('lib.useragent.browser_version').': '.$lExtract['browser_version'].BR;
    } else {
      $lRet = $lVal;
    }

    return $this -> td($lRet);
  }

  protected function getNavBar() {
    if (!$this -> mNavBar) {
      return '';
    }
    if (isset($this -> mJobId)){
      $lJobId = $this -> mJobId;
    }else {
      $lJobId = '';
    }
  
    $lNav = new CHtm_NavBar($this -> mMod, $this -> mPage, $this -> mMaxLines, $this -> mLpp, $lJobId);
    // START: user id needs to be explicitely set
    $lNav -> setParam('id', $this -> mUserId);
    // END: user id needs to be explicitely set
    return $lNav -> getContent();
  }
}