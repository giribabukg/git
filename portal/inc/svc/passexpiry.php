<?php
/**
 * 
 * @author polash Sarker
 * User will get remainder email for password change based on CCor_Cfg::get('password.expire.email.remainder') before password disable
 * If user are in several group then take min. pass_exp value
 * If min. pass_exp is "0" then take value from config
 * Password will be disable if user not change password for a long time based on CCor_Cfg::get('password.expire.days')
 * 
 */

class CInc_Svc_Passexpiry extends CSvc_Base
{

  protected function doExecute ()
  {
    $this -> mEmailTemplate = CCor_Cfg::get('tpl.email');
    $CtokenExpiration = CCor_Cfg::get('token.expiration',3);
    //Password expire days (lastresetpass and expiredays)
    $lExpireDays = CCor_Cfg::get('password.expire.days');
    //When users will get password remainder email before expire?? defined following $lExpiryRemainder variable
    $lExpiryRemainder = CCor_Cfg::get('password.expire.email.remainder',5);
    //Service activation; if variable not available in config then service are disable/inactive
    $lPasswordExpiry = CCor_Cfg::get('login.password.expiry.active', FALSE);
    //Token expiration
    $lUserEmailConfirmationTokenExpiry = date('Y-m-d', strtotime("+$CtokenExpiration days"));
    $lArr = CCor_Res::extract('id', 'name_' . LAN, 'mand');
    $lPortalName = (isset($lArr[MID])) ? $lArr[MID] : CUSTOMER_NAME;
    
    //If service is active
    if ($lPasswordExpiry) {
      $lArr = array();
      $lSql = 'SELECT DISTINCT u.anrede, u.firstname, u.lastname, u.id, min(g.pass_exp) as pass_exp , u.email,u.lastreset_password, u.password_disable';
      $lSql.= ' FROM al_usr_mem m, al_gru g,al_usr u';
      $lSql.= ' WHERE pass_exp <> -1  AND g.id =  m.gid AND m.uid =  u.id AND u.del <> "Y" AND u.password_disable <> "Y" AND u.mand IN ('.MID.') GROUP BY u.email';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lItm = array();
        $lItm['id'] = $lRow['id'];
        $lItm['anrede'] = $lRow['anrede'];
        $lItm['firstname'] = $lRow['firstname'];
        $lItm['lastname'] = $lRow['lastname'];
        $lItm['email'] = $lRow['email'];
        $lItm['lastreset_password'] = $lRow['lastreset_password'];
        $lItm['password_disable'] = $lRow['password_disable'];
        $lPassExp = ($lRow['pass_exp'] !=0) ? $lRow['pass_exp']: $lExpireDays;
        $lItm['pass_exp'] = $lPassExp;
        $lArr[] = $lItm;
      }
      foreach ($lArr as $lUsr) {
        if ($lUsr['pass_exp'] !=0) {
          $lToken = CApp_Pwd::createNewToken();
          $lLastResetPass = $lUsr['lastreset_password'];
          $lUsrId = $lUsr['id'];
          $lEmail = $lUsr['email'];
          $lExpireDays = $lUsr['pass_exp'];
          $lRecNam = $lUsr['firstname'] . ' ' . $lUsr['lastname'];
          $lPresentDate = date('Y-m-d', strtotime(date("Y-m-d")));
          $lExpireDate = date('Y-m-d', strtotime($lLastResetPass . " + $lExpireDays days"));
          $lExpiryRemainderEmail = date('Y-m-d',strtotime($lExpireDate . " - $lExpiryRemainder days"));
          if ($lExpiryRemainderEmail == $lPresentDate) {
            $lSql = 'UPDATE al_usr SET token=' . esc($lToken) .', tokenlifetime= ' . esc($lUserEmailConfirmationTokenExpiry) .' WHERE email=' . esc($lEmail) . ' ';
            $lQry -> query('SELECT * FROM al_eve_tpl WHERE id=' .esc($this -> mEmailTemplate['remainderpass']));
            $lDat = $lQry -> getDat();
            $lTpl = new CCor_Tpl();
            $lTpl -> setPat('portal_name', $lPortalName);
            $lTpl -> setPat('exp_date', $lUserEmailConfirmationTokenExpiry);
            $lTpl -> setPat('to.name', $lRecNam);
            $lTpl -> setPat('to.anrede', $lUsr['anrede']);
            if (in_array($lUsr['anrede'], array('Herr', 'Mr.' )))
              $lTpl -> setPat('geehrte', 'geehrter');
            elseif (in_array($lUsr['anrede'], array('Frau', 'Mrs.', 'Miss' )))
              $lTpl -> setPat('geehrte', 'geehrte');
            else
              $lTpl -> setPat('geehrte', 'geehrte/r');
            $lTpl -> setPat('link', CCor_Cfg::get('base.url') . 'index.php?act=log.passupdate&email=' .$lEmail . '&confirmtoken=' . $lToken);
            
            $lTpl -> setPat('from.anrede', lan('lib.salutation.value'));
            $lTpl -> setPat('from.firstname', 'Administrator');
            $lTpl -> setPat('from.lastname', 'Team');
            $lTpl -> setPat('from.email', 'info@5flow.eu');
            $lTpl -> setPat('from.phone', '');
            
            $lTpl -> setDoc($lDat['subject']);
            $lSub = $lTpl -> getContent();
            $lTpl -> setDoc($lDat['msg']);
            $lTxt = $lTpl -> getContent();
            
            $lFrmAdr = 'info@5flow.eu';
            $lFrmNam = '5Flow GmbH';
            $lMai = new CApi_Mail_Item($lFrmAdr, $lFrmNam, $lRecAdr = $lEmail, $lRecNam, $lSub, $lTxt);
            $lMai -> insert();
            CCor_Qry::exec($lSql);
          } elseif ($lExpireDate <= $lPresentDate) {
            $lSql = 'UPDATE al_usr set password_disable="Y" where id="' . $lUsrId .'"';
            CCor_Qry::exec($lSql);
          }
        }
      }
    }
    return true;
  }
}