<?php
class CInc_Cor_Usr_Pref extends CCor_Dat {

  private $mId;
  private $mMid;
  private $mPrefDef;

  public function __construct($aId) {
    $this -> mId = intval($aId);
    $this -> getPrefs();
  }

  public function __destruct() {
    $lSys = CCor_Sys::getInstance();
    $lSys['usr.pref'] = $this -> mVal;
  }

  private function getPrefs() {
    $lSys = CCor_Sys::getInstance();
    $this -> mVal = $lSys['usr.pref'];
    $this -> mMid = $this['sys.mid'];
    #$this -> dump($this -> mVal, 'PREFERENCES');
  }

  protected function loadPrefDefs() {
    if (isset($this -> mPrefDef)) {
      return;
    }
    $this -> mPrefDef = CCor_Res::get('syspref');
  }

  public function loadPrefsFromDb() {
    $this -> loadPrefDefs();
    foreach ($this -> mPrefDef as $lRow) {
      $lPref[$lRow['code']] = $lRow['val'];
    }
    $lQry = new CCor_Qry();
    $lQry -> query('SELECT code,val FROM al_usr_pref WHERE uid='.$this -> mId.' AND mand=0');
    foreach ($lQry as $lRow) {
      $lPref[$lRow['code']] = $lRow['val'];
    }
    $lMid = (isset($lPref['sys.mid'])) ? intval($lPref['sys.mid']) : MID;
    $this -> mMid = $lMid;
    $lQry -> query('SELECT code,val FROM al_usr_pref WHERE uid='.$this -> mId.' AND mand='.$lMid);
    foreach ($lQry as $lRow) {
      $lPref[$lRow['code']] = $lRow['val'];
    }
    $this -> mVal = $lPref;
    $lSys = CCor_Sys::getInstance();
    $lSys['usr.pref'] = $this -> mVal;
    #$this -> dump($this -> mVal, 'PREFERENCES');
  }

  protected function doSet($aKey, $aValue) {
    if (is_array($aValue)) {
      $lValue = serialize($aValue);
    } else {
      $lValue = $aValue;
    }

    if ($aKey == "sys.mid" OR $aKey == "sys.mand") {
      $lMid = 0 ; // sys.mid = 0 and sys.mand = 0 for administrators
    } else {
      $lMid = MID;
    }

    $lSql = 'REPLACE INTO al_usr_pref SET ';
    $lSql.= 'code="'.addslashes($aKey).'", ';
    $lSql.= 'val="'.addslashes($lValue).'", ';
    $lSql.= 'mand='.intval($lMid).', ';
    $lSql.= 'uid='.intval($this -> mId).';';
    CCor_Qry::exec($lSql);

    parent::doSet($aKey, $aValue);
  }
}