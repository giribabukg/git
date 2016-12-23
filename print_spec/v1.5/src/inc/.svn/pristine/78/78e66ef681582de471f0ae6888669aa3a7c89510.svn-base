<?php
class CInc_Gru_His_Mod extends CCor_Mod_Table {
  
  public function __construct() {
    parent::__construct('al_gru_his');
    $this -> addField(fie('subject'));
    $this -> addField(fie('msg'));
  }
  
  protected function doInsert() {
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    $lId = $_REQUEST['id'];
    
    $lSql = 'INSERT INTO '.$this -> mTbl.' SET ';
    foreach ($this -> mVal as $lKey => $lVal) {
      if ($lKey == $this -> mKey) {  // autoinc
        continue;
      }
      $lSql.= $lKey.'="'.addslashes($lVal).'",';
    }
    $lSql.= 'datum=NOW(),';
    $lSql.= 'typ='.htComment.',';
    $lSql.= 'uid='.$lUid.',';
    $lSql.= 'gru_id='.$lId;
    $this -> dbg($lSql);
    
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