<?php
class CInc_Lan_Mod extends CCor_Mod_Table {

  public function __construct($aAvailLang = array()) {
    parent::__construct('al_sys_languages', 'code');
    $this -> addField(fie('code'));

    $this -> mAvailLang = $aAvailLang;
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addField(fie('name_'.$lLang));
    }

    $this -> mAutoInc = FALSE;
  }

  protected function beforePost($aNew = FALSE) {
/*
    if ($aNew) {
      $lSql = 'ALTER TABLE `al_sys_languages` ';
      $lSql.= ' ADD '.backtick('name_'.$this -> mReqVal['code']).' VARCHAR( 255  )  CHARACTER  SET utf8 COLLATE utf8_general_ci NOT  NULL';
      $lQry = new CCor_Qry($lSql);
      #$lRet = $lQry -> query($lSql);

      $lSql = 'ALTER TABLE `al_sys_lang` ';
      $lSql.= ' ADD '.backtick('value_'.$this -> mReqVal['code']).' VARCHAR( 255  )  CHARACTER  SET utf8 COLLATE utf8_general_ci NOT  NULL';
      $lRet = $lQry -> query($lSql);

    }
*/
  }

  protected function doInsert() {
    #echo '<pre>---mod.php---'.get_class().'---';var_dump($this -> mReqVal,$this -> mVal,'#############');echo '</pre>';
    $lSql = 'REPLACE INTO '.backtick($this -> mTbl).' SET ';
    foreach ($this -> mVal as $lKey => $lVal) {
      if ('new_name' != $lKey) {
        $lSql.= backtick($lKey).'='.esc($lVal).',';
      }
    }
    $lSql.= backtick('name_'.$this -> mVal['code']).'='.esc($this -> mVal['new_name']);

    $this -> dbg($lSql);
    if ($this -> mTest) {
      return TRUE;
    } else {
      $lQry = new CCor_Qry();
      $lRet = $lQry -> query($lSql);

      return $lRet;
    }
  }

  protected function afterPost($aNew = FALSE) {
    if ($aNew) {

      //hier fehlt noch die neue Spalte: name_'code'
      $lUsr = CCor_Usr::getInstance();
      $lSql = 'REPLACE INTO `al_sys_lang` SET';
      $lSql.= ' `code`='.esc('lan.'.$this -> mVal['code']);
      $lSql.= ', `mand`="0"';
      foreach ($this -> mAvailLang as $lLang => $lName) {
        $lSql.= ', `value_'.$lLang.'`='.esc($this -> mVal['name_'.$lLang]);
      }
      $lSql.= ', `value_'.$this -> mVal['code'].'`='.esc($this -> mVal['new_name']);
      $lSql.= ';';

      CCor_Qry::exec($lSql);

    }
  }

}