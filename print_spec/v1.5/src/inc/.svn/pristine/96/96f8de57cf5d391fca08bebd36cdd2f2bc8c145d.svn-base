<?php
class CInc_App_Tpl extends CCor_Tpl {

  public $mSubject = '';
  public $mBody = '';

  public function __construct() {
    $this -> mSubject = '';
    $this -> mBody    = '';
  }

  public function addUserPat($aId, $aPrefix = 'usr') {
    $lId = intval($aId);

    $lSql = 'SELECT * FROM al_usr WHERE id='.$lId;
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      foreach ($lRow as $lKey => $lVal) {
        $this -> setPat($aPrefix.'.'.$lKey, $lVal);
      }
      $this -> setPat($aPrefix.'.fullname', cat($lRow['firstname'], $lRow['lastname']));

    }
  }
  public function loadTemplate($aTid = 0, $aName = '', $aLang = LAN) {
    if (!defined(MID)) {
      define('MID', 0);
    }
    if (0 < $aTid) {
      $lSql = 'SELECT * FROM `al_eve_tpl` WHERE `mand` IN(0,'.MID.') AND `id`='.intval($aTid);
      $lQry = new CCor_Qry($lSql);
    } else {
      $lSql = 'SELECT * FROM `al_eve_tpl` WHERE `mand` IN(0,'.MID.') AND `name`='.esc($aName).' AND `lang`='.esc($aLang).' ORDER BY `mand` DESC LIMIT 0,1';
      $lQry = new CCor_Qry($lSql);
    }
    if ($lRow = $lQry -> getDat()) {
      $this -> setSubject($lRow['subject']);
      $this -> setBody($lRow['msg']);
      return TRUE;
    }
    return FALSE;
  }

  public function setSubject($aSubject) {
    $this -> mSubject = $aSubject;
  }

  public function getSubject() {
    $this -> mDoc = $this -> mSubject;
    return $this -> getCont();
  }

  public function setBody($aBody) {
    $this -> mBody = $aBody;
  }

  public function getBody() {
    $this -> mDoc = $this -> mBody;
    return $this -> getCont();
  }

  protected function createPlain() {
    if (isset($this -> mPlain)) return;
    $this -> mPlain = new CHtm_Fie_Plain();
    $this -> mFie = CCor_Res::getByKey('alias', 'fie');
  }

  public function setJobValues($aVal, $aPrefix = 'val') {
    $this -> createPlain();
    foreach ($aVal as $lKey => $lVal) {
      if (isset($this -> mFie[$lKey])) {
        $lDef = $this -> mFie[$lKey];
        $lCap = $lDef['name_'.LAN];
        $lTxt = $this -> mPlain -> getPlain($lDef, $lVal);
      } else {
        $lCap = '';
        $lTxt = $lVal;
      }
      $this -> setPat('bez.'.$lKey, $lCap);
      $this -> setPat('val.'.$lKey, $lTxt);
    }
  }
  
  public function setUserNamesLis($aStringOfNames) {
    $this -> setPat('usr.incopy', $aStringOfNames);
  }

  public function getRep($aPattern) {
    if(isset($this -> mPat[$aPattern])) {
      return true;
    } else {
      return false;
    }
  }


}