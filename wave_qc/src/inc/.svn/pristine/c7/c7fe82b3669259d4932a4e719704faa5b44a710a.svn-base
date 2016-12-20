<?php
/**
 * Title
 *
 * Description
 *
 * @package    package
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Usr_Info_Form extends CHtm_Form {

  public function __construct($aUserId) {
    $this -> mUid = intval($aUserId);
    parent::__construct('usr-info.sedt', 'User Infos', FALSE);
    $this -> getFields();
    $this -> getValues();
    $this -> setParam('id', $this -> mUid);
    $this -> setParam('old[id]', $this -> mUid);
    $this -> setParam('val[id]', $this -> mUid);
  }

  protected function getFields() {
    $lSql = 'SELECT DISTINCT(iid) AS iid FROM al_usr_info ORDER BY iid';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> addDef(fie($lRow['iid'], $lRow['iid']));
    }
  }

  protected function getValues() {
    $lSql = 'SELECT iid,val FROM al_usr_info WHERE uid='.$this -> mUid;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> setVal($lRow['iid'], $lRow['val']);
    }
  }

}
