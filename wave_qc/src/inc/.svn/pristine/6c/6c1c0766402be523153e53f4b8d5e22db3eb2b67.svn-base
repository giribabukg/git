<?php
class CInc_Htm_Mainmenu extends CCor_Ren {

  protected $mItems = array();

  public function __construct($aKey = '') {
    $this->mKey = $aKey;

    $this->mLan = CCor_Lang::getInstance(LAN);
    $this->mUsr = CCor_Usr::getInstance();

    $this->setupItems();
  }

  protected function setupItems() {
    $this->addHome();
    if (0 != MID) {
      $this->addJobMenu();
      $this->addArchiveMenu();
      $this->addRecentMenu();
      $this->addSearchMenu();
      $this->addReportingMenu();
    }
    $this->addUserMenu();
    $this->addOptionMenu();
    $this->addCustomerAdminMenu();
    $this->addTabMenu();
    $this->addSysDocMenu();
  }

  protected function addHome() {
    $this->addItem('hom-wel', $this->mLan->get('hom-wel.menu'), 'mmHome', true);
  }

  protected function addJobMenu() {
    if (CCor_Licenses::get('job-pro') AND $this->mUsr->canRead('job-pro')) {
      $this->addItem('job-pro', $this->mLan->get('job-pro.menu'), 'mmProj');
    }

    if (CCor_Licenses::get('job-sku') AND $this->mUsr->canRead('job-sku')) {
      $this->addItem('job-sku', $this->mLan->get('job-sku.menu'), 'mmSku');
    }

    $lMnu_Akt = CCor_Cfg::get('menu-aktivejobs');
    $lMnuAkt = array();
    foreach ($lMnu_Akt as $lK => $lRow) {
      if ($this->mUsr->canRead($lRow)) {
        $lMnuAkt[] = $lRow;
      }
    }
    $lCountMnuAkt = count($lMnuAkt);
    if (1 == $lCountMnuAkt){
      if ($this->mKey == 'job') {
        $this->mKey = $lMnuAkt[0];
      }
      $this->addItem($lMnuAkt[0], $this->mLan->get($lMnuAkt[0].'.menu'), 'mmJobs', true);
    } elseif (1 < $lCountMnuAkt) {
      $lMen = $this->addMenu('job', $this->mLan->get('job.menu'), 'mmJobs');
      foreach ($lMnuAkt as $lRow)  {
        $lMen->addItem('index.php?act='.$lRow, $this->mLan->get($lRow.'.menu'), '<i class="ico-16-'.LAN.' ico-16-'.LAN.'-'.$lRow.'"></i>');
      }
    }
  }

  protected function addArchiveMenu() {
    // Menu Archiv
    // Falls nur ein Job-Kategorie gibt, wird als Item  dargestellt.
    // Ask for archive read right
    if ($this->mUsr->canRead('arc')) {
      $lMnu_Arc = CCor_Cfg::get('menu-archivjobs');
      $lMnuArc = array();
      foreach ($lMnu_Arc as $lK => $lRow) {
        if ($this->mUsr->canRead('job-'.$lRow) && $lRow !== 'all') {
          $lMnuArc[] = $lRow;
        }
      }
      $lCountMnuArc = count($lMnuArc);
      if (1 == $lCountMnuArc){
        if ($this->mUsr->canRead('job-'.$lMnuArc[0])) {
          $this->addItem('arc-'.$lMnuArc[0], $this->mLan->get('arc.menu'), 'mmArc', true);
        }
      } elseif (1 < $lCountMnuArc) {
        $lMen = $this->addMenu('arc', $this->mLan->get('arc.menu'), 'mmArc');
        if ($this->mUsr->canRead('job-all')) {
          $lMen->addItem('index.php?act=arc-all', $this->mLan->get('job-all.menu'), '<i class="ico-16-'.LAN.' ico-16-'.LAN.'-job-all"></i>');
        }
        foreach ($lMnuArc as $lRow)  {
          if ($this->mUsr->canRead('job-'.$lRow)) {
            $lMen->addItem('index.php?act=arc-'.$lRow, $this->mLan->get('job-'.$lRow.'.menu'), '<i class="ico-16-'.LAN.' ico-16-'.LAN.'-job-'.$lRow.'"></i>');
          }
        }
      }
    }
  }

