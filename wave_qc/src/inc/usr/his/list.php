<?php
class CInc_Usr_His_List extends CHtm_List {

  public function __construct($aUid) {
    parent::__construct('pro-his');
    $this -> mUid = $aUid;
    $this -> setAtt('class', 'tbl w800');
    $this -> mTitle = lan('usr.menu').' '.lan('lib.history');
    $this -> mLpp = 0; // show all, no paging

    $this -> mOrdLnk = 'index.php?act=usr-his.ord&amp;id='.$this -> mUid;
    $this -> mStdLnk = '';

    $this -> getPriv('usr-his');

    $this -> addCtr();
    $this -> addColumn('typ', '', FALSE, array('width' => 16));
    $this -> addColumn('datum', lan('lib.file.date'), FALSE, array('width' => 50));
    $this -> addColumn('user_name',  lan('lib.user'), FALSE);
    $this -> addColumn('subject',  lan('lib.sbj'), FALSE, array('width' => 50));
    $this -> addColumn('msg',  lan('lib.msg'), FALSE, array('width' => "100%"));

    $this -> getPrefs('usr-his');
    $this -> mCanInsert = TRUE;

    if ($this -> mCanInsert) {
      $this -> addBtn(lan('lib.msg.new'), 'go(\'index.php?act=usr-his.new&id='.$this -> mUid.'\')', 'img/ico/16/plus.gif');
    } else {
      $this -> mShowSubHdr = FALSE;
    }

    $this -> mIte = new CCor_TblIte('al_usr_his h,al_usr u');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> addField('h.id');
    $this -> mIte -> addField('h.datum');
    $this -> mIte -> addField('h.subject');
    $this -> mIte -> addField('h.msg');
    $this -> mIte -> addField('h.typ');
    $this -> mIte -> addField('u.firstname');
    $this -> mIte -> addField('u.lastname AS user_name');
    $this -> mIte -> addCnd('h.user_id=u.id'); // join
    $this -> mIte -> addCnd('h.uid="'.$this -> mUid.'"');
  }

  protected function getTdTyp() {
    $lImg = $this -> getVal('typ');
    $lImg = 'img/his/'.$lImg.'.gif';
    return $this -> td(img($lImg));
  }

  protected function getTdUser_name() {
    $lVal = cat($this -> getVal('user_name'), $this -> getVal('firstname'), ', ');
    return $this -> td(htm($lVal));
  }

  protected function getTdDatum() {
    $lVal = $this -> getVal('datum');
    $lDat = new CCor_Date($lVal);
    return $this -> td($lDat -> getFmt(lan('lib.date.week')));
  }

  protected function getTdSubject() {
    // suppress link
    return $this -> td(htm($this -> getVal('subject')));
  }

}