<?php
class CInc_Crp_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('crp.menu');
    $this -> mMmKey = 'opt';

    $lPn = 'crp';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lPn)) {
      $this -> setProtection('*', $lPn, rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CCrp_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lId = $this -> getInt('id');
    $lForm = new CCrp_Form_Edit($lId);
    $lWrap = new CCrp_Wrap($lId, 'dat', $lForm);
    $this -> render($lWrap);
  }

  protected function actSedt() {
    $lMod = new CCrp_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> update()) {
      CCor_Cache::clearStatic('cor_res_crp_'.MID);
      CCor_Cache::clearStatic('cor_res_crpstep_'.MID);
    }
    $this -> redirect();
  }

  protected function actNew() {
    $lVie = new CCrp_Form_Base('crp.snew', lan('crp.new'));
    $this -> render($lVie);
  }

  protected function actSnew() {
    $lMod = new CCrp_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      CCor_Cache::clearStatic('cor_res_crp_'.MID);
      CCor_Cache::clearStatic('cor_res_crpstep_'.MID);
    }
    $this -> redirect();
  }

  protected function actDel() {
    $lId = $this -> getReqInt('id');
    if (0 == MID) {
      $lSql = 'UPDATE al_crp_mastertpl SET del="Y" WHERE mand='.MID.' AND id='.$lId;
    } else {
      $lSql = 'DELETE FROM al_crp_step WHERE mand='.MID.' AND crp_id='.$lId;
      CCor_Qry::exec($lSql);
      $lSql = 'DELETE FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$lId;
      CCor_Qry::exec($lSql);
      $lSql = 'DELETE FROM al_crp_master WHERE mand='.MID.' AND id='.$lId;
    }
    CCor_Qry::exec($lSql);

    CCor_Cache::clearStatic('cor_res_crp_'.MID);
    CCor_Cache::clearStatic('cor_res_crp_'.MID.'_'.$lId);
    CCor_Cache::clearStatic('cor_res_crpstep_'.MID);
    CCor_Cache::clearStatic('cor_res_crpstep_'.MID.'_'.$lId);

    $this -> redirect();
  }

  protected function actCpy() {
    $lId = $this -> mReq -> getInt('id');

    $this -> mAvailLang = CCor_Res::get('languages');
    $lLangStr = '';
    $lLangStr2 = '';
    $lLangStrP = '';
    $lLangConcat = array();
    $lLangName = array();
    $lLangDesc = array();
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $lLangStr .= ', '.backtick('name_'.$lLang);
      $lLangStr2.= ', '.backtick('desc_'.$lLang);
      $lLangStrP.= ', p.'.backtick('name_'.$lLang);
      $lLangConcat[] = ' name_'.$lLang.'=concat("'.CCor_Lang::getStatic('lib.copy_from',$lLang).' ", name_'.$lLang.')';
      $lLangName[] = 'name_'.$lLang;
      $lLangDesc[] = 'desc_'.$lLang;
    }
    $lLangStr2 = $lLangStr.$lLangStr2;
    $lConcat = implode (',', $lLangConcat);

    // copy from al_crp_master to al_crp_master
    if (0 == MID) {
      $lTbl = 'al_crp_mastertpl'; // templates for all mandators
      $lSql = 'INSERT INTO '.$lTbl.' (mand, code'.$lLangStr.', eve_draft, eve_comment, eve_jobchange, eve_upload, eve_onhold, eve_continue, eve_cancel, eve_revive, eve_archive, eve_archive_condition, eve_archive_numberofjobs) SELECT '.MID.', code'.$lLangStr.', eve_draft, eve_comment, eve_jobchange, eve_upload, eve_onhold, eve_continue, eve_cancel, eve_revive, eve_archive, eve_archive_condition, eve_archive_numberofjobs FROM al_crp_mastertpl WHERE id='.$lId;
    } else {
      $lTbl = 'al_crp_master'; // critical paths of each mandator
      $lSql = 'INSERT INTO '.$lTbl.' (id, mand, code'.$lLangStr.', eve_draft, eve_comment, eve_jobchange, eve_upload, eve_onhold, eve_continue, eve_cancel, eve_revive, eve_archive, eve_archive_condition, eve_archive_numberofjobs) SELECT id, '.MID.', code'.$lLangStr.', eve_draft, eve_comment, eve_jobchange, eve_upload, eve_onhold, eve_continue, eve_cancel, eve_revive, eve_archive, eve_archive_condition, eve_archive_numberofjobs FROM al_crp_mastertpl WHERE id='.$lId;
    }
    CCor_Qry::exec($lSql);

    // get new id
    if (0 == MID) {
      $lNId = mysql_insert_id(); // for templates only
    } else {
      $lNId = $lId;
    }
    // rename to 'copy of ...'
    $lSql = 'UPDATE '.$lTbl.' SET '.$lConcat.' WHERE mand='.MID.' AND id='.$lNId;
    CCor_Qry::exec($lSql);

    //    // assign to current mandator
    //    $lSql = 'INSERT INTO al_crp_mand (id, mand) VALUES ('.$lNId.', '.MID.')';
    //    CCor_Qry::exec($lSql);

    // copy from al_crp_status
    $lSql = 'SELECT * FROM al_crp_status WHERE mand=0 AND crp_id='.$lId;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet[] = $lRow -> toArray();
    }

    // copy to al_crp_status, but remember old ids
    $lArrEv = array();
    $lArrNO = array();
    $lArrID = array();
    foreach ($lRet as $lRow) {
      $lSql = 'INSERT INTO al_crp_status (mand, crp_id'.$lLangStr2.', status, display, on_enter, on_exit, img, apl) VALUES ('.esc(MID).','.esc($lNId).',';
      foreach ($lLangName as $lV) {
        $lSql.= esc($lRow[$lV]).',';
      }
      foreach ($lLangDesc as $lV) {
        $lSql.= esc($lRow[$lV]).',';
      }
      $lSql.= $lRow['status'].','.$lRow['display'].','.$lRow['on_enter'].','.$lRow['on_exit'].','.$lRow['img'].','.$lRow['apl'].')';
      CCor_Qry::exec($lSql);
      $lArrNO += array($lRow['id'] => mysql_insert_id());
      array_push($lArrID, $lRow['id']);
    }

    // copy from al_crp_step to al_crp_step
    $lArrID = implode(",", $lArrID);
    $lSql = 'SELECT p.id, q.crp_id, p.from_id, p.to_id'.$lLangStrP.', p.event, p.flags, p.trans, q.display FROM al_crp_step AS p, al_crp_status AS q WHERE q.mand=0 AND p.from_id IN ('.$lArrID.') AND p.to_id=q.id ORDER BY q.display';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lKey => $lVal) {
      $lRet = $lVal -> toArray();

      if(!isset($lArrEv[$lRet['event']]) AND 0 < $lRet['event']) {
        $lArrEv[$lRet['event']] = $lRet['event'];
      }

      if (array_key_exists($lRet['from_id'], $lArrNO)) {
        $lRet['from_id'] = $lArrNO[$lRet['from_id']];
      }

      if (array_key_exists($lRet['to_id'], $lArrNO)) {
        $lRet['to_id'] = $lArrNO[$lRet['to_id']];
      }

      $lSql = 'INSERT INTO al_crp_step (mand, crp_id, from_id, to_id'.$lLangStr.', event, flags, trans) VALUES ('.esc(MID).','.$lNId.','.$lRet['from_id'].', '.$lRet['to_id'].', ';
      foreach ($lLangName as $lV) {
        $lSql.= esc($lRet[$lV]).', ';
      }
      $lSql.= $lRet['event'].', '.$lRet['flags'].', "'.$lRet['trans'].'")';
      CCor_Qry::exec($lSql);

    }

    // events
    if (!empty($lArrEv)) {
      $lArrEv = implode(",", $lArrEv);
      $lSql = 'REPLACE INTO al_eve (id, mand, src'.$lLangStr.') SELECT id, '.esc(MID).', src'.$lLangStr.' FROM `al_eve` WHERE mand=0 AND id IN ('.$lArrEv.')';
      CCor_Qry::exec($lSql);
    }

    CCor_Cache::clearStatic('cor_res_crp_'.MID);
    CCor_Cache::clearStatic('cor_res_crpstep_'.MID);

    $this -> redirect();
  }

  public function getJob2ProjectAssignment($aWithNoProjection = FALSE) {
    // which critical path belongs to which job type?
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $lSrcStaPro = array();
    if (!empty($lCrp)) {
      foreach ($lCrp as $lSrc => $lCrpId) {
        $lCrpStaDis = CCor_Res::extract('status', 'display', 'crp', $lCrpId);
        $lCrpStaPro = CCor_Res::extract('status', 'pro_con', 'crp', $lCrpId);
        foreach ($lCrpStaPro as $lSta => $lPro) {
          if ($aWithNoProjection) {
            if (!isset($lSrcStaPro[$lSrc])) {
              $lSrcStaPro[$lSrc] = array();
            }
            if (0 < $lCrpStaDis[$lSta]) {
              $lSrcStaPro[$lSrc][$lSta] = array('display' => $lCrpStaDis[$lSta], 'pro_con' => $lPro);
            }
          } elseif (0 < $lPro) {
            if (!isset($lSrcStaPro[$lSrc])) {
              $lSrcStaPro[$lSrc] = array();
            }
            $lSrcStaPro[$lSrc][$lSta] = array('display' => $lCrpStaDis[$lSta], 'pro_con' => $lPro);
          }
        }
      }
    }

    return $lSrcStaPro;
  }
}