  protected function addRecentMenu() {
    $lArr = array();
    $lQry = $this->mUsr->getRecentJobs();
    foreach ($lQry as $lRow) {
      $lArr[] = $lRow;
    }
    if (!empty($lArr)) {
      $lMen = $this->addMenu('rec', $this->mLan->get('lib.recent'), 'mmRecent');
      foreach ($lArr as $lRow) {
        $lSrc = $lRow['src'];
        $lJid = $lRow['jobid'];
        $lJnr = jid($lJid, TRUE);
        if ('pro' == $lSrc) {
          $lJnr = '';
        }
        if (substr($lSrc,0,4) == 'arc-') {
          $lImg = substr($lSrc,4);
          $lMen->addItem('index.php?act='.$lSrc.'.edt&amp;jobid='.$lJid, cat($lJnr, $lRow['keyword']), 'ico/16/'.LAN.'/job-'.$lImg.'.gif');
        } else {
          $lMen->addItem('index.php?act=job-'.$lSrc.'.edt&amp;jobid='.$lJid, cat($lJnr, $lRow['keyword']), 'ico/16/'.LAN.'/job-'.$lSrc.'.gif');
        }
      }
    }
  }

  protected function addSearchMenu() {
    if ($this->mUsr->canRead('ser')) {
      $this->addItem('job-ser',  $this->mLan->get('job-ser.menu'), 'mmSearch', 'ser');
    }
  }

  protected function addReportingMenu() {
    if ($this->mUsr->canRead('rep')) {
      $this->addItem('rep', lan('rep.menu'), 'mmReport');
    }
  }

  protected function addUserMenu() {
    if ($this->mUsr->canRead('usr') || $this->mUsr->canRead('gru') || $this->mUsr->canRead('rol') || $this->mUsr->canRead('prf') || $this->mUsr->canRead('rig')) {
      $lMen = $this->addMenu('usr', $this->mLan->get('usr.menu'), 'mmUsr');

      if ($this->mUsr->canRead('usr')) {
      	$lMen->addItem('index.php?act=usr', $this->mLan->get('usr.menu'),   '<i class="ico-16-'.LAN.' ico-16-'.LAN.'-usr"></i>');
      }
      if ($this->mUsr->canRead('gru')) {
        $lMen->addItem('index.php?act=gru', $this->mLan->get('gru.menu'), '<i class="ico-16-'.LAN.' ico-16-'.LAN.'-gru"></i>');
      }
      if ($this->mUsr->canRead('rol')) {
        $lMen->addItem('index.php?act=rol', $this->mLan->get('rol.menu'));
      }
      if ($this->mUsr->canRead('prf')) {
        $lMen->addItem('index.php?act=prf', $this->mLan->get('prf.menu'));
      }
      if ($this->mUsr->canRead('rig')) {
        $lMen->addItem('index.php?act=rig', $this->mLan->get('rig'));
      }
    }
  }

