<?php
class CInc_Api_Alink_Response extends CCor_Obj {
  /**
   * The Alink Method
   * @var string
   */
  public $mMethod = '';
  
  public function __construct($aXml, $aMethod = ''){
    $this -> mXml = $aXml;
    $this -> mMethod = $aMethod;
    if ($this -> mMethod != 'getFile'){
      // Send Xml to parser.
      $this -> mDoc = simplexml_load_string($this -> mXml);
    }else {
      // Get Object $this ->mDoc without XML parser.
      $this -> getFileData();
    }
    $this -> mVal = array();
    #$this -> getResult();
  }

  public function getVal($aKey) {
    return (isset($this -> mDoc -> $aKey)) ? $this -> mDoc -> $aKey : NULL;
  }

  public function getResult() {
    #$this -> dbg($this -> mXml);
    #$this -> dbg(var_export($this -> mDoc, TRUE));
    foreach ($this -> mDoc as $lKey => $lVal) {
      $lKey = (string)$lKey;
      $lVal = $this -> mDoc -> $lKey;
      if ($lKey == 'item') {
        $this -> dbg('Type '.gettype($lVal));
      }
      if (is_array($lVal)) {
        $this -> mVal[$lKey][] = $this -> getVar($lVal);
      } else {
        $this -> mVal[$lKey] = $this -> getVar($lVal);
      }
      $this -> dbg('Dump '.(string)$lKey.' '.var_export($lVal, TRUE), mlInfo);
    }
    $this -> dump($this -> mVal,'RESPONSE ');
  }

  private function getVar($aVal) {
    if (count($aVal)>1) {
      $lRet = array();
      foreach ($aVal as $lKey => $lVal) {
        if (is_numeric($lKey)) {
          $lRet[] = $this -> getVar($lVal);
        } else {
          $lRet[$lKey] = $this -> getVar($lVal);
        }
      }
      return $lRet;
    } else {
      return (string)$aVal;
    }
  }

  /**
   * Parse Data by Method 'getFile'
   * @return string Data Part of Alink Response
   */
  private function getFileData(){
    $lArr = Array();

    $lLogIt = CCor_Cfg::get('msg.log.FileNotFound', TRUE);
    if (!(strpos($this -> mXml, '<errno>0</errno>') === false)) {
      $lArr['errno'] = 0;
      $lArr['errmsg'] = '';
      $istart = strpos($this -> mXml, '<mime>') + 6;
      if ($istart>0) {
        $iende = strpos($this -> mXml, '</mime>');
        $lArr['mime'] = substr ($this -> mXml, $istart , $iende - $istart);
      }
      $this -> msg('RECV : No Error. Get: '.$lArr['mime'], mtApi, mlInfo);
      $istart = strpos($this -> mXml, '<data>') + 6;
      if ($istart>0) {
        $iende = strpos($this -> mXml, '</data>');
        $lArr['data'] = substr ($this -> mXml, $istart , $iende - $istart);
      }
    } elseif (!$lLogIt AND !(strpos($this -> mXml, '<errno>602</errno>') === false)) {
      // Do Nothing
      // "RECV : <response><errno>602</errno><errmsg>File 0019906.pdf not found</errmsg></response>"
    } else {
      $this -> msg('RECV : '.$this -> mXml, mtApi, mlError);
    }
    $this -> mDoc = (object)$lArr;
  }
  
}