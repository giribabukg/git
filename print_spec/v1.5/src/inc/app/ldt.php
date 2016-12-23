<?php
class CInc_App_Ldt extends CCor_Obj {
  
  public function __construct($aLid, & $aJob) {
    $this -> mLid = intval($aLid);
    $this -> mJob = & $aJob;
    $this -> loadMaster();
  }
  
  protected function loadMaster() {
    $lSql = 'SELECT * FROM al_ldt_master WHERE id='.$this -> mLid;
    $lQry = new CCor_Qry($lSql);
    $this -> mMas = $lQry -> getDat();
    $this -> mFie = CCor_Res::extract('id','alias','fie','pro,tpl,pac');
    $lCol = $this -> mMas['fac_cols'];
    $this -> mFac = array();
    if (!empty($lCol)) {
      $lCol = explode(',', $lCol);
      $lCnt = 1;
      foreach ($lCol as $lFid) {
        if (isset($this -> mFie[$lFid])) {
          $this -> mFac['f'.$lCnt] = $this -> mFie[$lFid];
          $lCnt++; 
        }
      }
    }
  }  
  
  protected function getVal($aAlias) {
    return $this -> mJob[$aAlias];
  }
  
  protected function getMatch($aRow) {
    if (empty($this -> mFac)) {
      return 0;
    }
    $lRet = 2;
    foreach ($this -> mFac as $lKey => $lAli) {
      $lFac = $aRow[$lKey];
      $lVal = $this -> mJob[$lAli];
      $this -> dbg('Comparing '.$lAli.' Job: '.$lVal.' FAC:'.$lFac, mlWarn);
      if ('' == $lFac) {
        $lRet = 1;
      } else {
        if ($lFac != $lVal) {
          return 0;
        }
      }
    }
    return $lRet;
  }
  
  public function getDays() {
    $lRet = $this -> mMas['std_val'];
    $lSql = 'SELECT * FROM al_ldt_itm WHERE lid='.$this -> mLid.' ORDER BY f1,f2,f3,f4';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMat = $this -> getMatch($lRow);
      if ($lMat > 0) {
        $lRet = $lRow['days'];
        if (2 == $lMat) {
          $this -> dbg('Perfect match!', mlWarn);
          BREAK;
        }
      }
    }
    return $lRet; 
  }
}