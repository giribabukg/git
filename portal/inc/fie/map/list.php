<?php
class CInc_Fie_Map_List extends CHtm_List {


  public function __construct() {
    parent::__construct('fie-map');
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('fie-map.menu');

    $this -> addMor();
    $this -> addColumn('name', lan('lib.name'), TRUE, array('width' => '100%'));
    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    if ($this -> mCanInsert) {
      $this->addBtn(lan('fie-map.new'), "go('index.php?act=fie-map.new')", 'img/ico/16/plus.gif');
      $this->addBtn(lan('fie-map.import'), "Flow.FieldMap.importMap()", 'img/ico/16/plus.gif');
    }

    $this -> mLpp = 25;
    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_fie_map_master');
    $this -> mIte -> addCnd('mand IN (0,'.intval(MID).')');

    if (!empty($this -> mSer)) {
      if (!empty($this -> mSer['name'])) {
        $lVal = esc('%'.$this -> mSer['name'].'%');
        $lCnd = 'name LIKE '.$lVal;
        $this -> mIte -> addCnd($lCnd);
      }
    }

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
    $this -> addPanel('sca', '| '.htmlan('lib.search'));
    $this -> addPanel('ser', $this -> getSearchForm());

    $this -> mNat = CCor_Res::extract('alias', 'name', 'fie');
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="fie-map.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['name'])) ? htm($this -> mSer['name']) : '';
    $lRet.= '<td><input type="text" name="val[name]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','s<ubmit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.show_all'),'go("index.php?act=fie-map.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getTdMor() {
    $lId = $this -> getInt('id');
    $this->mMoreId = 'm'.$lId;
    $lRet = '<a class="nav" onclick="Flow.FieldMap.showSub('.$lId.')">';
    $lRet.= '...</a>';
    return $this -> tdClass($lRet, 'w16 ac', true);
  }

  protected function afterRow() {
    $lRet = parent::afterRow();
    $lRet.= '<tr id="'.$this->mMoreId.'" class="fiemap-tr" style="display:none"><td class="td1 tg">&nbsp;</td>';
    $lCol = $this -> mColCnt -1;
    $lRet.= '<td class="td1 p16 map-parent" id="'.$this -> mMoreId.'r" colspan="'.$lCol.'"><img src="img/pag/ajx.gif" alt="" /></td>';
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

}
