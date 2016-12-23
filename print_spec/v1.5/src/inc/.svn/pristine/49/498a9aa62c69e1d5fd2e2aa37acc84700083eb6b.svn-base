<?php
class CInc_Ajx_List extends CCor_Ren {

  public function __construct($aDir = NULL, $aPref = 'p') {
    if (NULL === $aDir) {
      $this -> mDir = getcwd();
      $this -> mMain = TRUE;
    } else {
      $this -> mDir = $aDir;
      $this -> mMain = FALSE;
    }
    $this -> mPrefix = $aPref;
  }

  protected function getCont() {
    $lRet = '';
    if ($this -> mMain) {
      $lRet.= '<h1>'.$this -> mDir.'</h1>';
      $lRet.= '<div class="w400">';
    }
    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w100p" style="border-collapse:collapse">';

    $lIte = new DirectoryIterator($this -> mDir);
    foreach ($lIte as $lFil) {
      if ($lIte -> isDot()) continue;
      if ($lIte -> isDir()) {
        $lNam = $this->mDir.DS.$lFil;
        $lNam = strtr($lNam, '\\', '/');
        $lNum = getNum($this -> mPrefix).'_';
        $lRet.= '<tr>'.LF;
        $lRet.= '<td class="td1 w16">'.img('img/ico/16/folder.png').'</td>'.LF;
        $lRet.= '<td class="td1">'.LF;
        $lRet.= '<a href="javascript:getSubDir(\''.$lNum.'\',\''.$lNam.'\')" class="nav">'.$lFil.'</a>'.LF;
        $lRet.= '</td>'.LF;
        $lRet.= '</tr>'.LF;
        $lRet.= '<tr style="display:none" id="'.$lNum.'">'.LF;
        $lRet.= '<td class="td1">&nbsp;</td>'.LF;
        $lRet.= '<td class="p0">'.LF;
        $lRet.= '<div id="'.$lNum.'_">Loading...</div>'.LF;
        $lRet.= '</td>'.LF;
        $lRet.= '</tr>'.LF;
      }
    }
    $lRet.= '</table>';
    if ($this -> mMain) {
      $lRet.= '</div>';
    }
    return $lRet;
  }

}