  protected function addOptionMenu() {
    if ($this->mUsr->canRead('opt')) {
      $lMen = $this->addMenu('opt', $this->mLan->get('opt.menu'), 'mmOption');
      if ($this->mUsr->canRead('fie')) {
        $lMen->addItem('index.php?act=fie', $this->mLan->get('fie.menu'), '<i class="ico-w16 ico-w16-fie"></i>');
      }
      if (CCor_Cfg::get('validate.available') && $this->mUsr->canRead('fie-validate')) {
        $lMen->addItem('index.php?act=fie-validate', $this->mLan->get('fie-validate.menu'), '<i class="ico-w16 ico-w16-fie"></i>');
      }
      if ($this->mUsr->canRead('fie-ref')) {
        $lMen->addItem('index.php?act=fie-ref', 'MOP Field Reference');
      }
      if ($this->mUsr->canRead('htb')) {
        $lMen->addItem('index.php?act=fie-learn', lan('fie-learn.menu'));
        $lMen->addItem('index.php?act=htb', $this->mLan->get('htb.menu'));
        $lMen->addItem('index.php?act=pck', $this->mLan->get('pck.menu'));
        #$lMen->addItem('index.php?act=htb', 'Image Lists');
      }
      if ($this->mUsr->canRead('crp')) {
        $lMen->addItem('index.php?act=jfl', $this->mLan->get('jfl.menu'));
      }
      if (CCor_Licenses::get('mig')) {
        $lMen->addItem('index.php?act=mig', $this->mLan->get('mig.menu'));
      }
      if ($this->mUsr->canRead('wiz')) {
        $lMen->addItem('index.php?act=wiz', $this->mLan->get('wiz.menu'));
      }
      if ($this->mUsr->canRead('crp')) {
        $lMen->addItem('index.php?act=crp', $this->mLan->get('crp.menu'), '<i class="ico-his ico-his-1"></i>');
      }
      if ($this->mUsr->canRead('fla')) {
        $lMen->addItem('index.php?act=fla', $this->mLan->get('flag.menu'), '<i class="ico-his ico-his-11"></i>');
      }
      if ($this->mUsr->canRead('apl-types')) {
        $lMen->addItem('index.php?act=apl-types', $this->mLan->get('apl-types.menu'));
      }
      if ($this->mUsr->canRead('eve-type')) {
        $lMen->addItem('index.php?act=eve-type', $this->mLan->get('eve-type.menu'));
      }
      if ($this->mUsr -> canRead('cnd')) {
        $lMen -> addItem('index.php?act=cnd', $this->mLan -> get('cnd.menu'));
      }
      if ($this->mUsr->canRead('eve')) {
        $lMen->addItem('index.php?act=eve', $this->mLan->get('eve.menu'));
      }
      if ($this->mUsr->canRead('conditions')) {
        $lMen->addItem('index.php?act=conditions', $this->mLan->get('conditions.menu'));
      }
      if ($this->mUsr->canRead('tpl')) {
        $lMen->addItem('index.php?act=tpl', $this->mLan->get('tpl.menu'));
      }
      if ($this->mUsr->canRead('dbg')) {
        $lMen->addItem('index.php?act=devtools', 'Devtools', '<i class="ico-w16 ico-w16-mt-64"></i>');
      }
      if ($this->mUsr->canRead('log')) {
        $lMen->addItem('index.php?act=sys-mail', lan('sys-mail.menu'), '<i class="ico-w16 ico-w16-email"></i>');
      }
      if ($this->mUsr->canRead('log')) {
        $lMen->addItem('index.php?act=sys-log',  lan('sys-log.menu'), '<i class="ico-w16 ico-w16-log"></i>');
      }
      if ($this->mUsr->canRead('sys-msg')) {
        $lMen->addItem('index.php?act=sys-msg',  lan('sys-msg.menu'), '<i class="ico-w16 ico-w16-ml-1"></i>');
      }
      if ($this->mUsr->canRead('svn')) {
        $lMen->addItem('index.php?act=svn',  'SVN');
      }
      if ($this->mUsr->canRead('sys-svc')) {
        $lMen->addItem('index.php?act=sys-svc',  lan('sys-svc.menu'));
      }
      if ($this->mUsr->canRead('xchange')) {
        $lMen->addItem('index.php?act=xchange',  lan('xchange.menu'));
      }
      if ($this->mUsr->canRead('fie-map')) {
        $lMen->addItem('index.php?act=fie-map', $this->mLan->get('fie-map.menu'));
      }
      if ($this->mUsr->canRead('sys-lang')) {
        $lMen->addItem('index.php?act=sys-lang', lan('sys-lang.menu'));
      }
      if ($this->mUsr -> canRead('lang') AND 0 == MID) {
        $lMen -> addItem('index.php?act=lan', lan('lang.menu'));
      }
      if ($this->mUsr -> canRead('mig-job')) {
        $lMen->addItem('index.php?act=mig-job', lan('sys-mig.menu'));
      }
      if ($this->mUsr->canRead('tab')) {
        $lMen->addItem('index.php?act=tab', $this->mLan->get('tab.menu'));
      }
      if ($this->mUsr->canRead('mnd')) {
        $lMen->addItem('index.php?act=mnd', $this->mLan->get('mnd.menu'));
      }
      if ($this->mUsr->canRead('wec-pi')) {
        $lMen->addItem('index.php?act=wec', $this->mLan->get('wec-pi.menu'));
      }
      if ($this->mUsr->canRead('job-fil-view')) {
        $lMen->addItem('index.php?act=job-fil-view', $this->mLan->get('job-fil-view.menu'));
      }
      if ($this->mUsr->canRead('job-fil-flink')) {
        $lMen->addItem('index.php?act=job-fil-flink', $this->mLan->get('job-fil-flink.menu'));
      }
      if ($this->mUsr->canEdit('job.multiple-edit')) {
        $lMen->addItem('index.php?act=job-multi', $this->mLan->get('job.multiple-edit'));
      }
    }
  }

