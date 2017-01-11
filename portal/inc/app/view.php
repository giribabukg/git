<?php
class CInc_App_View extends CCor_Obj {
  
  public function __construct($aRef) {
    $this -> mRef = $aRef;
    $this -> mVal = array();
    $this -> setVal('ref', $aRef);
    $this -> setVal('src', 'usr');
    $this -> setVal('mand', MID);
    $this -> setVal('src_id', CCor_Usr::getAuthId());
  }
  
  public function setVal($aKey, $aVal) {
    $this -> mVal[$aKey] = $aVal;
  }
  
  public function getFromPref($aPref = NULL) {
    $lPrf = (NULL == $aPref) ? $this -> mRef : $aPref;
    $lUsr = CCor_Usr::getInstance();
    $this -> setVal('cols', $lUsr -> getPref($lPrf.'.cols'));
    $this -> setVal('ord',  $lUsr -> getPref($lPrf.'.ord'));
    $this -> setVal('sfie', $lUsr -> getPref($lPrf.'.sfie'));
    $this -> setVal('lpp',  $lUsr -> getPref($lPrf.'.lpp'));
  }
  
  public function insert() {
    $lSql = 'INSERT INTO al_usr_view SET ';
    foreach ($this -> mVal as $lKey => $lVal) {
      if (!empty($lVal)) {
        $lSql.= $lKey.'="'.addslashes($lVal).'",';
      }
    }
    $lSql = strip($lSql,1);
    CCor_Qry::exec($lSql);
  }
  
  public static function tableToPref($aId, $aRef = NULL) {
    $lQry = new CCor_Qry('SELECT * FROM al_usr_view WHERE id='.intval($aId));
    if ($lRow = $lQry -> getDat()) {
      $lPrf = (NULL == $aRef) ? $lRow['ref'] : $aRef;
      $lUsr = CCor_Usr::getInstance();
      $lUsr -> setPref($lPrf.'.cols', $lRow['cols']);
      $lUsr -> setPref($lPrf.'.ord',  $lRow['ord']);
      $lUsr -> setPref($lPrf.'.sfie', $lRow['sfie']);
      $lUsr -> setPref($lPrf.'.lpp',  $lRow['lpp']);
    }
  }
}