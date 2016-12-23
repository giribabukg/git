<?php
class CInc_Job_Sku_Cnt extends CJob_Cnt {

  protected $mSrc = 'sku';

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mAva = fsSku;
    $this -> mMmKey = 'job-sku';
  }

  protected function actStd() {
    $lVie = new CJob_Sku_List();
    $this -> render($lVie);
  }

  public function actNew() {
    $lJob = new CCor_Dat();

    $lVie = new CJob_Sku_Tabs(0);
    $lRet = $lVie -> getContent();

    $lFrm = new CJob_Sku_Form(0, 'job', 'job-'.$this -> mSrc.'.snew');
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  public function actSnew() {
    $lPage = $this -> getReq('page', 'job');

    $lMod = new CJob_Sku_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lJid = $lMod -> getInsertId();
      $this -> redirect('index.php?act=job-'.$this -> mSrc.'.edt&jobid='.$lJid.'&page='.$lPage);
    }
    $this -> redirect('index.php?act=job-'.$this -> mSrc.'.new');
  }

  protected function actEdt() {
    $lSKUID = $this -> getInt('jobid');
    $lPage = $this -> getReq('page');

    $lSKU = new CJob_Sku_Dat();
    $lSKU -> load($lSKUID);
    $lSKU -> addRecentJob();

    $lVie = new CJob_Sku_Header($lSKU);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Sku_Tabs($lSKUID, $lPage);
    $lRet.= $lVie -> getContent();

    $lFrm = new CJob_Sku_Form($lSKUID, $lPage);
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actSedt() {
    $lCID = $this -> getReq('clientid');
    parent::checkMand($lCID);

    $lJid = $this -> getInt('jobid');
    $lStep = $this -> getReq('step');
    $lPag = $this -> getReq('page');
    $this -> dump($_REQUEST);

    $lMod = new CJob_Sku_Mod($lJid);
    $lOld = $this->getReq('old');
    $lMod -> getPost($this -> mReq, !empty($lOld));
    if ($lMod -> update()) {
      $this->afterUpdate($lMod);
      if ($lStep > 0) {
        $this -> redirect('index.php?act=job-'.$this -> mSrc.'.step&sid='.$lStep.'&jobid='.$lJid);
        exit;
      }
    }
    $this -> redirect('index.php?act=job-'.$this -> mSrc.'.edt&jobid='.$lJid.'&page='.$lPag);
  }

  protected function actDel() {
    $lSKUID = $this -> getReq('id');
    $lSQL = 'UPDATE al_job_sku_'.intval(MID).' SET del="Y" WHERE id='.esc($lSKUID);
    CCor_Qry::exec($lSQL);
    $this -> redirect();
  }

  protected function actStep() {
    $lSKUID = $this -> getInt('jobid');
    $lStep = $this -> getInt('sid');

    $lAct = new CJob_Sku_Step($lSKUID);

    $lFla = intval(CCor_Qry::getStr('SELECT flags FROM al_crp_step WHERE mand='.intval(MID).' AND id='.esc($lStep)));
    if (bitset($lFla, sfComment)) {
      $this -> stepDialog();
      exit;
    } else {
      $this -> redirect('index.php?act=job-'.$this -> mSrc.'.cnf&jobid='.$lSKUID.'&sid='.$lStep);
    }
  }

  protected function stepDialog() {
    $lSKUID = $this -> getInt('jobid');
    $lStep = $this -> getInt('sid');

    $lSKU = new CJob_Sku_Dat();
    $lSKU -> load($lSKUID);

    $lHdr = new CJob_Sku_Header($lJob);
    $lRet = $lHdr -> getContent();

    $lDlg = new CJob_Dialog($this -> mSrc, $lSKUID, $lStep, $lSKU);
    $lRet.= $lDlg -> getContent();

    $this -> render($lRet);
  }

  protected function actCnf() {
    $lSKUID = $this -> getInt('jobid');
    $lStepID = $this -> getInt('sid');
    $lPage = $this -> getReq('page');
    $lVal = $this -> getReq('val');
    $lMsg = (isset($lVal['msg'])) ? $lVal['msg'] : '';

    $lAdd = array();
    if (isset($lVal['amt'])) {
      $lAdd['amt'] = $lVal['amt'];
    }
    if (isset($lVal['cause'])) {
      $lAdd['cause'] = $lVal['cause'];
    }
    if (isset($_FILES['val'])) {
      $lFin = new CApp_Finder($this -> mSrc, $lSKUID);
      $lDir = $lFin -> getPath('doc');
      $lUpl = new CCor_Upload();
      $lRes = $lUpl -> uploadIndex('file', $lDir);
      if ($lRes) {
        $lAdd['fil'] = $lRes;
        CCor_Usr::insertJobFile($this -> mSrc, $lSKUID, 'doc', $lRes);

        $lArr = $_FILES['val'];
        $lNam = $lArr['name']['file'];
        $lHis = new CApp_His($this -> mSrc, $lSKUID);
        $lMsg = sprintf(lan('filupload.success'),$lNam);
        $lHis -> add(htFileupload, lan('filupload.his.msg'), $lMsg);
      }
    }

    // ignore mail notifications?
    $lOld = $this -> getReq('act_old', array());
    $lAct = $this -> getReq('act_new', array());
    $lIgn = array();
    foreach ($lOld as $lKey => $lVal1) {
      if (!isset($lAct[$lKey])) {
        $lIgn[] = $lKey;
      }
    }

    $lObj = new CJob_Sku_Step($lSKUID);
    $lObj -> doStep($lStepID, $lMsg, $lAdd, $lIgn);
    $this -> redirect('index.php?act=job-'.$this -> mSrc.'.edt&jobid='.$lSKUID.'&page='.$lPage);
  }

  protected function actSub() {
    $lPid = $this -> getInt('jid');
    $lVie = new CJob_Sku_Sub($lPid);
    $lVie -> render();
  }

  protected function actAssignskusur() {
    $lSKUID = $this -> getReq('jobid');

    $lSKU = new CJob_Sku_Dat();
    $lSKU -> load($lSKUID);

    $lHdr = new CJob_Sku_Header($lSKU);
    $lRet = $lHdr -> getContent();

    $lVie = new CJob_Assign($this -> mSrc, $lSKUID, FALSE);
    $this -> render($lVie);
  }

  protected function actSassignprj() {
    $lSKUID = $this -> getReq('jobid');
    $lProID = $this -> getReqInt('pid');
    $lPage = $this -> getReq('page', 'job');

    $lResult = CCor_Qry::getStr('SELECT * FROM al_job_sku_sur_'.intval(MID).' WHERE sku_id='.esc($lSKUID).' AND pro_id='.esc($lProID));
    if (!$lResult) {
      CCor_Qry::exec('INSERT INTO al_job_sku_sur_'.intval(MID).' (pro_id, sku_id) VALUES ('.esc($lProID).', '.esc($lSKUID).')');

      // write project information to sku header
      $lProjectFieldsArray = CCor_Cfg::get('job-pro.fields');
      $lProjectFieldsString = implode(',', $lProjectFieldsArray);

      $lUpdateSKU = 'UPDATE al_job_sku_'.intval(MID).' SET ';
      $lGetProjectInformation = new CCor_Qry('SELECT '.$lProjectFieldsString.' FROM al_job_pro_'.intval(MID).' WHERE id='.$lProID);
      foreach ($lGetProjectInformation as $lRow) {
        foreach ($lRow as $lKey => $lValue) {
          $lUpdateSKU.= $lKey.'='.esc($lValue).',';
        }
      }
      $lUpdateSKU = rtrim($lUpdateSKU, ',');
      $lUpdateSKU.= ' WHERE id='.esc($lSKUID);

      CCor_Qry::exec($lUpdateSKU);

      // check whether all jobs in this sku are already assigned to the project
    }

    $this -> redirect('index.php?act=job-'.$this -> mSrc.'.edt&jobid='.$lSKUID.'&page='.$lPage);
  }

  protected function actNewsur() {
    $lProID = $this -> getVal('pid');

    $lQry = new CCor_Qry('SELECT * FROM al_job_pro_'.intval(MID).' WHERE id='.esc($lProID));
    $lPro = $lQry -> getDat();

    $lVie = new CJob_Sku_Tabs(0);
    $lRet = $lVie -> getContent();

    $lFrm = new CJob_Sku_Form(0, 'job', 'job-'.$this -> mSrc.'.snewsur', $lPro);
    $lFrm -> setParam('pid', $lProID);
    $lRet.= $lFrm -> getContent();
    $this -> render($lRet);
  }

  protected function actSnewsur() {
    $lProID = $this -> getVal('pid');
    $lPage = $this -> getReq('page', 'job');

    $lMod = new CJob_Sku_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lSKUID = $lMod -> getInsertId();

      $lResult = CCor_Qry::getStr('SELECT * FROM al_job_sku_sur_'.intval(MID).' WHERE sku_id='.esc($lSKUID).' AND pro_id='.esc($lProID));
      if (!$lResult) {
        CCor_Qry::exec('INSERT IGNORE INTO al_job_sku_sur_'.intval(MID).' (pro_id, sku_id) VALUES ('.esc($lProID).', '.esc($lSKUID).')');

        // START1: when a job field has not yet been set up in the table, add a new column to it
        $lProjectFieldsArray = CCor_Cfg::get('job-pro.fields');

        foreach ($lProjectFieldsArray as $lKey => $lValue) {
          $lColumnName = CCor_Qry::getStr('SHOW COLUMNS FROM al_job_sku_'.intval(MID).' LIKE "'.$lValue.'";');

          if ((!empty($lColumnName)) || ($lValue == 'jobid')) {
            # either the field is already existing or we just have grabbed jobid
          } else {
            # what type of is the job field
            $lTypeIn = CCor_Qry::getStr("SELECT typ FROM al_fie WHERE alias='$lValue';");

            switch ($lTypeIn) {
              case 'date':
                $lTypeOut = 'date';
                break;
              case 'time':
                $lTypeOut = 'time';
                break;
              case 'integer':
                $lTypeOut = 'int(11)';
                break;
              case 'memo':
                $lTypeOut = 'text';
                break;
              default:
                $lTypeOut = 'varchar(255)';
              break;
      }

            # create the missing column
            if (CCor_Qry::exec('ALTER TABLE al_job_sku_'.intval(MID).' ADD `'.$lValue.'` '.$lTypeOut)) {
            } else {
            }
          }
        }
        // END1: when a job field has not yet been set up in the table, add a new column to it

        // START2: when a job field has not yet been set up in the table, add a new column to it
        $lAllJobFields = CCor_Res::extract('id', 'alias', 'fie');

        foreach ($lAllJobFields as $lKey => $lValue) {
          $lColumnName = CCor_Qry::getStr('SHOW COLUMNS FROM al_job_sku_'.intval(MID).' LIKE "'.$lValue.'";');

          if ((!empty($lColumnName)) || ($lValue == 'jobid')) {
            # either the field is already existing or we just have grabbed jobid
          } else {
            # what type of is the job field
            $lTypeIn = CCor_Qry::getStr("SELECT typ FROM al_fie WHERE alias='$lValue';");

            switch ($lTypeIn) {
              case 'date':
                $lTypeOut = 'date';
                break;
              case 'time':
                $lTypeOut = 'time';
                break;
              case 'integer':
                $lTypeOut = 'int(11)';
                break;
              case 'memo':
                $lTypeOut = 'text';
                break;
              default:
                $lTypeOut = 'varchar(255)';
              break;
            }

            # create the missing column
            if (CCor_Qry::exec('ALTER TABLE al_job_sku_'.intval(MID).' ADD `'.$lValue.'` '.$lTypeOut)) {
            } else {
            }
          }
        }
        // END2: when a job field has not yet been set up in the table, add a new column to it

        // START3: write project information to sku header
        $lProjectFieldsString = implode(',', $lProjectFieldsArray);

        $lUpdateSKU = 'UPDATE al_job_sku_'.intval(MID).' SET ';
        $lGetProjectInformation = new CCor_Qry('SELECT '.$lProjectFieldsString.' FROM al_job_pro_'.intval(MID).' WHERE id='.$lProID);
        foreach ($lGetProjectInformation as $lRow) {
          foreach ($lRow as $lKey => $lValue) {
            $lUpdateSKU.= $lKey.'='.esc($lValue).',';
          }
        }
        $lUpdateSKU = rtrim($lUpdateSKU, ',');
        $lUpdateSKU.= ' WHERE id='.esc($lSKUID);

        CCor_Qry::exec($lUpdateSKU);
        // END3: write project information to sku header
      }

      $lMod -> reportDraft($lSKUID, $lProID);
      $this -> redirect('index.php?act=job-sku.edt&jobid='.$lSKUID.'&page='.$lPage);
    }

    $this -> redirect('index.php?act=job-sku.new');
  }

    protected function actNewsub() {
    $lJobID = $this -> getVal('jid');
    $lJobType = $this -> getVal('src');

    $lVie = new CJob_Sku_Tabs(0);
    $lRet = $lVie -> getContent();

    $lFrm = new CJob_Sku_Form(0, 'job', 'job-'.$this -> mSrc.'.snewsub');
    $lFrm -> setParam('jid', $lJobID);
    $lFrm -> setParam('src', $lJobType);
    $lRet.= $lFrm -> getContent();
    $this -> render($lRet);
  }

  protected function actSnewsub() {
    $lJobID = $this -> getVal('jid');
    $lJobType = $this -> getVal('src');
    $lPage = $this -> getReq('page', 'job');

    $lMod = new CJob_Sku_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      $lSKUID = $lMod -> getInsertId();

      $lResult = CCor_Qry::getStr('SELECT * FROM al_job_sku_sub_'.intval(MID).' WHERE sku_id='.esc($lSKUID).' AND job_id='.esc($lJobID));
      if (!$lResult) {
        CCor_Qry::exec('INSERT IGNORE INTO al_job_sku_sub_'.intval(MID).' (job_id, sku_id, src) VALUES ('.esc($lJobID).', '.esc($lSKUID).', '.esc($lJobType).')');
      }

      CJob_Sku_Mod::reportDraft($lJobID, $this -> mSrc, $lSKUID);
      $this -> redirect('index.php?act=job-sku.edt&jobid='.$lSKUID.'&page='.$lPage);
    }
    $this -> redirect('index.php?act=job-sku.new');
  }

}