<?php
class CInc_Mnd_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_sys_mand');
    $this -> addField(fie('id'));
    $this -> addField(fie('code'));
    $this -> addField(fie('name_en'));
    $this -> addField(fie('name_de'));
    $this -> addField(fie('pass','','hidden'));
    $this -> mAutoInc = FALSE;
  }

  protected function afterPost($aNew) {
  	if ($aNew == TRUE) {
#  		$lSrc = $_POST[val][copy];
		$lSrcMand = $this -> getReqVal('copy');
  		$lDstMand = $this -> getReqVal('id');
  		$lCode = $this -> getReqVal('code');
  		$lrepNr = $this -> getReqVal('repNr');
  		$lartNr = $this -> getReqVal('artNr');
  		$lappName = $this -> getReqVal('appName');  		
	  	$lSrc="mand/mand_".$lSrcMand;
	  	$lDst="mand/mand_".$lDstMand;
	  	$lPath = $lDst."/inc/cor/cfg.php";
	  	
	  	$lSql = 'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '.esc(CCor_Cfg::get('db.name')).' AND TABLE_NAME = "al_fie";';
	  	$lColumns = array();
	  	$lQry = new CCor_Qry($lSql);
	  	foreach ($lQry as $lKey => $lVal) {
	  	  if ($lVal['COLUMN_NAME'] == 'id' || $lVal['COLUMN_NAME'] == 'mand') continue;
	  	  $lColumns[] = $lVal['COLUMN_NAME'];
	  	}
	  	$lFieColumnNames = implode(',', $lColumns);
	  		
	  	$this -> copyMandFiles($lSrc, $lDst);
		$this -> readCfgFile($lPath, $lCode, $lappName, $lrepNr, $lartNr, $lDstMand);
  		
		if ($this -> getReqVal('sysPref') == TRUE) {
			$lSql = "INSERT INTO al_sys_pref (code, mand, grp, name_de, name_en, val) SELECT  code, ".$lDstMand.", grp, name_de, name_en, val FROM al_sys_pref WHERE mand=".$lSrcMand."";
			CCor_Qry::exec($lSql);
		}		
		if ($this -> getReqVal('alFie') == TRUE) {
			$lSql = 'INSERT INTO al_fie (mand, '.$lFieColumnNames.') SELECT  '.$lDstMand.', '.$lFieColumnNames.' FROM al_fie WHERE mand='.$lSrcMand;
			CCor_Qry::exec($lSql);
		}
  	}
  	$lEnc = CApp_Pwd::encryptPassword($this -> getReqVal('pass'));
  	$lPassCheck = CCor_Qry::getInt('SELECT id FROM al_sys_mand WHERE pass='.esc($lEnc));
  	if ($lPassCheck) {
  	  $this -> msg('Password is already in use, please choose another password',mtUser,mlError);
  	  CCor_Qry::exec('UPDATE `al_sys_mand` SET `pass`="" WHERE  `id`='.$this -> getReqVal('id').' LIMIT 1;');
  	}
  	else CCor_Qry::exec('UPDATE `al_sys_mand` SET `pass`='.esc($lEnc).' WHERE  `id`='.$this -> getReqVal('id').' LIMIT 1;');
 }
 
 /**
  * Detect the define('', '') Lines in the CFG File and replace them with the below parameters
  * 
  * @param $aPath
  * @param $aCode
  * @param $aAppName
  * @param $arepNr
  * @param $aArtNr
  * @param $aDstMand
  */
 protected function readCfgFile($aPath, $aCode, $aAppName, $arepNr, $aArtNr, $aDstMand) {
		$lLines = array();
		foreach (new SplFileObject($aPath) as $lineNumber => $lineContent) {			
        	if (FALSE !== strpos($lineContent, "define('MANDATOR',")) {
			$lLines['mandCode'] = $lineContent; }
		    if (FALSE !== strpos($lineContent, "define('MANDATOR_NAME',")) {
			$lLines['mandName'] = $lineContent; }
		    if (FALSE !== strpos($lineContent, "define('KNR_REP',")) {
			$lLines['repN'] = $lineContent; }
		    if (FALSE !== strpos($lineContent, "define('KNR_ART',")) {
			$lLines['artN'] = $lineContent; }
		    if (FALSE !== strpos($lineContent, "define('NEW_MID',")) {
			$lLines['mandID'] = $lineContent; }
		}
  		$this ->updateCfgFile($aPath, $lLines['mandCode'], "define('MANDATOR', 			'".$aCode."');\n");
		$this ->updateCfgFile($aPath, $lLines['mandName'], "define('MANDATOR_NAME', '".$aAppName."');\n");
		$this ->updateCfgFile($aPath, $lLines['repN'], "define('KNR_REP', 			'".$arepNr."');\n");
		$this ->updateCfgFile($aPath, $lLines['artN'], "define('KNR_ART', 			'".$aArtNr."');\n");
		$this ->updateCfgFile($aPath, $lLines['mandID'], "define('NEW_MID', 		  ".$aDstMand.");\n");
}

/**
 * Copy directory, contents and all subdirectory recursively from a to b
 * 
 * @param $aSrc (The source directory to be copied)
 * @param $aDst (The Target path to copy the Source directory in it)
 * @param $excludeSvnFolders (TRUE = don't copy .svn directories to the target Dir.)
 */
 protected function copyMandFiles($aSrc,$aDst, $excludeSvnFolders=true) { 
    $dir = opendir($aSrc); 
    @mkdir($aDst); 
    while(false !== ( $file = readdir($dir)) ) {
        if ($file == '.' || $file == '..')
      continue;
    if ($excludeSvnFolders && $file == '.svn')
      continue; 
            if ( is_dir($aSrc . '/' . $file) ) {
                self::copyMandFiles($aSrc . '/' . $file,$aDst . '/' . $file, $excludeSvnFolders); 
            } 
            else { 
                copy($aSrc . '/' . $file,$aDst . '/' . $file); 
            }     
    }
    closedir($dir);  
}

/**
 * Replace a certain string in a certain File.
 *  
 * @param $aPath (The path of the file to replace the string in it)
 * @param $aString (The String to be replaced)
 * @param $aReplace (The new String that should be written)
 */
 protected function updateCfgFile($aPath, $aString, $aReplace) {
    set_time_limit(0);
    if (is_file($aPath) === true) {
        $file = fopen($aPath, 'r');
        $temp = tempnam('./', 'tmp');
        if (is_resource($file) === true) {
            while (feof($file) === false) {
                file_put_contents($temp, str_replace($aString, $aReplace, fgets($file)), FILE_APPEND);
            }
            fclose($file);
        }
        unlink($aPath);
    }
    return rename($temp, $aPath);
	}
}