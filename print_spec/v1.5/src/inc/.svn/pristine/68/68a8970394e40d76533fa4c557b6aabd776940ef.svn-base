<?php
class CInc_Cor_Usr_Step extends CCor_Obj {
  
  protected $mId;
  protected $mPriv;
  
  public function __construct($aId) {
    $this -> mId = $aId;
    $this -> loadPrivs();
  }
  
  protected function loadPrivs() {
    $lQry = new CCor_Qry();
    $lSql = 'SELECT p.code,p.level FROM al_gru_rig p,al_usr_mem m WHERE m.uid='.$this -> mId.' AND m.gid=p.group_id';
    $lQry -> query($lSql);
    foreach ($lQry as $lRow) {
      $lOld = (isset($this -> mPriv[$lRow['code']])) ? $this -> mPriv[$lRow['code']] : 0; 
      $this -> mPriv[$lRow['code']] = $lOld | intval($lRow['level']);
    }
  }
  
  public function canDo($aCode, $aLevel) {
    if (!isset($this -> mPriv[$aCode])) return false;
    return bitSet($this -> mPriv[$aCode], $aLevel);
  }
  
}