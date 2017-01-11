<?php
class CInc_Job_Pro_Sub_Item_Dat extends CJob_Pro_Dat {

  public function __construct($aSrc = 'sub') {
    parent::__construct($aSrc);
  }

  protected function doLoad($aID) {
    $lID = intval($aID);

    $lUserCondition = $this -> addUserConditions();

    $lSQL = 'SELECT * FROM al_job_sub_'.intval(MID);
    $lSQL .= ' WHERE 1'.$lUserCondition.' AND id='.esc($lID);
    $lQry = new CCor_Qry($lSQL);
    if ($lRow = $lQry -> getDat()) {
      $this -> assign($lRow);
      $this -> mJobId = $aID;
      $this -> mJid = $this -> mJobId;
      return TRUE;
    } else {
      return FALSE;
    }
  }

  protected function addUserConditions() {
    $lRet = '';

    $lUserID = CCor_Usr::getAuthId();
    $lSQL = 'SELECT * FROM al_cnd WHERE usr_id='.$lUserID.' AND mand='.MID;
    $lQry = new CCor_Qry($lSQL);
    foreach ($lQry as $lRow) {
      if ($lRow['cond'] !== '') {
        $lArr = explode(';', $lRow['cond']);
        foreach ($lArr as $lVal) {
          list($lField, $lOp, $lValue) = explode(' ', $lVal, 3);

          $lRet .= ' AND '.backtick($lField).$lOp.esc($lValue);
        }
      }
    }

    return $lRet;
  }
}