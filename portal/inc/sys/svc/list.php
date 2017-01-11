<?php
class CInc_Sys_Svc_List extends CHtm_List {

  public function __construct() {
    parent::__construct('sys-svc');
    $this -> setAtt('width', '900');
    $this -> mTitle = 'Services';

    $lUsr = CCor_Usr::getInstance();

    $this -> addCtr();
    $this -> addColumn('active', '', false, array('width' => '16'));
    $this -> addColumn('pos', '', true, array('width' => '16'));
    $this -> addColumn('act', 'Code', false, array('width' => '40'));
    $this -> addColumn('name', 'Name', true, array('width' => '80%'));

    $this -> addColumn('actions', 'Actions', false, array('width' => '50'));

    if ($lUsr->getPref($this->mPrf.'.show.schedule', false)) {
      $this -> addColumn('schedule', 'Schedule', true, array('width' => '20%'));
    }
    if ($lUsr->getPref($this->mPrf.'.show.last_progress', true)) {
      $this -> addColumn('last_progress', 'Last Progress', true, array('width' => '50'));
    }
    if ($lUsr->getPref($this->mPrf.'.show.last_action', true)) {
      $this -> addColumn('last_action', 'Last Status', true, array('width' => '50'));
    }

    $this -> addColumn('last_run', 'Last Run', true, array('width' => '50'));
    $this -> mDefaultOrder = 'pos';

    if ($this -> mCanInsert) {
      $this -> addCpy();
    }

    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    if ($this -> mCanInsert) {
      $this -> addBtn('New Service', "go('index.php?act=sys-svc.new')", '<i class="ico-w16 ico-w16-plus"></i>');
    }
    $this -> getPrefs();

    $this->loadScheduleLookup();

    $this -> mGrp = 'mand';
    $lMand = CCor_Res::extract('id', 'name_'.LAN, 'mand');
    $lMand[0] = 'Global';
    $this->mMand = $lMand;

    $this -> mIte = new CCor_TblIte('al_sys_svc');
    $this -> mIte -> setOrder('mand', $this -> mDir);
    $this -> mIte -> set2Order($this -> mOrd, $this -> mDir);

    $this -> addPanel('vie', $this -> getViewMenu());

    $this -> getJs();
  }

  protected function & getViewMenuObject() {
    $lMen = new CHtm_Menu(lan('lib.opt'));

    $lOk = '<i class="ico-w16 ico-w16-plus"></i>';
    $lNOk = '<img src="img/d.gif" alt="" width="16">';
    $lImg = ($this->hasColumn('schedule')) ? $lOk : $lNOk;
    $lMen -> addItem('index.php?act=sys-svc.pref&pref=schedule', 'Show schedule', $lImg);

    $lImg = ($this->hasColumn('last_progress')) ? $lOk : $lNOk;
    $lMen -> addItem('index.php?act=sys-svc.pref&pref=last_progress', 'Show last progress', $lImg);

    $lImg = ($this->hasColumn('last_action')) ? $lOk : $lNOk;
    $lMen -> addItem('index.php?act=sys-svc.pref&pref=last_action', 'Show last action', $lImg);

    $lMen -> addJsItem('Flow.Std.showByClass(\'bh-svc\')', 'Expand all');
    $lMen -> addJsItem('Flow.Std.hideByClass(\'bh-svc\')', 'Collapse all');

    $lUsr = CCor_Usr::getInstance();
    $lShowList = $lUsr->getPref($this->mPrf.'.show.list', true);
    $lImg = ($lShowList) ? $lOk : $lNOk;
    $lMen -> addItem('index.php?act=sys-svc.pref&pref=list', 'Always show list', $lImg);

    return $lMen;
  }

  protected function loadScheduleLookup() {
    $lDate = new CCor_Date();
    $lDate = $lDate -> getFirstOfWeek();
    $lBit = 1;
    for ($i=0; $i<7; $i++) {
      $lBit = 1 << $i;
      $lRet[$lBit] = $lDate->getFmt('D');
      $lDate = $lDate->getDaysDif(1);
    }
    $this -> mDow = $lRet;

    $this -> mDowNames = array(
    	0 => 'never',
        31 => 'weekdays',
        127 => 'any day'
    );

    $lArr[0] = 'always';
    $lArr[60] = 'every minute';
    $lArr[300] = 'every 5 minutes';
    $lArr[600] = 'every 10 minutes';
    $lArr[3600] = 'every hour';
    $lArr[SECS_PER_DAY] = 'once per day';

    $this->mInterval = $lArr;
  }

  protected function getTrTag() {
    $lMand = $this -> getVal('mand');
    #$lExt = ((MID == $lMand) || (0 == $lMand)) ? '' : ' dn';
    $lExt = '';
    return '<tr class="hi bh-mand-'.$lMand.$lExt.' bh-svc">';
  }