  protected function addCustomerAdminMenu() {
    if (
      $this -> mUsr -> canRead('usg')
      OR $this -> mUsr -> canRead('htg')
      OR $this -> mUsr -> canRead('pck')
      OR $this -> mUsr -> canRead('content')
      OR $this -> mUsr -> canRead('chk')
    ) {
      $lMen = $this -> addMenu('htg', $this -> mLan -> get('lib.data'), 'mmData');

      if ($this -> mUsr -> canRead('usg')) {
        $lMen -> addItem('index.php?act=usg', $this -> mLan -> get('usr.menu'), '<i class="ico-16-'.LAN.' ico-16-'.LAN.'-usr"></i>');
      }

      if ($this -> mUsr -> canRead('htg')) {
        $lMen->addItem('index.php?act=htg', $this -> mLan -> get('htb.menu'));
      }

      if ($this -> mUsr -> canRead('pck')) {
        $lMen->addItem('index.php?act=pck', $this -> mLan -> get('pck.menu'));
      }

      if ($this -> mUsr -> canRead('content')) {
    	$lMen->addItem('index.php?act=content', $this -> mLan -> get('content.text.menu'));
      }

      if ($this -> mUsr -> canRead('chk')) {
        $lMen -> addItem('index.php?act=chk', $this -> mLan -> get('chk.menu'), '<i class="ico-16-'.LAN.' ico-16-'.LAN.'-check-hi"></i>');
      }
    }
  }

  protected function addAssetMenu() {
    if ($this->mUsr->canRead('ast')) {
      $this->addItem('hom-cc',  $this->mLan->get('lib.cc'), 'mmAssets', 'cc');
    }
  }

  protected function addTabMenu() {
    $lType = new CCor_Qry('SELECT code FROM al_tab_slave WHERE mand IN (0, '.MID.') AND type="mainmenu" ORDER BY id;');
    foreach ($lType as $lKey => $lValue) {
      if ($this->mUsr->canRead('tab.mainmenu.'.$lValue['code'])) {
        $this->addItem('hom-tab-mainmenu&code='.$lValue['code'], lan('tab.mainmenu.'.$lValue['code']), 'mm'.ucfirst($lValue['code']), TRUE);
      }
    }
  }

  protected function addSysDocMenu() {
  	if ($this->mUsr->canRead('sys-doc')) {
  		$this->addItem('sys-doc',  $this->mLan->get('sys-doc.menu'), 'mmSystem Doc', 'sys-doc');
  	}
  }

  public function setKey($aKey) {
    $this->mKey = $aKey;
  }

  public function addItem($aUrl, $aCaption, $aClass='', $aPriv = NULL) {
    switch ($aPriv) {
      case TRUE :
        $lCan = TRUE;
        break;
      case NULL :
        $lCan = $this->mUsr->canRead($aUrl);
        break;
      default:
        $lCan = $this->mUsr->canRead($aPriv);
      break;
    }
    if ($lCan) {
      $this->mItems[$aUrl] = array('cap' => $aCaption, 'class' => $aClass);
    }
  }

  public function & addMenu($aKey, $aCaption, $aClass='') {
    $lMen = new CHtm_Menu($aCaption, $aClass);
    $this->mItems[$aKey] = & $lMen;
    return $lMen;
  }

