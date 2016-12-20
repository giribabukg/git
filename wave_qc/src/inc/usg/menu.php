<?php
class CInc_Usg_Menu extends CHtm_Vmenu {

  public function __construct($aId, $aKey, $aMod) {
    $this -> mMod = $aMod;
    $this -> m2Act = $this -> mMod;
    $this -> mId = intval($aId);

    $lNam = CCor_Qry::getStr('SELECT lastname FROM al_usr WHERE mand IN(0,'.MID.') AND id='.$this -> mId);
    parent::__construct($lNam);
    $lUsr = CCor_Usr::getInstance();

    $this -> setKey($aKey);
    $this -> addItem('dat', 'index.php?act='.$this -> m2Act.'.edt&amp;id='.$this -> mId, lan('lib.data'));
    $this -> addItem('mem', 'index.php?act='.$this -> m2Act.'-mem&amp;id='.$this -> mId, lan('usr-mem.menu'));
    $this -> addItem('his', 'index.php?act='.$this -> m2Act.'-his&amp;id='.$this -> mId, lan('lib.history'));

    if ($lUsr -> canRead('usg-info')) {
      $this -> addItem('info', 'index.php?act=usg-info&amp;id='.$this -> mId, lan('usg-info.menu'));
    }
    if ($lUsr -> canRead('usg-wecusr')) {
      $this -> addItem('wecu', 'index.php?act=usg.wecusr&amp;id='.$this -> mId, lan('usg-wecusr.menu'));
    }

    $this -> addItem('opt', 'index.php?act=usg-opt&amp;id='.$this -> mId, lan('lib.opt'));
    $this -> addItem('bck', 'index.php?act='.$this -> m2Act, lan('lib.back'));
  }

}