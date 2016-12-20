<?php
class CInc_Job_His_Mod extends CCor_Mod_Table {

  public function __construct($aSrc, $aJobId, $aType = htComment) {
    parent::__construct('al_job_his');
    $this -> mSrc = $aSrc;
    $this -> mJid = $aJobId;
    $this -> mTyp = intval($aType);
    $this -> addField(fie('subject'));
    $this -> addField(fie('msg'));
  }

  protected function doInsert() {
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();

    $this -> mVal['mand'] = intval(MID);
    $this -> mVal['src'] = $this -> mSrc;
    $this -> mVal['src_id'] = $this -> mJid;
    $lSql = 'INSERT INTO '.$this -> mTbl.' SET ';
    foreach ($this -> mVal as $lKey => $lVal) {
      if ($lKey == $this -> mKey) {  // autoinc
        continue;
      }
      $lSql.= $lKey.'="'.addslashes($lVal).'",';
    }
    $lSql.= 'datum=NOW(),';
    $lSql.= 'typ='.$this -> mTyp.',';
    $lSql.= 'user_id='.$lUid;

    $lQry = new CCor_Qry();
    $lRet = $lQry -> query($lSql);
    if ($lRet) {
      $this -> mInsertId = $lQry -> getInsertId();
    } else {
      $this -> mInsertId = NULL;
    }
    return $lRet;
  }

}