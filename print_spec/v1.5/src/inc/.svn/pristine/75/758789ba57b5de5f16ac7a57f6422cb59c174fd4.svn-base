<?php
class CInc_App_Rem extends CCor_Obj {

  // private $mIdList = array();
  
  public function __construct($aSrc, $aJobId) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = intval($aJobId);
  }
  
  public function add($aUid, $aTyp, $aSubject, $aLink = '', $aMsg = '') {
    if (empty($aUid)) {
      return;
    }
    if (empty($aLink)) {
      $lLnk = $this -> mSrc.'.edt&jobid='.$this -> mJobId;
    } else {
      $lLnk = $aLink;
    }
    $liSql = 'INSERT INTO al_usr_rem SET ';
    $liSql.= 'user_id='.intval($aUid).',';
    $liSql.= 'typ='.intval($aTyp).',';
    $liSql.= 'datum=NOW(),';
    $liSql.= 'subject="'.addslashes($aSubject).'",';
    $liSql.= 'comment="'.addslashes($aMsg).'",';
    $liSql.= 'ref_src="'.addslashes($this -> mSrc).'",';
    $liSql.= 'ref_id="'.addslashes($this -> mJobId).'",';
    $liSql.= 'ref_link="'.addslashes($lLnk).'"';
    CCor_Qry::exec($liSql);
    
    $lSql = 'SELECT val FROM al_usr_pref WHERE uid='.intval($aUid).' AND code="sys.mail.reminder"';
    $lVal = CCor_Qry::getStr($lSql);
    if (FALSE === $lVal) {
      $lSql = 'SELECT val FROM al_sys_pref WHERE code="sys.mail.reminder"';
      $lVal = CCor_Qry::getStr($lSql);
    } 
    if ($lVal == smEveryTime) {     
      $lUsr = CCor_Usr::getInstance();
      $lFMail = $lUsr -> getVal('email');
      $lFName = $lUsr -> getVal('firstname').' '.$lUsr -> getVal('lastname');
      
      $lSql = 'SELECT firstname,lastname,email FROM al_usr WHERE id='.intval($aUid);
      $lQry = new CCor_Qry($lSql);
      if ($lRow = $lQry -> getAssoc()) {
        $lToName = $lRow['firstname'].' '.$lRow['lastname'];
        $lToMail = $lRow['email'];
        $lMsg = 'Dear user,'.LF.LF.'you have a new reminder in your reminder list:'.LF.LF.$aSubject.LF.LF.$aMsg.LF.LF;
        $lCfg = CCor_Cfg::getInstance();
        $lUrl = $lCfg -> getVal('base.url').'index.php?act=';
        $lMsg.= 'Link: '.$lUrl.$lLnk;
        $lMai = new CApi_Mail_Item($lFMail, $lFName,  $lToMail, $lToName, 'Reminder: '.$aSubject, $lMsg);
        #$lMai -> send();
        $lMai -> insert();
      } 
    }
  }
  
  function deleteWhere($aField, $aVal) {
    $lSql = 'DELETE FROM al_usr_rem WHERE ';
    $lSql.= 'ref_src="'.addslashes($this -> mSrc).'" AND ';
    $lSql.= 'ref_id="'.addslashes($this -> mJobId).'" AND ';
    $lSql.= $aField.'="'.addslashes($aVal).'"';
    CCor_Qry::exec($lSql);
  }

  public function dailyMail($aUid) {
    // $this -> mIdList = array();
    if (empty($aUid)) {
      return;
    }
    $lSql = 'SELECT val FROM al_usr_pref WHERE uid='.intval($aUid).' AND code="sys.mail.reminder"';
    $lVal = CCor_Qry::getStr($lSql);
    if ($lVal != smOncePerDay) {
      return;
    }

    $lCfg = CCor_Cfg::getInstance();        
    $lUrl = $lCfg -> getVal('base.url').'index.php?act=';  
    
    $lMsgHead = 'your daily mail of your reminder list:';
    $lMsgBody = '';
    
    $lQry = new CCor_Qry('SELECT * FROM al_usr_rem WHERE user_id='.$aUid);
    foreach ($lQry as $lRow) {
      $aTyp = $lRow['typ'];
      $aDatum = $lRow['datum'];
      $aSubject = $lRow['subject'];
      $aMsg = $lRow['comment'];
      $aSrc = $lRow['ref_src'];
      $lLnk = $lRow['ref_link'];    
      $lMsg = '';
      if ($aSubject != '') {
        $lMsg.= LF.$aSubject;      
      }
      if ($aMsg != '') {
        $lMsg.= LF.$aMsg;      
      }
      if ($lLnk != '') {
        $lMsg.= LF.'Link: '.$lUrl.$lLnk;
      } 
      if ($lMsg != '') {
        // $this -> mIdList[] = $lRow['id'];           
        $lMsgBody.= LF.$lMsg;
      };
    };
    if ($lMsgBody != '') {
      return $lMsgHead.$lMsgBody;
    } else {
      return;
    }
  }

}