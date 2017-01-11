<?php
class CInc_Wec_Menu extends CHtm_Vmenu {

  public function __construct($aKey) {
    parent::__construct(lan('lib.preview'));

    $this -> setKey($aKey);

    $this -> addItem('preview', 'index.php?act=wec.preview', lan('lib.preview'));
  }
}