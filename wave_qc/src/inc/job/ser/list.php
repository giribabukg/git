<?php
/**
 * Jobs: Search - List
 *
 *  Description
 *
 * @package    JOB
 * @subpackage    Ser
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 11415 $
 * @date $Date: 2015-11-16 23:48:52 +0800 (Mon, 16 Nov 2015) $
 * @author $Author: jwetherill $
 */
class CInc_Job_Ser_List extends CHtm_List {

  protected $mUsr;

  public function __construct($aAnf = TRUE, $aJob = TRUE, $aFil = array()) {

    parent::__construct('job-ser');

    $this -> mShowHdr = FALSE;
    $this -> mShowSubHdr = FALSE;
    $this -> mStdLnk = '';
    $this -> setAtt('width', '100%');
    $this -> getCriticalPaths();
    $this -> mFie = CCor_Res::get('fie');
    $this -> mUsr = CCor_Usr::getInstance();

    $this -> mDefs = CCor_Res::getByKey('alias', 'fie');
    $this -> mCnd = new CCor_Cond();

    $this -> getPrefs('job-ser');

    $this -> getIterator();
    $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
    if ($lWriter == 'alink') {
      if ($aAnf) {
        if (!$aJob) {
          $this -> mIte -> addCondition('jobid', '>', 'A');
        }
      } else {
        $this -> mIte -> addCondition('jobid', '<', 'A');
      }
    }
    $this -> addCtr();

    $lDef = $this -> mDefs['src'];
    $this -> onAddField($lDef);

    $this -> addColumn('src', '', TRUE, array('width' => 16));
    $this -> addColumns();

    unset($this -> mSer['anf']);
    unset($this -> mSer['job']);
    unset($this -> mSer['arc']);

    $this -> mFil = $aFil;

    unset($this -> mSer['flags']);

    $this -> addFilterConditions();
    $this -> addSearchConditions();
    $this -> addSrcConditions();

    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> mAllFlags = CCor_Res::get('fla');
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');

    $this -> lAplstatus = array();
    foreach ($lCrp as $lCode => $lId) {
      $lSql = 'SELECT status FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$lId.' AND apl=1';
      $lQry = new CCor_Qry($lSql);
      foreach($lQry as $lRow){
        if (!empty($lRow['status'])){
          $this -> lAplstatus[$lCode][] = $lRow['status'];
        }
      }

      if (isset($this -> mDdl[$lCode])) {
        $lDdl = $this -> mDdl[$lCode];
        foreach ($lDdl as $lddl) {
          $this -> requireAlias($lddl);
        }
        $this -> requireAlias('src');
      }
    }

    $this -> mShowFlaWithFlags = CCor_Cfg::get('show.flawithflags', TRUE);
  }

  protected function requireAlias($aAlias) {
    if (!isset($this -> mDefs[$aAlias])) {
      $this -> dbg('Field '.$aAlias. ' not defined', mlWarn);
      return;
    }

    $lDef = $this -> mDefs[$aAlias];
    $this -> mIte -> addDef($lDef);
  }

  protected function getCriticalPaths() {
    $lCrp = CCor_Res::extract('code', 'id', 'crpmaster');

    $this -> mCrp = array();
    $lAllJobs = CCor_Cfg::get('menu-aktivejobs');
    foreach ($lAllJobs as $lJobs) {
      if ('all' != substr($lJobs, 4, 3)) {
        if (isset($lCrp[ltrim($lJobs, 'job-')])) {
          $this -> mCrp[ltrim($lJobs, 'job-')] = CCor_Res::get('crp', $lCrp[ltrim($lJobs, 'job-')]);
        }
      }
    }
  }

  protected function addCondition($aAlias, $aOp, $aValue) {
    if (!isset($this -> mDefs[$aAlias])) {
      $this -> dbg('Unknown Field '.$aAlias, mlWarn);
      return;
    }
    $lDef = $this -> mDefs[$aAlias];
    $this -> mIte -> addCondition($lDef['native'], $aOp, $aValue);
  }

  protected function getIterator() {
    if (CCor_Cfg::get('job.writer.default') == 'portal') {
      $this -> mIte = new CCor_TblIte('all', $this -> mWithoutLimit);
      $this -> mIte -> addField('jobid');
    } else {
      $this -> mIte = new CApi_Alink_Query_Getjoblist();
      $this -> mIte -> addField('jobnr', 'jobnr');
    }
  }

