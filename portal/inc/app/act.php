<?php
class CInc_App_Act extends CCor_Dat {

  private $mIdList = array();  
  
  public function __construct($aSrc, $aJobId) {
    $this['ref_src'] = $aSrc;
    $this['ref_id']  = intval($aJobId);
    $this['ref_link'] = $aSrc.'.edt&jobid='.intval($aJobId);
  }
  
  public function setValues($aAlias, $aUid, $aTyp, $aSubject, $aDeadLine = '', $aState = tfDefault) {
    $this['alias']    = $aAlias;
    $this['user_id']  = intval($aUid);
    $this['typ']      = intval($aTyp);
    $this['subject']  = $aSubject;
    $this['deadline'] = $aDeadLine;
    $this['status']   = $aState;
  }
  
  public function add($aAlias, $aUid, $aTyp, $aSubject, $aDeadLine = '', $aState = tfDefault) {
    $this -> setValues($aAlias, $aUid, $aTyp, $aSubject, $aDeadLine, $aState);
    $this -> insert();
  }

  public function insert() {
    $lSql = 'SELECT COUNT(*) FROM al_usr_act WHERE 1 ';
    $lSql.= 'AND user_id='.intval($this['user_id']).' ';
    $lSql.= 'AND typ='.intval($this['typ']).' ';
    $lSql.= 'AND ref_src="'.$this['ref_src'].'" ';
    $lSql.= 'AND ref_id='.intval($this['ref_id']).' ';
    $lSql.= 'AND status<'.tfDone;
    $lCnt = CCor_Qry::getInt($lSql);
    if ($lCnt > 0) return;
    
    $lDdl = $this['deadline'];
    if (empty($lDdl)) {
      $lDat = new CCor_Date();
      $lDat = $lDat -> getDaysDif(2);
      $this['deadline'] = $lDat -> getSql();
    }
    $this['created_on'] = date('Y-m-d H:i:s');
    $lSql = 'INSERT INTO al_usr_act SET ';
    foreach ($this as $lKey => $lVal) {
      if (!empty($lVal)) {
        $lSql.= $lKey.'="'.addslashes($lVal).'",';
      }
    }
    $lSql = strip($lSql, 1);
    $lQry = new CCor_Qry($lSql);
    $this -> afterInsert();
  }
  
  protected function afterInsert() {
    // pruefen ob email versendet werden muss
    // echo 'checking mail todo'.BR;
    $lUid = intval($this -> mVal['user_id']);
    if (empty($lUid)) {
      return;
    }
    $lSql = 'SELECT val FROM al_usr_pref WHERE uid='.$lUid.' AND code="sys.mail.todo"';
    $lVal = CCor_Qry::getStr($lSql);
    if (FALSE === $lVal) {
      $lSql = 'SELECT val FROM al_sys_pref WHERE code="sys.mail.todo"';
      $lVal = CCor_Qry::getStr($lSql);
    } 
    if (smEveryTime == $lVal) {
      // mail versenden
      $lUsr = CCor_Usr::getInstance();
      $lFMail = $lUsr -> getVal('email');
      $lFName = $lUsr -> getVal('firstname').' '.$lUsr -> getVal('lastname');
      
      $lSql = 'SELECT firstname,lastname,email FROM al_usr WHERE id='.intval($lUid);
      $lQry = new CCor_Qry($lSql);
      if ($lRow = $lQry -> getAssoc()) {
        $lToName = $lRow['firstname'].' '.$lRow['lastname'];
        $lToMail = $lRow['email'];
        $lMsg = 'Dear user,'.LF.LF.'you have a new todo in your todo list:'.LF.LF;
        $lMsg.= $this['subject'].LF.LF;        
        $lCfg = CCor_Cfg::getInstance();
        $lUrl = $lCfg -> getVal('base.url').'index.php?act=';
        $lMsg.= 'Link: '.$lUrl.$this['ref_link'];
        #echo $lMsg;
        $lMai = new CApi_Mail_Item($lFMail, $lFName,  $lToMail, $lToName, 'Todo: '.$this['subject'], $lMsg);
        #$lMai -> send();
        $lMai -> insert();
      }
    }    
  }
  
  public function dailyMail($aUid) {
    $this -> mIdList = array();
    if (empty($aUid)) {
      return;
    }
    $lSql = 'SELECT val FROM al_usr_pref WHERE uid='.intval($aUid).' AND code="sys.mail.todo"';
    $lVal = CCor_Qry::getStr($lSql);
    
    if ($lVal != smOncePerDay) {
      return;
    }

    $lCfg = CCor_Cfg::getInstance();        
    $lUrl = $lCfg -> getVal('base.url').'index.php?act=';  
    
    $lMsgHead = 'your daily mail of your todo list:';
    $lMsgBody = '';
    
    $lQry = new CCor_Qry('SELECT * FROM al_usr_act WHERE finished_on="0000-00-00 00:00:00" and user_id='.$aUid);
    foreach ($lQry as $lRow) {
      $aTyp = $lRow['typ'];
      $aSubject = $lRow['subject'];
      $aSrc = $lRow['ref_src'];
      $lLnk = $lRow['ref_link'];    
      $lMsg = '';
      if ($aSubject != '') {
        $lMsg.= LF.$aSubject;      
      }
      if ($lLnk != '') {
        $lMsg.= LF.'Link: '.$lUrl.$lLnk;
      } 
      if ($lMsg != '') {
        $this -> mIdList[] = $lRow['id'];           
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