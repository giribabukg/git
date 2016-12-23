<?php
class CInc_Job_His_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job-his.menu');
  }

  protected function actStd() {
    $lJid = $this -> getReq('jobid');
    $lFac = new CJob_Fac($this->mSrc, $lJid);
    $lFac->getDat();
    $lRet = '';
    $lVie = $lFac -> getHeader();
    $lRet.= $lVie -> getContent();
    $lVie = $lFac -> getTabs('his');
    $lRet.= $lVie -> getContent();
    $lVie = new CJob_His_List($this->mSrc, $lJid);
    $lRet.= $lVie -> getContent();
    $this -> render($lRet);
  }

  protected function actSub() {
    $lHisid = $this -> getInt('hisid');
    $lVie = new CJob_His_Mails_List($lHisid);
    $lVie -> render();
  }

  protected function actFilter() {
    //Get Values
    $lJid = $this -> getReq('jobid');
    $lJobHisType = $this -> getReq('filterBy');
    //Show History
    $lFac =  new CJob_Fac($this->mSrc, $lJid);
    $lFac -> getDat();
    $lVie =  $lFac -> getHeader();
    $lRet =  $lVie ->getContent();
    $lVie =  $lFac -> getTabs('his');
    $lRet .= $lVie ->getContent();
    $lVie =  new CJob_His_List($this->mSrc, $lJid, 'job', $lJobHisType);
    $lRet .= $lVie -> getContent();
    $this -> render($lRet);

  }

  protected function actNew() {
    $lJid = $this -> getReq('jobid');
    $lFac = new CJob_Fac($this->mSrc, $lJid);
    $lFac->getDat();
    $lRet = '';
    $lVie = $lFac -> getHeader();
    $lRet.= $lVie -> getContent();
    $lVie = new CJob_His_Form($this->mSrc, $lJid);
    $lRet.= $lVie -> getContent();
    $this -> render($lRet);
  }

  protected function actSnew() {
    $lJid = $this -> getReq('jobid');
    $lMod = new CJob_His_Mod($this->mSrc, $lJid);
    $lMod -> getPost($this -> mReq, FALSE);
    if ($lMod -> insert()) {
      $lEve = $this->getCrpEvent('eve_comment');
      if (!empty($lEve)) {
        $lFac = new CJob_Fac($this->mSrc, $lJid);
        $lJob = $lFac->getDat();
        $lMsg = array('subject' => $lMod->getVal('subject'), 'body' => $lMod->getVal('msg'));

        $lEve = new CJob_Event($lEve, $lJob, $lMsg);
        $lEve->execute();
      }
    }

    $this -> redirect('index.php?act=job-'.$this->mSrc.'-his&jobid='.$lJid);
  }

  protected function actOrd() {
    $this -> mReq -> expect('fie');
    $lFie = $this -> mReq -> getVal('fie');
    $lJid = $this -> mReq -> getVal('jobid');

    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref('job-his.ord', $lFie);

    $this -> redirect('index.php?act=job-'.$this -> mSrc.'-his&jobid='.$lJid);
  }

  protected function getCrpEvent($aEvent) {
    $lCrp = CCor_Res::getByKey('code', 'crpmaster');
    if (!isset($lCrp[$this -> mSrc])) {
      return;
    }
    $lRow = $lCrp[$this -> mSrc];
    return $lRow[$aEvent];
  }

  protected function actEdt() {
    $lId  = $this -> getReq('id');
    $lJid = $this -> getReq('jobid');

    $lFac = new CJob_Fac($this->mSrc, $lJid);
    $lFac -> getDat();

    $lRet = '';

    $lVie = $lFac -> getHeader();
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_His_Form($this->mSrc, $lJid, 'sedt');
    $lVie -> load($lId);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actSedt() {
    $lJid = $this -> getReq('jobid');
    $lMod = new CJob_His_Mod($this->mSrc, $lJid);
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect('index.php?act=job-'.$this->mSrc.'-his&jobid='.$lJid);
  }

  protected function actPrnitm() {
    $lId = $this -> getInt('id');
    $lJid = $this -> getReq('jobid');

    $lFac = new CJob_Fac($this->mSrc, $lJid);
    $lFac->getDat();

    $lRet = '';

    $lVie = $lFac -> getHeader();
    $lVie -> hideMenu();
    $lRet.= $lVie -> getContent();

    $lHis = new CJob_His_Single($lId);
    $lRet.= $lHis -> getContent();

    $lMailHis = new CJob_His_Mails_List($lId);
    $lRet.= $lMailHis -> getContent();

    $lPag = new CUtl_Page();
    $lPag -> setPat('pg.cont', $lRet);
    $lPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));
    $lPag -> setPat('pg.title', htm(lan('job-his.menu')));
    $lPag -> setPat('pg.js', '<script type="text/javascript">window.print()</script>');

    echo $lPag -> getContent();
    exit;
  }

  protected function actNewmail() {
    $lJobId = $this -> getReq('jobid');
    $lSrc = $this -> getReq('src');
    $lFrm = $this -> getReq('frm');
    $lMailRequestId = $this -> getReq('emlid');

    $lRet = '';

    $lFac = new CJob_Fac($lSrc, $lJobId);
    $lJob = $lFac -> getDat();

    $lVie = $lFac -> getHeader();
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_His_Mailform($lSrc, $lJobId, $lJob, $lFrm, $lMailRequestId);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function getFree($aVal) {
    $lVal = trim($aVal);
    if (empty($lVal)) return 0;
    if (preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/', $lVal)) {
      return array('email' => $lVal);
    }
    if (preg_match('/(.+)[ ]?<(.+)>/', $lVal, $lArr)) {
      $this -> dbg('Multi name '.$lArr[1].'/email '.$lArr[2].' found');
      return array('email' => trim($lArr[2]), 'name' => trim($lArr[1]));
    }
    return 0;
  }

  protected function actSnewmail() {
    $lJobId = $this -> getReq('jobid');
    $lSrc = $this -> getReq('src');
    $lFrm = $this -> getReq('frm');
    $lUsrRes = CCor_Res::get('usr');
    $lVal = $this -> getReq('val');
    $lResponseVal = $lVal['responce'];
    $lResponse = ($lResponseVal == 'on') ? 1:0;
    $lMailRequestId = $this -> getReq('emlid');

    $lTo = array();
    $lArr = (isset($lVal['uid'])) ? $lVal['uid'] : array();
    if (!empty($lArr)) {
      foreach($lArr as $lUid) {
        if (isset($lUsrRes[$lUid])) {
          $lUsr = $lUsrRes[$lUid];
          $lTo[$lUsr['email']] = $lUsr['id'];
        }
      }
    }

    foreach ($lVal as $lKey => $lValue) {
      if (strpos($lKey, 'inpMailAddr') >= 0) {
        $lFree = $this -> getFree($lVal[$lKey]);
        $lFreeUsrId = CCor_Res::extract('id', 'fullname', 'usr', array('email' => $lFree['email']));
        if (!empty($lFree)) {
          $lTo[$lFree['email']] = key($lFreeUsrId);#isset($lFree['name']) ? $lFree['name'] : '';
        }
      }
    }

    $lMod = new CJob_His_Mod($lSrc, $lJobId, htMail);
    $lMod -> getPost($this -> mReq, FALSE);
    $lMsg = $lMod -> getVal('msg');
    $lOrg = $lMsg;
    $lSub = $lMod -> getVal('subject');
    if (!empty($lTo)) {
      $lMsg.= LF.LF.LF.'Sent to:'.LF;
      foreach ($lTo as $lKey => $lVal) {
        $lToName = $lUsrRes[$lVal]['first_lastname'];
        if ($lKey == $lVal) {
          $lMsg.= '- '.$lToName.LF;
        } else {
          $lMsg.= '- '.$lToName.' <'.$lKey.'>'.LF;
        }
      }
    }
    $lMsg = trim($lMsg);
    $lMod -> setVal('msg', $lMsg);
    $lMod -> insert();
    $lUsr = CCor_Usr::getInstance();
    $lEmail = $lUsr -> getVal('email');
    $lName  = $lUsr -> getVal('first_lastname');
    $lSenderId = $lUsr -> getVal('id');

    if (!empty($lTo)) {
      foreach ($lTo as $lKey => $lVal) {
        $lToName = $lUsrRes[$lVal]['first_lastname'];
        $lMai = new CApi_Mail_Item($lEmail, $lName, '', '', $lSub, $lOrg);
        $lMai -> setSenderID($lSenderId);
        $lMai -> setJobId($lJobId);
        $lMai -> setJobSrc($lSrc);
        $lMai -> setMailType(mailJobNotification);
        if ($lKey == $lVal) {
          $lMai -> setTo($lKey);
        } else {
          $lMai -> setTo($lKey, $lToName);
        }
        if (is_numeric($lVal)) {
          $lMai -> setReciverId($lUsrRes[$lVal]['id']);
          $lMai -> setMailNeedResponse($lResponse);
        }
        $lMai -> insert();
      }
      if (isset($lMailRequestId) AND $lMailRequestId != 0) $this -> resetResponse($lMailRequestId);
    }
    // Email Formular from Job or History?
    if ($lFrm == 'job') {
      $this -> redirect('index.php?act=job-'.$lSrc.'.edt&jobid='.$lJobId);
    } elseif ($lFrm == 'hom') {
      $this -> redirect('index.php?act=hom-wel');
    } else {
      $this -> redirect('index.php?act=job-'.$lSrc.'-his&jobid='.$lJobId);
    }

  }

  protected function resetResponse($aMailRequestId) {
    if ($aMailRequestId == 0) return;
    $lSql = 'UPDATE al_sys_mails set response = 0 WHERE id = '.$aMailRequestId;
    CCor_Qry::exec($lSql);
  }

  /**
   * Add Webcenter Annotations to History
   * First Get existing Anno. from al_job_his
   * Then get Anno. from Webcenter
   * Merge Hash from each other and add the new one to history.
   * If Debug = TRUE, give Messages to display.
   * @return unknown_type
   */
  protected function actUpdwec() {
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jobid');
    #$lDownloadAnnotations = $this -> getReq('da'); // use DownloadAnnotations.jsp instead of GetDocumentHistory.jsp

    $lApl = new CApp_Apl_Loop($lSrc, $lJid, 'apl');

    // Get WebCenter ID
    $lObj = new CApp_Wec($lSrc, $lJid);
    $lWecPid = $lObj -> getWebcenterId();
    if (empty($lWecPid)) {
      $this -> redirect('index.php?act=job-'.$lSrc.'-his&jobid='.$lJid);
    }

    // Get WebCenter annotations
    $lUpd = new CApi_Wec_Updatehistory($lSrc, $lJid, $lWecPid, $this -> getReq('debug'), $lDownloadAnnotations);
    $lArr = $lUpd -> getHistoryArray();
    if ($this -> getReq('debug')) {
      echo '<pre>';
      echo BR.'################### HISTORY ARRAY #######################'.BR;
      echo 'ARR:'.var_export($lArr, True).BR;
      echo '</pre>';
    }

    // Get existing Anootations Hash from al_job_his
    // So existing Items not overwrite.
    $lHis = new CJob_His($lSrc, $lJid);
    if (!empty($lArr)) {
      // load hashes from local history to prevent importing an item twice
      $lHisArr = array();
      $lSql = 'SELECT add_data FROM al_job_his WHERE 1 ';
      $lSql.= 'AND src='.esc($lSrc).' ';
      $lSql.= 'AND mand='.intval(MID).' ';
      $lSql.= 'AND src_id='.esc($lJid);

      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lAdd = $lRow['add_data'];
        if (!empty($lAdd)) {
          $lAddArr = unserialize($lAdd);
          if (!empty($lAddArr['hash'])) {
            $lHisArr[] = $lAddArr['hash'];
          }
        }
      }

      if ($this -> getReq('debug')) {
        echo '<pre>'.BR.'################### Hash Items of existing Annotaions #######################'.BR;
        echo 'lHisArr:'.var_export($lHisArr, True).'</pre>';
      }

      foreach ($lArr as $lRow) {
        $lHash = CApi_Wec_Query_History::getItemHash($lRow);
        if ($this -> getReq('debug')) {
          echo 'Proofs from Webcenter :'.$lHash.BR;
        }

        // If Annotation is already existing, not add.
        if (in_array($lHash, $lHisArr)) {
          continue;
        }

        if ($this -> getReq('debug')) {
          echo '<pre>############ HASH of Annotation to ADD ############# '.BR;
          echo $lHash.' '.var_export($lRow, True).'</pre>'.BR;
        }

        if (isset($lRow['userid'])) {
          $lUid = $lRow['userid'];
        } else {
          $lUid = $lUpd -> mapUser($lRow['uid']);
        }

        $lHis -> setUser($lUid);
        $lHis -> setDate($lRow['date']);

        $lSub = lan('wec.comment');//'Webcenter Kommentar';
        $lTyp = $lRow['typ'];
        switch ($lTyp) {
          case htAplOk :
            $lSub = lan('apl.approval');
            $lApl -> setState($lUid, CApp_Apl_Loop::APL_STATE_APPROVED, $lRow['comment']);
            break;
          case htAplNok :
            $lSub = lan('apl.amendment');
            $lApl -> setState($lUid, CApp_Apl_Loop::APL_STATE_AMENDMENT, $lRow['comment']);
            break;
          case htAplCond :
            $lSub = lan('apl.conditional');
            $lApl -> setState($lUid, CApp_Apl_Loop::APL_STATE_CONDITIONAL, $lRow['comment']);
            break;
        }
        $lAdd = array();
        $lAdd['hash'] = $lHash;
        if ($lRow['filename']) {
          $lAdd['fil'] = $lRow['filename'];
        }

        if ($this -> getReq('debug')) {
          echo 'Add: "'.$lRow['typ'].'" "'.$lSub.'" "'.$lRow['comment'].'" "'.var_export($lAdd, True).'"'.BR;
        }

        $lHis -> add($lRow['typ'], $lSub, $lRow['comment'], $lAdd);
      }
    }

    if ($this -> getReq('debug')) {
      exit;
    }

    $this -> redirect('index.php?act=job-'.$lSrc.'-his&jobid='.$lJid);
  }
}