<?php
class CInc_Job_Sku_Sur_Cnt extends CCor_Cnt {
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mAva = fsSku;
  }

  protected function getStdUrl() {
    $lJobID = $this -> mReq -> jobid;
    return 'index.php?act='.$this -> mMod.'&jobid='.$lJobID;
  }

  protected function actStd() {
    $lSKUID = $this -> getReq('jobid');

    $lQry = new CCor_Qry('SELECT * FROM al_job_sku_'.intval(MID).' WHERE id='.esc($lSKUID));
    $lSKU = $lQry -> getDat();

    $lVie = new CJob_Sku_Header($lSKU);
    $lRet = $lVie -> getContent();

    $lVie = new CJob_Sku_Tabs($lSKUID, 'sur');
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Sku_Sur_List($lSKUID);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  public function actNew() {
    $lSKUID = $this -> getInt('jobid');

    $lQry = new CCor_Qry('SELECT * FROM al_job_sku_'.intval(MID).' WHERE id='.esc($lSKUID));
    $lSKU = $lQry -> getDat();

    $lVie = new CJob_Sku_Header($lSKU);
    $lRet = $lVie -> getContent();

    $lFrm = new CJob_Sku_Sub_Form('job-sku-sub.snew', $lSKUID, $lSKU);
    $lRet.= $lFrm -> getContent();

    $this -> render($lRet);
  }

  protected function actSnew() {
    $lSKUID = $this -> getInt('jobid');

    $lMod = new CJob_Sku_Sub_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> setVal('sku_id', $lJobId);
    $lMod -> insert();

    $this -> redirect('index.php?act=job-sku-sub&jobid='.$lSKUID);
  }

  protected function actEdt() {
    $lSKUID = $this -> getInt('jobid');
    $lSid  = $this -> getInt('id');

    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canEdit('job-sku-sub')) {
      $this -> redirect('index.php?act=job-sku-sub&jobid='.$lSKUID);
    }

    $lQry = new CCor_Qry('SELECT * FROM al_job_sub_'.intval(MID).' WHERE id='.esc($lSid));
    $lJob = $lQry -> getDat();

    $lVie = new CJob_Sku_Header($lJob);
    $lRet = $lVie -> getContent();

    $lRet.= '<table cellpadding="1" cellspacing="0" border="0" class="tabTbl"><tr>
		<td class="tabAct nw" id="tabjob">Identifikation</td></tr></table>';

    $lFrm = new CJob_Sku_Sub_Form('job-sku-sub.sedt', $lSKUID, $lJob);
    $lFrm -> setParam('old[id]', $lSid);

    $lRet.= $lFrm -> getContent();
    $this -> render($lRet);
  }

  protected function actSedt() {
    $lSKUID = $this -> getInt('jobid');
    $lId  = $this -> getInt('id');
    $lPag = $this -> getReq('page');

    $lMod = new CJob_Sku_Sub_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();

    $this -> redirect('index.php?act=job-sku-sub&jobid='.$lSKUID);
  }

  protected function actUnassign() {
    $lProID = $this -> getReq('jobid');
    $lSKUID = $this -> getReq('id');

    if (!empty($lProID) AND !empty($lSKUID)) {
      $lSql = 'DELETE FROM al_job_sku_sur_'.intval(MID).' WHERE sku_id='.esc($lSKUID).' AND pro_id='.esc($lProID);
      CCor_Qry::exec($lSql);
    }

    $this -> redirect();
  }

  protected function actDel() {
    $lSKUID = $this -> getReq('id');

    if (!empty($lSKUID)) {
      $lSql = 'DELETE FROM al_job_sku_'.intval(MID).' WHERE sku_id='.esc($lSKUID);
      CCor_Qry::exec($lSql);
      $lSql = 'DELETE FROM al_job_sku_sur_'.intval(MID).' WHERE sku_id='.esc($lSKUID);
      CCor_Qry::exec($lSql);
      $lSql = 'DELETE FROM al_job_sku_sub_'.intval(MID).' WHERE sku_id='.esc($lSKUID);
      CCor_Qry::exec($lSql);
    }

    $this -> redirect();
  }

  protected function actFpr() {
    $lSKUID = $this -> getVal('jobid');
    
    $lVie = new CHtm_Fpr($this -> mMod.'.sfpr', $this -> mMod.'&jobid='.$lSKUID);
    $lVie -> setParam('jobid', $lSKUID);
    $lDef = CCor_Res::get('fie');

    $this -> mAva = 0;
    $lJobTypes = CCor_Cfg::get('menu-skuitems');
    foreach ($lJobTypes as $lKey => $lValue) {
      $lJobTypes[$lKey] = substr($lValue, -3); // art instead of job_art
      $this -> mAva+= constant(fs.ucfirst($lJobTypes[$lKey]));
    }

    $lArr = array();
    foreach ($lDef as $lFie) {
      $lFla = intval($lFie['flags']);
      $lAva = intval($lFie['avail']);
      if (bitSet($lAva, $this -> mAva)) {
        if (bitSet($lFla, ffList)) {
          $lArr[$lFie['id']] = $lFie['name_'.LAN];
        }
      }
    }
    $lVie -> setSrc($lArr);
    $lUsr = CCor_Usr::getInstance();
    $lVie -> setSel($lUsr -> getPref($this -> mPrf.'.cols'));
    $this -> render($lVie);
  }

  protected function actSfpr() {
    $lDst = $this -> mReq -> getVal('dst');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.cols', implode(',', $lDst));
    $this -> redirect();
  }

  protected function actSpr() {
    $lSKUID = $this -> getVal('jobid');

    $lVie = new CHtm_Fpr($this -> mMod.'.sspr', $this -> mMod.'&jobid='.$lSKUID);
    $lVie -> setParam('jobid', $lSKUID);
    $lDef = CCor_Res::get('fie');

    $this -> mAva = 0;
    $lJobTypes = CCor_Cfg::get('menu-skuitems');
    foreach ($lJobTypes as $lKey => $lValue) {
      $lJobTypes[$lKey] = substr($lValue, -3); // art instead of job_art
      $this -> mAva+= constant(fs.ucfirst($lJobTypes[$lKey]));
    }

    $lArr = array();
    foreach ($lDef as $lFie) {
      $lFla = intval($lFie['flags']);
      $lAva = intval($lFie['avail']);
      if (bitSet($lAva, $this -> mAva)) {
        if (bitSet($lFla, ffSearch)) {
          $lArr[$lFie['id']] = $lFie['name_'.LAN];
        }
      }
    }

    $lVie -> setSrc($lArr);
    $lUsr = CCor_Usr::getInstance();
    $lVie -> setSel($lUsr -> getPref($this -> mPrf.'.sfie'));
    $this -> render($lVie);
  }

  protected function actSspr() {
    $lDst = $this -> mReq -> getVal('dst');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.sfie', implode(',', $lDst));
    $this -> redirect();
  }

}