<?php
class CInc_App_Event_Action_Phrase_Export extends CApp_Event_Action {
  
  public function execute() {
    $this -> dbg('Phrase: Export Content');

    $lJob = $this -> mContext['job'];
    $lHotfolder = $this -> mParams['location'] . DS . 'out';
    $lJobId = $lJob -> jobid;
    $lSrc = $lJob -> src;
    
    $lPhraseFields = CCor_Cfg::get('job-cms.fields');
    $lLangKey = $lPhraseFields['languages'];
    $lLangs = $lJob[$lLangKey];
    
    // Filename
    $lMandArray = CCor_Res::extract('code', 'name_'.LAN, 'mand');
    $lMandName = str_replace(' ', '_', $lMandArray[MAND]);
    $lFileName = $lHotfolder . DS . lan('job-cms.menu') . '_' . $lJobId . '_' . date('Ymd_H-i-s') . '.xml';
    
    // Content
    $lJobCms = new CJob_Cms_Dat($lSrc);
    $lDat = $lJobCms -> load($lJobId);
    $lXml = $lJobCms -> export($lDat, $lLangs);
    $this -> exportAdd($lHotfolder, $lJob, $lLangs);
    
    $lRet = file_put_contents($lFileName, $lXml);
    return ($lRet !== FALSE) ? TRUE : FALSE;
  }

  public static function getParamDefs($aType) {
    $lArr = array();
    
    $lFie = fie('location', 'Hotfolder', 'input');
    $lArr[] = $lFie;
    
    return $lArr;
  }

  public static function paramToString($aParams) {
    $lRet = '';
    
    if (isset($aParams['location'])) {
      $lHotfolder = $aParams['location'];
      
      $lRet.= (isset($lHotfolder)) ? 'Export to: '.$lHotfolder : 'unknown';
    }
  
    return $lRet;
  }
  
  public function exportAdd($aHotfolder, $aJob, $aLangs) {
    return true;
  }

}