<?php
class CInc_Cor_Res_Rolmytask extends CCor_Res_Plugin {
  
  /**
   * Roles Array
   * @var array
   */
  public $mArrRol = Array();
  
  /**
   * Crp Steps Array
   * @var array
   */
  public $mArrCrpStp = Array();
  
  /**
   * Crp Status Array
   * @var array
   */
  public $mArrCrpStatus = Array();
  
  /**
   * Crp Master Array
   * @var array
   */
  public $mArrCrpMaster = Array();
  
  
  protected function refresh($aParam = NULL) {
    $lCkey = 'cor_res_rolmytask_'.MID;
    if ($lRet = $this -> getCache($lCkey)) {
      return $lRet;
    }
    $lRet = array();

    $this -> mArrRol = CCor_Res::get('rol');
    $this -> mArrCrpStp = CCor_Res::get('crpstep');
    $this-> mArrCrpStatus = CCor_Res::get('crpstatus');
    $this-> mArrCrpMaster = CCor_Res::get('crpmaster');
        
    /**
     * Das Ziel ist, ein Array mit folgende Infos erstellen.
     * ['role_name'] -> fuer welche role ist das Funtion aktiviert
     * ['webstatus'] -> from webstatus von aktivierte step
     * ['src'] -> job type
     */
    
    $lSql= 'SELECT s.stp_id,s.role_id,s.crp_id,r.mand,s.show_mytask FROM al_rol_rig_stp AS s LEFT JOIN al_rol AS r ON s.role_id=r.id  WHERE s.show_mytask="Y" AND r.mand='.MID;
    $lQry = new CCor_Qry($lSql);
    $lArrRolMyTask = Array();
    foreach($lQry as $lRow) {
      $lTemp = Array();
      // find out the role alias
      $lTemp['alias'] = $this -> getAlias($lRow);
      // Find out jobtyp
      $lTemp['src'] = $this -> getSrc($lRow);
      // Find out webstatus
      $lStep_Id = $lRow['stp_id'];
      $lTemp['webstatus'] = $this -> getWebstatus($lStep_Id);
      $lRet[]= $lTemp;
    }
    $this -> setCache($lCkey, $lRet);
    return $lRet;
  }
  
  /**
   * Return Alias Name
   * @param $aRow array Roles
   * @return $lRet string Alias
   */
  public function getAlias($aRow){
    $lRet = '';
    $lRoleId = $aRow['role_id'];
    if (isset($this -> mArrRol[$lRoleId]['alias'])){
      $lRet = $this -> mArrRol[$lRoleId]['alias'];
    }
    return $lRet;
  }
  
  /**
   * find out the job type
   * @param $aRow array Roles
   * @return $lRet string job type
  */
  public function getSrc($aRow){
    $lRet = '';
    $lCrp_Id = $aRow['crp_id'];
    if (isset($this-> mArrCrpMaster[$lCrp_Id]['code'])){
      $lRet = $this-> mArrCrpMaster[$lCrp_Id]['code'];
    }
    return $lRet;
  }
  
  /**
   * find out the webstatus
   * @param $aStep_Id string  Step Id
   * @return $lRet string Webstatus
  */
  public function getWebstatus($aStep_Id) {
    $lRet = '';

    if (array_key_exists($aStep_Id, $this -> mArrCrpStp)) {
      $lFrom_Id =  $this -> mArrCrpStp[$aStep_Id]['from_id'];
      if (isset($this -> mArrCrpStatus[$lFrom_Id]['status'])) {
        $lRet = $this -> mArrCrpStatus[$lFrom_Id]['status'];
      }
    }

    return $lRet;
  }
}