  protected function addFilterConditions() {
    if (empty($this -> mFil)) return;

    foreach ($this -> mFil as $lKey => $lValue) {
      if (!empty($lValue)) {
        if (is_array($lValue) AND $lKey == "webstatus") {
          $lStates = "";

          foreach ($lValue as $lWebstatus => $foo) {
            if ($lWebstatus == 0) {
              break;
            } else {
              $lStates.= '"'.$lWebstatus.'",';
            }
          }

          if (!empty($lStates)) {
            $lStates = substr($lStates, 0, -1);

            $this -> addCondition('webstatus', 'in', $lStates);
          }
        } elseif (is_array($lValue) AND $lKey == "flags") {
          $lStates = "";

          foreach ($lValue as $lKey => $lValue) {
            if($lKey == 0){
              break;
            } else {
              $lStates.= "((flags & ".$lKey.") = ".$lKey.") OR ";
            }
          }
          $lStates = (!empty($lStates)) ? substr($lStates, 0, strlen($lStates) - 4) : '';

          if (!empty($lStates)) {
            $lJobIds = '';
            $lSQL = 'SELECT jobid FROM al_job_shadow_'.MID.' WHERE '.$lStates.';';
            $lQry = new CCor_Qry($lSQL);
            foreach ($lQry as $lRow) {
              $lJobId = trim($lRow['jobid']);
              if (!empty($lJobId)) {
                $lJobIds.= '"'.$lJobId.'",';
              }
            }
            $lJobIds = strip($lJobIds);

            if (!empty($lJobIds)) {
              $this -> mIte -> addCondition('jobid', 'IN', $lJobIds);
            } else {
              $this -> mIte -> addCondition('jobid', '=', 'NOJOBSFOUND');
            }
          }
        }
      }
    }
  }

  protected function addSearchConditions() {
    if (empty($this -> mSer)) return;
    foreach ($this -> mSer as $lAli => $lVal) {
      if (empty($lVal)) continue;
      if (!isset($this -> mDefs[$lAli])) {
        $this -> dbg('Unknown Field '.$lAli, mlWarn);
        continue;
      }
      $lDef = $this -> mDefs[$lAli];
      $lCnd = $this -> mCnd -> convert($lAli, $lVal, $lDef['typ']);
      if ($lCnd) {
        foreach($lCnd as $lItm) {
          $this -> mIte -> addCondition($lItm['field'], $lItm['op'], $lItm['value']);
        }
      }
    }
  }

  protected function addColumns() {
    $lUsr = CCor_Usr::getInstance();
    $lCol = explode(',', $lUsr -> getPref($this -> mMod.'.cols'));

    foreach ($lCol as $lFid) {
      if (isset($this -> mFie[$lFid])) {
        $lDef = $this -> mFie[$lFid];
        $this -> addField($lDef);
        $this -> onAddField($lDef);
      }
    }
  }

  protected function onAddField($aDef) {
    $this -> mIte -> addDef($aDef);
  }

