<?php
function getChar($aPool) {
  $lPos = mt_rand(0, strlen($aPool)-1);
  return substr($aPool, $lPos, 1);
}

class CInc_App_Pwd extends CCor_Obj {

  public static function createPassword ($aLen)
  {
    mt_srand((double) microtime() * 1000000);
    $lNums = '234578'; // no 1,6,9 because of l/I,G,g
    $lAlpha = 'abcdefhjkmnpqrstwxyz'; // no l,i,g,u,v
    $lBigAlpha = 'ABCDEFHJKLMNPQRSTWXYZ'; // no G,I,U,V
    $lSpeChars = ',;:!?.$/*-+&@_.#'; // add by Polash
    
    $lPassCondions = CCor_Cfg::get('hom-pwd.conditions');
    list ($lLength, $lLowerCase, $lUpperCase, $lDigit, $lSpecial) = array_values($lPassCondions);
    
    $lRet = array();
    $lNum = ($lDigit != 'off') ? mt_rand(1, 2) : 0;
    for ($i = 1; $i <= $lNum; $i ++ ) {
      $lRet[] = getChar($lNums);
    }
    $lNum = ($lLowerCase != 'off') ? mt_rand(1, 2) : 0;
    for ($i = 1; $i <= $lNum; $i ++ ) {
      $lRet[] = getChar($lAlpha);
    }
    $lNum = ($lSpecial != 'off') ? mt_rand(1, 2) : 0;
    for ($i = 1; $i <= $lNum; $i ++ ) {
      $lRet[] = getChar($lSpeChars);
    }
    $lNum = ($lUpperCase != 'off') ? $lLength - count($lRet) : 0;
    for ($i = 1; $i <= $lNum; $i ++ ) {
      $lRet[] = getChar($lBigAlpha);
    }
    for ($i = 1; $i < ($aLen * 4); $i ++ ) {
      $lPos = mt_rand(0, $aLen - 1);
      $lTmp = $lRet[$lPos];
      $lRet[$lPos] = $lRet[0];
      $lRet[0] = $lTmp;
    }
    return implode('', $lRet);
  }

  public static function encryptPassword($aPwd) {
    $lCfg = CCor_Cfg::getInstance();
    $lMag = $lCfg -> get('log.magic');

/*  //berechnet ein neues Master-Passwort
    $aPwd = MANDATOR_NAME;
    echo '<pre>---pwd.php---';var_dump(md5(sha1($lMag.$aPwd)),'#############');echo '</pre>';
*/
    return md5(sha1($lMag.$aPwd));
  }
  
  public static function createNewToken() {
  	return bin2hex(openssl_random_pseudo_bytes(16));
  }
  
  public function passValidationCheck ($aOld, $aNew, $aCnf, $aNewEncoded, $lDbPwd, $aUsrId)
  {
    $lPassCondions = CCor_Cfg::get('hom-pwd.conditions');
    list ($lLength, $lLowerCase, $lUpperCase, $lDigit, $lSpecial) = array_values($lPassCondions);
    
    $lError = array();
    if(empty($aOld) || empty($aNew) || empty($aCnf)){
      $lError[] = lan('pwd.restrictions.required');
      return $lError;
    }
    if(!count($lError)){
        if (strlen($aNew) < $lLength) {
          $lError[] = sprintf(lan('pwd.restrictions.length'), $lLength);
        }
        if ( ! preg_match('/^(?=.*[a-z]).+$/', $aNew) && $lLowerCase != 'off') {
          $lError[] = lan('pwd.restrictions.lcase');
        }
        if ( ! preg_match('/^(?=.*[A-Z]).+$/', $aNew) && $lUpperCase != 'off') {
          $lError[] = lan('pwd.restrictions.ucase');
        }
        if ( ! preg_match('/\d/', $aNew) && $lDigit != 'off') {
          $lError[] = lan('pwd.restrictions.number');
        }
        if ( ! preg_match('/[,\;\:\!\?\.\$\/\*\-\+\&\@\_\.\#]{1,}/', $aNew) &&
             $lSpecial != 'off') {
          $lError[] = lan('pwd.restrictions.special.char');
        }
        if ($aNew != $aCnf) {
          $lError[] = lan('pwd.restrictions.new.cnf');
        }
        if ($aNewEncoded == $lDbPwd) {
          $lError[] = lan('pwd.restrictions.not.change');
        }
        if (self::hisPass($aUsrId, $aNew, $aOld, $lDbPwd)) {
          $lError[] = lan('pwd.restrictions.used.pass');
        }
        if (count($lError)) {
          return $lError;
        }
      }
    }

  public function hisPass ($aUsrId, $aNew, $aOld, $lDbPwd)
  {
    $lNotAllowedLastPass = CCor_Cfg::get('password.reuse.not.last',3);
    $lNew = CApp_Pwd::encryptPassword($aNew);
    $lOld = CApp_Pwd::encryptPassword($aOld);
    
    $lSql = 'SELECT * FROM al_usr_pwd_his where user_id=' . $aUsrId .' ORDER BY id DESC LIMIT ' . $lNotAllowedLastPass;
    $lQry = new CCor_Qry($lSql);
    $lUsedPass = array();
    foreach ($lQry as $lRow) {
      $lUsedPass[] = $lRow['pass'];
    }
    if (in_array($lNew, $lUsedPass)) {
      return true;
    }
  }
 
  public function addedToPassHis ($aUserId, $aNew){
    $lSql = 'INSERT INTO al_usr_pwd_his SET ';
    $lSql .= 'user_id=' . $aUserId . ',';
    $lSql .= 'pass="' . $aNew . '",';
    $lSql .= 'password_change_date=NOW()';
    CCor_Qry::exec($lSql);
  }

  public function EnDecryptor($aAction, $aString) {
    $lOutput = false;
    $lEncryptMethod = "AES-256-CBC";
    // Set unique hashing key
    $lSecretKey = openssl_random_pseudo_bytes('SEcretKeY');
    // An IV is generally a random number that guarantees the encrypted text is unique
    // http://stackoverflow.com/questions/11821195/use-of-initialization-vector-in-openssl-encrypt
    $lSecretIv = 'Secret InmbnmV';
    // Generate a hash value
    $lKey = hash('sha256', $lSecretKey);
    $lIv = substr(hash('sha256', $lSecretIv), 0, 16);
    // Encrypts given data with given method and key, returns a raw or base64
    // encoded string
    if ($aAction == 'encrypt') {
      $lOutput = openssl_encrypt($aString, $lEncryptMethod, $lKey, 0, $lIv);
      $lOutput = base64_encode($lOutput);
    } else 
      if ($aAction == 'decrypt') {
        // Takes a raw or base64 encoded string and decrypts it using a given method and key
        $lOutput = openssl_decrypt(base64_decode($aString), $lEncryptMethod, $lKey, 0, $lIv);
      }
    
    return $lOutput;
  }
}