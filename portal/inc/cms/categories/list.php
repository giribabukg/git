<?php
class CInc_Cms_Categories_List extends CHtm_List {

  public function __construct() {
    parent::__construct('cms-categories');

    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('cms-categories.menu');

    $this -> addCtr();
    $this -> addColumn('active', '', FALSE, array('width' => '16'));
    $this -> addColumn('value', lan('lib.key'), TRUE);
    $this -> addColumn('value_'.LAN, lan('lib.value'), TRUE);
    $this -> addColumn('layouts', lan('lib.layout'), TRUE);

    $this -> mDefaultOrder = 'value';

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    if ($this -> mCanInsert) {
      $this -> addBtn(lan('cms-categories.new'), "go('index.php?act=cms-categories.new')", '<i class="ico-w16 ico-w16-plus"></i>');
    }

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_cms_categories');
    $this -> mIte -> addField('id');
    $this -> mIte -> addField('value');
    $this -> mIte -> addField('value_'.LAN);
    $this -> mIte -> addField('layouts');
    $this -> mIte -> addField('active');

    $this -> mIte -> addCnd('mand='.MID);

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
  }

  protected function getTdLayouts() {
    $lRet = array();
    $lValue = $this -> getVal('layouts');
    $lValue = explode(",", $lValue);
    $lLayouts = CCor_Res::get('htb', array('domain' => 'phl'));

    foreach($lValue as $lKey => $lVal) {
      $lRet[] = $lLayouts[$lVal]; 
    }
    $lRet = implode(", ", $lRet);
    
    return $this -> td($lRet);
  }

  protected function getTdDel() {
    $lValue = $this -> getVal('value');
    $lDelLink = $this -> mStdLink.'.del&amp;value='.$lValue;

    $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw w16 ac">';
    $lRet.= '<a class="nav" href="javascript:Flow.Std.cnf(\''.$lDelLink.'\', \'cnfDel\')">';
    $lRet.= img('img/ico/16/del.gif');
    $lRet.= '</a>';
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getTdActive() {
    $lVal = $this ->getCurVal();
    $lMasterId = $this -> getVal('id');
    $lRet = '<a href="'.$this -> mStdLink.'.';
    if ($lVal) {
      $lRet.= 'deact&amp;id='.$lMasterId.'" class="nav">';
      $lRet.= '<i class="ico-w16 ico-w16-flag-03"></i>';
    } else {
      $lRet.= 'act&amp;id='.$lMasterId.'" class="nav">';
      $lRet.= '<i class="ico-w16 ico-w16-flag-00"></i>';
    }
    $lRet.= '</a>';
    return $this -> td($lRet);
  }
}