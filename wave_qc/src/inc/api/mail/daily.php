<?php
/**
 * Api: Mail - Daily
 *
 * SINGLETON
 *
 * @package    API
 * @subpackage Mail
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 16:50:56 +0800 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CApi_Mail_Daily extends CCor_Obj {
  
  protected $mMtype = mtApi;
  private static $mInstance = NULL;
  
  protected $mGlobalTo = '';
  protected $mForceSave = FALSE;
  
  public function __construct($aGlobalTo = '', $aForceSave = FALSE) {
    $this -> mGlobalTo = $aGlobalTo;
    $this -> mForceSave = $aForceSave;
  }

  public function getInstance($aGlobalTo = '', $aForceSave = FALSE){
    if (NULL === self::$mInstance) {
      self::$mInstance = new self();
    }
    self::$mInstance -> mGlobalTo = $aGlobalTo;
    self::$mInstance -> mForceSave = $aForceSave;
    return self::$mInstance;
  }

  private final function __clone() {}
  
  public function generate($aSendUid='') {
    if (empty($aSendUid)) {
      $this -> msg('CApi_Mail_Daily -> generate: Empty Sender ID', mtUser, mlError);
      return;
    }
    $this -> dbg('[CApi_Mail_Daily] SendUid=' . $aSendUid);
    // Sender address
    $lSql = 'SELECT firstname,lastname,email FROM al_usr WHERE id='.intval($aSendUid);
    $lQry = new CCor_Qry($lSql);
    $lFName = '';
    $lFMail = '';
    if ($lRow = $lQry -> getAssoc()) {
      $lFName = $lRow['firstname'].' '.$lRow['lastname'];
      $lFMail = $lRow['email'];
    }
    if ($lFName == '' or $lFMail == '') {
      return;
    }
                         
    $lSql = 'SELECT distinct(up.uid) FROM al_usr_pref up, al_usr_rem ur, al_usr_act ua WHERE '.
            ' (ur.user_id=up.uid and up.val='.smOncePerDay.' and up.code="sys.mail.reminder") or'.
            ' (ua.user_id=up.uid and up.val='.smOncePerDay.' and up.code="sys.mail.todo")';
                         
    $lgQry = new CCor_Qry($lSql);
    foreach ($lgQry as $lgRow) {
      // Receiver address
      $lSql = 'SELECT firstname,lastname,email FROM al_usr WHERE id='.$lgRow['uid'];
      // echo $lSql.'<br>';
      $lQry = new CCor_Qry($lSql);
      if ($lRow = $lQry -> getAssoc()) {
        $lToName = $lRow['firstname'].' '.$lRow['lastname'];
        $lToMail = $lRow['email'];
        $lmails = array();
        $lkey = '';
        // Todo mail
        $lAct = new CApp_Act('', '');
        $lmail = $lAct -> dailyMail($lgRow['uid']);
        if ($lmail != '') {
          $lkey = 'Daily Todo';
          $lmails[$lkey] = $lmail;
        }
        // Reminder mail
        $lRem = new CApp_Rem('', '');
        $lmail = $lRem -> dailyMail($lgRow['uid']);
        $lsubject = '';
        if ($lmail != '') {
          $lkey = 'Daily Reminder';
          $lmails[$lkey] = $lmail;
        }
        // Mails zusammensetzen
        if (count($lmails) >= 1 and count($lmails) <= 2) {
          if (count($lmails) == 1) {
            $lsubject = $lkey;
            $lmail = $lmails[$lkey];
          } else if (count($lmails) == 2) {
            $lsubject = 'Daily Todo / Reminder';
            $lmail = $lmails['Daily Todo'].LF.LF.
                   '-----------------------------------------'.LF.LF.
                   $lmails['Daily Reminder'];
          }
          $lmail = 'Dear user,'.LF.LF.$lmail;
          $lMai = new CApi_Mail_Item($lFMail, $lFName,  $lToMail, $lToName, $lsubject, $lmail);
          $lMai -> insert();
        }
      }
    }
  }
}