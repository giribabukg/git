<?php
class CInc_App_Chk extends CCor_Obj {
  
  protected $mSrc;
  protected $mJobId;
  protected $mChg;
  protected $mUpd;
  protected $mVal;
  
  public function __construct($aSrc, $aJobId) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = intval($aJobId);
    $this -> mChg = array();
    $this -> mUpd = array();
  }
  
  public function getFromPost($aVal, $aOld = array()) {
    $this -> mChg = array();
    $this -> mUpd = array();
    $this -> mVal = $aVal;
    
    if (!empty($aVal)) 
    foreach ($aVal as $lKey => $lVal) {
      $lNewVal = intval($lVal);
      $lOldVal = isset($aOld[$lKey]) ? intval($aOld[$lKey]) : 0;
      $lBit = 0;
      if ($lOldVal != $lNewVal) {
        $this -> mUpd[$lKey] = $lNewVal;
        $lBit = 4;
      }
      if (-1 == $lNewVal) {
        $lNewVal = 2;
      } 
      $lNewVal = $lNewVal | $lBit;
      $this -> mChg[$lKey] = $lNewVal;
    }
  }
  
  public function getVal($aKey) {
    return (isset($this -> mVal[$aKey])) ? $this -> mVal[$aKey] : 0;
  }
  
  public function isAllOkay() {
    foreach ($this -> mVal as $lKey => $lVal) {
      if (1 != $lVal) return FALSE;
    }
    return TRUE;
  }
  
  public function hasEmpty() {
    // one or more items left in grey state
    foreach ($this -> mVal as $lKey => $lVal) {
      if (0 == $lVal) return TRUE;
    }
    return FALSE;
  }
  
  public function update() {
    if (empty($this -> mUpd)) {
      return TRUE;
    }
    $lUid = CCor_Usr::getAuthId();
    $lQry = new  CCor_Qry();
    foreach ($this -> mUpd as $lKey => $lVal) {
      $lSql = 'UPDATE al_job_chk SET ';
      $lSql.= 'user_id='.$lUid.',';
      $lSql.= 'datum=NOW(),';
      $lSql.= 'status='.intval($lVal).' ';
      $lSql.= 'WHERE 1 ';
      $lSql.= 'AND src="'.$this -> mSrc.'" ';
      $lSql.= 'AND src_id='.$this -> mJobId.' ';
      $lSql.= 'AND check_id='.intval($lKey);
      $lQry -> query($lSql);    
    }  
  }
  
  public function getSerialized() {
    $lArr = array('chk' => $this -> mChg);
    return serialize($lArr);
  }
  
  
}