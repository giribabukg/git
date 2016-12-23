<?php
class CInc_Sys_Svc_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = 'Services';
    $this -> mMmKey = 'opt';

    // Ask If user has right for this page
    $lpn = 'sys-svc';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CSys_Svc_List();
    $this -> render($lVie);
  }
  
  protected function renderMenu($aContent) {
    $lUsr = CCor_Usr::getInstance();
    $lShow = $lUsr->getPref($this->mPrf.'.show.list', true);
    if ($lShow) {
      $lList = new CSys_Svc_List();
      $this->render(CHtm_Wrap::wrap($lList, $aContent));
    } else {
      $this->render($aContent);
    }
  }

  protected function actEdt() {
    $lId = $this -> mReq -> getInt('id');
    $lVie = new CSys_Svc_Form_Edit($lId);
    $this -> renderMenu($lVie);
  }

  protected function actSedt() {
    $lMod = new CSys_Svc_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actNew() {
    $lVie = new CSys_Svc_Form_Base('sys-svc.snew', 'New Service');
    $lVie -> setVal('from_time', '08:00:00');
    $lVie -> setVal('to_time', '17:00:00');
    $lVie -> setVal('mand', MID);
    $lVie -> setVal('dow', 31); // default: weekdays Mo-Fr
    $this -> renderMenu($lVie);
  }
  
  protected function actCpy() {
    $lId = $this -> getInt('id');
    $lVie = new CSys_Svc_Form_Base('sys-svc.snew', 'Copy Service');
    $lVie->load($lId);
    $this -> renderMenu($lVie);
  }

  protected function actSnew() {
    $lMod = new CSys_Svc_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect();
  }

  protected function actTog() {
    $lId = $this -> getReqInt('id');
    $lQry = new CCor_Qry('SELECT flags,name FROM al_sys_svc WHERE id='.$lId);
    $lDat = $lQry -> getDat();
    $lFla = intval($lDat['flags']);
    
    $lRet = '';
    
    if (bitSet($lFla, sfActive)) {
      $this -> msg('Service stopped: '.$lDat['name'], mtAdmin, mlInfo);
      $lRet.= img('img/ico/16/flag-00.gif');
    } else {
      $this -> msg('Service started: '.$lDat['name'], mtAdmin, mlInfo);
      $lRet = img('img/ico/16/flag-03.gif');
    }
    $lSql = 'UPDATE al_sys_svc SET flags=flags^'.sfActive.' WHERE id='.$lId;
    CCor_Qry::exec($lSql);
    echo $lRet;
    exit;
  }

  protected function actStop() {
    $lId = $this -> getReqInt('id');
    $lSql = 'UPDATE al_sys_svc SET running="N" WHERE id='.$lId;
    CCor_Qry::exec($lSql);
    $this -> redirect();
  }

  protected function actDel() {
    $lId = $this -> mReq -> getInt('id');
    $lSql = 'DELETE FROM al_sys_svc WHERE id="'.addslashes($lId).'"';
    CCor_Qry::exec($lSql);
    $this -> redirect();
  }

  protected function actRun() {
    $lId = $this -> getInt('id');
    $lUrl = $this -> getReq('url','');
    $lSql = 'SELECT * FROM al_sys_svc WHERE id='.$lId;
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry -> getAssoc();
    
    CSvc_Runner::defineRun();
    CSvc_Base::addLog('------------------------');

    $lCls = 'CSvc_'.ucfirst($lRow['act']);
    if (class_exists($lCls)) {
      $lName = '[ID'.$lRow['id'].'] '.'['.$lRow['act'].'] '.$lRow['name'].' ';
      CSvc_Base::addLog($lName.'started manually...');

      $lCls = new $lCls($lRow);
      if ($lCls -> run()) {
        CSvc_Base::addLog($lName.'okay.');
        if (empty($lUrl)) {
          $lQry -> query('SELECT last_run FROM al_sys_svc WHERE id='.$lId);
          $lRow = $lQry -> getAssoc();
          echo '<a href="javascript:svcRun('.$lId.')" class="nav">';
          $lDat = new CCor_Date($lRow['last_run']);
          echo $lDat -> getFmt(lan('lib.date.long')).' '.substr($lRow['last_run'], 11);
          echo '</a>';
        } else {
          $this -> msg('Service '.$lRow['name'].' runs successfully.', mtAdmin, mlInfo);
          $this -> redirect($lUrl);
        }
      } else {
        CSvc_Base::addLog($lName.'not okay.');
        $this -> msg('Service '.$lRow['name'].' reported problem.', mtAdmin, mlError);
        echo 'Error!';
      }
    } else {
      $this -> msg('Service class '.$lCls.' not found.', mtAdmin, mlFatal);
      echo 'Error '.$lCls.' not found';
    }
  }

  protected function actDelCacheAll() {
    $lName = $this -> getReq('name');
    $lUrl = $this -> getReq('url','');
    $lRow = array('id' => 6, 'params'=> 'a:1:{s:4:"mode";s:3:"all";}');
     
    $lCls = 'CSvc_'.ucfirst($lName);
    if (class_exists($lCls)) {
      $lCls = new $lCls($lRow);
      if ($lCls -> run() AND !empty($lUrl)) {
        $this -> msg('Service "Clean Main Cache (all)" runs successfully.', mtAdmin, mlInfo);
        $this -> redirect($lUrl);

      } else {
        $this -> msg('"Clean Main Cache (all)" doesn\'t run.', mtAdmin, mlError);
      }
    } else {
      $this -> msg('Service class '.$lCls.' not found.', mtAdmin, mlFatal);
    }
  }
  
  protected function actUpdate() {
    $lSql = 'SELECT id,mand,running,last_run,last_progress,last_action FROM al_sys_svc';
    $lQry = new CCor_Qry($lSql);
    $lRet = array(); 
    $lRun = array(); 
    $lProgress = array(); 
    $lAction = array(); 
    foreach ($lQry as $lRow) {
      $lRun[$lRow['id']] = CInc_Sys_Svc_List::getLastRun($lRow);
      $lProgress[$lRow['id']] = CInc_Sys_Svc_List::getLastProgress($lRow);
      $lAction[$lRow['id']] = CInc_Sys_Svc_List::getLastAction($lRow);
    }
    $lRet['run'] = $lRun;
    $lRet['progress'] = $lProgress;
    $lRet['action'] = $lAction;
    
    echo Zend_Json::encode($lRet);
    exit;
  }
  
  protected function actPref() {
    $lPref = $this->getReq('pref');
    $lUsr = CCor_Usr::getInstance();
    $lKey = $this->mPrf.'.show.'.$lPref;
    $lCurrent = $lUsr->getPref($lKey, false);
    $lUsr->setPref($lKey, !$lCurrent);
    $this->redirect();
  }
  
  protected function actReset() {
    $lId = $this->getInt('id');
    $lSql = 'UPDATE al_sys_svc SET running="N", last_action="reset",';
    $lSql.= 'last_progress=NOW(),last_run=NOW() ';
    $lSql.= 'WHERE id='.$lId;
    
    CCor_Qry::exec($lSql);
    exit;
  }

}