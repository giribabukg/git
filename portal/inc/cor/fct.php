<?php
/**
 * Core:  Funktion: dispatch
 *
 * SINGLETON
 *
 * @package    COR
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 15015 $
 * @date $Date: 2016-07-12 07:48:18 +0200 (Tue, 12 Jul 2016) $
 * @author $Author: gemmans $
 */
class CCor_Fct extends CCor_Obj {

  private static $mInstance = NULL;

  private function __construct() {}

  private function __clone() {}

  public static function getInstance() {
    if (null === self::$mInstance) {
      self::$mInstance = new self();
    }
    return self::$mInstance;
  }

  /**
   *
   * @param CCor_Req $aReq
   */
  public function predispatch($aReq) {
    $this->predispatchSso($aReq);
    $this->predispatchMand($aReq);
  }

  protected function predispatchDeepDown($aReq) {
    if (!defined('DOWN_TOKENNAME')) return;
    $lToken = $aReq->getVal(DOWN_TOKENNAME);
    if (empty($lToken)) return;
    $lUtl = new CInc_Utl_Fil_Cnt($aReq, $aMod);
    $lTokenDetails = $lUtl -> checkToken($lToken);
    if ($lTokenDetails) {
    	foreach ($lTokenDetails as $lKey => $lVal) {
    		$aReq -> $lKey = $lVal;
    		if ($lKey == 'mand') $lMandId = $lVal;
    	}
    	if (!empty($lMandId)) define(MID, $lMandId);
    	include_once 'mand/mand_'.MID.'/inc/cor/cfg.php';
#    	return $lUtl->actDown();
    	return $lUtl->actDownloadTokenFile();
    }
    else $this -> msg('Download link is already Expired, please contact your contact person.', mtUser, mlError);
  }

  protected function predispatchSso($aReq) {
    if (!defined('SSO_TOKENNAME')) return;
    $lToken = $aReq->getVal(SSO_TOKENNAME);
    if (empty($lToken)) return;
    if ($this->trySso($lToken)) {
      $lAct = $aReq->act;
      if (empty($lAct)) {
        $aReq->act = 'hom-wel';
      }
    }
  }

  protected function predispatchMand($aReq) {
    define('FCT_MAND', '_mid');
    $lMid = $aReq->getVal(FCT_MAND);
    if (!empty($lMid)) {
      $lUid = CCor_Usr::getAuthId();
      if (empty($lUid)) return;
      // cannot use ccor_res here because LAN is not yet defined
      $lArr = array();
      $lQry = new CCor_Qry('SELECT id,code FROM al_sys_mand');
      foreach ($lQry as $lRow) {
        $lArr[ $lRow['id'] ] = $lRow['code'];
      }
      if (!isset($lArr[$lMid])) return;
      $lPriv = new CCor_Usr_Mand($lUid);
      if ($lPriv->canAccess($lMid)) {
        $lUsr = CCor_Usr::getInstance();
        $lUsr -> setPref('sys.mid',$lMid);
        $lUsr -> setPref('sys.mand',  $lArr[$lMid]);
        $lUsr -> loadPrefsFromDb();
      } else {
        $this->msg('Invalid mandator', mtUser, mlError);
      }
    }
  }

