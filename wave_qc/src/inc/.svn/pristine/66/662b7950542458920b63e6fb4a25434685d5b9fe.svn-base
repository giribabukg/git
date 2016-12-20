<?php
class CInc_Utl_Fil_Cnt extends CCor_Cnt {

  protected function getFinder($aSrc, $aJobId = 0, $aJob = NULL) {
    $lRet = new CApp_Finder($aSrc, $aJobId, $aJob);
    return $lRet;
  }

  public function actDownloadTokenFile() {
  	$lFileName = $this -> getReq('fn');
  	$lSrc = $this -> getReq('src');
  	$lSub = $this -> getReq('sub');
  	$lJobid = $this -> getReq('jid');
  	$lVerId = $this->getReq('dvi');
  	$lDownloadSrc = $this->getReq('download_src'); // only in case of downloading from Third party system and not from pdf or doc.
  	if ($lDownloadSrc == 'wec' AND !empty($lVerId)) return $this -> actWecversion();
  	else return $this -> actDown();
  }
  
  protected function actDown() {
    $lNam = $this -> getReq('fn');
    $lSrc = $this -> getReq('src');
    $lSub = $this -> getReq('sub');
    $lJid = $this -> getReq('jid');

    $lFnc = 'get'.$lSub;
    if ($this -> hasMethod($lFnc)) {
      $this -> $lFnc($lSrc, $lJid, $lNam);
      exit;
    }

    $lFin = $this -> getFinder($lSrc, $lJid);
    $lFil = $lFin -> getName($lNam, $lSub);
    if (is_readable($lFil)) {
      $lExt = strtolower(strrchr($lNam,'.'));
      if ($lExt == '.pdf') {
        header('Content-Type: application/pdf');
      } else if ($lExt == '.dxf'){
        header('Content-Type: application/dxf');
      } else {
        header('Content-Type: application/octet-stream');
      }
      header('Cache-Control: public');
      header('Pragma: public');
      header('Content-Disposition: attachment; filename="'.$lNam.'"');
      readfile($lFil);
      exit;
    }
  }
  
  protected function actView() {
    $lNam = $this -> getReq('fn');
    $lSrc = $this -> getReq('src');
    $lSub = $this -> getReq('sub');
    $lJid = $this -> getReq('jid');

    $lFnc = 'get'.$lSub;
    if ($this -> hasMethod($lFnc)) {
      $this -> $lFnc($lSrc, $lJid, $lNam);
      exit;
    }

    $lFin = $this -> getFinder($lSrc, $lJid);
    $lFil = $lFin -> getName($lNam, $lSub);
    if (is_readable($lFil)) {
      $lExt = strtolower(strrchr($lNam,'.'));
      if ($lExt == '.pdf') {
        header('Content-Type: application/pdf');
      } else if ($lExt == '.dxf'){
        header('Content-Type: application/dxf');
      } else {
        header('Content-Type: application/octet-stream');
      }
      readfile($lFil);
      exit;
    }
  }
  
  public function checkToken($aToken) {
  	$lRes = FALSE;
  	$lSql = 'SELECT f.`filename` AS fn, d.`mand`, d.`src`, f.`sub`, d.`jobid` AS jid, d.`jobid` AS jobid, d.`download_src`, d.`wec_ver_id` AS dvi  FROM `al_job_files_down_links` d';
  	$lSql.= ' LEFT JOIN `al_job_files` f ON (d.`file_id` = f.`id`)';
  	$lSql.= ' WHERE d.`token` = '.esc($aToken);
  	$lSql.= ' AND d.`expire_date` > NOW() AND d.`available` = 1';
  	$lSql.= ' LIMIT 1';

  	$lQry = new CCor_Qry($lSql);
  	$lRes = $lQry->getDat();
  	return $lRes;
  }

  protected function getPdf($aSrc, $aJobId, $aFilename) {
    $lUploadViaAlink = CCor_Cfg::get('wec.upload.alink', TRUE);
    if ($lUploadViaAlink) {
      $this -> getPdfViaAlink($aSrc, $aJobId, $aFilename);
    }
    else {
      $this -> getPdfViaFolder($aSrc, $aJobId, $aFilename);
    }
  }
  
  protected function getPdfViaFolder($aSrc, $aJobId, $aFilename) {
    header('Cache-Control: public');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="'.$aFilename.'"');
    
    $lFinder = new CApp_Finder($aSrc, $aJobId);
    $lDir = $lFinder ->getDynPath(CCor_Cfg::get('flink.destination.pdf.dir'));
    if (is_readable($lDir.DS.$aFilename)) {
      $lExt = strtolower(strrchr($aFilename,'.'));
      if ($lExt == '.pdf') {
        header('Content-Type: application/pdf');
      } else if ($lExt == '.dxf'){
        header('Content-Type: application/dxf');
      } else {
        header('Content-Type: application/octet-stream');
      }
      header('Cache-Control: public');
      header('Pragma: public');
      header('Content-Disposition: attachment; filename="'.$aFilename.'"');
      readfile($lDir.DS.$aFilename);
      exit;
    }
    
  }
  
