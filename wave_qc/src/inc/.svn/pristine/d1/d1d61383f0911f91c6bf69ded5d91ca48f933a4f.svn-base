<?php
/**
 * Jobs: Search - List
 *
 *  Description
 *
 * @package    JOB
 * @subpackage    Ser
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 615 $
 * @date $Date: 2013-01-09 15:55:52 +0000 (Wed, 09 Jan 2013) $
 * @author $Author: gemmans $
 */
class CInc_Xchange_Log_List extends CHtm_List {

  public function __construct() {
    parent::__construct('xchange-log');
    $this -> getPriv('xchange');
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('xchange-log.menu');

    $this -> addCtr();
    $this -> addColumn('typ',   '', TRUE, array('width' => '16'));
    $this -> addColumn('lvl',   '', TRUE, array('width' => '16'));
    $this -> addColumn('filename',   'Filename', TRUE);
    $this -> addColumn('msg',   'Comment', TRUE);
    $this -> addColumn('datum',   'Date', TRUE);
    $this -> addColumn('reprocess', '', FALSE, array('width' => '16'));
    if ($this -> mCanDelete) {
      $this -> addBtn(lan('lib.del.all'), 'javascript:Flow.Std.cnfDel("index.php?act='.$this->mMod.'.truncate", "'.LAN.'")', '<i class="ico-w16 ico-w16-del"></i>');
    }

    $this -> getPrefs();
    $this -> mIte = new CCor_TblIte('al_xchange_log');

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

    $this -> mIte -> setOrder('datum', 'desc');

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
    $lRet.= '<input type="hidden" name="act" value="'.$this->mMod.'.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['msg'])) ? htm($this -> mSer['msg']) : '';
    $lRet.= '<td><input type="text" name="val[msg]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.all'),'go("index.php?act=xchange-log.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getFilterForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this->mMod.'.fil" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;

    $lVal = (isset($this -> mFil['typ'])) ? htm($this -> mFil['typ']) : '';
    $lArr = array(mtNone => '[All]', 128 => 'Parsing', 256 => 'File', 512 => 'Job', 1024 => 'Lookup');
    $lRet.= '<td>';
    $lRet.= getSelect('val[typ]', $lArr, $lVal, array('onchange' => 'this.form.submit()'));
    $lRet.= '</td>'.LF;

    $lVal = (isset($this -> mFil['lvl'])) ? htm($this -> mFil['lvl']) : '';
    $lArr = array(mtNone => '[All]', 4 => 'Error', 64 => 'Success');
    $lRet.= '<td>';
    $lRet.= getSelect('val[lvl]', $lArr, $lVal, array('onchange' => 'this.form.submit()'));
    $lRet.= '</td>'.LF;

    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getTdTyp() {
    $lTyp = $this -> getVal('typ');
    $lImg = img('img/ico/16/mt-'.$lTyp.'.gif');
    return $this -> td($lImg);
  }

  protected function getTdLvl() {
    $lLvl = $this -> getVal('lvl');
    $lImg = img('img/ico/16/ml-'.$lLvl.'.gif');
    return $this -> td($lImg);
  }

  protected function getTdDatum() {
    $lDat = $this -> getVal('datum');
    $lRet = date(lan('lib.datetime.long'), strtotime($lDat));
    return $this -> td($lRet);
  }

  protected function getTdMsg() {
    $lRet = $this -> getVal('msg');
    return $this -> td($lRet);
  }

  protected function getTdReprocess() {
    $lRp = $this -> getVal('rp');
  	$lLvl = $this -> getVal('lvl');
  	$lRpLink = 'index.php?act='.$this->mMod.'.reprocess&id=';
  	$lId = $this -> getVal($this -> mIdField);
  	$lLink = $lRpLink.$lId;

  	$lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw w16 ac">';
  	if($lRp == 'N' && $lLvl == 4){
	  	$lRet.= '<a class="nav" href="'.$lLink.'">';
	  	$lRet.= img('img/ico/16/mt-16.gif', array("title" => 'Reprocess File'));
	  	$lRet.= '</a>';
  	}
  	$lRet.= '</td>'.LF;

  	return $lRet;
  }
}