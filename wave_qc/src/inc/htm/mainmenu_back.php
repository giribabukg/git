<?php
class CInc_Htm_Mainmenu extends CCor_Ren {

  protected $mItems = array();

  public function __construct($aKey = '') {
    $lUsr = CCor_Usr::getInstance();
    $this -> mKey = $aKey;
    $lLan = CCor_Lang::getInstance(LAN);

    // Menu "Projekte"
    $this -> dbg('lang '.LAN);
    $this -> addItem('hom-wel', $lLan -> get('hom-wel.menu'), true);

    if (0 != MID) { // Zugriff zu Mandant XY

      if (CCor_Licenses::get('job-pro') AND $lUsr -> canRead('job-pro')) {
        $this -> addItem('job-pro', $lLan -> get('job-pro.menu'));
      }

      if (CCor_Licenses::get('job-sku') AND $lUsr -> canRead('job-sku')) {
        $this -> addItem('job-sku', $lLan -> get('job-sku.menu'));
      }

      // Menu "Aktive Jobs"
      $lMnuAkt = CCor_Cfg::get('menu-aktivejobs');
      // Falls nur ein Job-Kategorie gibt, wird als Item  dargestellt.
      if (count($lMnuAkt) == 1){
        $this -> addItem($lMnuAkt[0], $lLan->get($lMnuAkt[0].'.menu'), true);
      }else {
        // When es mehrere Job-Kategories gibt, wird als Menu angezeigt.
        if ($lUsr -> canRead($lMnuAkt[0])) {
          $lMen = $this -> addMenu('job', $lLan->get('job.menu'));
          foreach ($lMnuAkt as $lRow)  {
            if ($lUsr -> canRead($lRow)) {
              $lMen -> addItem('index.php?act='.$lRow, $lLan->get($lRow.'.menu'), 'ico/16/'.LAN.'/'.$lRow.'.gif');
            }
          }
        }
      }

      // Menu Archiv
      // bisher war die Anzeige vom Archiv an kein Recht gekoppelt.
      // Falls nur ein Job-Kategorie gibt, wird als Item  dargestellt.
      $lMnuArv = CCor_Cfg::get('menu-archivjobs');
      if (count($lMnuArv) == 1){
        $this -> addItem('arc-'.$lMnuArv[0], $lLan->get('arc.menu'), true);
      }else {
        $lMen = $this -> addMenu('arc', $lLan->get('arc.menu'));
        foreach ($lMnuArv as $lRow)  {
          $lMen -> addItem('index.php?act=arc-'.$lRow, $lLan->get('job-'.$lRow.'.menu'), 'ico/16/'.LAN.'/job-'.$lRow.'.gif');
        }
      }

      $lArr = array();
      $lQry = $lUsr -> getRecentJobs();
      foreach ($lQry as $lRow) {
        $lArr[] = $lRow;
      }
      if (!empty($lArr)) {
        $lMen = $this -> addMenu('rec', $lLan -> get('lib.recent'));
        foreach ($lArr as $lRow) {
          $lSrc = $lRow['src'];
          $lJid = $lRow['jobid'];
          $lJnr = jid($lJid, TRUE);
          if ('pro' == $lSrc) {
            $lJnr = '';
          }
          if (substr($lSrc,0,4) == 'arc-') {
            $lImg = substr($lSrc,4);
            $lMen -> addItem('index.php?act='.$lSrc.'.edt&amp;jobid='.$lJid, cat($lJnr, $lRow['keyword']), 'ico/16/'.LAN.'/job-'.$lImg.'.gif');
          } else {
            $lMen -> addItem('index.php?act=job-'.$lSrc.'.edt&amp;jobid='.$lJid, cat($lJnr, $lRow['keyword']), 'ico/16/'.LAN.'/job-'.$lSrc.'.gif');
          }
        }
      }

      if ($lUsr -> canRead('ser')) {
        $this -> addItem('job-ser',  $lLan -> get('job-ser.menu'), 'ser');
      }

      if ($lUsr -> canRead('rep')) {
        $this -> addItem('rep', lan('rep.menu'));
      }

    }//end_if (0 !== MID) // Zugriff zu Mandant XY

    if ($lUsr -> canRead('usr')) {
      $lMen = $this -> addMenu('usr', $lLan -> get('usr.menu'));
      $lMen -> addItem('index.php?act=usr', $lLan -> get('usr.menu'),   'ico/16/'.LAN.'/usr.gif');

      if ($lUsr -> canRead('gru')) {
        $lMen -> addItem('index.php?act=gru', $lLan -> get('gru.menu'), 'ico/16/'.LAN.'/gru.gif');
      }
      if ($lUsr -> canRead('rol')) {
        $lMen -> addItem('index.php?act=rol', $lLan -> get('rol.menu'));
      }
      if ($lUsr -> canRead('prf')) {
        $lMen -> addItem('index.php?act=prf', $lLan -> get('prf.menu'));
      }
      if ($lUsr -> canRead('rig')) {
        $lMen -> addItem('index.php?act=rig', $lLan -> get('rig'));
      }

    }

    if ($lUsr -> canRead('opt')) {
      $lMen = $this -> addMenu('opt', $lLan -> get('opt.menu'));
      if ($lUsr -> canRead('fie'))
      $lMen -> addItem('index.php?act=fie', $lLan -> get('fie.menu'), 'ico/16/'.LAN.'/fie.gif');
      if ($lUsr -> canRead('htb')) {
        $lMen -> addItem('index.php?act=fie-learn', lan('fie-learn.menu'));
        $lMen -> addItem('index.php?act=htb', $lLan -> get('htb.menu'));
        $lMen -> addItem('index.php?act=pck', $lLan -> get('pck.menu'));
        #$lMen -> addItem('index.php?act=htb', 'Image Lists');
      }
      if ($lUsr -> canRead('crp')) {
        $lMen -> addItem('index.php?act=jfl', $lLan -> get('jfl.menu'));
      }
      if (CCor_Licenses::get('mig')) {
        $lMen -> addItem('index.php?act=mig', $lLan -> get('mig.menu'));
      }
      if ($lUsr -> canRead('wiz')) {
        $lMen -> addItem('index.php?act=wiz', $lLan -> get('wiz.menu'));
      }
      if ($lUsr -> canRead('crp')) {
        $lMen -> addItem('index.php?act=crp', $lLan -> get('crp.menu'), 'his/1.gif');
      }
      if ($lUsr -> canRead('eve')) {
        $lMen -> addItem('index.php?act=eve', $lLan -> get('eve.menu'));
      }
      if ($lUsr -> canRead('tpl')) {
        $lMen -> addItem('index.php?act=tpl', $lLan -> get('tpl.menu'));
      }
      if ($lUsr -> canRead('log')) {
        $lMen -> addItem('index.php?act=sys-mail', lan('sys-mail.menu'), 'ico/16/'.LAN.'/email.gif');
      }
      if ($lUsr -> canRead('log')) {
        $lMen -> addItem('index.php?act=sys-log',  lan('sys-log.menu'), 'ico/16/'.LAN.'/log.gif');
      }
      if ($lUsr -> canRead('sys-svc')) {
        $lMen -> addItem('index.php?act=sys-svc',  lan('sys-svc.menu'));
      }
      if ($lUsr -> canRead('sys-lang')) {
        $lMen -> addItem('index.php?act=sys-lang', lan('sys-lang.menu'));
      }
      if ($lUsr -> canRead('mig-job')) {
        $lMen -> addItem('index.php?act=mig-job', lan('sys-mig.menu'));
      }
    }

    if ($lUsr -> canRead('htg') OR $lUsr -> canRead('usg')) {
      $lMen = $this -> addMenu('htg', $lLan -> get('lib.data'));
      if ($lUsr -> canRead('usg'))
        $lMen -> addItem('index.php?act=usg', $lLan -> get('usr.menu'), 'ico/16/'.LAN.'/usr.gif');
      if ($lUsr -> canRead('htg'))
        $lMen -> addItem('index.php?act=htg', $lLan -> get('htb.menu'));
    }

    if ($lUsr -> canRead('ast')) {
      $this -> addItem('hom-cc',  $lLan -> get('lib.cc'), 'cc');
    }
  }

