<?php
class CInc_App_Event_Action_Email_Group extends CApp_Event_Action {

  public function execute() {
    $lSender = new CApp_Sender('gru', $this -> mParams, $this -> mContext['job'], $this -> mContext['msg']);
    return $lSender -> execute();
  }

  public static function getStructureGroups($aFunc, $aEmptyFirst = true, $aEventId = NULL) {
    $lFunc = intval($aFunc);
    $lRet = array();
    if ($aEmptyFirst) {
      $lRet[''] = ' ';
    }
    if (empty($lFunc)) {
      return $lRet + CCor_Res::extract('id', 'name', 'gru');
    }
    
    $lFilter = array();
    $lEve = intval($aEventId);
    if (!empty($lEve)) {
      $lSql = 'SELECT alias,val FROM al_eve_infos ';
      $lSql.= 'WHERE eve_id='.$lEve.' ';
      $lSql.= 'AND alias NOT IN ("function", "prefix", "artwork_type")';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        if (empty($lRow['val'])) continue;
        $lFilter[$lRow['alias']] = $lRow['val'];
      }
    } 
    
    $lSql = 'SELECT g.id,g.name FROM al_gru g, al_gru_infos i ';
    $lSql.= 'WHERE g.id=i.gid ';
    $lSql.= 'AND i.alias="function" ';
    $lSql.= 'AND i.val='.$lFunc.' ';
    $lSql.= 'ORDER BY g.name';

    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lGroups[$lRow['id']] = $lRow['name']; 
    }
    if (empty($lFilter)) {
      return $lRet + $lGroups;
    }

    $lGids = implode(',', array_keys($lGroups));
    if (empty($lGids)) {
      return $lRet;
    }
    $lSql = 'SELECT gid,alias,val FROM al_gru_infos ';
    $lSql.= 'WHERE gid IN ('.$lGids.') ';
    $lSql.= 'AND alias NOT IN ("function", "name")';
    
    #var_export($lFilter);
    
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lGid = $lRow['gid'];
      if (!isset($lGroups[$lGid])) continue; // could already be deleted
      $lAlias = $lRow['alias'];
      if (empty($lFilter[$lAlias])) continue; // no need to filter
      $lValue = $lRow['val'];
      if (empty($lValue)) continue;
      if ($lValue != $lFilter[$lAlias]) {
        #echo $lValue .' != '.$lFilter[$lAlias];
        unset($lGroups[$lGid]);
      }
    }
    $lRet = $lRet + $lGroups;
    return $lRet;
  }

  public static function getParamDefs($aRow) {
    $lArr = array();
    $lTmp = CCor_Res::get('gru', array('parent_id'=>FUNC_PARENT));
    $lGru = array('' => ' ');
    foreach ($lTmp as $lRow) {
      $lName = trim($lRow['name']);
      $lCode = trim($lRow['code']);
      if ( (!empty($lCode)) && ($lCode != $lName) ) {

        $lName = cat($lName, '('.$lCode.')', ' ');
      }
      $lGru[$lRow['id']] = $lName;
    }
    $lFie = fie('func', lan('lib.email.function'), 'select', $lGru, array('onchange' => 'Flow.event.onFunctionChange(this)', 'class' => 'bc-func w200'));
    $lArr[] = $lFie;

    $lJs = 'jQuery(function() {Flow.event.onFunctionChange(jQuery(\'.bc-func\'));});';
    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lJs);

    $lGroups = self::getStructureGroups(0, true);

    $lFie = fie('sid', lan('lib.email.group'), 'select', $lGroups, array('class' => 'bc-group w400'));
    $lArr[] = $lFie;

    $lTpl = CCor_Res::extract('id', 'name', 'tpl');
    $lFie = fie('tpl', lan('lib.tpl'), 'select', $lTpl);
    $lArr[] = $lFie;
        
    $lFie = fie('task', 'Task', 'tselect', array('dom' => 'apl_task'));
    $lArr[] = $lFie;
    
    $lTmp = array('y' => lan('lib.yes'), 'n' => lan('lib.no'), 'f' => lan('lib.forced'));
    $lFie = fie('def', lan('lib.default'), 'select', $lTmp);
    $lArr[] = $lFie;
    
    $lTmp = array('all' => lan('crp.usr.confirm.all'), 'one' => lan('crp.usr.confirm.one'));
    $lFie = fie('confirm', lan('crp.apl.used').' '.lan('crp.usr.confirm'), 'select', $lTmp);
    $lArr[] = $lFie;
    
    $lTmp = array('y' => lan('lib.yes'), 'n' => lan('lib.no'));
    $lFie = fie('inv', lan('eve.act.invite.active'), 'select', $lTmp);
    $lArr[] = $lFie;
    
    $lTmp = array('y' => lan('lib.yes'), 'n' => lan('lib.no'));
    $lFie = fie('email', lan('eve.act.email.send'), 'select', $lTmp);
    $lArr[] = $lFie;
    
    return $lArr;
  }

  public static function paramToString($aParams) {
    $lRet = '';
    if (isset($aParams['sid'])) {
      $lSid = $aParams['sid'];
      $lArr = CCor_Res::extract('id', 'name', 'gru');
      $lRet.= (isset($lArr[$lSid])) ? $lArr[$lSid] : '['.lan('lib.unknown').' '.$lSid.']';
    } else {
      $lRet.= '[empty group]';
    }
    $lRet.= ', ';
    if (isset($aParams['tpl'])) {
      $lTpl = $aParams['tpl'];
      $lArr = CCor_Res::extract('id', 'name', 'tpl');
      $lRet.= (isset($lArr[$lTpl])) ? $lArr[$lTpl] : lan('lib.unknown').' '.$lTpl;
    } else {
      $lRet.= 'empty';
    }
    if (!empty($aParams['task'])) {
      $lRet.= ', Task: '.$aParams['task'];
    }
    $lRet.= ', checked: ';
    $lDef = $aParams['def'];
    $lTmp = array('y' => lan('lib.yes'), 'n' => lan('lib.no'), 'f' => lan('lib.forced'));
    $lRet.= (isset($lTmp[$lDef])) ? $lTmp[$lDef] : lan('lib.yes');

    if (isset($aParams['confirm'])) {
      $lRet.= ', '.lan('crp.apl.used').' '.lan('crp.usr.confirm').': ';
      $lDef = $aParams['confirm'];
      $lTmp = array('all' => lan('crp.usr.confirm.all'), 'one' => lan('crp.usr.confirm.one'));
      $lRet.= (isset($lTmp[$lDef])) ? $lTmp[$lDef] : lan('lib.yes');
    }
    
    $lRet.= ', '.lan('eve.act.invite.active').': ';
    if (isset($aParams['inv'])) {
      $lDef = $aParams['inv'];
    } else {
      $lDef = 'y';
    }
    $lTmp = array('y' => 'yes', 'n' => 'no');
    $lRet.= (isset($lTmp[$lDef])) ? $lTmp[$lDef] : 'yes';
    
    $lRet.= ', '.lan('eve.act.email.send').': ';
    if (isset($aParams['email'])) {
      $lDef = $aParams['email'];
    } else {
      $lDef = 'y';
    }
    $lTmp = array('y' => 'yes', 'n' => 'no');
    $lRet.= (isset($lTmp[$lDef])) ? $lTmp[$lDef] : 'yes';
    
    return $lRet;
  }
}