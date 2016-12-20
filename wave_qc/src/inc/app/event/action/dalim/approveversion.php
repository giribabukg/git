<?php
/**
 * When a job is approved, lookup the current Dalim version and store that in
 * the job field "version" so users can see the approved version number.
 * @author gemmans
 *
 */
class CInc_App_Event_Action_Dalim_Approveversion extends CApp_Event_Action {

  const VERSION_DELIMITER = '_';

  public function execute() {
    $lVer = $this -> getHighestVersion();
    if (empty($lVer)) return true;

    $lRet = $this -> updateJob(array('version' => $lVer));
    return $lRet;
  }

  protected function getHighestVersion() {
    $lJob = $this -> mContext['job'];
    $lJid = $lJob -> getId();
    $lSrc = $lJob -> getSrc();

    $lCls = new CApp_Finder($lSrc, $lJid);
    $lDir = $lCls -> getPath('dalim');

    if (!file_exists($lDir)) return 0;

    $lMax = 0;

    try {
      $lIte = new DirectoryIterator($lDir);
      foreach ($lIte as $lLin) {
        $lItm = array();
        if (!$lIte -> isFile()) continue;
        $lNam = $lIte -> getFilename();
        if (intval($lNam) != $lJid) continue;
        $lPos = strrpos($lNam, self::VERSION_DELIMITER);
        if (false === $lPos) continue;
        $lVersion = intval(substr($lNam, $lPos + 1));
        if ($lVersion > $lMax) $lMax = $lVersion;
      }
    } catch (Exception $ex) {
      $this -> dbg($ex -> getMessage());
    }

    return $lMax;
  }

  protected function updateJob($aArr) {
    $lJob = $this -> mContext['job'];
    $lJid = $lJob -> getId();
    $lSrc = $lJob -> getSrc();

    $lFac = new CJob_Fac($lSrc, $lJid, $lJob);
    $lMod = $lFac -> getMod($lJid);
    return $lMod -> forceUpdate($aArr);
  }
}