<?php
class CInc_Crp_Mod extends CCor_Mod_Table {

  public function __construct() {
    if (0 == MID) {
      parent::__construct('al_crp_mastertpl'); // templates for all mandators
    } else {
      parent::__construct('al_crp_master', 'id,mand'); // critical path for mandator
    }

    // 'mand' is set automatically
    $this -> addField(fie('mand'));

    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addField(fie('name_'.$lLang));
    }

    // upcoming fields can be set by the user
    $lFields = array('code', 'eve_draft', 'eve_comment', 'eve_jobchange', 'eve_upload', 'eve_onhold', 'eve_cancel', 'eve_continue', 'eve_revive', 'eve_archive', 'eve_archive_condition', 'eve_archive_numberofjobs', 'eve_phrase');
    foreach ($lFields as $lKey => $lValue) {
      $this -> addField(fie($lValue));
    }
  }

  protected function doUpdate() {
    $lSql = 'UPDATE '.$this -> mTbl.' SET ';

    foreach ($this -> mOld as $lKey => $lVal) {
      if ($this -> fieldHasChanged($lKey)) {
        $lNew = $this -> getVal($lKey);
        if (in_array($lKey, array('eve_draft', 'eve_comment', 'eve_jobchange', 'eve_upload', 'eve_onhold', 'eve_cancel', 'eve_continue', 'eve_revive', 'eve_archive', 'eve_archive_condition', 'eve_archive_numberofjobs'))) {
          if (!$lNew) {
            $lSql.= $lKey.'=0,';
          } else {
            $lSql.= $lKey.'='.$lNew.',';
          }
        } else {
          $lSql.= $lKey.'='.esc($lNew).',';
        }
      }
    }
    $lSql = strip($lSql, 1);
    $lSql.= ' WHERE';
    foreach($this -> mKey as $lKey) {
      $lSql.= ' '.$lKey.' = '.esc($this -> getOld($lKey)).' AND';
    }
    $lSql = strip($lSql,4);
    $lSql.= ' LIMIT 1';

    return CCor_Qry::exec($lSql);
  }

  protected function beforePost($aNew = FALSE) {
    if ($aNew) {
      $this -> setVal('mand', MID);
    }
  }

  public static function clearCache() {
    $lCkey = 'cor_res_crpmaster';
    CCor_Cache::clearStatic($lCkey);
  }

  protected function afterChange() {
    self::clearCache();
  }
}