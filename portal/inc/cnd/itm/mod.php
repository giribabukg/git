<?php
class CInc_Cnd_Itm_Mod extends CCor_Mod_Table {

  /**
   * Constructor
   *
   * @access public
   */
  public function __construct() {
    parent::__construct('al_cnd_items');

    $this -> addField(fie('cnd_id'));
    $this -> addField(fie('field'));

    $lOperations = array('dom' => 'op');
    $this -> addField(fie('operator', lan('cnd-itm.operator'), 'tselect', $lOperations));

    $this -> addField(fie('value'));

    $lConjucntions = array('dom' => 'con');
    $this -> addField(fie('conjunction', lan('cnd-itm.conjunction'), 'tselect', $lConjucntions));
  }

  /**
   * afterPost
   *
   * @access protected
   */
  protected function afterPost($aNew = FALSE) {
    $lCndId = $this -> getReqVal('cnd_id');
    $this -> createCondition($lCndId);
  }

  public static function createCondition($aCndId) {
    $lCndId = $aCndId;

    $lUntouched = '';
    $lAliased = '';
    $lNatived = '';
    
    $lOldConjunction = 'NULL';
    $lError = false;

    $lInnerSQL = new CCor_Qry("SELECT field,operator,value,conjunction FROM al_cnd_items WHERE cnd_id=".$lCndId." ORDER BY id ASC;");
    foreach ($lInnerSQL as $lRow) {
      // if we have more than one row, we need a conjunction in the previous row
      if (empty($lOldConjunction)) {
        $lError = true;
        CCor_Msg::add(lan('cnd-itm.no_conjunction'), mtUser, mlNone); //mlNone bewirkt eine Ausgabe , auch wenn die User-Messages abgeschaltet sind
        break;
      }
    
      // Aliased
      $lAliased.= "(";
      $lAliased.= $lRow['field'];
    
      switch ($lRow['operator']) {
        case 'op_equals':
          $lRow['operator'] = ' = ';
          break;
        case 'op_equals_not':
          $lRow['operator'] = ' != ';
          break;
        case 'op_contains':
          $lRow['operator'] = ' LIKE ';
          $lRow['value'] = '%'.$lRow['value'].'%';
          break;
        case 'op_contains_not':
          $lRow['operator'] = ' NOT LIKE ';
          $lRow['value'] = '%'.$lRow['value'].'%';
          break;
      }
    
      $lAliased.= $lRow['operator'];
      $lAliased.= esc($lRow['value']);
      $lAliased.= ")";
    
      switch ($lRow['conjunction']) {
        case '':
          $lRow['conjunction'] = '';
          break;
        case 'con_and':
          $lRow['conjunction'] = ' AND ';
          break;
        case 'con_or':
          $lRow['conjunction'] = ' OR ';
          break;
      }
    
      $lAliased.= $lRow['conjunction'];
    
      // Natived
      $lNatived.= "("; // gehoert hierhin, da sonst msg(lan('cnd-itm.why_conjunction') angezeigt wird, wenn es das native nicht gibt!
      $lFields = CCor_Res::extract('alias', 'native', 'fie');
      if (CCor_Cfg::get('job.writer.default') == 'portal') {
        $lFields = CCor_Res::extract('alias', 'alias', 'fie');
      }
      
      if ($lFields[$lRow['field']]) {
        $lNatived.= $lFields[$lRow['field']];
      } else {
        $lError = true;
        CCor_Msg::add(esc($lRow['field']).' '.lan('cnd-itm.no_native'), mtUser, mlNone);
        break;
      }
    
      switch ($lRow['operator']) {
        case 'op_equals':
          $lRow['operator'] = ' = ';
          break;
        case 'op_equals_not':
          $lRow['operator'] = ' != ';
          break;
        case 'op_contains':
          $lRow['operator'] = ' LIKE ';
          $lRow['value'] = '%'.$lRow['value'].'%';
          break;
        case 'op_contains_not':
          $lRow['operator'] = ' NOT LIKE ';
          $lRow['value'] = '%'.$lRow['value'].'%';
          break;
      }
    
      $lNatived.= $lRow['operator'];
      $lNatived.= esc($lRow['value']);
      $lNatived.= ")";
    
      switch ($lRow['conjunction']) {
        case '':
          $lRow['conjunction'] = '';
          break;
        case 'con_and':
          $lRow['conjunction'] = ' AND ';
          break;
        case 'con_or':
          $lRow['conjunction'] = ' OR ';
          break;
      }
    
      $lNatived.= $lRow['conjunction'];
    
      $lOldConjunction = $lRow['conjunction'];
    }//end_foreach ($lInnerSQL as $lRow)
    
    $lAliasedAND = $lAliased;
    $lAliased = rtrim($lAliased, ' AND ');
    $lAliasedOR = $lAliased;
    $lAliased = rtrim($lAliased, ' OR ');
    $lNativedAND = $lNatived;
    $lNatived = rtrim($lNatived, ' AND ');
    $lNativedOR = $lNatived;
    $lNatived = rtrim($lNatived, ' OR ');

    $lAliased = trim($lAliased);
    $lNatived = trim($lNatived);
    
    if (empty($lAliased)) {
      $lAliased = '1';
    } else {
      $lAliased = '('.$lAliased.')';
    }
    
    if (empty($lNatived)) {
      $lNatived = '1';
    } else {
      $lNatived = '('.$lNatived.')';
    }

    if ($lAliased != $lAliasedAND || $lAliased != $lAliasedAND || $lNatived != $lNativedAND || $lNatived != $lNativedOR) {
      CCor_Msg::add(lan('cnd-itm.why_conjunction'), mtUser, mlNone);
    }
    
    if (false == $lError) {
      $lOuterSQL = "UPDATE `al_cnd_master` SET";
      $lOuterSQL.= " `untouched`=".esc($lUntouched).',';
      $lOuterSQL.= " `aliased`=".esc($lAliased).',';
      $lOuterSQL.= " `natived`=".esc($lNatived);
      $lOuterSQL.= " WHERE `id`=".$lCndId.";";
      CCor_Qry::exec($lOuterSQL);

      $lFlags = CCor_Qry::getInt("SELECT flags FROM al_cnd_master WHERE id=".addslashes($lCndId).";");
      if (($lFlags & 4) > 0) {
        $lId = CCor_Qry::getInt("SELECT id FROM al_cnd WHERE cnd_id=".addslashes($lCndId).";");
        if (!isset($lId)) {
          CCor_Qry::exec("INSERT INTO al_cnd (mand,pro_id,cnd_id) VALUES (".addslashes(MID).",".addslashes($lCndId).",".addslashes($lCndId).");");
        }
      }
    } else {
      $lOuterSQL = "UPDATE `al_cnd_master` SET";
      $lOuterSQL.= " `untouched`=NULL".',';
      $lOuterSQL.= " `aliased`=NULL".',';
      $lOuterSQL.= " `natived`=NULL";
      $lOuterSQL.= " WHERE `id`=".$lCndId.";";
      CCor_Qry::exec($lOuterSQL);
    }
    
    CCor_Cache::clearStatic('cor_res_cndmaster_'.MID);
  }

}