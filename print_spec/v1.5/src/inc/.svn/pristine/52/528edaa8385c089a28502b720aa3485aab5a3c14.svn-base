<?php
class CInc_Svn_Cnt extends CCor_Cnt {
  
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = 'SVN';
    $this -> setProtection('*', 'svn', rdRead);
  }
  
  protected function actStd() {
    $lTree = new CSvn_Tree();
    
    $this->mRootDir = dirname($_SERVER['SCRIPT_FILENAME']);
    
    $lFolders = $this->getFoldersToAdd();
    foreach ($lFolders as $lName => $lFolder) {
      $lTree->addRoot($lFolder, $lName);
    }
    $lWrap = new CSvn_Wrap($lTree);
    
    $this->render($lWrap);
  }
  
  protected function getFoldersToAdd() {
    $lUsr = CCor_Usr::getInstance();
    $lDir = $lUsr->getPref('svn.dir');
    //$lUsr->setPref('svn.dir', 'root');
    
    if (is_array($lDir)) {
      // could be an array, e.g. (cust=>blah/cust, mand=>blah/mand)
      $lRet = $lDir;
    } elseif ('root' == $lDir) {
      $lRet['root'] = $this->mRootDir;
    } else {
      // default : only current mandator
      $lRet[MANDATOR] = $this->mRootDir.DS.'mand'.DS.'mand_'.MID;
    }
    return $lRet;
  } 
  
  protected function actFolder() {
    $lCred = $this->getSvnCredentials();
    if (!$lCred) {
      return $this->actLogin();
    }
    echo BR;
    $lTab = new CSvn_Bar('folder');
    $lTab->render();
    
    $lDir = $this->getReq('dir');
    $lFiles = new CSvn_Files($lDir);
    $lFiles->render();
    exit;
  }
  
  protected function actLog() {
    $lDir = $this->getReq('dir');
    
    $lCred = $this->getSvnCredentials();
    if (!$lCred) {
      return $this->actLogin();
    }
    echo BR;
    $lTab = new CSvn_Bar('log');
    $lTab->render();
    
    $lExe = 'svn log -v '.escapeshellarg($lDir);
    $lExe.= ' --username '.escapeshellarg($lCred['user']);
    $lExe.= ' --password '.escapeshellarg($lCred['pass']);
    $lExe.= ' --no-auth-cache --non-interactive --trust-server-cert';
    $lExe.= ' -l 10';
    $this->dbg($lExe);
    
    exec($lExe, $lArr);
    echo nl2br(implode(BR, $lArr));
    exit;
  }
  
  protected function actLocal() {
    $lCred = $this->getSvnCredentials();
    if (!$lCred) {
      return $this->actLogin();
    }
    echo BR;
    $lTab = new CSvn_Bar('local');
    $lTab->render();
  
    $lDir = $this->getReq('dir');
  
    $lExe = '';
    $lExe.= 'svn status '.escapeshellarg($lDir);
    $lExe.= ' --username '.escapeshellarg($lCred['user']);
    $lExe.= ' --password '.escapeshellarg($lCred['pass']);
    $lExe.= ' --depth=infinity';
    $this->dbg($lExe);
    exec($lExe, $lArr);
    if (empty($lArr)) { 
      echo BR.'No local changes.';
    } else {
      $lView = new CSvn_Local($lDir, $lArr);
      $lView->render();
    }
    exit;
  }
  
  protected function actCommit() {
    $lDir = $this->getReq('dir');
    $lFiles = $this->getReq('files');
    $lMsg = $this->getReq('msg');

    $lCred = $this->getSvnCredentials();
    if (!$lCred) {
      return $this->actLogin();
    }
    if (empty($lFiles)) {
      echo "No files were selected";
      exit;
    }
    
    $lExe = '';
    $lExe.= 'svn commit ';
    foreach ($lFiles as $lFile) {
      $lExe.= escapeshellarg($lFile).' ';
    }
    $lExe.= '-m '.escapeshellarg($lMsg); 
    
    $lExe.= ' --username '.escapeshellarg($lCred['user']);
    $lExe.= ' --password '.escapeshellarg($lCred['pass']);
    $lExe.= ' --no-auth-cache --non-interactive --trust-server-cert';
    $this->dbg($lExe);
    exec($lExe, $lArr);
    echo '<div class="cap">Commit results</div>'.BR;
    echo '<div class="box">'.nl2br(utf8_encode(implode(BR, $lArr))).'</div>'.BR.BR;
    $this->actLocal();
  }
  
  protected function actRevert() {
    $lDir = $this->getReq('dir');
    $lFiles = $this->getReq('files');
  
    $lCred = $this->getSvnCredentials();
    if (!$lCred) {
      return $this->actLogin();
    }
    if (empty($lFiles)) {
      echo "No files were selected";
      exit;
    }
  
    $lExe = '';
    $lExe.= 'svn revert ';
    foreach ($lFiles as $lFile) {
      $lExe.= escapeshellarg($lFile).' ';
    }
    $this->dbg($lExe);
    exec($lExe, $lArr);
    echo '<div class="cap">Revert results</div>'.BR;
    echo '<div class="box">'.nl2br(utf8_encode(implode(BR, $lArr))).'</div>'.BR.BR;
    $this->actLocal();
  }
  
  protected function actAdd() {
    $lDir = $this->getReq('dir');
    $lFiles = $this->getReq('files');
    if (empty($lFiles)) {
      echo "No files were selected";
      exit;
    }
    $lExe = '';
    $lExe.= 'svn add ';
    foreach ($lFiles as $lFile) {
      $lExe.= escapeshellarg($lFile).' ';
    }
    $this->dbg($lExe);
    exec($lExe, $lArr);
    echo '<div class="cap">Add results</div>'.BR;
    echo '<div class="box">'.nl2br(utf8_encode(implode(BR, $lArr))).'</div>'.BR.BR;
    $this->actLocal();
  }
  
  protected function actDryrun() {
    $lDir = $this->getReq('dir');
 
    $lCred = $this->getSvnCredentials();
    if (!$lCred) {
      return $this->actLogin();
    }
    $lExe = '';
    $lExe.= 'svn merge --dry-run -r BASE:HEAD '.escapeshellarg($lDir);
    $lExe.= ' --username '.escapeshellarg($lCred['user']);
    $lExe.= ' --password '.escapeshellarg($lCred['pass']);
    $lExe.= ' --no-auth-cache --non-interactive --trust-server-cert';
    $this->dbg($lExe);
    exec($lExe, $lArr);
    
    echo '<div class="cap">Dryrun Update results</div>'.BR;
    echo '<div class="box">'.nl2br(implode(BR, $lArr)).'</div>'.BR.BR;
    return $this->actFolder();
  }
  
  protected function actUpdate() {
    $lDir = $this->getReq('dir');
  
    $lCred = $this->getSvnCredentials();
    if (!$lCred) {
      return $this->actLogin();
    }
    $lExe = '';
    $lExe.= 'svn up '.escapeshellarg($lDir);
    $lExe.= ' --username '.escapeshellarg($lCred['user']);
    $lExe.= ' --password '.escapeshellarg($lCred['pass']);
    $lExe.= ' --no-auth-cache --non-interactive --trust-server-cert';
    $this->dbg($lExe);
    exec($lExe, $lArr);
  
    echo '<div class="cap">Update results</div>'.BR;
    echo '<div class="box">'.nl2br(implode(BR, $lArr)).'</div>'.BR.BR;
    return $this->actFolder();
  }
  
  protected function getSvnCredentials() {
    $lUsr = CCor_Usr::getInstance();
    $lName = $lUsr->getPref('svn.usr');
    $lPass = $lUsr->getPref('svn.pwd');
    if (empty($lName)) {
      return null;
    }
    return array('user' => $lName, 'pass' => $lPass);
  }
  
  protected function actLogin() {
    $lForm = new CHtm_Form('svn.slogin', 'SVN Login');
    $lForm->addDef(fie('usr', 'Username'));
    $lForm->addDef(fie('pwd', 'Password'));
    $lForm->render();
    exit;
  }
  
  protected function actSlogin() {
    $lVal = $this->getReq('val');
    $lUsr = CCor_Usr::getInstance();
    $lUsr->setPref('svn.usr', $lVal['usr']);
    $lUsr->setPref('svn.pwd', $lVal['pwd']);
    $this->redirect();
  }
  
}