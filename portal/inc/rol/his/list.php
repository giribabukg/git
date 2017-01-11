<?php
class CInc_Rol_His_List extends CHtm_List {
  
  public function __construct($aRoleId) {
    parent::__construct('rol-his');
    $this -> mRoleId = $aRoleId;
    $this -> setAtt('class', 'tbl w800');
    $this -> mTitle = lan('rol.menu').' '.lan('lib.history');
    $this -> mLpp = 0; // show all, no paging
  
    $this -> getPriv('rol-his');
  
    $this -> addCtr();
    $this -> addColumn('typ', '', FALSE, array('width' => 16));
    $this -> addColumn('datum', lan('lib.file.time.modification'), FALSE, array('width' => 50));
    $this -> addColumn('user_name',  lan('lib.user'), FALSE);
    $this -> addColumn('subject',  lan('lib.sbj'), FALSE, array('width' => 50));
    $this -> addColumn('msg',  lan('lib.msg'), FALSE, array('width' => "100%"));

    $this -> mCanInsert = TRUE;
    $this -> getPrefs();
    
    if ($this -> mCanInsert) {
      $this -> addBtn(lan('lib.msg.new'), 'go(\'index.php?act=rol-his.new&id='.$this -> mRoleId.'\')', 'img/ico/16/plus.gif');
    }
    
    $this -> mIte = new CCor_TblIte('al_role_his h,al_usr u');
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> addField('h.id');
    $this -> mIte -> addField('h.datum');
    $this -> mIte -> addField('h.subject');
    $this -> mIte -> addField('h.msg');
    $this -> mIte -> addField('h.typ');
    $this -> mIte -> addField('u.firstname');
    $this -> mIte -> addField('u.lastname AS user_name');
    $this -> mIte -> addCnd('h.uid=u.id');
    $this -> mIte -> addCnd('h.role_id="'.$this -> mRoleId.'"');
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
    return $this -> td(htm($this -> getVal('subject')));
  }

}