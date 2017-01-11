<?php
class CInc_Fie_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_fie');
    $this -> addField(fie('id'));
    $this -> addField(fie('src'));
    $this -> addField(fie('alias'));
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addField(fie('name_'.$lLang));
    }
    $this -> addField(fie('native'));
    $this -> addField(fie('native_wdc'));
    $this -> addField(fie('native_core'));
    $this -> addField(fie('typ'));
    $this -> addField(fie('maxlen'));
    $this -> addField(fie('param'));
    $this -> addField(fie('attr'));
    $this -> addField(fie('feature'));
    $this -> addField(fie('learn'));
    $this -> addField(fie('flags'));
    $this -> addField(fie('avail'));
    $this -> addField(fie('desc_en'));
    $this -> addField(fie('desc_de'));
    if (CCor_Cfg::get('validate.available')) {
      $this->addField(fie('validate_rule'));
    }
    #$this -> mTest = TRUE;
  }

  public function getPost(ICor_Req $aReq, $aOld = TRUE) {
    $lReq = $aReq -> getAll();

    $lVal = $lReq['val'];
    $lTyp = $lVal['typ'];
    if ($aOld) {
      $lOld = $lReq['par_old'];
      if(isset($lOld[$lTyp])) {
        $lOld = $lOld[$lTyp];
        if (empty($lOld)) {
          $lOld = '';
        } else {
          $lOld = serialize($lOld);
        }
      } else  $lOld = '';
      $lReq['old']['param'] = $lOld;
    }

    $lNew = $lReq['par_val'];

    // In htm/fie/fac wird über ListChange() ein weiteres Inputfeld gesteuert: 'SteerAlias'
    // Wenn im steuernden DropDown KEINE Auswahl selektiert ist, muß im gesteuerten
    // Inputfeld die ganze Liste (abhängig v. der Gid in NoChoice) angezeigt werden.
    // Damit dieser Wert nicht gespeichert wird, ist er auch im hidden-Feld zu setzen! Dort
    // ist der Wert v. NoChoice noch nicht bekannt, deshalb die Speicherung.
    if (!empty($lNew['gselect']['gid']) AND FALSE !== strpos($lReq['val']['feature'], 'SteerAlias')) {
      $lFeature = $lReq['val']['feature'];
      $lFeature = str_replace('\\','', $lFeature);
      $lFeature = toArr($lFeature);
      #$lFeature['NoChoice'] = $lNew['gselect']['gid'];
      $lReq['val']['feature'] = serialize($lFeature);
    }

    if (isset($lNew[$lTyp]) AND !empty($lNew[$lTyp])) {
      $lNew = serialize($lNew[$lTyp]);
    } else {
      $lNew = '';
    }
    $lReq['val']['param'] = $lNew;

    $lReqObj = clone($aReq);
    $lReqObj -> assign($lReq);
    parent::getPost($lReqObj, $aOld);
  }

  protected function beforePost($aNew = FALSE) {
    $lTmp = $this -> getVal('learn');
    if ('_self' == $lTmp) {
      $lVal = $this -> getVal('alias');
      $this -> setVal('learn', $lVal);
      if (!$aNew) {
        $lOld = $this -> getOld('learn');
        if ($lOld != $lVal) {
          $this -> mUpd['learn'] = $lVal;
        }
      }
    }
    if ($aNew) {
      $this -> setVal('mand', MID);
    }
  }

  protected function doInsert() {
    /**
     * TODO Dont Insert If there is same Alias OR Space in the Alias
     *
     */
    parent::doInsert();

    $lReg = new CHtm_Fie_Reg();
    $lMaxLen = intval($this -> getVal('maxlen'));
    $lTyp = $lReg -> getSqlType($this -> getVal('typ'), $lMaxLen);
    $lAli = $this -> getVal('alias');
    $lAva = intval($this -> getVal('avail'));
    $lFlag = intval($this -> getVal('flags'));

    if (bitset($lAva, fsPro)) {
      $this -> addSqlField('al_job_pro_'.intval(MID), $lAli, $lTyp);
    }
    if (bitset($lAva, fsSku)) {
      $this -> addSqlField('al_job_sku_'.intval(MID), $lAli, $lTyp);
    }
    if (bitset($lAva, fsSub)) {
      $this -> addSqlField('al_job_sub_'.intval(MID), $lAli, $lTyp);
    }
    if (bitset($lFlag, ffReport)) {
      $this -> addSqlField('al_job_shadow_'.intval(MID), $lAli, $lTyp);
      if (CCor_Cfg::get('extended.reporting')) {
        $this -> addSqlField('al_job_shadow_'.intval(MID).'_report', $lAli, $lTyp);
      }
    }
    $this -> addSqlField('al_job_arc_'.intval(MID), $lAli, $lTyp);

    foreach ($this -> mAvailLang as $lLang => $lName) {
      CCor_Cache::clearStatic('cor_res_fie_'.MID.'_'.$lLang);
  }
  }

  protected function doUpdate() {
    parent::doUpdate();

    $lReg = new CHtm_Fie_Reg();
    $lMaxLen = intval($this -> getVal('maxlen'));
    $lTyp = $lReg -> getSqlType($this -> getVal('typ'), $lMaxLen);
    $lAli = $this -> getVal('alias');
    $lOldAli = $this -> getOld('alias');
    $lAva = intval($this -> getVal('avail'));
    $lFlag = intval($this -> getVal('flags'));

    if ($lOldAli == $lAli){
      if (bitset($lAva, fsPro)) {
        $this -> addSqlField('al_job_pro_'.intval(MID), $lAli, $lTyp);
      }
      if (bitset($lAva, fsSku)) {
        $this -> addSqlField('al_job_sku_'.intval(MID), $lAli, $lTyp);
      }
      if (bitset($lAva, fsSub)) {
        $this -> addSqlField('al_job_sub_'.intval(MID), $lAli, $lTyp);
      }
      if (bitset($lFlag, ffReport)) {
        $this -> addSqlField('al_job_shadow_'.intval(MID), $lAli, $lTyp);
        if (CCor_Cfg::get('extended.reporting')) {
          $this -> addSqlField('al_job_shadow_'.intval(MID).'_report', $lAli, $lTyp);
        }
      }
     $this -> addSqlField('al_job_arc_'.intval(MID), $lAli, $lTyp);
    } else{
        if (bitset($lAva, fsPro)) {
          $this -> changeSqlField('al_job_pro_'.intval(MID), $lAli, $lOldAli, $lTyp);
        }
        if (bitset($lAva, fsSku)) {
          $this -> changeSqlField('al_job_sku_'.intval(MID), $lAli, $lOldAli, $lTyp);
        }
        if (bitset($lAva, fsSub)) {
          $this -> changeSqlField('al_job_sub_'.intval(MID), $lAli, $lOldAli, $lTyp);
        }
        if (bitset($lFlag, ffReport)) {
          $this -> changeSqlField('al_job_shadow_'.intval(MID), $lAli, $lOldAli, $lTyp);
          if (CCor_Cfg::get('extended.reporting')) {
            $this -> changeSqlField('al_job_shadow_'.intval(MID).'_report', $lAli, $lOldAli, $lTyp);
          }
      }
     $this -> changeSqlField('al_job_arc_'.intval(MID), $lAli, $lOldAli, $lTyp);

    }

  }
  /**
   *
   * @param $aTbl
   * @param $aAlias	Alias of Jobfields
   * @param $aTyp	Type of Jobfields
   * @return
   */
  protected function addSqlField($aTbl, $aAlias, $aTyp) {
    $lSql = "DESCRIBE ".$aTbl." `".$aAlias."`";
    $lQry = new CCor_Qry($lSql);

    if (!$lRow = $lQry -> getAssoc()) {
      $lSql = 'ALTER TABLE '.$aTbl.' ADD `'.addslashes($aAlias).'` '.$aTyp.';';
      $this->dbg($lSql);
      $lQry -> exec($lSql);
    }

  }

  protected function changeSqlField($aTbl, $aAlias, $aOldAlias, $aTyp) {
    $lSql = "DESCRIBE ".$aTbl." `".$aOldAlias."`";
    $lQry = new CCor_Qry($lSql);

    if ($lRow = $lQry -> getAssoc()) {
      $lSql = 'ALTER TABLE '.$aTbl.' CHANGE `'.$aOldAlias.'` `'.$aAlias.'` '.$aTyp.';';
    } else {
      $lSql = 'ALTER TABLE '.$aTbl.' ADD `'.addslashes($aAlias).'` '.$aTyp.';';
    }
     $this->dbg($lSql);
     $lQry -> exec($lSql);
  }

  protected function afterChange() {
      $lCkey = 'cor_res_fie_'.MID.'_';
      CCor_Cache::clearStatic($lCkey.'de');
      CCor_Cache::clearStatic($lCkey.'en');
  }

}
