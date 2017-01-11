<?php
class CInc_Hom_Mass_Filebox extends CCor_Ren {

  public function __construct($aTitle, $aMand) {
    $this -> mTitle = $aTitle;
    $this -> mMand = $aMand;

    $lUsr = CCor_Usr::getInstance();
    $this -> mCanInsert = $lUsr -> canInsert('app-mass');
    $this -> mCanDelete = $lUsr -> canDelete('app-mass');
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<div class="tbl w800">'.LF;
    if ($this -> mCanInsert) {
      $lDiv = getNum('d');
      $lRet.= '<div class="cap"><a href="javascript:Flow.Std.tog(\''.$lDiv.'\')" class="captxt">'.htm(lan('lib.mass_upload')).' '.htm($this -> mTitle).'</a>';
      $lRet.= '</div>'.LF;
    } else {
      $lRet.= '<div class="cap">'.htm($this -> mTitle).'</div>'.LF;
    }
    $lRet.= '<div class="p0">'.LF;

    if ($this -> mCanInsert) {
      $lRet.= '<div id="'.$lDiv.'" class="td2 p4">'.LF;
      $lRet.= '<form action="index.php" method="post" enctype="multipart/form-data">'.LF;
      $lRet.= '<input type="hidden" name="act" value="hom-mass.snew" />'.LF;
      $lRet.= '<input type="hidden" name="sub" value="'.$this -> mMand.'" />'.LF;
      $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>';
      $lRet.= '<td>'.htm(lan('lib.upload_mass')).'</td>';
      $lRet.= '<td><input type="file" name="file" /></td>';
      $lRet.= '<td>'.btn(lan('lib.ok'), '', '<i class="ico-w16 ico-w16-ok">', 'submit').'</td>';
      $lRet.= '</tr></table>';
      $lRet.= '</form>';
      $lRet.= '</div>';
    }

    $lRows = $this->getFiles();

    $lCls = 'td1';
    $lCtr = 1;
    if (!empty($lRows)) {
      $lRet.= '<table cellpadding="2" cellspacing="0" class="w100p">'.LF;
      $lRet.= '<tr>';
      $lRet.= '<td class="th2 w16">&nbsp;</td>';
      $lRet.= '<td class="th2 w100p">'.htm(lan('lib.file.name')).'</td>';
      $lRet.= '<td class="th2">'.htm(lan('lib.file.size')).'</td>';
      $lRet.= '<td class="th2">'.htm(lan('lib.file.time.modification')).'</td>';
      $lRet.= '<td class="th2">'.htm(lan('lib.file.fileStatus')).'</td>';
      if ($this -> mCanDelete) {
        $lRet.= '<td class="th2 w16">&nbsp;</td>';
      }
      $lRet.= '</tr>'.LF;
      foreach ($lRows as $lRow) {
        $lRet.= '<tr class="hi">';
        $lRet.= '<td class="'.$lCls.' w16 ar">'.$lCtr.'.</td>';
        $lRet.= '<td class="'.$lCls.' w100p">';
        $lRet.= htm($lRow['name']);
        $lRet.= '</td>';

        $lRet.= '<td class="'.$lCls.' ar nw">';
        $lRet.= '<a href="'.htm($lLnk).'">';
        $lRet.= $this -> fmtSize($lRow['size']);
        $lRet.= '</a>';
        $lRet.= '</td>';

        $lRet.= '<td class="'.$lCls.' nw">';
        $lRet.= '<a href="'.htm($lLnk).'">';
        $lRet.= $this -> fmtDate($lRow['date']);
        $lRet.= '</a>';
        $lRet.= '</td>';

        $lRet.= '<td class="'.$lCls.' nw">';
        $lRet.= '<a href="'.htm($lLnk).'">';
        $lRet.= $this -> fmtProgress($lRow['progress']);
        $lRet.= '</a>';
        $lRet.= '</td>';

        if ($this -> mCanDelete) {
          $lDel = 'index.php?act=hom-mass.del&amp;sub='.$this -> mMand.'&amp;fn='.htm(urlencode($lRow['name']));
          $lRet.= '<td class="'.$lCls.' ac">';
          $lRet .= '<a class="nav" href="javascript:Flow.Std.cnf(\''.$lDel.'\', \'cnfDel\')">';
          $lRet .= img('img/ico/16/del.gif');
          $lRet .= '</a>';
          $lRet.= '</td>';
        }

        $lRet.= '</tr>';
        $lCls = ($lCls == 'td1') ? 'td2' : 'td1';
        $lCtr++;
      }
      $lRet.= '</table>';
    }
    $lRet.= '</div>'.LF;
    $lRet.= '</div>'.BR.BR.LF;
    return $lRet;
  }

