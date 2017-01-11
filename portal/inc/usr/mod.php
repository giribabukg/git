<?php
class CInc_Usr_Mod extends CCor_Mod_Table {

  public function __construct($aCond = 0) {
    parent::__construct('al_usr');

    // 'id' and 'mand' are set automatically
    $this -> addField(fie('id'));

    // upcoming fields can be set by the user
    $lFields = array('anrede', 'firstname', 'lastname', 'company', 'location', 'department', 'pnr', 'email', 'email_from', 'email_replyto', 'phone', 'cnd', 'procnd', 'user', 'gadmin', 'elements_cond');
    foreach ($lFields as $lKey => $lValue) {
      $this -> addField(fie($lValue));
    }

    if (is_numeric($aCond)) {
      $this -> mDelAllowed = TRUE;
    } else {
      $this -> mDelAllowed = FALSE;
  }
  }

  public function getMapPost(ICor_Req $aReq) {
    parent::getPost($aReq);

    $lVal = $aReq -> getVal('val');
    $this -> mVal['backup'] = $lVal['per_role'];
  }

  protected function beforePost($aNew = FALSE) {
    if ($aNew) {
      $lPwd = CApp_Pwd::createPassword(8);
      $lPwd = CApp_Pwd::encryptPassword($lPwd);

      $this -> setVal('pass', $lPwd);
      $this -> setVal('created', date('Y-m-d'));
      $lMand = $this -> getVal('mand');
      if (empty($lMand)) {
        $this -> setVal('mand', MID);
      }
    }

    if (isset($this -> mReqOld['id'])) {
      $this -> save_cnd();
      if (false != CCor_Cfg::get('extcnd')) {
        $this -> save_procnd();
      }
      if ($this -> mReqVal['gfunc'] != $this -> mReqOld['gfunc']) {
        $this -> saveUsrGroup(gfunc);
      }
      if ($this -> mReqVal['gright'] != $this -> mReqOld['gright']) {
        $this -> saveUsrGroup(gright);
      }
    }
  }

  protected function saveUsrGroup ($aGroup) {
    if (isset($this -> mInsertId)) { // New User
      $lUid = $this -> mInsertId;
      $this -> parentGroups($lUid, $this -> mReqVal[$aGroup]);
    } 
    else {      // User exist already
      $lUid = $this -> mReqOld['id'];
      if (empty($this -> mReqVal[$aGroup]) && !empty($this -> mReqOld[$aGroup])) { // Update Function-Group (from a to 0)
          $lSql = 'DELETE FROM al_usr_mem WHERE uid = '.$lUid.' AND gid = '.$this -> mReqOld[$aGroup].' AND mand = '.MID;
          CCor_Qry::exec($lSql);
      }
      if (!empty($this -> mReqVal[$aGroup]) && !empty($this -> mReqOld[$aGroup])) { // Update Function-Group (from a to b)
          $lSql = 'UPDATE `al_usr_mem` SET `gid` = '.$this -> mReqVal[$aGroup];
          $lSql.= ' WHERE `gid` = '.$this -> mReqOld[$aGroup];
          $lSql.= ' AND uid = '.$lUid;
          $lSql.= ' AND mand = '.MID;
          CCor_Qry::exec($lSql);
      }
      if (empty($this -> mReqOld[$aGroup])) { // Insert Function-Group (from 0 to a)
          $this -> parentGroups($lUid, $this -> mReqVal[$aGroup]);
      }
    }  
    }
    
