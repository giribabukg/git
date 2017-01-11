<?php
class CInc_Eve_Act_Form extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aCancel = NULL, $aEveId = 0) {
    parent::__construct($aAct, $aCaption, $aCancel);
    $this -> setAtt('class', 'tbl w600');
    $this -> mEveId = $aEveId;
    $this -> setUsedMaxPosition();

    $this -> addDef(fie('param', '', 'hidden'));
    $this -> addDef(fie('mand','','hidden'));
    $this -> addDef(fie('typ', '', 'hidden'));
    $this -> addDef(fie('eve_id', '', 'hidden'));

    $lPrefDef = CCor_Res::get('syspref');
    if (isset($lPrefDef['crp.posmax'])) {
      $lUsualAmount = $lPrefDef['crp.posmax']['val'];// 5
    } else {
      $lUsualAmount = 10;
    }
    // $lUsualAmount: usual amount of entries, 1 .. 5
    // $this -> mMaxPos: biggest number saved in Position
    // Show the list with the bigger one of them and if MaxPos is bigger, offer the next higher number too.
    if ($this -> mMaxPos < $lUsualAmount) {
      $lMax = $lUsualAmount;
    } else {
      $lMax = $this -> mMaxPos + 1;
    }
    $lArr = array();
    for ($i=0; $i < $lMax; $i++) {
      $lArr[] = $i + 1;
    }
    $lArr[100] = lan('lib.eve.deferred');
    #echo '<pre>---list.php---';var_dump($this -> mEveId,$lUsualAmount,$this -> mMaxPos,'#############');echo '</pre>';
    $this -> addDef(fie('pos', 'Position', 'select', $lArr));
    $this -> addDef(fie('dur', lan('lib.duration'))); // hier stehen alle Felder, die separat i.d. DB gespeichert werden

    $lPar = array('res' => 'cond', 'key' => 'id', 'val' => 'name');
    $this -> addDef(fie('cond_id', 'Condition', 'resselect', $lPar));

    $this -> setVal('mand', MID);#0); // als Default bei neuem Eintrag  0 = Fuer alle Mandanten
    #$this -> setParam('id', $this -> mEveId);
    $this -> setVal('dur', 1); // Default-Wert bei neuem Eintrag
  }

  public function addParamFields($aType) {
    $lReg = new CApp_Event_Action_Registry();
    $lAct = $lReg -> getAction($aType);
    $this -> mCap.= ': '.$lAct['name'];
    $lArr = $lReg -> getParamDefs($aType);
#    echo '<pre>---form.php---';var_dump($lArr,'#############');echo '</pre>';
    if (!empty($lArr))
    foreach ($lArr as $lDef) {
      $this -> addDef($lDef);
    }
  }

  public function load($aId) {
    $lId = intval($aId);
/*
    $lQry = new CCor_Qry('SELECT * FROM al_eve_act WHERE mand='.MID.' AND id='.$lId);
    if ($lRow = $lQry -> getAssoc()) {
*/
    if (!empty($this -> mIteEve)) {
      $lRow = $this -> mIteEve[$lId];
      $lTyp = $lRow['typ'];
      $this -> addParamFields($lTyp);
      $this -> assignVal($lRow);
      $lPar = toArr($lRow['param']);
      $this -> assignVal($lPar);
      $this -> setParam('old[id]', $lId);
      $this -> setParam('val[id]', $lId);

      $this -> setParam('old[typ]', $lRow['typ']);
      $this -> setParam('val[typ]', $lRow['typ']);
     } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }

  protected function setUsedMaxPosition() {
    $lMaxPos = 0;
    $this -> mIteEve = '';
    if (0 < $this -> mEveId) {
      $this -> mIteEve = new CCor_TblIte('al_eve_act');
      $this -> mIteEve -> addCnd('eve_id='.$this -> mEveId);
      $this -> mIteEve -> addCnd('mand='.MID);
      $this -> mIteEve = $this -> mIteEve -> getArray('id');#
      foreach ($this -> mIteEve as $lAct) {
        if ($lMaxPos < $lAct['pos'] and ($lAct['pos'] < 100)) {
          $lMaxPos = $lAct['pos'];
        }
      }
    }
    $this -> mMaxPos = $lMaxPos + 1 ;
  }

}