<?php
/**
 * Which document types are available for a given job?
 *
 * @author g.emmans@5flow.eu
 *
 */
class CInc_Job_Fil_Folders extends CCor_Obj implements IteratorAggregate {

  /**
   * Constructor set src and jobid
   * @param string $aSrc
   * @param string $aJobId
   */
  public function __construct($aSrc, $aJobId, $aAge = 'job') {
    $this -> mSrc = $aSrc;
    $this -> mJid = $aJobId;
    $this -> mAge = ($aAge != 'job') ? 'arc' : 'job';

    $this -> mFolder = array();
    $this -> init();
  }

  protected function init() {
    $lCfg = CCor_Cfg::getInstance();
    $lProv = $lCfg -> getFallback('job-'.$this -> mSrc.'.files.providers', 'job.files.providers');

    if (is_null($lProv)) {
      $lProv = $this -> getDefaultProviders();
    }
    foreach ($lProv as $lKey) {
      if (!self::canRead($lKey)) {
        continue;
      }
      $this -> add($lKey, lan('job-fil.'.$lKey));
    }
  }

  protected function getDefaultProviders() {
    $lCfg = CCor_Cfg::getInstance();

    if (('pro' != $this -> mSrc) && ('sku' != $this -> mSrc)) {
      if ($lCfg -> get('webcenter.available', true)) {
        if (self::canRead('wec')) {
          $lRet[] = 'wec';
        }
      }
      if ($lCfg -> get('dalim.available')) {
        if (self::canRead('dalim')) {
          $lRet[] = 'dalim';
        }
      }
      if ($lCfg -> get('dms.available')) {
        if (self::canRead('dms')) {
          $lRet[] = 'dms';
        }
      }
      if ($lCfg -> get('cloudflow.available')) {
        if (self::canRead('cloudflow')) {
          $lRet[] = 'cloudflow';
        }
      }
      if ($lCfg -> get('job.files.show.pdf', true)) {
        if (self::canRead('pdf')) {
          $lRet[] = 'pdf';
        }
      }

      if ($lCfg -> get('globalvision.available')) {
        if (self::canRead('gv')) {
          $lRet[] = 'gv';
        }
      }
    }

    // START #818 job-doc right does not work
    if (self::canRead($this -> mAge.'-fil-doc')) {
      $lRet[] = 'doc';
    }
    // STOP #818 job-doc right does not work

    return $lRet;
  }

  public static function canRead($aSrc) {
    if ('doc' == $aSrc) {
      return true;
    }
    if ('job-fil-doc' == $aSrc) {
      return true;
    }
    if ('arc-fil-doc' == $aSrc) {
      return true;
    }
    if ('pdf' == $aSrc || 'gv' == $aSrc) {
      return true;
    }
    $lUsr = CCor_Usr::getInstance();
    if ('pixelboxx' == $aSrc) {
      return $lUsr -> canRead('job-pixelboxx');
    }
    if ('cart' == $aSrc) {
      return $lUsr -> canRead('job-pixelboxx');
    }
    if ('cloudflow' == $aSrc) {
      return $lUsr -> canRead('job-cloudflow');
    }
    if (in_array($aSrc, array('wec', 'dalim', 'dms'))) {
      return $lUsr -> canRead('job-wec');
    }
    return false;
  }

  public function add($aSrc, $aCaption = null) {
    $this -> mFolder[$aSrc] = $aCaption;
    return $this;
  }

  public function remove($aSrc) {
    unset($this -> mFolder[$aSrc]);
    return $this;
  }

  public function has($aSrc) {
    return isset($this -> mFolder[$aSrc]);
  }

  public function getFolders() {
    return $this -> mFolder;
  }

  public function getIterator() {
    return new ArrayIterator($this -> mFolder);
  }

  public function getViewer() {
    if ($this -> has('wec'))   return 'wec';
    if ($this -> has('dalim')) return 'dalim';
    if ($this -> has('dms'))   return 'dms';
    if ($this -> has('cloudflow')) return 'cloudflow';
    return false;
  }
}
