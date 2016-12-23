<?php
class CInc_Hom_Menu extends CHtm_Vmenu {

  public function __construct($aKey = NULL) {
    parent::__construct(lan('hom-wel.menu'));

    $this -> setKey($aKey);
    $this -> getMenuItems();
  }
  
  protected function getMenuItems() {
  	$lDashboard = CCor_Cfg::get('hom.wel.dashboard');
  	if ($lDashboard){ // if Dashboard not be defined in Config,redirect to Startseite
  		$this -> addItem('fla', 'index.php?act=hom-fla', lan('hom.fla'));
  	}
  	
  	$this -> addItem('wel', 'index.php?act=hom-wel', lan('hom.account'));
  	$this -> addItem('usr', 'index.php?act=hom-usr', lan('hom.usr.change'));
  	$this -> addItem('pwd', 'index.php?act=hom-pwd', lan('hom.pwd.change'));
  	
  	$lUsr = CCor_Usr::getInstance();
  	if ($lUsr -> canRead('mand')) {
  		$this -> addItem('mand', 'index.php?act=hom-mand', lan('lib.mand.chg'));
  	}
  	
  	$this -> addItem('pref', 'index.php?act=hom-pref', lan('hom.pref'));
  	if ($lUsr -> canRead('app-mass')) {
  		$this -> addItem('mass', 'index.php?act=hom-mass', lan('hom.mass'));
  	}
  	$this -> addItem('fil', 'index.php?act=hom-fil', lan('hom.files'));
  	$this -> addItem('pic', 'index.php?act=hom-pic', lan('hom.pic'));
  	
  	if ($lUsr -> canRead('service.portal.link')) {
  		$this -> addItem('sportal', 'http://5flow.eu/serviceportal/index.php', '5Flow Service Portal', TRUE);
  	}
  	if (0 == MID AND $lUsr -> canRead('copy.mand')) {
  		$this -> addItem('copymand', 'index.php?act=hom-mand.cpy', lan('lib.mand.new'));
  	}  	
  }

}