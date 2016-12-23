<?php
class CInc_Usr_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('usr.menu');

    // Ask If user has right for this page
    $lpn = 'usr';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }
  }

  protected function actXlsexp() {

    $lFileName = 'User_List';
    $lFileName.= date('Ymd_H-i-s');
    $lFileName.= '.xls';

    $lUserList = new CInc_Usr_List();
    $lUserList -> mIte -> setLimit(0, 10000);
    $lUserList -> mIte = $lUserList -> mIte -> getArray();
    $lXls = $lUserList -> getExcel();
    $lXls -> downloadAs($lFileName);
  }

  protected function actStd() {
    $lVie = new CUsr_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lUid = $this -> getReqInt('id');
    //darf nur der Benutzer aus aktuellem Mandant und "Mandant=0" editiert werden.
   # $lNam = CCor_Qry::getInt('SELECT id FROM al_usr WHERE id='.$lUid.' AND (mand IN (0,'.MID.')'.' OR mands LIKE "%,'.MID.',%")');
/*    $lNam = CCor_Qry::getInt('SELECT uid FROM al_usr_mem WHERE uid='.$lUid.' AND mand IN (0,'.MID.')');
    if (empty($lNam)){
      $this -> redirect();
    }
*/
    $lMen = new CUsr_Menu($lUid, 'dat');
    $lVie = new CUsr_Form_Edit($lUid);
    $this -> render(CHtm_Wrap::wrap($lMen,$lVie));
  }

  protected function actSedt() {
    $lVal = $this -> mReq -> getVal('val');
    $lUid = intval($lVal ['id']);
    $lCond = $this -> getReq('cond_id');
    $lMod = new CUsr_Mod($lCond);
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
      $lMen = new CUsr_Menu($lUid, 'dat');
      $lVie = new CUsr_Form_Edit($lUid);
      $lVie -> assignVal($lVal);
      $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
      $this -> redirect('index.php?act=usr.edt&id=' . $lUid);
    }

    else {
      $lMod -> update();
      $this -> msg(lan('usr.succ.update'), mtUser, mlInfo);
      $this -> redirect('index.php?act=usr.edt&id=' . $lUid);
      CCor_Cache::clearStatic('cor_res_usr');
    }
  }

  protected function actNew() {
    $lVie = new CUsr_Form_Base('usr.snew', lan('usr.new'));
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
      $lVie = new CUsr_Form_Base('usr.snew', lan('usr.new'));
      $lVie -> assignVal($lVals);
      $this -> render($lVie);
    }

    if ($lNum != 0) {
      $this -> msg(lan('hom.usr.error.inuse'), mtUser, mlWarn);
      $lVie = new CUsr_Form_Base('usr.snew', lan('usr.new'));
      $lVie -> assignVal($lVals);
      $this -> render($lVie);
      $this -> redirect('index.php?act=usr.new');
    }

    else {
      $this -> msg("$lUsername " . lan('usr.success'), mtUser, mlInfo);
      if ($lMod -> insert()) {
        $lUid = $lMod -> getInsertId();
        // fï¿½r die neu Benutzer werden sys.mid und sys.mand im al_usr_pref eingetragen.
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
        $this -> redirect('index.php?act=usr.edt&id=' . $lUid);
      } else {
        $this -> redirect();
      }
    }
  }

  protected function actDel() {
    $lUid = $this -> mReq -> getInt('id');
    CCor_Qry::exec('UPDATE al_usr SET del="Y", backup="0" WHERE id='.$lUid);
    $lUsr = CCor_Usr::getInstance();
    $lSql = 'INSERT INTO al_usr_his SET ';
    $lSql.= 'uid='.$lUid.',';
    $lSql.= 'user_id="'.$lUsr -> getId().'",';
    $lSql.= 'datum=NOW(),';
    $lSql.= 'typ=1,';
    $lSql.= 'subject="User account marked as deleted"';
    CCor_Qry::exec($lSql);
    CCor_Cache::clearStatic('cor_res_usr');
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
    CCor_Cache::clearStatic('cor_res_usr');
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


  protected function actAct() {
    $lUid = $this -> getReqInt('id');
    $lSql = 'UPDATE al_usr SET flags=(flags | 1) WHERE id='.$lUid;
    CCor_Qry::exec($lSql);
    CCor_Cache::clearStatic('cor_res_usr');
    $this -> redirect();
  }


  protected function actTog() {
    $lUsr = CCor_Usr::getInstance();
    $lPrf = $lUsr -> getPref('usr.showdel', '');
    if (empty($lPrf)) {
      $lUsr -> setPref('usr.showdel', 1);
    } else {
      $lUsr -> setPref('usr.showdel', '');
    }
    $this -> redirect();
  }

  protected function actImp() {
    $lArr = array();
    $lQry = new CCor_Qry('SELECT * FROM al_user WHERE id > 1 ORDER BY id');
    foreach ($lQry as $lRow) {
      $lItm = array();
      $lItm['id']        = $lRow['id'];
      if (('Herr' == $lRow['anrede']) or ('Mr.' == $lRow['anrede'])) {
        $lItm['anrede'] = 'Mr.';
      } else {
        $lItm['anrede'] = 'Mrs.';
      }
      $lItm['firstname'] = $lRow['vorname'];
      $lItm['lastname']  = $lRow['name'];
      $lItm['email']     = $lRow['email'];
      $lItm['user']      = $lRow['user'];
      $lItm['pass']      = CApp_Pwd::encryptPassword($lRow['pass']);
      $lArr[] = $lItm;
    }
    foreach ($lArr as $lUsr) {
      $lSql = 'INSERT INTO al_usr SET ';
      foreach ($lUsr as $lKey => $lVal) {
        if (!empty($lVal)) {
          $lSql.= $lKey.'="'.addslashes($lVal).'",';
        }
      }
      $lSql = strip($lSql);
      $lQry -> query($lSql);
    }

  }

  protected function actWecUpdate() {
    $lWec = new CApi_Wec_Client();
    $lWec -> loadConfig();
    $lWex = new CApi_Wec_Query_Createuser($lWec);
    $lArr = array();
    $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE id > 1 ORDER BY id');
    $lRet = '';
    foreach ($lQry as $lRow) {
      $lRes = $lWex -> getUserID($lRow['id']);
      $lRet.=$lRow['firstname'].' '.$lRow['lastname'].' ('.$lRow['id'].') = '.$lRes.'<br>';
    }
    $this -> render($lRet);
  }

  protected function actAjx() {
    $json_row = array();
    $ret_json = array();

    $lSql = 'SELECT firstname,lastname FROM al_usr WHERE 1 ';
    $lVal = addslashes(trim($this -> getReq('term')));
    if (!empty($lVal)) {
      $lSql.= 'AND (';
      $lSql.= '(firstname LIKE "'.$lVal.'%") OR ';
      $lSql.= '(lastname LIKE "'.$lVal.'%")';
      $lSql.= ') ';
    }
    $lSql.= 'ORDER BY lastname,firstname LIMIT 15';
    $lQry = new CCor_Qry($lSql);

    //Transform to json
    foreach($lQry as $lRow) {
      $json_row["value"] = $lRow['lastname'] . ", " . $lRow['firstname'];
      $json_row["label"] = $lRow['firstname'] . " " . $lRow['lastname'];
      array_push($ret_json, $json_row);
    }
    //Ausgabe
    print Zend_Json::encode($ret_json);
  }

  protected function actReport() {
    $lArr['oldjobid'] = 'jobid';
    $lArr['marke'] = 'marke';
    $lArr['sorte'] = 'sorte';
    $lArr['stichw'] = 'stichw';
    $lArr['land_besteller'] = 'land_besteller';
    $lArr['land_vertrieb'] = 'land_vertrieb';
    $lArr['regional_code'] = 'regional_code';
    $lArr['region_vertrieb'] = 'region_vertrieb';
    $lArr['prod_art'] = 'prod_art';
    $lArr['packmittel'] = 'packmittel';
    $lArr['packmittel_cat'] = 'packmittel_cat';

    $lQry = new CCor_Qry('SELECT * FROM al_archiv_1 WHERE oldjobid>"" ORDER BY oldjobid LIMIT 400');
    foreach ($lQry as $lRow) {
      $lSql = 'INSERT INTO al_job_shadow_'.intval(MID).' SET ';
      foreach ($lArr as $lKey => $lVal) {
        $lSql.= $lVal.'="'.addslashes(stripslashes($lRow[$lKey])).'",';
      }
      $lSql = strip($lSql);
      $this -> dbg($lSql);
      CCor_Qry::exec($lSql);
    }
    $this -> redirect();
  }

  protected function actDdl() {
    $lSql = 'SELECT id FROM al_job_shadow_'.intval(MID);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lId = $lRow['id'];
      $lTmp = array();
      $lRnd = rand(1, 12);
      $lRndDay = rand(1, 250);
      $lTim = mktime(1+$lRnd,0,0, 1,$lRndDay, 2008);
      for ($i=1; $i<11; $i++) {
        $lDat = date('Y-m-d H:i:s', $lTim);
        $lTmp['fti_'.$i] = $lDat;
        $lRndHr = rand(1,12);
        $lRndDay = rand(2,10);
        $lTim+= $lRndDay*24*60*60 + $lRndHr * 60*60;
      }
      for ($i=1; $i<11; $i++) {
        $lDat = date('Y-m-d H:i:s', $lTim);
        $lTmp['lti_'.$i] = $lDat;
        $lRndHr = rand(1,12);
        $lRndDay = rand(2,10);
        $lTim+= $lRndDay*24*60*60 + $lRndHr * 60*60;
      }
      $lSql = 'UPDATE al_job_shadow_'.intval(MID).' SET '.LF;
      foreach ($lTmp as $lKey => $lVal) {
        $lSql.= $lKey.'="'.$lVal.'",';
      }
      $lSql = strip($lSql);
      $lSql.= ' WHERE id='.$lId.LF;
      echo nl2br($lSql);
      CCor_Qry::exec($lSql);
    }
  }

  protected function actClear() {
    $lQry = new CCor_Qry('SELECT id,flags FROM al_fie');
    foreach ($lQry as $lRow) {
      $lFla = $lRow['flags'];
      if (bitset($lFla, ffReport)) {
        $lFla = $lFla &~ ffReport;
        $lSql = 'UPDATE al_fie SET flags='.$lFla.' WHERE id='.$lRow['id'].' AND `mand`='.MID;
        CCor_Qry::exec($lSql);
        #$this -> dbg($lSql);
      }
    }
  }

  protected function actTpl() {
    $lFie = CCor_Res::get('fie');
    foreach ($lFie as $lRow) {
      echo $lRow['name_'.LAN].' ';
      echo '{bez.'.$lRow['alias'].'} ';
      echo '{val.'.$lRow['alias'].'}'.BR;
    }
  }

  protected function actMail() {
    $lMai = new CApi_Mail_Item('info@5flow.eu', '', 'info@5flow.eu', '', 'Test', 'Body');
    $lMai -> send();
  }

  protected function actTrans() {
    $lSql = 'SELECT * FROM al_job_fields_choice WHERE mand='.MID.' AND TRIM(value)<>"" ORDER BY field,value';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lSql = 'INSERT INTO al_fie_choice SET mand='.MID.',inserted=NOW(),';
      $lSql.= 'alias='.esc($lRow['field']).',';
      $lSql.= 'val='.esc(trim($lRow['value']));
      echo $lSql.BR.LF;
    }
    exit;
  }


  protected function actCheck() {
    $lFie = CCor_Res::extract('alias', 'learn', 'fie');
    $lSql = 'SELECT distinct(alias) FROM al_fie_choice where mand='.MID;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lAli = $lRow['alias'];
      $lDef = $lFie[$lAli];
      if (empty($lDef)) {
        $lSql = 'UPDATE al_fie SET learn='.esc($lAli).' WHERE alias='.esc($lAli).' AND `mand`='.MID.';';
        echo $lSql.BR.LF;
      }

    }

  }

  protected function actEvent() {
    $lQry = new CApi_Alink_Query_Callevent('100061840','test','soso');
    $lRes = $lQry->query();
  }

  protected function actLogin() {
    $lQry = new CApi_Alink_Query('login');
    $lQry -> addParam('sid', MANDATOR); // 'har'
    $lQry -> addParam('user', strtolower(MANDATOR_NAME)); // 'haribo'
    $lQry -> addParam('pass', 'pass');
    $lRes = $lQry->query();
  }

  protected function actCndtest() {
    $lCnd = new CCor_Cond();
    $lCnd -> test();
  }

  protected function actWec() {
    #$lWec = new CApi_Wec_Client();
    $lWec = new CApi_Wec_Stub();
    $lWec -> setConfig('http://webcenter2.esko.com/WebCenter_Inst/', 'qbf', 'qbf2qbf');
    $lWec -> setResponse('<error><code>001</code><message>an error message</message></error>');
    $lQry = new CApi_Wec_Query_Doclist($lWec);
    $lRet = $lQry -> getList('00002_0000000060');
    #var_export($lRet);

    #var_export($lWec -> query('GetDocumentList.jsp', array('projectID' => '00002_0000000060')));
    foreach ($lRet as $lKey => $lRow) {
      $this -> dump($lRow);
    }
    $this -> render('');
  }

  protected function actWecusr2() {
    $lWec = new CApi_Wec_Client();
    $lWec -> loadConfig();

    // create the user?
    $lQry = new CApi_Wec_Query_Createuser($lWec);
    $lRes = $lQry -> createFromDb(1);
    $this -> render($lRes);
  }

  protected function actWechis() {
    $lFrm = new CHtm_Form('usr.swechis', 'Webcenter Projekt-History', false);
    $lFrm -> addDef(fie('pid', 'Webcenter Projekt-ID'));
    $lFrm -> setVal('pid', '00002_0000000002');
    $this -> render($lFrm);
  }

  protected function actSwechis() {
    $lVal = $this -> getReq('val');
    $lPid = $lVal['pid'];
    $lCls = new CApi_Wec_Updatehistory('art', '011', $lPid);

    $lFrm = new CHtm_Form('usr.swechis', 'Webcenter Projekt-History', false);
    $lFrm -> addDef(fie('pid', 'Webcenter Projekt-ID'));
    $lFrm -> setVal('pid', $lPid);

    $this -> render(CHtm_Wrap::wrap($lFrm,$lCls -> getHistoryDump()));
  }

  protected function actWecusr() {
    $lUid = $this -> getInt('id');

    $lMen = new CUsr_Menu($lUid, 'wecu');
    # $lVie = new CUsr_Info_Form($lUid);

    $lFrm = new CHtm_Form('usr.swecusr', lan('usr-wecusr.new'), false);
    $lFrm -> addDef(fie('uid', 'User', 'uselect'));
    $lFrm -> setVal('uid', $lUid);
    # $this -> render($lFrm);

    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSwecusr() {
    $lVal = $this -> getReq('val');
    $lUid = $lVal['uid'];
    $lWec = new CApi_Wec_Client();
    $lWec -> loadConfig();

    // create the user?
    $lQry = new CApi_Wec_Query_Createuser($lWec);
    $lRes = $lQry -> createFromDb($lUid);

    if ($lRes !== False) {
      // AddMemberToGroup
      $lCfg = CCor_Cfg::getInstance();
      $lQry = new CApi_Wec_Query_Addusertogroup($lWec);
      $lRes = $lQry -> createFromDb($lUid, $lCfg -> getVal('wec.grp'));
    }
    # $this -> render($lRes);
    $this -> redirect('index.php?act=usr-info&id='.$lUid);
  }

  protected function actSwecusrEx() {
    $lVal = $this -> getReq('val');
    $lUid = $lVal['uid'];

    $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE id='.$lUid);
    if (!$lRow = $lQry -> getDat()) return false;

    $aUame = $lRow['user'];
    $aLame = $lRow['lastname'];
    $aVame = $lRow['firstname'];
    $aMail = $lRow['email'];

    $lCli = new CApi_Wec_Robot();
    $lCli -> loadConfig();
    $lCli -> setDebug();
    $lRet = $lCli -> login();

    $lRet .= $lCli -> createNewUser($aUame, $aVame, $aLame, $aMail);
    $lRet .= $lCli -> logout();
    $this -> render($lRet);

  }

  // to do: rename to dashboard instead of hom2...
  protected function actHom2() {
    $lUid = $this -> getInt('id');
    $lErr = $this -> getInt('err');

    $lQry = new CCor_Qry('SELECT val FROM al_usr_pref WHERE code="hom.url" AND uid='.$lUid.' AND mand='.MID);
    $lRow = $lQry -> getDat();

    $lFrm = new CHtm_Form('usr.shom2', lan('usr-hom2'), false);
    $lFrm -> setAtt('style', 'width: 600px');
    $lFrm -> addDef(fie('uid', '', 'hidden'));
    $lFrm -> addDef(fie('homurl', 'URL', 'edit', '' , array('style' => 'width: 520px')));
    $lFrm -> setVal('uid', $lUid);
    $lFrm -> setVal('homurl', $lRow['val']);

    $lMen = new CUsr_Menu($lUid, 'hom2');
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actShom2() {
    $lVal = $this -> getReq('val');
    $lURL = trim($lVal['homurl']);
    $lUid = $lVal['uid'];

    if (!empty($lURL)) {
      if (!preg_match("=://=", $lURL)) {
        $lURL = "http://$lURL";
      }

      $lURL = parse_url($lURL);

      if (!isset($lURL["port"])) {
        $lURL["port"] = 80;
      }

      if (!isset($lURL["path"])) {
        $lURL["path"] = "/";
      }

      $lFSO = fsockopen($lURL["host"], $lURL["port"], $errno, $errstr, 30);

      if (!$lFSO) {
        $this -> msg('The URL you entered is either not valid or not accessible at the moment!', mtUser, mlError);
        $this -> redirect('index.php?act=usr.hom2&id='.$lUid);
      } else {
        $lURL = $this -> unparse_url($lURL);
      }
    }

    $lSql = 'REPLACE INTO al_usr_pref SET ';
    $lSql.= 'uid="'.$lUid.'", ';
    $lSql.= 'mand="'.MID.'", ';
    $lSql.= 'code="hom.url", ';
    $lSql.= 'val="'.$lURL.'" ';
    CCor_Qry::exec($lSql);

    $this -> redirect('index.php?act=usr.hom2&id='.$lUid);
  }

  // to do: rename to reports instead of rep2
  protected function actRep2() {
    $lUid = $this -> getInt('id');

    $lQry = new CCor_Qry('SELECT val FROM al_usr_pref WHERE code="rep.url" AND uid='.$lUid.' AND mand='.MID);
    $lRow = $lQry -> getDat();

    $lFrm = new CHtm_Form('usr.srep2', lan('usr-rep2'), false);
    $lFrm -> setAtt('style', 'width: 600px');
    $lFrm -> addDef(fie('uid', '', 'hidden'));
    $lFrm -> addDef(fie('repurl', 'URL', 'edit', '' , array('style' => 'width: 520px')));
    $lFrm -> setVal('uid', $lUid);
    $lFrm -> setVal('repurl', $lRow['val']);

    $lMen = new CUsr_Menu($lUid, 'rep2');
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSrep2() {
    $lVal = $this -> getReq('val');
    $lURL = trim($lVal['repurl']);
    $lUid = $lVal['uid'];

    if (!empty($lURL)) {
      if (!preg_match("=://=", $lURL)) {
        $lURL = "http://$lURL";
      }

      $lURL = parse_url($lURL);

      if (!isset($lURL["port"])) {
        $lURL["port"] = 80;
      }

      if (!isset($lURL["path"])) {
        $lURL["path"] = "/";
      }

      $lFSO = fsockopen($lURL["host"], $lURL["port"], $errno, $errstr, 30);

      if (!$lFSO) {
        $this -> msg('The URL you entered is either not valid or not accessible at the moment!', mtUser, mlError);
        $this -> redirect('index.php?act=usr.rep2&id='.$lUid);
      } else {
        $lURL = $this -> unparse_url($lURL);
      }
    }

    $lSql = 'REPLACE INTO al_usr_pref SET ';
    $lSql.= 'uid="'.$lUid.'", ';
    $lSql.= 'mand="'.MID.'", ';
    $lSql.= 'code="rep.url", ';
    $lSql.= 'val="'.$lURL.'" ';
    CCor_Qry::exec($lSql);

    $this -> redirect('index.php?act=usr.rep2&id='.$lUid);
  }

  protected function actApl() {
    $lCli = new CApi_Wec_Robot();
    $lCli -> loadConfig();
    $lCli -> setDebug();
    $lRet = $lCli -> login();
    #$lRet = $lCli -> startApl('00002_0000000003');
    #$lRet = $lCli -> startApl('00002_0000000003');
    #$lRet = $lCli -> forcedApproval('00002_0000000025', '00002_0000000026');
    $lRet = $lCli -> startProject('00002_0000000033');
    #$lCli -> logout();
    $this -> render($lRet);
  }

  protected function unparse_url($lParse_URL) {
    if (!is_array($lParse_URL)) {
      return false;
    }

    $lURI = isset($lParse_URL['scheme']) ? $lParse_URL['scheme'].':'.((strtolower($lParse_URL['scheme']) == 'mailto') ? '' : '//') : '';
    $lURI.= isset($lParse_URL['user']) ? $lParse_URL['user'].(isset($lParse_URL['pass']) ? ':'.$lParse_URL['pass'] : '').'@' : '';
    $lURI.= isset($lParse_URL['host']) ? $lParse_URL['host'] : '';
    $lURI.= isset($lParse_URL['port']) ? ':'.$lParse_URL['port'] : '';

    if (isset($lParse_URL['path'])) {
      $lURI.= (substr($lParse_URL['path'], 0, 1) == '/') ? $lParse_URL['path'] : ((!empty($lURI) ? '/' : '' ) . $lParse_URL['path']);
    }

    $lURI.= isset($lParse_URL['query']) ? '?'.$lParse_URL['query'] : '';
    $lURI.= isset($lParse_URL['fragment']) ? '#'.$lParse_URL['fragment'] : '';

    return $lURI;
  }

  protected function actDeprecatemem() {
    $lUsr = CCor_Res::get('usr');
    $lQry = new CCor_Qry('SELECT * FROM al_usr_mem');
    $lRes = array();
    $lRet = '';
    foreach ($lQry as $lRow) {
      $lUid = $lRow['uid'];
      if (!isset($lUsr[$lUid])) {
        $lRet.= $lUid.BR;
        $lRes[$lUid] = $lUid;
      }
    }
    sort($lRes);
    $lSql = 'DELETE FROM al_usr_mem WHERE uid IN ('.implode(',',$lRes).');';
    #$lQry->query($lSql);
    #$this->render(var_export($lRes, TRUE));
    $this->render($lSql);
  }

  protected function actConfig() {
    $lCfg = CCor_Cfg::getInstance();
    $lRows = $lCfg->getValues();
    $lRet = '';
    $lRet.= '<table cellpadding="2" class="tbl">';
    $lRet.= '<tr><td class="th1">Key</td><td class="th1">Value</td></tr>';
    foreach ($lRows as $lKey => $lVal) {
      $lRet.= '<tr>';
      $lRet.= '<td class="td2">'.$lKey.'</td>';
      $lRet.= '<td class="td1">';
      if (is_array($lVal)) {
        $lRet.= htm(var_export($lVal, TRUE));
      } else {
        $lRet.= htm($lVal);
      }

      $lRet.= '</td></tr>';
    }
    $lRet.= '</table>';
    $this->render($lRet);
  }

  function actMytest() {
    $lMem = new CInc_App_Mem();
    #$lRet = $lMem->getParentNames(3);
    #var_dump($lRet);
    #$lMem -> addUserToGroup(541, 3);
    #$lMem -> removeFromGroups(541, array('13'));
    echo 'NEW ALGO'.BR;
    $lMem -> addToGroups(541, '3,460');
    #$lMem -> removeFromGroups(541, 13);
  }

  function actGetinfo() {
    $lQry = new CApi_Alink_Query('getInfo');
    $lQry-> addParam('sid', MAND);
    var_export($lQry->query());
  }

  protected function actSession() {
    $lSes = CCor_Ses::getInstance();
    $this->dump($lSes->toArray());
    $this->render('');
  }

  protected function actCopymem() {
    $lId = $this->getInt('id');
    $lUsr = CCor_Usr::getInstance();
    $lUsr->setPref('usr.copymem', $lId);
    $this->redirect();
  }

  protected function actStopCopy() {
    $lUsr = CCor_Usr::getInstance();
    $lUsr->stopCopyJob();
    $this->redirect();
  }

  protected function actPastemem ()
  {
    $lAdd = $this -> getVal('add');
    $lIds = $this -> getVal('ids');
    $lUsr = CCor_Usr::getInstance();
    $lUid = intval($lUsr -> getPref('usr.copymem'));

    if (empty($lIds) or empty($lUid)) {
      $this -> msg('Nothing to copy', mtUser, mlWarn);
      $this -> redirect();
    }
    $this -> mNames = CCor_Res::extract('id', 'name', 'gru');
    $lAdminlevel = array();
    $lQry = new CCor_Qry('SELECT * FROM al_gru WHERE admin_level <> 0');
    if ($lRow = $lQry -> getAssoc()) {
      $lGroupId = $lRow['id'];
      #$lAdminlevel [] = $this->mNames[$lGroupId];
      $lAdminlevel[] = $lGroupId;
    }
    $lAdminlevel = implode(', ', $lAdminlevel);
    $lQry = new CCor_Qry('SELECT gid FROM al_usr_mem WHERE mand IN (0,' . MID .') AND gid NOT IN ("' . $lAdminlevel . '") AND uid=' . $lUid);
    foreach ($lQry as $lRow) {
      $lGid[] = $lRow['gid'];
      $lGname[] = $this -> mNames[$lRow['gid']];
    }
    if ( ! $lAdd) {
      $lQry -> exec('DELETE FROM al_usr_mem WHERE uid IN (' . $lIds . ') AND mand IN (0,' .MID . ')');
    }
    $this -> msg('Paste membership ' . '(' . implode(', ', $lGname) . ')',mtUser);
    $lMem = new CInc_App_Mem();
    $lArrIds = explode(',', $lIds);

    foreach ($lArrIds as $lId) {
      $lMem -> addToGroups($lId, $lGid);
    }
    $this -> redirect();
  }

}