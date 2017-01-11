<?php
class CInc_Questions_Itm_List extends CHtm_List {

  public function __construct($aMasterId, $aDomain) {
    parent::__construct('questions-itm');

    $this -> mMasterId = $aMasterId;
    $this -> mDomain = $aDomain;

    $this -> setAtt('width', '100%');

    $lSql = 'SELECT id, name_'.LAN.' AS name FROM al_questions_master WHERE id="'.addslashes($this -> mMasterId).'"';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mName = $lRow['name'];
      $this -> mMasterId = $lRow['id'];
    }
    $this -> mTitle = lan('questions.itm').' - '.$this -> mName. ' - ' . $this->mDomain;

    $this -> addCtr();
    $this -> addColumn('active', '', FALSE, array('width' => '16'));
    $this -> addColumn('name_'.LAN, lan('lib.value'), TRUE);
    $this -> addColumn('question_type', lan('lib.type'), TRUE);
    $this -> addColumn('cnd', lan('lib.condition'), TRUE, array('width' => '16'));
    $this -> addColumn('size', lan('lib.opt.lines'), TRUE);

    $this -> mDefaultOrder = 'name_'.LAN;

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> mOrdLnk = 'index.php?act=questions-itm.ord&amp;master_id='.$this -> mMasterId.'&amp;fie=';
    $this -> mStdLnk = 'index.php?act=questions-itm.edt&amp;master_id='.$this -> mMasterId.'&amp;id=';
    $this -> mDelLnk = 'index.php?act=questions-itm.del&amp;master_id='.$this -> mMasterId.'&amp;id=';

    $this -> addBtn('Back', "go('index.php?act=questions')", '<i class="ico-w16 ico-w16-back-hi"></i>');

    if ($this -> mCanInsert) {
      $lTemp = strval($this -> mMasterId);
      $lUrl = "go('index.php?act=questions-itm.new&master_id=".$lTemp."')";
      $this -> addBtn(lan('lib.new_item'), $lUrl, '<i class="ico-w16 ico-w16-plus"></i>');
    }

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_questions_items AS questions_items');
    $this -> mIte -> addField('questions_items.id AS id');
    $this -> mIte -> addField('questions_items.question_type');
    $this -> mIte -> addField('questions_items.size');
    $this -> mIte -> addField('questions_items.active');
    $this -> mIte -> addField('questions_items.master_id');
    $this -> mIte -> addField('questions_items.name_'.LAN.' AS name_'.LAN);
    $this -> mIte -> addField('(SELECT cond.name FROM al_cond AS cond WHERE questions_items.cnd_id=cond.id) AS cnd');

    $this -> mIte -> addCnd('master_id="'.addslashes($this -> mMasterId).'"');

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);

    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
  }

  protected function & getViewMenuObject() {
    $lMen = new CHtm_Menu(lan('lib.opt'));

    $lMen -> addTh2(lan('lib.opt.opr'));
    $lMen -> addItem('index.php?act='.$this -> mMod.'.opr&domain='.$this -> mDomain, lan('lib.opt.opr'), '<i class="ico-w16 ico-w16-ord-asc-desc"></i>');

    $lMen -> addTh2(lan('lib.opt.lpp'));
    $lOk = '<i class="ico-w16 ico-w16-ok"></i>';
    $lArr = array(25, 50, 100, 200);
    foreach ($lArr as $lLpp) {
      $lImg = ($lLpp == $this -> mLpp) ? $lOk : '';
      $lMen -> addItem($this -> mLppLnk.$lLpp, $lLpp.' '.lan('lib.opt.lines'), $lImg);
    }
    return $lMen;
  }
  protected function getTdActive() {
    $lVal = $this ->getCurVal();
    $lId = $this -> getVal('id');
    $lMasterId = $this -> getVal('master_id');
    $lRet = '<a href="index.php?act=questions-itm.';
    if ($lVal) {
      $lRet.= 'deact&amp;id='.$lId.'&amp;master_id='.$lMasterId.'" class="nav">';
      $lRet.= '<i class="ico-w16 ico-w16-flag-03"></i>';
    } else {
      $lRet.= 'act&amp;id='.$lId.'&amp;master_id='.$lMasterId.'" class="nav">';
      $lRet.= '<i class="ico-w16 ico-w16-flag-00"></i>';
    }
    $lRet.= '</a>';
    return $this -> td($lRet);
  }
}