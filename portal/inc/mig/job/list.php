<?php
class CInc_Mig_Job_List extends CJob_List {

  protected $mSrc = 'com';

  public function __construct() {
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp[$this -> mSrc];
    parent::__construct('job-'.$this -> mSrc, $this -> mCrpId, 'tbl');
    $this -> mImg = 'cust/img/ico/40/'.LAN.'/job-'.$this -> mSrc.'.gif';

    $this -> mIdField = 'jobid';

    $lUsr = CCor_Usr::getInstance();

    $this -> addCtr();
    $lFla = false;
    $this -> mSrcArr = array($this -> mSrc);
    foreach ($this -> mSrcArr as $lKey) {
      $lFla = true;
      $this -> mCanIns[$lKey] = $lUsr -> canInsert('job-'.$lKey);
    }
    $this -> mMod = 'mig-job';
    $this -> getPrefs('mig-job');

    $this -> addColumn('mig', '', false, array('width' => '16'));
    if ($this -> mCanDelete) {
      $this -> addDel();
    }
    $this -> addColumns();
    $this -> addColumn('err', 'Errors', true, array('width' => '128'));

    $this -> mOrdLnk = 'index.php?act=mig-job.ord&amp;fie=';
    $this -> mLppLnk = 'index.php?act=mig-job.lpp&amp;lpp=';

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
  }

  protected function getIterator() {
    $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
    if ('portal' == $lWriter) {
      $this -> mIte = new CCor_TblIte('al_job_pdb_'.intval(MID).' LEFT OUTER JOIN al_mig ON al_job_pdb_'.intval(MID).'.jobid = al_mig.oldid', false);
      $this -> addCondition('src', '<>', 'oldcom', false);
      $this -> addCondition('src', '<>', 'art', false);
      $this -> addCondition('src', '<>', 'rep', false);
      $this -> addCondition('src', '<>', 'tra', false);
    } else {
      $this -> mIte = new CApi_Alink_Query_Getjoblist($this -> mSrc);
      $this -> addCondition('src', '=', $this -> mSrc);
    }
  }

  protected function & getViewMenuObject() {
    $lMen = new CHtm_Menu(lan('lib.opt'));
    $lMen -> addTh2(lan('lib.opt.view'));
    $lMen -> addItem('index.php?act=mig-job.fpr', lan('lib.opt.fpr'), 'cust/img/ico/16/col.gif');
    #$lMen -> addItem('index.php?act=mig-job.spr', lan('lib.opt.spr'), 'ico/16/search.gif');

    $lOk = 'cust/img/ico/16/ok.gif';

    $lMen -> addTh2(lan('lib.opt.lpp'));
    $lArr = array(25, 50, 100, 200, 500);
    foreach ($lArr as $lLpp) {
      $lImg = ($lLpp == $this -> mLpp) ? $lOk : 'd.gif';
      $lMen -> addItem($this -> mLppLnk.$lLpp, $lLpp.' '.lan('lib.opt.lines'), $lImg);
    }

    return $lMen;
  }

  protected function getSearchBar() {
    return '';
  }

  protected function getTdMig() {
    $lId = $this -> getInt('jobid');
    $lRet = '<input type="checkbox" value="'.$lId.'" />';
    return $this -> tdc($lRet);
  }

  protected function getTdCpy() {
    $lJid = $this -> getVal('jobid');
    $lMen = new CHtm_Menu(img('cust/img/ico/16/copy.gif'), '', false);
    foreach ($this -> mSrcArr as $lKey) {
      if (!$this -> mCanIns[$lKey]) continue;
      $lMen -> addItem('index.php?act=job-'.$lKey.'.cpy&amp;jobid='.$lJid.'&amp;src='.$this -> mSrc, lan('lib.copy_to').' '.lan('job-'.$lKey.'.item'), 'ico/16/'.LAN.'/job-'.$lKey.'.gif');

    }
    return $this -> td($lMen -> getContent());
  }

  protected function getTdDel() {
    $lJid = $this -> getVal('jobid');
    if ('A' != substr($lJid, 0, 1)) return $this -> td();
    return parent::getTdDel();
  }

  protected function getTdErr() {
    $lJid = $this -> getVal('errors');
    return $this -> td($lJid);
  }

  protected function getBody() {
    $lColCnt = $this -> getColCnt() - 2; // Anzahl der Spalten minus Zählerspalte und Checkbox-Spalte
    $lRet = $this -> getRows();

    $lJs = 'function selAll(aEl) {'.LF;
    $lJs.= 'var lVal = aEl.checked;'.LF;
    $lJs.= '$("tbl").getElementsBySelector("[type=\'checkbox\']").each(function(aNod) {aNod.checked=lVal;});'.LF;
    $lJs.='}'.LF;

    $lJs.= 'function confirmSel(aEl) {'.LF;
    $lJs.= 'var lSel = new Array();'.LF;
    $lJs.= 'var lUnSel = new Array();'.LF;
    $lJs.= '$("tbl").getElementsBySelector("[type=\'checkbox\']").each(function(aNod) {if ((aNod.checked) && (aNod.value!="on")) {lSel.push(aNod.value)} else {lUnSel.push(aNod.value)}});'.LF;
    $lJs.= 'go("index.php?act=mig-job.mig&ids=" + lSel.join(","));'.LF;
    $lJs.='}'.LF;

    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lJs);

    $lRet.= '<tr>';
    $lRet.= '<td class="th2">&nbsp;</td>';
    $lRet.= '<td class="th2">';
    $lRet.= '<input type="checkbox" onclick="selAll(this)" />';
    $lRet.= '</td>';
    $lRet.= '<td class="th2" colspan="'.$lColCnt.'">';
    $lRet.= '<a href="javascript:confirmSel(this)">Migrate!</a>';
    $lRet.= '</td>';
    $lRet.= '</tr>';
    return $lRet;
  }

}