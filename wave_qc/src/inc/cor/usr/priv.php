<?php
class CInc_Cor_Usr_Priv extends CCor_Obj {

  protected $mId;
  protected $mPriv;

  public function __construct($aId, $aMid = NULL, $aRig = '') {
    $this -> mId = intval($aId);
    $this -> mMid = (empty($aMid)) ? MID : intval($aMid);
    $this -> mRig = $aRig;
    $this -> loadPrivs();
  }

  protected function loadPrivs() {
  	$lUsr = CCor_Usr::getInstance();
  	$lUsrToBackupId = $lUsr->shallIBackupAnyUsr();
  	if ($lUsrToBackupId !== FALSE) {
  		$lSqlPart = ' IN('.$this->mId.','.$lUsrToBackupId.') ';
  	}else $lSqlPart = ' = '.$this->mId.' ';

    $lSql = 'SELECT code,level,mand FROM al_usr_rig ';
    $lSql.= 'WHERE user_id'.$lSqlPart;
    if(!empty($this -> mRig))
      $lSql.= 'AND `right` LIKE "'.$this -> mRig.'" ';
    $lSql.= 'AND mand IN (0,'.$this -> mMid.') ORDER BY level';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if(MID == $lRow['mand'])
        $this -> mPriv[$lRow['code']] = intval($lRow['level']);
      elseif(0 == $lRow['mand'])
        $this -> mPriv[$lRow['code']] = intval($lRow['level']);
    }
    $lSql = 'SELECT p.code,p.level,p.mand FROM al_gru_rig p,al_usr_mem m ';
    $lSql.= 'WHERE m.uid'.$lSqlPart.' AND m.gid=p.group_id ';
    
    if(!empty($this -> mRig))
      $lSql.= 'AND `right` LIKE "'.$this -> mRig.'" ';
    $lSql.= 'AND p.mand IN (0,'.$this -> mMid.')';
    $lQry -> query($lSql);
    foreach ($lQry as $lRow) {
      if(MID == $lRow['mand'])
        $lKey = $lRow['code'];
      elseif(0 == $lRow['mand'])
        $lKey = $lRow['code'];

      $lOld = (isset($this -> mPriv[$lKey])) ? $this -> mPriv[$lKey] : 0;
      $this -> mPriv[$lKey] = $lOld | intval($lRow['level']);
    }
  }

  public function canDo($aCode, $aLevel) {
    if (!isset($this -> mPriv[$aCode])) return false;
    return bitSet($this -> mPriv[$aCode], $aLevel);
  }

}