  protected function getTdCtr($aStat = false) {

    #$lCls = ($this->getVal('mand') == MID) ? ' cy' : '';
    $lCls = '';
    $lRet = '<td class="'.$this -> mCls.$lCls.' ar w16">';
    $lRet.= $this -> mCtr.'.';
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getGroupHeader() {
    $lRet = '';
    if (!empty($this -> mGrp)) {
      $lNew = $this -> getVal($this -> mGrp);
      if ($lNew !== $this -> mOldGrp) {
        $lRet = TR;
        if (MID == $lNew) {
          $lRet.= '<td class="tg1 cy cp" '.$this -> getColspan().' onclick="Flow.Std.togByClass(\'bh-mand-'.$lNew.'\')">';
        } else {
          $lRet.= '<td class="tg1 cp" '.$this -> getColspan().' onclick="Flow.Std.togByClass(\'bh-mand-'.$lNew.'\')">';
        }
        $lName = (isset($this -> mMand[$lNew])) ?  $this -> mMand[$lNew] : '???';
        $lRet.= ' MID '.$lNew.' - ';
        $lRet.= htm($lName).NB;
        if (MID == $lNew) {
          $lRet.= ' - Active';
        }
        $lRet.= '</td>';
        $lRet.= _TR;
        $this -> mOldGrp = $lNew;
        $this -> mCls = 'td1';
      }
    }
    return $lRet;
  }

  protected function getJs() {
    $lRet = 'function svcRun(aId) {';
    $lRet.= '  $("ac" + aId).update(" ");'.LF;
    $lRet.= '  var lEl = $("t" + aId);'.LF;
    $lRet.= '  lEl.update("<img src=\'img/pag/ajx2.gif\' />");'.LF;
    $lRet.= '  new Ajax.Updater(lEl, "index.php", {parameters:{act:"sys-svc.run", id:aId}, evalScripts:true});'.LF;
    $lRet.= '}'.LF;

    $lRet.= 'function svcTog(aId) {';
    $lRet.= '  jQuery("#img" + aId).load("index.php?act=sys-svc.tog&id="+aId);'.LF;
    $lRet.= '}'.LF;

    $lRet.= 'function svcReset(aId) {'.LF;
    $lRet.= '  jQuery.post("index.php?act=sys-svc.reset&id=" + aId);'.LF;
    $lRet.= '}'.LF;

    $lRet.= 'function updateSvc() {'.LF;
    $lRet.= '  jQuery.post("index.php?act=sys-svc.update", function(aData) {'.LF;

    $lRet.= '    jQuery.each(aData["run"], function(i,item) {'.LF;
    $lRet.= '      var el = jQuery("#t"+i);'.LF;
    $lRet.= '      if (el && (el.html() != item)) {'.LF;
    $lRet.= '        el.fadeTo("fast", 0.2, function() {el.html(item).fadeTo("fast", 1);});'.LF;
    $lRet.= '      }'.LF;
    $lRet.= '    });'.LF;

    $lRet.= '    jQuery.each(aData["progress"], function(i,item) {'.LF;
    $lRet.= '      var el = jQuery("#p"+i);'.LF;
    $lRet.= '      if (el && (el.html() != item)) {'.LF;
    $lRet.= '        el.fadeTo("fast", 0.2, function() {el.html(item).fadeTo("fast", 1);});'.LF;
    $lRet.= '      }'.LF;
    $lRet.= '    });'.LF;

    $lRet.= '    jQuery.each(aData["action"], function(i,item) {'.LF;
    $lRet.= '      var el = jQuery("#ac"+i);'.LF;
    $lRet.= '      if (el && (el.html() != item)) {'.LF;
    $lRet.= '        el.fadeTo("fast", 0.2, function() {el.html(item).fadeTo("fast", 1);});'.LF;
    $lRet.= '      }'.LF;
    $lRet.= '    });'.LF;

    $lRet.= '    setTimeout(updateSvc, 3000);'.LF;

    $lRet.= '  }, "json");'.LF;
    $lRet.= '}'.LF;

    $lRet.= 'jQuery(function() {setTimeout(updateSvc, 3000);});'.LF;
    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lRet);
  }

  protected function getTdActive() {
    $lSid = $this -> getInt('id');
    $lVal = $this -> getInt('flags');
    $lImg = (bitSet($lVal, sfActive)) ? 3 : 0;
    $lRet = '<a href="javascript:svcTog('.$lSid.')" id="img'.$lSid.'" class="nav">';
    $lRet.= '<i class="ico-w16 ico-w16-flag-0'.$lImg.'"></i>';
    $lRet.= '</a>';
    return $this -> td($lRet);
  }

  protected function getTdSchedule() {
    $lDow = $this -> getInt('dow');
    if (isset($this->mDowNames[$lDow])) {
      $lRet = $this->mDowNames[$lDow];
    } else {
      $lDows = array();
      foreach ($this->mDow as $lBit => $lName) {
        if (bitSet($lDow, $lBit)) {
          $lDows[] = $lName;
        }
      }
      $lRet = implode(',', $lDows);
    }
    $lSec = $this->getVal('tick');
    if (isset($this->mInterval[$lSec])) {
      $lRet.= ', '.$this->mInterval[$lSec];
    }
    $lFrom = $this->getVal('from_time');
    if (!$this->isEmptyTime($lFrom)) {
      $lRet.= ' from '.substr($lFrom, 0, 25);
    }
    $lTo = $this->getVal('to_time');
    if (!$this->isEmptyTime($lTo)) {
      $lRet.= ' to '.substr($lTo, 0, 25);
    }
    return $this -> td($lRet);
  }

