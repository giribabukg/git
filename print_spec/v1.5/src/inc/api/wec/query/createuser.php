<?php
/**
 * Create a new or update an existing user in Webcenter
 *
 * If the NewUsername/NewCompanyName already exists, the existing user will be
 * updated. Please note that only WEC Admin users can create other users
 *
 * @package    API
 * @subpackage Webcenter
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Api_Wec_Query_Createuser extends CApi_Wec_Query {

  /**
   * Valid fields that can be passed to create or update a user
   *
   * @var array
   */
  protected $mValidFields = array(
    'name','password','lastname','firstname','companyname','companylocation',
    'email','phone','mobile', 'function','isprojectmanager',
    'forcepasswordchange','guaranteedaccess'
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
    $this -> setParam('newuser'.$lKey, $aVal);
    return true;
  }

  /**
   * Create the user
   *
   * @return false|string Either false if an error occurred or the new user ID
   */
  public function create() {
    $lXml = $this -> query('CreateUser.jsp');
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
  public function createFromDb($aUid) {
    $lUid = intval($aUid);
    $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE id='.$lUid);
    if (!$lRow = $lQry -> getDat()) return false;
    $this -> setField('name', $lRow['user']);
    $this -> setField('password', base64_encode($lRow['user']));    
    $this -> setField('lastname', $lRow['lastname']);
    $this -> setField('firstname', $lRow['firstname']);
#    $this -> setField('companyname', $lRow['company']);
#    $this -> setField('companylocation', $lRow['location']);
    $this -> setField('email', $lRow['email']);
    $lRet = $this -> create();
    if (!$lRet) return false;
    $lInf = new CCor_Usr_Info($lUid);
    $lInf -> set('wec_usr', $lRow['user']);
    $lInf -> set('wec_pwd', $lRow['user']);
    $lInf -> set('wec_uid', $lRet);

    return $lRet;
  }

  /**
   * Get the user ID from Webcenter 
   *
   * @param int $aUid User ID in local database
   * @return false|string Either false if an error occurred or the new user ID
   */
  public function getUserID($aUid) {
    $lUid = intval($aUid);
    $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE id='.$lUid);
    if (!$lRow = $lQry -> getDat()) return false;
    $lInf = new CCor_Usr_Info($aUid);
    $lusr = $lInf -> get('wec_usr');        
    if (empty($lusr)) $lusr = $lRow['user'];
    if (empty($lusr)) return false;
   
    $this -> setParam('askedusername', $lusr);
    
    $lXml = $this -> query('GetUserInfo.jsp');
    $this -> dbg($lXml);
    if (empty($lXml)) return false;
    $lRes = new CApi_Wec_Response($lXml);    
    $lDoc = $lRes -> getDoc();
    
    if ($lRes -> isSuccess()) {
      $lRet = (string)$lDoc -> user_ID;
      
      $lInf = new CCor_Usr_Info($aUid);
      $lInf -> set('wec_usr', $lusr);
      $lInf -> set('wec_pwd', $lusr);
      $lInf -> set('wec_uid', $lRet);
      
      return (string)$lRet;
    }
    return false;
  }

}