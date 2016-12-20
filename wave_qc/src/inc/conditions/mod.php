<?php
class CInc_Conditions_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_cond');

    $this -> addField(fie('id'));
    $this -> addField(fie('mand'));
    $this -> addField(fie('type'));
    $this -> addField(fie('name'));
    $this -> addField(fie('params'));
  }

  protected function beforePost($aNew = FALSE) {
    if ($aNew) {
      $this -> setVal('mand', MID);
    }
  }

  public function setParams($aParams) {
    $lType = $this -> getVal('type', 'simple');

    $lReg = new CInc_App_Condition_Registry();
    $lObj = $lReg -> factory($lType);
    $lParams = $lObj -> requestToArray($aParams);
    $this -> forceVal('params', serialize($lParams));
  }

  public static function clearCache() {
    $lKey = 'cor_res_cond_'.MID;
    CCor_Cache::clearStatic($lKey);
  }

  protected function afterChange() {
    self::clearCache();
  }
}