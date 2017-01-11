<?php
class CInc_Svc_DelNonActUser extends CSvc_Base {
  /**
   *
   * @author polash Sarker
   * When a user has not logged into WAVE for set period of time then that user should become inactive
   * and added deleted messege to his user history with reason
   *
   * For this service, following variable need to active in cfg file:
   * $this->mVal['del.nonact.users'] = TRUE
   * 
   * CCor_Cfg::get('del.nonact.users.days', XXX); after XXX days user will be deleted (del=Y)
   * 
   **/
  
  protected function doExecute (){
    // Service activation
    $lDelNonActiveUser = CCor_Cfg::get('del.nonact.users', FALSE);
    $lUsrDelDays = CCor_Cfg::get('del.nonact.users.days', 180);
    
    if ($lDelNonActiveUser) {
      $lArr = array();
      $lQry = new CCor_Qry('SELECT *FROM al_usr where del !="Y" AND mand IN (0,' . MID . ')');
      foreach ($lQry as $lRow) {
        $lItm = array();
        $lItm['id'] = $lRow['id'];
        $lItm['lastlogin'] = $lRow['lastlogin'];
        $lItm['lastreset_password'] = $lRow['lastreset_password'];
        $lArr[] = $lItm;
      }
      foreach ($lArr as $lUsr) {
        $lUsrLastLogin = $lUsr['lastlogin'];
        $lUsrId = $lUsr['id'];
        $lPresentDate = date('Y-m-d', strtotime(date("Y-m-d")));
        $lUserDelDate = date('Y-m-d', strtotime($lUsrLastLogin . " + $lUsrDelDays days"));
        
        if ($lUserDelDate < $lPresentDate) {
          $lSql = 'UPDATE al_usr set del="Y" where id="' . $lUsrId .'" AND mand IN (0,' . MID . ')';
          CCor_Qry::exec($lSql);
          CUsr_External_Cnt::extUsrHis($lUsrId, '', date("Y-m-d", time()), 'Deleted account due to inactivity');
        }
      }
    }
    return true;
  }
}