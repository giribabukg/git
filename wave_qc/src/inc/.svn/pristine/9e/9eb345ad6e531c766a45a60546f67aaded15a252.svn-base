<?php
class CInc_Svc_Wec extends CCor_Obj {

  protected $mPreviewKeys = array(
    'wec-pi.fetch_subfolder',
    'wec-pi.fetch_number',
    'wec-pi.fetch_log',
    'wec-pi.sync_subfolder',
    'wec-pi.sync_number',
    'wec-pi.sync_log',
    'wec-pi.image_subfolder',
    'wec-pi.image_name',
    'wec-pi.image_notfound',
    'wec-pi.thumbnail_subfolder',
    'wec-pi.thumbnail_name',
    'wec-pi.thumbnail_notfound'
  );
  protected $mPreviewArray;
  protected $mPreviewImageNotFound;
  protected $mThumbnailNotFound;

  protected $mClients;

  protected static $mInstance;

  public function __construct() {
    $this -> mClients = array();
    $lQuery = new CCor_Qry('SELECT id, code FROM al_sys_mand WHERE id NOT IN (0, 998, 999, 1000) ORDER BY id;');
    foreach ($lQuery as $lRow) {
      $this -> mClients[$lRow['id']] = $lRow['code'];
    }

    $this -> mPreviewArray = array();
    $this -> mPreviewArray['wec-pi.fetch_subfolder'] = 'tmp/';
    $this -> mPreviewArray['wec-pi.fetch_number'] = 10;
    $this -> mPreviewArray['wec-pi.fetch_log'] = False;
    $this -> mPreviewArray['wec-pi.sync_subfolder'] = 'tmp/';
    $this -> mPreviewArray['wec-pi.sync_number'] = 10;
    $this -> mPreviewArray['wec-pi.sync_log'] = False;

    $this -> mPreviewArray['wec-pi.image_path'] = getcwd();
    if (substr($this -> mPreviewArray['wec-pi.image_path'], -1) != '/') {
      $this -> mPreviewArray['wec-pi.image_path'].= '/';
    }

    $this -> mPreviewArray['wec-pi.image_subfolder'] = '{JNR DIV 1000}/{JNR}/doc/';
    $this -> mPreviewArray['wec-pi.image_name'] = '{JNR}.jpg';
    $this -> mPreviewArray['wec-pi.image_notfound'] = 'tmp/sorry.jpg';

    $this -> mPreviewArray['wec-pi.thumbnail_path'] = getcwd();
    if (substr($this -> mPreviewArray['wec-pi.thumbnail_path'], -1) != '/') {
      $this -> mPreviewArray['wec-pi.thumbnail_path'].= '/';
    }

    $this -> mPreviewArray['wec-pi.thumbnail_subfolder'] = '{JNR DIV 1000}/{JNR}/doc/';
    $this -> mPreviewArray['wec-pi.thumbnail_name'] = '{JNR}ico.jpg';
    $this -> mPreviewArray['wec-pi.thumbnail_notfound'] = 'tmp/sorryico.jpg';

    $lKeys = implode('","', $this -> mPreviewKeys);
    $lQry = new CCor_Qry('SELECT code,val FROM al_sys_pref WHERE code IN ("'.$lKeys.'")');
    foreach ($lQry as $lKey => $lValue) {
      $lValue['val'] = str_replace("\\", "/", $lValue['val']);
      $this -> mPreviewArray[$lValue['code']] = $lValue['val'];
    }

    $this -> mPreviewArray['getcwd'] = str_replace("\\", "/", getcwd());
    if (substr($this -> mPreviewArray['getcwd'], -1, 1) != "/" && strlen($this -> mPreviewArray['getcwd']) >= 1) {
      $this -> mPreviewArray['getcwd'].= "/";
    }

    $this -> mPreviewImageNotFound = $this -> mPreviewArray['wec-pi.image_notfound'];
    $this -> mThumbnailNotFound = $this -> mPreviewArray['wec-pi.thumbnail_notfound'];
  }

  public function __destruct() {
    self::$mInstance = NULL;
  }

  public static function getInstance(){
    if (NULL === self::$mInstance) {
      self::$mInstance = new self();
    }
    return self::$mInstance;
  }

