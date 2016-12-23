<?php
class CInc_Eve_Act_Cnt extends CCor_Cnt {

  private $mMakeFunction = array();

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('eve.act');
    $this -> mEve = $this -> getInt('id');

    if (empty($this->mEve)) {
      $this->dbg('EVENT ACT without EventId!', mlError);
    }

    $this -> mMmKey = 'opt';

    // Ask If user has right for this page
    $lpn = 'eve';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdRead);
    }

  }

  protected function getStdUrl() {
    return 'index.php?act='.$this -> mMod.'&id='.$this -> mEve;
  }

  protected function actStd() {
    $lVie = new CEve_Act_List($this -> mEve);
    $lMen = new CEve_Menu($this -> mEve, 'act');

    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actEdt() {
    $lSid = $this -> getInt('sid');

    $lFrm = new CEve_Act_Form('eve-act.sedt', lan('eve.act.edt'), 'eve-act&id='.$this -> mEve, $this -> mEve);
    $lFrm -> setParam('id', $this -> mEve);
    $lFrm -> load($lSid);
    
    $lField = $lFrm->getDef('func');
    if (!is_null($lField)) {
      $lAttr  = $lField['attr'];
      $lAttr['data-eve'] = $this->mEve;
      $lField['attr'] = $lAttr;
      $lFrm->addDef($lField);
    }
    $lMen = new CEve_Menu($this -> mEve, 'act');

    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSedt() {
    $lMod = new CEve_Act_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> update()) {
      CCor_Cache::clearStatic('cor_res_action_'.MID);
    }
    $this -> redirect();
  }

  protected function actNew() {
    $lFrm = new CEve_Act_Form('eve-act.snew', lan('eve.act.new'), 'eve-act&id='.$this -> mEve, $this -> mEve);
    $lFrm -> setParam('id', $this -> mEve);
    $lFrm -> setVal('eve_id', $this -> mEve);
    $lTyp = $this -> getReq('typ');
    $lFrm -> setVal('typ', $lTyp);
    $lFrm -> setVal('confirm', 'one');
    $lFrm -> setVal('dur', 2);
    $lFrm -> addParamFields($lTyp);
    
    $lField = $lFrm->getDef('func');
    if (!is_null($lField)) {
      $lAttr  = $lField['attr'];
      $lAttr['data-eve'] = $this->mEve;
      $lField['attr'] = $lAttr;
      $lFrm->addDef($lField);
    }
    $this -> render($lFrm);
  }

  protected function actSnew() {
    $lMod = new CEve_Act_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> insert()) {
      CCor_Cache::clearStatic('cor_res_action_'.MID);
    }
    $this -> redirect();
  }

  protected function actDel() {
    $lSid = $this -> getInt('sid');
    $lSql = 'DELETE FROM al_eve_act WHERE mand='.MID.' AND id='.$lSid;
    CCor_Qry::exec($lSql);
    CCor_Cache::clearStatic('cor_res_action_'.MID);
    ##CCor_Cache::clearStatic("cor_res_action_MID_*");//Zend_Cache_Exception: Invalid id or tag 'gri_cor_res_action_MID_*' : must use only [a-zA-Z0-9_]
    $this -> redirect();
  }

  protected function actAct() {
    $lSid = $this -> getInt('sid');
    $lSql = 'UPDATE al_eve_act SET active=1 WHERE mand='.MID.' AND id='.$lSid;
    CCor_Qry::exec($lSql);
    CCor_Cache::clearStatic('cor_res_action_'.MID);
    $this -> redirect();
  }

  protected function actDeact() {
    $lSid = $this -> getInt('sid');
    $lSql = 'UPDATE al_eve_act SET active=0 WHERE mand='.MID.' AND id='.$lSid;
    CCor_Qry::exec($lSql);
    CCor_Cache::clearStatic('cor_res_action_'.MID);
    $this -> redirect();
  }

  /*
   *  Es sollen alle Werte von 'dur' addiert werden. Je 'pos' wird aber nur der groesste Wert aufsummiert.
   *  used_in eve/act/list
   * @param array $aAllRows
   * @return array()
  */
  public function countDurationTime(array $aAllRows = array()) {

    // used in eve/act/list & job/dialog
    $this -> mMakeFunction = array(
        'funct' => 'amount',
        'value' => 'dur',
        'restr' => array('pos', 'max')
    ); // Es sollen alle Werte von 'dur' addiert werden. Je 'pos' wird aber nur der groesste Wert aufsummiert.

    $lFuncVal = 0;
    $lFuncKey = '';
    $lAllRows = array();
    if (!empty($aAllRows)) {
      $lFuncKey = $this -> mMakeFunction['value'];

      if (!empty($this -> mMakeFunction['restr'])) {
        $lRestrKey = $this -> mMakeFunction['restr'][0];

        foreach ($aAllRows as $lRow) {
          $lKey = $lRow[ $lRestrKey ] + 1;
          if (empty($lAllRows) OR !isset($lAllRows[$lKey])) {
            $lAllRows[$lKey] = (int)$lRow[ $lFuncKey ];
          } else {
            if ('max' == $this -> mMakeFunction['restr'][1]) {
              if ($lAllRows[$lKey] < $lRow[ $lFuncKey ]) {
                $lAllRows[$lKey] = (int)$lRow[ $lFuncKey ];
              }
            }
          }
        }

      } else {
        foreach ($aAllRows as $lRow) {
          $lAllRows[] = (int)$lRow[ $lFuncKey ];
        }
      }

      if (!empty($lAllRows)) {
        foreach ($lAllRows as $lRowVal) {
          if ('amount' == $this -> mMakeFunction['funct']) {
            $lFuncVal += $lRowVal;
          }
        }
        $lAllRows[0] = 0;
      }
    }

    if (0 < $lFuncVal) {
      ksort($lAllRows);
      $lRet = array('val' => $lFuncVal, 'key' => $lFuncKey, 'all' => $lAllRows);
    } else {
      $lRet = array('val' => 1, 'key' => $lFuncKey);
    }
    return $lRet;
  }
}