  public function setKey($aKey) {
    $this -> mKey = $aKey;
  }

  public function addItem($aUrl, $aCaption, $aPriv = NULL) {
    $lUsr = CCor_Usr::getInstance();
    switch ($aPriv) {
      case TRUE :
        $lCan = TRUE;
        break;
      case NULL :
        $lCan = $lUsr -> canRead($aUrl);
        break;
      default:
        $lCan = $lUsr -> canRead($aPriv);
        break;
    }
    if ($lCan) {
      $this -> mItems[$aUrl] = $aCaption;
    }
  }

  public function & addMenu($aKey, $aCaption) {
    $lMen = new CHtm_Menu($aCaption);
    $this -> mItems[$aKey] = & $lMen;
    return $lMen;
  }

  protected function getBackButton() {
    $lRet = '';
    $lSys = CCor_Sys::getInstance();
    $lHis = $lSys -> get('his', array());
    if (!empty($lHis)) {
      $lCnt = count($lHis);
      if ($lCnt > 1) { // do not count current url
        $lRet.= '<td class="mmLo" onclick="go(\'index.php?act=hom-wel.back\')">';
        $lRet.= htm(lan('lib.back'));
        $lRet.= '</td>'.LF;
      }
    }
    return $lRet;
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= $this -> getComment('start');
    $lRet.= '<table cellpadding="9" cellspacing="0" border="0" id="pgMenu" class="pgMenu" width="100%"><tr>'.LF;
    foreach ($this -> mItems as $lUrl => $lCap) {
      if (is_string($lCap)) {
        $lCls = ($lUrl == $this -> mKey) ? 'mmHi' : 'mmLo';
        $lRet.= '<td class="'.$lCls.'" onclick="go(\'index.php?act='.$lUrl.'\')">';
        $lRet.= htm($lCap);
        $lRet.= '</td>'.LF;
      } else {
        $lNum = getNum('d');
        $lCls = ($lUrl == $this -> mKey) ? 'mmHi' : 'mmLo';
        $lRet.= '<td class="'.$lCls.'" id="'.$lCap -> mLnkId.'" onclick="popMain(\''.$lCap -> mDivId.'\',null,\''.$lNum.'\')">';
        $lRet.= htm($lCap -> mCaption);
        $lRet.= '<div style="position:relative; top:9px; left:-8px;" id="'.$lNum.'">';
        $lRet.= $lCap -> getMenuDiv();
        $lRet.= '</div>';
        $lRet.= '</td>';
      }
    }
    $lRet.= '<td width="100%" class="mmPad">&nbsp;</td>';
    $lRet.= '<td class="mmLo w16 p0"><img id="pag_ajx" src="img/d.gif" width="16" height="16" alt="" /></td>';

    $lRet.= $this -> getBackButton();

    $lRet.= '<td class="mmLo" style="border-right:0" onclick="go(\'index.php?act=log.out\')">';
    $lRet.= 'Logout</td>'.LF;
    $lRet.= '</tr></table>';
    $lRet.= $this -> getComment('end');
    return $lRet;
  }
}