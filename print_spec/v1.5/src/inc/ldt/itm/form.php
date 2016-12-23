<?php
class CInc_Ldt_Itm_Form extends CHtm_Form {
  
  public function __construct($aLid, $aAct, $aCaption) {
    $this -> mLid = intval($aLid);
    parent::__construct($aAct, $aCaption, 'ldt-itm&lid='.$this -> mLid);
    $this -> setParam('lid', $this -> mLid);
    $this -> setParam('old[lid]', $this -> mLid);
    $this -> setParam('val[lid]', $this -> mLid);
    $this -> getMaster();
    $this -> mCap.= ' / '.$this -> mMas['name_'.LAN];
    
    $lCol = $this -> mMas['fac_cols'];
    $this -> mFieDef = CCor_Res::get('fie', 'pro,tpl,pac');
    if (!empty($lCol)) {
      $lCol = explode(',', $lCol);
      $lCnt = 1;
      foreach ($lCol as $lVal) {
        if (isset($this -> mFieDef[$lVal])) {
          $lFie = $this -> mFieDef[$lVal];
          $lFie['alias'] = 'f'.$lCnt;
          $this -> addDef($lFie);
          $lCnt++;
        }
      }
    }
    $this -> addDef(fie('days','Days'));
  }
  
  protected function getMaster() {
    $lSql = 'SELECT * FROM al_ldt_master WHERE id='.$this -> mLid;
    $lQry = new CCor_Qry($lSql);
    $this -> mMas = $lQry -> getDat();
    $this -> setVal('days', $this -> mMas['std_val']);
  }
  
  public function load($aId) {
    $lId = intval($aId);
    $lSql = 'SELECT * FROM al_ldt_itm WHERE id='.$lId;
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
      $this -> setParam('val[id]', $lId);
      $this -> setParam('old[id]', $lId);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}