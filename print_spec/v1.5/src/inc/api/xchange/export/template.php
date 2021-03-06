<?php
class CInc_Api_Xchange_Export_Template extends CCor_Tpl {

  public function __construct($aJob, $aParam = null) {
    $this -> mJob = $aJob;
    $this -> mParam = $aParam;

    $this -> mJobFields = CCor_Res::get('fie');
    $this -> init();
    $this -> loadTemplate();
    $this -> fillTemplate();
  }

  protected function init() {
    // here you can do additional initialization in cust/mand
    // without overwriting the constructor with its parameters
  }

  protected function loadTemplate() {
    $lTplId = $this -> mParam['tpl'];
    $lArr = CCor_Res::extract('id', 'msg', 'tpl');
    if (isset($lArr[$lTplId])) {
      $this -> mDoc = $lArr[$lTplId];
    }
  }

  protected function fillTemplate() {
    $lGSelect = CCor_Res::extract('alias', 'name_'.LAN, 'fie', array('mand' => MID, 'typ' => 'gselect'));
    $lUSelect = CCor_Res::extract('alias', 'name_'.LAN, 'fie', array('mand' => MID, 'typ' => 'uselect'));

    // val.
    $lFpa = $this -> findPatterns('val.');
    foreach ($lFpa as $lPat) {
      $this -> setPat('val.'.$lPat, '');
    }
    $lEnc = $this -> mParam['encode'];
    foreach ($this -> mJob as $lKey => $lVal) {
      if ($lGSelect[$lKey]) {
        $lGroup = new CCor_Anygrp($lVal);
        $lGroupName = $lGroup -> getVal('name');
        $lVal = $this -> encode($lGroupName, $lEnc);
      }
      if ($lUSelect[$lKey]) {
        $lUser = new CCor_Anyusr($lVal);
        $lUserFullname = $lUser -> getFullName();
        $lVal = $this -> encode($lUserFullname, $lEnc);
      }
      $lVal = $this -> encode($lVal, $lEnc);
      $this -> setPat('val.'.$lKey, $lVal);
      $lFunc = 'fill'.$lKey;
      if ($this -> hasMethod($lFunc)) {
        $this -> $lFunc();
      }
    }

    // bez.
    $lMapper = CCor_Res::extract('alias', 'name_'.LAN, 'fie');
    $lPat = $this -> findPatterns('bez.');
    if (!empty($lPat)) {
      foreach ($lPat as $lAli) {
        if (isset($lMapper[$lAli])) {
          $this -> setPat('bez.'.$lAli, htm($lMapper[$lAli]));
        }
      }
    }
  }

  protected function encode($aValue, $aMethod) {
    $lRet = $aValue;
    if ('htmlentities' == $aMethod) {
      $lRet = htmlentities($lRet, ENT_QUOTES, 'UTF-8');
    }
    if ('htmlspecialchars' == $aMethod) {
      $lRet = htmlspecialchars($lRet, ENT_QUOTES, 'UTF-8');
    }
    return $lRet;
  }
}