  public function trySso($aToken) {
    $lCli = new CCust_Api_Henkel_Sso_Client(); # Live
    #$lCli = new CCust_Api_Henkel_Sso_Stub();  # Test
    $lRes = $lCli -> query($aToken);
    if (false === $lRes) {
      $lCli = new CCust_Api_Henkel_Sso_Client();
      $lRes = $lCli -> query($aToken);
    }
    if (!$lRes) {
      return false;
    }

    $lUsername = strtolower($lRes -> getVal('user'));

    $lCheckUserName = true;
    if ($lCheckUserName) {
      $lSql = 'SELECT i.uid FROM al_usr_info i,al_usr u ';
      $lSql.= 'WHERE i.iid="seal_sso_username" ';
      $lSql.= 'AND i.val LIKE ('.esc($lUsername).') ';
      $lSql.= 'AND i.uid=u.id AND u.del="N"';
      $lUid = CCor_Qry::getInt($lSql);
      if (!empty($lUid)) $this->dbg('Found '.$lUid.' by username '.$lUsername);
    }
    if (empty($lUid)) {
      $lMail = strtolower($lRes -> getVal('email'));
      if (empty($lMail)) return false;

      $lSql = 'SELECT i.uid FROM al_usr_info i,al_usr u ';
      $lSql.= 'WHERE i.iid="seal_sso_email" ';
      $lSql.= 'AND i.val LIKE ('.esc($lMail).')';
      $lSql.= 'AND i.uid=u.id AND u.del="N"';
      $lUid = CCor_Qry::getInt($lSql);
      if (!empty($lUid)) $this->dbg('Found '.$lUid.' by exact email '.$lMail);

      if (empty($lUid)) {
        $lRelaxedEmailValidation = true;
        if ($lRelaxedEmailValidation) {
          $lFirstPart = strtok($lMail, '@');
          $lSql = 'SELECT i.uid FROM al_usr_info i,al_usr u ';
          $lSql.= 'WHERE i.iid="seal_sso_email" ';
          $lSql.= 'AND i.val LIKE ("'.mysql_real_escape_string($lFirstPart).'@%")';
          $lSql.= 'AND i.uid=u.id AND u.del="N"';
          $lUid = CCor_Qry::getInt($lSql);
          if (!empty($lUid)) $this->dbg('Found '.$lUid.' by first part of email '.$lFirstPart);
          if (!empty($lUid)) {
            $lSql = 'UPDATE al_usr SET email='.esc($lMail).' WHERE id='.$lUid;
            CCor_Qry::exec($lSql);
            $lSql = 'REPLACE INTO al_usr_info SET uid='.esc($lUid).',';
            $lSql.= 'iid="seal_sso_email",val='.esc($lMail);
            CCor_Qry::exec($lSql);
          }
        }
      }

      if (empty($lUid)) {
        $lTool = new CCust_Api_Henkel_Sso_Createuser();
        $lUid = $lTool->createUser($lRes);
        if (!$lUid) return false;
      } else {
        //we've identified the user but seal_sso_username is not set
        $lSql = 'REPLACE INTO al_usr_info SET uid='.esc($lUid).',';
        $lSql.= 'iid="seal_sso_username",val='.esc($lUsername);
        CCor_Qry::exec($lSql);
      }
    }

    $lSql = 'SELECT * FROM al_usr WHERE id='.$lUid;
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry->getDat();

    if ($lRow === false) return false;
    $lUsrMail = strtolower($lRow['email']);

    if (!empty($lMail) && ($lUsrMail != $lMail) ) {
      $lSql = 'UPDATE al_usr SET email='.esc($lMail).' WHERE id='.$lUid;
      CCor_Qry::exec($lSql);
      $lSql = 'REPLACE INTO al_usr_info SET uid='.esc($lUid).',';
      $lSql.= 'iid="seal_sso_email",val='.esc($lMail);
    }
    $lLoginManager = new CCor_Usr_Login();
    return $lLoginManager->login($lUid, $lRow);
  }

  protected function isAuthRequired($aMod) {
    $lArr = array('log', 'webservice-dalim');
    return !in_array($aMod, $lArr);
  }