  protected function getPdfViaAlink($aSrc, $aJobId, $aFilename) {
    header('Cache-Control: public');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="'.$aFilename.'"');
    
    $lQry = new CApi_Alink_Query('getFile');
    $lQry -> addParam('sid', MAND);
    $lQry -> addParam('jobid', $aJobId);
    $lQry -> addParam('filename', $aFilename);
    $lRes = $lQry -> query();
    echo base64_decode($lRes -> getVal('data'));
    exit;
  }

  protected function actWecversion() {
    $lVerId = $this->getReq('dvi');
    $lSrc   = $this->getReq('src');
    $lJid   = $this->getReq('jobid');
    
    $lObj = new CApp_Wec($lSrc, $lJid);
    $lWecPid = $lObj ->getWebcenterId();

    $lRobot = new CApi_Wec_Robot();
    $lRobot->loadConfig();
    $lRet = $lRobot->getDownloadDocumentVersion($lWecPid, $lVerId);

    if (!$lRet) {
      echo 'Error loading file';
      exit;
    }
    $unzip = true;
    if ($unzip) {
      //file_put_contents('php://temp/maxmemory:1048576', $lRet);
      $lTmp = tempnam('tmp/', 'aa');
#      echo $lTmp;
      //file_put_contents('php://temp', $lRet);
      if (!file_put_contents($lTmp, $lRet)) {
        echo 'Error writing to file';
        exit;
      }
      $lZip = new ZipArchive;
      if (!$lZip->open($lTmp)) {
        echo 'Error uncompressing file';
        exit;
      }
      $lName = $lZip->getNameIndex(0);
      if (strpos($lName,'.') === false) {
        $lName.= '.pdf';
      }
      header('Content-Type: application/octet-stream');
      header('Cache-Control: public');
      header('Pragma: public');
      header('Content-Disposition: attachment; filename="'.$lName.'"');
      echo $lZip->getFromIndex(0);
    } else {
      header('Content-Type: application/octet-stream');
      header('Cache-Control: public');
      header('Pragma: public');
      header('Content-Disposition: attachment; filename="download.zip"');

      echo $lRet;
    }
  }

  protected function actWecapprover() {
    $lVerId = $this -> getReq('dvi');
    $lSrc   = $this -> getReq('src');
    $lJid   = $this -> getReq('jobid');
    $lAPLId = $this -> getReq('aplid');

    $lObj = new CApp_Wec($lSrc, $lJid);
    $lWecPid = $lObj ->getWebcenterId();

    $lRobot = new CApi_Wec_Robot();
    $lRobot -> loadConfig();
    $lRet = $lRobot->getDownloadDocumentVersion($lWecPid, $lVerId);

    if (!$lRet) {
      echo 'Error loading file';
      exit;
    }

    $lTmp = tempnam('tmp/', 'aa');
    echo $lTmp;

    if (!file_put_contents($lTmp, $lRet)) {
      echo 'Error writing to file';
      exit;
    }

    $lZip = new ZipArchive;
    if (!$lZip -> open($lTmp)) {
      echo 'Error uncompressing file';
      exit;
    }

    $lName = $lZip -> getNameIndex(0);
    if (strpos($lName,'.') === FALSE) {
      $lName.= '.pdf';
    }

    file_put_contents('tmp/'.$lName, $lZip -> getFromIndex(0));
    $lZip -> close();

    // create new PDF
    $lPDF = new Zend_Pdf();

    $lSql = 'SELECT a.num, b.name, b.datum, b.gru_id';
    $lSql.= ' FROM al_job_apl_loop AS a, al_job_apl_states AS b';
    $lSql.= ' WHERE a.id=b.loop_id';
    $lSql.= ' AND a.mand='.MID;
    $lSql.= ' AND a.jobid='.esc($lJid);
    //$lSql.= ' AND b.loop_id='.esc($lAPLId);
    $lSql.= ' AND b.`status`=3';
    $lSql.= ' AND b.done="Y"';
    $lSql.= ' ORDER BY b.datum DESC';

    $lVerticalOffset = 24;
	$lLoop = $lPage = 0;
    $lGroups = CCor_Res::extract('id', 'name', 'gru');
	$lNewPage = TRUE;
	
	$lWaveLogo = Zend_Pdf_Image::imageWithPath('img/pag/cust.png'); //WAVE logo
    $lLogo = Zend_Pdf_Image::imageWithPath('img/pag/logo.png'); //5Flow logo
	
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lKey => $lValue) {
      if($lNewPage) {
        $lPDFPage = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
        $lPDFFontHelvetica = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $lPDFFontHelveticaBold = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);

        $lPDFPage -> setFont($lPDFFontHelveticaBold, 16) -> drawText(lan('apl.pdf.approver.header'), 40, 800);
        $lPDFPage -> drawImage($lLogo, 470, 785, 570, 820);
        
        $lLoop--; //make loop different to $lValue['num'] so cycle header is placed in the page
        $lNewPage = FALSE;
      }
      
