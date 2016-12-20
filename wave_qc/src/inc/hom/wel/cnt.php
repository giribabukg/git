<?php
/**
 * Home: Welcome - Controller
 *
 *  Description
 *
 * @package    HOM
 * @subpackage    Welcome
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 12421 $
 * @date $Date: 2016-02-09 16:49:18 +0800 (Tue, 09 Feb 2016) $
 * @author $Author: ahajali $
 */
class CInc_Hom_Wel_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('hom-wel.menu');
    $this -> mMmKey = 'hom-wel';
    $this -> mEmailTemplate = CCor_Cfg::get('tpl.email');
  }

  protected function actStd() {
    $lAplReqUid =  $this -> mReq -> getVal('aplfor');
    $lTpl = new CCor_Tpl();
    $lTpl -> openProjectFile('welcome.htm');
    $lTpl -> setPat('pg.usr.welcome',  htm(lan('usr.welcome')));

    $lUsr = CCor_Usr::getInstance();
    $lUsrId = $lUsr -> getId();
    if (is_null($lAplReqUid)) {
      $lAplReqUid = NULL;
    }
    else {
      $lUsr -> setPref('home.aplfor.filter', $lAplReqUid);
    }

    $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE id='.$lUsrId);
    if ($lRow = $lQry -> getAssoc()) {
      foreach ($lRow as $lKey => $lVal) {
        $lTpl -> setPat('usr.'.$lKey, htm($lVal));
      }
    }

    $lBox = new CHom_Wel_Usrbox($lUsrId);
    $lTpl -> setPat('box.user', $lBox -> getContent());

    if ($lUsr -> canRead('hom-wel.backup')) {
      $lBackup = new CHom_Wel_Backupbox($lUsrId);
      $lTpl -> setPat('box.backup', $lBackup -> getContent());
    } else {
      $lTpl -> setPat('box.backup', null);
    }
    
    if ($lUsr -> canRead('notification-center')) {
      $lInBox = new CHom_Wel_Myinbox_List();
      $lTpl -> setPat('box.myinbox', $lInBox -> getContent());
    }
    else $lTpl -> setPat('box.myinbox', null);

    $lSer = new CHom_Wel_Searchbox();
    $lTpl -> setPat('box.search', $lSer -> getContent());

    $lMyTasks = new CHom_Wel_MyTasks($lAplReqUid);
    $lTpl -> setPat('box.mytasks', $lMyTasks -> getContent());

    $lMen = new CHom_Menu('wel');
    $this -> render(CHtm_Wrap::wrap($lMen, $lTpl));
  }

  protected function actBack() {
    $lSys = CCor_Sys::getInstance();
    $lHis = $lSys['his'];
    if (!empty($lHis)) {
      $lCnt = count($lHis);
      if ($lCnt > 1) {
        $lUrl = array_pop($lHis);
        $lUrl = array_pop($lHis);
        $lSys['his'] = $lHis;
        $this -> redirect($lUrl);
      } else {
        $lSys['his'] = array();
        $this -> redirect('index.php?act=hom-wel');
      }
    }
  }

  protected function actSerpre() {
    $lId = $this -> getInt('id');
    $lUsr = CCor_Usr::getInstance();

    $lUsr -> setPref('job.ser_id', $lId);
    if (!empty($lId)) {
      $lQry = new CCor_Qry('SELECT * FROM al_usr_search WHERE id='.$lId.' AND mand='.MID);
      if ($lRow = $lQry -> getDat()) {
        $lUsr -> setPref('job.ser_ser', unserialize($lRow['ser']));
      }
    }

    if ($lId == 0) {
      $lUsrId = $lUsr -> getAuthId();

      $lSql = 'DELETE FROM al_usr_pref WHERE ';
      $lSql.= 'uid='.intval($lUsrId).' AND ';
      $lSql.= 'mand='.intval(MID).' AND ';
      $lSql.= 'code IN ("'.addslashes('job.ser_id').'", "'.addslashes('job.ser_ser').'");';
      CCor_Qry::exec($lSql);
    }

    $lUsr -> setPref('job-art.page', 0);
    $lUsr -> setPref('job-rep.page', 0);
    $lUsr -> setPref('job-sec.page', 0);
    $lUsr -> setPref('job-mis.page', 0);
    $lUsr -> setPref('job-adm.page', 0);
    $lUsr -> setPref('job-com.page', 0);
    $lUsr -> setPref('job-tra.page', 0);

    $this -> redirect();
  }

  protected function actImOnholiday() {
    $lUsr = CCor_Usr::getInstance();
    $lUsrId = CCor_Usr::getAuthId();
    $lImOnHoliday = $lUsr -> getPref('usr.onholiday');

    $lIfDbg = 0; // for debugging only

    $lSql = 'SELECT u.backup FROM al_usr u, al_usr b WHERE u.id='.$lUsrId.' AND u.backup=b.id AND b.del="N";';
    $lBackupUser = CCor_Qry::getInt($lSql);

    if(!empty($lBackupUser)){
    $lSql = 'SELECT b.uid from al_usr_mand a LEFT JOIN al_usr_mand b ON (a.mand = b.mand) WHERE a.uid='.$lUsrId.' AND b.uid='.$lBackupUser.';';
    $lBackupUser = CCor_Qry::getInt($lSql);
    } else {
      $lBackupUser = 0;
    }

    if (0 < $lBackupUser && 0 < $lUsrId) {
      if ($lImOnHoliday == 'Y') {
        $lUsr -> setPref('usr.onholiday', 'N');
        $lSql = 'UPDATE al_job_apl_states s, al_job_apl_loop l SET';
        $lSql.= ' s.user_id=s.uid, s.backupuser_id=0';
        $lSql.= ' WHERE s.loop_id=l.id AND s.uid='.$lUsrId;
        $lSql.= ' AND s.status=0 AND l.mand='.MID.' AND l.status="open";';
        $lResult = CCor_Qry::exec($lSql);

        $lSql = 'UPDATE al_job_apl_states s, al_job_apl_loop l SET';
        $lSql.= ' s.user_id=s.backupuser_id, s.backupuser_id=s.uid';
        $lSql.= ' WHERE s.loop_id=l.id AND s.backupuser_id='.$lUsrId;
        $lSql.= ' AND s.status=0 AND l.mand='.MID.' AND l.status="open";';
        $lResult = CCor_Qry::exec($lSql);

        $lIfDbg = 1;

        $lSql = 'REPLACE INTO al_usr_pref (`uid`, `mand`, `code`, `val`) VALUES ('.$lUsrId.', '.MID.', "usr.onholiday", "N");';
        $lResult = CCor_Qry::exec($lSql);

        $this -> sendInfoMail($this -> mEmailTemplate['i.back.fromholiday'], $lBackupUser);
      } else {
        $lUsr -> setPref('usr.onholiday', 'Y');
        $lSql = 'UPDATE al_job_apl_states s, al_job_apl_loop l SET';
        $lSql.= ' s.user_id='.$lBackupUser.', s.`backupuser_id`=s.user_id';
        $lSql.= ' WHERE s.loop_id=l.id AND s.user_id='.$lUsrId;
        $lSql.= ' AND s.status=0 AND l.mand='.MID.' AND l.status="open";';
        $lResult = CCor_Qry::exec($lSql);

        $lIfDbg = 2;

        $lSql = 'REPLACE INTO al_usr_pref (`uid`, `mand`, `code`, `val`) VALUES ('.$lUsrId.', '.MID.', "usr.onholiday", "Y");';
        $lResult = CCor_Qry::exec($lSql);

        $this -> sendInfoMail($this -> mEmailTemplate['i.go.onholiday'], $lBackupUser);
      }
    } else {
      if ($lImOnHoliday == 'Y') {
        $lUsr -> setPref('usr.onholiday', 'N');

        $lSql = 'REPLACE INTO al_usr_pref (`uid`, `mand`, `code`, `val`) VALUES ('.$lUsrId.', '.MID.', "usr.onholiday", "N");';
        $lResult = CCor_Qry::exec($lSql);

        $lIfDbg = 3;
      } else {
        $lUsr -> setPref('usr.onholiday', 'Y');

        $lSql = 'REPLACE INTO al_usr_pref (`uid`, `mand`, `code`, `val`) VALUES ('.$lUsrId.', '.MID.', "usr.onholiday", "N");';
        $lResult = CCor_Qry::exec($lSql);

        $lIfDbg = 4;
      }
    }

    $this -> dbg(MID.', '.$lIfDbg.', '.$lUsrId.' BACKUP_ImOnHoliday: '.$lImOnHoliday.', '.$lSql, mlInfo);

    $this -> redirect();
  }

  protected function actIsOnholiday() {
    $lUsr = CCor_Usr::getInstance();
    $lUsrId = CCor_Usr::getAuthId();
    $lImOnHoliday = $lUsr -> getPref('usr.onholiday');

    $lIfDbg = 0; // for debugging only

    $lSql = '';
    $lIsOnHoliday = 'undefined';

    if ($lImOnHoliday != 'Y' && 0 < $lUsrId) {
      $lSql = 'SELECT u.id FROM al_usr u, al_usr b WHERE u.backup='.$lUsrId.' AND u.backup=b.id AND u.del="N";';
      $lBackupUser = CCor_Qry::getInt($lSql);

      if(!empty($lBackupUser)){
      $lSql = 'SELECT b.uid from al_usr_mand a LEFT JOIN al_usr_mand b ON (a.mand = b.mand) WHERE a.uid='.$lUsrId.' AND b.uid='.$lBackupUser.';';
      $lImBackuping = CCor_Qry::getInt($lSql);
      } else {
        $lImBackuping = 0;
      }

      if (0 < $lImBackuping) {
        $lSql = 'SELECT val FROM al_usr_pref WHERE uid='.$lImBackuping.' AND mand='.MID.' AND code="usr.onholiday";';
        $lIsOnHoliday = CCor_Qry::getStr($lSql);

        if ($lIsOnHoliday == 'Y') {
          $lResult = CCor_Qry::exec('REPLACE INTO al_usr_pref (`uid`,`mand`,`code`,`val`) VALUES ('.$lImBackuping.','.MID.',"usr.onholiday","N")');
          $lSql = 'UPDATE al_job_apl_states s, al_job_apl_loop l SET';
          $lSql.= ' s.user_id=s.uid, s.backupuser_id=0';
          $lSql.= ' WHERE s.loop_id=l.id AND s.uid='.$lImBackuping;
          $lSql.= ' AND s.status=0 AND l.mand='.MID.' AND l.status="open";';
          $lResult = CCor_Qry::exec($lSql);

          $lSql = 'UPDATE al_job_apl_states s, al_job_apl_loop l SET';
          $lSql.= ' s.user_id=s.backupuser_id, s.backupuser_id=s.uid';
          $lSql.= ' WHERE s.loop_id=l.id AND s.backupuser_id='.$lImBackuping;
          $lSql.= ' AND s.status=0 AND l.mand='.MID.' AND l.status="open";';
          $lResult = CCor_Qry::exec($lSql);

          $lIfDbg = 1;

          $this -> sendInfoMail($this -> mEmailTemplate['i.will.not.backup.you'], $lImBackuping);
        } else { // Go to holiday
          $lResult = CCor_Qry::exec('REPLACE INTO al_usr_pref (`uid`,`mand`,`code`,`val`) VALUES ('.$lImBackuping.','.MID.',"usr.onholiday","Y")');
          $lSql = 'UPDATE al_job_apl_states s, al_job_apl_loop l SET';
          $lSql.= ' s.user_id='.$lUsrId.', s.`backupuser_id`=s.user_id';
          $lSql.= ' WHERE s.loop_id=l.id AND s.user_id='.$lImBackuping;
          $lSql.= ' AND s.status=0 AND l.mand='.MID.' AND l.status="open";';
          $lResult = CCor_Qry::exec($lSql);

          $lIfDbg = 2;

          $this -> sendInfoMail($this -> mEmailTemplate['you.go.onholiday'], $lImBackuping);
        }
      }
    }

    $this -> dbg(MID.', '.$lIfDbg.', '.$lUsrId.' BACKUP_IsOnHoliday:'.$lIsOnHoliday.', ImOnHoliday:'.$lImOnHoliday.', '.$lSql, mlInfo);

    $this -> redirect();
  }

  protected function actMyBackup() {
    $lUsr = CCor_Usr::getInstance();
    $lUsrId = CCor_Usr::getAuthId();
    $lImOnHoliday = $lUsr -> getPref('usr.onholiday');
    $lMyBackup = $this -> getVal('mybackupuser');

    $lIfDbg = 0;

    if ($lImOnHoliday != 'Y') {
      $lSql = 'UPDATE al_usr u, al_usr_mand m SET u.backup='.$lMyBackup.' WHERE m.mand IN(0,'.MID.') AND u.id='.$lUsrId.' AND u.id=m.uid;';
      CCor_Qry::exec($lSql);

      $lIfDbg = 1;
    }

    $this -> dbg(MID.', '.$lIfDbg.', '.$lUsrId.' BACKUP_MyBackup ImOnHoliday:'.$lImOnHoliday.' $lSql:'.$lSql, mlInfo);

    $this -> redirect();
  }

  protected function actImBackup() {
    $lUsrId = CCor_Usr::getAuthId();
    $lImBackup = $this -> getVal('imbackupuser');

    $lIfDbg = 0;

    $lSql = 'SELECT u.id FROM al_usr u, al_usr b WHERE u.backup='.$lUsrId.' AND u.backup=b.id AND b.del="N";';
    $lImBackuping = CCor_Qry::getStr($lSql);

    $lSql = 'SELECT b.uid from al_usr_mand a LEFT JOIN al_usr_mand b ON (a.mand = b.mand) WHERE a.uid='.$lUsrId.' AND b.uid='.$lImBackuping.';';
    $lImBackuping = CCor_Qry::getInt($lSql);

    $lSql1 = '';

    if (FALSE === $lImBackuping AND 0 < $lImBackup) { //Ich bin nirgens Backup -> Wechsel zu XY
      $lSql = 'SELECT val FROM al_usr_pref WHERE uid='.$lImBackup.' AND mand='.MID.' AND code="usr.onholiday";';
      $lIsOnHoliday = CCor_Qry::getStr($lSql);

      if ($lIsOnHoliday == 'Y') { // Ich vertrete eine Kollegin, die bereits in Urlaub ist
        $lSql = 'SELECT u.backup FROM al_usr u, al_usr b WHERE u.id='.$lImBackup.' AND u.backup=b.id AND u.del="N";';
        $lBackupOfBackup = CCor_Qry::getInt($lSql);

        if (0 < $lBackupOfBackup) {
          $lIfDbg = 1;
          $this -> dbg(MID.', '.$lIfDbg.', '.$lUsrId.' BACKUP_ImBackup '.$lSql.' & '.$lImBackup.' is already onholiday! Her Backup is '.$lBackupOfBackup, mlInfo);
          $this -> redirect();
        }
      }

      $lSql = 'UPDATE al_usr u, al_usr_mand m SET u.backup='.$lUsrId.' WHERE m.mand='.MID.' AND u.id='.$lImBackup.' AND u.id=m.uid;';
      CCor_Qry::exec($lSql);
      if ($lIsOnHoliday == 'Y') { // Ich vertrete eine Kollegin, die bereits in Urlaub ist und keine Vertretung hatte
        $lIfDbg = 2;
        $this -> dbg(MID.', '.$lIfDbg.', '.$lUsrId.' BACKUP_ImBackup '.$lSql.' & '.$lImBackup.' is already onholiday! => Update al_job_apl_states', mlInfo);

        $lSql = 'UPDATE al_job_apl_states s, al_job_apl_loop l SET';
        $lSql.= ' s.user_id='.$lUsrId.', s.`backupuser_id`=s.user_id';
        $lSql.= ' WHERE s.loop_id=l.id AND s.user_id='.$lImBackup;
        $lSql.= ' AND s.status=0 AND l.mand='.MID.' AND l.status="open";';
        $lResult = CCor_Qry::exec($lSql);

      }
    } elseif (0 < $lImBackuping) {
      $lSql = 'SELECT val FROM al_usr_pref WHERE uid='.$lImBackuping.' AND mand='.MID.' AND code="usr.onholiday";';
      $lIsOnHoliday = CCor_Qry::getStr($lSql);

      if ($lIsOnHoliday != 'Y') {
        // 1. Ich bin nirgens Backup
        $lSql1.= 'UPDATE al_usr SET backup=0 WHERE backup='.$lUsrId.';';
        CCor_Qry::exec($lSql1);//== Keine Vertretung ausgewaehlt

        $lIfDbg = 3;

        // 2. Ich bin neu Backup, wenn 0 < $lImBackup
        if (0 < $lImBackup) {
          $lSql = 'UPDATE al_usr SET backup='.$lUsrId.' WHERE id='.$lImBackup.';';
          CCor_Qry::exec($lSql);

          $lIfDbg = 4;
        }
      }
    }

    $this -> dbg(MID.', '.$lIfDbg.', '.$lUsrId.' BACKUP_ImBackup '.$lSql1.' & '.$lSql, mlInfo);

    $this -> redirect();
  }

  protected function actOrd() {
    $this -> mReq -> expect('fie');
    $this -> mReq -> expect('typ');
    $lFie = $this -> mReq -> getVal('fie');
    $lTyp = $this -> mReq -> getVal('typ');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.'.$lTyp.'.ord', $lFie);
    $this -> redirect();
  }

  protected function sendInfoMail($aTplId, $aReceiverId) {
    $lTplId = $aTplId;

    $lUsr = CCor_Usr::getInstance();

    if (!empty($lTplId)) {
      $lTpl = new CApp_Tpl();
      if (is_int($lTplId)) {
        $lTpl -> loadTemplate($lTplId);
      } else {
        $lTpl -> loadTemplate(0, $lTplId, LAN);
      }

      $lTpl -> addUserPat($aReceiverId, 'to');
      $lTpl -> addUserPat($lUsr -> getId(), 'from');

      // START: for Seal only
      if (method_exists('CApp_Sender', 'getSealUrl')) {
        $lFac = new CJob_Fac('art', 0);
        $lJob = $lFac -> getDat();

        $lSender = new CApp_Sender('usr', array(), $lJob);
        $lSealHome = $lSender -> getSealUrl('act=hom-wel');
        $lTpl -> setPat('seal.home', $lSealHome);
      }
      // STOP: for Seal only

      $lTpl -> setPat('link', CCor_Cfg::get('base.url').'index.php?act=hom-wel');

      $lSubject = $lTpl -> getSubject();
      $lBody = $lTpl -> getBody();

      $lReceiver = new CCor_Anyusr($aReceiverId);
      $lItm = new CApi_Mail_Item($lUsr -> getVal('email'), $lUsr -> getVal('firstname').' '.$lUsr -> getVal('lastname'), $lReceiver -> getVal('email'), $lReceiver -> getVal('firstname').' '.$lReceiver -> getVal('lastname'), $lSubject, $lBody);
      $lItm -> setSenderID($lUsr -> getId());
      $lItm -> setReciverId($lReceiver -> getId());
      $lItm -> setMailType(mailSys);
      $lItm -> insert();
    }
  }
}