  protected function isEmptyTime($aTime) {
    if (empty($aTime)) return true;
    if ('00:00:00' == $aTime) return true;
    return false;
  }

  protected function getTdLast_Run() {
    $lSid = $this -> getInt('id');
    $lMand = $this -> getInt('mand');
    $lRunning = $this -> getVal('running');
    $lRet = '<td class="'.$this -> mCls.' nw" id="t'.$lSid.'">';
    if ('Y' == $lRunning) {
      $lRet.= '<img src="img/pag/ajx2.gif">';
    } elseif (in_array($lMand, array(0, MID))) {
      $lRet.= '<a href="javascript:svcRun('.$lSid.')" class="nav">';
      $lVal = $this -> getVal('last_run');
      $lDat = new CCor_Date($lVal);
      $lRet.= $lDat -> getFmt(lan('lib.date.long')).' '.substr($lVal, 11);
      $lRet.= '</a>';
    } else {
      $lRet = '<td class="'.$this -> mCls.' tg" id="t'.$lSid.'">';
      $lVal = $this -> getVal('last_run');
      $lDat = new CCor_Date($lVal);
      $lRet.= $lDat -> getFmt(lan('lib.date.long')).' '.substr($lVal, 11);
    }
    $lRet.= '</td>';
    return $lRet;
  }

  public static function getLastRun($aRow) {
    $lSid = $aRow['id'];
    $lMand = $aRow['mand'];
    $lRunning = $aRow['running'];
    if ('Y' == $lRunning) {
      $lRet = '<img src="img/pag/ajx2.gif">';
    } elseif (in_array($lMand, array(0,MID))) {
      $lRet = '<a href="javascript:svcRun('.$lSid.')" class="nav">';
      $lVal = $aRow['last_run'];
      $lDat = new CCor_Date($lVal);
      $lRet.= $lDat -> getFmt(lan('lib.date.long')).' '.substr($lVal, 11);
      $lRet.= '</a>';
    } else {
      $lVal = $aRow['last_run'];
      $lDat = new CCor_Date($lVal);
      $lRet = $lDat -> getFmt(lan('lib.date.long')).' '.substr($lVal, 11);
    }
    return $lRet;
  }

  protected function getTdLast_Progress() {
    $lSid = $this -> getInt('id');
    $lRet = '<td class="'.$this -> mCls.' nw" id="p'.$lSid.'">';

    $lRet.= '<a href="javascript:svcRestart('.$lSid.')" class="nav">';
    $lVal = $this -> getVal('last_progress');
    $lDat = new CCor_Date($lVal);
    $lRet.= $lDat -> getFmt(lan('lib.date.long')).' '.substr($lVal, 11);
    $lRet.= '</a>';

    $lRet.= '</td>';
    return $lRet;
  }

  public static function getLastProgress($aRow) {
    $lSid = $aRow['id'];

    $lRet = '<a href="javascript:svcRestart('.$lSid.')" class="nav">';
    $lVal = $aRow['last_progress'];
    $lDat = new CCor_Date($lVal);
    $lRet.= $lDat -> getFmt(lan('lib.date.long')).' '.substr($lVal, 11);
    $lRet.= '</a>';

    return $lRet;
  }

  protected function getTdLast_Action() {
    $lSid = $this -> getInt('id');
    $lRet = '<td class="'.$this -> mCls.' nw" id="ac'.$lSid.'">';
    $lRet.= htm($this -> getVal('last_action')).NB;
    $lRet.= '</td>';
    return $lRet;
  }

  public static function getLastAction($aRow) {
    $lRet = htm($aRow['last_action']).NB;
    return $lRet;
  }

  protected function getCont() {
    $lRet = parent::getCont();
    $lRet.= '<div id="svcCheck" class="dn"></div>';
    return $lRet;
  }

  protected function getTdActions() {
    $lId = $this->getInt('id');
    $lName = $this->getVal('name');
    $lMen = new CHtm_Menu('Actions');
    $lMen->addTh1($lName);

    $lMand = $this -> getInt('mand');

    $lImg = '';
    $lMen -> addJsItem('svcTog('.$lId.')', 'Toggle Active', '<i class="ico-w16 ico-w16-flag-03"></i>');
    $lMen -> addJsItem('svcReset('.$lId.')', 'Reset', '<i class="ico-w16 ico-w16-cancel"></i>');

    #if (in_array($lMand, array(0,MID))) {
    #  $lMen -> addJsItem('svcRun('.$lId.')', 'Run now', 'ico/16/process_doit.gif');
    #}
    $lMen -> addItem('index.php?act=sys-svc.cpy&id='.$lId, 'Copy service', '<i class="ico-w16 ico-w16-copy"></i>');
    $lMen -> addJsItem('Flow.Std.cnf(\''.$this -> getDelLink().'\', \'cnfDel\')', 'Delete', '<i class="ico-w16 ico-w16-del"></i>');

    return $this->td($lMen->getContent());
  }

}