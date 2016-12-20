<?php
class CInc_Gru_Fie_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('gru-fie');
    $this -> mMmKey = 'usr';
    $lpn = 'rig';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lUsr = CCor_Usr::getInstance();
    $lSrc = $lUsr -> getPref('gru-fie.src', 'pro');
    $lKey = $lUsr -> getPref('gru-fie.key', 'ids');
    $lVie = new CGru_Fie_List($lSrc, $lKey);
    $this -> render($lVie);
  }

  protected function actSedt() {
    $lSrc = addslashes($this -> getReq('src'));
    $lKey = addslashes($this -> getReq('code'));

    $lQry = new CCor_Qry('DELETE FROM al_gru_fie WHERE src="'.$lSrc.'" AND code="'.$lKey.'"');
    $lChk = $this -> getReq('val');
    if (!empty($lChk))
    foreach ($lChk as $lGid => $lArr) {
      $lPri = 0;
      if (!empty($lArr))
      foreach ($lArr as $lLvl => $lTmp) {
        $lPri += $lLvl;
      }
      if (!empty($lPri)) {
        $lSql = 'INSERT INTO al_gru_fie SET ';
        $lSql.= 'gid='.intval($lGid).',';
        $lSql.= 'src="'.$lSrc.'",';
        $lSql.= 'code="'.$lKey.'",';
        $lSql.= 'level='.$lPri;
        #addMsg($lSql);
        $lQry -> query($lSql);
      }
    }
    $this -> redirect();
  }


  public function actSelect() {
    $lUsr = CCor_Usr::getInstance();
    $lReq = $this -> getReq('rig');
    $lArr = explode('-', $lReq);
    if (count($lArr) == 2) {
      $lUsr -> setPref('gru-fie.src', $lArr[0]);
      $lUsr -> setPref('gru-fie.key', $lArr[1]);
    }
    $this -> redirect();
  }

}