<?php
class CInc_Wec_Menu extends CHtm_Vmenu {

  public function __construct($aKey) {
    parent::__construct(lan('wec-pi'));

    $this -> setKey($aKey);

    $this -> addItem('fetch',     'index.php?act=wec.fetch',     lan('wec-pi.fetch')); // Download service
    $this -> addItem('sync',      'index.php?act=wec.sync',      lan('wec-pi.sync'));  // Sync service
    $this -> addItem('image',     'index.php?act=wec.image',     lan('wec-pi.image')); // Images
    $this -> addItem('thumbnail', 'index.php?act=wec.thumbnail', lan('wec-pi.thumbnail')); // Thumbnails
  }

}