  protected function parentGroups($aUid, $aGid) {    
    while ($aGid != 0) {      
      $lSql = 'SELECT parent_id from al_gru where id = '.$aGid;
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lSql = 'INSERT INTO al_usr_mem SET uid = '.$aUid.', gid = '.$aGid.', mand = '.MID;
        CCor_Qry::exec($lSql);
        $aGid = $lRow['parent_id'];
        }        
    } 
  }

  protected function save_cnd() {
    $lSqlCond = '';
    if (false != CCor_Cfg::get('extcnd')) {
    if ($this -> mReqVal['cnd'] != $this -> mReqOld['cnd']) {
        if (isset($this -> mInsertId)) {
          $lUid = $this -> mInsertId;
        } else {
          $lUid = $this -> mReqOld['id'];
        }

        $lUpdateUserTable = true;
  
        $lNewCond = esc($this -> mReqVal['cnd']);
        $lOldCond = esc($this -> mReqOld['cnd']);
  
        $lNewCndId = 0;
        $lCondChanged = TRUE;
        $lQry = new CCor_Qry();
        if (empty($this -> mReqVal['cnd']) AND $this -> mDelAllowed) {
          $lSQL = 'DELETE FROM al_cnd WHERE usr_id='.$lUid.' AND mand='.MID;
          $lQry -> query($lSQL);
        } elseif (!empty($this -> mReqVal['cnd'])) {
  
          $lSQL = 'SELECT * FROM al_cnd WHERE usr_id='.$lUid.' AND mand='.MID;
          $lQry -> query($lSQL);
          if ($lRow = $lQry -> getAssoc()) {
            if (!empty($lRow['cond'])) {
              $lSqlCond.= ',`cond`=""';
            }
            $lSQL = 'UPDATE al_cnd SET cnd_id='.$lNewCond.$lSqlCond.' WHERE mand='.MID." AND usr_id=".$lUid;
            $lUpdateUserTable = false;
          } else {
            $lSQL = 'INSERT INTO al_cnd (mand,usr_id,cnd_id) VALUES (';
            $lSQL.= MID.", ".$lUid.", ".$lNewCond.")";
          }
  
          $lRet = $lQry -> query($lSQL);
          $lNewCndId = $lQry -> getInsertId();
        } else { // if (empty($this -> mReqVal['cnd']) AND !$this -> mDelAllowed)
          $lUpdateUserTable = false;
          $lCondChanged = FALSE;
        }
  
        if ($lCondChanged) {
          $this -> dbg('Condition changed from '.$lOldCond.' to '.$lNewCond.' for user '.$lUid.' (al_usr.cnd = '.$lNewCndId.((0 == $lNewCndId) ? ' - Deleted' : '').') /MID='.MID);
        }
  
        if ($lUpdateUserTable) {
          $lSQL = 'UPDATE al_usr SET cnd='.$lNewCndId;
          $lSQL.= ' WHERE id='.$lUid;
          $lSQL.= ' LIMIT 1';
          $lQry -> query($lSQL);
        }
  
        CCor_Cache::clearStatic('cor_res_cnd_'.MID);
        CCor_Cache::clearStatic('cor_res_cnd_'.MID.'_uid');
        CCor_Cache::clearStatic('cor_res_cnd_'.MID.'_gid');
      }
    } else {
      if ($this -> mReqVal['cnd'] != $this -> mReqOld['cnd']) {

      if (isset($this -> mInsertId)) {
        $lUid = $this -> mInsertId;
      } else {
        $lUid = $this -> mReqOld['id'];
      }

      $lUpdateUserTable = TRUE;

      $lNewCond = esc($this -> mReqVal['cnd']);
      $lOldCond = esc($this -> mReqOld['cnd']);

      if ($this -> mReqVal['cnd'] == ''){
        $lSql = 'Delete FROM al_cnd WHERE usr_id='.$lUid.' AND mand='.MID;
        $lQry = new CCor_Qry($lSql);
        $lNewCndId = 0;
      } else {
        $lSql = 'SELECT * FROM al_cnd WHERE usr_id='.$lUid.' AND mand='.MID;
        $lQry = new CCor_Qry($lSql);
          $this -> mAvailLang = CCor_Res::get('languages');
          $lSqlNam = '';

          foreach ($this -> mAvailLang as $lLang => $lName) {
            $lSqlNam.= " ,".backtick('name_'.$lLang);
            $lSqlCond.= " ,".$lNewCond;
          }

        if ($lRow = $lQry -> getAssoc()) {
            $lSql = 'REPLACE INTO `al_cnd` (`id`, `mand`, `usr_id`'.$lSqlNam.', `cond`) VALUES (';
          $lSql.= $lRow['id'].", ";
            $lSql.= MID.", ".$lUid.$lSqlCond.", ".$lNewCond.")";
          $lUpdateUserTable = FALSE; // Update nicht noetig, da sich die Id in al_usr nicht aendern wird!
        } else {
            $lSql = 'INSERT INTO `al_cnd` (`mand`, `usr_id`'.$lSqlNam.', `cond`) VALUES (';
            $lSql.= MID.", ".$lUid.$lSqlCond.", ".$lNewCond.")";
          }

        $lRet = $lQry -> query($lSql);
        $lNewCndId = $lQry -> getInsertId();
        }

      $this -> dbg('Condition changed from '.$lOldCond.' to '.$lNewCond.' for user '.$lUid.' (al_usr.cnd = '.$lNewCndId.((0 == $lNewCndId) ? ' - Deleted' : '').') /MID='.MID);

      if ($lUpdateUserTable) {
        $lSql = 'UPDATE `al_usr` SET `cnd` = '.$lNewCndId;
        $lSql.= ' WHERE `id` = '.$lUid;
        $lSql.= ' LIMIT 1';
        $lQry -> query($lSql);
      }
  }
    }
  }
  
  protected function save_procnd() {
    if ($this -> mReqVal['procnd'] != $this -> mReqOld['procnd']) {
      if (isset($this -> mInsertId)) {
        $lUid = $this -> mInsertId;
      } else {
        $lUid = $this -> mReqOld['id'];
      }

      $lNewCond = esc($this -> mReqVal['procnd']);
      $lOldCond = esc($this -> mReqOld['procnd']);

      $lSQL = 'UPDATE al_usr SET procnd='.$lNewCond;
      $lSQL.= ' WHERE id='.$lUid;
      $lSQL.= ' LIMIT 1';
      $lQry = new CCor_Qry();
      $lQry -> query($lSQL);
  
      CCor_Cache::clearStatic('cor_res_cnd_'.MID);
      CCor_Cache::clearStatic('cor_res_cnd_'.MID.'_uid');
      CCor_Cache::clearStatic('cor_res_cnd_'.MID.'_gid');
    }
  }

  protected function afterPost($aNew = FALSE) {
    if (!isset($this -> mReqOld['id'])) {//Modfifications in cnd has to made after usr - if a new user has to be inserted
      $this -> save_cnd();
      $this -> save_procnd();
      $this -> saveUsrGroup(gright);
      $this -> saveUsrGroup(gfunc);
    }    
    if ($aNew) {
      $lUsr = CCor_Usr::getInstance();
      $lSql = 'INSERT INTO al_usr_his SET ';
      $lSql.= 'uid="'.$this -> mInsertId.'",';  // changed user
      $lSql.= 'user_id="'.$lUsr -> getId().'",';// 'admin', who has done the modifications
      $lSql.= 'datum=NOW(),';
      $lSql.= 'typ=1,';
      $lSql.= 'subject="User account created"';
      CCor_Qry::exec($lSql);
      if (CCor_Cfg::get('wec.available', TRUE)) $this -> createWcUser();
      $this->autoInsertMand();
    }
  }
  
  protected function createWcUser() {
    $lQue = new CApp_Queue('wecusr');
    $lQue->setParam('uid', $this -> mInsertId);
    $lQue->setParam('mid', MID);
    $lQue->insert();
  }
  
  protected function autoInsertMand() {
    // insert mand membership value
    $lSql = 'INSERT INTO al_usr_mand SET uid='.$this -> mInsertId.', mand='.MID;
    CCor_Qry::exec($lSql);
  }
  
