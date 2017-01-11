<?php
class CInc_Questions_List extends CHtm_List {

  public function __construct() {
    parent::__construct('questions');

    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('questions-list.menu');

    $this -> addCtr();
    $this -> addColumn('active', '', FALSE, array('width' => '16'));
    $this -> addColumn('name_'.LAN, lan('lib.name'), TRUE);
    $this -> addColumn('cnd', lan('lib.condition'), TRUE, array('width' => '16'));
    $this -> addColumn('items', lan('lib.items'), TRUE, array('width' => '16'));

    $this -> mDefaultOrder = 'name_'.LAN;

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    if ($this -> mCanInsert) {
      $this -> addBtn(lan('questions.new'), "go('index.php?act=questions.new')", '<i class="ico-w16 ico-w16-plus"></i>');
    }

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_questions_master AS questions_master');
    $this -> mIte -> addField('questions_master.id AS id');
    $this -> mIte -> addField('questions_master.domain AS domain');
    $this -> mIte -> addField('questions_master.name_'.LAN.' AS name_'.LAN);
    $this -> mIte -> addField('questions_master.active AS active');
    $this -> mIte -> addField('(SELECT cond.name FROM al_cond AS cond WHERE questions_master.cnd_id=cond.id) AS cnd');
    $this -> mIte -> addField('(SELECT COUNT(*) FROM al_questions_items AS questions_items WHERE mand IN (0,'.MID.') AND questions_master.id=questions_items.master_id) AS items');

    if (!empty($this -> mSer)) {
      if (!empty($this -> mSer['name'])) {
        $lVal = '"%'.addslashes($this -> mSer['name']).'%"';
        $lCnd.= '(name_de LIKE '.$lVal.' OR ';
        $lCnd.= 'name_en LIKE '.$lVal.' OR ';
        $lCnd.= 'domain LIKE '.$lVal.')';
        $lCnd.= ' AND `mand`='.MID;
        $this -> mIte -> addCnd($lCnd);
      }
    } else {
      $this -> mIte -> addCnd('mand='.MID);
    }

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
    $this -> addPanel('sca', $this -> getSearchForm());
  }

  protected function getTdItems() {
    $lRet = '';
    $lUsr = CCor_Usr::getInstance();
    $lDomain = $this -> getVal('domain');
    $lItems = $this -> getVal('items');
    $lMasterId = $this -> getVal('id');
    if($lUsr->canRead("questions-itm")) {
      $lRet .= '<a href="index.php?act=questions-itm&amp;master_id='.$lMasterId.'&amp;domain='.$lDomain.'">';
    }
    $lRet.= $lItems.' '.lan('lib.items');
    $lRet.= '</a>';
    return $this -> td($lRet);
  }

  protected function getTdDel() {
    $lDomain = $this -> getVal('domain');
    $lId = $this -> getVal('id');
    $lDelLink = $this -> mStdLink.'.del&amp;id='.$lId;

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
    $lRet = '<a href="index.php?act=questions.';
    if ($lVal) {
      $lRet.= 'deact&amp;id='.$lMasterId.'&amp" class="nav">';
      $lRet.= '<i class="ico-w16 ico-w16-flag-03"></i>';
    } else {
      $lRet.= 'act&amp;id='.$lMasterId.'&amp;" class="nav">';
      $lRet.= '<i class="ico-w16 ico-w16-flag-00"></i>';
    }
    $lRet.= '</a>';
    return $this -> td($lRet);
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">' . LF;
    $lRet.= '<input type="hidden" name="act" value="questions.ser" />' . LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>' . LF;
    $lVal = (isset($this->mSer['name'])) ? htm($this->mSer['name']) : '';
    $lRet.= '<td><input type="text" name="val[name]" class="inp" value="' . $lVal . '" /></td>' . LF;
    $lRet.= '<td>' . btn(lan('lib.search'), '', '<i class="ico-w16 ico-w16-search"></i>', 'submit','','btn') . '</td>';
    if (!empty($this->mSer)) {
      $lRet.= '<td>' . btn(lan('lib.show_all'), 'go("index.php?act=questions.clser")') . '</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>' . LF;

    return $lRet;
  }

}