  public function dispatch() {
    $lReq = new CCor_Req();
    $lReq -> loadRequest();


    define('CUST_PATH_HTM', CUST_PATH.'htm'.DS);
    define('CUST_PATH_INC', CUST_PATH.'inc');
    define('CUST_PATH_IMG', CUST_PATH.'img'.DS);
    CCor_Loader::addDir(CUST_PATH_INC);

    $this->predispatch($lReq);

    $lModAct = $lReq -> act;
    if (empty($lModAct)) {
      $lModAct = 'log.in';
    }
    $this -> dbg('Action: '.$lModAct);

    $lArr = explode('.', $lModAct, 2);
    if (count($lArr) == 1) {
      $lMod = $lModAct;
      $lCls = strtr($lMod, '-', '_');
      $lAct = 'std';
    } else {
      $lAct = array_pop($lArr);
      $lMod = array_pop($lArr);
      $lCls = strtr($lMod, '-', '_');
    }

    if ($this->isAuthRequired($lMod)) {
      $lUsr = CCor_Usr::getInstance();

      $lLan = $lUsr -> getPref('sys.lang', LANGUAGE);
      if ($lLan == ''){
        $lLan = LANGUAGE;
      }
      define('LAN', $lLan);

      $lMand = $lUsr -> getPref('sys.mand', CCor_Cfg::get('mand.key', 'mandant'));
      $lMandArr = CCor_Res::extract('code', 'id', 'mand');
      if(isset($lMandArr[$lMand])) $lSysMid = $lMandArr[$lMand];
      else $lSysMid = CCor_Cfg::get('mand.mid', 0);
      $lMid  = $lUsr -> getPref('sys.mid',  $lSysMid);
      define('MID',  $lMid);
      define('MAND', $lMand);

      $lUid = $lUsr -> getId();
      if (empty($lUid)) {
        $lUrl = 'index.php?act=log.in';
        $lReq = $_SERVER["QUERY_STRING"];
        if (!empty($lReq)) {
          $lUrl.= '&nact='.urlencode($lReq);
        }
        CCor_Msg::add(lan('log.ses'), mtUser, mlError);
        header('Location: '.$lUrl);
        exit;
      }

      // Pfade zu kunden- & mandanten-spezifischen Dateien
      define('MAND_PATH', 	 'mand'.DS.'mand_'.MID.DS);
      define('MAND_PATH_HTM', MAND_PATH.'htm'.DS);
      define('MAND_PATH_INC', MAND_PATH.'inc');
      define('MAND_PATH_IMG', MAND_PATH.'img'.DS);

      CCor_Loader::injectDir(MAND_PATH_INC);

      if (file_exists(MAND_PATH_INC.'/cor/cfg.php')) {
        ob_start();
        include_once MAND_PATH_INC.'/cor/cfg.php';
        ob_end_clean();
      }
      $lEnviromentMode = CCor_Cfg::get('environment');
      $lMandator = ($lEnviromentMode) ? $lEnviromentMode.'_'.MANDATOR : MANDATOR;
      define('MANDATOR_ENVIRONMENT',$lMandator);
    }

    define('THEME', CCor_Cfg::get('theme.choice'));
    define('THEME_PATH_HTM', 'htm'.DS.THEME.DS);
    define('THEME_PATH_IMG', 'img'.DS.THEME.DS);

    // Pfade zu kunden- & mandanten-spezifischen Dateien
    CCor_Loader::addDir(CUST_PATH_INC);

    $lMandCls = 'C'.$lCls.'_Cnt';
    $lCustCls = 'CCust_'.$lCls.'_Cnt';
    $lClass  = 'C'.$lCls.'_Cnt';

    $lGetClass = CCor_Loader::loadClass($lClass, $lCustCls, $lMandCls);

    if (FALSE !== $lGetClass) {
      $lCon = new $lGetClass($lReq, $lMod, $lAct);
    } else {
      $this -> msg('Unknown Module '.$lMod, mtUser, mlFatal);
      $lCon = new CHom_Wel_Cnt($lReq, $lMod, $lAct);
    }
    $this->predispatchDeepDown($lReq);
    if (CCor_Cfg::get('composer.available', false)) {
      include_once 'vendor/autoload.php';
    }
    $lCon -> dispatch();
    exit;
  }

}
