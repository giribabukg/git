<?php
class CInc_Usg_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> m2Act = $this -> mMod;
    $this -> mTitle = lan($this -> mMod.'.menu');
    $this->mMmKey = 'usg';

    // Ask If user has right for this page
    $lpn = 'usg';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actStd() {
    $lVie = new CUsg_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lUid = $this -> getReqInt('id');
    // Es darf nur der Benutzer aus dem aktuellen Mandanten und "Mandant=0" editiert werden.
    $lNam = CCor_Qry::getInt('SELECT uid FROM al_usr_mand WHERE uid='.$lUid.' AND mand = '.MID);
    if (empty($lNam)){
      $this -> redirect();
    }
    $lMen = new CUsg_Menu($lUid, 'dat', $this -> mMod);
    $lVie = new CUsg_Form_Edit($lUid);
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSedt() {
    $lVal = $this -> mReq -> getVal('val');
    $lUid = intval($lVal ['id']);
    $lMod = new CUsr_Mod();
    $lMod -> getPost($this -> mReq);
    
    $lUsername = trim($lVal ['user']);
    $lQryResult = CCor_Res::extract('id', 'user', 'usr', array (
        'user' => $lUsername 
    ));
    unset($lQryResult [$lUid]);
    $lNum = count($lQryResult);
    
    $aRequiredFields = $lMod -> getMendatoryfie($lVal);
    $lAllMissingFields = $lMod -> getEmptyfilled($aRequiredFields);
    
    $lStr2Arr = explode(',', $lAllMissingFields);
    $lEmailpos = strpos($lAllMissingFields, 'E-Mail');
    
    if ($lAllMissingFields) {
      
      if (($lEmailpos !== false) && count($lStr2Arr) > 1) {
        $lAllMissingFields = str_replace(", E-Mail", " ", $lAllMissingFields);
        $this -> msg(lan('usr.error.email'), mtUser, mlWarn);
        $this -> msg(lan('usr.error.req.empty') . " ($lAllMissingFields)", mtUser, mlWarn);
      } 

      elseif ($lEmailpos !== false) {
        $this -> msg(lan('usr.error.email'), mtUser, mlWarn);
      } else
        $this -> msg(lan('usr.error.req.empty') . " ($lAllMissingFields)", mtUser, mlWarn);
        $this -> redirect('index.php?act=usr.edt&id=' . $lUid);
    }
    
    if ($lNum != 0) {
      $this -> msg(lan('hom.usr.error.inuse'), mtUser, mlWarn);
      $lMen = new CUsg_Menu($lUid, 'dat', $this->mMod);
      $lVie = new CUsg_Form_Edit($lUid);
      $lVie -> assignVal($lVal);
      $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
      $this -> redirect('index.php?act=' . $this->m2Act . '.edt&id=' . $lUid);
    } else {
      $lMod -> update();
      $this -> msg(lan('usr.succ.update'), mtUser, mlInfo);
      CCor_Cache::clearStatic('cor_res_usr');
      $this -> redirect('index.php?act=' . $this -> m2Act . '.edt&id=' . $lUid);
    }
  }

  protected function actNew() {
    $lVie = new CUsg_Form_Base($this -> m2Act.'.snew', lan('usg.new'));
    $this -> render($lVie);
  }

  protected function actSnew() {
    $lVals = $this -> mReq -> getVal('val');
    $lMod = new CUsr_Mod();
    $lMod -> getPost($this -> mReq);
    
    $lUsername = trim($lVals ['user']);
    $lQryResult = CCor_Res::extract('id', 'fullname', 'usr', array (
        'user' => $lUsername 
    ));
    $lNum = count($lQryResult);
    
    $aRequiredFields = $lMod -> getMendatoryfie($lVals);
    $lAllMissingFields = $lMod -> getEmptyfilled($aRequiredFields);
    
    if ($lAllMissingFields) {
      $this -> msg((lan('usr.required') . ' (' . $lAllMissingFields . ')'), mtUser, mlWarn);
      $lVie = new CUsg_Form_Base('usg.snew', lan('usg.new'));
      $lVie -> assignVal($lVals);
      $this -> render($lVie);
      exit();
    }
    
    if ($lNum != 0) {
      $this -> msg(lan('hom.usr.error.inuse'), mtUser, mlWarn);
      $lVie = new CUsg_Form_Base($this -> m2Act . '.snew', lan('usg.new'));
      $lVie -> assignVal($lVals);
      $this -> render($lVie);
    } else {
      $this -> msg("$lUsername " . lan('usr.success'), mtUser, mlInfo);
      if ($lMod -> insert()) {
        $lUid = $lMod -> getInsertId();
        // Fuer die neuen Benutzer werden sys.mid und sys.mand im al_usr_pref eingetragen.
        $lSql = 'REPLACE INTO al_usr_pref SET ';
        $lSql .= 'code="sys.mid", ';
        $lSql .= 'val=' . MID . ', ';
        $lSql .= 'uid="' . $lUid . '" ';
        CCor_Qry::exec($lSql);
        
        $lSql = '';
        $lSql = 'REPLACE INTO al_usr_pref SET ';
        $lSql .= 'code="sys.mand", ';
        $lSql .= 'val="' . MAND . '", ';
        $lSql .= 'uid="' . $lUid . '" ';
        CCor_Qry::exec($lSql);
        
        CCor_Cache::clearStatic('cor_res_usr');
        
        // Legt den User auch in der richtigen Hauptgruppe an.
        $lUsr = CCor_Usr::getInstance();
        $lGru = $lUsr -> getVal('gadmin');
        
        $lMem = new CApp_Mem();
        $lMem -> addToGroups($lUid, $lGru);
        
        CCor_Cache::clearStatic('cor_res_mem_' . MID);
        CCor_Cache::clearStatic('cor_res_mem_' . MID . '_uid');
        CCor_Cache::clearStatic('cor_res_mem_' . MID . '_gid');
        
        $this -> redirect('index.php?act=' . $this -> m2Act . '.edt&id=' . $lUid);
      } else {
        $this -> redirect();
      }
    }
  }

  protected function actDel() {
    $lUid = $this -> mReq -> getInt('id');
    CCor_Qry::exec('UPDATE al_usr SET del="Y" WHERE id='.$lUid);
    $lUsr = CCor_Usr::getInstance();
    $lSql = 'INSERT INTO al_usr_his SET ';
    $lSql.= 'uid='.$lUid.',';
    $lSql.= 'user_id="'.$lUsr -> getId().'",';
    $lSql.= 'datum=NOW(),';
    $lSql.= 'typ=1,';
    $lSql.= 'subject="User account marked as deleted"';
    CCor_Qry::exec($lSql);
    $this -> redirect();
  }

  protected function actUndel() {
    $lUid = $this -> mReq -> getInt('id');
    CCor_Qry::exec('UPDATE al_usr SET del="N" WHERE id='.$lUid);
    $lUsr = CCor_Usr::getInstance();
    $lSql = 'INSERT INTO al_usr_his SET ';
    $lSql.= 'uid='.$lUid.',';
    $lSql.= 'user_id="'.$lUsr -> getId().'",';
    $lSql.= 'datum=NOW(),';
    $lSql.= 'typ=1,';
    $lSql.= 'subject="User account re-activated"';
    CCor_Qry::exec($lSql);
    $this -> redirect();
  }

  protected function actOpt() {
    $this -> mReq -> expect('val');
    $lVal = $this -> getReq('val');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.opt', $lVal);
    $lUsr -> setPref($this -> mPrf.'.page', 0);
    $this -> redirect();
  }

  protected function actAjx() {
    $lRet = '';
    $lRet.= '<ul>';
    $lSql = 'SELECT firstname,lastname FROM al_usr WHERE 1 ';
    $lVal = addslashes(trim($this -> getReq('val')));
    if (!empty($lVal)) {
      $lSql.= 'AND (';
      $lSql.= '(firstname LIKE "'.$lVal.'%") OR ';
      $lSql.= '(lastname LIKE "'.$lVal.'%")';
      $lSql.= ') ';
    }
    $lSql.= 'AND mand IN(0,'.MID.') ';
    $lSql.= 'ORDER BY lastname,firstname LIMIT 10';
    $lQry = new CCor_Qry($lSql);
    $lRet.= '<li>'.$this -> getReq('val').'</li>';
    foreach ($lQry as $lRow) {
      $lRet.= '<li>'.htm($lRow['lastname'].', '.$lRow['firstname']).'</li>';
    }
    $lRet.= '</ul>';
    echo $lRet;
  }

  protected function actWecusr() {
    $lUid = $this -> getInt('id');
    $lMen = new CUsg_Menu($lUid, 'wecu', $this -> m2Act);

    $lFrm = new CHtm_Form('usg.swecusr', lan('usg-wecusr.new'), false);

    $lUsr = CCor_Usr::getInstance();
    $this -> mGruKey = $lUsr -> getVal('gadmin');
    $lSql = 'SELECT p.id, p.firstname, p.lastname FROM al_usr as p, al_usr_mem as q WHERE q.uid=p.id AND q.gid='.$this -> mGruKey.' ORDER BY p.lastname';
    $lQry = new CCor_Qry($lSql);
    $lArr = array();
    foreach ($lQry as $lRow) {
      $lArr[$lRow['id']] = $lRow['lastname'].', '.$lRow['firstname'];
    }

    $lFrm -> addDef(fie('uid', 'User', 'select', $lArr));
    $lFrm -> setVal('uid', $lUid);

    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSwecusr() {
    $lVal = $this -> getReq('val');
    $lUid = $lVal['uid'];
    $lWec = new CApi_Wec_Client();
    $lWec -> loadConfig();

    $lQry = new CApi_Wec_Query_Createuser($lWec);
    $lRes = $lQry -> createFromDb($lUid);

    if ($lRes !== False) {
      $lCfg = CCor_Cfg::getInstance();
      $lQry = new CApi_Wec_Query_Addusertogroup($lWec);
      $lRes = $lQry -> createFromDb($lUid, $lCfg -> getVal('wec.grp'));
    }
    $this -> redirect('index.php?act=usg-info&id='.$lUid);
  }
  
  protected function actXlsexp() {
  
    $lFileName = 'User_List';
    $lFileName.= date('Ymd_H-i-s');
    $lFileName.= '.xls';
  
    $lUserList = new CInc_Usg_List();
    $lUserList -> mIte -> setLimit(0, 10000);
    $lUserList -> mIte = $lUserList -> mIte -> getArray();
    $lXls = $lUserList -> getExcel();
    $lXls -> downloadAs($lFileName);
  }

}