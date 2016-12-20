<?php
class CInc_Svc_Xchange extends CSvc_Base {

  protected function doExecute() {
    $lMod = $this -> getPar('handler', '');
    if (!class_exists($lMod)) {
      $lMsg = 'Xchange: Handler "'.$lMod.'" not defined';
      CSvc_Base::addLog($lMsg);
      $this->msg($lMsg, mtApi, mlError);
      return false;
    }
    $lObj = new $lMod($this -> mParam);
    $lRet = $lObj -> execute();
    return $lRet;
  }

}