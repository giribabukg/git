<?php
class CInc_Pixelboxx_Tree extends CHtm_Tree_List {
  
  
  public function __construct($aClient, $aDoi) {
    parent::__construct();
    $this->mClient = $aClient;
    $this->mDoi = $aDoi;
    $lUsr = CCor_Usr::getInstance();
    $this->mCond = $lUsr->getVal('elements_cond');
  }
  
  public function setPicklist($aPick) {
    $this->mPick = $aPick;
    $lSql = 'SELECT alias,col FROM al_pck_columns WHERE mand IN (0,'.MID.') ';
    $lSql.= 'AND domain='.esc($this->mPick);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this->mCol[$lRow['alias']] = $lRow['col'];
    }
  }
  
  public function setFields($aAliasArray) {
    if (empty($aAliasArray)) {
      $this->dbg('Empty AliasArray passed to setFields!', mlWarn);
      return;
    }
    foreach ($aAliasArray as $lAlias) {
      if (!isset($this->mCol[$lAlias])) {
        $this->dbg('Alias '.$lAlias.' not in picklist '.$this->mPick,  mlWarn);
        continue;
      }
      $this->mFields[$lAlias] = $this->mCol[$lAlias];
    }
  }
  
  public function loadItems() {
    if (!empty($this->mCond)) {
      $lReg = new CApp_Condition_Registry();
      $lCond = $lReg->loadFromDb($this->mCond);
    }
    
    $lSql = 'SELECT DISTINCT ';
    $lNum = 1;
    foreach ($this->mFields as $lAlias => $lCol) {
      $lSql.= 'col'.intval($lCol).' AS '.$lAlias.',';
      $lNum++;
    }
    
    $lSql = strip($lSql);
    $lSql.= ' FROM al_pck_items WHERE domain='.esc($this->mPick);
    $lSql.= ' ORDER BY ';
    foreach ($this->mFields as $lAlias => $lCol) {
      $lSql.= 'col'.intval($lCol).',';
    }
    $lSql = strip($lSql);
    
    //echo $lSql;
    $this->mArr = array();
    $lCount = 0;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if (!empty($lCond)) {
        $lCond->setContext('data', $lRow);
        $lMet = $lCond->isMet();
        if (!$lMet) continue;
      }
      $lRow = $lRow->toArray();
      $lArr = array();
      $lArr = $this->addRow($lRow);
      $this->mArr = array_merge_recursive($this->mArr, $lArr);
      $lCount++;
    }
    $this->dbg($lCount.' items processed');
    
    $this->addNodes($this->mArr, $this->mRoot, 0);
  }
  

  protected function addRow($aRow) {
    //echo implode( ' / ', $aRow).BR;
    $lRow = $aRow;
    $lFirst = array_shift($lRow);
    $lFirst = trim($lFirst);
    if ('' == $lFirst) {
      $lFirst = '[empty]';
    }
    if (0 == count($lRow)) {
      return array($lFirst => 1);
    }
    $lSub = $this->addRow($lRow);
    return array($lFirst => $lSub);
  }

  /**
   * 
   * @param array $aArray
   * @param CInc_Htm_Tree_Node $aParentNode
   */
  protected function addNodes($aArray, $aParentNode, $aLevel) {
    foreach ($aArray as $lKey => $lSub) {
      //$lAttributes = array('val' => $lKey, 'level' => $aLevel);

      //$lFunc = 'md5';
      $lFunc = 'crc32';
      if ($aLevel > 0) {
        $lHash = $lFunc($aParentNode->getVal('hash').' / '.$lKey);
        //echo $lKey.' has hash '.$lHash.BR;
        $lDbg = 'child has '.$aParentNode->getVal('hash').' / '.$lKey.' : '.$lHash;
      } else {
        $lHash = $lFunc($lKey);
        $lDbg = 'top has '.$lHash;
      }
      
      if (isset($this->mHashes[$lHash])) {
        $this->dbg('Hash collision!');
      }
      $this->mHashes[$lHash] = 1;
      
      #$lAttributes = array('hash' => $lHash, 'level' => $aLevel, 'dbg' => $lDbg);
      $lAttributes = array('hash' => $lHash, 'level' => $aLevel);
      $lNode = $aParentNode->add($lKey, $lAttributes);
      if (is_array($lSub)) {
        $lIsEmpty = ((count($lSub) == 1) && empty($lSub[0]));
        if (!$lIsEmpty) {
          $this->addNodes($lSub, $lNode, $aLevel +1);
        }
      }
    }
  
  }
  
  protected function getNode($aNode, $aDepth = 0) {
    try {
      $lCss = $aNode->hasChildren() ? ' bc-has-children' : '';
      
      $lRet = '<li id="'.$aNode -> getId().'" data-level="'.$aNode->getVal('level').'" class="bc-li'.$lCss.'">';
  
      $lRet.= $this -> getNodeSpan($aNode);
  
      if ($aNode -> hasChildren()) {
        $lDis = ($aNode -> isExpanded()) ? '' : ' style="display:none"';
        $lRet.= '<ul'.$lDis.'>';
        $lChi = $aNode -> getChildren();
        foreach ($lChi as $lChild) {
          $lRet.= $this -> getNode($lChild, $aDepth +1);
        }
        $lRet.= '</ul>';
      }
      $lRet.= '</li>'.LF;
    } catch (Exception $e) {
      $this -> dbg($e -> getMessage());
    }
    return $lRet;
  }
  
  protected function getNodeSpan($aNode) {
    $lRet = '<span class="nav bc-node" ';
    $lVal = $aNode->getVal('val');
    $lRet.= 'data-val="'.htm($lVal).'" ';
    $lRet.= 'data-hash="'.htm($aNode->getVal('hash')).'"';
    #$lRet.= 'data-dbg="'.htm($aNode->getVal('dbg')).'"';
    if ($aNode -> hasChildren()) {
      #$lRet.= ' onclick="jQuery(this).closest(\'li\').find(\'ul\').first().toggle();"';
    }
    $lRet.= '>'.htm($aNode -> getVal('caption')).'</span>';
    return $lRet;
  }
  

} 