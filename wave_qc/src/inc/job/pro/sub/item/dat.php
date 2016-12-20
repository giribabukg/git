<?php
class CInc_Job_Pro_Sub_Item_Dat extends CJob_Pro_Dat {

  public function __construct($aSrc = 'sub') {
    parent::__construct($aSrc);
  }

  protected function doLoad($aId) {
    $lCond = $this -> addUserConditions();
    #echo '<pre>---dat.php---'.get_class().'---';var_dump($lCond,'#############');echo '</pre>';
    $lId = intval($aId);
    $lSql = 'SELECT * FROM al_job_sub_'.intval(MID);
    $lSql .= ' WHERE 1'.$lCond.' AND id='.esc($lId);
    #echo '<pre>---doLoad---'.get_class().'---';var_dump($lSql,'#############');echo '</pre>';
    $lQry = new CCor_Qry($lSql);
    
    if ($lRow = $lQry -> getDat()) {
      $this -> assign($lRow);
      #echo '<pre>---dat.php---'.get_class().'---';var_dump($this -> mVal['id'],'#############');echo '</pre>';
      $this -> mJobId = $aId;//$this -> doGet('jobid');#$this -> mVal['id'];
      $this -> mJid = $this -> mJobId;
      return TRUE;
    } else {
      return FALSE;
    }
  }

  protected function addUserConditions() {
    $lRet = '';

    $lUid = CCor_Usr::getAuthId();
    $lSql = 'SELECT * FROM al_cnd WHERE usr_id='.$lUid.' AND mand='.MID;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      // wenn Feld 'Cond' empty, No Condition
      if ($lRow['cond'] !== ''){
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