	  if($lValue['num'] !== $lLoop) {
		if($lLoop > 0){
			$lVerticalOffset += 24;
		}
	    $lLoop = $lValue['num'];
	    $lPDFPage -> setFont($lPDFFontHelveticaBold, 14) -> drawText("Cycle ".$lLoop, 40, (780 - $lVerticalOffset));
        $lVerticalOffset += 24;
	  }
	
	  $lText = date(lan("lib.datetime.long"), strtotime($lValue['datum']))." : ";
	  
	  $lUserName = explode(", ", $lValue['name']);
	  $lText.= $lUserName[1]." ".$lUserName[0];
      
	  $lGru = intval($lValue['gru_id']);
      if($lGru > 0 && array_key_exists($lGru, $lGroups) !== FALSE) {
        $lText.= " (".$lGroups[$lGru].")";
      }
      $lPDFPage -> setFont($lPDFFontHelvetica, 12) -> drawText($lText, 40, (780 - $lVerticalOffset));
      $lVerticalOffset += 24;

      if((760 - $lVerticalOffset) < 64) { // if end of page is reached
        //set page footer
        $lPDFPage -> setFont($lPDFFontHelveticaBold, 10) -> drawText("Page ".($lPage+1), 530, 30);
		$lPDFPage -> drawImage($lWaveLogo, 40, 10, 170, 45);
        
        $lPDF -> pages[] = $lPDFPage;
        $lPDF -> save('tmp/'.$lName.'.add');
        
        $lPage++;
        $lNewPage = TRUE; //set new page to be created
        $lVerticalOffset = 24;
      }
    }
    
    if($lLoop == 0) { //no one has approved for the job
      $lPDFPage = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
      $lPDFFontHelvetica = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
      $lPDFFontHelveticaBold = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);

      $lPDFPage -> setFont($lPDFFontHelveticaBold, 16) -> drawText(lan('apl.pdf.approver.not.header'), 40, 800);
      $lPDFPage -> drawImage($lLogo, 470, 785, 570, 820);
    }
    //set last page footer
    $lPDFPage -> setFont($lPDFFontHelveticaBold, 10) -> drawText("Page ".($lPage+1), 530, 30);
	$lPDFPage -> drawImage($lWaveLogo, 40, 20, 160, 55);

    $lPDF -> pages[] = $lPDFPage;
    $lPDF -> save('tmp/'.$lName.'.add');
    
    // combine both PDFs
    $lPDF1 = Zend_Pdf::load('tmp/'.$lName);
    $lPDF2 = Zend_Pdf::load('tmp/'.$lName.'.add');
    $lExtractor = new Zend_Pdf_Resource_Extractor();
    
    foreach($lPDF2 -> pages as $lIdx => $lVal) {
      $lPDF2Content = $lExtractor -> clonePage($lPDF2 -> pages[$lIdx]);
      $lPDF1 -> pages[] = $lPDF2Content;
      $lPDF1 -> save('tmp/'.$lName, TRUE);
    }

    header('Content-Type: application/octet-stream');
    header('Cache-Control: public');
    header('Pragma: public');
    header('Content-Disposition: attachment; filename="'.$lName.'"');
    echo file_get_contents('tmp/'.$lName);

    unlink($lTmp);
    unlink('tmp/'.$lName);
    unlink('tmp/'.$lName.'.add');
  }

  public function actWecxfdf() {
    $lWecPid = $this->getReq('wec_prj_id');
    $lDocVerId = $this->getReq('dvi');

    $lClient = new CApi_Wec_Client();
    $lClient->loadConfig();
    $lQry = new CApi_Wec_Query($lClient);
    $lQry -> setParam('projectid', $lWecPid);
    $lQry -> setParam('docversionid', $lDocVerId);

    $lQry -> setParam('returnasfile', 0);
    $lRet = $lQry -> query('DownloadAnnotations.jsp');
    $lRet = str_replace('<xfdf>', '<xfdf xml:space="preserve" xmlns="http://ns.adobe.com/xfdf/">', $lRet);

    header('Content-Type: application/octet-stream');
    header('Cache-Control: public');
    header('Pragma: public');
    header('Content-Disposition: attachment; filename="'.$lDocVerId.'.xfdf"');
    echo $lRet;
    exit;
  }
}