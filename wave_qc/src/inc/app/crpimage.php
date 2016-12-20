<?php
class CInc_App_Crpimage extends CCor_Obj {

  public static function getCriticalPathSrc($aCrpId){
    $lQry = new CCor_Qry('SELECT code FROM al_crp_master WHERE id='.$aCrpId.' AND mand='.MID);
    $lResults = $lQry -> getAssoc();
    if (FALSE !== $lResults) {
      return $lResults['code'];
    } else {
      return '';
    }
  }

  public static function getColourForSrc($aSrc){
    $lColours = CCor_Cfg::get('theme.colours');

    if(THEME === 'default'){
      return $aSrc;
    } else {
      return (array_key_exists($aSrc, $lColours) ? $lColours[$aSrc] : 'navy');
    }
  }

  /**
   * Get the absolute path of a given Source
   * @param string Subpath
   * @return string Absolute path
   */
  public static function getSrcPath($aSrc, $aPath) {
    $lImgDir = array(MAND_PATH_IMG, CUST_PATH_IMG, THEME_PATH_IMG, 'img'.DS);
    $lFound = FALSE;

    if (0 === strpos($aPath,'mand/mand_'.MID.'/')) {
      $aPath = str_replace_once('mand/mand_'.MID.'/', '', $aPath);
    }
    if (0 === strpos($aPath,'cust/')) {
      $aPath = str_replace_once('cust/', '', $aPath);
    }
    if (0 === strpos($aPath,'img/crp/')) {
      $aPath = str_replace_once('img/crp/', '', $aPath);
    }
    
    $lSrcDir = (THEME === 'default' ? $aSrc : self::getColourForSrc($aSrc));
    foreach($lImgDir as $lDir) {
      if (!$lFound) {
        $lFile = $lDir.'crp/'.$lSrcDir.'/'.$aPath; //first check src folder for image
        if (file_exists($lFile)) {
          $lFound = TRUE;
        } else {
          $lFile = $lDir.'crp/'.$aPath; //if src path not found check original path
          if (file_exists($lFile)) {
            $lFound = TRUE;
          }
        }
      }
    }

    if ($lFound) {
      return $lFile;
    } else {
      return '';
    }
  }
  /**
   * Get a specific CRP Icon
   * @param type $aSrc
   */
  public static function getCrpIco($aSrc, $aPath) {
    $lTheme = CCor_Cfg::get("theme.choice", "wave8");
    $lColor = self::getColourForSrc($aSrc);

    if($lTheme === "wave8") {
      return '<i class="ico-crp-'.$lTheme.'-'.$lColor.' ico-crp-'.$aPath.'"></i>';
    }
    else {
      return '<i class="ico-crp ico-crp-'.$aPath.'"></i>';
    }
  }
}