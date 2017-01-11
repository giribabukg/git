<?php
class CInc_Svc_Wec extends CCor_Obj {

  protected static $mInstance;

  protected $mAttributes = array();
  protected $mMand = array();
  protected $mSysPref = array();

  public function __construct() {
    $this -> mMand = CCor_Res::extract('id', 'name_'.LAN, 'mand');
    $this -> mSysPref = CCor_Res::extract('code', 'val', 'syspref', array('code' => 'preview.*'));

    $this -> mAttributes['preview.path'] = !empty($this -> mSysPref['preview.path']) ? $this -> mSysPref['preview.path'] : '{JNR DIV 1000}/{JNR}/doc/';
    $this -> mAttributes['preview.filename'] = !empty($this -> mSysPref['preview.filename']) ? $this -> mSysPref['preview.filename'] : '{JNR}.jpg';
  }

  public function __destruct() {
    self::$mInstance = NULL;
  }

  public static function getInstance() {
    if (NULL === self::$mInstance) {
      self::$mInstance = new self();
    }
    return self::$mInstance;
  }

  public function getAttributes($aJobID) {
    if (!empty($aJobID)) {
      $lJobNr = ltrim($aJobID, '0');

      $lPath = $this -> mAttributes['preview.path'];
      $lFilename = $this -> mAttributes['preview.filename'];

      // TODO START: implement tokenizer
      $lPath = str_replace('{MID ZEROFILL}', str_pad(MID, 4, "0", STR_PAD_LEFT), $lPath);
      $lFilename = str_replace('{MID ZEROFILL}', str_pad(MID, 4, "0", STR_PAD_LEFT), $lFilename);

      $lPath = str_replace('{MID}', MID, $lPath);
      $lFilename = str_replace('{MID}', MID, $lFilename);

      $lPath = str_replace('{MAND}', $this -> mMand[MID], $lPath);
      $lFilename = str_replace('{MAND}', $this -> mMand[MID], $lFilename);

      $lPath = str_replace('{JNR DIV 1000000000}', substr($lJobNr, -strlen($lJobNr), -9), $lPath);
      $lFilename = str_replace('{JNR DIV 1000000000}', substr($lJobNr, -strlen($lJobNr), -9), $lFilename);

      $lPath = str_replace('{JNR DIV 100000000}', substr($lJobNr, -strlen($lJobNr), -8), $lPath);
      $lFilename = str_replace('{JNR DIV 100000000}', substr($lJobNr, -strlen($lJobNr), -8), $lFilename);

      $lPath = str_replace('{JNR DIV 10000000}', substr($lJobNr, -strlen($lJobNr), -7), $lPath);
      $lFilename = str_replace('{JNR DIV 10000000}', substr($lJobNr, -strlen($lJobNr), -7), $lFilename);

      $lPath = str_replace('{JNR DIV 1000000}', substr($lJobNr, -strlen($lJobNr), -6), $lPath);
      $lFilename = str_replace('{JNR DIV 1000000}', substr($lJobNr, -strlen($lJobNr), -6), $lFilename);

      $lPath = str_replace('{JNR DIV 100000}', substr($lJobNr, -strlen($lJobNr), -5), $lPath);
      $lFilename = str_replace('{JNR DIV 100000}', substr($lJobNr, -strlen($lJobNr), -5), $lFilename);
      
      $lPath = str_replace('{JNR DIV 10000}', substr($lJobNr, -strlen($lJobNr), -4), $lPath);
      $lFilename = str_replace('{JNR DIV 10000}', substr($lJobNr, -strlen($lJobNr), -4), $lFilename);

      $lPath = str_replace('{JNR DIV 1000}', substr($lJobNr, -strlen($lJobNr), -3), $lPath);
      $lFilename = str_replace('{JNR DIV 1000}', substr($lJobNr, -strlen($lJobNr), -3), $lFilename);

      $lPath = str_replace('{JNR DIV 100}', substr($lJobNr, -strlen($lJobNr), -2), $lPath);
      $lFilename = str_replace('{JNR DIV 100}', substr($lJobNr, -strlen($lJobNr), -2), $lFilename);

      $lPath = str_replace('{JNR DIV 10}', substr($lJobNr, -strlen($lJobNr), -1), $lPath);
      $lFilename = str_replace('{JNR DIV 10}', substr($lJobNr, -strlen($lJobNr), -1), $lFilename);

      $lPath = str_replace('{JNR}', $lJobNr, $lPath);
      $lFilename = str_replace('{JNR}', $lJobNr, $lFilename);
      // TODO END: implement tokenizer

      return array(
        'preview.path' => $lPath,
        'preview.filename' => $lFilename
      );
    } else {
      return FALSE;
    }
  }
}