<?php
interface ICor_Req {
  public function getInt($aName);
  public function getVal($aName, $aStd = NULL);
  public function expect($aName, $aType = NULL);
}

class CInc_Cor_Req extends CCor_Obj implements ICor_Req {

  protected $mVal = array();
  protected $mLoaded = FALSE;

  public function __construct() {
  }

  public function loadRequest() {
    $this -> assign($_GET);     //loads CCor Obj's $lKey and $lVal property. So this class(CInc_Cor_Req) extends CCor_Obj class. then this assign() method present in CCor_Obj class.
    $this -> assign($_POST);    //loads CCor Obj's $lKey and $lVal property. So this class(CInc_Cor_Req) extends CCor_Obj class.
  }

  public function loadArgv() {
    global $argc, $argv;
    $lCnt = $_SERVER['argc'];
    $lArg = $_SERVER['argv'];
    $lArr = array();
    for ($i=1; $i < $lCnt; $i++) {
      parse_str($lArg[$i], $lArr);
      $this -> assign($lArr);
    }
    //echo '<pre>---req.php---'.get_class().'---';var_dump($lArg,'#############');echo '</pre>';
    #$this -> assign($lArr);
  }

  public function isAjax() {
    return ('XMLHttpRequest' == $_SERVER['HTTP_X_REQUESTED_WITH']);
  }

  public function getAll() {
    return $this -> mVal;
  }

  public function __get($aName) {
//    error_log('.....I am in __get inc cor req ....'.var_export($this -> mVal, true)."\n",3,'logggg.txt');
    return (isset($this -> mVal[$aName])) ? $this -> mVal[$aName] : NULL;
  }

  public function __set($aName, $aValue) {
    $this -> mVal[$aName] = $aValue;
  }

  public function getInt($aName) {
    return intval($this -> __get($aName));
  }

  public function getVal($aName, $aStd = NULL) {
    $lRet = $this -> __get($aName);
    if (NULL === $lRet) $lRet = $aStd;
    return $lRet;
  }

  public function expect($aName, $aType = NULL) {
    if (!isset($this -> mVal[$aName])) {
      $this -> dbg('Expected request parameter ['.$aName.'] not set', mlWarn);
      return FALSE;
    } else if (NULL !== $aType) {
      $lTyp = gettype($this -> mVal[$aName]);
      if ($lTyp !== $aType) {
        $this -> dbg('Request parameter '.$aName.' has type '.$lTyp.', but '.$aType.' expected', mlWarn);
        return FALSE;
      }
    }
    return TRUE;
  }

}