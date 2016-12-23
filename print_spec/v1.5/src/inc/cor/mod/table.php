<?php
class CInc_Cor_Mod_Table extends CCor_Mod_Base {

  protected $mTbl;
  protected $mInsertId;

  public function __construct($aTbl, $aKey = 'id', $aAutoIncKey = 'id') {
    $this -> mTbl = $aTbl;

    if (FALSE === strpos($aKey, ',')){
      $this -> mKey[0] = $aKey;
    } else {
      $this -> mKey = explode(',',$aKey);
    }

    if (FALSE === strpos($aAutoIncKey, ',')){
      $this -> mAutoIncKey[0] = $aAutoIncKey;
    } else {
      $this -> mAutoIncKey = explode(',',$aAutoIncKey);
    }

    $this -> mInsertId = NULL;
    $this -> mAutoInc = TRUE;
  }

  protected function doUpdate() {
    $lSql = 'UPDATE '.$this -> mTbl.' SET ';

    foreach ($this -> mOld as $lKey => $lVal) {
      if ($this -> fieldHasChanged($lKey)) {
        $lNew = $this -> getVal($lKey);
        $lSql.= $lKey.'='.esc($lNew).',';
      }
    }
    $lSql = strip($lSql, 1);
    $lSql.= ' WHERE';
    foreach($this -> mKey as $lKey) {
      $lSql.= ' '.$lKey.' = '.esc($this -> getOld($lKey)).' AND';
    }
    $lSql = strip($lSql,4);
    $lSql.= ' LIMIT 1';
    return CCor_Qry::exec($lSql);
  }

  protected function doInsert() {
    $lSql = 'INSERT INTO '.$this -> mTbl.' SET ';

    foreach ($this -> mVal as $lKey => $lVal) {
      if ($this -> mAutoInc) {
        if(in_array($lKey, $this -> mAutoIncKey)) {  // autoinc
#        if ($lKey == $this -> mKey) {  // autoinc
          continue;
        }
      }
      $lSql.= $lKey.'="'.mysql_real_escape_string($lVal).'",';
    }
    $lSql = strip($lSql, 1);

    $this -> dbg($lSql);
    if ($this -> mTest) {
      return TRUE;
    } else {
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

  public function doDelete($aId) {
    $lSql = 'DELETE FROM '.$this -> mTbl.' ';
    $lSql.= 'WHERE '.$this -> mKey[0].'= "'.addslashes($aId).'" LIMIT 1';
    /* neue Lösung: muß noch getestet werden!
    $lSql.= 'WHERE 1';
    foreach($this -> mAutoIncKey as $lKey) {
      $lSql.= ' AND '.$lKey.' = "'.$this -> getOld($lKey).'"';
    }
    $lSql.= ' LIMIT 1';
    #echo '<pre>---table.php---';var_dump($lSql,'#############');echo '</pre>';
    */
    return CCor_Qry::exec($lSql);
  }

  public function getInsertId() {
    return $this -> mInsertId;
  }

  public function load($aId) {
    return $this->doLoad($aId);
  }

  protected function doLoad($aKeys) {
    $lSql = 'SELECT * FROM '.$this -> mTbl.' WHERE ';
    if (count($this -> mKey) == 1) {
      $lSql.= $this -> mKey[0].'='.esc($aKeys);
    } else {
      if (!is_array($aKeys)) throw new Exception('Array of keys expected');
      $lSql.= '1';
      foreach ($aKeys as $lKey => $lVal) {
        $lSql.= ' AND '.$lKey.' = '.esc($lVal);
      }
    }
    $lQry = new CCor_Qry($lSql);
    return $lQry->getDat();
  }

}