  protected function getBackButton() {
    $lRet = '';
    $lSys = CCor_Sys::getInstance();
    $lHis = $lSys->get('his', array());
    if (!empty($lHis)) {
      $lCnt = count($lHis);
      if ($lCnt > 1) { // do not count current url
        $lRet.= '<td class="mmBack mmLo" onclick="go(\'index.php?act=hom-wel.back\')">';
        $lRet.= htm(lan('lib.back'));
        $lRet.= '</td>'.LF;
      }
    }
    return $lRet;
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= $this->getComment('start');
    $lRet.= '<table cellpadding="9" cellspacing="0" border="0" id="pgMenu" class="pgMenu" width="100%"><tr>'.LF;
    $lRet.= $this->getItemContent();
    $lRet.= $this->getPadding();
    $lRet.= $this->getAjaxImage();
    $lRet.= $this->getBackButton();
    $lRet.= $this->getLogoutButton();
    $lRet.= '</tr></table>'.LF;
    $lRet.= $this->getComment('end');
    return $lRet;
  }

  protected function getItemContent() {
    $lRet = '';
	$lSub = '&nbsp;';
	$lSubMenu = false;
    $lAvailSrc = CCor_Cfg::get('all-jobs_ALINK');
    foreach ($this->mItems as $lUrl => $lCap) {
      if (is_array($lCap)) {
        $lCls = ($lUrl == $this->mKey ? 'mmHi ' : 'mmLo ') . $lCap['class'];
        if($lUrl == $this->mKey){
          if(strpos($this->mKey, 'job') !== FALSE || strpos($this->mKey, 'arc') !== FALSE){
            $lSrc = substr($_GET['act'], 4,3);
            if(!in_array($lSrc, $lAvailSrc)){
              $lSrc = $_GET['src'];
            }
            $lColour = (THEME === 'default' ? '' : CApp_Crpimage::getColourForSrc($lSrc));
            $lCls .= ' '.$lColour;
          }
        }
        $lRet.= '<td class="'.$lCls.'" title="'.$lCap['cap'].'" onclick="go(\'index.php?act='.$lUrl.'\')">';
        $lRet.= htm($lCap['cap']);
        $lRet.= '</td>'.LF;
      } else {
        $lNum = getNum('d');

        if($lUrl == $this->mKey){
          $lSub = $lCap->getSubMenu();
          $lCls = 'mmHi '.$lCap->mClass;
          if(strpos($this->mKey, 'job') !== FALSE || strpos($this->mKey, 'arc') !== FALSE){
            $lSrc = substr($_GET['act'], 4,3);
            if(!in_array($lSrc, $lAvailSrc)){
              $lSrc = $_GET['src'];
            }
            $lColour = (THEME === 'default' ? '' : CApp_Crpimage::getColourForSrc($lSrc));
            $lCls .= ' '.$lColour;
          }
          $lSubMenu = true;
        } else {
          $lCls = 'mmLo '.$lCap->mClass;
        }

        $lRet.= '<td class="'.$lCls.'" id="'.$lCap->mLnkId.'" title="'.lan($lUrl.".menu").'" onclick="Flow.Std.popMain(\''.$lCap->mDivId.'\',null,\''.$lNum.'\')">';
        $lRet.= htm($lCap->mCaption);
        $lRet.= '<div style="position:relative; top:9px; left:-8px;" id="'.$lNum.'">';
        $lRet.= (THEME === 'default' ? $lCap->getMenuDiv() : $lCap->getMenuWaveDiv());
        $lRet.= '</div>';
        $lRet.= '</td>'.LF;
      }
    }

    $lPg = CHtm_Page::getInstance();
    $lPg->setPat('pg.submenu', $lSub);

    return $lRet;
  }

  protected function getPadding() {
    return '<td width="100%" class="mmPad">&nbsp;</td>';
  }

  protected function getAjaxImage() {
    $lRet = '';
    $lRet.= '<td class="mmLo w16 p0">';
    $lRet.= '<img id="pag_ajx" src="img/d.gif" width="16" height="16" alt="" />';
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getLogoutButton() {
    $lRet = '';
    $lRet.= '<td class="mmLogout mmLo" style="border-right:0" onclick="go(\'index.php?act=log.out\')">';
    $lRet.= 'Logout</td>'.LF;
    return $lRet;
  }

}
