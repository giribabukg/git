<?php
/**
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 8398 $
 * @date $Date: 2015-04-14 00:04:13 +0800 (Tue, 14 Apr 2015) $
 * @author $Author: ahajali $
 */
class CInc_Job_Pro_Sub_Item_List extends CHtm_List {

  public function __construct($aJobId) {
    parent::__construct('job-pro-sub');

    $this -> mJobId = intval($aJobId);

    $this -> mStdLnk = 'index.php?act='.$this -> mMod.'.edt&amp;jobid='.$this -> mJobId.'&amp;id=';
    $this -> mOrdLnk = 'index.php?act='.$this -> mMod.'.ord&amp;jobid='.$this -> mJobId.'&amp;fie=';
    $this -> mDelLnk = 'index.php?act='.$this -> mMod.'.del&amp;jobid='.$this -> mJobId.'&amp;id=';

    $this -> setAtt('width', '100%');

    $this -> mCapCls = 'th1';
    $this -> mTitle = lan('job-sub.menu');
    $this -> mImg = 'img/ico/40/'.LAN.'/job-pro.gif';

    $this -> mFie = CCor_Res::getByKey('id', 'fie');
    $lUsr = CCor_Usr::getInstance();

    $this -> mCanInsArt = $lUsr -> canInsert('job-art');
    $this -> mCanInsRep = $lUsr -> canInsert('job-rep');
    $this -> mCanInsSec = $lUsr -> canInsert('job-sec');
    $this -> mCanInsMis = $lUsr -> canInsert('job-mis');
    $this -> mCanInsAdm = $lUsr -> canInsert('job-adm');
    $this -> mCanInsCom = $lUsr -> canInsert('job-com');
    $this -> mCanInsTra = $lUsr -> canInsert('job-tra');

    $this -> addCtr();

    if ($this -> mCanInsArt) {
      $this -> addColumn('sel', '', FALSE, array('width' => '16'));
      $this -> addColumn('is_master', '', FALSE, array('width' => '16'));
    }
    $this -> addColumns();

    $lMnuItems = Ccor_Cfg::get('menu-projektitems');

    // job Kategories werden aus config geladen.
    foreach ($lMnuItems as $lRow) {
      $lTemp = str_replace("_", "-", $lRow); // es gibt schon die Übersetzung von z.B "job-rep".
                                           // Bei $lMnuItems konnte nicht mit "-" benannt werden, weil bei später kommender funktion "-" nicht erlaubt ist.
      $this -> addColumn($lRow, lan($lTemp.'.menu'), FALSE, array('width' => '70'));
    }

    if ($this -> mCanInsert) {
      $this -> addColumn('cpy', '', FALSE, array('width' => '16'));
    }

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> getPrefs();

    $this -> mAli = CCor_Res::getByKey('alias', 'fie');

    $this -> mPlain = new CHtm_Fie_Plain();

    $this -> getIterator();
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> setGroup($this -> mOrd);
    $this -> mGrpDef = (isset($this -> mAli[$this -> mOrd])) ? $this -> mAli[$this -> mOrd] : NULL;

    $this -> mMaxLines = $this -> mIte -> getCount();
    $this -> preLoad();

    if ($this -> mCanInsArt) {
      $this -> addBtn(lan('job-pro-sub.createbundle'), 'createArt("'.$this -> mJobId.'","'.LAN.'")', 'img/ico/16/plus.gif');
    }
    $this -> addBtn(lan('lib.opt.fpr'), 'go(\'index.php?act='.$this -> mMod.'.fpr&jobid='.$this -> mJobId.'\')', 'img/ico/16/col.gif');
    if ($this -> mCanInsert) {
      $this -> addBtn(lan('job-pro-sub.new'), 'go(\'index.php?act='.$this -> mMod.'.new&jobid='.$this -> mJobId.'\')', 'img/ico/16/edit.gif');
    }

    $this -> getWizards();
    if ($this -> mCanInsert) {
      $this -> addPanel('new', $this -> getSubMenu());
      $this -> addPanel(NULL, '|');
      $this -> addPanel('wiz', $this -> getWizMenu());
    }

    $this -> addJs();
  }