//check required fields empty or not
	public function getEmptyfilled($aRequiredFields) {
	  $lRet = FALSE;
	  $lError = false;
	  $lMissingFields = array ();
	  foreach ( $aRequiredFields as $lRequiredField => $lRequiredvalues ) {
	    if (empty($lRequiredvalues)) {
	      $lMissingFields [] = $lRequiredField;
	      $lError = true;
	    }
	  }
	  if ($lError) {
	    $lAllMissingFields = implode(", ", $lMissingFields);
	    $lRet = $lAllMissingFields;
	  }
	
	  return $lRet;
	}
	#email validate return true or false
	public function isValidEmail($email) {
	  return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $email) && ! preg_match('/@\[/', $email) && ! preg_match('/".+@/', $email) && ! preg_match('/=.+@/', $email);
	}
	
	#mandatory field list
	public function getMendatoryfie($lVals) {
	  $lEmail = $this->isValidEmail(trim($lVals ['email']));
	  $lFirstname = trim($lVals ['firstname']);
	  $lLastname = trim($lVals ['lastname']);
	  $lLocation = trim($lVals ['location']);
	  $lCompany = trim($lVals ['company']);
	  $lUsername = trim($lVals ['user']);
	  $lRet = array (
	      'First Name' => $lFirstname,
	      'Last Name' => $lLastname,
	      'Location' => $lLocation,
	      'Company' => $lCompany,
	      'E-Mail' => $lEmail,
	      'Username' => $lUsername
	  );
	  return $lRet;
	  
	}
	
	protected function doInsert() {
	  $lSql = 'INSERT INTO '.$this -> mTbl.' SET ';
	
	  foreach ($this -> mVal as $lKey => $lVal) {
	    if ($this -> mAutoInc) {
	      if(in_array($lKey, $this -> mAutoIncKey)) {  // autoinc
	        #        if ($lKey == $this -> mKey) {  // autoinc
	        continue;
	      }
	    }
	    if (!empty($lVal)) {
	    $lSql.= $lKey.'="'.mysql_real_escape_string($lVal).'",';
	    }
	  }
	  $lSql = strip($lSql, 1);
	
	  $this -> dbg($lSql);
	  if ($this -> mTest) {
	    return TRUE;
	  } else {
	    $lQry = new CCor_Qry();
	    $lRet = $lQry -> query($lSql);
	    if ($lRet) {
	      $this -> mInsertId = $lQry -> getInsertId();
	    } else {
	      $this -> mInsertId = NULL;
	    }
	    return $lRet;
	  }
  }
}