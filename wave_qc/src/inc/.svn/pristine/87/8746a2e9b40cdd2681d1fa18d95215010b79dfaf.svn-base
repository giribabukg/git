<?php
class CInc_Job_Cnt extends CCor_Cnt {

  protected $mSrcCnt;
  protected $mSrc;

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    $lModArr = explode('-', $aMod);
    if (!empty($lModArr[1])) {
      $this -> mSrcCnt = $lModArr[1];
    }
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan($aMod.'.menu');
    $this -> mUsr = CCor_Usr::getInstance();
    if (!$this -> mUsr -> canRead($aMod)) {
      $this -> denyAccess();
    }
    $this -> mAva = fsPro;
  }

  protected function getFac() {
    if (is_null($this->mFac)) {
      $this->mFac = new CJob_Fac($this->mSrc);
    }
    return $this->mFac;
  }

  protected function checkArc($aSrc, $aJobId) {
    if ('pro' == $aSrc) {
      $lSql = 'SELECT COUNT(*) FROM al_job_pro_'.MID.' WHERE del="N" AND webstatus=200 AND id='.esc($aJobId);
    } else {
      $lSql = 'SELECT COUNT(*) FROM al_job_arc_'.MID.' WHERE jobid='.esc($aJobId);
    }
    $lCnt = CCor_Qry::getInt($lSql);
    if (0 < $lCnt) {
      $this -> redirect('index.php?act=arc-'.$aSrc.'.edt&jobid='.$aJobId);
    }
  }

  protected function checkMand($aCID) {
    if ($aCID != MID) {
      $lUrl = 'index.php?act=hom-mand';
      CCor_Msg::add('Your Login session has expired. Please login and retry.', mtUser, mlError);
      header('Location: '.$lUrl);
      exit;
    }
  }

  // deprecated, still here to avoid fatal errors when mand code is using this
  protected function checkComparison($aSrc, $aJobId) {
    return false;
  }

  protected function actStd() {
    $lVie = new CJob_List('job');
    $this -> render($lVie);

    $lVie = $this -> getFac() -> getList();
    $this -> render($lVie);
  }

  protected function afterInsert($aMod) {
    $this->afterPostUpload($aMod, 'insert');
  }

  protected function afterUpdate($aMod) {
    $this->afterPostUpload($aMod, 'update');
  }

  protected function getUploadRec($aName) {
    if (!isset($_FILES['val']['name'][$aName])) return false;
    if (0 !== $_FILES['val']['error'][$aName]) return false;

    $lRet = array();
    $lRet['name']     = $_FILES['val']['name'][$aName];
    $lRet['type']     = $_FILES['val']['type'][$aName];
    $lRet['tmp_name'] = $_FILES['val']['tmp_name'][$aName];
    $lRet['error']    = $_FILES['val']['error'][$aName];
    $lRet['size']     = $_FILES['val']['size'][$aName];
    return $lRet;
  }

  protected function afterPostUpload($aMod, $aPostType = 'update') {
    if (!isset($_FILES['val'])) return;
    $lFiles = $_FILES['val']['name'];
    $lFieldParams = CCor_Res::extract('alias', 'param', 'fie', array('typ' => 'file'));

    foreach ($lFiles as $lKey => $lDummy) {
      $lRec = $this->getUploadRec($lKey);
      if (!$lRec) continue;

      $lParam = (isset($lFieldParams[$lKey])) ? toArr($lFieldParams[$lKey]) : null;

      $lFunc = 'afterPostUploadField'.$lKey;
      if ($this->hasMethod($lFunc)) {
        $this->$lFunc($aMod, $lRec, $lParam, $aPostType);
      } else {
        $this->afterPostUploadGeneric($aMod, $lRec, $lParam, $aPostType);
      }
    }
  }

  protected function afterPostUploadGeneric($aMod, $aRec, $aParam, $aPostType) {
    $lSrc = $this->mSrc;
    $lJid = $aMod->getJobId();
    $lUpload = new CJob_Fil_Upload($lSrc, $lJid);
    $lUpload->uploadRec($aRec, $aParam);
  }

  ######################################################################################
  /*
   * Create Job from ProjektItem
  * Get ProjectItem Infos into Job
  * @param pid int|string ProjectId
  * @param sid int|string Project-ItemId
  * @param src string Job Type
  */
  protected function actSub() {
    $lProId = $this -> getInt('pid');
    $lSid = $this -> getInt('sid');
    $lSrc = $this -> getVal('src');
    $lIsMaster = $this -> getVal('ismaster');
    $lMasterId = $this -> getVal('masterid');

    $lQry = new CCor_Qry('SELECT * FROM al_job_pro_'.intval(MID).' WHERE id='.$lProId);
    $lJob = $lQry -> getDat();

    $lViewProjektJoblist = CCor_Cfg::get('view.projekt.joblist', TRUE);
    // !$lViewProjektJoblist: it works with ProjectItems (S+T View) - Copy Content from Subproject
    if (!$lViewProjektJoblist AND !empty($lSid)){
      $lQry -> query('SELECT * FROM al_job_sub_'.intval(MID).' WHERE id='.$lSid);
      $lSub = $lQry -> getDat();
      foreach ($lSub as $lKey => $lVal) {
        if (!empty($lVal)) {
          $lJob[$lKey] = $lVal;
        }
      }
      $lArt = $lSub['jobid_art'];
      if (!empty($lArt)) {//MOP2010: in grauen Vorzeiten konnte ein Rep aus einem Master(art) kopiert werden
        $lArtJob = new CJob_Art_Dat();
        $lArtJob -> load($lArt);
        foreach ($lArtJob as $lKey => $lVal) {
          if (!empty($lVal)) {
            $lJob[$lKey] = $lVal;
          }
        }
      }
    }
    $lClassTabs = 'CJob_'.$lSrc.'_Tabs';
    $lVie = new $lClassTabs(0);
    $lRet = $lVie -> getContent();

    $lJob['webstatus'] = 0; // avoid edit by status check
    $lClassForm = 'CJob_'.$lSrc.'_Form';
    $lFrm = new $lClassForm('job-'.$lSrc.'.ssub', 0, $lJob);
    $lFrm -> setParam('pid', $lProId);
    $lFrm -> setParam('sid', $lSid);
    $lFrm -> setParam('src', $lSrc);
    if ($lIsMaster != ''){
      $lFrm -> setParam('old[is_master]', $lIsMaster);
      $lFrm -> setParam('val[is_master]', $lIsMaster);
    }
    if ($lMasterId != ''){
      $lFrm -> setParam('old[master_id]', $lMasterId);
      $lFrm -> setParam('val[master_id]', $lMasterId);
    }
    $lFrm -> setParam('old[jobid_pro]', $lProId);
    $lFrm -> setParam('val[jobid_pro]', $lProId);
    if (!empty($lArt)) {
      $lFrm -> setParam('old[jobid_art]', $lArt);
      $lFrm -> setParam('val[jobid_art]', $lArt);
    }
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  /*
   * Create Job from ProjektItem
  * @param pid int|string ProjectId
  * @param sid int|string Project-ItemId
  * @param src string Job Type
  */
  protected function actSsub() {
    $lProId = $this -> getInt('pid');
    $lSid = $this -> getInt('sid');
    $lPag = $this -> getReq('page', 'job');
    $lSrc = $this -> mSrc;

    $lMod = $this->getFac()->getMod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lJobId = $lMod -> getInsertId();

      $lViewProjektJoblist = CCor_Cfg::get('view.projekt.joblist', TRUE);
      if ($lViewProjektJoblist){
        $lModSub = new CJob_Pro_Sub_Mod();
        $lModSub -> getPost($this -> mReq);
        $lModSub -> setVal('wiz_id', 3);
        $lModSub -> setVal('pro_id', $lProId);
        $lModSub -> setVal('jobid_'.$this -> mSrc, $lJobId);
        $lModSub -> setVal('src', $this -> mSrc);
        $lModSub -> setVal('webstatus', 10);
        //if ($lIsMaster != '') $lModSub -> setVal('is_master', 'X');
        //if ($lMasterId != '') $lModSub -> setVal('master_id', $lMasterId);
        $lModSub -> insert();
      } else {
        // !$lViewProjektJoblist: it works with ProjectItems (S+T View) - Copy Content from Subproject
        $lSql = 'UPDATE al_job_sub_'.intval(MID).' SET jobid_'.$lSrc.'="'.$lJobId.'" WHERE id='.$lSid;
        CCor_Qry::exec($lSql);
        $this->updateProjectItem($lJobId, $lMod->getValues());

        //22651 Project Critical Path Functionality
        $lMod -> insertIntoProjectStatusInfo($lJobId, $lProId, $lSid);
      }
      CJob_Pro_Mod::reportDraft($lProId, $this -> mSrc, $lJobId);

      $this -> redirect('index.php?act=job-'.$lSrc.'.edt&jobid='.$lJobId.'&page='.$lPag);
    }
    $this -> redirect('index.php?act=job-'.$lSrc.'.new');
  }

  ######################################################################################

  protected function actFpr() {
    $lVie = new CHtm_Fpr($this -> mMod.'.sfpr');
    $lDef = CCor_Res::get('fie');

    $lArr = array();
    foreach ($lDef as $lFie) {
      $lFla = intval($lFie['flags']);
      $lAva = intval($lFie['avail']);
      if (bitSet($lAva, $this -> mAva)) {
        if (bitSet($lFla, ffList)) {
          // If Jobfield has Read Flag active, ask for User READ-RIGHT (combination of 'fie_' and Alias).
          // If User has no READ-RIGHT, Jobfield not shown in the list.
          $lFieRight = 'fie_'.$lFie['alias'];
          #echo '<pre>---cnt.php---'.get_class().'---';var_dump($lFie['id'], $lFie['name_'.LAN],$lFla,bitset($lFla,ffRead) , $lFieRight,$this -> mUsr -> canRead($lFieRight),'#############');echo '</pre>';
          if (bitset($lFla,ffRead) && !$this -> mUsr -> canRead($lFieRight)){
            continue;
          }
          $lArr[$lFie['id']] = $lFie['name_'.LAN];
        }
      }
    }
    $lVie -> setSrc($lArr);
    $lUsr = CCor_Usr::getInstance();
    $lVie -> setSel($lUsr -> getPref($this -> mPrf.'.cols'));

    $lPag = CHtm_Page::getInstance();
    $lPag->addJs($lVie->getTooltips());
    $this -> render($lVie);
  }

  protected function actSfpr() {
    $lDst = $this -> mReq -> getVal('dst');
    $lUsr = CCor_Usr::getInstance();
    if (!empty($lDst)) {
      $lDstStr = implode(',', $lDst);
    } else {
      $lDstStr = '';
    }
    $lUsr -> setPref($this -> mPrf.'.cols', $lDstStr);
    $this -> redirect();
  }

  protected function actSpr() {
    $lVie = new CHtm_Fpr($this -> mMod.'.sspr');
    $lVie -> setTitle(lan('lib.opt.spr'));
    $lDef = CCor_Res::get('fie');

    $lArr = array();
    foreach ($lDef as $lFie) {
      $lFla = intval($lFie['flags']);
      $lAva = intval($lFie['avail']);
      if (bitSet($lAva, $this -> mAva)) {
        if (bitSet($lFla, ffSearch)) {
          // If Jobfield has Read Flag active, ask for User READ-RIGHT (combination of 'fie_' and Alias).
          // If User has no READ-RIGHT, Jobfield not shown in the list.
          $lFieRight = 'fie_'.$lFie['alias'];
          if (bitset($lFla,ffRead) && !$this -> mUsr -> canRead($lFieRight)){
            continue;
          }
          $lArr[$lFie['id']] = $lFie['name_'.LAN];
        }
      }
    }

    $lVie -> setSrc($lArr);
    $lUsr = CCor_Usr::getInstance();
    $lVie -> setSel($lUsr -> getPref($this -> mPrf.'.sfie'));
    $lPag = CHtm_Page::getInstance();
    $lPag->addJs($lVie->getTooltips());
    $this -> render($lVie);
  }

  protected function actSspr() {
    $lDst = $this -> mReq -> getVal('dst');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.sfie', implode(',', $lDst));
    $this -> redirect();
  }

  protected function actTogfil() {
    $lUsr = CCor_Usr::getInstance();
    $lOld = $lUsr -> getPref($this -> mMod.'.hidefil');
    $lNew = ($lOld == 1) ? 0 : 1;
    $lUsr -> setPref($this -> mMod.'.hidefil', $lNew);
    $this -> redirect();
  }

  protected function actTogser() {
    $lUsr = CCor_Usr::getInstance();
    $lOld = $lUsr -> getPref($this -> mMod.'.hideser');
    $lNew = ($lOld == 1) ? 0 : 1;
    $lUsr -> setPref($this -> mMod.'.hideser', $lNew);
    $this -> redirect();
  }

  protected function actSelview() {
    $lId = $this -> getInt('id');

    $lQry = new CCor_Qry('SELECT * FROM al_usr_view WHERE id='.$lId);
    if ($lRow = $lQry -> getDat()) {
      $lUsr = CCor_Usr::getInstance();
      $lUsr -> setPref($this -> mMod.'.cols', $lRow['cols']);
      $lUsr -> setPref($this -> mMod.'.lpp', $lRow['lpp']);
      $lUsr -> setPref($this -> mMod.'.ord', $lRow['ord']);
      $lUsr -> setPref($this -> mMod.'.sfie', $lRow['sfie']);
      $lUsr -> setPref($this -> mMod.'.page', 0);
    }
    $this -> redirect();
  }

  protected function actSelsearch() {
    $lId = $this -> getInt('id');

    $lQry = new CCor_Qry('SELECT ser FROM al_usr_search WHERE id='.$lId.' AND mand='.MID);
    if ($lRow = $lQry -> getDat()) {
      $lUsr = CCor_Usr::getInstance();
      $lUsr -> setPref($this -> mMod.'.ser', unserialize($lRow['ser']));
      $lUsr -> setPref($this -> mMod.'.page', 0);
    }
    $this -> redirect();
  }

  protected function actAllview() {
    $lUsr = CCor_Usr::getInstance();

    $lPrf = array();
    $lPrf['cols'] = $lUsr -> getPref($this -> mMod.'.cols');
    $lPrf['lpp']  = $lUsr -> getPref($this -> mMod.'.lpp');
    $lPrf['ord']  = $lUsr -> getPref($this -> mMod.'.ord');
    $lPrf['sfie'] = $lUsr -> getPref($this -> mMod.'.sfie');

    foreach ($lPrf as $lKey => $lVal) {
      if (empty($lVal)) unset($lPrf[$lKey]);
    }

    if (!empty($lPrf)) {
      $lQry = new CCor_Qry();
      foreach ($lPrf as $lKey => $lVal) {
        $lModKey = $this -> mMod.'.'.$lKey;
        $lSql = 'INSERT INTO al_sys_pref SET mand='.MID.',code='.esc($lModKey).',val='.esc($lVal).' ';
        $lSql.= 'ON DUPLICATE KEY UPDATE val='.esc($lVal).';';
        $lQry -> query($lSql);
      }
    }
    $this -> redirect();
  }

  protected function actDoassign() {
    $lSql = 'SELECT * FROM al_job_sub_'.intval(MID).'';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lPro = $lRow['pro_id'];
      $lArt = $lRow['jobid_art'];
      $lRep = $lRow['jobid_rep'];
      $lSec = $lRow['jobid_sec'];
      $lAdm = $lRow['jobid_adm'];
      $lMis = $lRow['jobid_mis'];
      $lCom = $lRow['jobid_com'];
      $lTra = $lRow['jobid_tra'];
      if (!empty($lArt)) {
        $lUpd = array();
        $lUpd['jobid_pro'] = $lPro;
        $lMod = new CJob_Art_Mod($lArt);
        $lMod -> forceUpdate($lUpd);
      }

      if (!empty($lRep)) {
        $lUpd = array();
        $lUpd['jobid_pro'] = $lPro;
        if (!empty($lArt)) {
          $lUpd['jobid_art'] = $lArt;
        }
        $lMod = new CJob_Art_Mod($lRep);
        $lMod -> forceUpdate($lUpd);
      }

    }
  }

  protected function actSetStatus() {
    $lJid = $this -> getReq('jobid');
    $lSrc = $this -> getReq('src');
    $lSta = $this -> getReq('sta');
    $lVal = $this -> getReq('val');
    $lSub = $lVal['subject'];
    $lMsg = $lVal['msg'];

    $lCls = 'CJob_'.$lSrc.'_Step';
    $lStep = new $lCls($lJid);
    $lStep -> doExceptionStep($lSta, $lSub, $lMsg);

    $this -> redirect('index.php?act=job-'.$lSrc.'.edt&jobid='.$lJid);
  }

  protected function actSetComment() {
    $lJid = $this -> getReq('jobid');
    $lSrc = $this -> getReq('src');
    $lSta = $this -> getReq('sta');

    $lFrm = new CHtm_Form('job-'.$lSrc.'.SetStatus', lan('lib.msg'), 'job-'.$lSrc.'.edt&jobid='.$lJid);
    $lFrm -> setAtt('style', 'width:700px');
    $lFrm -> setParam('val[subject]', lan('crp.chg.except'));
    $lFrm -> setParam('jobid', $lJid);
    $lFrm -> setParam('src', $lSrc);
    $lFrm -> setParam('sta', $lSta);
    $lFrm -> addDef(fie('msg', lan('lib.msg'), 'memo', NULL, array('style' => 'width:400px;', 'rows' => '18')));

    $this -> render($lFrm);
  }

  protected function makeAnnotationReport(&$aAdd, &$aApl, $aMsg, $aVal) {
    //$aAdd wird um "annotations" angereichert
    //$aAdd wird um "annotationsall" angereichert -> f. History
    //$aAdd wird um "files" angereichert

    $lCapAnnrepComment = CCor_Cfg::get('wec.annrep.comment', 'KOMMENTAR');
    $lCapAnnrepAnnotation = CCor_Cfg::get('wec.annrep.annotation', 'ANNOTATION');
    $lCapAnnrepNo = CCor_Cfg::get('wec.annrep.No', 'Nr.');
    $lLenAnnrepComment = strlen($lCapAnnrepComment);
    $lLenAnnrepAnnotation = strlen($lCapAnnrepAnnotation);
    $lLenAnnrepNo = strlen($lCapAnnrepNo);

    #echo '<pre>--makeAnnotationReport----'.get_class().'---';var_dump($_REQUEST,'#############');echo '</pre>';

    $lActUser = CCor_Usr::getAuthId();
    $lProtocolState = array();
    $lDat = new CCor_Datetime();
    $lDate = $lDat -> getFmt(lan('lib.datetime.short'));
    $lProtocolState['user'] = $lActUser;
    $lProtocolState['date'] = $lDate;

    $lUsrNamArr = CCor_Res::extract('id', 'fullname', 'usr');
    $lUsrArr = CCor_Res::extract('id', 'departm_fullname', 'usr');
    if (isset($lUsrArr[$lActUser])) {
      $lProtocolState['username'] = $lUsrArr[$lActUser];
    }

    $lLine  = '______________________________________________________________________'.LF.LF;
    $lHypen = '----------------------------------------------------------------------'.LF;

    $lUsrInApl = FALSE;
    $lUsrList = array();
    //----------------------------------------------------------------START: mit activexfdf  // wird in CInc_Job_Apl_Page_Annotations::getHiddenElements() gesetzt
    if ($this -> getInt('activexfdf') == 1) {

      $lPrjMaster = $this -> getReq('prjmaster');
      $lOld = $this -> getReq('old');

      $lcAnn = $this -> getInt('cntannotation');//count Annotations
      $lcMsg = $this -> getInt('cntusermsg');   //count Messages

      #$ltxt4eMails = '';        // APL -> Aenderungsanforderung: Anzeige als {annotations} in der eMail
      $ltxt4eMailsChecked = ''; // APL -> Aenderungsanforderung: Anzeige als {annotations} in der eMail, aber nur die mit [X]
      $lAlltxt4His = ''; // Speicherung i. History AND spaeter in stepDialog: 'SELECT msg FROM al_job_his WHERE...' und Anzeige bei 'BackToProduction' im Kommentar

      for ($iUsrIdx = 1; $iUsrIdx<=$lcMsg; $iUsrIdx++) {
        if (isset($lOld['msg.'.$iUsrIdx])) {
          $leMailtxtHdl = '';
          $leMailtxtAPL = '';
          $leMailtxtANN = '';

          // die Kommentare aus dem APL
          $lUser = array();  // enthaelt nur [X]-Infos, spaeter auch die Annotationen
          $lUserALLInfo = array();  // enthaelt spaeter auch die Annotationen
          $lUid = $lOld['msg.'.$iUsrIdx];
          $lUserALLInfo['uid'] = $lUid;

          $lCap = '';
          $lcom = '';
          if (!isset($lOld['msg.empty.'.$iUsrIdx])) {
            if (empty($aVal['msg.state.'.$iUsrIdx])) { // User hat im APL nicht committed
              $lState = '----';
            } else {
              $lState = $aVal['msg.state.'.$iUsrIdx]; // "Freigabe", "Bedingte Freigabe", "Korrektur"
            }

            $lAlltxt4His .= $lLine;
            #$ltxt4eMails .= $lLine;
            $leMailtxtHdl .= $lLine;
            //______________________________________________________________________
            //'APL-Commit', 'dd.mm.YYYY HH:mm', '(Abteilung) Nachname, Vorname'
            $lx = $lState .', '. $aVal['msg.date.'.$iUsrIdx].', '. $aVal['msg.usr.'.$iUsrIdx].LF;
            $lAlltxt4His.= $lx;
            #$ltxt4eMails.= $lx;
            $leMailtxtHdl.= $lx;

            $lUserALLInfo['state'] = $lState;
            $lUserALLInfo['name'] = $aVal['msg.usr.'.$iUsrIdx];

            $lMsgTag = 'com';
            $lChanged = False;
            if (!empty($aVal['msg.btn.'.$iUsrIdx])) {
              if ($aVal['msg.btn.'.$iUsrIdx] == 1) {
                $lMsgTag = 'edit';
                $lChanged = True;
              }
            }
            if ($lActUser == $lUid AND !empty($aMsg)) { // Statuswechsler ebenfalls im APL
              $lUsrInApl = TRUE;
              $lcom = $aMsg;
              $lComXd = '[X]'; // der gerade eingegebene Kommentar
              $lDat = new CCor_Datetime();
              $lDate = $lDat -> getFmt(lan('lib.datetime.short'));
            } elseif (isset($aVal['msg.'.$iUsrIdx])) { // checked
              $lcom = $aVal['msg.'.$lMsgTag.'.'.$iUsrIdx]; // die von den anderen eingegebenen Kommentare bzgl. Freigabe/Abgelehnt
              $lComXd = '[X]';
              $lDate = $aVal['msg.date.'.$iUsrIdx];
            } elseif (!empty($aVal['msg.'.$lMsgTag.'.'.$iUsrIdx])) { // unchecked
              $lcom = $aVal['msg.'.$lMsgTag.'.'.$iUsrIdx]; // die von den anderen eingegebenen Kommentare bzgl. Freigabe/Abgelehnt
              $lComXd = '[]';
              $lDate = $aVal['msg.date.'.$iUsrIdx];
            } else {
              $lcom = ''; // die von den anderen "nicht eingegebenen Kommentare" bzgl. Freigabe/Abgelehnt
              $lComXd = '[ ]';
              $lDate = $aVal['msg.date.'.$iUsrIdx];
            }
          }//end_if (!isset($lOld['msg.empty.'.$iUsrIdx]))

          $lUser = $lUserALLInfo;

          if (!empty($lcom)) {
            $lUserALLInfo['comment.text']    = $lcom;
            $lUserALLInfo['comment.status']  = $lComXd;
            $lUserALLInfo['comment.date']    = $lDate;
            $lUserALLInfo['comment.changed'] = $lChanged;

            $lcount = 1;
            while ($lcount>0) {
              $lcom = str_replace(LF.' ',LF, $lcom, $lcount);
            }
            $lx = LF.$lCapAnnrepComment.LF;
            $lx.= str_repeat('-', $lLenAnnrepComment).LF;

            //KOMMENTAR
            //---------
            //User comment in APL approve
            #$ltxt4eMails.= $lx;
            #$ltxt4eMails.= $lcom.LF;
            $leMailtxtAPL.= $lx;
            $leMailtxtAPL.= $lcom.LF;

            $lcom = str_replace(LF,LF.str_repeat(' ', $lLenAnnrepNo+1), $lcom, $lcount);
            $lAlltxt4His.=  $lx;
            $lAlltxt4His.= $lComXd.' '.$lcom.LF;

            $lCap = LF;
          } //end_if (!empty($lcom))

          // die Annotationen aus WEC
          $lCap.= $lCapAnnrepAnnotation.LF;
          $lCap.= str_repeat('-', $lLenAnnrepAnnotation).LF;
          $lAllCap = '';

          $lAnnots = array();
          $lExistAnno4Usr = false;
          for ($j = 1; $j <= $lcAnn; $j++) {
            if ($aVal['annots.usr.'.$j] == $lUid) {

              if (!empty($lAllCap)) {
                $lAllCap = LF;
                $lAllCap.= $lCapAnnrepAnnotation.LF;
                $lAllCap.= str_repeat('-', $lLenAnnrepAnnotation).LF;
                //ANNOTATION
                //----------
                $lAlltxt4His.= $lAllCap;
              }

              if (isset($aVal['annots.'.$j])) {
                $lExistAnno4Usr = true;
                $lAnnXd = '[X]';
              } else {
                $lAnnXd = '[ ]';
              }
              $lx = $lCapAnnrepNo.' '.$aVal['annots.nr.'.$j].':';  // $lLenAnnrepNo+1 + (2) + 2
              $lxp = str_repeat('-', strlen($lx));
              $lx = str_pad($lx, $lLenAnnrepComment,' ');
              $lxp = str_pad($lxp, $lLenAnnrepComment,' ');

              $lAnnTag = 'annots.com.';
              $lChanged = False;
              if (!empty($aVal['ann.btn.'.$j])) {
                if ($aVal['ann.btn.'.$j] == 1) {
                  $lAnnTag = 'ann.edit.';
                  $lChanged = True;
                }
              }

              $lannot = $aVal[$lAnnTag.$j];
              $lcount = 1;
              while ($lcount>0) {
                $lannot = str_replace(LF.' ',LF, $lannot, $lcount);
              }

              // wird in add_data d. apl_loop gespeichert und auf d. APLpages angezeigt
              $lTheAnnot = array();
              $lTheAnnot['text'] = $lannot;
              $lTheAnnot['page'] = $aVal['annots.page.'.$j];
              $lTheAnnot['nr'] = $aVal['annots.nr.'.$j];
              $lTheAnnot['date'] = $aVal['annots.date.'.$j];
              $lTheAnnot['status'] = $lAnnXd;
              $lTheAnnot['changed'] = $lChanged;
              $lAnnots[] = $lTheAnnot;

              $lannot = str_replace(LF,LF.str_repeat(' ', $lLenAnnrepComment), $lannot, $lcount);
              $lannota = str_replace(LF,LF.str_repeat(' ', $lLenAnnrepNo+1), $lannot, $lcount);

              $lPage = (0 < $aVal['annots.page.'.$j] ? ' ('.$aVal['annots.page.'.$j].')' : '');

              $lAlltxt4His.= $lAnnXd.' '.$lx;
              $lAlltxt4His.= $aVal['annots.date.'.$j].$lPage.LF;
              $lAlltxt4His.= str_repeat('-', $lLenAnnrepNo+1).$lxp;
              $lAlltxt4His.= $lannota.LF;
              $lAlltxt4His.= $lHypen;

              if ($lAnnXd == '[X]') {
                if ($lCap != '') { // nur einmal anzeigen, muss aber vor d. for-Schleife gefuellt werden!
                  #$ltxt4eMails.= $lCap;
                  $leMailtxtANN.= $lCap;
                  $lCap = '';
                }
                //ANNOTATION
                //----------
                //User annotation in WEC

                $leMailtxtANN.= $lx;
                $leMailtxtANN.= $aVal['annots.date.'.$j].$lPage.LF;
                $leMailtxtANN.= $lxp;
                $leMailtxtANN.= $lannot.LF;
                $leMailtxtANN.= $lHypen;
                //---------------------------------------------------//Abschluss nach jeder Annotation

              }
            }//end_if ($aVal['annots.usr.'.$j] == $lUid)
          }//end_for ($j = 1; $j <= $lcAnn; $j++)

          if (isset($aVal['msg.'.$iUsrIdx]) OR $lUsrInApl) { // Zeige nur die mit [X] gekennzeichneten Kommentare und nur die mit [X] Annos
            $ltxt4eMailsChecked .= $leMailtxtHdl;
            $ltxt4eMailsChecked .= $leMailtxtAPL;
            if ($lExistAnno4Usr) {
              $ltxt4eMailsChecked .= $leMailtxtANN;
            }
            $lUser = $lUserALLInfo;
            $lUser['annotations'] = $lAnnots; // wird in add_data d. apl_loop gespeichert und auf d. APLpages angezeigt
            $lUsrList[$lUid] = $lUser;

          } elseif ($lExistAnno4Usr) { // Zeige nur die mir [X] gekennzeichneten Annotationen
            $ltxt4eMailsChecked .= $leMailtxtHdl;
            $ltxt4eMailsChecked .= $leMailtxtANN;
            $lUser['annotations'] = $lAnnots; // wird in add_data d. apl_loop gespeichert und auf d. APLpages angezeigt
            $lUsrList[$lUid] = $lUser;
          }
          #echo '<pre>-33-makeAnnotationReport----'.get_class().'---';var_dump($lAnnots,$lUser,'#############');echo '</pre>';
        }
      }//end_for ($iUsrIdx = 1; $iUsrIdx<=$lcMsg; $iUsrIdx++)

      if ($ltxt4eMailsChecked != '') { // APL -> Aenderungsanforderung: Anzeige als {annotations} in der eMail, aber nur mir [X]
        $aAdd['annotations'] = $ltxt4eMailsChecked;
      } else {
        $aAdd['annotations'] = ''; // {annotations} sollte ersetzt werden koennen
      }
      if ($lAlltxt4His != '') { // Speicherung i. History
        $aAdd['annotationsall'] = $lAlltxt4His;
      }

    }//end_if ($this -> getInt('activexfdf') == 1)
    //----------------------------------------------------------------ENDE: mit activexfdf

    #echo '<pre>-444-makeAnnotationReport----'.get_class().'---$ltxt4eMailsChecked';print_r($ltxt4eMailsChecked);echo BR.'#############$lAlltxt4His'.BR;print_r($lAlltxt4His);var_dump( '#############');print_r($aAdd);echo '</pre>';

    if (isset($lUsrList[$lActUser])) {
      $lUser = $lUsrList[$lActUser];
    } else {
      $lUser = array();
      $lUser['uid'] = $lActUser;
      if (!empty($aMsg)) {
        $lUser['comment.text'] = $aMsg;
        $lUser['comment.status'] = '[X]';
      }
      $lUser['annotations'] = array();
      if (isset($lUsrArr[$lActUser])) {
        $lUser['name'] = $lUsrArr[$lActUser];
      } else {
        if (isset($lUsrNamArr[$lActUser])) {
          $lUser['name'] = $lUsrNamArr[$lActUser];
        }
        $lUsrList[$lActUser] = $lUser;
      }
    }

    //Start: auch der gerade eingegebene Kommentar muss mit in die eMail
    if (!empty($aMsg) AND isset($aAdd['annotations']) AND !$lUsrInApl) {
      //Zusammenbau der Anzeige v. $aMsg f. Historie und eMail, kommt automatisch in APL
      $lcom = $aMsg; // der gerade eingegebene Kommentar
      $lDat = new CCor_Datetime();
      $lDate = $lDat -> getFmt(lan('lib.datetime.short'));
      $lx = lan('apl.finalcomment').', '. $lDate.', ';
      if (isset($lUsrNamArr[$lActUser])) {
        $lx.= $lUsrNamArr[$lActUser];
      }
      $lx.= LF;

      $lcount = 1;
      while ($lcount > 0) {
        $lcom = str_replace(LF.' ',LF, $lcom, $lcount);
      }
      $lx.= LF.$lCapAnnrepComment.LF;
      $lx.= str_repeat('-', $lLenAnnrepComment).LF;

      $lAddMailaMsg = LF.$lLine;
      //______________________________________________________________________
      //Final, 'dd.mm.YYYY HH:mm', '(Abteilung) Nachname, Vorname'
      //KOMMENTAR
      //---------
      //der Kommentar aus $aMsg
      $lAddMailaMsg.= $lx;
      $lAddMailaMsg.= $lcom.LF;

      $aAdd['annotations'].= $lAddMailaMsg;
    }
    //Stop: auch der gerade eingegebene Kommentar muss mit in die eMail
    #echo '<pre>-2-makeAnnotationReport----'.get_class().'---f. eMails'.LF;print_r($aAdd['annotations']);echo LF.LF.'#######*******************************************************###### f. Historie'.LF.LF;print_r($aAdd['annotationsall']);echo '</pre>';

    # *****************************************************************************************************
    # echo '<pre>'.$ltxt4eMails.'</pre>';
    # *****************************************************************************************************

    //----------------------------------------------------------------START: DATEIEN
    // --------------------------------- Dateien des akt. Benutzers ---------------------------
    $lArrFil = array();
    $lArrFsL = array();
    $lFiles = $this -> getReq('listuserfiles');
    if (!empty($lFiles)) {
      $lArr = explode("\r", $lFiles);
      foreach ($lArr as $lfi) {
        if (trim($lfi) != '') {
          $lArrFil[] = trim($lfi);
          $lax = array();
          $lax['name'] = trim($lfi);
          $lax['status'] = '[X]';
          $lArrFsL[] = $lax;
        }
      }
    }
    $lUser['files'] = $lArrFsL;
    $lUsrList[$lActUser] = $lUser;
    // $lFiles = str_replace("\r", "\n", $lFiles);

    // --------------------------------- Dateien anderer Benutzer
    $lcFil = 0 + $this -> getInt('cntuserfile');
    $lcMsg = 0 + $this -> getInt('cntusermsg');
    for ($u = 1; $u<=$lcMsg; $u++) {
      if (isset($lOld['msg.'.$u])) {
        $lUid = $lOld['msg.'.$u];
        if ($lActUser == $lUid) continue;
        $lArrFsL = array();
        # *****************************************************************************************************
        # echo '<pre>UID:'.$lUid.'</pre>';
        # *****************************************************************************************************
        for ($i = 1; $i <= $lcFil; $i++) {
          if (isset($lOld['fil.u'.$lUid.'.n'.$i])) {
            $lax = array();
            $lfi = $lOld['fil.u'.$lUid.'.n'.$i];
            $lax['name'] = trim($lfi);
            if (isset($aVal['fil.u'.$lUid.'.n'.$i])) {
              // -> fuer getCurrentUserComment($aUid)
              $lfi = $aVal['fil.u'.$lUid.'.n'.$i];
              if (trim($lfi) != '') {
                $lArrFil[] = trim($lfi);
              }
              # *****************************************************************************************************
              # echo '<pre>'.$lfi.'</pre>';
              # *****************************************************************************************************
              if (empty($lFiles)) {
                $lFiles = $lfi;
              } else {
                $lFiles = $lFiles."\n".$lfi;
              }
              $lax['status'] = '[X]';
              # *****************************************************************************************************
              # echo '<pre>'.$lfi.'</pre>';
              # *****************************************************************************************************
            } else {
              $lax['status'] = '[ ]';
            }
            $lArrFsL[] = $lax;
          }
        }

        if (isset($lUsrList[$lUid])) {
          $lUser = $lUsrList[$lUid];
          $lUser['files'] = $lArrFsL;
          $lUsrList[$lUid] = $lUser;
        }

      }//end_if (isset($lOld['msg.'.$iUsrIdx]))
    }//end_for ($u = 1; $u<=$lcMsg; $u++)
    //----------------------------------------------------------------ENDE: DATEIEN
    #echo '<pre>-55-makeAnnotationReport----'.get_class().'---';var_dump($lUsrList,'#############');echo '</pre>';
    $lProtocolState['list'] = $lUsrList;
    #exit;
    $lAddData = $aApl -> getAddData();
    if (!is_array($lAddData)) $lAddData = array();
    $lAddData['protocol'] = $lProtocolState;
    $aApl -> setAddData($lAddData);     // wird in add_data d. apl_loop gespeichert und auf d. APLpages angezeigt

    if (!empty($lArrFil)) $aAdd['files'] = $lArrFil;

  }

  protected function actCpy() {
    $lRet = '';
    // Field List from Config (job.cpy.set-empty) and Copy Flag OFF and in Target Jobart NOT avaiable.
    $lFieldListSetEmpty = Array();
    // Field List with CopyFrom Feature. If they have no Copy Flag, they shouldn't empty.
    $lFieldListCopyFrom = Array();

    $lProId = $this -> getInt('pid'); // Project ID, not yet used, for further improvement
    $lItmId = $this -> getInt('itmid'); // Item ID, not yet used, for further improvement
    $lJobId = $this -> getReq('jobid'); // Job ID
    $lSrc = $this -> getReq('src'); // Source - aus welchem Jobtyp wird kopiert
    $lTarget = $this -> getReq('target'); // Source - aus welchem Jobtyp wird kopiert
    $lAssignedProId = $this -> getReq('proid');
    $lCopyTask = $this -> getInt('copytask');

    // If Copy from Arc, the vaiable $lArc is defined.
    $lArc = $this -> getReq('arc'); // Archive

    // if copy not direct from Arc but from Copy event then Check if the Job in Archive
    if (!isset($lArc)){
      $lSql = 'SELECT COUNT(*) FROM al_job_arc_'.MID.' WHERE jobid='.esc($lJobId);
      $lCnt = CCor_Qry::getInt($lSql);
      if (0 < $lCnt) {
        $lArc = TRUE;
      }
    }

    //Wenn ein Archive-Job kopiert werden, hole die Daten aus Archive-Tabelle.
    if (isset($lArc)) {
      $lJob = new CArc_Dat($lSrc);
      $lJob -> load($lJobId);
      $this -> dbg('Job from Archive');
    } else {
      $lObj = 'CJob_'.$lSrc.'_Dat';
      $lJob = new $lObj();
      $lJob -> load($lJobId);
      $this -> dbg('Job from Networker');
    }
    $lWecPrjId = $lJob -> __get('wec_prj_id');

    $lFieldListSetEmpty = CCor_Cfg::get('job.cpy.set-empty', array());

    $lCopyTaskFields = array();
    if (1 == $lCopyTask) {
      $lCopyTaskFields = CCor_Cfg::get('job-pro.fields.copytask');
    }

    $lDefFie = CCor_Res::get('fie');
    foreach ($lDefFie as $lFie) {
      $lAli = $lFie['alias'];
      #$lFeature = toArr($lFie['feature']); ////wird jetzt in cor/res/fie erledigt!
      // Dieses Feld gibt an, ob ein Alias den Wert eines anderen Alias beim Kopieren uebernimmt.
      if (isset($lFie['CopyFrom'])) {
        $lFieldListCopyFrom[] = $lAli;
        $lJob[$lAli] = $lJob[$lFie['CopyFrom']];
        //$lJob[ $lFeature['CopyFrom'] ] = '';  // "Vorlage" loeschen
      }

      $lFla = intval($lFie['flags']);
      $lAva = intval($lFie['avail']);

      if (in_array($lAli, $lCopyTaskFields)) continue;

      if (!bitSet($lAva, $this -> mAva) OR !bitSet($lFla, ffCopy)) {
        $lFieldListSetEmpty[] = $lAli;
      }
    }

    // Empty Job Fields
    foreach ($lFieldListSetEmpty as $lFie) {
      // Field List with CopyFrom Feauture. If they have no Copy Flag, they shouldn't empty.
      if (!in_array($lFie,$lFieldListCopyFrom)){
        $lJob[$lFie] = '';
      }
    }

    $lClassTabs = 'CJob_'.$this -> mSrc.'_Tabs';
    $lClassForm = 'CJob_'.$this -> mSrc.'_Form';

    $lVie = new $lClassTabs();

    $lRet.= $lVie -> getContent();

    $lFrm = new $lClassForm('job-'.$this -> mSrc.'.snew', 0, $lJob);
    if (!is_null($lAssignedProId)) { // Add assigned projectId of reference Job.
      // Check if Project Item of Reference Job has Target JobId.
      // Wenn Projek-Item fuer ziel Jobtyp platz hat, soll der Job zu dieser Item geordnet werden.
      $lCol= 'jobid_'.$lSrc;
      $lColTarget= 'jobid_'.$lTarget;
      $lSql= 'SELECT id FROM al_job_sub_'.MID;
      $lSql.= ' WHERE pro_id='.esc($lAssignedProId).' AND '.$lCol.'='.esc($lJobId).' AND '.$lColTarget.' =""';
      $lQry = new CCor_Qry($lSql);
      if ($lRow = $lQry -> getAssoc()) {
        $lProItemId = $lRow['id'];
        $lFrm->setParam('AssignedProItemId',$lProItemId);
      }
      $lFrm->setParam('AssignedProId',$lAssignedProId);
    }
    $lFrm->setParam('orig_src', $lSrc);
    $lFrm->setParam('orig_jobid', $lJobId);
    $lFrm->setParam('orig_wecprjid', $lWecPrjId);

    if (CCor_Cfg::get('portal-jobid-field')) {
      $lPortalJobId = $this -> getPortalJobId($lJobId, $lSrc, $lTarget);
      $lFrm -> setParam('val[portal_jobid]', $lPortalJobId);
    }

    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actDel() {
    $lJobId = $this -> getReq('id');
    $lSrc = $this->getReq('src', $this->mSrc); // passed as src in job-all, $this->mSrc otherwise

    $lFac = new CJob_Fac($lSrc, $lJobId);
    $lMod = $lFac -> getMod($lJobId);
    $lMod -> deleteFromProjectStatusInfo($lJobId);

    $lUsr = CCor_Usr::getInstance();

    $lSub = "Job Deleted";
    $lMsg = "Job was Deleted by ". $lUsr->getFullName() . " (User ID: ". $lUsr->getId().")";
    $lMod -> addHistory(htStatus, $lSub, $lMsg);

    if ($lMod->forceUpdate(array('webstatus' => 0))) {
      $this -> msg('Job '.$lJobId.' deleted', mtAdmin, mlInfo);
    }

    $lSql = 'UPDATE al_job_shadow_'.MID.' SET `webstatus`=0 WHERE `jobid`='.esc($lJobId).' LIMIT 1;';
    CCor_Qry::exec($lSql);
    if (CCor_Cfg::get('extended.reporting')) {
      $lSql = 'UPDATE al_job_shadow_'.MID.'_report SET `webstatus`=0 WHERE `jobid`='.esc($lJobId).' LIMIT 1;';
      CCor_Qry::exec($lSql);
    }



    //22651 Project Critical Path Functionality
    $this -> JobDelete($lJobId, $lSrc);
    $this -> redirect();
  }

  /**
   * 22651 Project Critical Path Functionality
   * Take out the deleted job from al_job_sub
   * @param $aJobId string
   * @param $aSrc string . It is only setted by all-jobs
   * @return void
   */
  protected function JobDelete($aJobId, $aSrc = null) {
    $lSrc = is_null($aSrc) ? $this -> mSrc : $aSrc;
    $lFac = new CJob_Fac($lSrc,$aJobId);
    $lMod = $lFac -> getMod($aJobId);
    $lMod -> deleteFromProjectStatusInfo($aJobId);

    $lApl = new CApp_Apl_Loop($lSrc, $aJobId);
    $lApl->closeLoops();

    $lFolders = new CJob_Fil_Folders($lSrc, $aJobId);
    if ($lFolders->has('dalim')) {
      $lHelper = new CApi_Dalim_Files($lSrc, $aJobId);
      $lHelper->removeAllFiles();
    }

    $lSql = 'UPDATE al_job_sub_'.MID.' SET jobid_'.$lSrc.'="" WHERE jobid_'.$lSrc.'='.esc($aJobId);
    CCor_Qry::exec($lSql);
  }

  protected function actCsvexp() {
    $lUsr = CCor_Usr::getInstance(); // needed fuer user id and preferences
    $lAge = $this -> getReq('age'); // either >job< or >arc<
    $lSrc = $this -> getReq('src'); // job type: art, rep, etc.

    if (CCor_Cfg::get('csv-exp.bymail', true)) {
      $lFil = base64_encode(serialize($lUsr -> getPref($lAge.'-'.$lSrc.'.fil')));
      $lSer = base64_encode(serialize($lUsr -> getPref($lAge.'-'.$lSrc.'.ser')));

      $lQueue = new CApp_Queue('createcsv');
      $lQueue -> setParam('uid', $lUsr -> getId());
      $lQueue -> setParam('mid', MID);
      $lQueue -> setParam('age', $lAge);
      $lQueue -> setParam('src', $lSrc);
      $lQueue -> setParam('fil', $lFil);
      $lQueue -> setParam('ser', $lSer);
      $lQueue -> insert();

      $this -> redirect();
    } else {
      // Columns
      $lCols = $lUsr -> getPref($lAge.'-'.$lSrc.'.cols');
      if (empty($lCols)) {
        CCor_Msg::add('No columns to show specified', mtUser, mlError);
        $this -> redirect();
      }

      // Filename
      $lMandArray = CCor_Res::extract('code', 'name_'.LAN, 'mand');
      $lMandName = str_replace(' ', '_', $lMandArray[MAND]);

      $lFileName = lan($lAge.'-'.$lSrc.'.menu');
      $lFileName.= '_';
      $lFileName.= $lMandName;
      $lFileName.= '_';
      $lFileName.= date('Ymd_H-i-s');
      $lFileName.= '.csv';

      // File
      header('Content-type: text/csv');
      header('Content-Disposition: attachment; filename="'.$lFileName.'"');
      flush();

      // Content
      $lClass_List = 'C'.ucfirst($lAge).'_'.ucfirst($lSrc).'_List';
      $lWithoutLimit = true;
      $lJobList = new $lClass_List($lWithoutLimit);

      $lIdField = $lJobList -> mIdField; // it's either jobid, jobnr or id
      $lJobList -> mIte = $lJobList -> mIte -> getArray($lIdField);

      $lJobList -> loadFlags();
      $lRet = $lJobList -> getCsvContent();
    }
  }

  protected function actXlsexp() {
    $lUsr = CCor_Usr::getInstance(); // needed fuer user id and preferences
    $lAge = $this -> getReq('age'); // needed to differ between job and arc
    $lSrc = $this -> getReq('src'); // needed for job type

    if (CCor_Cfg::get('csv-exp.bymail', true)) {
      $lFil = base64_encode(serialize($lUsr -> getPref($lAge.'-'.$lSrc.'.fil')));
      $lSer = base64_encode(serialize($lUsr -> getPref($lAge.'-'.$lSrc.'.ser')));

      $lQueue = new CApp_Queue('createxls');
      $lQueue -> setParam('uid', $this -> mUsr -> getId());
      $lQueue -> setParam('mid', MID);
      $lQueue -> setParam('age', $lAge);
      $lQueue -> setParam('src', $lSrc);
      $lQueue -> setParam('fil', $lFil);
      $lQueue -> setParam('ser', $lSer);
      $lQueue -> insert();

      $this -> redirect();
    } else {
      // Filename
      $lMandArray = CCor_Res::extract('code', 'name_'.LAN, 'mand');
      $lMandName = str_replace(' ', '_', $lMandArray[MAND]);

      $lFileName = lan($lAge.'-'.$lSrc.'.menu');
      $lFileName.= '_';
      $lFileName.= $lMandName;
      $lFileName.= '_';
      $lFileName.= date('Ymd_H-i-s');
      $lFileName.= '.xls';

      // Content
      $lClass_List = 'C'.ucfirst($lAge).'_'.ucfirst($lSrc).'_List';
      $lWithoutLimit = true;
      $lJobList = new $lClass_List($lWithoutLimit);

      $lIdField = $lJobList -> mIdField; // it's either jobid, jobnr or id
      $lJobList -> mIte = $lJobList -> mIte -> getArray($lIdField);

      $lJobList -> loadFlags();
      $lJobList -> loadApl();
      $lXls = $lJobList -> getExcel();
      $lXls -> downloadAs($lFileName);
    }
  }

  protected function actRepexp() {
  	$lUsr = CCor_Usr::getInstance(); // needed fuer user id and preferences
  	$lAge = $this -> getReq('age'); // needed to differ between job and arc
  	$lSrc = $this -> getReq('src'); // needed for job type

  	if (CCor_Cfg::get('rep-exp.bymail', true)) {
  		$lFil = base64_encode(serialize($lUsr -> getPref($lAge.'-'.$lSrc.'.fil')));
  		$lSer = base64_encode(serialize($lUsr -> getPref($lAge.'-'.$lSrc.'.ser')));

  		$lQueue = new CApp_Queue('createrep');
  		$lQueue -> setParam('uid', $lUsr -> getId());
  		$lQueue -> setParam('mid', MID);
  		$lQueue -> setParam('age', $lAge);
  		$lQueue -> setParam('src', $lSrc);
  		$lQueue -> setParam('fil', $lFil);
  		$lQueue -> setParam('ser', $lSer);
  		$lQueue -> insert();

  		$this -> redirect();
  	} else {
  		// Filename
  		$lMandArray = CCor_Res::extract('code', 'name_'.LAN, 'mand');
  		$lMandName = str_replace(' ', '_', $lMandArray[MAND]);

  		$lFileName = lan($lAge.'-'.$lSrc.'.menu');
  		$lFileName.= '_';
  		$lFileName.= $lMandName;
  		$lFileName.= '_';
  		$lFileName.= date('Ymd_H-i-s');
  		$lFileName.= '.csv';

  		// File
  		header('Content-type: text/csv');
  		header('Content-Disposition: attachment; filename="'.$lFileName.'"');
  		flush();

  		// Content
  		$lClass_List = 'C'.ucfirst($lAge).'_'.ucfirst($lSrc).'_List';
  		$lWithoutLimit = true;
  		$lJobList = new $lClass_List($lWithoutLimit);

  		$lIdField = $lJobList -> mIdField; // it's either jobid, jobnr or id
  		$lJobList -> mIte = $lJobList -> mIte -> getArray($lIdField);

  		#$lJobList -> loadFlags();
  		$lRet = $lJobList -> getRepContent();
  	}
  }

  protected function actFlag() {
    $lUid = $this -> mUsr -> getId();
    $lJobId = $this -> getReq('jobid');
    $lVote = $this -> getInt('vote');
    $lFlag = $this -> getInt('flag');

    $lAllFlags = CCor_Res::get('fla');
    $lShowFlags = CApp_Apl_Loop::showFlagButtons($this -> mSrcCnt, $lJobId, $lAllFlags);
    if (isset($lAllFlags[$lFlag]) AND isset($lShowFlags[$lFlag])) {
      $lShow = $lShowFlags[$lFlag];

      $lClass = 'CJob_'.$this -> mSrcCnt.'_Dat';
      $lJob = new $lClass();
      $lJob -> load($lJobId);

      $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
      $lSrc = $lJob -> getSrc();
      $this -> mCrpId = $lCrp[$lSrc];

      if (($lShow !== '-' AND $lShow) OR //[TYP => $lShow muss im Vgl. zuerst stehen] Eingeladen und an der Reihe ODER
          ($lShow === '-' AND $this -> mUsr -> canConfirmFlag($lFlag, $this -> mCrpId, $lJob)) // kein Eintrag in DB und habe das Recht
      ) {
        $lClass = 'CJob_'.$this -> mSrcCnt.'_Header';
        $lHdr = new $lClass($lJob);
        $lRet = $lHdr -> getContent();

        $lFlagEve = $lAllFlags[$lFlag];
        $lCap = $lFlagEve['name_'.LAN];
        $lImg = 'img/flag/';
        switch($lVote) {
          case FLAG_STATE_AMENDMENT :
            $lCap.= ': '.$lFlagEve['amend_'.LAN];
            $lImg.= $lFlagEve['amend_ico'];
            BREAK;
          case FLAG_STATE_CONDITIONAL :
            $lCap.= ': '.$lFlagEve['condit_'.LAN];
            $lImg.= $lFlagEve['condit_ico'];
            BREAK;
          case FLAG_STATE_APPROVED :
            $lCap.= ': '.$lFlagEve['approv_'.LAN];
            $lImg.= $lFlagEve['approv_ico'];
            BREAK;
        }
        $lPage = FALSE;
        $lImg.= '.gif';
        $lApl = new CApp_Apl_Loop($this -> mSrc, $lJobId, $lFlag, MID, $lJob['webstatus']);
        $lDlg = new CJob_Flag_Dialog($this -> mSrc, $lJobId, $lCap, $lVote, $lFlag, $lApl, $lImg);
        $lMsg = $lApl -> getCurrentUserComment($lUid);
        $lFillIn = '';

        $lAplUsrList = $lApl -> getAplUserlist();
        $lUsrArr = CCor_Res::extract('id', 'fullname', 'usr');
        // Name der Backup-person, wenn sie als Vertretung auftritt.
        // Wenn die Vertretung diesen Kommentar nicht entfernen darf, muss er unter actsapl eingebaut werden.
        if (isset($lAplUsrList[$lUid])) {
          $lRow = $lAplUsrList[$lUid];
          $lNam = explode(') ',$lRow['name']);//will nicht nach '(...)' suchen...
          if (isset($lNam[1])) {
            $lName = $lNam[1];
          } else {
            $lName = $lNam[0];
          }
          if (isset($lUsrArr[$lRow['uid']]) AND $lName != $lUsrArr[$lRow['uid']]) {
            $lFillIn = '('.lan('FillIn:').' '.$lUsrArr[$lRow['uid']].') ';
          }
          $lMsg = $lFillIn.$lMsg;
        }
        $lDlg -> setVal('msg', trim($lMsg));
        $lDlg -> setParam('webstatus', $lJob['webstatus']);

        $lDlg -> setUsers($lAplUsrList);
        $lRet.= $lDlg -> getContent();

        $this -> render($lRet);
      } elseif (('-' != $lShow AND !$lShow)) {
        $this -> dbg(MID.','.$this -> mSrcCnt.','.$lJobId.': User('.$lUid.') isn\'t in the series for Flag '.$lFlag.' or has still confirmed.', mlInfo);
        $this -> redirect($this -> getStdUrl().'.edt&jobid='.$lJobId);
      } else {
        $this -> dbg(MID.','.$this -> mSrcCnt.','.$lJobId.': User('.$lUid.') has no Rights for Flag '.$lFlag.'!', mlWarn);
        $this -> redirect($this -> getStdUrl().'.edt&jobid='.$lJobId);
      }
    } else {
      $this -> dbg(MID.','.$this -> mSrcCnt.','.$lJobId.': User('.$lUid.'): Flag '.$lFlag.' doesn\'t exist i._fla OR i._states!', mlError);
      $this -> redirect($this -> getStdUrl().'.edt&jobid='.$lJobId);
    }
  }

  protected function actSflag() {
    $lFlag = $this -> getReq('typ');
    $lJobId = $this -> getReq('jobid');
    $lAllFlags = CCor_Res::get('fla');
    if (isset($lAllFlags[$lFlag])) {
      $lFlagEve = $lAllFlags[$lFlag];
      $lVote  = $this -> getInt('vote');
      $lVal   = $this -> getReq('val');
      $lWebstatus = $this -> getInt('webstatus');
      $lMsg   = (isset($lVal['msg'])) ? $lVal['msg'] : '';
      $lFiles = $this -> getReq('listuserfiles');

      $lUid = $this -> mUsr -> getId();
      $lArr = CCor_Res::extract('id', 'departm_fullname', 'usr');
      $lName = (isset($lArr[$lUid])) ? $lArr[$lUid] : '[unknown name]';
      $lApl = new CApp_Apl_Loop($this -> mSrc, $lJobId, $lFlag, MID, $lWebstatus);
      $lApl -> setFlagFiles($lUid, $lFiles);
      $lSetFlagState = $lApl -> setFlagState($lUid, $lName, $lVote, $lMsg);

      if ($lSetFlagState) {
        $lNew = $lApl -> getOverallFlagState();//aus {0,1,2,3}
        if ($lNew != $lFlag) {
          $lClass = 'CJob_'.$this -> mSrcCnt.'_Mod';
          $lMod = new $lClass($lJobId);
          $lUpd = array('last_status_change' => date('Y-m-d H:i:s'));
          $lUpd[$lFlagEve['ddl_fie']] = $lNew;
          $lMod -> forceUpdate($lUpd);
        }

        $lCap = '';#$lFlagEve['name_'.LAN];
        #$lImg = 'img/flag/';
        switch($lVote) {
          case FLAG_STATE_AMENDMENT :
            $lCap.= ' - '.$lFlagEve['amend_'.LAN];
            #$lImg.= $lFlagEve['amend_ico'];
            $lTyp = htAplNok;
            BREAK;
          case FLAG_STATE_CONDITIONAL :
            $lCap.= ' - '.$lFlagEve['condit_'.LAN];
            #$lImg.= $lFlagEve['condit_ico'];
            $lTyp = htAplCond;
            BREAK;
          case FLAG_STATE_APPROVED :
            $lCap.= ' - '.$lFlagEve['approv_'.LAN];
            #$lImg.= $lFlagEve['approv_ico'];
            $lTyp = htAplOk;
            BREAK;
        }
        $this -> dbg('FlagIsConfirmed '.MID.', '.$this -> mSrcCnt.', '.$lJobId.', '.lan('flag.confirm').': '.$lFlagEve['name_'.LAN]);
        $lClass = 'CJob_'.$this -> mSrcCnt.'_Step';
        $lStepClass = new $lClass($lJobId);
        $lConfirm = $lStepClass -> ConfirmFlag($lFlag, $lCap, $lMsg, $lWebstatus);
        if ($lConfirm) {
          $lJobId = $lStepClass -> getJobId();
          $lCopyJobTo = $lStepClass -> mCopyJobTo;
          $lCopyTaskTo = $lStepClass -> mCopyTaskTo;

          if ($lCopyJobTo !== '') {
            // Copy Job without his Project Assigment.
            $this -> redirect('index.php?act=job-'.$lCopyJobTo.'.cpy&jobid='.$lJobId.'&src='.$this -> mSrcCnt.'&target='.$lCopyJobTo);
          } elseif ($lCopyTaskTo !== '') {
            // Copy Job with his Project Assigment.
            // Find assigned ProjektId.
            $lJobIdColumn = 'jobid_'.$this -> mSrcCnt;
            $lSql = 'Select pro_id from al_job_sub_'.MID;
            $lSql.= ' WHERE '. $lJobIdColumn.' ='.esc($lJobId);
            $lAssignedProId = CCor_Qry::getInt($lSql);
            if ($lAssignedProId != FALSE) {
              $this -> dbg('Assigned ProjectId of reference job:'.$lAssignedProId);
              $this -> redirect('index.php?act=job-'.$lCopyTaskTo.'.cpy&jobid='.$lJobId.'&src='.$this -> mSrcCnt.'&target='.$lCopyTaskTo.'&proid='.$lAssignedProId);
            } else {
              $this -> redirect('index.php?act=job-'.$lCopyTaskTo.'.cpy&jobid='.$lJobId.'&src='.$this -> mSrcCnt.'&target='.$lCopyTaskTo);
            }
          } else {
            $this -> redirect($this -> getStdUrl().'.edt&jobid='.$lJobId);
          }
        } else {
          $this -> redirect($this -> getStdUrl().'.edt&jobid='.$lJobId);
        }
      }
    }
    $this -> redirect($this -> getStdUrl().'.edt&jobid='.$lJobId);
  }

  protected function actSetstate() {
    $lJobId = $this -> getReq('jobid');

    $lClass = 'CJob_'.$this -> mSrcCnt.'_Dat';
    $lJob = new $lClass();
    $lJob -> load($lJobId);

    // Setzen der Flags nur im Status 'Korrekturumlauf' mit apl=1
    $lSql = 'SELECT apl FROM `al_crp_status` s, `al_crp_master` m where m.mand='.MID.' AND s.mand=m.mand';
    $lSql.= ' AND m.code='.esc($lJob['src']).' AND m.id=s.crp_id AND s.status='.$lJob['webstatus'].' LIMIT 0,1';
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      $lApl = $lRow['apl'];
    } else {
      $lApl = 0;
    }

    $lLis2 = new CJob_Apl_Page_List($this -> mSrcCnt, $lJobId);
    $lUserAccess = $lLis2 -> ShowAplButtons();
    if (1 == $lApl AND !$lUserAccess) {
      $this -> dbg('No User Access to set commit');
      $this -> redirect('index.php?act=job-apl&src='.$this -> mSrcCnt.'&jobid='.$lJobId);

    } elseif (1 == $lApl) {

      $lClass = 'CJob_'.$this -> mSrcCnt.'_Header';
      $lHdr = new $lClass($lJob);
      #$lHdr = new CJob_Rep_Header($lJob);
      $lRet = $lHdr -> getContent();

      $lDlg = new CJob_Apl_Page_Form($this -> mSrcCnt, $lJobId, 'job-'.$this -> mSrcCnt.'.sapl');
      $lDlg -> setJob($lJob);

      $lDlg -> setParam('apl', $lJob['apl']);
      $lApl = new CApp_Apl_Loop($this -> mSrcCnt, $lJobId, 'apl');

      $lUid = $this -> mUsr -> getId();
      $lMsg = $lApl -> getCurrentUserComment($lUid);
      $lDlg -> setVal('msg', trim($lMsg));

      $lAnn = new CJob_Apl_Page_Annotations($lJob);

      $lRet.= $lAnn -> getContent().LF;

      $lRet.= $lDlg -> getContent();
      $lRet.= $lDlg -> getUpload();

      $lRet.= $this -> getWecLogout();

      $this -> render($lRet);
    } else {
      $this -> redirect($this -> getStdUrl().'.edt&jobid='.$lJobId);
    }
  }

  protected function actApl() {
    $lJobId = $this -> getReq('jobid');
    $lFla = $this -> getInt('flag');
    $lnpl = 1; //$this -> getInt('newapl');

    $lClass = 'CJob_'.$this -> mSrcCnt.'_Dat';
    $lJob = new $lClass();
    $lJob -> load($lJobId);

    // Setzen der Flags nur im Status 'Korrekturumlauf' mit apl=1
    $lSql = 'SELECT apl FROM `al_crp_status` s, `al_crp_master` m where m.mand='.MID.' AND s.mand=m.mand';
    $lSql.= ' AND m.code='.esc($lJob['src']).' AND m.id=s.crp_id AND s.status='.$lJob['webstatus'].' LIMIT 0,1';
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      $lApl = $lRow['apl'];
    }
    #echo '<pre>---cnt.php---';var_dump($lJob,$lSql,$lApl,'#############');echo '</pre>';
    if (1 == $lApl) {

      $lClass = 'CJob_'.$this -> mSrcCnt.'_Header';
      $lHdr = new $lClass($lJob);
      #$lHdr = new CJob_Rep_Header($lJob);
      $lRet = $lHdr -> getContent();

      $lCap = $lFla;
      switch($lFla) {
        case CApp_Apl_Loop::APL_STATE_AMENDMENT :
          $lCap = lan('apl.amendment');
          BREAK;
        case CApp_Apl_Loop::APL_STATE_CONDITIONAL :
          $lCap = lan('apl.conditional');
          BREAK;
        case CApp_Apl_Loop::APL_STATE_APPROVED :
          $lCap = lan('apl.approval');
          BREAK;
        case CApp_Apl_Loop::APL_STATE_BACKTOGROUP :
          $lCap = lan('apl.backtogroup');
          BREAK;
      }

      $lDlg = new CJob_Apl_Loop_Form($this -> mSrc, $lJobId, $lCap, $lFla, $lJob['apl'], $lJob[CCor_Cfg::get('wec.annotation.master', 'per_prj_verantwortlich')]);
      $lDlg->setJob($lJob);

      $lApl = new CApp_Apl_Loop($this -> mSrc, $lJobId, 'apl');
      $lUid = $this -> mUsr -> getId();
      $lMsg = $lApl -> getCurrentUserComment($lUid);

      $lAplUsrList = $lApl -> getAplUserlist();
      $lUsrArr = CCor_Res::extract('id', 'fullname', 'usr');
      // Name der Backup-person, wenn sie als Vertretung auftritt.
      // Wenn die Vertretung diesen Kommentar nicht entfernen darf, muss er unter actsapl eingebaut werden.
      $lRow = $lAplUsrList[$lUid];
      $lNam = explode(') ',$lRow['name']);//will nicht nach '(...)' suchen...
      if (isset($lNam[1])) {
        $lName = $lNam[1];
      } else {
        $lName = $lNam[0];
      }
      if (isset($lUsrArr[$lRow['uid']]) AND $lName != $lUsrArr[$lRow['uid']]) {
        $lFillIn = '('.lan('FillIn:').' '.$lUsrArr[$lRow['uid']].') ';
      } else {
        $lFillIn = '';
      }
      #echo '<pre>---cnt.php---'.get_class().'---';var_dump($lAplUsrList,$lNam,$lMsg,$lFillIn,'#############');echo '</pre>';
      $lMsg = $lFillIn.$lMsg;
      $lDlg -> setVal('msg', trim($lMsg));

      $lDlg -> setUsers($lAplUsrList);
      $lRet.= $lDlg -> getContent();

      $this -> render($lRet);
    }
  }

  protected function actSapl() {
    $lJobId = $this -> getReq('jobid');

    // Setzen der Flags nur im Status 'Korrekturumlauf' mit apl=1
    $lFac = new CJob_Fac($this -> mSrc, $lJobId);
    $lJob = $lFac -> getDat();
    $lSql = 'SELECT apl FROM `al_crp_status` s, `al_crp_master` m where m.mand='.MID.' AND s.mand=m.mand';
    $lSql.= ' AND m.code='.esc($lJob['src']).' AND m.id=s.crp_id AND s.status='.$lJob['webstatus'].' LIMIT 0,1';
    #echo '<pre>---cnt.php---'.get_class().'---';var_dump($_REQUEST,$lSql,'#############');echo '</pre>';
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      $lApl = $lRow['apl'];
    }
    if (1 == $lApl) {
      $lFla = $this -> getInt('flag');
      $lVal = $this -> getReq('val');
      $lSta = $this -> getReq('apl');
      $lMsg = (isset($lVal['msg'])) ? $lVal['msg'] : '';
      $lAction = (isset($lVal['action_for'])) ? explode(",", $lVal['action_for']) : NULL;
      $lFiles = $this -> getReq('listuserfiles');

      $lApl = new CApp_Apl_Loop($this -> mSrc, $lJobId, 'apl');
      $lUid = $this -> mUsr -> getId();
      $lApl -> setToFiles($lUid, $lFiles);

      if (0 == $lFla) {
          // Status unveraendert lassen, aber Kommentar speichern
          $lApl -> setState($lUid, NULL, $lMsg);
        } elseif (6 == $lFla) {
          $lApl -> updateGroupState($lUid, $lFla, $lMsg);
        } else {
          // Status aendern und Kommentar speichern
          $lApl -> setState($lUid, $lFla, $lMsg, NULL, FALSE, $lAction);
      }

      $lNew = $lApl -> getOverallState();
      if ($lNew != $lSta) {
        $lUpd['apl'] = $lNew;
        $lClass = 'CJob_'.$this -> mSrcCnt.'_Mod';
        $lMod = new $lClass($lJobId);
        #$lMod = new CJob_Rep_Mod($lJobId);
        $lMod -> forceUpdate($lUpd);
      }

      switch($lFla) {
        case CApp_Apl_Loop::APL_STATE_AMENDMENT :
          $lCap = lan('apl.amendment');
          $lTyp = htAplNok;
          BREAK;
        case CApp_Apl_Loop::APL_STATE_CONDITIONAL :
          $lCap = lan('apl.conditional');
          $lTyp = htAplCond;
          BREAK;
        case CApp_Apl_Loop::APL_STATE_APPROVED :
          $lCap = lan('apl.approval');
          $lTyp = htAplOk;
          BREAK;
        case CApp_Apl_Loop::APL_STATE_BACKTOGROUP :
          $lCap = lan('apl.backtogroup');
          $lTyp = htAplBackToGroup;
          BREAK;
        default:
          $lCap = lan('apl.savecomment');
          $lTyp = htFlags;
      }

      //  **************** START: TTS-478 XFDF Dateien (Ruediger) ***************************************
      // xfdf Dateinen lesen

      $lWec = CCor_Cfg::get('wec.available', true);
      $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
      if ($lWec) {
        $lAnn = new CJob_Apl_Page_Annotations($lJob);
        // Get existing add_data from al_job_apl_loop
        $lArr = $lApl -> getAddData();
        if (!is_array($lArr)) $lArr = array();
        $lArr['xfdf'] = $lAnn -> getXml();
        $lApl -> setAddData($lArr);

        // xfdf auf PDF Ordner speichern
        if ($lWriter == 'alink') {
          if (!empty($lArr['xfdf']) && $this -> arrayDepth($lArr['xfdf']) < 3) {
            foreach($lArr['xfdf'] as $lKey => $lVal) {
              if (is_array($lVal)) {
                $ln = pathinfo($lVal['name']);
                $lFil = $ln['filename'].'_'.$lApl -> getMaxNum().'.xfdf';  // Dateiname + APL-Nummer + xfdf
                $lQry = new CApi_Alink_Query('putFile');
                $lQry -> addParam('sid', MAND);
                $lQry -> addParam('jobid', $lJobId);
                $lQry -> addParam('filename', $lFil);
                $lQry -> addParam('data', base64_encode($lVal['xfdf']));
                $lQry -> addParam('mode', 2);
                $lRes = $lQry -> query();
              }
            }
          } else {
            foreach ($lArr['xfdf'] as $lOuterKey => $lOuterValue) {
              foreach ($lOuterValue as $lInnerKey => $lInnerValue) {
                if (is_array($lInnerValue)) {
                  $ln = pathinfo($lInnerValue['name']);
                  $lFil = $ln['filename'].'_'.$lApl -> getMaxNum().'_'.$lInnerValue['page'].'.xfdf';  // Dateiname + APL-Nummer + Seitennummer + xfdf
                  $lQry = new CApi_Alink_Query('putFile');
                  $lQry -> addParam('sid', MAND);
                  $lQry -> addParam('jobid', $lJobId);
                  $lQry -> addParam('filename', $lFil);
                  $lQry -> addParam('data', base64_encode($lInnerValue['xfdf']));
                  $lQry -> addParam('mode', 2);
                  $lRes = $lQry -> query();
                }
              }
            }
          }
        }
      }
      //  **************** STOPP: TTS-478  *************************************************************

      $lHis = new CApp_His($this -> mSrc, $lJobId);
      $lNeedSignature = CCor_Cfg::get('job.apl.signature', false);
      if ($lNeedSignature) {
        $lHis ->setVal('signature_id', CCor_Usr::getAuthId());
      }
      $lHis -> add($lTyp, $lCap, $lMsg) ;
    }

    $lBeat = new CJob_Workflow_Heartbeat($this -> mSrcCnt, $lJobId);
    $lBeat->heartBeat();

    $lUrl = $this->getRedirectAfterApl($lJobId);
    $this -> redirect($lUrl);
  }

  protected function getRedirectAfterApl($aJobId) {
    $lPref = $this->mUsr->getPref('apl.redirect');
    $lRet = 'index.php?act=';
    if ('home' == $lPref) {
      return $lRet.'hom-wel';
    }
    if ('apl' == $lPref) {
      return $lRet.'job-apl&src='.$this->mSrc.'&jobid='.$aJobId;
    }
    // for 'job' and anything else
    return $this -> getStdUrl().'.edt&jobid='.$aJobId;
  }

  public function arrayDepth($aArray) {
    if (is_array(reset($aArray))) {
      $lRet = $this -> arrayDepth(reset($aArray)) + 1;
    } else {
      $lRet = 1;
    }

    return $lRet;
  }

  ######################################################################################
  /*
   * Status changes
  */
  protected function actStep() {
    $lJobId = $this -> getReq('jobid');
    $lStp   = $this -> getInt('sid'); // Step_Id

    // If $lAddUser = TRUE, Add user or group to existing APL. NOT used in pro, sku
    $lAddUser = $this -> getReq('addUser', FALSE);

    #// Wird noch nicht genutzt. $lFnc braucht redirect vor exit; ODER KEIN exit;
    $lFnc = 'actStep'.$lStp;
    if ($this -> hasMethod($lFnc)) {
      $this -> $lFnc();
      exit;
    }

    // Kommentar-Eingabe zu Flags in Critical Path?
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp[ $this -> mSrcCnt ];
    $lCrpSteps = CCor_Res::get('crpstep', $this -> mCrpId);
    $lCrpStep = $lCrpSteps[$lStp];
    #echo '<pre>---cnt.php---'.get_class().'---';var_dump($lCrpStep ,'#############');echo '</pre>';
    #$lFla = intval(CCor_Qry::getStr('SELECT flags FROM al_crp_step WHERE mand='.MID.' AND id='.$lStp));
    $lFla = intval($lCrpStep['flags']);
    $lFlagsActStr = $lCrpStep['flag_act'];
    $lFlagActShowDialog = FALSE;
    if (!empty($lFlagsActStr)) {
      $lFlagsAct = array_map('intval', explode(',', $lFlagsActStr));
      foreach ($lFlagsAct as $lFlagAct) {
        if (bitset($lFlagAct, sfComment)) {
          $lFlagActShowDialog = TRUE;
          break;
        }
      }
    }
    #echo '<pre>---cnt.php---'.get_class().'---';var_dump($lFlagsActStr,$lFlagsAct,$lFlagActShowDialog,'#############');echo '</pre>';
    // mit oder ohne Kommentar-Eingabe?
    if (bitset($lFla, sfComment) OR $lFlagActShowDialog) {
      $this -> stepDialog();
    } else {
      $this -> redirect('index.php?act=job-'.$this -> mSrcCnt.'.cnf&jobid='.$lJobId.'&sid='.$lStp.'&webstatus='.$this -> getInt('webstatus'));
    }
  }

  protected function stepDialog() {
    $lJobId = $this -> getReq('jobid');
    $lStp = $this -> getInt('sid');
    // If $lAddUser = TRUE, Add user or group to existing APL
    $lAddUser = $this -> getReq('addUser', FALSE);

    #$lJob = new CJob_Rep_Dat();
    $lClass = 'CJob_'.$this -> mSrcCnt.'_Dat';
    $lJob = new $lClass();
    $lJob -> load($lJobId);

    #$lHdr = new CJob_Rep_Header($lJob);
    $lClass = 'CJob_'.$this -> mSrcCnt.'_Header';
    $lHdr = new $lClass($lJob);
    $lRet = $lHdr -> getContent();
    $lRet.= $this -> getBreakeStepWarning($lStp);

    // Ansicht des eMail-Buttons nur im APL!!!
    $lShowButton = FALSE;
    $lFrm = new CJob_Apl_List($this -> mSrcCnt, $lJobId, 'job', $lShowButton);
    $lFrm -> mTitle = lan('job-apl.menu.old');
    $lRet.= $lFrm -> getContent();

    $lDlg = new CJob_Dialog($this -> mSrcCnt, $lJobId, $lStp, $lJob, $lAddUser);

    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $lCrpId = $lCrp[ $this -> mSrcCnt ];
    $lSql = 'SELECT id, from_id FROM `al_crp_step` WHERE `mand`='.MID.' AND `crp_id`='.$lCrpId;
    $lSql .= ' AND `flags` & '.sfAmendDecide.' AND NOT(`flags` & '.sfCloseApl.')';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lStpFla = $lRow['id'];
      $lStpSta = $lRow['from_id'];

      // Die Gleichheit tritt nur f�r einen Step ein! //(98 == $lStp) f�r Griesson
      #$lSql = 'SELECT `status` FROM `al_crp_status` WHERE `mand`='.MID.' AND `id`='.$lStpSta;
      #$lWebSta = CCor_Qry::getInt($lSql);
      if ($lStpFla == $lStp) {
        $lArrSta = CCor_Res::getByKey('id', 'crp', $lCrpId);
        $lWebSta = $lArrSta[$lStpSta]['status'];

        $lSql = 'SELECT msg FROM al_job_his WHERE `mand`='.MID.' AND src_id='.esc($lJobId).' AND to_status='.$lWebSta.' ORDER BY datum DESC LIMIT 1';
        $lQry2 = new CCor_Qry($lSql);
        if ($lRow2 = $lQry2 -> getDat()) {
          $lDlg -> setVal('msg', $lRow2['msg']);
        }
      }
    }
    $lRet.= $lDlg -> getContent();

    $this -> render($lRet); // exit; ist die letzte Funktion in render!
  }

  protected function getBreakeStepWarning($aStpId) {
    return "";
  }

  protected function actCnf() {

    // error_reporting(E_ALL);
    // ini_set('display_errors', TRUE);
    $lRequestedFromthisWebStatus = $this -> getReq('webstatus');
    $lSid = $this -> getInt('sid');
    $lJobId = $this -> getReq('jobid');

    $lAplTypes = CCor_Res::extract('id', 'apl_type', 'Crpstep');
    $lTyp = $lAplTypes[$lSid];
    $lAplType = (!empty($lTyp) && isset($lTyp)) ? $lTyp : 'apl';

    $lIsApl = $this -> getReq('apl', FALSE);
    $lPag = $this -> getReq('page', 'job');
    $lVal = $this -> getReq('val');
    // Add User or Group to APL
    $lAddUser = $this -> getReq('addUser', FALSE);
    $lMsg = (isset($lVal['msg'])) ? $lVal['msg'] : '';
    //[1135] Add-on zu 'Flags in Critical Path'
    $lMsg_Flag = (isset($lVal['msg_Flag'])) ? $lVal['msg_Flag'] : array();
    $lEmailInfoApl = $this -> getReq('emailinfoapl', -1);

    $lAdd = array();
    #echo '<pre>---cnt.php--actCnf-'.get_class().'---';var_dump($lVal,'#############');echo '</pre>';
    #echo '<pre>---cnt.php---'.get_class().'---';print_r($_REQUEST);echo '</pre>';
    // Ignore any email-notifications?
    // Hier sollten alle Daten geholt werden, da sonst mehrfach GetJobDetails aufgerufen wird
    $lFac = new CJob_Fac($this -> mSrcCnt, $lJobId);
    $lJob = $lFac -> getDat();


    if (!empty($lRequestedFromthisWebStatus) && ($lRequestedFromthisWebStatus != $lJob['webstatus'])) {
      $lWarnMsg = lan('step-change.warning.msg').BR.BR;
      $lWarnMsg.= '<a href="'.$this -> getStdUrl().'.edt&jobid='.$lJobId.'&page='.$lPag.'"><u>'.lan('step-change.warning.link.txt').'</u></a>';
      $this -> render($lWarnMsg);
	    return;
    }
    // Special APL Dialog window
    $lMethod = 'actCnf'.$lSid;
    if ($this -> hasMethod($lMethod)) {
      $this -> $lMethod();
      return;
    }

    $lNewAplDate = '';
    if (isset($lVal['ddl_korrekturumlauf'])) {
      $lDat = new CCor_Date($lVal['ddl_korrekturumlauf']);
      if (!$lDat -> isEmpty()) {
        $lNewAplDate = $lDat -> getFmt(lan('lib.date.long')); //APL: damit das geaenderte Datum in den eMails angezeigt werden kann
        $lJob['ddl_korrekturumlauf'] = $lNewAplDate;
      }
    }

    $lClass = 'CJob_'.$this -> mSrcCnt.'_Step';
    $lStepClass = new $lClass($lJobId, $lJob);
    $lNoStop4Crp = $lStepClass -> StopFlags($lSid);// NoStop4Crp == Flags could be stopped


    // Set APL State to 100 if this is Break Step!
    $lBreaksSteps = CCor_Cfg::get('break.step');
    $lBreakStepId = $lBreaksSteps[$this -> mSrcCnt][$lJob['webstatus']];
    if ($lSid == $lBreakStepId) {
      $lId = CCor_Usr::getInstance();
      $lUID = $lId -> getId();

      $lBreakValue = CApp_Apl_Loop::APL_STATE_BREAK;
      $lApl = new CApp_Apl_Loop($this -> mSrc, $lJobId, $lAplType);
      $lApl ->setState($lUID, $lBreakValue, $lMsg);
      $this -> dump('BreakId ='.$lBreakStepId.' match => Set APL States to ='.$lBreakValue);
      $lHis = new CApp_His($this -> mSrcCnt, $lJobId);
      $lHis ->add($lBreakValue, 'Break', $lMsg);
    }

    if ($lNoStop4Crp) {

      $lApl = new CApp_Apl_Loop($this -> mSrcCnt, $lJobId, $lAplType, MID, $lJob['webstatus']);

      //is null!!!
      /** START: TTS-478 XFDF Dateien (Ruediger).
       *  siehe 'Freigabeumlauf++Annotation+aus+WebCenter+ins+Portal+einbinden-1.pdf' unter Ordner 'Doku'
       */
      if (!$lAddUser){
        $this -> makeAnnotationReport($lAdd, $lApl, $lMsg, $lVal);
      }
      //  **************** STOPP: TTS-478  *************************************************************

      /** --START: TTS-481:
       * Zum Korrekturumlauf sollen den eingeladenen Personen
       * unterschiedliche Emails zugesendet werden.
       * Siehe 'TTS-481_Start_Korrekturumlauf.pdf' under Ordner 'Doku'
       */
      $lOld1 = $this -> getReq('act_old1'); // Ignore any email-notifications? Check/Uncheck
      $lAct1 = $this -> getReq('act_new1'); // UsrId => 'Vorname Zuname'
      $lOld2 = $this -> getReq('act_old2'); // Wich email shoud be send to user: EmailTplId, 0= 'noch keine Auswahl'
      $lAct2 = $this -> getReq('act_new2'); // EveAct.EmailTpl.Position='Reihenfolge d. Einladung'

      $lIgn1 = array();
      $lInvite = array();
      $lMinPos = MAX_SEQUENCE; // Behelfsvorbelegung
      $lInvite['apl'] = array();
      if (!empty($lOld1)) {
        foreach ($lOld1 as $lUid => $lVal1) {
          if (!isset($lAct1[$lUid])) {
            $lIgn1[] = $lUid;
            #if ( isset($lOld2[$lUid]) ) unset($lOld2[$lUid]); // brauche ich vielleicht garnicht
            if ( isset($lAct2[$lUid]) ) unset($lAct2[$lUid]);
          } else {
            if (-1 == $lAct2[$lUid]) {
              $lInvite['apl'][] = $lUid;
            } else {
              # $lInvite[ $lAct2[$lUid] ][] = $lUid;
              # ersetze Uid durch das Array, da spaeter noch die Id vom DBEintrag al_job_spl_states hinzukommt
              $lActTplPos = explode('.',$lAct2[$lUid]); // EveAct.EmailTplId.Position
              $lActTpl = $lActTplPos[0].'.'.$lActTplPos[1];
              if (!isset($lActTplPos[5])) {
                $lActTplPos[5] = 'y';
              }
              $lInvite[ $lActTpl ][] = array(
                  $lUid => array(
                      'uid' => $lUid,
                      'pos' => $lActTplPos[2],
                      'dur' => $lActTplPos[3],
                      'ddl' => $lActTplPos[4],
                      'inv' => $lActTplPos[5],
                  )
              );
              if ($lMinPos > $lActTplPos[2]) {
                $lMinPos = $lActTplPos[2];
              }
            }//end_if/else (-1 == $lAct2[$lUid])
          }//end_if/else (!isset($lAct1[$lUid]))
        }//end_foreach ($lOld1 as $lUid => $lVal1)
      }//end_if (!empty($lOld1))
      if(empty($lInvite['apl'])) {
        unset($lInvite['apl']);
      }

      if(is_array($lAct2) AND !empty($lAct2)) {
        $lChoice = array_search(0,$lAct2); // ein Vorkommen reicht, zum Abbruch
        if (FALSE !== $lChoice) {
          $lUrl = 'index.php?act=job-'.$this -> mSrcCnt.'.step&jobid='.$lJobId.'&sid='.$lSid.'&dialog=2';
          foreach ($lAct2 as $lUid => $lTpl) {
            $lUrl .= '&dst[]='.$lUid;
          }
          $lUrl .= '&not='.$lChoice;
          $this -> redirect($lUrl);
        }
      }

      # *****************************************************************************************************
      #   echo '<pre>---cnt.php---';var_dump($lChoice,$_REQUEST,$lAct2,$lUrl,$lAdd['special_apl_usr'],'#############');echo '</pre>';
      # *****************************************************************************************************

      //--STOPP: TTS-481

      // amendment type & root cause
      if (isset($lVal['amt'])) {
        $lAdd['amt'] = $lVal['amt'];
      }
      if (isset($lVal['cause'])) {
        $lAdd['cause'] = $lVal['cause'];
      }

      /**
       * JobId: 23398
       * Define a rout cause for amendments
       * Additional selection field with a helptable behind, to define route cause.
       */
      $lCause = array();
      if (isset($lVal['apl_amendment_cause_1']) && $lVal['apl_amendment_cause_1'] != "") {
        $lAdd['apl_amendment_cause_1'] = $lVal['apl_amendment_cause_1'];
        array_push($lCause, $lVal['apl_amendment_cause_1']);
      }
      if (isset($lVal['apl_amendment_cause_2']) && $lVal['apl_amendment_cause_2'] != "") {
        $lAdd['apl_amendment_cause_2'] = $lVal['apl_amendment_cause_2'];
        array_push($lCause, $lVal['apl_amendment_cause_2']);
      }
      if (isset($lVal['apl_amendment_cause_3']) && $lVal['apl_amendment_cause_3'] != "") {
        $lAdd['apl_amendment_cause_3'] = $lVal['apl_amendment_cause_3'];
        array_push($lCause, $lVal['apl_amendment_cause_3']);
      }
      //Write Amendment Cause into Job History
      if(!empty($lCause)) {
        $lHis = new CApp_His($this -> mSrcCnt, $lJobId);
        $lHis -> add(htEdit, lan("apl_amendment_cause_added"), implode(", ", $lCause));
      }


      if (isset($_FILES['val'])) {
        $lFin = new CApp_Finder($this -> mSrcCnt, $lJobId);
        $lDir = $lFin -> getPath('doc');
        $lUpl = new CCor_Upload();
        $lRes = $lUpl -> uploadIndex('file', $lDir);
        if ($lRes) {
          $lAdd['fil'] = $lRes;
          CCor_Usr::insertJobFile($this -> mSrcCnt, $lJobId, 'doc', $lRes);

          $lArr = $_FILES['val'];
          $lNam = $lArr['name']['file'];
          $lHis = new CApp_His($this -> mSrcCnt, $lJobId);
          $lMsg = sprintf(lan('filupload.success'),$lNam);
          $lHis -> add(htFileupload, lan('filupload.his.msg'), $lMsg);
        }
      }

      // Ignore any email-notifications?
      $lOld_All = $this -> getReq('act_old');
      $lAct_All = $this -> getReq('act_new'); //ToDo: spaeter in Step nachpr�fen, ob $lIgn[$lK] als empty vorhanden ist!
      $lIgn = array();
      #echo '<pre>---cnt.php---'.get_class().'---';var_dump($lOld_All,$lAct_All,'#############');echo '</pre>';
      if (!empty($lOld_All)) {
        foreach ($lOld_All as $lK => $lOld) {
          $lIgn[$lK] = array();
          foreach ($lOld as $lKey => $lVal2) {
            if (!isset($lAct_All[$lK][$lKey])) {
              $lIgn[$lK][] = $lKey;
            }
          }
        }
      }

      //Alle Gruppenmitglieder in die Invite! :(
      if(!empty($lAct_All) AND !empty($lAct_All[0])) {
        // Uebergabe POS -> Dialog!!
        $i = 0;// dadurch koennen user, die in mehreren Gruppen sind, mehrfach im apl_states u. sys_mails eingetragen werden
        foreach ($lAct_All[0] as $lActId => $lVal2) {
          $lPosArr = explode('.', $lVal2);
          if ('G' == $lPosArr[0]) {
            $lSql = 'SELECT m.uid FROM al_usr u, al_usr_mem m WHERE u.id=m.uid AND u.del="N" AND m.gid='.$lPosArr[1];
            $lQry = new CCor_Qry($lSql);
            $lTpl = $lPosArr[2].'.'.$lPosArr[3];
            foreach ($lQry as $lRow) {
              #echo '<pre>---cnt.php--G-';var_dump($lSql,$lRow,'#############');echo '</pre>';
              $lInvite[$lTpl]['G'.$i][$lRow['uid']] = array(
                  'uid' => $lRow['uid'],
                  'pos' => $lPosArr[4],
                  'dur' => $lPosArr[5],
                  'ddl' => $lPosArr[6],
                  'gru' => $lPosArr[1],
                  'inv' => $lPosArr[8],
              );
              if (isset($lPosArr[3]) && 'one' == $lPosArr[3]) {
                $lInvite[$lTpl]['G'.$i][$lRow['uid']]['confirm'] = 'one';
              }
              if (isset($lPosArr[7])) {
                $lInvite[$lTpl]['G'.$i][$lRow['uid']]['confirm'] = $lPosArr[7];
              }
            }
          }
          if ('U' == $lPosArr[0]) {
            $lTpl = intval($lActId).'.'.$lPosArr[2];
            $lInvite[$lTpl][0][$lPosArr[1]] = array(
                'uid' => $lPosArr[1],
                'pos' => $lPosArr[3],
                'dur' => $lPosArr[4],
                'ddl' => $lPosArr[5],
            );
            if (isset($lPosArr[6])) {
              $lInvite[$lTpl][0][$lPosArr[1]]['inv'] = $lPosArr[6];
            }
          }
          $i++;
        }
      }

      // Start Apl?
      if (FALSE !== $lIsApl) {
        $lDst = $this -> getReq('dst');
        if (!empty($lDst)) {
          $lAdd['apl_usr'] = implode(',', $lDst);//hier fehlen die Gruppenmitglieder
        }
        if (!empty($lDst) OR !empty($lInvite)) {
          //$lDat = new CCor_Date();
          //$lDat -> setInp($lVal['ddl_korrekturumlauf']);
          if (!empty($lNewAplDate)) {
            $lAdd['apl_date'] = $lNewAplDate;
          }

          // CREATE NEW LOOP
          if ($lAddUser){
            // Add User or Group to already opened Loop
            // No need to create a new Loop. Get last open Loop Id instead
            $lAdd['apl_id'] = $lApl -> getLastOpenLoop(TRUE, $lDat -> getSql());
          } else {
            $lAdd['apl_id'] = $lApl -> createLoop($lDat -> getSql(), $lSid);
          }

          $lUsrArr = CCor_Res::extract('id', 'departm_fullname', 'usr');

          // FPR STYLE SELECT
          #$lArr = array('prj' => intval($lJobId)); // jobno = webcenter projectname
          if (!empty($lDst)) {
            if (-1 != $lEmailInfoApl) {
              $lEmailInfoApl = explode('.', $lEmailInfoApl);
              //$lRow['id'].'.'.$lPar['tpl'].'.'.$lRow['pos'].'.'.$lRow['dur'].'.'.$lDdl4User
              $lDur = $lEmailInfoApl[3];
              $lDdl = $lEmailInfoApl[4];
            }
            foreach ($lDst as $lUid) {
              // Einladen einer Gruppe per emailtoApl - old version
              if (0 === strpos($lUid, 'G')) {
                //dialog.php: $this -> mEmailInfo_Apl = $lRow['id'].'.'.$lPar['tpl'].'.'.$lRow['pos'];
                $lG_id = str_replace('G', '', $lUid);
                $lSql = 'SELECT m.uid FROM al_usr u, al_usr_mem m WHERE u.id=m.uid AND u.del="N" AND m.gid='.$lG_id;
                #echo '<pre>---cnt.php--G-';var_dump($lSql,'#############');echo '</pre>';
                $lQry = new CCor_Qry($lSql);
                foreach ($lQry as $lRow) {
                  // Speichern wer eingeladen wurde -> INSERT INTO al_job_apl_states SET loop_id, user_id & name
                  // und in welcher Reihenfolge eingeladen werden soll: 0=sofort

                  $lU_id = $lRow['uid'];
                  if (!isset($lUsrArr[$lU_id])) continue;
                  $lAdd['apl_usr'] .= ','.$lU_id; // fuer den email-Versand
                  $lNam = $lUsrArr[$lU_id];

                  $lInfoArr = array('pos' => 0);

                  // wenn es nur EmailToApl gibt, sind Auswahldialog und Message auf der gleichen Seite.
                  // d.h. die folgende Info fehlte
                  if (isset($lDur) AND isset($lDdl)) {
                    $lInfoArr['dur'] = $lDur;
                    $lInfoArr['ddl'] = $lDdl;
                  }

                  $lInfoArr['gru'] = $lG_id;
                  $lInfoArr['confirm'] ='one';
                  #echo '<pre>---cnt.php---'.get_class().'---';var_dump($lU_id, $lNam, $lInfoArr,'#############');echo '</pre>';
                  $lApl -> addItem($lU_id, $lNam, $lInfoArr);
                }
              }

              if (!isset($lUsrArr[$lUid])) continue;

              $lNam = $lUsrArr[$lUid];
              // Speichern wer eingeladen wurde -> INSERT INTO al_job_apl_states SET loop_id, user_id & name
              if (isset($lDur) AND isset($lDdl)) {
                $lInfoArr = array('pos' => 0);
                $lInfoArr['dur'] = $lDur;
                $lInfoArr['ddl'] = $lDdl;
                $lApl -> addItem($lUid, $lNam, $lInfoArr);
              } else {
                $lApl -> addItem($lUid, $lNam);
              }
              #$lArr['uid'] = $lUid;
              #CApp_Queue::add('wecinvite', $lArr);
            }
          }//end_if (!empty($lDst))
        }


        // NEW INVITE
        #echo '<pre>---cnt.php--$lMinPos-'.$lMinPos;print_r($lInvite);echo '#############</pre>';
        if (!empty($lInvite)) {
          #var_export($lInvite);
          #exit;
          foreach ($lInvite as $lTpl => $lArrUid) {
          if ('apl' != $lTpl) {
            // $lArrUid = array of all users for this template
            // $lUidArr =
            foreach ($lArrUid as $lK => $lUidArr) {
              foreach ($lUidArr as $lUid => $lUidPos) {
                if (!isset($lUsrArr[$lUid])) continue;
                $lNam = $lUsrArr[$lUid];
                // Speichern wer eingeladen wurde -> INSERT INTO al_job_apl_states SET loop_id, user_id & name
                // und in welcher Reihenfolge eingeladen werden soll: 0=sofort
                #echo '<pre>---cnt.php---';var_dump($lUid, $lNam,$lUidPos,'#############');echo '</pre>';
                $lInfoArr = array(
                    'pos' => $lUidPos['pos'],
                    'dur' => $lUidPos['dur'],
                    'ddl' => $lUidPos['ddl']
                );
                if (isset($lUidPos['gru'])) {
                  $lInfoArr['gru'] = $lUidPos['gru'];
                }
                if (isset($lUidPos['confirm'])) {
                  $lInfoArr['confirm'] = $lUidPos['confirm'];
                }
                if (isset($lUidPos['inv'])) {
                  $lInfoArr['inv'] = $lUidPos['inv'];
                }
                $lAplStatesId = $lApl -> addItem($lUid, $lNam, $lInfoArr);
                // ersetze nun $lUid2 durch die $lAplStatesId
                $lInvite[$lTpl][$lK][$lUid]['apl_id'] = $lAplStatesId;

                // enthaelt nun EveAct.EmailTplId, UsrId,Position&AplStatesId
                // somit kann spaeter ueber die AplStatesId der zeitl. versetzte eMail-Versand erfolgen
              }
            }
          }//end_if
        }
        }
      }// Ende Start Apl
      #var_dump($lInvite);

      #echo '<pre>---cnt.php---';print_R($lInvite);echo '#############</pre>';

      if(!empty($lInvite)) {
        $lAdd['special_apl_usr'] = $lInvite;
      }
      // $lInvite enthaelt nun alle Uids und die eMail-templateIds, die versendet werden sollen.


      //Datum_Korrekturumlauf soll im Job uebernommen werden.
      $lClass = 'CJob_'.$this -> mSrcCnt.'_Mod';
      $lMod = new $lClass($lJobId);
      #$lMod = new CJob_Rep_Mod($lJobId);
      $lMod -> getPost($this -> mReq);
      $lMod -> update();


      # echo '<pre>---cnt.php--actCnf-';var_dump($lSid, $lMsg, $lAdd, $lIgn, $lMsg_Flag,'#############');print_r($lAdd);echo '</pre>';
      // 'Flags in Critical Path': Uebergabe der Flag-Kommentare als Array :)

      #var_export($lAdd);
      #exit;
      if ($lAddUser) {
        $lStepClass -> setAddUser(true);
      }
      $lHasStepped = $lStepClass -> doStep($lSid, $lMsg, $lAdd, $lIgn, $lMsg_Flag, $lIsApl);

      if ($lHasStepped) {
        $lJobId = $lStepClass -> getJobId();
        $lCopyJobTo = $lStepClass -> getCopyJobTo();
        $lCopyTaskTo = $lStepClass -> getCopyTaskTo();
        $lMoveJobTo = $lStepClass -> getMoveJobTo();

        if (isset($lApl) && !$lAddUser ) {
          if ($this -> getInt('activexfdf') == 1) {
            // xfdf Dateien lesen
            $lClass = 'CJob_'.$this -> mSrcCnt.'_Dat';
            $lJob = new $lClass();
            #$lJob = new CJob_Rep_Dat();
            $lJob -> load($lJobId);
            $lAnn = new CJob_Apl_Page_Annotations($lJob);
            $lAddData = $lApl -> getAddData();
            if (!is_array($lAddData)) $lAddData = array();
            $lAddData['xfdf'] = $lAnn -> getXml();
            $lApl -> setAddData($lAddData);
          }
        }
        if ($lCopyJobTo !== '') {
          // Copy Job without his Project Assigment.
          $this -> redirect('index.php?act=job-'.$lCopyJobTo.'.cpy&jobid='.$lJobId.'&src='.$this -> mSrcCnt.'&target='.$lCopyJobTo);
        } elseif ($lMoveJobTo !== '') {
          $this -> redirect('index.php?act=job-'.$lMoveJobTo.'.edt&jobid='.$lJobId);
        } elseif ($lCopyTaskTo !== '') {
          // Copy Job with his Project Assigment.
          // Find assigned ProjektId.
          $lJobIdColumn = 'jobid_'.$this -> mSrcCnt;
          $lSql = 'SELECT id,pro_id FROM al_job_sub_'.MID;
          $lSql.= ' WHERE '. $lJobIdColumn.' ="'.$lJobId.'"';
          $lQry = new CCor_Qry($lSql);
          if ($lRow = $lQry->getDat()) {
            $lAssignedProItemId = $lRow['id'];
            $lAssignedProId = $lRow['pro_id'];
          }
          if ($lAssignedProItemId !== FALSE) {
            $this -> dbg('Assigned ProjectId of reference job:'.$lAssignedProId);
            $this -> redirect('index.php?act=job-'.$lCopyTaskTo.'.cpy&jobid='.$lJobId.'&src='.$this -> mSrcCnt.'&target='.$lCopyTaskTo.'&proid='.$lAssignedProId.'&itmid='.$lAssignedProItemId.'&copytask=1');
          } else {
            $this -> dbg('Copy Task without ProjectItemId');
            $this -> redirect('index.php?act=job-'.$lCopyTaskTo.'.cpy&jobid='.$lJobId.'&src='.$this -> mSrcCnt.'&target='.$lCopyTaskTo.'&copytask=1');
          }
        } else {
          $lBeat = new CJob_Workflow_Heartbeat($this -> mSrcCnt, $lJobId);
          $lBeat->heartBeat();
        }
      }
    }
    $this -> redirect($this -> getStdUrl().'.edt&jobid='.$lJobId.'&page='.$lPag);
  }

  protected function actCnfnewapl() {
  }

  ######################################################################################
  /*
   * Create Webcenter Project.
  *
  * @param Get String $jobid JobId
  * @param Get string $src Jobtype
  * @param Get string $direct If defined create Webcenter directly. If Not take Queue.
  * @return redirect
  */
  protected function actWec() {
    $lJobId = $this -> getReq('jobid');
    $lSrc = $this -> getReq('src');
    $lDir = $this -> getReq('direct');
    $lTemplate  = CApi_Wec_WebcenterTemplate::getTemplate($lJobId);
    if (empty($lDir)) {
      $lArr = array();
      $lArr['jid'] = (string)$lJobId;
      $lArr['src'] = $lSrc;
      $lArr['name'] = intval($lJobId);
      $lArr['tpl'] = $lTemplate;
      CApp_Queue::add('wecprj', $lArr);
    } else {
      $lWec = new CApp_Wec($lSrc, $lJobId);
      $lWecPrjId = $lWec -> createWebcenterProject();
    }
    // Falls es kein Tab "Details (det)" gibt, redirect zu Tab "Identifikation (job)"
    $lTabs  = CCor_Cfg::get('job.mask.tabs');
    if (in_array('det', $lTabs)){
      $this -> redirect('index.php?act=job-'.$lSrc.'.edt&jobid='.$lJobId.'&page=det');
    } else {
      $this -> redirect('index.php?act=job-'.$lSrc.'.edt&jobid='.$lJobId);
    }

  }

  ######################################################################################

  protected function actAssignprj() {
    $lJobId = $this -> getReq('jobid');
    $lPrjId = $this -> getReq('prjid');

    $lVie = new CJob_Assign_List($this -> mSrc, $lJobId, $lPrjId, FALSE);
    $this -> render($lVie);
  }

  protected function actSassignprj() {
    $lJobId = $this -> getReq('jobid');
    // JobId: #22823 Copy Task and JobId #23094
    // Nach Anlegen der nue Job, wird es an referenz Projekt bzw. Projekt-Item zugeordnet.
    // $lProId and $lProItemId are set if Job has to assigned to this ProjektItems
    $lProId = $this -> getReqInt('pid');
    $lProItemId = $this -> getReqInt('prjitmid');

    $lPag = $this -> getReq('page', 'job');

    //if job already assigned to Projekt, get ProjektId.
    $lFromProId = $this -> getReqInt('fromid');
    $lColoumnName = 'jobid_'.$this -> mSrc;
    $lFromSubId = 0;
    /** If the Job is already assigned to Project.
     * Set Jobid in the old Project Items empty.
     */
    if ($lFromProId){
      //22651 Project Critical Path Functionality
      $lSql = 'SELECT `sub_id` FROM `al_job_pro_crp` WHERE `mand`='.MID;
      $lSql.= ' AND pro_id ='.esc($lFromProId).' AND jobid='.esc($lJobId);
      $lQry = new CCor_Qry();
      $lFromSubId = $lQry -> getInt($lSql);

      $lSql = 'UPDATE al_job_sub_'.MID;
      $lSql.= ' SET '.$lColoumnName.' = ""';
      $lSql.= ' WHERE pro_id ="'.$lFromProId.'"';
      $lSql.= ' AND  '.$lColoumnName.' = "'.$lJobId.'"';
      CCor_Qry::exec($lSql);
      $this ->dbg('Job Assigment from ProjektId #'.$lFromProId.' deleted.');

      // Reset Master-Variant Infos in the Job
      if ($lMasterVariantBundleActiv = CCor_Cfg::get('master.varaiant.bundle', FALSE)){
        $lUpd = Array('is_master' => '', 'master_id'=>'');
        $lFac = new CJob_Fac($this -> mSrc);
        $lMod = $lFac -> getMod($lJobId);
        $lMod -> forceUpdate($lUpd);
      }
    }

    $lFac = new CJob_Fac($this -> mSrc, $lJobId);
    $lJob = $lFac -> getDat();
    $lWebstatus = $lJob['webstatus'];

    $lArr = array('jobid');
    $lMod = new CJob_Pro_Sub_Mod();
    foreach ($lJob as $lKey => $lVal3) {
      if (in_array($lKey, $lArr)) continue;
      $lMod -> setVal($lKey, $lVal3);
    }
    $lMod -> setVal('wiz_id', 3);
    $lMod -> setVal('pro_id', $lProId);
    $lMod -> setVal($lColoumnName, $lJobId);

    // Before INSERT to al_job_sub_x , werden die Daten aus Projekt uebernommen.
    // JobId #22942: Alle Deadlines aus Projekt wierden im Projekt Item uebernommen.

    $lJobUpd = Array();
    //Projektfelder, die im Job uebernommen werden soll,werden aus config gelesen
    $lCnfArr = CCor_Cfg::getFallback('job-pro.fields.onassign', 'job-pro.fields', array());

    $lJobUpd['pro_id'] = $lProId;

    // Get Projekt or if is set ProjektItem Daten to take on.
    if ($lProItemId){
      // Get Daten from ProjektItem
      $lQry = new CCor_Qry('SELECT * FROM al_job_sub_'.intval(MID).' WHERE id ='.$lProItemId);
    } else {
      // Get Daten from Projekt
      $lQry = new CCor_Qry('SELECT * FROM al_job_pro_'.intval(MID).' WHERE id ='.$lProId);
    }
    $lRow = $lQry-> getAssoc();

    //Felder werden im Job uebernommen.
    foreach ($lCnfArr as $lKey) {
      if (isset ($lRow[$lKey])) {
        // Feld ist im Projekt definiert. Inhalt wird uebernommen.
        $lJobUpd[$lKey] = $lRow[$lKey];
        // Feld wird im Projekt Item uebernommen.
        $lMod -> setVal($lKey, $lRow[$lKey]);
      } else {
        // Feld ist nicht im Projekt definiert. Leeres Inhalt wird uebernommen.
        $lJobUpd[$lKey] = '';
      }
    }

    $lGetDdlFromProject = CCor_Cfg::get('job-pro.fields.pro2item.ddl', true);
    if ($lGetDdlFromProject && (!empty($lRow))) {
      // Alle Deadlines werden in Projekt Item uebernommen.
      foreach ($lRow as $lKey => $lVal) {
        if (substr($lKey,0,4) == 'ddl_') {
          // Alle Deadlines im ProjektItems uebernommen.
          $lMod -> setVal($lKey, $lVal);
        }
      }
    }

    /*
     * Entweder wird ein neu ProjektItem angelegt oder
    * zu einer bestehende ProjektItem hinzugefuegt.
    *
    */
    if (!$lProItemId) {
      // Add new Project Item
      $lMod -> insert();
      $lSubId = $lMod -> getInsertId();
    } else {
      //Target job src id is free. It means, add in existin Project Item.
      $lMod -> insertInPrjItem($lJobId,$lProItemId, $this -> mSrc);
      $lSubId = $lProItemId;
      $lAddInProjectItem = TRUE;
    }

    // zugeordnete Projektdaten werden im Job ueberschrieben.
    $lMod = $lFac -> getMod($lJobId);
    $lMod -> forceUpdate($lJobUpd);

    //22651 Project Critical Path Functionality
    if ($lFromProId AND 0 < $lFromSubId) {
      $lMod -> updateProjectStatusInfo($lJobId, $lProId, $lSubId, $lFromProId, $lFromSubId);
    } else {
      $lMod -> insertIntoProjectStatusInfo($lJobId, $lProId, $lSubId, $lWebstatus);
    }
    $this -> redirect('index.php?act=job-'.$this->mSrc.'.edt&jobid='.$lJobId.'&page='.$lPag);
  }

  protected function actAssignskusub() {
    $lJobId = $this -> getReq('jobid');

    $lFac = new CJob_Fac($this -> mSrc,$lJobId);
    $lJob = $lFac->getDat();

    $lHdr = $lFac->getHeader();
    $lRet = $lHdr ->getContent();

    $lVie = new CJob_Assign($this -> mSrc, $lJobId, true);
    $lRet.= $lVie->getCont();
    $this -> render($lRet);
  }

  protected function actSassignskusub() {
    $lJobId = $this -> getReq('jobid');
    $lSKUId = $this -> getReqInt('skuid');
    $lPag = $this -> getReq('page', 'job');

    $lResult = CCor_Qry::getStr('SELECT * FROM al_job_sku_sub_'.intval(MID).' WHERE sku_id='.esc($lSKUId).' AND job_id='.esc($lJobId));
    if (!$lResult) {
      CCor_Qry::exec('INSERT IGNORE INTO al_job_sku_sub_'.MID.' (sku_id, job_id, src) VALUES ("'.$lSKUId.'", "'.$lJobId.'", "'.$this -> mSrc.'")');
    }

    $this -> redirect('index.php?act=job-'.$this -> mSrc.'.edt&jobid='.$lJobId.'&page='.$lPag);
  }

  protected function actPrn() {
    $lJobId = $this -> getReq('jobid');

    $lFac = new CJob_Fac($this -> mSrc, $lJobId);
    $lJob = $lFac -> getDat();

    $lRet = '';
    $lHdr = $lFac -> getHeader();
    $lHdr -> hideMenu();
    $lRet.= $lHdr -> getContent().BR;

    $lVie = new CJob_Print($this -> mSrc, $lJob, 'job', $lJobId);
    $lRet.= $lVie -> getContent();

    $lPag = new CUtl_Page();
    $lPag -> setPat('pg.cont', $lRet);
    $lPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));
    $lPag -> setPat('pg.title', htm(lan('job-art.menu')));
    $lPag -> setPat('pg.js', '<script type="text/javascript">jQuery(document).ready(function(){window.print();jQuery("#ui-datepicker-div").remove();})</script>');

    echo $lPag -> getContent();
    exit;
  }

  protected function actWebcenterTemplate() {
    $lJobid = $this -> getReq('jobid');
    $lTpl = CApi_Wec_WebcenterTemplate::getTemplate($lJobid);
    echo '<pre>';
    echo 'Webcenter template name: '.var_export($lTpl, true);
    echo '</pre>';
  }

  protected function actJfl() {
    $lJobId = $this -> getReq('jobid');
    $lFlag = $this -> getInt('flag');

    $lAbs = abs($lFlag);
    $lJfl = CCor_Res::extract('val', 'name_'.LAN, 'jfl');

    $lJflSet = Array(); // set_de or set_en from al_jfl
    $lJflReset = Array(); // reset_de or reset_en from al_jfl
    $lJflSet = CCor_Res::extract('val', 'set_'.LAN,'jfl');
    $lJflReset = CCor_Res::extract('val', 'reset_'.LAN,'jfl');

    if ($lFlag < 0 AND isset($lJflReset[$lAbs])) {
      $lCap = $lJflReset[$lAbs];
    } elseif ($lFlag > 0 AND isset($lJflSet[$lAbs])) {
      $lCap = $lJflSet[$lAbs];
    }

    $lFrm = new CHtm_Form('job-'.$this -> mSrc.'.sjfl', $lCap, 'job-'.$this -> mSrc.'.edt&jobid='.$lJobId);
    $lFrm -> setAtt('style', 'width:700px');
    $lFrm -> setParam('val[subject]', $lCap);
    $lFrm -> setParam('jobid', $lJobId);
    $lFrm -> setParam('flag', $lFlag);
    $lFrm -> addDef(fie('msg', lan('lib.msg'), 'memo', NULL, array('style' => 'width:400px;', 'rows' => '18')));

    $this -> render($lFrm);
  }

  protected function actSjfl() {
    $lJobId = $this -> getReq('jobid');
    $lFla = $this -> getInt('flag');
    $lVal = $this -> getReq('val');

    $lSub = $lVal['subject'];
    $lMsg = $lVal['msg'];

    $lFac = new CJob_Fac($this -> mSrc, $lJobId);
    $lMod = $lFac -> getMod($lJobId);
    if ($lFla < 0) {
      $lMod -> resetFlag(-$lFla, $lMsg);

      if ($lFla == (jfOnhold * -1)) {
        $lCrp = CCor_Res::getByKey('code', 'crpmaster');
        if (isset($lCrp[$this -> mSrc])) {
          $lRow = $lCrp[$this -> mSrc];
          $lEve = $lRow['eve_continue'];
          if (!empty($lEve)) {
            $lFac = new CJob_Fac($this -> mSrc, $lJobId);
            $lDat = $lFac -> getDat();

            $lResult = CCor_Qry::getStr('SELECT flag_continue_reason FROM al_job_shadow_'.MID.' WHERE jobid="'.addslashes($lJobId).'";');
            $lReason = array('add' => array('reason' => $lResult));

            $lEve = new CJob_Event($lEve, $lDat, $lReason);
            $lEve -> execute();
          }
        }
      } elseif ($lFla == (jfCancelled * -1)) {
        $lCrp = CCor_Res::getByKey('code', 'crpmaster');
        if (isset($lCrp[$this -> mSrc])) {
          $lRow = $lCrp[$this -> mSrc];
          $lEve = $lRow['eve_revive'];
          if (!empty($lEve)) {
            $lFac = new CJob_Fac($this -> mSrc, $lJobId);
            $lDat = $lFac -> getDat();

            $lResult = CCor_Qry::getStr('SELECT flag_revive_reason FROM al_job_shadow_'.MID.' WHERE jobid="'.addslashes($lJobId).'";');
            $lReason = array('add' => array('reason' => $lResult));

            $lEve = new CJob_Event($lEve, $lDat, $lReason);
            $lEve -> execute();
          }
        }
      }
    } else {
      $lMod -> setFlag($lFla, $lMsg);

      if ($lFla == jfOnhold) {
        $lCrp = CCor_Res::getByKey('code', 'crpmaster');
        if (isset($lCrp[$this -> mSrc])) {
          $lRow = $lCrp[$this -> mSrc];
          $lEve = $lRow['eve_onhold'];
          if (!empty($lEve)) {
            $lFac = new CJob_Fac($this -> mSrc, $lJobId);
            $lDat = $lFac -> getDat();

            $lResult = CCor_Qry::getStr('SELECT flag_onhold_reason FROM al_job_shadow_'.MID.' WHERE jobid="'.addslashes($lJobId).'";');
            $lReason = array('add' => array('reason' => $lResult));

            $lEve = new CJob_Event($lEve, $lDat, $lReason);
            $lEve -> execute();
          }
        }
      } elseif ($lFla == jfCancelled) {
        $lCrp = CCor_Res::getByKey('code', 'crpmaster');
        if (isset($lCrp[$this -> mSrc])) {
          $lRow = $lCrp[$this -> mSrc];
          $lEve = $lRow['eve_cancel'];
          if (!empty($lEve)) {
            $lFac = new CJob_Fac($this -> mSrc, $lJobId);
            $lDat = $lFac -> getDat();

            $lResult = CCor_Qry::getStr('SELECT flag_cancel_reason FROM al_job_shadow_'.MID.' WHERE jobid="'.addslashes($lJobId).'";');
            $lReason = array('add' => array('reason' => $lResult));

            $lEve = new CJob_Event($lEve, $lDat, $lReason);
            $lEve -> execute();
          }
        }
      }
    }

    $lMod -> addHistory(htStatus, $lSub, $lMsg);

    $lArr = array(jfOnhold, '-'.jfOnhold, jfCancelled, '-'.jfCancelled);
    if (in_array($lFla, $lArr)) {
      $lUpd = array('last_status_change' => date('Y-m-d H:i:s'));
      $lMod -> forceUpdate($lUpd);
    }
    $this -> redirect('index.php?act=job-'.$this -> mSrc.'.edt&jobid='.$lJobId);
  }

  /**
   * Import the Dates from Assigned Job
   *
   * @return unknown_type
   */
  protected function actSetAssignedProDat(){
    $lJobId = $this -> getReq('jobid'); // Job ID
    $lSrc = $this -> getReq('src'); // Source
    $lProId = $this -> getReq('proid'); // Assigned Pro Id
    // Fields to update
    $lJobUpd = Array();

    // Get Job Informationen
    $lFac = new CJob_Fac($lSrc, $lJobId);
    $lJob = $lFac -> getDat();



    //Projektfelder, die im Job uebernommen werden soll,werden aus config gelesen
    $lCnfArr = CCor_Cfg::get('job-pro.fields', array());

    $lSql = 'SELECT * from al_job_pro_'.intval(MID).' WHERE id ="'.$lProId.'"';
    $lQry = new CCor_Qry($lSql);

    // Get Project Daten
    $lRow = $lQry -> getAssoc();

    foreach ($lCnfArr as $lKey){
      if (isset ($lRow[$lKey])){
        // Feld ist im Projekt definiert. Inhalt wird uebernommen.
        $lJobUpd[$lKey] = $lRow[$lKey];
      }else {
        $lJobUpd[$lKey] = '';
      }
    }


    if (!empty($lJobUpd)){
      // Update Job
      $this -> dump($lJobUpd);
      $lMod = $lFac -> getMod($lJobId);
      $lMod -> forceUpdate($lJobUpd);
    }
    $this -> redirect('index.php?act=job-'.$lSrc.'.edt&jobid='.$lJobId);
  }

  ######################################################################################

  // Diese Funktion gibt das gleiche Ergebnis wie CApp_Apl_Loop::isFlagConfirmed, aber direkt aus den Jobfields!
  public function isFlagConfirmed($aJob) {//used in job/form & job/bar
    $lCrpFlags = array();
    $lAllFlags = CCor_Res::get('fla');
    if (!empty($lAllFlags)) {
      foreach ($lAllFlags as $lF => $lFlagEve) {
        if (!empty($lFlagEve['alias']) AND isset($aJob[ $lFlagEve['alias'] ]) AND FLAG_STATE_CLOSED > $aJob[ $lFlagEve['alias'] ]) {
          if (FLAG_STATE_CONFIRMED == $aJob[ $lFlagEve['alias'] ]) {
            $lCrpFlags[$lF] = TRUE;
          } elseif (FLAG_STATE_ACTIVATE == $aJob[ $lFlagEve['alias'] ]) {
            $lCrpFlags[$lF] = FALSE;
          }
        }
      }
    }
    #echo '<pre>---isFlagConfirmed---'.get_class().'---';var_dump($aJob,$lAllFlags,$lCrpFlags,'#############');echo '</pre>';
    return $lCrpFlags;
  }

  public function actStopFlag() { //used in job/form
    $lJobId = $this -> getReq('jobid'); // Job ID
    $lStepId = $this -> getReq('sid');

    $lClass = 'CJob_'.$this -> mSrc.'_Step';
    $lStepClass = new $lClass($lJobId);
    $lNoStop4Crp = $lStepClass -> StopFlags($lStepId);// NoStop4Crp == Flags could be stopped

    $this -> redirect('index.php?act=job-'.$this -> mSrcCnt.'.edt&jobid='.$lJobId);
  }

  protected function actGetrevdlg() {
    $lJid = $this->getReq('jid');
    $lPrefix = $this->getReq('prefix');
    $lHidePos = (bool)$this->getReq('pos', false);
    $lHideDesc = (bool)$this->getReq('desc', true);

    $lDlg = new CJob_Apl_Adduserdialog($this->mSrc, $lJid, $lPrefix, !$lHidePos, $lHideDesc);
    echo $lDlg->getContent();
  }

  protected function selectAplDialog($aType = 'apl') {
    $lJobId = $this -> getReq('jobid');
    $lStep  = $this -> getInt('sid'); // Step_Id

    $lAddUser = $this -> getReq('addUser', FALSE);

    $lClass = 'CJob_'.$this -> mSrcCnt.'_Dat';
    $lJob = new $lClass();
    $lJob -> load($lJobId);

    $lUsr = CCor_Usr::getInstance();
    $lUsr->setPref('apl.job', serialize($lJob->toArray()));

    $lClass = 'CJob_'.$this -> mSrcCnt.'_Header';
    $lHdr = new $lClass($lJob);
    $lRet = $lHdr -> getContent();

    $lObj = new CCust_Job_Apl_Preview($lJobId);
    $lObj->killSession();

    $lApl = new CApp_Apl_Loop($this -> mSrcCnt, $lJobId, $aType, MID, $lJob['webstatus']);
    $lAid = $lApl->getLastLoop();
    if (!empty($lAid)) {
      $lSql = 'SELECT event_id, event_prefix FROM al_job_apl_loop_events WHERE loop_id='.$lAid;
      $lQry = new CCor_Qry($lSql);
      $lEveIds = array();
      foreach ($lQry as $lRow) {
        $lEveIds[$lRow['event_prefix']] = $lRow['event_id'];
      }
    }

    $lDlg = new CJob_Dialog($this -> mSrcCnt, $lJobId, $lStep, $lJob, $lAddUser, $aType);


    $lFrm = new CJob_Apl_List($this -> mSrcCnt, $lJobId, 'job', false);
    $lFrm -> mTitle = lan('job-apl.menu.old');
    $lDlg->prependForm($lFrm -> getContent());

    $lDlg->setAct('job-'.$this->mSrc.'.cnf');
    $lDlg->setParam('act', 'job-'.$this->mSrc.'.cnf');
    $lDlg->mApl_Notify = false;
    if (!empty($lEveIds)) {
      $lDlg->setCountryEventIds($lEveIds);
    }
    $lLoop = new CApp_Apl_Loop($this->mSrcCnt, $lJobId, $aType);
    if ($lLoop->hasCompletedLoop($aType)) {
      $lDlg->setCanDeselectCountry(true);
    }
    $lDlg->addCountryAplSelections($aType);

    $lRet.= $lDlg -> getContent();

    $this->render($lRet);
  }

  protected function getKeyCleanReq($aKey) {
    $lRet = array();
    $lReq = $this -> getReq($aKey);
    foreach ($lReq as $lKey => $lVal) {
      $lRet[base64_decode($lKey)] = $lVal;
    }
    return $lRet;
  }

  protected function getDynamicTotalAplDdl($aEventsIds, $aPosMax = FALSE) {
    $lEvents = CCor_Res::get('action');
    foreach ($aEventsIds as $lKey => $lVal) {
      $lEvenstInfo = CEve_Act_Cnt::countDurationTime($lEvents[$lVal]);
      $lEvenstDuration[$lVal] = $lEvenstInfo['val']; // Return the Max duration for the complete Workflow
      if ($aPosMax) {
        $lEvenstDuration[$lVal] = $lEvenstInfo['all']; // Return the Max duration for each position
        unset($lEvenstDuration[$lVal][0]);
      }
    }
    return $lEvenstDuration;
  }

  protected function getDynamicPersonDdl($aPosMaxDuration, $aPos, $aDur) {
    $lPosMaxDuration = $aPosMaxDuration;
    foreach ($aPosMaxDuration as $lKey => $lVal) {
      if ($lKey >= $aPos) unset($lPosMaxDuration[$lKey]);
    }
    $lSum = array_sum($lPosMaxDuration)+$aDur;
    $lPersonalDdl = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + $lSum, date('Y')));
    return $lPersonalDdl;

  }

  protected function fixAplPositions($aNot, $aCheck, $aCountry) {
    // fix positions (e.g. if a group in position 1 is deselected)
    // unserialize and remove deselected groups
    $lNot = array();
    foreach ($aNot as $lRowKey => $lRow) {
      if ($aCheck) {
      if (!isset($aCheck[$aCountry][$lRowKey])) {
        continue;
      }
      }
      $lRow = unserialize($lRow);
      $lNot[$lRowKey] = $lRow;
    }
    if (empty($lNot)) {
      return $lNot;
    }

    if ($aCheck) {
      // generate a map oldPos => newPos
      $lOld = -1; // dummy so we get a first map entry on first row
      $lLast = 0;
      $lMap = array();
      $lMap[EVENT_DEFER_POSITION] = EVENT_DEFER_POSITION;
      foreach ($lNot as $lRowKey => $lRow) {
        $lPos = $lRow['pos'];
        if ($lPos == EVENT_DEFER_POSITION) {
          continue;
        }
        if ($lOld != $lPos) {
          $lMap[$lPos] = $lLast;
          $lLast++;
          $lOld = $lPos;
        }
      }

      // remap the notifications
      $lNewNot = array();
      foreach ($lNot as $lRowKey => $lRow) {
        $lPos = $lRow['pos'];
        $lNewPos = $lMap[$lPos];
        $lRow['pos'] = $lNewPos;
        $lNewNot[$lRowKey] = $lRow;
      }
      $lNot = $lNewNot;
    }
    return $lNot;
  }

  protected function confirmNewAplDialog($aType = 'apl') {
    $lSid = $this -> getInt('sid');
    $lJobId = $this -> getReq('jobid');

    $lAddUser = $this -> getReq('addUser', FALSE);

    $lAdd = array();
    $lVal = $this -> getReq('val');
    $lMsg = (isset($lVal['msg'])) ? $lVal['msg'] : '';
    $lMsgRec = array('body' => $lMsg);

    $lFac = new CJob_Fac($this -> mSrcCnt, $lJobId);
    $lJob = $lFac -> getDat();
    $lEve = $this -> getKeyCleanReq('eve');
    $lNotify = $this -> getKeyCleanReq('apl_eve');
    $lCheck  = $this -> getKeyCleanReq('apl_chk');

    $lEvenstMaxDuration = $this -> getDynamicTotalAplDdl($lEve);
    $lEvenstPosMAxDuration = $this -> getDynamicTotalAplDdl($lEve, TRUE);
    $lTotalTime = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + max($lEvenstMaxDuration), date('Y')));
    $lVal['ddl_korrekturumlauf'] = $lTotalTime;

    $lDat = new CCor_Date();
    $lDat -> setInp($lVal['ddl_korrekturumlauf']);

    $lAplType = new CApp_Apl_Type($aType);
    $lGetAplMode = $lAplType->get('apl_mode');
    //$lGetAplMode = CCor_Qry::getInt('SELECT apl_mode from al_apl_types WHERE mand='.MID.' AND code='.esc($aType).' LIMIT 1;');
    $lAplMode = (!empty($lGetAplMode)) ? $lGetAplMode : '1';

    $lDdl = $lDat -> getSql();
    $lJobObj = new CApl_Job($this -> mSrcCnt, $lJobId);

    if ($lAddUser){
      // Add User or Group to already opened Loop
      // No need to create a new Loop. Get last open Loop Id instead
      $lAplObj = $lJobObj->getLastLoop($aType);
    } else {
      $lAplObj = $lJobObj->insertLoop($lSid, $aType, $lDdl, $lAplMode);
    }
    $lAid = $lAplObj['id'];

    $lUseSubLoops = $lAplType->usesSubLoops();
    $lAdd['apl_id'] = $lAid;

    $lQry = new CCor_Qry('SELECT mem.uid, mem.gid, mem.mand FROM al_usr_mem mem, al_usr u where mem.uid=u.id and u.del != "Y"');
    foreach ($lQry as $lRow) {
      $lMem[$lRow['gid']][$lRow['uid']] = $lRow['uid'];
    }
    $lNames = CCor_Res::extract('id', 'fullname', 'usr');

    $lRet = '';

    $lQry = new CCor_Qry('SELECT * FROM al_crp_step WHERE id='.$lSid);
    $lRow = $lQry->getDat();
    $lStepName = $lRow['name_'.LAN];

    // START #264: User set in multiple workflows
    $lMultipleWorkflows = CCor_Cfg::get('job.apl.multipleworkflows', true);
    $lMultipleWorkflowsGroups = array();
    // STOP #264: User set in multiple workflows

    $lEmailIds = array();

    $lParent = $lAplObj;
    foreach ($lEve as $lCountry => $lEventId) {

      if ($lUseSubLoops) {
      	$lParent = $lAplObj->insertSubLoop($lCountry);
      }
      $lAllAct = array();

      $this -> dbg('Country '.$lCountry.' Event '.$lEventId);
      $lNum = 0;

      $lNot = $lNotify[$lCountry];
      $this->dump($lNot, 'Country Notify');
      if (empty($lNot)) continue;

      $lNot = $this->fixAplPositions($lNot, $lCheck, $lCountry);
      $this->dump($lNot, 'Country Notify');

      foreach ($lNot as $lRowKey => $lRow) {
        if ($lRow['pos'] == EVENT_DEFER_POSITION) {
          continue;
        }
        $lLine = array();
        $lParams = $lRow['param'];
        $lTyp = $lRow['typ'];
        if ($lTyp == 'email_gru' || $lTyp == 'email_gruasrole') {
          if($lTyp == 'email_gruasrole'){
            $lRolFie = $lParams['sid'];
            $lParams['sid'] = $lJob[$lRolFie];
          }
          $lGroupId = $lParams['sid'];

          // START #264: User set in multiple workflows
          if (!$lMultipleWorkflows) {
            if (!in_array($lGroupId, $lMultipleWorkflowsGroups)) {
              array_push($lMultipleWorkflowsGroups, $lGroupId);
            } else {
              continue;
            }
          }
          // STOP #264: User set in multiple workflows

          $lRec = array();
          $lRec['pos'] = $lRow['pos'];
          $lRec['dur'] = $lRow['dur'];
          $lRec['typ'] = $aType;
          //$lRec['tpl'] = $lParams['tpl'];

          $lRec['confirm'] = $lParams['confirm'];
          $lRec['prefix'] = $lCountry;
          $lRec['ddl'] = $this -> getDynamicPersonDdl($lEvenstPosMAxDuration[$lEventId], $lRow['pos']+1, $lRow['dur']);
          $lRec['inv'] = $lParams['inv'];
          $lRec['task'] = $lParams['task'];

          $lAllUsr = array();
          $lMembers = $lMem[$lGroupId];
          if (empty($lMembers)) continue;

          //is select members on for email to group as role
          if($lTyp == 'email_gruasrole' && $lParams['members'] == "on"){
            // unset all member which aren't checked in 'Email Notification' section of dialog
            $lAct = $this -> getReq('act_new');
            $lEveId = $lRow['id'];
            foreach ($lMembers as $lUid) {
              if(!array_key_exists($lEveId."-".$lUid, $lAct[0]))
                unset($lMembers[$lUid]);
            }
          }

          foreach ($lMembers as $lUid) {
            $lRec['user_id'] = $lUid;
            $lRec['uid'] = $lUid;
            $lRec['name'] = $lNames[$lUid];
            $lRec['gru_id'] = $lGroupId;

            $lStateRec = $lRec;
            $lStateRec['pos'] = $lStateRec['pos'] +1;

            //$lAplStateId = $lApl->addItem($lUid, $lName, $lRec);

            $lStateObj = $lParent->insertState($lStateRec);
            $lAplStateId = $lStateObj['id'];

            // send mail
            $lUsrRec = $lRec;

            $lUsrRec['tpl'] = $lParams['tpl'];
            $lUsrRec['apl_id'] = $lAplStateId;
            $lUsrRec['gru'] = $lGroupId;
            $lAllUsr[] = $lUsrRec;

            $lSender = new CApp_Sender('email_gru', $lUsrRec, $lJob, $lMsgRec);
            $lSender -> setMailType(mailAplInvite);
            $lSent = $lSender -> sendItem($lUid, $lRow['pos'], $lAplStateId, $lMembers);
            if (!empty($lSent)) {
              $lEmailIds = array_merge($lEmailIds, $lSent);
            }
          }
        } else if ($lTyp == 'email_usr') {

          $lRec = array();
          $lUid = $lParams['sid'];
          $lRec['pos'] = $lRow['pos'];
          $lRec['dur'] = $lRow['dur'];
          $lRec['typ'] = $aType;
          $lRec['tpl'] = $lParams['tpl'];
          $lRec['usr'] = $lUid;
          $lRec['prefix'] = $lCountry;
          $lRec['ddl'] = $this -> getDynamicPersonDdl($lEvenstPosMAxDuration[$lEventId], $lRow['pos']+1, $lRow['dur']);
          $lRec = $lRec;
          $lRec['uid'] = $lUid;
          $lRec['inv'] = $lParams['inv'];
          $lRec['task'] = $lParams['task'];

          $lName = $lNames[$lUid];
          //var_dump($lParams); exit;

          $lStateRec = $lRec;
          $lStateRec['pos'] = $lStateRec['pos'] +1;
          $lStateRec['user_id'] = $lUid;
          $lStateRec['name'] = $lNames[$lUid];
          unset($lStateRec['tpl']);
          unset($lStateRec['usr']);

          $lStateObj = $lParent->insertState($lStateRec);
          $lAplStateId = $lStateObj['id'];

          $lRec['apl_id'] = $lAplStateId;
          $lAllUsr[] = $lRec;

          $lSender = new CApp_Sender('email_usr', $lRec, $lJob, $lMsgRec);
          $lSender -> setMailType(mailAplInvite);
          $lSent = $lSender->sendItem($lUid, $lRow['pos'], $lAplStateId);
          if (!empty($lSent)) {
            $lEmailIds = array_merge($lEmailIds, $lSent);
          }
        } else if ($lTyp == 'email_rol') {

          $lRec = array();
          $lRole = $lParams['sid'];
          $lUid = $lJob[$lRole];

          if (!empty($lUid)) {
            $lRec['pos'] = $lRow['pos'];
            $lRec['dur'] = $lRow['dur'];
            $lRec['typ'] = $aType;
            $lRec['tpl'] = $lParams['tpl'];
            $lRec['usr'] = $lUid;
            $lRec['prefix'] = $lCountry;
            $lRec['ddl'] = $this -> getDynamicPersonDdl($lEvenstPosMAxDuration[$lEventId], $lRow['pos']+1, $lRow['dur']);
            $lRec['confirm'] = 'one';
            $lRec['inv'] = $lParams['inv'];
            $lRec['task'] = $lParams['task'];

            $lRec['uid'] = $lUid;
            $lRec['user_id'] = $lUid;

            $lName = $lNames[$lUid];

            $lStateRec = $lRec;
            $lStateRec['pos'] = $lStateRec['pos'] +1;
            $lStateRec['name'] = $lNames[$lUid];
            unset($lStateRec['tpl']);
            unset($lStateRec['usr']);

            $lStateObj = $lParent->insertState($lStateRec);
            $lAplStateId = $lStateObj['id'];

            $lSender = new CApp_Sender('email_rol', $lRec, $lJob, $lMsgRec);
            $lSender -> setMailType(mailAplInvite);
            $lSent = $lSender->sendItem($lUid, $lRow['pos'], $lAplStateId);
            if (!empty($lSent)) {
              $lEmailIds = array_merge($lEmailIds, $lSent);
            }
          }
        }

        $lKey = getNum('') .'.'.$lParams['tpl'];
        #$lKey = $lRow['id'].'.'.$lParams['tpl'];
        $lAllAct[$lKey] = array('U'.$lNum => $lAllUsr);
        $lNum++;
      }

      $lAdd['special_apl_usr'] = $lAllAct;
      $lAdd['tablekeyid'] = 1;
      $lAdd['mails_ids'] = $lEmailIds;

      $lSql = 'INSERT INTO `al_job_apl_loop_events` (`datum`, `uid`, `mand`, `jobid`, `loop_id`, `event_id`, `event_prefix`) VALUES ('.esc(date('Y-m-d')).','.$this -> mUsr->getAuthId().','.MID.','.esc($lJobId).','.$lAid.', '.esc($lEventId).', '.esc($lCountry).')';
      CCor_Qry::exec($lSql);
    }

    $lMsgArr = array('subject' => $lStepName, 'body' => $lMsg, 'add' => $lAdd);

    $lObj = new CCust_Job_Apl_Preview($lJobId);
    #$lObj->killSession();

    $lClass = 'CJob_'.$this -> mSrcCnt.'_Step';
    $lStepClass = new $lClass($lJobId, $lJob);
    $lHasStepped = $lStepClass -> doStep($lSid, $lMsgArr, $lAdd);

    $this -> redirect($this -> getStdUrl().'.edt&jobid='.$lJobId);
  }

  protected function actDelaction() {
    $lJid    = $this->getReq('jid');
    $lPrefix = $this->getReq('prefix');
    $lHash   = $this->getReq('hash');

    $lObj = new CJob_Apl_Preview($lJid);

    $lUsr = CCor_Usr::getInstance();
    $lJob = $lUsr->getPref('apl.job');
    try{
      $lJob = unserialize($lJob);
      $lObj->setJob($lJob);
    }catch(Exception $lExc){}

    $lObj->loadFromSession($lPrefix);
    $lObj->deleteAction($lPrefix, $lHash);
    $lObj->saveToSession($lPrefix);
    echo $lObj->getContent();
  }

  protected function actNew() {
    $lFac = new CJob_Fac($this->mSrc);
    $lVie = $lFac->getTabs();
    $lRet = $lVie -> getContent();

    $lFrm = $lFac->getForm('job-'.$this->mSrc.'.snew');
    $lPro = $this->getInt('pro_id');
    if (!empty($lPro)) {
      $lFrm->setParam('val[pro_id]', $lPro);
      $lFrm->setParam('pro_id', $lPro);
      $lFrm->setParam('AssignedProId', $lPro);
    }
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actSnew() {
    $lPag = $this -> getReq('page', 'job');

    $lAssignedProId = $this -> getReqInt('AssignedProId');
    $lAssignedProItemId = $this -> getReqInt('AssignedProItemId');

    $lOrigSrc = $this -> getReq('orig_src', NULL);
    $lOrigJobID = $this -> getReq('orig_jobid', NULL);
    $lOrigWecPrjID = $this -> getReq('orig_wecprjid', NULL);

    $lObj = 'CJob_'.$this -> mSrc.'_Mod';
    $lMod = new $lObj();
    $lMod -> getPost($this -> mReq);

    $lMod -> setVal('orig_src', $lOrigSrc);
    $lMod -> setVal('orig_jobid', $lOrigJobID);
    $lMod -> setVal('orig_wecprjid', $lOrigWecPrjID);

    if ($lMod -> insert()) {
      $lJobId = $lMod -> getInsertId();

      $this -> afterInsert($lMod);

      if ($lAssignedProId != FALSE){ // After Save, the job will be  assigned to Project.
        $this -> dbg('Job is assigned to ProjectId '.$lAssignedProId);
        $lUrl = 'index.php?act=job-'.$this -> mSrc.'.sassignprj&jobid='.$lJobId.'&pid='.$lAssignedProId;
        if ($lAssignedProItemId){
          //Job wird an Projekt Item zugeordnet, weil in der ProjektItem der Spalte "jobid_[jobtyp]" frei ist.
          $lUrl.='&prjitmid='.$lAssignedProItemId;
        }
        $this -> redirect($lUrl); // Send neu Job zu Projekt Zuordnung.
      } else {
        $this -> redirect('index.php?act=job-'.$this -> mSrc.'.edt&jobid='.$lJobId.'&page='.$lPag);
      }
    }

    $this -> redirect('index.php?act=job-'.$this -> mSrc.'.new');
  }

  protected function actSedt() {
    $lCid = $this -> getReq('clientid');
    self::checkMand($lCid);

    $lJobId = $this -> getReq('jobid');
    $lStp = $this -> getReq('step');
    $lPag = $this -> getReq('page', 'job');
    $lRequestedWebStatus = $this -> getReq('webstatus');

    $lObj = 'CJob_'.$this -> mSrcCnt.'_Mod';
    $lMod = new $lObj($lJobId);
    $lOld = $this->getReq('old');
    $lMod -> getPost($this -> mReq, !empty($lOld));

    if ($lMod -> update()) {
      $this -> afterUpdate($lMod);
      if ('pro' != $this -> mSrc) {
        $this -> updateProjectItem($lJobId, $lMod->getUpdate());
      }
      if ($lStp > 0){
        $this -> redirect('index.php?act=job-'.$this -> mSrcCnt.'.step&sid='.$lStp.'&jobid='.$lJobId.'&webstatus='.$lRequestedWebStatus);
      } else {
        $lBeat = new CJob_Workflow_Heartbeat($this -> mSrcCnt, $lJobId);
        $lBeat->heartBeat();
      }
    }
    $this -> redirect('index.php?act=job-'.$this -> mSrcCnt.'.edt&jobid='.$lJobId.'&page='.$lPag);
  }

  protected function actStepindependent() {
    $lCid = $this -> getReq('clientid');
    self::checkMand($lCid);

    $lJobId = $this -> getReq('jobid');
    $lStp = $this -> getReq('step');
    $lPag = $this -> getReq('page', 'job');

    $lObj = 'CJob_'.$this -> mSrcCnt.'_Mod';
    $lMod = new $lObj($lJobId);
    $lOld = $this->getReq('old');
    $lMod -> getPost($this -> mReq, !empty($lOld));

    if ($lMod -> update()) {
      $this -> afterUpdate($lMod);
      if ('pro' != $this -> mSrc) {
        $this -> updateProjectItem($lJobId, $lMod->getUpdate());
      }
      $lBeat = new CJob_Workflow_Heartbeat($this -> mSrcCnt, $lJobId);
      $lBeat->heartBeat();

      $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
      $lCrpId = $lCrp[$this -> mSrcCnt];
      $lCrpSteps = CCor_Res::get('crpstep', $lCrpId);
      $lCrpStep = $lCrpSteps[$lStp];
      $lFla = intval($lCrpStep['flags']);

      if (bitset($lFla, sfComment)) {
        $this -> redirect('index.php?act=job-'.$this -> mSrcCnt.'.stepindependentdialog&jobid='.$lJobId.'&sid='.$lStp);
      } else {
        $this -> redirect('index.php?act=job-'.$this -> mSrcCnt.'.stepindependentcnf&jobid='.$lJobId.'&sid='.$lStp);
      }
    }
    $this -> redirect('index.php?act=job-'.$this -> mSrcCnt.'.edt&jobid='.$lJobId.'&page='.$lPag);
  }

  protected function actStepindependentdialog() {
    $lJobId = $this -> getReq('jobid');
    $lStp = $this -> getInt('sid');

    $lClass = 'CJob_'.$this -> mSrcCnt.'_Dat';
    $lJob = new $lClass();
    $lJob -> load($lJobId);

    $lClass = 'CJob_'.$this -> mSrcCnt.'_Header';
    $lHdr = new $lClass($lJob);
    $lRet = $lHdr -> getContent();

    $lDlg = new CJob_Dialog($this -> mSrcCnt, $lJobId, $lStp, $lJob);
    $lDlg -> setParam('act', 'job-'.$this -> mSrcCnt.'.stepindependentcnf');
    $lRet.= $lDlg -> getContent();

    $this -> render($lRet);
  }

  protected function actStepindependentcnf() {
    $lSid = $this -> getInt('sid');
    $lJobId = $this -> getReq('jobid');

    $lPag = $this -> getReq('page', 'job');
    $lVal = $this -> getReq('val');
    $lMsg = (isset($lVal['msg'])) ? $lVal['msg'] : '';

    $lAdd = array();
    $lFac = new CJob_Fac($this -> mSrcCnt, $lJobId);
    $lJob = $lFac -> getDat();

    $lClass = 'CJob_'.$this -> mSrcCnt.'_Step';
    $lStepClass = new $lClass($lJobId, $lJob);

    if (isset($_FILES['val'])) {
      $lFin = new CApp_Finder($this -> mSrcCnt, $lJobId);
      $lDir = $lFin -> getPath('doc');
      $lUpl = new CCor_Upload();
      $lRes = $lUpl -> uploadIndex('file', $lDir);
      if ($lRes) {
        $lAdd['fil'] = $lRes;
        CCor_Usr::insertJobFile($this -> mSrcCnt, $lJobId, 'doc', $lRes);

        $lArr = $_FILES['val'];
        $lNam = $lArr['name']['file'];
        $lHis = new CApp_His($this -> mSrcCnt, $lJobId);
        $lMsg = sprintf(lan('filupload.success'),$lNam);
        $lHis -> add(htFileupload, lan('filupload.his.msg'), $lMsg);
      }
    }

    // Ignore any email-notifications?
    $lOld_All = $this -> getReq('act_old');
    $lAct_All = $this -> getReq('act_new'); //ToDo: spaeter in Step nachpü�fen, ob $lIgn[$lK] als empty vorhanden ist!
    $lIgn = array();
    #echo '<pre>---cnt.php---'.get_class().'---';var_dump($lOld_All,$lAct_All,'#############');echo '</pre>';
    if (!empty($lOld_All)) {
      foreach ($lOld_All as $lK => $lOld) {
        $lIgn[$lK] = array();
        foreach ($lOld as $lKey => $lVal2) {
          if (!isset($lAct_All[$lK][$lKey])) {
            $lIgn[$lK][] = $lKey;
          }
        }
      }
    }

    $lHasStepped = $lStepClass -> doStepIndependent($lSid, $lMsg, $lAdd);

    if ($lHasStepped) {
      $lBeat = new CJob_Workflow_Heartbeat($this -> mSrcCnt, $lJobId);
      $lBeat->heartBeat();
    }
    $this -> redirect($this -> getStdUrl().'.edt&jobid='.$lJobId.'&page='.$lPag);
  }

  protected function actSedts() {
    $lJobIds = $this -> getReq('jobids');
    $lValues = $this -> getReq('values');

    $lJobIdsJSONDec = json_decode($lJobIds);
    $lValuesJSONDec = json_decode($lValues);

    foreach ($lJobIdsJSONDec as $lJobID => $lSrc) {
      $lDat = new CJob_Dat($lSrc);
      $lDatRes = $lDat -> load($lJobID);

      $lItm = array();
      $lUpd = array();
      foreach ($lValuesJSONDec as $lKey => $lValue) {
        $lItm['old'] = $lDat -> __get($lKey);
        $lItm['new'] = $lValue;
        $lUpd[$lKey] = $lItm;
      }

      $lMod = new CJob_Mod($lSrc, $lJobID);
      $lModRes = $lMod -> forceUpdate($lValuesJSONDec);
      if ($lModRes) {
        CJob_Utl_Shadow::reflectUpdate($lSrc, $lJobID, $lValuesJSONDec);
      }

      $lHis = new CJob_His($lSrc, $lJobID);
      $lHis -> add(htEdit, lan('job.changes'), '', array('upd' => $lUpd));
    }

    return TRUE;
  }

  protected function updateProjectItem($aJobId, $aUpdates) {
    $lBase = 'job-pro.fields.onupdate';
    $lUpdateFields = CCor_Cfg::getFallback($lBase.'-'.$this->mSrc, $lBase, array());
    $lExcludeFields = CCor_Cfg::getFallback($lBase.'-'.$this->mSrc.'.exclude', $lBase.'.exclude');

    $lSql = 'SELECT id FROM al_job_sub_'.intval(MID).' ';
    $lSql.= 'WHERE jobid_'.$this->mSrc.'='.esc($aJobId).' LIMIT 1';
    $lSid = CCor_Qry::getInt($lSql);

    if (empty($lSid)) return;

    $lUpd = $aUpdates;
    $lMod = new CJob_Pro_Sub_Mod();
    $lMod -> forceVal('id', $lSid);
    foreach ($lUpd as $lKey => $lVal) {
      if (is_array($lExcludeFields) && in_array($lKey, $lExcludeFields)) continue;
      if (
        ('*' == $lUpdateFields) ||
        (is_array($lUpdateFields) && in_array($lKey, $lUpdateFields))
      ) {
        $lMod->forceVal($lKey, $lVal);
      }
    }
    $lMod->update();
  }

  protected function getPortalJobId($aJobId, $aCopyFromSrc, $aCopyToSrc) {
    $lSql = 'SELECT id FROM `al_portal_job_ids` WHERE jobid='.esc($aJobId);
    $lId = CCor_Qry::getInt($lSql);
    if ($lId) {
      $lSql = 'SELECT src FROM `al_portal_job_ids` WHERE id='.$lId.' AND src="'.$aCopyToSrc.'"';
      if (CCor_Qry::getStr($lSql)) return NULL;
      return $lId;
    }
    else return NULL;
  }

  protected function actNewsub() {
    $lPid = $this->getInt('pid');
    $lJob = new CJob_Pro_Dat();
    $lJob->load($lPid);

    $lFac = new CJob_Fac($this->mSrc);

    $lVie = $lFac->getTabs();
    $lRet = $lVie->getContent();

    $lFrm = $lFac->getForm('job-'.$this->mSrc.'.snewsub');

    if (!empty($lPid)) {
      $lFrm->setJob($lJob);
      $lFrm->setParam('pid', $lPid);
      $lFrm->addBtn('act', 'Back to Project', 'go("index.php?act=job-pro-sub&jobid=' . $lPid . '")', 'img/ico/16/cancel.gif', 'button', array('class' => 'btn w200'));
    }

    $lRet.= $lFrm->getContent();

    $this->render($lRet);
  }

  protected function actSnewsub() {
    $lPid = $this->getInt('pid');

    $lFac = new CJob_Fac($this->mSrc);

    $lMod = $lFac->getMod();
    $lMod->getPost($this->mReq);

    if($lMod->insert()) {
      $lJobId = $lMod->getInsertId();
      $this->afterInsert($lMod);
      if (!empty($lPid)) {
        $lSubMod = new CJob_Pro_Sub_Mod();
        $lSubMod->getPost($this->mReq);
        $lSubMod->forceVal('pro_id', $lPid);
        $lSubMod->forceVal('jobid_'.$this->mSrc, $lJobId);
        $lSubMod->forceVal('jobid', $lJobId);
        $lSubMod->forceVal('job_id', $lJobId);
        $lSubMod->forceVal('src', $this->mSrc);

        if ($lSubMod->insert()) {
          $lSid = $lSubMod->getInsertId();
          $lMod -> insertIntoProjectStatusInfo($lJobId, $lPid, $lSid);
        }
      }
      $this->redirect('index.php?act=job-'.$this->mSrc.'.edt&jobid=' . $lJobId);
    }
    if (empty($lPid)) {
      $this->redirect('index.php?act=job-'.$this->mSrc);
    } else {
      $this->redirect('index.php?act=job-pro-sub&jobid=' . $lPid);
    }
  }

  public function actGetjobfields() {
    // TODO: make on-the-fly-updates of the joblist
//     $lSrc = $this -> getReq('src');
//     $lJobIDs = $this -> getReq('jobids');
//     $lJobIDsArray = explode(',', $lJobIDs);
//     $lFields = $this -> getReq('fields');
//     $lFieldsArray = json_decode($lFields);
//     $lValues = $this -> getReq('values');
//     $lValuesArray = json_decode($lValues);

//     $lUsr = CCor_Usr::getInstance();
//     $lMod = 'job-'.$lSrc;
//     $lUsrPref = $lUsr -> getPref($lMod.'.cols');
//     if (empty($lUsrPref)) {
//       $lSql = 'SELECT val FROM al_sys_pref WHERE code = "'.$lMod.'.cols'.'" AND mand='.MID;
//       $lUsrPref = CCor_Qry::getArrImp($lSql);
//       $lUsrPref2 = CCor_Qry::getArr($lSql);
//     }

//     $lDef = array();
//     $lQry = new CCor_Qry('SELECT id,mand,src,alias,native,name_en,desc_en,desc_de,name_de,typ,param,attr,feature,learn,avail,flags,used FROM al_fie WHERE mand='.MID.' AND id IN ('.$lUsrPref.') ORDER BY alias;');
//     foreach ($lQry as $lRow) {
//       $lDef[$lRow['alias']] = array(
//         'id' => $lRow['id'],
//         'mand' => $lRow['mand'],
//         'src' => $lRow['src'],
//         'alias' => $lRow['alias'],
//         'native' => $lRow['native'],
//         'name_en' => $lRow['name_en'],
//         'desc_en' => $lRow['desc_en'],
//         'desc_de' => $lRow['desc_de'],
//         'name_de' => $lRow['name_de'],
//         'typ' => $lRow['typ'],
//         'param' => $lRow['param'],
//         'attr' => $lRow['attr'],
//         'feature' => $lRow['feature'],
//         'learn' => $lRow['learn'],
//         'avail' => $lRow['avail'],
//         'flags' => $lRow['flags'],
//         'used' => $lRow['used']
//       );
//     }

//     $lHTMList = new CHtm_Fie_Plain($lSrc, $lJobID);
//     $lResult = $lHTMList -> getPlain($lDef[$lAlias], $lValue);

    echo "OK";
  }
}