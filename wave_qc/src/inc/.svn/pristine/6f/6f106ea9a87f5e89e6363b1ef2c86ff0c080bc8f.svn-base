<?php
class CInc_Cnd_Mod extends CCor_Mod_Table {

  /**
   * Constructor
   *
   * @access public
   */
  public function __construct() {
    parent::__construct('al_cnd_master');

    $this -> addField(fie('name'));
    $this -> addField(fie('flags'));
  }

  /**
   * Before post
   *
   * @access protected
   */
  protected function beforePost($aNew = FALSE) {
    if ($aNew) {
      $this -> setVal('mand', MID);
      $this -> setVal('aliased', 1);
      $this -> setVal('natived', 1);
    }
  }

}