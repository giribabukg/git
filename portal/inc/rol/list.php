<?php
class CInc_Rol_List extends CHtm_List {

  public function __construct() {
    parent::__construct('rol');
    $this -> setAtt('width', '800px');
    $this -> mTitle = lan('rol.menu');

    $this -> addCtr();
    $this -> addColumn('typ', 'Type', TRUE);
    $this -> addColumn('name', 'Name', TRUE);
    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> addBtn(lan('rol.new'), "go('index.php?act=rol.new')", 'img/ico/16/plus.gif');

    $this -> getPrefs();
    $this -> mIte = new CCor_TblIte('al_rol');
    $this -> mIte -> addCnd('mand='.intval(MID));
  }

}