  protected function addJs() {
    $lJs = 'function subAll(aEl){'.LF;
    $lJs.= 'var lFla=$(aEl).checked;'.LF;
    $lJs.= 'var lTr=$(aEl).up("tr");'.LF;
    $lJs.= 'while(lTr=lTr.next()){'.LF;
    $lJs.= 'if (lTr.firstDescendant().hasClassName("tg1")) break;'.LF;
    $lJs.= 'if (lTr.down("input")) lTr.down("input").checked=lFla;'.LF;
    $lJs.= '}}';
    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lJs);
  }

  protected function getWizards() {
    $this -> mWiz = array();
    $lSql = 'SELECT * FROM al_wiz_master ORDER BY name_'.LAN;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mWiz[$lRow['id']] = $lRow['name_'.LAN];
    }
  }

  protected function getSubMenu() {
    $lMen = new CHtm_Menu(lan('lib.new_item'));
    $lMen -> addTh2(lan('lib.tpl'));
    foreach ($this -> mWiz as $lKey => $lVal) {
      $lMen -> addItem('index.php?act=job-pro-sub.new&amp;jobid='.$this -> mJobId.'&amp;wiz='.$lKey, $lVal);
    }
    return $lMen -> getContent();
  }

  protected function getWizMenu() {
    $lMen = new CHtm_Menu(lan('wiz.menu'));
    $lMen -> addTh2(lan('lib.tpl'));
    foreach ($this -> mWiz as $lKey => $lVal) {
      $lMen -> addItem('index.php?act=job-pro-sub.wiz&amp;jobid='.$this -> mJobId.'&amp;wiz='.$lKey, $lVal);
    }
    return $lMen -> getContent();
  }

  protected function getIterator() {
    $this -> mIte = new CCor_TblIte('al_job_sub_'.intval(MID));
    $this -> mIte -> addCnd('pro_id='.$this -> mJobId);
  }

  protected function preLoad() {
    $lArr = array();
    $lArrNoAlink = array();
    $lArrLeer = array();
    $lPrjLeiter = array();

    $this -> mIte = $this -> mIte -> getArray('id');

    $lZeile = 1;
    foreach ($this -> mIte as $lKey => $lRow) {
      if (!empty($lRow['jobid_art'])) {
        $lArr[$lRow['jobid_art']] = TRUE;
      }else{
        $lArrLeer['art'][$lZeile] = $lRow['id'];
      }
      if (!empty($lRow['jobid_rep'])) {
        $lArr[$lRow['jobid_rep']] = TRUE;
      }else{
        $lArrLeer['rep'][$lZeile] = $lRow['id'];
      }
      if (!empty($lRow['jobid_sec'])) {
        $lArr[$lRow['jobid_sec']] = TRUE;
      }else{
        $lArrLeer['sec'][$lZeile] = $lRow['id'];
      }
      if (!empty($lRow['jobid_adm'])) {
        $lArr[$lRow['jobid_adm']] = TRUE;
      }else{
        $lArrLeer['adm'][$lZeile] = $lRow['id'];
      }
      if (!empty($lRow['jobid_mis'])) {
        $lArr[$lRow['jobid_mis']] = TRUE;
      }else{
        $lArrLeer['mis'][$lZeile] = $lRow['id'];
      }

      $lJobs_PDB = CCor_Cfg::get('all-jobs_PDB');
      foreach ($lJobs_PDB as $lJob) {
        if (!empty($lRow['jobid_'.$lJob])) {
          $lArrNoAlink[] = $lRow['jobid_'.$lJob];
        }else{
          $lArrLeer[$lJob][$lZeile] = $lRow['id'];
        }
      }

      if (!empty($lRow['per_prj_verantwortlich'])) {
        $lPrjLeiter[$lZeile] = $lRow['per_prj_verantwortlich'];
      }
      $this -> mIte[$lKey]['zeile'] = $lZeile++;
    }
    foreach ($lArrLeer as $lKey => $lRow) {
      $lArrLeerAmount[$lKey] = count($lRow);
    }
    $this -> mArrLeer = $lArrLeer;
    $this -> mArrLeerAmount = $lArrLeerAmount;

    // Winter: "Der Projektverantwortliche, sowie der Admin, darf alle Jobs verschieben."
    $lUsr = CCor_Usr::getInstance();
    $lUid = $lUsr -> getId();
    $lCanSlide = false;
    foreach ($lPrjLeiter as $lPrjNr) {// user =?= PrjLeiter ?
      if ($lPrjNr == $lUid) {
        $lCanSlide = true;
        break; // Durchlauf beenden, sobald die Frage beantwortet ist.
      }
    }
    if (!$lCanSlide) {// user!=PrjLeiter => user =?= Admin ?
      $lSql = 'SELECT m.uid FROM al_usr_mem m, al_gru g WHERE g.code="adm" AND g.id=m.gid AND m.uid='.$lUid.' LIMIT 0,1';
      $lAdmQry = new CCor_Qry($lSql);
      $lAdmQryResult = $lAdmQry->getAssocs('');
      if (!empty($lAdmQryResult)) {
        $lCanSlide = true;
      }
    }
    $this -> mCanSlide = $lCanSlide;

    $this -> mSub = array();
    $this -> mJfl = array();

    if (empty($lArr) AND empty($lArrNoAlink)) return;

    $lJid = '';
    if (!empty($lArr)) {
      foreach ($lArr as $lKey => $lDum) {
        $lJid.= '"'.$lKey.'",';
      }
      $lJid = strip($lJid);
      $this -> dump($lJid);

      $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
      if ('portal' == $lWriter) {
        $lIte = new CCor_TblIte('all', $this->mWithoutLimit);
        $lIte -> addField('jobid');
        $lIte -> addField('webstatus');
        $lIte -> addCnd('jobid IN ('.$lJid.')');
      } else {
        $lIte = new CApi_Alink_Query_Getjoblist();
        $lIte -> addField('webstatus', 'webstatus');
        $lIte -> addCondition('jobid', 'in', $lJid);
      }

      foreach ($lIte as $lRow) {
        $this -> mSub[$lRow['jobid']] = $lRow;
      }
    }

    $lIds = '';
    if (!empty($lArrNoAlink)) {
      $lArrNoAl = array_map("esc", $lArrNoAlink);//jedes Element wird ".mysql_escaped."
      $lIds = implode(',', $lArrNoAl);
      $lSql = 'SELECT jobid,webstatus FROM al_job_pdb_'.intval(MID).' WHERE jobid IN ('.$lIds.')';
      $lQry = new CCor_Qry($lSql);

      foreach ($lQry as $lRow) {
        $this -> mSub[$lRow['jobid']] = $lRow;
      }
    }

    $lJid.= $lIds;
    $lSql = 'SELECT jobid,flags FROM al_job_shadow_'.intval(MID).' WHERE jobid IN ('.$lJid.')';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mJfl[$lRow['jobid']] = $lRow['flags'];
    }
  }

  protected function addColumns() {
    $lUsr = CCor_Usr::getInstance();
    $lCol = $lUsr -> getPref('job-pro-sub.cols');
    if (empty($lCol)) return;

    $lArr = explode(',',$lCol);
    foreach ($lArr as $lFid) {
      if (isset($this -> mFie[$lFid])) {
        $lDef = $this -> mFie[$lFid];
        $this -> addField($lDef);
      }
    }
  }

  protected function getGroupHeader() {
    $lRet = '';
    if (!empty($this -> mGrp)) {
      $lNew = $this -> getVal($this -> mGrp);
      if ($lNew !== $this -> mOldGrp) {
        $lRet = TR;
        $lRet.= '<td class="tg1 ac">';
        $lId = getNum('c');
        $lRet.= '<input type="checkbox" class="grp" id="'.$lId.'" onclick="subAll(\''.$lId.'\')" />';
        $lRet.= '</td>';
        $lRet.= '<td class="tg1" colspan="'.($this -> mColCnt -1).'">';
        $lVal = $lNew;
        if ($this -> mGrpDef) {
          $lVal = $this -> mPlain -> getPlain($this -> mGrpDef, $lVal);
        }
        $lRet.= htm($lVal).NB;
        $lRet.= '</td>';
        $lRet.= _TR;
        $this -> mOldGrp = $lNew;
        $this -> mCls = 'td1';
      }
    }
    return $lRet;
  }

  protected function beforeRow() {
    $lMas = $this -> getVal('is_master');
    if ('Y' == $lMas) {
      $this -> mCls.= ' cy';
    }
    return $this -> getGroupHeader();
  }


  protected function getTdSel() {
    $lSid = $this -> getInt('id');
    $lArt = $this -> getVal('jobid_art');
    if (empty($lArt)) {
      $lRet = '<input type="checkbox" class="art" id="c'.$lSid.'" ';
      $lRet.= ' />';
    } else {
      $lRet = NB;
    }
    return $this -> tdc($lRet);
  }

  protected function getTdIs_master() {
    $lMas = $this -> getCurVal();
    $lSid = $this -> getInt('id');
    if ('Y' == $lMas) {
      $lRet = '<a href="index.php?act=job-pro-sub.master_unset&amp;jobid='.$this -> mJobId.'&amp;sid='.$lSid.'" class="nav">';
      $lRet.= img('img/ico/16/master.gif');
      $lRet.= '</a>';
    } else {
      $lRet = '<a href="index.php?act=job-pro-sub.master_set&amp;jobid='.$this -> mJobId.'&amp;sid='.$lSid.'" class="nav">';
      $lRet.= img('img/ico/16/master-lo.gif');
      $lRet.= '</a>';
    }
    return $this -> tdc($lRet);
  }

  protected function getTdMenu($aSrc, $aJobID, $aFrom, $aCaption) {
    $lCaptWithHtm = false;
    $lMen = new CHtm_Menu($aCaption, '', $lCaptWithHtm);
    $lMen -> addItem('index.php?act=job-'.$aSrc.'.edt&amp;jobid='.$aJobID, lan('lib.open'));
    if( $this -> mArrLeerAmount[$aSrc] > 0 AND $this -> mCanSlide ){
      $lMen -> addTh2(lan('lib.move_to'));
      foreach ($this -> mArrLeer[$aSrc] as $lKey => $lVal) {
        $lMen -> addItem('index.php?act=job-pro-sub.slide&amp;jobid='.$aJobID.'&amp;from='.$aFrom.'&amp;to='.$lVal, 'Zeile '.$lKey);
      }
    }
    return $lMen -> getContent();
  }

  protected function getTdJob_Art() {
    $lId = $this -> getVal('jobid_art');
    $lSid = $this -> getInt('id');
    if (!empty($lId)) {
      $lRow = (isset($this -> mSub[$lId])) ?  $this -> mSub[$lId] : new CCor_Dat();
      $lSta = $lRow['webstatus'] / 10;

	  $lImg = CApp_Crpimage::getSrcPath('art', 'img/crp/'.$lSta.'b.gif');
      if (isset($this -> mJfl[$lId])) {
        $lJfl = $this -> mJfl[$lId];
        if (bitset($lJfl, jfOnhold)) {
          $lImg = 'img/jfl/'.jfOnhold.'.gif';
        }
        if (bitset($lJfl, jfCancelled)) {
          $lImg = 'img/jfl/'.jfCancelled.'.gif';
        }
      }

      $aCaption = img($lImg).NB.jid($lId, TRUE);
      $lFrom = $lSid;
      $lRet = $this -> getTdMenu('art', $lId, $lFrom, $aCaption);
    } else {
      if ($this -> mCanInsArt) {
        $lRet = '<a href="index.php?act=job-art.sub&amp;pid='.$this -> mJobId.'&amp;sid='.$lSid.'" class="nav">';
        $lRet.= htm(lan('lib.create'));
        $lRet.= '</a>';
      } else {
        $lRet.= NB;
      }
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Rep() {
    $lId = $this -> getVal('jobid_rep');
    $lSid = $this -> getInt('id');
    if (!empty($lId)) {
      $lRow = (isset($this -> mSub[$lId])) ?  $this -> mSub[$lId] : new CCor_Dat();
      $lSta = $lRow['webstatus'] / 10;
	  $lImg = CApp_Crpimage::getSrcPath('rep', 'img/crp/'.$lSta.'b.gif');
      if (isset($this -> mJfl[$lId])) {
        $lJfl = $this -> mJfl[$lId];
        if (bitset($lJfl, jfOnhold)) {
          $lImg = 'img/jfl/'.jfOnhold.'.gif';
        }
        if (bitset($lJfl, jfCancelled)) {
          $lImg = 'img/jfl/'.jfCancelled.'.gif';
        }
      }

      $aCaption = img($lImg).NB.jid($lId, TRUE);
      $lFrom = $lSid;
      $lRet = $this -> getTdMenu('rep', $lId, $lFrom, $aCaption);
    } else {
      if ($this -> mCanInsRep) {
        $lRet = '<a href="index.php?act=job-rep.sub&amp;pid='.$this -> mJobId.'&amp;sid='.$lSid.'" class="nav">';
        $lRet.= htm(lan('lib.create'));
        $lRet.= '</a>';
      } else {
        $lRet.= NB;
      }
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Sec() {
    $lId = $this -> getVal('jobid_sec');
    $lSid = $this -> getInt('id');
    if (!empty($lId)) {
      $lRow = (isset($this -> mSub[$lId])) ?  $this -> mSub[$lId] : new CCor_Dat();
      $lSta = $lRow['webstatus'] / 10;
	  $lImg = CApp_Crpimage::getSrcPath('sec', 'img/crp/'.$lSta.'b.gif');
      if (isset($this -> mJfl[$lId])) {
        $lJfl = $this -> mJfl[$lId];
        if (bitset($lJfl, jfOnhold)) {
          $lImg = 'img/jfl/'.jfOnhold.'.gif';
        }
        if (bitset($lJfl, jfCancelled)) {
          $lImg = 'img/jfl/'.jfCancelled.'.gif';
        }
      }

      $aCaption = img($lImg).NB.jid($lId, TRUE);
      $lFrom = $lSid;
      $lRet = $this -> getTdMenu('sec', $lId, $lFrom, $aCaption);
    } else {
      if ($this -> mCanInsSec) {
        $lRet = '<a href="index.php?act=job-sec.sub&amp;pid='.$this -> mJobId.'&amp;sid='.$lSid.'" class="nav">';
        $lRet.= htm(lan('lib.create'));
        $lRet.= '</a>';
      } else {
        $lRet.= NB;
      }
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Adm() {
    $lId = $this -> getVal('jobid_adm');
    $lSid = $this -> getInt('id');
    if (isset($this -> mSub[$lId])) {
      $lRow = $this -> mSub[$lId];
      $lSta = $lRow['webstatus'] / 10;
	  $lImg = CApp_Crpimage::getSrcPath('adm', 'img/crp/'.$lSta.'b.gif');
      if (isset($this -> mJfl[$lId])) {
        $lJfl = $this -> mJfl[$lId];
        if (bitset($lJfl, jfOnhold)) {
          $lImg = 'img/jfl/'.jfOnhold.'.gif';
        }
        if (bitset($lJfl, jfCancelled)) {
          $lImg = 'img/jfl/'.jfCancelled.'.gif';
        }
      }

      $aCaption = img($lImg).NB.jid($lId, TRUE);
      $lFrom = $lSid;
      $lRet = $this -> getTdMenu('adm', $lId, $lFrom, $aCaption);
    } else {
      if ($this -> mCanInsAdm) {
        $lRet = '<a href="index.php?act=job-adm.sub&amp;pid='.$this -> mJobId.'&amp;sid='.$lSid.'" class="nav">';
        $lRet.= htm(lan('lib.create'));
        $lRet.= '</a>';
      } else {
        $lRet.= NB;
      }
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Com() {
    $lId = $this -> getVal('jobid_com');
    $lSid = $this -> getInt('id');
    if (!empty($lId)) {
      $lRow = (isset($this -> mSub[$lId])) ?  $this -> mSub[$lId] : new CCor_Dat();
      $lSta = $lRow['webstatus'] / 10;
	  $lImg = CApp_Crpimage::getSrcPath('com', 'img/crp/'.$lSta.'b.gif');
      if (isset($this -> mJfl[$lId])) {
        $lJfl = $this -> mJfl[$lId];
        if (bitset($lJfl, jfOnhold)) {
          $lImg = 'img/jfl/'.jfOnhold.'.gif';
        }
        if (bitset($lJfl, jfCancelled)) {
          $lImg = 'img/jfl/'.jfCancelled.'.gif';
        }
      }

      $aCaption = img($lImg).NB.jid($lId, TRUE);
      $lFrom = $lSid;
      $lRet = $this -> getTdMenu('com', $lId, $lFrom, $aCaption);
    } else {
      if ($this -> mCanInsSec) {
        $lRet = '<a href="index.php?act=job-com.sub&amp;pid='.$this -> mJobId.'&amp;sid='.$lSid.'" class="nav">';
        $lRet.= htm(lan('lib.create'));
        $lRet.= '</a>';
      } else {
        $lRet.= NB;
      }
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Tra() {
    $lId = $this -> getVal('jobid_tra');
    $lSid = $this -> getInt('id');
    if (isset($this -> mSub[$lId])) {
      $lRow = $this -> mSub[$lId];
      $lSta = $lRow['webstatus'] / 10;
	  $lImg = CApp_Crpimage::getSrcPath('tra', 'img/crp/'.$lSta.'b.gif');
      if (isset($this -> mJfl[$lId])) {
        $lJfl = $this -> mJfl[$lId];
        if (bitset($lJfl, jfOnhold)) {
          $lImg = 'img/jfl/'.jfOnhold.'.gif';
        }
        if (bitset($lJfl, jfCancelled)) {
          $lImg = 'img/jfl/'.jfCancelled.'.gif';
        }
      }

      $aCaption = img($lImg).NB.jid($lId, TRUE);
      $lFrom = $lSid;
      $lRet = $this -> getTdMenu('tra', $lId, $lFrom, $aCaption);
    } else {
      if ($this -> mCanInsAdm) {
        $lRet = '<a href="index.php?act=job-tra.sub&amp;pid='.$this -> mJobId.'&amp;sid='.$lSid.'" class="nav">';
        $lRet.= htm(lan('lib.create'));
        $lRet.= '</a>';
      } else {
        $lRet.= NB;
      }
    }
    return $this -> td($lRet);
  }

  protected function getTdJob_Mis() {
    $lId = $this -> getVal('jobid_mis');
    $lSid = $this -> getInt('id');
    if (isset($this -> mSub[$lId])) {
      $lRow = $this -> mSub[$lId];
      $lSta = $lRow['webstatus'] / 10;
	  $lImg = CApp_Crpimage::getSrcPath('mis', 'img/crp/'.$lSta.'b.gif');
      if (isset($this -> mJfl[$lId])) {
        $lJfl = $this -> mJfl[$lId];
        if (bitset($lJfl, jfOnhold)) {
          $lImg = 'img/jfl/'.jfOnhold.'.gif';
        }
        if (bitset($lJfl, jfCancelled)) {
          $lImg = 'img/jfl/'.jfCancelled.'.gif';
        }
      }

      $aCaption = img($lImg).NB.jid($lId, TRUE);
      $lFrom = $lSid;
      $lRet = $this -> getTdMenu('mis', $lId, $lFrom, $aCaption);
    } else {
      if ($this -> mCanInsMis) {
        $lRet = '<a href="index.php?act=job-mis.sub&amp;pid='.$this -> mJobId.'&amp;sid='.$lSid.'" class="nav">';
        $lRet.= htm(lan('lib.create'));
        $lRet.= '</a>';
      } else {
        $lRet.= NB;
      }
    }
    return $this -> td($lRet);
  }

  protected function getTdCpy() {
    $lSid = $this -> getInt('id');
    $lRet = '<a href="index.php?act=job-pro-sub.cpy&amp;jobid='.$this -> mJobId.'&amp;id='.$lSid.'" class="nav">';
    $lRet.= img('img/ico/16/copy.gif');
    $lRet.= '</a>';
    return $this -> td($lRet);
  }
}