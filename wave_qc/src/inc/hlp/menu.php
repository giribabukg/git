<?php
class CInc_Hlp_Menu extends CHtm_Vmenu {

  public function __construct($aCaption, $aActive) {
    $lUsr = CCor_Usr::getInstance();
    parent::__construct($aCaption, $aActive);
    $this -> addItem('idx', 'index.php?act=hlp',      lan('lib.toc'));
    $this -> addItem('ser', 'index.php?act=hlp-ser',  lan('lib.search'));
    $this -> addItem('fav', 'index.php?act=hlp-fav',  lan('job.bm.menu'));
    $this -> addItem('hlp', 'index.php?act=hlp-hlp',  lan('lib.use.hlp.sys'));
  }

}