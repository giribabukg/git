<?php
class CInc_Eve_Act_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_eve_act', 'id,mand');
    $this -> addField(fie('id'));
    $this -> addField(fie('mand'));
    $this -> addField(fie('eve_id'));
    $this -> addField(fie('typ'));
    $this -> addField(fie('pos'));
    $this -> addField(fie('param'));
    $this -> addField(fie('dur'));
    $this -> addField(fie('cond_id'));

    $this -> mReg = new CApp_Event_Action_Registry();
  }

  public function getPost(ICor_Req $aReq, $aOld = TRUE) {
    parent::getPost($aReq, $aOld);
    $this ->dbg('XXXXXXXXXXXXX');
    $lPar = array();
    $lTyp = $this -> getVal('typ');
    $lArr = $this -> mReg -> getParamDefs($lTyp);
    if (!empty($lArr)) {
      foreach ($lArr as $lDef) {
        $lAlias = $lDef['alias'];
        $lValue = $this -> getReqVal($lAlias);
        $lPar[$lAlias] = $lValue;
      }
      $lVal = serialize($lPar);
      $this -> setVal('param', $lVal);
      $this -> dbg('PARAM is now '.$lVal);
      if ($aOld) {
        $lOld = $this -> getOld('param');
        if ($lOld != $lVal) {
          $this -> mUpd['param'] = $lVal;
        }
      }
    }
    $this -> dump($this -> mUpd, 'UPDATE');
  }

  public static function clearCache() {
    $lCkey = 'cor_res_action_'.MID;
    CCor_Cache::clearStatic($lCkey);
  }

  protected function afterChange() {
    self::clearCache();
  }



}