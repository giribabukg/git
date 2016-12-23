<?php
class CInc_Api_Alink_Query_Getartcalc extends CApi_Alink_Query implements IteratorAggregate {

  public function __construct($aJobId) {
    parent::__construct('getArtCalc');
    $this -> addParam('jobid', $aJobId);
    $this -> addParam('sid', MAND);
    $this -> mLoaded = FALSE;
  }

  public function getIterator() {
    if (!$this -> mLoaded) {
      $this -> query();
      $this -> mLoaded = TRUE;
    }
    if ($this -> mResponse) {
      $lRows = $this -> mResponse -> getVal('item');
    }

    $lRet = array();

    if (!empty($lRows)) {
      foreach ($lRows as $lRow) {
        $lTmp = array();
        foreach ($lRow as $lKey => $lVal) {
          $lTmp[(string)$lKey] = $lVal;
        }
        $lRet[] = $lTmp;
      }
    }
    return new ArrayIterator($lRet);
  }

  public function getArray($aKeyField = NULL) {
    $lRet = array();
    $lIte = $this -> getIterator();

    if (NULL == $aKeyField) {
      foreach ($lIte as $lRow) {
        $lRet[] = $lRow;
      }
    } else {
      foreach ($lIte as $lRow) {
        $lRet[$lRow[$aKeyField]] = $lRow;
      }
    }
    return $lRet;
  }

}