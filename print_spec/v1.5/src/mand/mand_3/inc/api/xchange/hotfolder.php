<?php
class CApi_Xchange_Hotfolder extends CCust_Api_Xchange_Hotfolder {

	const ACTION_OKAY = 1;
	const ACTION_IGNORE = 2;
	const ACTION_ERROR = 4;

	public function __construct($aParams){
		$this->mParams = $aParams;
	}

	public function getParam($aKey, $aDefault = null){
		return (isset($this->mParams[$aKey])) ? $this->mParams[$aKey] : $aDefault;
	}

	public function execute(){
		try{
			$lRet = $this->doImport();
		} catch(Exception $ex){
			$this->msg($ex->getMessage(), mtApi, mlError);
			$lRet = false;
		}
		return $lRet;
	}

	protected function doImport(){
		$lDir = $this->getParam('base').$this->getParam('folder');
		if(empty($lDir)){
			$this->msg('Hotfolder not defined', mtApi, mlError);
			return false;
		}
		if(! file_exists($lDir)){
			$this->msg('Specified hotfolder ' . $lDir . ' does not exist', mtApi, mlError);
			return false;
		}
		$lIte = new DirectoryIterator($lDir);
		foreach($lIte as $lFile){
			if(! $lIte->isFile())
				continue;
			$lName = $lIte->getPathname();
			$lRet = $this->handleFile($lName);
		}
		return true;
	}

	protected function handleFile($aFilename){
		$lRes = $this->parse($aFilename);
		if(! $lRes){
			$this->msg('XChange: Error parsing ' . $aFilename, mtApi, mlError);
			$this->moveFile($aFilename, 'error');
			return false;
		}
		$this->msg('XChange: Import of ' . $aFilename . ' successful', mtApi, mlInfo);
		$this->moveFile($aFilename, 'parsed');
		return true;
	}

	protected function moveFile($aFilename, $aFolderKey){
  	  $lDir = $this->getParam('base').$this->getParam($aFolderKey);
  	  $lBase = basename($aFilename);
  	  $lNewName = $lDir . DS . $lBase;
  	  rename($aFilename, $lNewName);
  	  copy($aFilename, $lNewName);
	}

	protected function parse($aFile){
	  $lParser = new CApi_Xchange_Xmlparser();
      $lXml = file_get_contents($aFile);
	  return (trim($lXml) == '') ? false : $lParser->parse($lXml, $aFile);
	}
}