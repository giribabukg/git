<?php
class CCor_Res_Pcklist extends CCor_Res_Plugin {

  public function get($aParam = NULL) {
    $lDomain = (!empty($aParam['domain'])) ? $aParam['domain'] : 'prnt' ;
       
    if ((!isset($this -> mRet[$lDomain])) OR (NULL === $this -> mRet[$lDomain])) {
      $this -> mRet[$lDomain] = $this -> refresh($aParam);
    }
    return $this -> mRet[$lDomain];
  }

  protected function refresh($aParam = NULL) {
    $lDomain = (!empty($aParam['domain'])) ? $aParam['domain'] : 'prnt' ;
    $lCkey = 'cor_res_picklist_'.$lDomain;
    if ($lRet = $this -> getCache($lCkey)) {
      return $lRet;
    }
   
    $lRet = array();
    // Find Coloumn Nr of Picklist Alias
    // Find Pick List Id from 'prnt'
    //$lSqlFind = 'SELECT DISTINCT(col) from al_pck_columns where domain ="'.$lDomain.'" AND alias ="'.$lAlias.'"';
    //echo $lSqlFind;
    //$lColId = CCor_Qry::getInt($lSqlFind);
        
    // Get Values
    $lSql = 'SELECT *  FROM al_pck_items ';
    $lSql.= 'WHERE mand IN (0,'.MID.') ';
    $lSql.= 'AND domain ="'.$lDomain.'" ';
    $lQry = new CCor_Qry($lSql);
    
    
    foreach($lQry as $lRow) {
      
      $lRet[$lRow->id] = $lRow -> toArray();
    }
    $this -> setCache($lCkey, $lRet);
    return $lRet;
  }

}