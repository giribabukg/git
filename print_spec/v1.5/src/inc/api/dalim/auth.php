<?php
class CInc_Api_Dalim_Auth extends CCor_Obj {

  public static function getSessionId($aUser, $aDocument, $aKey = NULL) {
    $lQuery = new CApi_Dalim_Query();
    $lQuery->setParam('User', $aUser);
    $lQuery->setParam('Document', $aDocument);
    $lQuery->setParam('Key', $aKey);

    $lRes = $lQuery->query('admin/Authenticate');
    if (!$lRes) return false;

    /* $lRes = '<?xml version="1.0" encoding="UTF-8"?>
     * <Authentication IsAuthenticated="true"
     * SessionID="5678F81F7C8B373BB5C2F0D9CCF1687E"/>';
     */
    try {
      $lDoc = simplexml_load_string($lRes);
    } catch (Exception $lExc) {
      $this -> msg($lExc->getMessage(), mtApi, mlError);
      return false;
    }

    $lAttr = $lDoc->attributes();
    $lSessionId = isset($lAttr['SessionID']) ? $lAttr['SessionID'] : false;
    return $lSessionId;
  }

}