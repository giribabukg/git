<?php
class CInc_Gru_Mod extends CCor_Mod_Table {

  public function __construct($aCond = 0) {
    parent::__construct('al_gru');

    // 'id' and 'mand' are set automatically
    $this -> addField(fie('id'));
    $this -> addField(fie('mand'));

    // upcoming fields can be set by the user
    $this -> addField(fie('name'));
    $lFields = array('parent_id', 'code', 'kundenId','cnd_sql','chk_master_src', 'admin_level','comp_dom','comp_name','max_usr','pass_exp','typ');
    foreach ($lFields as $lKey => $lValue) {
      if (isset($_REQUEST['val'][$lValue])) {
        $this -> addField(fie($lValue));
      }
    }
    if (is_numeric($aCond)) {
      $this -> mDelAllowed = TRUE;
    } else {
      $this -> mDelAllowed = FALSE;
    }
  }

  protected function beforeDelete($aId) {
    $lId = intval($aId);
    $lCnt = CCor_Qry::getInt('SELECT COUNT(*) FROM al_gru WHERE parent_id='.$lId);
    if ($lCnt > 0) {
      $this -> msg('Group with subgroups cannot be deleted!', mtUser, mlError);
      $this -> mCancel = TRUE;
    } else {
      $lCnt = CCor_Qry::getInt('SELECT mand FROM al_gru WHERE id='.$lId);
      if (0 == $lCnt) {
        $this -> msg('Mandantenunabhaengige Gruppen koennen nicht geloescht werden!', mtUser, mlError);
        $this -> mCancel = TRUE;
      }
    }
  }

  protected function afterDelete($aId){
    CCor_Qry::exec('DELETE FROM al_usr_mem WHERE gid='.intval($aId));

    CCor_Cache::clearStatic('cor_res_mem_'.MID);
    CCor_Cache::clearStatic('cor_res_mem_'.MID.'_uid');
    CCor_Cache::clearStatic('cor_res_mem_'.MID.'_gid');

    $this -> msg('Group '.$aId.' deleted', mtAdmin, mlInfo);
  }

  public function setInfos($aId, $aValues) {
    $lId = intval($aId);
    $lSql = 'DELETE FROM al_gru_infos WHERE gid='.$lId;
    $lQry = new CCor_Qry($lSql);
    if (!empty($aValues))
      foreach ($aValues as $lKey => $lVal) {
      $lSql = 'INSERT INTO al_gru_infos SET gid='.$lId.',';
      $lSql.= 'alias='.esc($lKey).',';
      $lSql.= 'val='.esc($lVal).';';
      $lQry ->query($lSql);
    }
  }

  public static function clearCache() {
    $lCkey = 'cor_res_gru';
    CCor_Cache::clearStatic($lCkey);
  }

  protected function afterChange() {
    self::clearCache();
  }

  protected function beforePost($aNew = FALSE) { //Aufruf vor update
    if (isset($this -> mReqOld['id'])) {
      $this -> save_cnd();
      $this -> save_procnd();
    }
  }

  protected function afterPost($aNew = FALSE) { //Aufruf nach insert
  if (!isset($this -> mReqOld['id'])) {//Modfifications in cnd has to made after usr - if a new group has to be inserted
      $this -> save_cnd();
      $this -> save_procnd();
    }
  }

