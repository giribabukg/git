<?php
class CInc_Crp_Export extends CCor_Obj {
  
  public function __construct($aCrpId, $aMand = null) {
    $this->mCid = intval($aCrpId);
    $this->mMid = (is_null($aMand)) ? MID : intval($aMand);

    $this->loadStates();
    $this->loadSteps();
  }

  protected function loadStates() {
    $lSql = 'SELECT * FROM al_crp_status WHERE mand='.$this->mMid.' ';
    $lSql.= 'AND crp_id='.$this->mCid;
    $lQry = new CCor_Qry($lSql);
    $lRet = array();
    foreach ($lQry as $lRow) {
      $lRet[$lRow['id']] = $lRow;
    }
    $this->mStates = $lRet;
  }
  
  protected function loadSteps() {
    $lSql = 'SELECT * FROM al_crp_step WHERE mand='.$this->mMid.' ';
    $lSql.= 'AND crp_id='.$this->mCid;
    $lQry = new CCor_Qry($lSql);
    $lRet = array();
    foreach ($lQry as $lRow) {
      $lFrom = $lRow['from_id'];
      $lTo = $lRow['to_id'];
      $lOkay = true;
      if (!isset($this->mStates[$lFrom])) {
        $this->msg('Preset State ID '.$lFrom.' in Step '.$lRow['id'].' not found!', mtUser, mlError);
        $lOkay = false;
      }
      if (!isset($this->mStates[$lTo])) {
        $this->msg('Postset State ID '.$lFrom.' in Step '.$lRow['id'].' not found!', mtUser, mlError);
        $lOkay = false;
      }
      if ($lOkay) {
        $lRet[$lRow['id']] = $lRow;
      }
    }
    $this->mSteps = $lRet;
  }
  
  protected function initXml() {
    $lRoot = '<?xml version="1.0" encoding="UTF-8"?><workflow />';
    $this->mXml = simplexml_load_string($lRoot);
  }

  public function getXml() {
    $this->initXml();
    $this->addStates();
    $this->addSteps();
    if (true) {
      $lDom = new DOMDocument("1.0");
      $lDom->preserveWhiteSpace = false;
      $lDom->formatOutput = true;
      $lDom->loadXML($this->mXml->asXML());
      return $lDom->saveXML();
    }
    return $this->mXml->asXML();
  }
  
  protected function addStates() {
    $lRoot = $this->mXml->addChild('states');
    if (empty($this->mStates)) return;
    
    $lFields = array('id', 'name_en', 'name_de', 'desc_en', 'desc_de', 'status', 
        'display', 'img', 'flags');
    foreach ($this->mStates as $lRow) {
      $lNode = $lRoot->addChild('state');
      foreach ($lFields as $lField) {
        $lVal = $lRow[$lField];
        $lNode->addChild($lField, $lVal);
      }
    }
  }
  
  protected function addSteps() {
    $lRoot = $this->mXml->addChild('steps');
    if (empty($this->mSteps)) return;
  
    $lFields = array('id', 'from_id', 'to_id', 'name_en', 'name_de', 'desc_en', 
        'desc_de', 'flags', 'trans');
    foreach ($this->mSteps as $lRow) {
      $lNode = $lRoot->addChild('step');
      foreach ($lFields as $lField) {
        $lVal = $lRow[$lField];
        $lNode->addChild($lField, $lVal);
      }
    }
  }
  
}