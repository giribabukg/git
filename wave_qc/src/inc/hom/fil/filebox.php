<?php
class CInc_Hom_Fil_Filebox extends CCor_Ren {

  public function __construct($aTitle, $aMand) {
    $this -> mTitle = $aTitle;
    $this -> mMand = $aMand;

    $lUsr = CCor_Usr::getInstance();
    $this -> mCanInsert = $lUsr -> canInsert('app-fil');
    $this -> mCanDelete = $lUsr -> canDelete('app-fil');
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<div class="tbl w800">'.LF;
    if ($this -> mCanInsert) {
      $lDiv = getNum('d');
      $lRet.= '<div class="cap"><a href="javascript:Flow.Std.tog(\''.$lDiv.'\')" class="captxt">'.htm($this -> mTitle).'</a>';
      $lRet.= '</div>'.LF;
    } else {
      $lRet.= '<div class="cap">'.htm($this -> mTitle).'</div>'.LF;
    }
    $lRet.= '<div class="p0">'.LF;

    if ($this -> mCanInsert) {
      $lRet.= '<div id="'.$lDiv.'" class="td2 p4">'.LF;
      $lRet.= '<form action="index.php" method="post" enctype="multipart/form-data">'.LF;
      $lRet.= '<input type="hidden" name="act" value="hom-fil.snew" />'.LF;
      $lRet.= '<input type="hidden" name="sub" value="'.$this -> mMand.'" />'.LF;
      $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>';
      $lRet.= '<td>'.htm(lan('lib.upload')).'</td>';
      $lRet.= '<td><input type="file" name="file" /></td>';
      $lRet.= '<td>'.btn(lan('lib.ok'), '', '<i class="ico-w16 ico-w16-ok"></i>', 'submit').'</td>';
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
      $lRet.= '<td class="th2">'.htm(lan('lib.file.date')).'</td>';
      $lRet.= '<td class="th2">'.htm(lan('lib.file.view')).'</td>';
      if ($this -> mCanDelete) {
        $lRet.= '<td class="th2 w16">&nbsp;</td>';
      }
      $lRet.= '</tr>'.LF;
      foreach ($lRows as $lRow) {
        $lRet.= '<tr class="hi">';
        $lRet.= '<td class="'.$lCls.' w16 ar">'.$lCtr.'.</td>';
        $lRet.= '<td class="'.$lCls.' w100p">';
        $lLnk = 'index.php?act=utl-fil.down&src=app&sub='.$this -> mMand.'&fn='.urlencode($lRow['name']);
        $lRet.= '<a href="'.htm($lLnk).'">';
        $lRet.= htm($lRow['name']);
        $lRet.= '</a>';
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
        
		$lFile = 'index.php?act=utl-fil.view&src=app&sub='.$this -> mMand.'&fn='.urlencode($lRow['name']);
        $lRet.= '<td class="'.$lCls.' ac">';
        $lRet .= '<a class="nav" href="'.$lFile.'" target="_BLANK">';
        $lRet .= img('img/ico/16/search.gif');
        $lRet .= '</a>';
        $lRet.= '</td>';

        if ($this -> mCanDelete) {
          $lUncodedPart = 'index.php?act=hom-fil.del&amp;sub='.$this -> mMand.'&amp;fn=';
          $lCodedPart = urlencode($lRow['name']);
          $lRet.= '<td class="'.$lCls.' ac">';
          $lRet .= '<a class="nav" href="javascript:Flow.Std.cnfDelGeneralFiles(\''.$lUncodedPart.'\', \''.$lCodedPart.'\')">';
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
    $lFin = new CApp_Finder('app');
    $lFin -> setMid($this -> mMand);
    $lDir = $lFin -> getPath($this -> mMand);
    $this->dbg('DIRECTORY '.$lDir);
    return $this->getFilesInDirectory($lDir);
  }

  protected function getFilesInDirectory($aDir) {
    $lRows = array();
    if (file_exists($aDir)) {
      try {
        $lIte = new DirectoryIterator($aDir);
        foreach ($lIte as $lRow) {
          $lItm = array();
          if ($lIte -> isFile()) {
            $lItm['name'] = $lIte -> getFilename();
            $lItm['size'] = $lIte -> getSize();
            $lItm['date'] = $lIte -> getMTime();
            $lRows[] = $lItm;
          }
        }
      } catch (Exception $lExc) {
        $this -> dbg($lExc -> getMessage(), mlWarn);
      }
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

}
