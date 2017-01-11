<?php
/**
 * Approval Loop Page
 *
 * Controller for a special page with limited possibilities.
 * Users will be invited here via email when an approval loop is started
 * They can launch the Webcenter viewer to annotate files. In the portal, they
 * will be asked to give their approval
 *
 * @package    Job
 * @subpackage Approval Loop
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Job_Apl_Page_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct = 'std') {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mReq -> expect('jid');
    $this -> mReq -> expect('src');
    $this -> mJobId = $this -> getReq('jid');
    $this -> mSrc = $this -> getReq('src');
    $this -> mTitle = lan('job-apl.menu');
    $this -> mHideMenu = false;
  }

  protected function abmeldung() {
    $lRet = '';
    $lRet.= '<table cellpadding="9" cellspacing="0" border="0" id="pgMenu" class="pgMenu" width="100%"><tr>'.LF;
    $lRet.= '<td class="mmLo" style="border-right:0" onclick="go(\'index.php?act=job-apl-page.logout\')">';
    $lRet.= lan('job-apl.logout').'</td>'.LF;
    $lRet.= '</tr></table>';
    return $lRet;
  }

  protected function getStdUrl() {
    return 'index.php?act=job-apl-page&src='.$this -> mSrc.'&jid='.$this -> mJobId;
  }

  protected function actLogout() {
    $lQry = new CCor_Qry('DELETE FROM al_usr_login WHERE session_id="'.session_id().'"');

    unset($_SESSION);
    session_unset();
    session_destroy();

    $lPag = CHtm_Page::getInstance();
    $lPag -> openProjectFile('nirvana.htm');
    $lRet = '';
    $this -> render($lRet);
  }

  public function actStd() {
    $lPag = CHtm_Page::getInstance();
    $lPag -> openProjectFile('page_apl.htm');

    $lFac = new CJob_Fac($this -> mSrc, $this -> mJobId);
    $lJob = $lFac -> getDat();

    $lHdr = $lFac -> getHeader();
    $lMen = new CJob_Apl_Page_Menu($this -> mSrc, $this -> mJobId);

    $lHdr -> addPanel($lMen -> getContent());
    $lHdr -> addPanel('&nbsp;');
    $lHdr -> addPanel($this-> abmeldung());

    $lHdr -> hideMenu();
    $lRet = $lHdr -> getContent().BR;

    $lLis = new CJob_Apl_Page_List($this -> mSrc, $this -> mJobId, $this -> getReq('prtid'));

    // Anzeige der Aktionsbuttons nur im Status 'Korrekturumlauf' mit apl=1
    $lSql = 'SELECT apl FROM `al_crp_status` s, `al_crp_master` m where m.mand='.MID.' AND s.mand=m.mand';
    $lSql.= ' AND m.code='.esc($this -> mSrc).' ANd m.id=s.crp_id AND s.status='.$lJob['webstatus'].' LIMIT 0,1';
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      $lApl = $lRow['apl'];
    }
    // echo '<pre>---cnt.php---';var_dump($lQry,$lSql,$lApl,$lRow['id'],'#############');echo '</pre>';
    if(1 == $lApl AND $lLis -> ShowAplButtons()) {
      $lImg = new CJob_Apl_Page_Images($lJob); // linke Seite: Dateien
      $lBtn = new CJob_Apl_Page_Buttons($lJob);// rechte Seite: Aktionen
    } else {
      $lImg = new CJob_Apl_Page_Images(0);
      $lBtn = new CJob_Apl_Page_Buttons(0); // Keine Anzeige der Aktionsbuttons
    }
    $lRet.= CJob_Apl_Page_Wrap::wrap($lImg, $lLis, $lBtn);
    $lRet.= $this -> getWecLogout();
    $this -> render($lRet);
  }

  protected function actApl() {
    $this -> mHideMenu = true;
    $lFac = new CJob_Fac($this -> mSrc, $this -> mJobId);
    $lFla = $this -> getInt('flag');
    $lJob = $lFac -> getDat();

    // Setzen der Flags nur im Status 'Korrekturumlauf' mit apl=1
    $lSql = 'SELECT apl FROM `al_crp_status` s, `al_crp_master` m where m.mand='.MID.' AND s.mand=m.mand';
    $lSql.= ' AND m.code='.esc($this -> mSrc).' ANd m.id=s.crp_id AND s.status='.$lJob['webstatus'].' LIMIT 0,1';
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      $lApl = $lRow['apl'];
    }
    if(1 == $lApl) {
      $lHdr = $lFac -> getHeader();
      $lMen = new CJob_Apl_Page_Menu($this -> mSrc, $this -> mJobId);

      $lHdr -> addPanel($lMen -> getContent());
      $lHdr -> hideMenu();
      $lRet = $lHdr -> getContent().BR;

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
      }

      # $lDlg = new CHtm_Form('job-apl-page.sapl', $lCap, 'job-apl-page&src='.$this -> mSrc.'&jid='.$this -> mJobId);
      # $lDlg -> setAtt('style', 'width:700px');
      # $lDlg -> setParam('src', $this -> mSrc);
      # $lDlg -> setParam('jid', $this -> mJobId);
      # $lDlg -> setParam('flag', $lFla);
      # $lDlg -> setParam('apl', $lJob['apl']);
      # $lDlg -> addDef(fie('msg', lan('lib.msg'), 'memo', NULL, array('style' => 'width:500px;', 'rows' => '18')));

      $lPage = TRUE;
      $lDlg = new CJob_Apl_Loop_Form($this -> mSrc, $this -> mJobId, $lCap, $lFla, $lJob['apl'], $lJob[CCor_Cfg::get('wec.annotation.master', 'per_prj_verantwortlich')], $lPage);
      $lApl = new CApp_Apl_Loop($this -> mSrc, $this -> mJobId, 'apl');
      $lMsg = $lApl -> getCurrentUserComment(CCor_Usr::getAuthId());
      $lDlg -> setVal('msg', trim($lMsg));

      $lDlg -> setUsers($lApl -> getAplUserlist());
      $lRet.= $lDlg -> getContent();

      $this -> render($lRet);
    } else {
      $this -> redirect('index.php?act=job-apl-page&src='.$this -> mSrc.'&jid='.$this -> mJobId);
    }
  }

  protected function actSapl() {
    $lJid = $this -> getReq('jobid');
    // Setzen der Flags nur im Status 'Korrekturumlauf' mit apl=1
    $lFac = new CJob_Fac($this -> mSrc, $this -> mJobId);
    $lJob = $lFac -> getDat();
    $lSql = 'SELECT apl FROM `al_crp_status` s, `al_crp_master` m where m.mand='.MID.' AND s.mand=m.mand';
    $lSql.= ' AND m.code='.esc($this -> mSrc).' ANd m.id=s.crp_id AND s.status='.$lJob['webstatus'].' LIMIT 0,1';
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      $lApl = $lRow['apl'];
    }
    if(1 == $lApl) {

      $lFla = $this -> getInt('flag');
      $lVal = $this -> getReq('val');

      $lSta = $this -> getReq('apl');
      $lMsg = (isset($lVal['msg'])) ? $lVal['msg'] : '';
      $lFiles = $this -> getReq('listuserfiles');

      $lApl = new CApp_Apl_Loop($this -> mSrc, $this -> mJobId, 'apl');
      $lApl -> setToFiles(CCor_Usr::getAuthId(), $lFiles);
      if (0 == $lFla) {
        // Status unveraendert lassen, aber Kommentar speichern
        $lApl -> setState(CCor_Usr::getAuthId(), NULL, $lMsg);
      } else {
        // Status aendern und Kommentar speichern
        $lApl -> setState(CCor_Usr::getAuthId(), $lFla, $lMsg);
      }

      $lNew = $lApl -> getOverallState();
      if ($lNew != $lSta) {
        $lUpd['apl'] = $lNew;
        $lClass = 'CJob_'.$this->mSrc.'_Mod';
        $lMod = new $lClass($this -> mJobId);
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

      //--START: TTS-478 XFDF Dateien (R�diger)
      $lWec = CCor_Cfg::get('wec.available', true);
      $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
      if ($lWec) {
	      // xfdf Dateinen lesen
	      $lAnn = new CJob_Apl_Page_Annotations($lJob);
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
    	            $lQry -> addParam('jobid', $this -> mJobId);
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
    	              $lQry -> addParam('jobid', $this -> mJobId);
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

      //--STOPP: TTS-478

      $lHis = new CApp_His($this -> mSrc, $this -> mJobId);
      $lNeedSignature = CCor_Cfg::getFallback('job.apl.'.$this->mSrc.'.signature', 'job.apl.signature', false);
      if ($lNeedSignature) {
        $lHis ->setVal('signature_id', CCor_Usr::getAuthId());
      }
      $lHis -> add($lTyp, $lCap, $lMsg);
    }
    
    $lBeat = new CJob_Workflow_Heartbeat($this -> mSrc, $this -> mJobId);
    $lBeat->heartBeat();
    
    $this -> redirect();
  }

  protected function arrayDepth($aArray) {
    if (is_array(reset($aArray))) {
      $lRet = $this -> arrayDepth(reset($aArray)) + 1;
    } else {
      $lRet = 1;
    }

    return $lRet;
  }

  protected function getPage() {
    $lPag = CHtm_Page::getInstance();
    // $lPag -> openProjectFile('page_apl.htm');
    if ($this -> mHideMenu) $lPag -> hideMenu();
    return $lPag;
  }

  protected function actSetstate() {
    $this -> mHideMenu = true;
    $lFac = new CJob_Fac($this -> mSrc, $this -> mJobId);
    $lJob = $lFac -> getDat();
    $lApl = 0;

    // Setzen der Flags nur im Status 'Korrekturumlauf' mit apl=1
    $lSql = 'SELECT apl FROM `al_crp_status` s, `al_crp_master` m where m.mand='.MID.' AND s.mand=m.mand';
    $lSql.= ' AND m.code='.esc($this -> mSrc).' ANd m.id=s.crp_id AND s.status='.$lJob['webstatus'].' LIMIT 0,1';
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry -> getDat()) {
      $lApl = $lRow['apl'];
    }


    if (1 == $lApl) {

      $lHdr = $lFac -> getHeader();

      $lHdr -> addPanel($this->abmeldung());
      $lHdr -> hideMenu();

      $lRet = $lHdr -> getContent().BR;
      #$lRet .= '<table cellpadding="2" cellspacing="0" class="w100p"><tr>';

      $lDlg = new CJob_Apl_Page_Form($this -> mSrc, $this -> mJobId);

      $lDlg -> setParam('apl', $lJob['apl']);
      $lApl = new CApp_Apl_Loop($this -> mSrc, $this -> mJobId, 'apl');
      $lMsg = $lApl -> getCurrentUserComment(CCor_Usr::getAuthId());
      $lDlg -> setVal('msg', trim($lMsg));

      $lAnn = new CJob_Apl_Page_Annotations($lJob);

      $lRet.= $lDlg -> getUpload();
      $lRet.= $lAnn -> getContent().LF;

      //$lRet.= $lDlg -> getUpload();

      $lRet.= $lDlg -> getContent();

      $lRet.= $this -> getWecLogout();

      $this -> render($lRet);
    } else {
      $this -> redirect('index.php?act=job-apl-page&src='.$this -> mSrc.'&jid='.$this -> mJobId);
    }
  }

  protected function getMenu() {
    return '';
  }

  public function actPrnProtocol() {
    $lFac = new CJob_Fac($this -> mSrc, $this -> mJobId);
    $lJob = $lFac -> getDat();

    $this -> mHideMenu = true;

    $lid = $this -> getReq('prtid');
    $lSrc = $this -> getVal('src');

    $lSql = 'SELECT * FROM al_job_apl_loop WHERE 1 ';
    $lSql.= 'AND id='.$lid;
    $lQry = new CCor_Qry($lSql);
    $lLis = '';
    $lRet = '';
    foreach ($lQry as $lRow) {
      $lLis = new CJob_Apl_Sub($lRow);
      $lRet = $lLis -> getSingleProtocol($lJob);
    }

    $lPag = new CUtl_Page();
    $lPag -> setPat('pg.cont', $lRet);
    $lPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));
    $lPag -> setPat('pg.title', htm(lan('job-'.$lSrc.'.menu')));
    $lPag -> setPat('pg.js', '<script type="text/javascript">window.print()</script>');

    echo $lPag -> getContent();
  }

  protected function actAddapluser() {
    $lJid = $this->getVal('jid');
    $lSrc = $this->getVal('src');
    $lUsr = $this->getVal('usr');
    $lMethod = $this->getVal('method');
    $lPrefix = $this->getVal('prefix');
    $lPrefix = $lPrefix == 'null' ? $lPrefix = '' : $lPrefix;
    if (empty($lUsr)) {
      echo 'ok';
      exit;
    }
    $lMe = CCor_Usr::getAuthId();
    $lLoop = new CApp_Apl_Loop($lSrc, $lJid);
    if ($lMethod == 'expand') {
      $lLoop->insertUsers($lUsr, $lMe, $lPrefix, $lMethod);
      $lLoop->insertUsers(array($lMe), '', $lPrefix, $lMethod);
    }
    if ($lMethod == 'forward') {
      $lLoop->insertUsers($lUsr,$lMe, $lPrefix, $lMethod);
      $lLoop->setState($lMe, CApp_Apl_Loop::APL_STATE_FORWARD);
    }
    if ($lMethod == 'add') {
      $lLoop->insertUsers($lUsr, $lMe, $lPrefix, $lMethod);
    }

    echo "ok";
  }
}