<?php
class CInc_Svn_Files extends CCor_Ren {
  
  public function __construct($aDir) {
    $this->mDir = $aDir;
    //echo $aDir;
  }
  
  protected function getIterator() {
    $lRet = new DirectoryIterator($this->mDir);
    return $lRet;
  }
  
  protected function getCont() {
    $lRet = '';
    $lRet.= '<table class="tbl w800" cellpadding="2">';
    $lRet.= $this->getHeader();
    $lRet.= $this->getRows();
    $lRet.= '</table>';
    $lRet.= BR.BR.$this->getButtons();
    return $lRet;
  }
  
  protected function getHeader() {
    $lRet = '';
    $lRet.= '<thead><tr>';
    $lRet.= '<td class="th2 w16">No.</td>';
    $lRet.= '<td class="th2 w400">Filename</td>';
    $lRet.= '<td class="th2 w50 ar">Size</td>';
    $lRet.= '<td class="th2 w100 ar">Date</td>';
    $lRet.= '</tr></thead>';
    return $lRet;
  }
  
  protected function formatSize($aBytes) {
    $lVal = $aBytes;
    $lRet = $lVal.' Bytes';
    if ($lVal > 1024) {
      $lRet = number_format($lVal / 1024, 1).' kB';
    }
    $lMb = 1024 * 1024;
    if ($lVal > $lMb) {
      $lRet = number_format($lVal / $lMb, 1).' MB';
    }
    return $lRet;
  }
  
  protected function formatDate($aTimestamp) {
    $lDate = new CCor_Datetime();
    $lDate->setTime($aTimestamp);
    return $lDate->getFmt('d.m.Y H:i:s');
  }
  
  protected function getRows() {
    $lRet = '';
    $lNum = 1;
    $lIte = $this->getIterator();
    foreach ($lIte as $lFile) {
      if (!$lFile->isFile()) {
        continue;
      }
      $lRet.= '<tr class="hi">';
      $lRet.= '<td class="td2 ar">';
      $lRet.= $lNum;
      $lRet.= '.</td>';
      
      $lRet.= '<td class="td1">';
      $lRet.= $lFile->getFilename();
      $lRet.= '</td>';
      
      $lRet.= '<td class="td2 ar">';
      $lRet.= $this->formatSize($lFile->getSize());
      $lRet.= '</td>';
      
      $lRet.= '<td class="td2 ar nw">';
      $lRet.= $this->formatDate($lFile->getMTime());
      $lRet.= '</td>';
      
      $lRet.= '</tr>';
      $lNum++;
    }
    return $lRet;
  }
  
  protected function getButtons() {
    $lRet = '';
    $lRet.= '<div class="svn-btn">';
    $lRet.= btn('Dry run', 'svnLoad("dryrun")');
    $lRet.= btn('Update this folder', 'svnLoad("update")');
    $lRet.= '</div>';
  
    return $lRet;
  }
  
}