<?php
class CInc_Fie_Blocks_List extends CHtm_List {

  public function __construct() {
    parent::__construct('fie-blocks');
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('job-fil.blo');

    $this -> addColumn('ctr');
    $this -> addColumn('src', 'SRC', TRUE, array('width' => 16));
    $this -> addColumn('code', 'Code', TRUE, array('width' => 16));
    $this -> addColumn('name', 'Name', TRUE);

    $this -> addBtn('New Block', "go('index.php?act=fie-blocks.new')", 'img/ico/16/plus.gif');

    $this -> mLpp = 25;
    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_fie_blocks');
    $this -> setGroup('src');
    if (!empty($this -> mFil)) {
      foreach ($this -> mFil as $lKey => $lVal) {
        $this -> mIte -> addCnd('`'.$lKey.'` in ("'.implode(explode(',',addslashes($lVal)),'","').'")');
      }
    }
    if (!empty($this -> mSer)) {
      if (!empty($this -> mSer['name'])) {
        $lVal = '"%'.addslashes($this -> mSer['name']).'%"';
        $lCnd = 'name LIKE '.$lVal.' OR ';
        $lCnd.= 'alias LIKE '.$lVal.' OR ';
        $lCnd.= 'typ LIKE '.$lVal;
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

    $this -> mReg = new CHtm_Fie_Reg();
  }

  protected function getFilterMenu() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="fie-blocks.fil" />'.LF;
    $lRet.= '<select name="val[src]" size="1" onchange="this.form.submit()">'.LF;
    $lSrc = array();
    $lSrc['pro,pde,mba,tpl,pac'] = '[all]';
    $lSrc['pro'] = 'Project';
    $lSrc['pde'] = 'Print Development';
    $lSrc['mba'] = 'Master Base Artwork';
    $lSrc['tpl'] = 'Variants';
    $lSrc['pac'] = 'Packaging';
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
    $lRet.= '<input type="hidden" name="act" value="fie-blocks.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['name'])) ? htm($this -> mSer['name']) : '';
    $lRet.= '<td><input type="text" name="val[name]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn('All','go("index.php?act=fie-blocks.clser")').'</td>';
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

}