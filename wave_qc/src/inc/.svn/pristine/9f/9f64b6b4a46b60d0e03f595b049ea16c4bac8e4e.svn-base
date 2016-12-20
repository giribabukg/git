<?php
class CInc_Sys_Log_List extends CHtm_List {

  public function __construct() {
    parent::__construct('sys-log');
    $this -> getPriv('log');
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('sys-log.menu').' '.lan('lib.list');

    $this -> addCtr();
    $this -> addColumn('typ',   '', TRUE, array('width' => '16'));
    $this -> addColumn('lvl',   '', TRUE, array('width' => '16'));
    $this -> addColumn('datum', lan('lib.file.date'), TRUE);
    $this -> addColumn('act',   'Act', TRUE);
    $this -> addColumn('user',  lan('lib.user'), TRUE);
    $this -> addColumn('msg',   lan('lib.msg'), TRUE, array('width' => '100%'));
    if ($this -> mCanDelete) {
      $this -> addBtn(lan('lib.del.all'), 'javascript:Flow.Std.cnfDel("index.php?act=sys-log.truncate", "'.LAN.'")', '<i class="ico-w16 ico-w16-del"></i>');
    }

    $this -> getPrefs();
    $this -> mIte = new CCor_TblIte('al_sys_log l, al_usr u');
    $this -> mIte -> addField('l.id');
    $this -> mIte -> addField('l.typ');
    $this -> mIte -> addField('l.lvl');
    $this -> mIte -> addField('l.datum');
    $this -> mIte -> addField('l.msg');
    $this -> mIte -> addField('l.act');
    $this -> mIte -> addField('CONCAT(u.lastname,", ",u.firstname) AS user');
    $this -> mIte -> addCnd('l.uid=u.id');

    if (!empty($this -> mSer['msg'])) {
      $this -> mIte -> addCnd('msg LIKE "%'.addslashes($this -> mSer['msg']).'%"');
    }
    if (!empty($this -> mFil['typ'])) {
      $lTyp = intval($this -> mFil['typ']);
      $this -> mIte -> addCnd('typ = '.$lTyp);
    }
    if (!empty($this -> mFil['lvl'])) {
      $lLvl = intval($this -> mFil['lvl']);
      $this -> mIte -> addCnd('lvl = '.$lLvl);
    }

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);

    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());

    $this -> addPanel('fca', '| '.htmlan('lib.filter'));
    $this -> addPanel('fty', $this -> getFilterForm());

    $this -> addPanel('sca', '| '.htmlan('lib.search'));
    $this -> addPanel('ser', $this -> getSearchForm());
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="sys-log.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['msg'])) ? htm($this -> mSer['msg']) : '';
    $lRet.= '<td><input type="text" name="val[msg]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.all'),'go("index.php?act=sys-log.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getFilterForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="sys-log.fil" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;

    $lVal = (isset($this -> mFil['typ'])) ? htm($this -> mFil['typ']) : '';
    $lArr = array();
    $lArr[mtNone]  = '[All]';
    $lArr[mtUser]  = 'User';
    $lArr[mtDebug] = 'Debug';
    $lArr[mtPhp]   = 'PHP';
    $lArr[mtSql]   = 'SQL';
    $lArr[mtApi]   = 'API';
    $lArr[mtAdmin] = 'Admin';
    $lRet.= '<td>';
    $lRet.= getSelect('val[typ]', $lArr, $lVal, array('onchange' => 'this.form.submit()'));
    $lRet.= '</td>'.LF;

    $lVal = (isset($this -> mFil['lvl'])) ? htm($this -> mFil['lvl']) : '';
    $lArr = array();
    $lArr[mlNone]  = '[All]';
    $lArr[mlInfo]  = 'Info';
    $lArr[mlWarn]  = 'Warn';
    $lArr[mlError] = 'Error';
    $lArr[mlFatal] = 'Fatal';

    $lRet.= '<td>';
    $lRet.= getSelect('val[lvl]', $lArr, $lVal, array('onchange' => 'this.form.submit()'));
    $lRet.= '</td>'.LF;


    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getTdTyp() {
    $lTyp = $this -> getVal('typ');
    $lImg = '<i class="ico-w16 ico-w16-mt-'.$lTyp.'"></i>';
    return $this -> td($lImg);
  }

  protected function getTdLvl() {
    $lLvl = $this -> getVal('lvl');
    $lImg = '<i class="ico-w16 ico-w16-ml-'.$lLvl.'"></i>';
    return $this -> td($lImg);
  }

  protected function getTdDatum() {
    $lDat = $this -> getVal('datum');
    $lRet = date(lan('lib.datetime.short'), strtotime($lDat));
    return $this -> td($lRet);
  }

  protected function getTdUser() {
    $lRet = $this -> getVal('user');
    return $this -> td($lRet);
  }

  protected function getTdMsg() {
    $lRet = $this -> getVal('msg');
    return $this -> td(htm($lRet));
  }


}