  public function getDynamics($aJobID) {
    if (!empty($aJobID)) {
      $lJobNr = ltrim($aJobID, '0');

      $lImage_subfolder = str_replace('{JNR}', $lJobNr, $this -> mPreviewArray['wec-pi.image_subfolder']);
      $lImage_name = str_replace('{JNR}', $lJobNr, $this -> mPreviewArray['wec-pi.image_name']);
      $lThumbnail_subfolder = str_replace('{JNR}', $lJobNr, $this -> mPreviewArray['wec-pi.thumbnail_subfolder']);
      $lThumbnail_name = str_replace('{JNR}', $lJobNr, $this -> mPreviewArray['wec-pi.thumbnail_name']);

      // TODO START: tokenizer implementieren!
      $lImage_subfolder = str_replace('{MID ZEROFILL}', str_pad(MID, 4, "0", STR_PAD_LEFT), $lImage_subfolder);
      $lImage_name = str_replace('{MID ZEROFILL}', str_pad(MID, 4, "0", STR_PAD_LEFT), $lImage_name);
      $lThumbnail_subfolder = str_replace('{MID ZEROFILL}', str_pad(MID, 4, "0", STR_PAD_LEFT), $lThumbnail_subfolder);
      $lThumbnail_name = str_replace('{MID ZEROFILL}', str_pad(MID, 4, "0", STR_PAD_LEFT), $lThumbnail_name);

      $lImage_subfolder = str_replace('{MID}', MID, $lImage_subfolder);
      $lImage_name = str_replace('{MID}', MID, $lImage_name);
      $lThumbnail_subfolder = str_replace('{MID}', MID, $lThumbnail_subfolder);
      $lThumbnail_name = str_replace('{MID}', MID, $lThumbnail_name);

      $lImage_subfolder = str_replace('{MAND}', $this -> mClients[MID], $lImage_subfolder);
      $lImage_name = str_replace('{MAND}', $this -> mClients[MID], $lImage_name);
      $lThumbnail_subfolder = str_replace('{MAND}', $this -> mClients[MID], $lThumbnail_subfolder);
      $lThumbnail_name = str_replace('{MAND}', $this -> mClients[MID], $lThumbnail_name);

      $lImage_subfolder = str_replace('{JNR DIV 100000}', substr($lJobNr, -strlen($lJobNr), -5), $lImage_subfolder);
      $lImage_name = str_replace('{JNR DIV 100000}', substr($lJobNr, -strlen($lJobNr), -5), $lImage_name);
      $lThumbnail_subfolder = str_replace('{JNR DIV 100000}', substr($lJobNr, -strlen($lJobNr), -5), $lThumbnail_subfolder);
      $lThumbnail_name = str_replace('{JNR DIV 100000}', substr($lJobNr, -strlen($lJobNr), -5), $lThumbnail_name);

      $lImage_subfolder = str_replace('{JNR DIV 10000}', substr($lJobNr, -strlen($lJobNr), -4), $lImage_subfolder);
      $lImage_name = str_replace('{JNR DIV 10000}', substr($lJobNr, -strlen($lJobNr), -4), $lImage_name);
      $lThumbnail_subfolder = str_replace('{JNR DIV 10000}', substr($lJobNr, -strlen($lJobNr), -4), $lThumbnail_subfolder);
      $lThumbnail_name = str_replace('{JNR DIV 10000}', substr($lJobNr, -strlen($lJobNr), -4), $lThumbnail_name);

      $lImage_subfolder = str_replace('{JNR DIV 1000}', substr($lJobNr, -strlen($lJobNr), -3), $lImage_subfolder);
      $lImage_name = str_replace('{JNR DIV 1000}', substr($lJobNr, -strlen($lJobNr), -3), $lImage_name);
      $lThumbnail_subfolder = str_replace('{JNR DIV 1000}', substr($lJobNr, -strlen($lJobNr), -3), $lThumbnail_subfolder);
      $lThumbnail_name = str_replace('{JNR DIV 1000}', substr($lJobNr, -strlen($lJobNr), -3), $lThumbnail_name);

      $lImage_subfolder = str_replace('{JNR DIV 100}', substr($lJobNr, -strlen($lJobNr), -2), $lImage_subfolder);
      $lImage_name = str_replace('{JNR DIV 100}', substr($lJobNr, -strlen($lJobNr), -2), $lImage_name);
      $lThumbnail_subfolder = str_replace('{JNR DIV 100}', substr($lJobNr, -strlen($lJobNr), -2), $lThumbnail_subfolder);
      $lThumbnail_name = str_replace('{JNR DIV 100}', substr($lJobNr, -strlen($lJobNr), -2), $lThumbnail_name);

      $lImage_subfolder = str_replace('{JNR DIV 10}', substr($lJobNr, -strlen($lJobNr), -1), $lImage_subfolder);
      $lImage_name = str_replace('{JNR DIV 10}', substr($lJobNr, -strlen($lJobNr), -1), $lImage_name);
      $lThumbnail_subfolder = str_replace('{JNR DIV 10}', substr($lJobNr, -strlen($lJobNr), -1), $lThumbnail_subfolder);
      $lThumbnail_name = str_replace('{JNR DIV 10}', substr($lJobNr, -strlen($lJobNr), -1), $lThumbnail_name);
      // TODO END: tokenizer implementieren!

      return array(
        'thumbnail_dir' => $this -> mPreviewArray['wec-pi.fetch_subfolder'].$lThumbnail_subfolder,
        'thumbnail_file' => $lThumbnail_name,
        'image_dir' => $this -> mPreviewArray['wec-pi.fetch_subfolder'].$lImage_subfolder,
        'image_file' => $lImage_name,
      );
    } else {
      return false;
    }
  }

  public function getStatics() {
    return array(
      'getcwd' => $this -> mPreviewArray['getcwd'],
      'fetch_subfolder' => $this -> mPreviewArray['wec-pi.fetch_subfolder'],
      'fetch_number' => $this -> mPreviewArray['wec-pi.fetch_number'],
      'sync_subfolder' => $this -> mPreviewArray['wec-pi.sync_subfolder'],
      'sync_number' => $this -> mPreviewArray['wec-pi.sync_number'],
      'thumbnail_not_found' => $this -> mThumbnailNotFound,
      'image_not_found' => $this -> mPreviewImageNotFound
    );
  }

}