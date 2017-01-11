<?php
class CInc_Content_List extends CHtm_List {

  public function __construct() {
    parent::__construct('content');
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('content.text.menu');
    $this -> mNewLnk = 'index.php?act=content.new&amp;typ=';

    $this -> addColumn('alias', 'Alias', TRUE);
    $this -> addColumn('name_'.LAN, 'Name '.LAN, TRUE);
    $this -> addColumn('content_'.LAN, 'Content '.LAN, TRUE, '', array('typ' => 'Content'));
    $this -> mDefaultOrder = 'alias';

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_text_content c');
    $this -> mIte -> addCnd('mand='.MID);
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    
    if (!empty($this -> mSer['name'])) {
    	$lVal = '"%'.addslashes($this -> mSer['name']).'%"';
    	$lCnd = '(name_'.LAN.' LIKE '.$lVal.' OR ';
    	$lCnd.= 'content_'.LAN.' LIKE '.$lVal.' OR ';
    	$lCnd.= 'alias LIKE '.$lVal.')';
    	$this -> mIte -> addCnd($lCnd);
    }


    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> mMaxLines = $this -> mIte -> getCount();

    if ($this -> mCanInsert) {
    	$this -> addColumn('copy','', false, array('width' => 16));
    	$this -> addBtn(lan('content.new'), "go('index.php?act=content.new')", 'img/ico/16/plus.gif');
    }

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
    $this -> addPanel('cap', '|');
    $this -> addPanel('con', $this -> getSearchForm());
  }


  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="content.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;

    $lRet.= '<td>'.htm(lan('lib.search')).'</td>';
    $lVal = (isset($this -> mSer['name'])) ? htm($this -> mSer['name']) : '';
    $lRet.= '<td><input type="text" name="val[name]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.show_all'),'go("index.php?act=content.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getTdCopy() {
    $lId = $this -> getInt('id');
    $lRet = '<a href="index.php?act=content.copy&id='.$lId.'" class="nav">';
    $lRet.= img('img/ico/16/copy.gif');
    $lRet.= '</a>';
    return $this -> td($lRet);
  }
  
  protected function getTdTypContent() {
  	$lContent = 'content_'.LAN;
  	$lOutput = $this->getVal($lContent);
  	$lOutput = strip_tags($lOutput);
#  	$lOutput = strip_tags(substr($lOutput, 0, 150));
  	$lRet = '<td class="td2">';
  	$lRet.= $lOutput;
  	$lRet.= '</td>';
  	return $lRet;
  }
 
}