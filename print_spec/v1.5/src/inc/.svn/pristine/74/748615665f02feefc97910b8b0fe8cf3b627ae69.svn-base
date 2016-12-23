<?php
class CInc_Fie_List extends CHtm_List {

  public function __construct() {
    parent::__construct('fie');

    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('fie.menu');

    $lWriter = CCor_Cfg::get('job.writer.default', 'alink');

    $this -> addColumn('ctr');
    $this -> addColumn('name_'.LAN, lan('lib.name'),        TRUE);
    $this -> addColumn('alias',     lan('fie.alias'),       TRUE);
    $this -> addColumn('native',    lan('fie.native'),      TRUE);
    $this -> addColumn('typ',       lan('lib.type'),        TRUE);
    $this -> addColumn('param',     lan('lib.param'),       TRUE);
    $this -> addColumn('learn',     lan('fie-learn.list'),  TRUE);
    if ($this -> mCanDelete) {
      $this -> addDel();
      //$this -> addColumn('make'); // can be used to create zus fields in networker
    }

    $this -> addBtn(lan('fie.new'), "go('index.php?act=fie.new')", '<i class="ico-w16 ico-w16-plus"></i>');

    $this -> mLpp = 25;
    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_fie');
    $this -> mIte -> addCnd('mand='.intval(MID));
    #$this -> setGroup('src');

    if (!empty($this -> mFil)) { // Auftragsfelder-Filter: Dropdown mit allen Jobs
      foreach ($this -> mFil as $lKey => $lVal) {
        if ('src' == $lKey) {
          $lAllJobs = CCor_Cfg::get('all-jobs');
          $lAllJobs[] = 'pro';
          $lAllJobs[] = 'sub';
          $lAvail = array();
          $lAll = 0;
          foreach ($lAllJobs as $ltyp) {
            $lTyp = ucfirst($ltyp);
            $lAvail[$ltyp] = constant('fs'.$lTyp);
            $lAll += $lAvail[$ltyp];
          }
          $lAvail['all'] = $lAll;
          $this -> mIte -> addCnd('(avail & "'.$lAvail[$lVal].'")');
        } else
          $this -> mIte -> addCnd('`'.$lKey.'` in ("'.implode(explode(',',addslashes($lVal)),'","').'")');
      }
    }
    if (!empty($this -> mSer)) {
      if (!empty($this -> mSer['name'])) {
        $lVal = '"%'.addslashes($this -> mSer['name']).'%"';
        $lCnd = '(name_'.LAN.' LIKE '.$lVal.' OR ';
        $lCnd.= 'alias LIKE '.$lVal.' OR ';
        $lCnd.= 'typ LIKE '.$lVal.')';
        $lCnd.= ' AND `mand`='.MID;
        $this -> mIte -> addCnd($lCnd);
      }
    }

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
    $this -> addPanel('cap', '| '.htmlan('lib.filter'));
    $this -> addPanel('fil', $this -> getFilterMenu());
    $this -> addPanel('sca', '| '.htmlan('lib.search'));
    $this -> addPanel('ser', $this -> getSearchForm());

    $this -> addBtn(lan('fie.proof'), "go('index.php?act=fie.structproof')", '<i class="ico-w16 ico-w16-mt-4"></i>');
    $this -> addBtn(lan('fie.check'), "go('index.php?act=fie.sanitycheck')", '<i class="ico-w16 ico-w16-ok"></i>');

    $this -> mReg = new CHtm_Fie_Reg();
    $this -> mNat = CCor_Res::extract('native', 'alias', 'native');
  }

  protected function getFilterMenu() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="fie.fil" />'.LF;
    $lRet.= '<select name="val[src]" size="1" onchange="this.form.submit()">'.LF;

    $lAllJobs = CCor_Cfg::get('all-jobs');
    $lAllJobs[] = 'pro';
    $lAllJobs[] = 'sub';
    $lSrc = array();
    $lSrc['all'] = '[all]';
    foreach ($lAllJobs as $ltyp) {
      $lSrc[$ltyp] = lan('job-'.$ltyp.'.menu');
    }
    $lSrc['arc'] = lan('arc.menu');
    $lFil = (isset($this -> mFil['src'])) ? $this -> mFil['src'] : '';
    foreach ($lSrc as $lKey => $lVal) {
      $lRet.= '<option value="'.$lKey.'" ';
      if ($lKey == $lFil) {
        $lRet.= ' selected="selected"';
      }
      $lRet.= '>'.htm($lVal).'</option>'.LF;
    }
    $lRet.= '</select>'.LF;
    $lRet.= '</form>';
    return $lRet;
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="fie.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['name'])) ? htm($this -> mSer['name']) : '';
    $lRet.= '<td><input type="text" name="val[name]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.show_all'),'go("index.php?act=fie.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getTdTyp() {
    $lTyp = $this -> getVal('typ');
    $lTxt = $this -> mReg -> typeToString($lTyp);
    return $this -> tda(htm($lTxt));
  }

  protected function getTdParam() {
    $lTyp = $this -> getVal('typ');
    $lPar = $this -> getVal('param');
    $lTxt = $this -> mReg -> paramToString($lTyp, $lPar);
    return $this -> tda(htm($lTxt));
  }

  protected function getTdNative() {
    $lRet = $this -> getCurVal();
    if (isset($this -> mNat[$lRet])) {
      $lRet = $this -> mNat[$lRet];
    }
    return $this -> tda(htm($lRet));
  }

  protected function getTdMake() {
    $lId = $this->getInt('id');
    $lAlias = $this->getVal('alias');
    $lUrl = 'index.php?act=fie.createzus&id='.$lId.'&alias='.$lAlias;
    $lCont = '<a href="'.$lUrl.'" class="nav">Make</a>';
    return $this->td($lCont);
  }  

}