<?php
class CInc_Job_Pro_Sku_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mAva = fsSku;
  }

  protected function getStdUrl() {
    $lJobID = $this -> mReq -> jobid;
    return 'index.php?act='.$this -> mMod.'&jobid='.$lJobID;
  }

  public function actStd() {
    $lJobID = $this -> getReq('jobid');

    $lJob = new CJob_Pro_Dat();
    $lJob -> load($lJobID);

    $lRet = '';

    $lVie = new CJob_Pro_Header($lJob);
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Pro_Tabs($lJobID, 'sku');
    $lRet.= $lVie -> getContent();

    $lVie = new CJob_Pro_Sku_List($lJobID);
    $lRet.= $lVie -> getContent();

    $this -> render($lRet);
  }

  protected function actFpr() {
    $lJobID = $this -> getVal('jobid');

    $lVie = new CHtm_Fpr($this -> mMod.'.sfpr', $this -> mMod.'&jobid='.$lJobID);
    $lVie -> setParam('jobid', $lJobID);
    $lDef = CCor_Res::get('fie');

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
    $lJobID = $this -> getVal('jobid');

    $lVie = new CHtm_Fpr($this -> mMod.'.sspr', $this -> mMod.'&jobid='.$lJobID);
    $lVie -> setParam('jobid', $lJobID);
    $lDef = CCor_Res::get('fie');

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

}