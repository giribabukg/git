<?php
class CInc_Hom_Wel_Flag_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mUsr = CCor_Usr::getInstance();
    $this -> mAva = fsArt;
  }

  protected function actStd() {
    $this -> redirect('index.php?act=hom-wel');
  }

  protected function actFpr() {
    $lVie = new CHtm_Fpr($this -> mMod.'.sfpr');
    $lDef = CCor_Res::get('fie');

    $lArr = array();
    foreach ($lDef as $lFie) {
      $lFla = intval($lFie['flags']);
      $lAva = intval($lFie['avail']);
      if (bitSet($lAva, $this -> mAva)) {
        if (bitSet($lFla, ffList)) {
          // If Jobfield has Read Flag active, ask for User READ-RIGHT (combination of 'fie_' and Alias).
          // If User has no READ-RIGHT, Jobfield not shown in the list.
          $lFieRight = 'fie_'.$lFie['alias'];
          if (bitset($lFla,ffRead) && !$this -> mUsr -> canRead($lFieRight)) {
            continue;
          }
          $lArr[$lFie['id']] = $lFie['name_'.LAN];
        }
      }
    }
    $lVie -> setSrc($lArr);
    $lUsr = CCor_Usr::getInstance();

    // 1. is hom-wel-XXX.cols set?
    // 2. is hom.wel.mytask.XXX.colum set?
    // 3. is hom.wel.mytask.colum set?
    // 4. fallback: jobnr, stichw, apl and webstatus

    $lUsrPref = $lUsr -> getPref($this -> mPrf.'.cols');
    $lModCols = CCor_Cfg::get('hom.wel.mytask.flag.column', array());
    $lGenCols = CCor_Cfg::get('hom.wel.mytask.column', array());
    $lFallBack = array('jobnr', 'stichw', 'apl', 'webstatus');

    if (!empty($lUsrPref)) {
      $lVie -> setSel($lUsr -> getPref($this -> mPrf.'.cols'));
    } elseif (!empty($lModCols)) {
      $lColIDs = '';
      foreach ($lModCols as $lKey => $lValue) {
        $lSQL = CCor_Qry::getInt('SELECT id FROM al_fie WHERE mand='.MID.' AND alias='.esc($lValue).';');
        if (!empty($lSQL)) {
          $lColIDs.= $lSQL.',';
        }
      }
      $lColIDs = ltrim($lColIDs, ',');

      $lVie -> setSel($lColIDs);
    } elseif (!empty($lGenCols)) {
      $lColIDs = '';
      foreach ($lGenCols as $lKey => $lValue) {
        $lSQL = CCor_Qry::getInt('SELECT id FROM al_fie WHERE mand='.MID.' AND alias='.esc($lValue).';');
        if (!empty($lSQL)) {
          $lColIDs.= $lSQL.',';
        }
      }
      $lColIDs = ltrim($lColIDs, ',');

      $lVie -> setSel($lColIDs);
    } else {
      $lColIDs = '';
      foreach ($lFallBack as $lKey => $lValue) {
        $lSQL = CCor_Qry::getInt('SELECT id FROM al_fie WHERE mand='.MID.' AND alias='.esc($lValue).';');
        if (!empty($lSQL)) {
          $lColIDs.= $lSQL.',';
        }
      }
      $lColIDs = ltrim($lColIDs, ',');

      $lVie -> setSel($lColIDs);
    }

    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lVie -> getTooltips());
    $this -> render($lVie);
  }

  protected function actSfpr() {
    $lDst = $this -> mReq -> getVal('dst');
    $lUsr = CCor_Usr::getInstance();
    if (!empty($lDst)) {
      $lDstStr = implode(',', $lDst);
    } else {
      $lDstStr = '';
    }
    $lUsr -> setPref($this -> mPrf.'.cols', $lDstStr);
    $this -> redirect();
  }

  protected function actSpr() {
    $lVie = new CHtm_Fpr($this -> mMod.'.sspr');
    $lVie -> setTitle(lan('lib.opt.spr'));
    $lDef = CCor_Res::get('fie');
  
    $lArr = array();
    foreach ($lDef as $lFie) {
      $lFla = intval($lFie['flags']);
      $lAva = intval($lFie['avail']);
      if (bitSet($lAva, $this -> mAva)) {
        if (bitSet($lFla, ffSearch)) {
          // If Jobfield has Read Flag active, ask for User READ-RIGHT (combination of 'fie_' and Alias).
          // If User has no READ-RIGHT, Jobfield not shown in the list.
          $lFieRight = 'fie_'.$lFie['alias'];
          if (bitset($lFla,ffRead) && !$this -> mUsr -> canRead($lFieRight)){
            continue;
          }
          $lArr[$lFie['id']] = $lFie['name_'.LAN];
        }
      }
    }
  
    $lVie -> setSrc($lArr);
    $lUsr = CCor_Usr::getInstance();
    $lVie -> setSel($lUsr -> getPref($this -> mPrf.'.sfie'));
    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lVie -> getTooltips());
    $this -> render($lVie);
  }
  
  protected function actSspr() {
    $lDst = $this -> mReq -> getVal('dst');
    $lUsr = CCor_Usr::getInstance();
    $lUsr -> setPref($this -> mPrf.'.sfie', implode(',', $lDst));
    $this -> redirect();
  }

  protected function actSelview() {
    $lId = $this -> getInt('id');
  
    $lQry = new CCor_Qry('SELECT * FROM al_usr_view WHERE id='.$lId);
    if ($lRow = $lQry -> getDat()) {
      $lUsr = CCor_Usr::getInstance();
      $lUsr -> setPref($this -> mMod.'.cols', $lRow['cols']);
      $lUsr -> setPref($this -> mMod.'.lpp', $lRow['lpp']);
      $lUsr -> setPref($this -> mMod.'.ord', $lRow['ord']);
      $lUsr -> setPref($this -> mMod.'.sfie', $lRow['sfie']);
      $lUsr -> setPref($this -> mMod.'.page', 0);
    }
    $this -> redirect();
  }

  protected function actSelsearch() {
    $lId = $this -> getInt('id');
  
    $lQry = new CCor_Qry('SELECT ser FROM al_usr_search WHERE id='.$lId.' AND mand='.MID);
    if ($lRow = $lQry -> getDat()) {
      $lUsr = CCor_Usr::getInstance();
      $lUsr -> setPref($this -> mMod.'.ser', unserialize($lRow['ser']));
      $lUsr -> setPref($this -> mMod.'.page', 0);
    }
    $this -> redirect();
  }
}