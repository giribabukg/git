<?php
class CInc_Job_View_Search_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('lib.opt.search_presets');
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

    $lVie = new CJob_View_Search_List($lSrc, $lJobId);
    $this -> render($lVie);
  }

  private function getSet() {
    $lUsr = CCor_Usr::getInstance();
    $lRet = array();
    $lRet['ser'] = serialize($lUsr -> getPref($this -> mSrc.'.ser'));
    return $lRet;
  }

  protected function actSnew() {
    $lId  = $this -> getInt('id');
    $lNam = $this -> getReq('name');

    if (empty($lNam)) {
      $this -> msg('Please enter a name for the search preset!', mtUser, mlWarn);
      $this -> redirect();
    }
    $lUpd = $this -> getSet();
    $lUpd['name'] = $this -> getReq('name');

    $lSrc = $this -> getReq('src');
    switch ($lSrc) {
      case 'job-pro':
        $lRef = 'pro';
        break;
      case 'job-sku':
        $lRef = 'sku';
        break;
      default:
        $lRef = 'job';
    }

    $lUpd['ref'] = $lRef;
    $lUpd['mand'] = MID;
    $lUpd['src'] = 'usr';
    $lUpd['src_id'] = CCor_Usr::getAuthId();

    $lSql = 'INSERT INTO al_usr_search SET ';
    foreach ($lUpd as $lKey => $lVal) {
      if (!empty($lVal)) {
        $lSql.= $lKey.'='.esc($lVal).',';
      }
    }
    $lSql = strip($lSql);
    CCor_Qry::exec($lSql);
    if ($this -> mJobId != 0) {
      $this -> redirect('index.php?act='.$this -> mSrc.'&jobid='.$this -> mJobId);
    } else {
      $this -> redirect('index.php?act='.$this -> mSrc);
    }
  }

  protected function actReplace() {
    $lId  = $this -> getInt('id');
    $lUpd = $this -> getSet();
    $lSql = 'UPDATE al_usr_search SET ';
    foreach ($lUpd as $lKey => $lVal) {
      $lSql.= $lKey.'='.esc($lVal).',';
    }
    $lSql = strip($lSql);
    $lSql.= 'WHERE id='.$lId;
    CCor_Qry::exec($lSql);
    if ($this -> mJobId != 0) {
      $this -> redirect('index.php?act='.$this -> mSrc.'&jobid='.$this -> mJobId);
    } else {
      $this -> redirect('index.php?act='.$this -> mSrc);
    }
  }

  protected function actDel() {
    $lId  = $this -> getInt('id');
    $lUid = CCor_Usr::getAuthId();
    $lOwn = CCor_Qry::getInt('SELECT src_id FROM al_usr_search WHERE id='.$lId.' AND mand='.MID);
    if ($lOwn == $lUid) {
      CCor_Qry::exec('DELETE FROM al_usr_search WHERE id='.$lId.' AND mand='.MID);
    }
    $this -> redirect();
  }

}