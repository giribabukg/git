<?php
function getChar($aPool) {
  $lPos = mt_rand(0, strlen($aPool)-1);
  return substr($aPool, $lPos, 1);
}

class CInc_App_Pwd extends CCor_Obj {

  public static function createPassword($aLen) {

    mt_srand((double)microtime()*1000000);

    $lNums = '234578'; // no 1,6,9 because of l/I,G,g
    $lAlpha = 'abcdefhjkmnpqrstwxyz'; // no l,i,g,u,v
    $lBigAlpha = 'ABCDEFHJKLMNPQRSTWXYZ'; // no G,I,U,V

    $lRet = array();
    $lNum = mt_rand(1,3);
    for ($i=1; $i <= $lNum; $i++) {
      $lRet[] = getChar($lNums);
    }
    $lNum = mt_rand(2,3);
    for ($i=1; $i <= $lNum; $i++) {
      $lRet[] = getChar($lAlpha);
    }
    $lNum = $aLen - count($lRet);
    for ($i=1; $i <= $lNum; $i++) {
      $lRet[] = getChar($lBigAlpha);
    }
    for ($i=1; $i < ($aLen*4); $i++) {
      $lPos = mt_rand(0, $aLen -1);
      $lTmp        = $lRet[$lPos];
      $lRet[$lPos] = $lRet[0];
      $lRet[0]     = $lTmp;
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

}