  protected function loadFlags() {
    $lArr = array_keys($this -> mIte);
    if (empty($lArr)) return;
    $lSql = 'SELECT jobid,flags FROM al_job_shadow_'.intval(MID).' WHERE jobid IN (';
    foreach ($lArr as $lJid) {
      $lSql.= esc($lJid).',';
    }
    $lSql = strip($lSql).')';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mIte[$lRow['jobid']]['flags'] = $lRow['flags'];
    }
  }

  protected function getTdSrc() {
    $lSrc = $this -> getCurVal();
    $lImg = (THEME === 'default' ? 'job-'.$lSrc : CApp_Crpimage::getColourForSrc($lSrc));
    $lRet = img('img/ico/16/'.$lImg.'.gif');
    return $this -> tdClass($this -> a($lRet), 'w16 ac');
  }

  protected function getTdJobnr() {
    $lJid = $this -> getVal('jobid');
    return $this -> tda(jid($lJid));
  }

  protected function getTdFlags() {
    $lJobId = $this -> getVal('jobid');
    $lCur = $this -> getCurInt();
    $lFla = CCor_Res::extract('val', 'name_'.LAN, 'jfl');
    $lRet = '';
    foreach ($lFla as $lKey => $lVal) {
      if (bitSet($lCur, $lKey)) {
        $lMsg = '';
        if ($lKey == jfOnhold) {
          $lSql = 'SELECT flag_onhold_reason FROM al_job_shadow_'.MID.' WHERE jobid='.esc($lJobId);
          $lRes = CCor_Qry::getStr($lSql);
          if (!empty($lRes)) {
            $lRes = str_replace("\r\n", "<br>", $lRes);
            $lMsg = ': '.$lRes;
          }
        }

        if ($lKey == jfCancelled) {
          $lSql = 'SELECT flag_cancel_reason FROM al_job_shadow_'.MID.' WHERE jobid='.esc($lJobId);
          $lRes = CCor_Qry::getStr($lSql);
          if (!empty($lRes)) {
            $lRes = str_replace("\r\n", "<br>", $lRes);
            $lMsg = ': '.$lRes;
          }
        }

        $lRet.= img('img/jfl/'.$lKey.'.gif', array('data-toggle' => 'tooltip', 'data-tooltip-head' => $lVal, 'data-tooltip-body' => $lMsg));
      }
    }

    // View CRP Flags
    if ($this -> mShowFlaWithFlags AND isset($this -> mIte[$lJobId]['loop_typ'])) {
      foreach ($this -> mIte[$lJobId]['loop_typ'] as $lTyp => $lLoopId) {
        if (isset($this -> mAllFlags[$lTyp])) {
          $lFlagEve = $this -> mAllFlags[$lTyp];
          $lName = $lFlagEve['name_'.LAN];
          $lAlias = $lFlagEve['alias'];
          if (isset($this -> mCrpFlagsAliase[$lAlias]['ddl_fie'])) {
            $lDdl = $this -> mCrpFlagsAliase[$lAlias]['ddl_fie'];
            $lVal = $this -> getVal($lDdl);
            $lDat = new CCor_Date($lVal);
            $lRetDate = $lDat -> getFmt(lan('lib.date.long'));
            $lRetDate = htm($lRetDate);
          }

          $lRetFlagActions = CApp_Apl_Loop::getFlagCommitList($lLoopId, $lFlagEve);
          $lHeadline = (!empty($lRetDate) ? $lName.': '.$lRetDate : $lName);
          if (isset($this -> mIte[$lJobId][$lAlias]) AND isset($lFlagEve['eve_'.$this -> mIte[$lJobId][$lAlias].'_ico'])) {
            $lImg = $lFlagEve['eve_'.$this -> mIte[$lJobId][$lAlias].'_ico'];
            $lRet.= img('img/flag/'.$lImg.'.gif', array('data-toggle' => 'tooltip', 'data-tooltip-head' => $lHeadline, 'data-tooltip-body' => $lRetFlagActions));
          }
        }
      }
    }

    return $this -> tdClass($lRet, 'w16 '. $lJobId);
  }

  protected function getTdWebstatus() {
      $lVal = $this -> getCurInt();
      $lSrc = $this -> getVal('src');
      if (isset($this -> mCrp[$lSrc])) {
          return $this -> getExtWebstatus($lVal, $this -> mCrp[$lSrc]);
      }
      $lDis = $lVal / 10;
      $lPath = CApp_Crpimage::getSrcPath($lSrc, 'img/crp/'.$lDis.'b.gif');
      $lRet = img($lPath, array('style' => 'margin-right:1px'));
      return $this -> tda($lRet);
  }

  protected function getExtWebstatus($aState, $aCrp) {
    $lDisplay = CCor_Cfg::get('status.display', 'progressbar');

      $lVal = $aState;
      $lNam = '[unknown]';
      $lSrc = $this->getVal('src');
    $lRet = '';

      foreach ($aCrp as $lRow) {
      if (($lDisplay == 'progressbar' && $lVal >= $lRow['status']) OR ($lDisplay == 'activeonly' && $lVal == $lRow['status'])) {
              $lPath = CApp_Crpimage::getSrcPath($lSrc, 'img/crp/'.$lRow['display'].'b.gif');
              $lRet.= img($lPath, array('style' => 'margin-right:1px'));
              $lNam = $lRow['name_'.LAN];
      } else if (($lDisplay == 'progressbar' && $lVal < $lRow['status']) OR ($lDisplay == 'activeonly' && $lVal != $lRow['status'])) {
              $lPath = CApp_Crpimage::getSrcPath($lSrc, 'img/crp/'.$lRow['display'].'l.gif');
              $lRet.= img($lPath, array('style' => 'margin-right:1px'));
          }
      }
      $lRet.= NB.htm($lNam);
      return $this -> tda($lRet);
  }

  protected function getLink() {
      $lSrc = $this -> getVal('src');
      $lJid = $this -> getVal('jobid');
      return 'index.php?act=job-'.$lSrc.'.edt&amp;jobid='.$lJid;
  }

  /**
   * Set Jobtype Condition to Itterator
   * Before show Jobs, ask if user has a Right for Jobtype.
   */
  protected function addSrcConditions() {
    $lAvaSrc = Array();
    $lSrcCnd ='';
    $lAvaSrc = CCor_Cfg::get('menu-aktivejobs');
    if (!empty($lAvaSrc)) {
      foreach ($lAvaSrc as $lRow)  {
        if ($this -> mUsr -> canRead($lRow)) {
          $lSrcCnd.= '"'.substr($lRow,4).'",';
        }
      }
      if ($lSrcCnd != '') {
        $lSrcCnd = substr($lSrcCnd, 0, -1);
        $this -> mIte -> addCondition('src', 'IN', $lSrcCnd);
      } else {
        $this -> mIte -> addCondition('src', '=', $lSrcCnd);
      }
    } else {
      return;
    }
  }

  protected function onBeforeContent() {
    $lRet = parent::onBeforeContent();
    $this -> mIte = $this -> mIte -> getArray('jobid');
    $this -> loadFlags();
//     $this -> loadApl();
    return $lRet;
  }


