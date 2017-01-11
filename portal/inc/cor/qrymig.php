<?php
class CInc_Cor_Qrymig extends CCor_Obj implements IteratorAggregate {

  protected $mHandle;
  protected $mDb;
  protected $mSql = '';

  public function __construct($aSql = '', ICor_Sqlmig & $aDb = NULL) {
    if (empty($aDb))
      $this -> mDb  = CCor_Sqlmig::getInstance();
    else
      $this -> mDb  = & $aDb;

    if (!empty($aSql)) $this -> query($aSql);
  }

  public function __destruct() {
    $this -> free();
  }

  function free() {
    if ($this -> mHandle) {
      $this -> mDb -> freeResult($this -> mHandle);
      $this -> mHandle = NULL;
    }
  }

  public function query($aSql) {
    $this -> free();
    $this -> mSql = $aSql;
    $this -> mHandle = $this -> mDb -> query($aSql);
    return $this -> mHandle;
  }

  public function getInsertId() {
    return $this -> mDb -> getInsertId();
  }

  public function getAssocs($aKey = '' ) {
    $lRet = array();
    if (empty($aKey)) {
      while ($lRow = $this -> mDb -> getAssoc($this -> mHandle)) {
        $lRet[] = $lRow;
      }
    } else {
      while ($lRow = $this -> mDb -> getAssoc($this -> mHandle)) {
        $lKey = $lRow[$aKey];
        $lRet[$lKey] = $lRow;
      }
    }
    return $lRet;
  }

  public function getObjects($aKey = '' ) {
    $lRet = array();
    if (empty($aKey)) {
      while ($lRow = $this -> mDb -> getObject($this -> mHandle)) {
        $lRet[] = $lRow;
      }
    } else {
      while ($lRow = $this -> mDb -> getObject($this -> mHandle)) {
        $lKey = $lRow -> $aKey;
        $lRet[$lKey] = $lRow;
      }
    }
    return $lRet;
  }

  public function getArray() {
    return $this -> mDb -> getArray($this -> mHandle);
  }

  public function getAssoc() {
    return $this -> mDb -> getAssoc($this -> mHandle);
  }

  public function getObject() {
    return $this -> mDb -> getObject($this -> mHandle);
  }

  public function getDat() {
    $lRow = $this -> mDb -> getAssoc($this -> mHandle);
    if (!$lRow) {
      return FALSE;
    }
    $lRet = new CCor_Dat();
    $lRet -> assign($lRow);
    return $lRet;
  }

  public function getIterator() {
    return new CCor_QryIte($this -> mHandle, $this -> mDb);
  }

  public static function getArr($aSql) {
    $lQry = new self($aSql);
    if ($lRet = $lQry -> getArray()) {
      foreach ($lRet as $lKey => $lVal) {
        $lRes = $lRes.' '.$lVal;
      }
      return trim($lRes);
    } else {
      return FALSE;
    }
  }

  public static function getStr($aSql) {
    $lQry = new self($aSql);
    if ($lRet = $lQry -> getArray()) {
      return $lRet[0];
    } else {
      return FALSE;
    }
  }

  public static function getInt($aSql) {
    $lRet = self::getStr($aSql);
    return ($lRet === FALSE) ? FALSE : intval($lRet);
  }

  public static function exec($aSql) {
    $lQry = new self();
    return $lQry -> query($aSql);
  }

}