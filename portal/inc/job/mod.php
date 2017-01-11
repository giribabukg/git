<?php
/**
 * Jobs: Data Modification
 *
 *  ABSTRACT! Description (bleibt CJob_Mod)
 *
 * @package    JOB
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 14657 $
 * @date $Date: 2016-06-22 17:50:37 +0200 (Wed, 22 Jun 2016) $
 * @author $Author: jwetherill $
 */
abstract class CInc_Job_Mod extends CCor_Mod_Base {

  public function __construct($aSrc, $aJobId = 0) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
    $this -> mInsertId = NULL;

    $lFie = CCor_Res::get('fie');
    foreach ($lFie as $lDef) {
      $this -> addField($lDef);
    }

    $lKnr = CCor_Cfg::get(MAND.'.'.$aSrc.'.knr');
    if (empty($lKnr)) {
      $this -> mDefaultKnr = CCor_Cfg::get(MAND.'.def.knr', 1000);
    } else $this -> mDefaultKnr = $lKnr;

    $this -> mDefaultWebstatus = CCor_Cfg::getFallback('job-'.$aSrc.'.webstatus', 'job.webstatus', 10);

    $this -> mInsertAsQuotation = CCor_Cfg::get('job.'.$aSrc.'.insertasquotation');
    if (is_null($this->mInsertAsQuotation)) {
      $this -> mInsertAsQuotation = CCor_Cfg::get('job.insertasquotation', true);
    }
  }

  public function getJobId() {
    return $this->mJobId;
  }

  public function setQuotation($aFlag = true) {
    $this->mInsertAsQuotation = (bool)$aFlag;
  }

  /**
   * @return IInc_Job_Writer
   */
  protected function getWriter() {
    if (!isset($this->mWriter)) {
      $this->mWriter = $this->createWriter();
    }
    return $this->mWriter;
  }

  protected function createWriter() {
    $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
    if ('mop' == $lWriter) {
      return new CInc_Job_Writer_Mop($this->mFie);
    } elseif ('portal' == $lWriter) {
      return new CInc_Job_Writer_Portal($this->mFie, $this -> mSrc);
    }
    return new CInc_Job_Writer_Alink($this->mFie);
  }

  public function & addField($aDef) {
    $lKey = $aDef['alias'];
    $lFie = array();
    $lFie['key'] = $aDef['alias'];
    $lFie['typ'] = $aDef['typ'];
    $lFie['par'] = $aDef['param'];
    $lFie['nat'] = $aDef['native'];
    $lFie['lis'] = $aDef['learn'];

    $this -> mFie[$lKey] = & $lFie;
    return $lFie;
  }

  /**
   * Preset any customer specific values in mUpd before we update
   *
   * Empty implementation here so we can overwrite in cust/mand
   */
  protected function presetUpdateValues() {
  }

  protected function doUpdate() {
    foreach ($this -> mOld as $lKey => $lVal) {
      if ($this -> fieldHasChanged($lKey)) {
        $this -> mUpd[$lKey] = $this -> getVal($lKey);
      }
    }
    if (empty($this -> mUpd)) {
      return TRUE;
    }

    $this -> presetUpdateValues();

    $lRet = $this -> forceUpdate($this -> mUpd);
    if ($lRet) {
      CJob_Utl_Shadow::reflectUpdate($this -> mSrc, $this -> mJobId, $this -> mUpd);
    }
    return $lRet;
  }

  public function forceUpdate($aArr = array()) {
    if (empty($aArr)) return TRUE;
    return $this -> writeUpdate($this -> mJobId, $aArr);
  }

  public function writeUpdate($aJobId, $aValues) {
    return $this->getWriter()->update($aJobId, $aValues);
  }

  protected function getKnr() {
    return $this->mDefaultKnr;
  }

  /**
   * Preset any customer specific values (like BONr) in mVal before we insert
   *
   * Empty here so we can overwrite in cust/mand
   */
  protected function presetInsertValues() {
  }

  protected function doInsert() {
    $this -> mVal['webstatus'] = $this->mDefaultWebstatus;
    $this -> mVal['src'] = $this -> mSrc;
    $lNow = date('Y-m-d H:i:s');
    $this -> mVal['last_status_change'] = $lNow;
    if (empty($this->mVal['net_knr'])) {
      $lKnr = $this->getKnr();
      $this -> setVal('net_knr', $lKnr);
    }
    $this -> presetInsertValues();
    
    if (CCor_Cfg::get('portal-jobid-field')) {
      if (empty($this->mVal['portal_jobid'])) {
        $this->mVal['portal_jobid'] = $this -> insertPortalJobId(0, $this -> mSrc, NULL); // reserve a portalId for this new job and pass it to the writer.
      } else {
        $this->mVal['portal_jobid'] = $this -> insertPortalJobId(0, $this -> mSrc, $this->mVal['portal_jobid']);
      }
    }
    
    $lValues = array();
    foreach ($this -> mVal as $lKey => $lVal) {
      if (!$this -> hasField($lKey)) {
        continue;
      }
      if ('' === $lVal) {
        continue;
      }
      $lValues[$lKey] = $lVal;
    }
    $lRet = $this->getWriter()->insert($lValues, $this -> mInsertAsQuotation);
    if ($lRet) {
      $this -> mInsertId = (string)$lRet;
      // enter the jobId comes from alink/mop and save it to reserved portalId
      if (CCor_Cfg::get('portal-jobid-field')) {
        $lSql = 'UPDATE `al_portal_job_ids` SET';
        $lSql.= ' `jobid`='.esc($this -> mInsertId);
        $lSql.= ' WHERE  `id`="'.$this->mVal['portal_jobid'].'"';
        $lSql.= ' AND `src`="'.$this -> mSrc.'" LIMIT 1';
        CCor_Qry::exec($lSql);
      }
      
      $this -> mJobId = $this -> mInsertId;
      $lMod = new CJob_His($this -> mSrc, $this -> mInsertId);
      $lMod -> add(htStatus, lan('job-'.$this -> mSrc.'.menu').' job created', '', '', '','',10);
      $lArr = array();
      $lArr['fti_1'] = $lNow;
      $lArr['lti_1'] = $lNow;
      CJob_Utl_Shadow::reflectInsert($this -> mSrc, $this -> mInsertId, $this -> mVal, $lArr);
      if (CCor_Cfg::get('extended.reporting')) {
      	CJob_Utl_Shadow::reflectInsertReport($this -> mSrc, $this -> mInsertId, $this -> mVal);
      }
      return TRUE;
    } else {
      $this -> mInsertId = NULL;
      // if the writer failed to save the job, delete the reserved item in the portalId table
      if (CCor_Cfg::get('portal-jobid-field')) {CCor_Qry::exec('DELETE FROM `al_portal_job_ids` WHERE  `id`='.$this->mVal['portal_jobid'].' AND `src`="'.$this -> mSrc.'" LIMIT 1');}
      return FALSE;
    }
  }
  
  protected function insertPortalJobId($aJobId, $aSrc, $aRelatedPortalId=NULL) {
    $lIdSettings = CCor_Cfg::get('portal-jobid-field-settings');
    $lJobTypCode = (isset($lIdSettings)) ? $lIdSettings[$aSrc] : $aSrc;
    
    if (is_null($aRelatedPortalId)) {
      $lQry = new CCor_Qry('INSERT INTO `al_portal_job_ids` (`jobid`, `mand`, `src`, `src_code`) VALUES ('.esc($aJobId).', '.MID.', "'.$aSrc.'", '.esc($lJobTypCode).')');
    }
    else {
      $lQry = new CCor_Qry('INSERT INTO `al_portal_job_ids` (`id`, `jobid`, `mand`, `src`, `src_code`) VALUES ('.esc($aRelatedPortalId).', '.esc($aJobId).', '.MID.', "'.$aSrc.'", '.esc($lJobTypCode).')');
    }
    if (isset($lIdSettings)) {
      $lSeperator = $lIdSettings['seperator'];
      $lId = $lQry->getInsertId().$lSeperator.$lJobTypCode;
    }
    else $lId = $lQry->getInsertId();
    
    return $lId;
  }

  protected function beforePost($aNew = FALSE) {
    $this -> setKeyword();
  }

  protected function doDelete($aId) {
    $lSql = 'DELETE FROM '.$this -> mTbl.' ';
    $lSql.= 'WHERE id='.$this -> mJobId.' LIMIT 1';
    return CCor_Qry::exec($lSql);
  }

  public function getInsertId() {
    return $this -> mInsertId;
  }

  public function addHistory($aType, $aSubject, $aMsg = '', $aAdd = NULL) {
    $lMod = new CJob_His($this -> mSrc, $this -> mJobId);
    $lMod -> add($aType, $aSubject, $aMsg, $aAdd);
  }

  protected function addToLearnTable($aList, $aValue) {
    $lVal = trim($aValue);
    if (empty($lVal)) return;
    $lSql = 'REPLACE INTO al_fie_choice SET mand='.MID.',';
    $lSql.= 'alias="'.addslashes($aList).'",';
    $lSql.= 'val="'.addslashes(trim($lVal)).'",';
    $lSql.= 'stamp="'.date('Y-m-d H:i:s').'"';
    CCor_Qry::exec($lSql);
  }

  protected function afterPost($aNew = FALSE) {
    if (!$aNew) {
      $this -> checkProtocol();
    }

    $lPhraseFields = CCor_Cfg::get('job-cms.fields');
    $lClientKey = $lPhraseFields['client_key'];

    foreach ($this -> mFie as $lKey => $lDef) {
      $lTyp = $lDef['typ'];
      $lLis = $lDef['lis'];

      if (empty($lLis) && $lKey !== $lClientKey) continue;

      if (('string' == $lTyp) or ('pick' == $lTyp)) {
        if (($aNew) or ($this -> fieldHasChanged($lKey))) {
          $lVal = $this -> getVal($lKey);
          $this -> addToLearnTable($lLis, $lVal);

          if($lKey == $lClientKey && !empty($lVal)) {
            $lOld = trim($this -> getFieOld($lDef));
            if(!empty($lOld)) {
              $lCmsMod = new CJob_Cms_Mod($this -> mSrc, $this -> mJobId, $this -> mVal);
              $lCmsMod -> updateProductRefs($lVal);
            }
          }
        }
      }
    }

    if ($aNew) {
      $this -> triggerEvent('eve_draft');
    }
  }

  public function triggerEvent($aEvent, $aMsg = '') {
    $lEve = $this->getCrpEvent($aEvent);
    if (empty($lEve)) {
      return;
    }
    $lFac = new CJob_Fac($this->mSrc, $this -> mJobId);
    $lDat = $lFac->getDat();
    $lMsg['body'] = $aMsg;
    $lEve = new CJob_Event($lEve, $lDat, $lMsg);
    $lEve -> execute();
  }
  
  protected function getCrpEvent($aEvent) {
    $lCrp = CCor_Res::getByKey('code', 'crpmaster');
    if (!isset($lCrp[$this -> mSrc])) {
      return;
    }
    $lRow = $lCrp[$this -> mSrc];
    return $lRow[$aEvent]; 
  }

  protected function checkProtocol() {
    $lUpd = array();
    $lFie = CCor_Res::getByKey('alias', 'fie');

    $lUSelect = CCor_Res::extract('id', 'fullname', 'usr');
    $lGSelect = CCor_Res::extract('id', 'name', 'gru');

    foreach ($this -> mFie as $lKey => $lRow) {
      if (!isset($lFie[$lKey])) {
        $this -> dbg('Unknown Alias '.$lKey, mlWarn);
        continue;
      }

      $lDef = $lFie[$lKey];
      $lFla = intval($lDef['flags']);
      $lTyp = $lDef['typ'];

      if (bitset($lFla, ffProtocol) and ($this -> fieldHasChanged($lKey))) {
        $lItm = array();
        $lItm['old'] = $this -> getOld($lKey);
        $lItm['new'] = $this -> getVal($lKey);
        $lUpd[$lKey] = $lItm;
      }

      if (bitset($lFla, ffOnchange) and ($this -> fieldHasChanged($lKey))) {
        $lItm = array();
        $lItm['old'] = $this -> getOld($lKey);
        $lItm['new'] = $this -> getVal($lKey);
//         if (!empty($lItm['old'])) {
          $lItm['old'] = $lTyp == 'uselect' ? $lUSelect[$lItm['old']] : $lItm['old']; // TODO: not the most beautiful solution for sure
          $lItm['new'] = $lTyp == 'uselect' ? $lUSelect[$lItm['new']] : $lItm['new'];

          $lItm['old'] = $lTyp == 'gselect' ? $lGSelect[$lItm['old']] : $lItm['old'];
          $lItm['new'] = $lTyp == 'gselect' ? $lGSelect[$lItm['new']] : $lItm['new'];

          $lChanges[] = $lFie[$lKey]['name_'.LAN].' '.lan('job.fields.changes').' '.$lItm['old'].' '.lan('lib.to').' '.$lItm['new'];
//         }
      }
    }

    if (!empty($lUpd)) {
      $this -> addHistory(htEdit, lan('job.changes'), '', array('upd' => $lUpd));
    }

    if (!empty($lChanges)) {
      $lMsg = implode(LF, $lChanges);
      $this -> triggerEvent('eve_jobchange', $lMsg);
    }
  }

  public function copyAnfToJob($aJobId) {
    return $this -> getWriter() -> copyAnfToJob($aJobId);
  }

  public function getFlags() {
    if (isset($this -> mJfl)) {
      return $this -> mJfl;
    }
    $lSql = 'SELECT flags FROM al_job_shadow_'.intval(MID).' WHERE 1 ';
    $lSql.= 'AND jobid="'.$this -> mJobId.'"';
    $lRet = CCor_Qry::getInt($lSql);
    $this -> mJfl = $lRet;
    return $lRet;
  }

  /**
   * @param Bitmask
   * @return boolean
   */
  public function getFlag($aFlag) {
    $lFla = $this -> getFlags();
    if (FALSE === $lFla) return FALSE;
    return bitSet($lFla, $aFlag);
  }

  public function setFlag($aFlag, $aMsg = '') {
    $this -> dbg('SetFlag '.$aFlag);
    $lFla = $this -> getFlags();
    if (FALSE === $lFla) {
      $lSet = intval($aFlag);
      $lSql = 'REPLACE INTO al_job_shadow_'.intval(MID).' SET ';
      $lSql.= 'src='.esc($this -> mSrc).',';
      $lSql.= 'jobid='.esc($this -> mJobId).',';
      $lSql.= 'flags='.esc($lSet);
      CCor_Qry::exec($lSql);
    } else {
      $lSet = intval($aFlag) | $lFla;
      $lSql = 'UPDATE al_job_shadow_'.intval(MID).' SET ';
      $lSql.= 'flags='.$lSet.' ';
      $lSql.= 'WHERE jobid='.esc($this -> mJobId);
      CCor_Qry::exec($lSql);

      if (!empty($aMsg) && $aFlag == jfOnhold) {
        $lSql = 'UPDATE al_job_shadow_'.intval(MID).' SET ';
        $lSql.= 'flag_onhold_reason='.esc($aMsg).' ';
        $lSql.= 'WHERE jobid='.esc($this -> mJobId);
        CCor_Qry::exec($lSql);
      }

      if (!empty($aMsg) && $aFlag == jfCancelled) {
        $lSql = 'UPDATE al_job_shadow_'.intval(MID).' SET ';
        $lSql.= 'flag_cancel_reason='.esc($aMsg).' ';
        $lSql.= 'WHERE jobid='.esc($this -> mJobId);
        CCor_Qry::exec($lSql);
      }
    }
  }

  public function resetFlag($aFlag, $aMsg = '') {
    $this -> dbg('ReSetFlag '.$aFlag);
    $lFla = $this -> getFlags();
    if (FALSE === $lFla) {
      $lSet = intval($aFlag);
      $lSql = 'REPLACE INTO al_job_shadow_'.intval(MID).' SET ';
      $lSql.= 'src='.esc($this -> mSrc).',';
      $lSql.= 'jobid='.esc($this -> mJobId).',';
      $lSql.= 'flags='.esc($lFla);
      CCor_Qry::exec($lSql);
    } else {
      $lSet = ~intval($aFlag) & $lFla;
      $lSql = 'UPDATE al_job_shadow_'.intval(MID).' SET ';
      $lSql.= 'flags='.$lSet.' ';
      $lSql.= 'WHERE jobid='.esc($this -> mJobId);
      CCor_Qry::exec($lSql);

      if (!empty($aMsg) && $aFlag == jfOnhold) {
        $lSql = 'UPDATE al_job_shadow_'.intval(MID).' SET ';
        $lSql.= 'flag_onhold_reason='.esc('').' ';
        $lSql.= 'WHERE jobid='.esc($this -> mJobId);
        CCor_Qry::exec($lSql);

        $lSql = 'UPDATE al_job_shadow_'.intval(MID).' SET ';
        $lSql.= 'flag_continue_reason='.esc($aMsg).' ';
        $lSql.= 'WHERE jobid='.esc($this -> mJobId);
        CCor_Qry::exec($lSql);
      }

      if (!empty($aMsg) && $aFlag == jfCancelled) {
        $lSql = 'UPDATE al_job_shadow_'.intval(MID).' SET ';
        $lSql.= 'flag_cancel_reason='.esc('').' ';
        $lSql.= 'WHERE jobid='.esc($this -> mJobId);
        CCor_Qry::exec($lSql);

        $lSql = 'UPDATE al_job_shadow_'.intval(MID).' SET ';
        $lSql.= 'flag_revive_reason='.esc($aMsg).' ';
        $lSql.= 'WHERE jobid='.esc($this -> mJobId);
        CCor_Qry::exec($lSql);
      }
    }
  }

  protected function isKeywordLocked() {
    $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
    if ('mop' == $lWriter || 'portal' == $lWriter) return false;

    return ((!empty($this->mJobId)) and ('A' != substr($this->mJobId,0,1)));
  }

  protected function setKeyword() {
    if ($this -> isKeywordLocked()) return;

    $lArr = CCor_Cfg::get('job.keyw');
    $lSrcArr = CCor_Cfg::get('job-'.$this -> mSrc.'.keyw');

    if (!empty($lSrcArr)) {
      $lArr = $lSrcArr;
    }

    if (empty($lArr)) {
      return;
    }

    $lFieldType = CCor_Res::extract('alias', 'typ', 'fie');
    $lTypeGroup = CCor_Res::extract('id', 'name', 'gru');
    $lTypeUser = CCor_Res::extract('id', 'fullname', 'usr');

    $lRet = '';
    if ($this -> hasValues($lArr)) {
      foreach ($lArr as $lAlias) {
        $lVal = trim($this -> getVal($lAlias));
        if (('gewicht' == $lAlias) and !empty($lVal) and ('g' != substr($lVal, -1))) {
          $lVal.= 'g';
        }

        switch ($lFieldType[$lAlias]) {
          case 'date':
            $lVal = date_format(date_create_from_format('Y-m-d', $lVal), lan('lib.date.long'));
          break;
          case 'datetime':
            $lVal = date_format(date_create_from_format('Y-m-d H:i:s', $lVal), lan('lib.datetime.long'));
          break;
          case 'gselect':
            $lVal = $lTypeGroup[$lVal];
          break;
          case 'uselect':
            $lVal = $lTypeUser[$lVal];
          break;
        }

        $lRet = cat($lRet, $lVal, ' ');
      }

      if ($lRet == '') return;

      $this -> mUpd['stichw'] = $lRet;
      $this -> setVal('stichw', $lRet);
    }
  }

  public function insertIntoProjectStatusInfo($aJobId, $aProId, $aSubId, $aJobwebstatus = '') {
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
    $this -> mCrpId = $lCrp[$this -> mSrc];
    $lCrpStaDis = CCor_Res::extract('status', 'display', 'crp', $this -> mCrpId);
    $lCrpStaPro = CCor_Res::extract('status', 'pro_con', 'crp', $this -> mCrpId);

    if (!empty($aSubId)) { // kann passieren, wenn das Insert-SQL fehlschlägt, zB wegen fehlendem Strukturabgleich!
      if (empty($aJobwebstatus)) {
        //wird beim Anlegen eines neuen Jobs -als Kopie aus ProjektItem- aufgerufen
        $lWebstatus = STATUS_DRAFT;
        $lIncProStatus = (isset($lCrpStaPro[$lWebstatus]) ? $lCrpStaPro[$lWebstatus] : 0);

        $lSql = 'INSERT INTO `al_job_pro_crp` SET ';
        $lSql.= 'mand='.MID.',';
        $lSql.= 'pro_id='.esc($aProId).',';
        $lSql.= 'sub_id='.esc($aSubId).',';
        $lSql.= 'src='.esc($this -> mSrc).',';
        $lSql.= 'jobid='.esc($aJobId).',';
        $lSql.= 'job_status='.esc($lCrpStaDis[$lWebstatus]);
        if (0 < $lIncProStatus) {
          $lSql.= ',pro_status='.esc($lIncProStatus).',';
          $lSql.= 'fti_'.$lIncProStatus.'='.esc(date('Y-m-d H:i:s'));
        }
        $lQry = new CCor_Qry();
        $lQry -> query($lSql);

      } else { // wird bei Assign Job to Projekt aufgerufen
        $lJobstatus = $aJobwebstatus;

        $lStaPro = array();
        foreach ($lCrpStaPro as $lSta => $lPro) {
          if (0 < $lPro) {
            $lStaPro[ $lSta ] = array('display' => $lCrpStaDis[$lSta], 'pro_con' => $lPro);
          }
        }

        $lSql = 'SELECT `jobid`,`webstatus`,`fti_1`,`fti_2`,`fti_3`,`fti_4`,`fti_5`,`fti_6`,`fti_7`,`fti_8`,`fti_9`,`fti_10`,`fti_11`,`lti_1`,`lti_2`,`lti_3`,`lti_4`,`lti_5`,`lti_6`,`lti_7`,`lti_8`,`lti_9`,`lti_10`,`lti_11`';
        $lSql.= ' FROM `al_job_shadow_'.MID.'` WHERE jobid='.esc($aJobId);
        $lQry = new CCor_Qry();
        $lQry -> query($lSql);
        $lJobStatusInfo = array();
        foreach ($lQry as $lRow) {
          $lJobStatusInfo = $lRow;
        }

        if (!empty($lJobStatusInfo)) {
          $lFiTime = array();
          $lLaTime = array();
          foreach ($lStaPro as $lK => $lDisplay) {
            if (0 < $lDisplay['pro_con']) {
              $lTimeIndx = $lDisplay['pro_con'];
            } else {
              $lTimeIndx = $lDisplay['display'];
            }
            if(!isset($lFiTime[$lDisplay['pro_con']])) {
              $lFiTime[$lTimeIndx] = '000-00-00 00:00:00';
            }
            if(!isset($lLaTime[$lTimeIndx])) {
              $lLaTime[$lTimeIndx] = '000-00-00 00:00:00';
            }
            $lFirst = 'fti_'.$lDisplay['display'];
            $lLast  = 'lti_'.$lDisplay['display'];
            if (!empty($lJobStatusInfo[$lFirst]) AND $lFiTime[$lTimeIndx] < $lJobStatusInfo[$lFirst]) {
              $lFiTime[$lTimeIndx] = $lJobStatusInfo[$lFirst];
            }
            if (!empty($lJobStatusInfo[$lLast]) AND $lLaTime[$lTimeIndx] < $lJobStatusInfo[$lLast]) {
              $lLaTime[$lTimeIndx] = $lJobStatusInfo[$lLast];
            }
          }//end_foreach ($lJobstatus as $lDisplay)
          $lSql = 'INSERT INTO `al_job_pro_crp` SET ';
          $lSql.= 'mand='.MID.',';
          $lSql.= 'pro_id='.esc($aProId).',';
          $lSql.= 'sub_id='.esc($aSubId).',';
          $lSql.= 'src='.esc($this -> mSrc).',';
          $lSql.= 'jobid='.esc($aJobId).',';
          $lSql.= 'job_status='.esc($lCrpStaDis[$lJobstatus]);
          if (isset($lStaPro[$lJobstatus])) {
            $lProStatus = $lStaPro[$lJobstatus]['pro_con'];//$lSrcStaPro[$lSrc][ $lRow['status'] ] = array($lRow['display'], $lRow['pro_con']);
            $lSql.= ',pro_status='.esc($lProStatus);
          }
          foreach ($lFiTime as $lDis => $lTim) {
            if ('000-00-00 00:00:00' < $lTim) {
              $lSql.= ',fti_'.$lDis.'='.esc($lTim);
            }
          }
          foreach ($lLaTime as $lDis => $lTim) {
            if ('000-00-00 00:00:00' < $lTim) {
              $lSql.= ',lti_'.$lDis.'='.esc($lTim);
            }
          }
          $lQry -> query($lSql);
        }
      }
    }
  }

  public function deleteFromProjectStatusInfo($aJobId = '', $aProId = '', $aSubId = '') {
    $lSql = 'DELETE FROM `al_job_pro_crp` WHERE `mand`='.MID;
    if (!empty($aProId)) {
      $lSql.= ' AND `pro_id`='.esc($aProId);
    }
    if (!empty($aSubId)) {
      $lSql.= ' AND `sub_id`='.esc($aSubId);
    }
    if (!empty($aJobId)) {
      $lSql.= ' AND `jobid`='.esc($aJobId);
      $lSql.= ' LIMIT 1';
    }
    CCor_Qry::exec($lSql);
  }

  public function updateProjectStatusInfo($aJobId, $aProId, $aSubId, $aFromProId = '', $aFromSubId = '') {
    $lIncProStatus = 0;
    if (empty($aFromProId)) {
      $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');
      $this -> mCrpId = $lCrp[$this -> mSrc];
    }
    $lSql = 'UPDATE `al_job_pro_crp` SET ';
    $lSql.= 'pro_id='.esc($aProId).',';
    $lSql.= 'sub_id='.esc($aSubId);
    $lSql.= ' WHERE mand='.MID;
    if (!empty($aFromProId)) {
      $lSql.= ' AND pro_id='.esc($aFromProId);
    }
    if (!empty($aFromSubId)) {
      $lSql.= ' AND sub_id='.esc($aFromSubId);
    }
    // evtl. auch Uebergabe der src, da in Table src & jobid = unique
    $lSql.= ' AND jobid='.esc($aJobId);
    CCor_Qry::exec($lSql);
  }
}