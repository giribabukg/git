<?php

class CInc_Usg_List extends CUsr_List {

  public function __construct() {
    $this -> mMod = 'usg';
    $this -> m2Act = $this -> mMod;

    $lUsr = CCor_Usr::getInstance();
    
    $this -> mGruKey = $lUsr -> getVal('gadmin');
    $lSub = CCor_Res::extract('id', 'name', 'gru', array('id' => $this -> mGruKey));

    parent::__construct($this -> mMod);
    $lGruKey = (isset($lSub[$this -> mGruKey])) ? $lSub[$this -> mGruKey] : '';
    $this -> mTitle .= ': '.$lGruKey;
  }

  protected function setExcelExportButton() {
    $lResCsv = 'go("index.php?act=usg.xlsexp")';
    $this -> addBtn('Export-User-List', $lResCsv, 'img/ico/16/excel.gif', true);
  }
  
  protected function getPrefs($aKey = NULL) {
    parent::getPrefs($aKey);

    if(NULL == $this -> mFil OR 'Array' === $this -> mFil){
      $this -> mFil = array();
      $this -> mFil['gru'] = $this -> mGruKey;
    }
  }

  protected function getFilterMenu() {
  	$lRet = '';
  	$lRet.= '<form action="index.php" method="post">'.LF;
  	$lRet.= '<input type="hidden" name="act" value="usg.fil" />'.LF;
  	$lRet.= '<select name="val[gru]" size="1" onchange="this.form.submit()">'.LF;
  	$lKey = $this -> mGruKey;
  	$lSrc = CCor_Res::extract('id', 'name', 'gru', array('parent_id' => $lKey));
  	$lFil = (isset($this -> mFil['gru'])) ? $this -> mFil['gru'] : '';
  	$lRet.= '<option value="'.$lKey.'">&nbsp;</option>'.LF;
  	foreach ($lSrc as $lKey => $lVal) {
  		$lRet.= '<option value="'.$lKey.'" ';
  		if ($lKey == $lFil) {
  			$lRet.= ' selected="selected"';
  		}
  		$lRet.= '>'.htm($lVal).'</option>'.LF;
  		$lRet.= $this->getSubGroupOptions($lKey, 1, $lFil);
  	}
  	$lRet.= '</select>'.LF;
  	$lRet.= '</form>';
  	return $lRet;
  }

}