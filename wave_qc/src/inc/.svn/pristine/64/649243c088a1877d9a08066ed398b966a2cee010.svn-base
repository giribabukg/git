<?php
class CInc_Job_View_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job.view');
    $this -> mSrc = $this -> getReq('src');
    $this -> mJobId = $this -> getReq('jobid');
  }

  protected function getStdUrl() {
    if ($this -> mJobId != 0) {
      return 'index.php?act='.$this -> mMod.'&src='.$this -> mSrc.'&jobid='.$this -> mJobId;
    } else {
      return 'index.php?act='.$this -> mMod.'&src='.$this -> mSrc;
    }
  }

  protected function actStd() {
    $lSrc = $this -> getReq('src');
    $lJobId = $this -> getReq('jobid');

    $lVie = new CJob_View_List($lSrc, $lJobId);
    $this -> render($lVie);
  }

  private function getSet() {
    $lUsr = CCor_Usr::getInstance();
    $lRet = array();
    $lRet['cols'] = $lUsr -> getPref($this -> mSrc.'.cols');
    $lRet['ord']  = $lUsr -> getPref($this -> mSrc.'.ord');
    $lRet['sfie'] = $lUsr -> getPref($this -> mSrc.'.sfie');
    $lRet['lpp']  = $lUsr -> getPref($this -> mSrc.'.lpp');
    if (empty($lRet['lpp'])) $lRet['lpp'] = 25;
    return $lRet;
  }

  protected function actSnew() {
    $lId  = $this -> getInt('id');
    $lNam = $this -> getReq('name');
    $lSrc_id = $this -> getReq('src_id');

    if (empty($lNam)) {
      $this -> msg(lan('lib.view.name'), mtUser, mlWarn);
      $this -> redirect();
    }
    $lUid = CCor_Usr::getAuthId();

    $lSql = 'SELECT id FROM al_usr_view WHERE src_id IN ('.esc($lUid).',0) ';
    $lSql.= 'AND name LIKE '.esc($lNam).' AND mand='.MID;
    $lNum = CCor_Qry::getInt($lSql);
    if (!empty($lNum)) {
      $this -> msg(lan('lib.view.view_exists'), mtUser, mlWarn);
      $this -> redirect();
    }

    $lUpd = $this -> getSet();
    $lUpd['name'] = $lNam;
    $lUpd['ref'] = $this -> getReq('src');
    $lUpd['src'] = 'usr';
    $lUpd['src_id'] = $lSrc_id;
    $lUpd['mand'] = MID;

    $lSql = 'INSERT INTO al_usr_view SET ';
    foreach ($lUpd as $lKey => $lVal) {
      if (!empty($lVal)) {
        $lSql.= $lKey.'='.esc($lVal).',';
      }
    }
    $lSql = strip($lSql);
    CCor_Qry::exec($lSql);
    $this->redirect();
  }

  protected function actReplace() {
    $lId  = $this -> getInt('id');
    $lUpd = $this -> getSet();
    $lSql = 'UPDATE al_usr_view SET ';
    foreach ($lUpd as $lKey => $lVal) {
      $lSql.= $lKey.'='.esc($lVal).',';
    }
    $lSql = strip($lSql);
    $lSql.= 'WHERE id='.$lId;
    CCor_Qry::exec($lSql);
    $this->redirect();
  }

  protected function actDel() {
    $lId  = $this -> getInt('id');
    $lUid = CCor_Usr::getAuthId();
    $lUsr = CCor_Usr::getInstance();
    $lOwn = CCor_Qry::getInt('SELECT src_id FROM al_usr_view WHERE id='.$lId);
    if (($lOwn == $lUid) OR ($lUsr -> canInsert('vie'))) {
      CCor_Qry::exec('DELETE FROM al_usr_view WHERE id='.$lId);
    }
    $this -> redirect();
  }
}