//   protected function isAplType($aType) {
//     return substr($aType,0,3) == 'apl';
//   }

//   protected function loadApl() {
//     $lArr = array_keys($this -> mIte);
//     if (empty($lArr)) return;

//     $lSql = 'SELECT id,jobid,typ FROM al_job_apl_loop WHERE 1';
//     $lSql.= ' AND status='.esc(CApp_Apl_Loop::APL_LOOP_OPEN);
//     $lSql.= ' AND mand='.intval(MID);
//     $lSql.= ' AND jobid IN (';
//     foreach ($lArr as $lJid) {
//       $lSql.= esc($lJid).',';
//     }
//     $lSql = strip($lSql).') ORDER BY id ASC';
//     $lQry = new CCor_Qry($lSql);
//     foreach ($lQry as $lRow) {
//       if ($this -> isAplType($lRow['typ'])) {
//         $this -> mIte[$lRow['jobid']]['loop_id'] = $lRow['id'];
//       } else {
//         $this -> mIte[$lRow['jobid']]['loop_typ'][$lRow['typ']] = $lRow['id'];
//         if (isset($this -> mAllFlags[$lRow['typ']])) {
//           $Flag = $this -> mAllFlags[$lRow['typ']];
//           if (!empty($Flag['alias']) AND !isset($this -> mCrpFlagsAliase[$Flag['alias']])) {
//             $this -> mCrpFlagsAliase[$Flag['alias']]['alias'] = $lRow['typ'];
//             if (!empty($Flag['ddl_fie'])) {
//               $this -> mCrpFlagsAliase[$Flag['alias']]['ddl_fie'] = $Flag['ddl_fie'];
//             }
//           }
//           if (!empty($Flag['ddl_fie']) AND !isset($this -> mCrpFlagsDdl[$Flag['ddl_fie']])) {
//             $this -> mCrpFlagsDdl[ $Flag['ddl_fie'] ]['ddl_fie'] = $lRow['typ'];
//             if (!empty($Flag['alias'])) {
//               $this -> mCrpFlagsDdl[ $Flag['ddl_fie'] ]['alias'] = $Flag['alias'];
//             }
//           }
//         }
//       }
//     }
//     foreach ($this -> mAllFlags as $lTyp => $lFlag) {
//       if (!empty($lFlag['alias'])) {
//         $this -> mCrpAllFlagsAli[$lFlag['alias']] = $lTyp;
//       }
//       if (!empty($lFlag['ddl_fie'])) {
//         $this -> mCrpAllFlagsDdl[$lFlag['ddl_fie']] = $lTyp;
//       }
//     }
//   }



//   protected function getTdApl() {
//     $lSrc = $this -> getVal('src');
//     $lJobID = $this -> getVal('jobid');
//     $lWebStatus= $this -> getVal('webstatus');

//     if (empty($lSrc)) {
//       return $this -> tda();
//     }

//     $lAplstatus = array();
//     if (isset($this -> lAplstatus[$lSrc])) {
//       $lAplstatus = $this -> lAplstatus[$lSrc];
//     }
//     if (!in_array($lWebStatus, $lAplstatus)) {
//       return $this -> tda();
//     }

//     $lLoopId = $this -> getInt('loop_id');
//     if (empty($lLoopId)) {
//       return $this -> tda();
//     }

//     $lLnk = 'index.php?act=job-apl&src='.$lSrc.'&jobid='.$this -> getVal('jobid');
//     $lRet = CApp_Apl_Loop::getAplCommitList($lLoopId, $lLnk);

//     return $this -> tdClass($lRet, 'w16 ac');
//   }

//   protected function getTdPdf_available() {
//     $lSrc = $this -> getVal('src');
//     $lJID = $this -> getVal('jobid');

//     $lLnk = CCor_Cfg::get('job.writer.default', 'alink');
//     if ('alink' == $lLnk) {
//       $lCount = $this -> getCurInt();
//     } elseif ('mop' == $lLnk) {
//       $lSQL = 'SELECT count(id) FROM al_job_files WHERE mand='.esc(MID).' AND src='.esc($lSrc).' AND jobid='.esc($lJID).' AND sub='.esc('pdf').';';
//       $lCount = CCor_Qry::getInt($lSQL);
//     }

//     $lURL = 'index.php?act=arc-'.$lSrc.'-fil&jobid='.$lJID;

//     $lRet = '<a href="'.htm($lURL).'">';
//     if ($lCount < 1) {
//       $lRet.= NB;
//     } else {
//       $lRet.= img('img/ico/16/pdf.png');
//     }
//     $lRet.= '</a>';

//     return $this -> tdClass($lRet, 'w16 ac', TRUE);
//   }
}