  protected function save_cnd() {
    if ($this -> mReqVal['cnd'] != $this -> mReqOld['cnd']) {
      if (isset($this -> mInsertId)) {
        $lGId = $this -> mInsertId;
      } else {
        $lGId = $this -> mReqOld['id'];
      }

      $lUpdateGroupTable = true;

      $lNewCond = esc($this -> mReqVal['cnd']);
      $lOldCond = esc($this -> mReqOld['cnd']);

      $lQry = new CCor_Qry();
      if (empty($this -> mReqVal['cnd']) AND $this -> mDelAllowed) {
        $lSQL = 'DELETE FROM al_cnd WHERE grp_id='.$lGId.' AND mand='.MID;
        $lQry -> query($lSQL);
        $lNewCndId = 0;
      } else {
        $this -> mAvailLang = CCor_Res::get('languages');
        $lSqlNam = '';
        $lSqlCond = '';
        foreach ($this -> mAvailLang as $lLang => $lName) {
          $lSqlNam.= " ,".backtick('name_'.$lLang);
          $lSqlCond.= " ,".$lNewCond;
        }

        $lSQL = 'SELECT * FROM al_cnd WHERE grp_id='.$lGId.' AND mand='.MID;
        $lQry -> query($lSQL);
        if ($lRow = $lQry -> getAssoc()) {
          $lSQL = 'UPDATE al_cnd SET cnd_id='.$lNewCond.' WHERE mand='.MID." AND grp_id=".$lGId;
          $lUpdateGroupTable = false;
        } else {
          $lSQL = 'INSERT INTO al_cnd (mand,grp_id,cnd_id) VALUES (';
          $lSQL.= MID.", ".$lGId.", ".$lNewCond.")";
        }

        $lRet = $lQry -> query($lSQL);
        $lNewCndId = $lQry -> getInsertId();
      }

      $this -> dbg('Condition changed from '.$lOldCond.' to '.$lNewCond.' for user '.$lGId.' (al_usr.cnd = '.$lNewCndId.((0 == $lNewCndId) ? ' - Deleted' : '').') /MID='.MID);

      if ($lUpdateGroupTable) {
        $lSQL = 'UPDATE al_gru SET cnd='.$lNewCndId;
        $lSQL.= ' WHERE id='.$lGId;
        $lSQL.= ' LIMIT 1';
        $lQry -> query($lSQL);
      }

      CCor_Cache::clearStatic('cor_res_cnd_'.MID);
      CCor_Cache::clearStatic('cor_res_cnd_'.MID.'_uid');
      CCor_Cache::clearStatic('cor_res_cnd_'.MID.'_gid');

    }
  }

  protected function save_procnd() {
    if ($this -> mReqVal['procnd'] != $this -> mReqOld['procnd']) {
      if (isset($this -> mInsertId)) {
        $lGId = $this -> mInsertId;
      } else {
        $lGId = $this -> mReqOld['id'];
      }
  
      $lNewCond = esc($this -> mReqVal['procnd']);
      $lOldCond = esc($this -> mReqOld['procnd']);
  
      $lSQL = 'UPDATE al_gru SET procnd='.$lNewCond;
      $lSQL.= ' WHERE id='.$lGId;
      $lSQL.= ' LIMIT 1';
      $lQry = new CCor_Qry();
      $lQry -> query($lSQL);
  
      CCor_Cache::clearStatic('cor_res_cnd_'.MID);
      CCor_Cache::clearStatic('cor_res_cnd_'.MID.'_uid');
      CCor_Cache::clearStatic('cor_res_cnd_'.MID.'_gid');
    }
  }
  public function writeHis() {
    $lUid = CCor_Usr::getAuthId();
    foreach ($this -> mOld as $lKey => $lVal) {
      if ($this -> fieldHasChanged($lKey)) {
        $this->saveGruHis($lUid, $this->getVal('id'), date("Y-m-d"), 14, "Group setting has been changed: ".$lKey, "From: ". $this->getOld($lKey) . " to " .$this->getVal($lKey));
      }
    }
  }
  public function saveGruHis ($aUid, $aGruId, $aDate, $aTyp, $aSubject, $aMsg)
  {
    $lQry = new CCor_Qry();
    $lGruHisSql = "INSERT INTO al_gru_his (uid,gru_id,datum,typ,subject,msg) VALUES ('" .$aUid . "','" . $aGruId . "','" . $aDate . "','" . $aTyp . "','" .
        $aSubject . "','" . $aMsg . "')";
    $lQry -> query($lGruHisSql);
  }
  
}