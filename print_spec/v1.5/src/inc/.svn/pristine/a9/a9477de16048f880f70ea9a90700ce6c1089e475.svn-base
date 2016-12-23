<?php
class CInc_Sys_Svc_Form_Edit extends CSys_Svc_Form_Base {
  
  public function __construct($aId) {
    parent::__construct('sys-svc.sedt', 'Edit Service');
    $this -> mId = intval($aId);
    $this -> setParam('val[id]', $this -> mId);
    $this -> setParam('old[id]', $this -> mId);
    $this -> load($this->mId);
  }
  
}