<?php
class CInc_Cor_Usr_Cond extends CCor_Obj {

  protected $mId;
  protected $mMem;
  protected $mCnd;

  public function __construct($aUid, & $aMem) {
    $this -> mId = $aUid;
    $this -> mMem = $aMem;
    $this -> mCnd = array();
    $this -> loadConditions();
  }

  protected function loadConditions() {
    $lSql = 'SELECT DISTINCT(cnd_sql) FROM al_gru ';
    $lSql.= 'WHERE id IN ('.$this -> mMem -> getStr().')';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lCnd = $lRow['cnd_sql'];
      $lCnd = strtr($lCnd, array('{uid}' => $this -> mId));
      if (!empty($lCnd)) {
        $this -> mCnd[] = $lCnd;
      }
    }
  }

  public function getCondSql() {
    if (empty($this -> mCnd)) {
      return '';
    }
    $lRet = '(';
    foreach ($this -> mCnd as $lCnd) {
      $lRet.= '('.$lCnd.') AND ';
    }
    $lRet = strip($lRet, 5).')';
    $lStr = $this -> mMem -> getStr();
    #$lRet = strtr($lRet, array('{member}' => $lStr, '{user}' => $this -> mId));
    return $lRet;
  }

}