<?php
/**
 * Adding an existing user to a group in Webcenter
  *
 * @package    API
 * @subpackage Webcenter
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Api_Wec_Query_Addusertogroup extends CApi_Wec_Query {

  /**
   * Valid fields that can be passed to create or update a user
   *
   * @var array
   */
  protected $mValidFields = array(
    'groupid','groupname','usermemberid','usermembername'
  );

  /**
   * Set a user field/option
   *
   * @param string $aKey Name of the user field/option
   * @param mixed $aVal The user field's or option's value
   * @return bool A valid field was set
   */

  public function setField($aKey, $aVal) {
    $lKey = strtolower($aKey);
    if (!in_array($lKey, $this -> mValidFields)) {
      $this -> dbg('Invalid Webcenter User field '.$aKey, mtApi, mlWarn);
      return false;
    }
    $this -> setParam($lKey, $aVal);
    return true;
  }

  /**
   * Create the user
   *
   * @return false|string Either false if an error occurred or the new user ID
   */
  public function create() {
    $lXml = $this -> query('AddMemberToGroup.jsp');
    $this -> dbg($lXml);    
    if (empty($lXml)) return false;
    $lRes = new CApi_Wec_Response($lXml);
    $lDoc = $lRes -> getDoc();
    if ($lRes -> isSuccess()) {
      $lRet = (string)$lDoc -> userID;
      if (empty($lRet)) {
        $lXml = '<root>'.$lXml.'</root>';
        $lRes = new CApi_Wec_Response($lXml);
        $lDoc = $lRes -> getDoc();
        $lRet = (string)$lDoc -> userID;
      }
      return (string)$lRet;
    }
    return false;
  }

  /**
   * Create a user, using data from the local user database
   *
   * @param int $aUid User ID in local database
   * @return false|string Either false if an error occurred or the new user ID
   */
  public function createFromDb($aUid, $aGroupname) {
    $lUid = intval($aUid);
    $lQry = new CCor_Qry('SELECT * FROM al_usr_info WHERE iid="wec_uid" and uid='.$lUid);
    if (!$lRow = $lQry -> getDat()) return false;
    
    $this -> setField('groupname', $aGroupname);
    $this -> setField('usermemberid', $lRow['val']);
    $lRet = $this -> create();
    if (!$lRet) return false;
    return $lRet;
  }

}