  protected function getFiles() {
    $lFin = new CApp_Finder('mass');
    $lFin -> setMid($this -> mMand);
    $lDir = $lFin -> getPath($this -> mMand);
    $this->dbg('DIRECTORY '.$lDir);
    return $this->getFilesInDirectory($lDir);
  }

  protected function getFilesInDirectory($aDir) {
    $lRows = array();
    if (file_exists($aDir.'in/')) {
      try {
        $lIte = new DirectoryIterator($aDir.'in/');
        foreach ($lIte as $lRow) {
          $lItm = array();
          $lFilext = $lIte -> getFilename();
	      $lExt = pathinfo($lFilext, PATHINFO_EXTENSION);
	      $lExtensions = CCor_Cfg::get('mass.file.extension');
	      if(in_array($lExt, $lExtensions)){
	        if ($lIte -> isFile()) {
	          $lItm['name'] = $lIte -> getFilename();
	          $lItm['size'] = $lIte -> getSize();
	          $lItm['date'] = $lIte -> getMTime();
	          $lItm['progress'] = '<strong>'.htm(lan('lib.file.inprogress')).'</strong>';
	          $lRows[] = $lItm;

	          $this -> fmtProgress($lItm['progress']);
	        }
	      }
        }

        if(file_exists($aDir.'parsed/')){
	        $lIte = new DirectoryIterator($aDir.'parsed/');
	        foreach ($lIte as $lRow) {
	          $lItm = array();
	          $lFilext = $lIte -> getFilename();
	          $lExt = pathinfo($lFilext, PATHINFO_EXTENSION);
	          $lExtensions = CCor_Cfg::get('mass.file.extension');
	          if(in_array($lExt, $lExtensions)){
		        if ($lIte -> isFile()) {
		          $lItm['name'] = $lIte -> getFilename();
		          $lItm['size'] = $lIte -> getSize();
		          $lItm['date'] = $lIte -> getMTime();
		          $lItm['progress'] = '<strong>'.htm(lan('lib.file.processed')).'</strong>';
		          $lRows[] = $lItm;

		          $this -> fmtProgress($lItm['progress']);
		        }
	          }
	        }
        }else{
        	$this -> makeDir($aDir.'parsed/', 0777);
        }

        if(file_exists($aDir.'error/')){
	        $lIte = new DirectoryIterator($aDir.'error/');
	        foreach ($lIte as $lRow) {
	          $lItm = array();
	          $lFilext = $lIte -> getFilename();
	          $lExt = pathinfo($lFilext, PATHINFO_EXTENSION);
	          $lExtensions = CCor_Cfg::get('mass.file.extension');
	          if(in_array($lExt, $lExtensions)){
		        if ($lIte -> isFile()) {
		          $lItm['name'] = $lIte -> getFilename();
		          $lItm['size'] = $lIte -> getSize();
		          $lItm['date'] = $lIte -> getMTime();
		          $lItm['progress'] = '<strong>'.htm(lan('lib.file.error')).'</strong>';
	              $lRows[] = $lItm;

                  $this -> fmtProgress($lItm['progress']);
                }
	          }
	        }
        }else{
        	$this -> makeDir($aDir.'error/', 0777);
        }

      } catch (Exception $lExc) {
        $this -> dbg($lExc -> getMessage(), mlWarn);
      }
    }else{
	  $this -> makeDir($aDir.'in/', 0777);
	  $this -> makeDir($aDir.'parsed/', 0777);
	  $this -> makeDir($aDir.'error/', 0777);
    }
    return $lRows;
  }

  protected function fmtSize($aBytes) {
    $lVal = $aBytes;
    $lRet = $lVal.' Bytes';
    if ($lVal > 1024) {
      $lRet = number_format($lVal/1024,1).' kB';
    }
    $lMb = 1024 * 1024;
    if ($lVal > $lMb) {
      $lRet = number_format($lVal/$lMb,1).' MB';
    }
    return $lRet;
  }

  protected function fmtDate($aTimestamp) {
    return date('D '.lan('lib.datetime.short'), $aTimestamp);
  }

  protected function fmtProgress($aFile) {
    $lMsg = $aFile;
    return $lMsg;
  }

  public function makeDir($aPath, $aMode = 0777) {
    $lSub = substr($aPath, 0, strrpos($aPath, DS));
    if ('' != $lSub) {
      $this -> makeDir($lSub, $aMode);
    }
    if (!file_exists($aPath)) {
      mkdir($aPath, $aMode);
    }
  }
}