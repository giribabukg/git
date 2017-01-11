<?php
/**
 * Api: Mail - Resend
 *
 * SINGLETON
 *
 * @package    API
 * @subpackage Mail
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6388 $
 * @date $Date: 2014-11-07 13:14:00 +0100 (Fri, 07 Nov 2014) $
 * @author $Author: gemmans $
 */
class CApi_Mail_Resend extends CCor_Obj {

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

  public function send($aMid = null) {
    $ldis = FALSE;
    //$lQry = new CCor_Qry('SELECT * FROM al_sys_mails WHERE mand='.MID.' AND mail_state in ('.mlError.','.mlWarn.')');
    $lSql = 'SELECT * FROM al_sys_mails WHERE mail_state in ('.mlError.','.mlWarn.')';
    if (!empty($aMid)) {
      $lSql.= ' AND mand='.intval($aMid);
    }
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lTo = ($this -> mGlobalTo == '') ? $lRow['to_mail'] : $this -> mGlobalTo;
      $this -> dbg('Resend Mail: '.$lRow['id']. ' to:' . $lTo);

      $lMail = new CApi_Mail_Item($lRow['from_mail'],
                                  $lRow['from_name'],
                                  $lTo,
                                  $lRow['to_name'],
                                  $lRow['mail_subject'],
                                  '');

      $lMail -> setHeader($lRow['mail_header'],'From,To,Date,Subject');
      $lMail -> resend($lRow['mail_body']);

      if (($this -> mGlobalTo == '') or ($this -> mForceSave)) {
        $this -> dbg('Replace Mail: '.$lRow['id']);
        $lMail -> replace($lRow['id']);
      };

      if ($lMail -> getErrNo() != aeOkay) {
        $lSmt = CApi_Mail_Smtp::getInstance();
        $lSmt -> disconnect();
        $ldis = FALSE;
      } else {
        $ldis = TRUE;
      };
    };
    if ($ldis) {
      $lSmt = CApi_Mail_Smtp::getInstance();
      $lSmt -> disconnect();
    };
  }
}