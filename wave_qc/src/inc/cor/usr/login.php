<?php
class CInc_Cor_Usr_Login {

  protected static $mCookieOptions;
  
  public static function getCookieOptions() {
    if (!isset(self::$mCookieOptions)) {
      $lCookieSecure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');
      $lCookieHost   = $_SERVER['HTTP_HOST'];
      $lCookiePath   = dirname($_SERVER['REQUEST_URI']).'/';
    
      $lOpt['cookie_httponly'] = 1;
      $lOpt['cookie_secure']   = $lCookieSecure ? 1 : 0;
      $lOpt['cookie_domain']   = $lCookieHost;
      $lOpt['cookie_path']     = $lCookiePath;
      $lOpt['cookie_lifetime'] = 0;
      
      self::$mCookieOptions = $lOpt;
    }
    return self::$mCookieOptions;
  }
  
  public static function startSession($aUsingZend = false) {
    $lOpt = self::getCookieOptions();
    
    if ($aUsingZend) {
      include_once 'Zend/Session.php';
      Zend_Session::setOptions($lOpt);
      Zend_Session::start();
    } else {
      foreach ($lOpt as $lKey => $lVal) {
        ini_set('session.'.$lKey, $lVal);
      }
      session_start();
    }
  }
  
  public static function setCookie($aKey, $aValue) {
    $lOpt = self::getCookieOptions();
    setcookie($aKey, $aValue, 0, $lOpt['cookie_path'], $lOpt['cookie_domain'], $lOpt['cookie_secure'], true);
  }
  
  public function login($aUid, $aRow) {
    $lUid = intval($aUid);
    if (empty($lUid)) return FALSE;

    $lUsrMid = $aRow['mand'];

    // delete old browser session and expired sessions
    $lTim = time() - 24*60*60;
    $lDat = date('Ymdhis', $lTim);
    $lSql = 'DELETE FROM al_usr_login WHERE session_id='.esc(session_id()).' OR zeit<"'.$lDat.'"';
    $lQry = new CCor_Qry($lSql);

   
    // prevent session-id hijacking
    #session_destroy();
    self::startSession();
    self::setCookie('Mand', $lUsrMid);

    $lQry -> query('INSERT INTO al_usr_login SET session_id="'.session_id().'", user_id="'.$lUid.'";');
    $lQry -> query('INSERT INTO al_usr_useragent SET userid="'.$lUid.'", useragent="'.$_SERVER['HTTP_USER_AGENT'].'";');
    $lQry -> query('UPDATE al_usr SET lastlogin=NOW() WHERE id="'.$lUid.'";');

    $lArr = array();
    $lSys = CCor_Sys::getInstance();
    foreach($aRow as $lKey => $lVal) {
      $lSys['usr.'.$lKey] = $lVal;
      $lArr[$lKey] = $lVal;
    }
    $lArr['fullname'] = $aRow['firstname'].' '.$aRow['lastname'];
    $lArr['first_lastname'] = $aRow['firstname'].' '.$aRow['lastname'];
    $lSys['usr.val'] = $lArr;

    
    /* Const 'MID_PREF' wird bei login inc/log/cnt definiert.
     * Bei mehreren Mandanten muessen die Prefs auch mandantabhaengig
    * gespeichert werden, was bis jetzt nicht der Fall war.
    * Damit nur mandantabhaengige Prefs geladen werden koennen
    * muss d. MandantId schon bei der Anmeldung festgestellt werden.
    * Fuer die Benutzer mit Mand= 0 wird es nicht definiert.
    */
    $lQry -> query('SELECT val FROM al_usr_pref WHERE uid='.$lUid.' AND code="sys.mid" ');
    if ( $lRow = $lQry -> getAssoc()){
      define('MID_PREF',$lRow['val']);
    }

    $lUsr = CCor_Usr::getInstance();
    $lUsr -> reloadId();
    $lUsr -> loadPrefsFromDb();

    //Wenn kein pref vorhanden, wird sys.mid gesetzt.
    $lPref = $lUsr -> getPref('sys.mid');
    if (empty($lPref)) {
      $lUsr -> setPref('sys.mid', $aRow['mand']);
      $lPref = $lUsr -> getPref('sys.mid');
    }

    $lQry = new CCor_Qry('SELECT id, code FROM al_sys_mand');
    foreach($lQry as $lRow) {
      $lMand[$lRow -> id] = $lRow -> code;
    }
    $lUsr -> setPref('sys.mand',$lMand[$lPref]);
    $lUsr -> loadPrefsFromDb();
    return true;
  }

  public function logout() {
    CCor_Qry::exec('DELETE FROM al_usr_login WHERE session_id='.esc(session_id()));
    $lSys = CCor_Sys::getInstance();
    $lMsg = $lSys['msg'];
    unset($_SESSION);
    session_unset();
    session_destroy();

    session_start();
    $lSys -> reloadSession();
    $lSys['msg'] = $lMsg;
  }
}