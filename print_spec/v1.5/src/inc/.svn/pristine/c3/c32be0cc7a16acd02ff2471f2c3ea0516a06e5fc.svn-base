<?php
class CInc_Chk_List extends CHtm_List {

  public function __construct() {
    parent::__construct('chk');

    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('chk.menu');

    $this -> addCtr();
    $this -> addColumn('name_'.LAN, lan('lib.name'), TRUE);
    $this -> addColumn('cnd', lan('lib.condition'), TRUE, array('width' => '16'));
    $this -> addColumn('items', lan('lib.items'), TRUE, array('width' => '16'));

    $this -> mDefaultOrder = 'name_'.LAN;

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    if ($this -> mCanInsert) {
      $this -> addBtn(lan('chk.new'), "go('index.php?act=chk.new')", 'img/ico/16/plus.gif');
    }

    $this -> getPrefs();
    $this -> addIsThisInUse();

    $this -> mIte = new CCor_TblIte('al_chk_master AS chk_master');
    $this -> mIte -> addField('chk_master.id AS id');
    $this -> mIte -> addField('chk_master.domain AS domain');
    $this -> mIte -> addField('chk_master.name_'.LAN.' AS name_'.LAN);
    $this -> mIte -> addField('(SELECT cond.name FROM al_cond AS cond WHERE chk_master.cnd_id=cond.id) AS cnd');
    $this -> mIte -> addField('(SELECT COUNT(*) FROM al_chk_items AS chk_items WHERE mand IN (0,'.MID.') AND chk_master.domain=chk_items.domain) AS items');

    $this -> mIte -> addCnd('mand='.MID);

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
  }

  protected function getTdItems() {
    $lDomain = $this -> getVal('domain');
    $lItems = $this -> getVal('items');
    $lMasterId = $this -> getVal('id');

    $lRet = '<a href="index.php?act=chk-itm&amp;master_id='.$lMasterId.'&amp;domain='.$lDomain.'">';
    $lRet.= $lItems.' '.lan('lib.items');
    $lRet.= '</a>';
    return $this -> td($lRet);
  }

  protected function getTdDel() {
    $lDomain = $this -> getVal('domain');
    $lDelLink = $this -> mStdLink.'.del&amp;domain='.$lDomain;

    $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw w16 ac">';
    $lRet.= '<a class="nav" href="javascript:Flow.Std.cnfDel(\''.$lDelLink.'\', \''.LAN.'\')">';
    $lRet.= img('img/ico/16/del.gif');
    $lRet.= '</a>';
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getTdInuse() {
    $lId = $this -> getVal($this -> mIdField);
    $lTip = '';

    $lInGru = 'SELECT * FROM al_gru WHERE chk_master_src='.$lId;
    $lQry = new CCor_Qry($lInGru);
    foreach ($lQry as $lRow) {
      $lTip.= lan('lib.group').': ';
      $lTip.= $lRow['name'].BR;
      if ($lRow) $lIsInUseInSteps = TRUE;
    }

    if (isset($lIsInUseInSteps)) {
      $lDis = img('img/ico/16/flag-03.gif');
    } else {
      $lDis = img('img/ico/16/flag-00.gif');
    }

    $lRet.= $lDis;
    $lRet.= '</span>';
    return $this -> td($lRet,$lId , array("data-toggle" => "tooltip", "data-tooltip" => $lTip));
  }
}