<?php
class CInc_Cor_Usr_Mem extends CCor_Obj {
  
  private $mId;
  private $mMem;
  
  public function __construct($aUid) {
    $this -> mId = $aUid;
    $this -> loadMemberShip();
  }
  
  protected function loadMemberShip() {
    $this -> mMem = array();
    $lQry = new CCor_Qry('SELECT gid FROM al_usr_mem WHERE uid='.$this -> mId);
    foreach ($lQry as $lRow) {
      $lGid = intval($lRow['gid']);
      $this -> mMem[$lGid] = TRUE; 
    }
  }
  
  public function isMemberOf($aGid) {
    $lGid = intval($aGid);
    return (isset($this -> mMem[$lGid]));
  }
  
  public function getArray() {
    return array_keys($this -> mMem);
  }
  
  public function getStr() {
    return implode(',', $this -> getArray());  
  }
}  