<?php
class CInc_Svn_Local extends CCor_Ren {
  
  public function __construct($aDir, $aLog) {
    $this->mDir = strtr($aDir, array('\\' => DS));
    $this->mLog = $aLog;
  }
  
  protected function getCont() {
    $lRet = '';
    $lRet.= '<form>';
    $lRet.= '<input type="hidden" class="elem-dir" name="dir" value="'.htm($this->mDir).'" />';
    $lRet.= '<table class="tbl w800" cellpadding="2">';
    $lRet.= $this->getHeader();
    $lRet.= $this->getRows();
    $lRet.= '</table>';
    $lRet.= BR.BR.$this->getButtons();
    $lRet.= '</form>';
    return $lRet;
  }
  
  protected function getHeader() {
    $lRet = '';
    $lRet.= '<thead><tr>';
    $lRet.= '<td class="th2 w16">&nbsp;</td>';
    $lRet.= '<td class="th2 w16">&nbsp;</td>';
    $lRet.= '<td class="th2 w16">&nbsp;</td>';
    $lRet.= '<td class="th2 w16">&nbsp;</td>';
    $lRet.= '<td class="th2 w700">Filename</td>';
    $lRet.= '<td class="th2 w100">Status</td>';
    $lRet.= '</tr></thead>';
    return $lRet;
  }
  
  protected function getCommitState($aState) {
    $lRet = '';
    if (in_array($aState, array('M', 'A', '?'))) {
      $lRet = 'checked';
    }
    if (in_array($aState, array('C', 'X', 'I'))) {
      $lRet = 'conflict';
    }
    return $lRet;
  }
  
  protected function getCheck($aStat, $aFilename) {
    $lRet = '<td class="td2 ac">';
    $lRes = $this->getCommitState($aStat);
    
    if ('checked' == $lRes) {
      $lRet.= '<input class="svn-cb" type="checkbox" name="file[]" checked="checked" value="'.htm($aFilename).'" />';
    } else {
      $lRet.= '<input class="svn-cb" type="checkbox" name="file[]" value="'.htm($aFilename).'" />';
    }
    $lRet.= '</td>';
    return $lRet;
  }
  
  protected function getStat($aStat) {
    $lRet = '<td class="td2 ac">';
    $lRet.= '<span class="app-version">';
    $lRet.= $aStat;
    $lRet.= '</span>';
    $lRet.= '</td>';
    return $lRet;
  }
  
  protected function getStates() {
    $lRet = array();
    $lRet[' '] = 'no changes';
    $lRet['A'] = 'added';
    $lRet['D'] = 'deleted';
    $lRet['M'] = 'changed';
    $lRet['R'] = 'replaced';
    $lRet['C'] = 'conflict!';
    $lRet['X'] = 'externals';
    $lRet['I'] = 'ignored';
    $lRet['?'] = 'unversioned';
    $lRet['!'] = 'missing';
    $lRet['~'] = 'type changed';
    return $lRet;
  }
  
  protected function getRows() {
    $lRet = '';
    $lNum = 1;
    $lValid = $this->getStates();
    foreach ($this->mLog as $lLine) {
      if (strpos($lLine, 'Textkonflikte') !== false) {
        continue;
      } 
      $lStat = $lLine[0];
      if (!isset($lValid[$lStat])) {
        continue;
      }
      $lRet.= '<tr class="hi">';
      
      $lBase = trim(substr($lLine, 4));
      $lRet.= $this->getCheck($lStat, $lBase);
      
      for ($i=0; $i<3; $i++) {
        $lRet.= $this->getStat($lLine[$i]);
      }
      
      $lName = strtr($lBase, array('\\' => DS));
      $lName = strtr($lName, array($this->mDir => '..'));
      
      $lRet.= '<td class="td1">';
      $lRet.= $lName;
      $lRet.= '</td>';
      
      $lRet.= '<td class="td2">';
      $lRet.= $lValid[$lStat];
      $lRet.= '</td>';
      
      $lRet.= '</tr>';
      $lNum++;
    }
    return $lRet;
  }
  
  protected function getButtons() {
    $lRet = '';
    $lRet.= '<div class="svn-btn">';
    $lRet.= btn('Select all', 'svnTogCheck(true)');
    $lRet.= btn('Deselect all', 'svnTogCheck(false)');
    $lRet.= btn('Commit selected', 'jQuery(".svn-btn, .svn-commit").toggle()');
    $lRet.= btn('Revert selected', 'svnRevert(this)');
    $lRet.= btn('Add selected', 'svnAdd(this)');
    $lRet.= '</div>';
    $lRet.= '<div class="svn-commit" style="display:none">';
    $lRet.= '<label for="elem-msg">Message:</label>'.BR;
    $lRet.= '<textarea id="elem-msg" class="svn-msg box w700"></textarea>';
    $lRet.= btn('Commit selected', 'svnCommit(this)');
    $lRet.= btn('Cancel', 'jQuery(".svn-btn, .svn-commit").toggle()');
    $lRet.= '</div>';
    
    return $lRet;
  }
  
}