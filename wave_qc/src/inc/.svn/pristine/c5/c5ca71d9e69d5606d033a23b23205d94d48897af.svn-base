<?php
class CInc_Api_Chili_Client extends CCor_Obj {

  protected $mJobid;
  protected $mService;
  protected $mKey;
  
  public function __construct($aJobId = null) {
    require_once 'inc/api/chili/service.php';
    $lWsdl = CCor_Cfg::get('chili.wsdlurl', "http://cpapp.matthewsbrandsolutions.co.uk/CHILI/main.asmx?wsdl");
    
    $this->mJobId = $aJobId;
    $this->mService = new main( $lWsdl );
    $this->mKey = $this->getApiKey();
  }
  
  /**
   * Get API key for chili service
   * @return string $lKey - API Key
   */
  protected function getApiKey() {
    try{
      $lApi = new GenerateApiKey();
      $lApi->environmentNameOrURL = CCor_Cfg::get('mand.usr');
      $lApi->userName = 'api';
      $lApi->password = 'api';
      $lResponse = $this->mService->GenerateApiKey($lApi);
       
      $lDom = new DOMDocument();
      $lDom->loadXML($lResponse->GenerateApiKeyResult);
      
      $lKey = $lDom->getElementsByTagName("apiKey")->item(0)->getAttribute("key");
      $lRes = $lDom->getElementsByTagName("apiKey")->item(0)->getAttribute("succeeded");
      
      return ($lRes === "true") ? $lKey : "";
    } catch(Exception $lExc){
      $this->msg( $lExc->getMessage(), mtAdmin, mlError);
    }
  }
	
  /**
   * Gets the Thumbnail of the selected template
   * @param string $aTemplateId - Template Document ID
   * @return string $lRet - Thumbnail URL
   */
  public function actGetTemplateThumb($aTemplateId) {
    try {
      $lThumb = new ResourceItemGetURL();
      $lThumb->apiKey = $this->mKey;
      $lThumb->resourceName = "Documents";
      $lThumb->itemID = $aTemplateId;
      $lThumb->type = "portal_thumbnail";
      $lThumb->pageNum = 1;
      $lResponse = $this->chiliservice->ResourceItemGetURL($lThumb);
      
      $lDom = new DOMDocument();
      $lDom->loadXML($lResponse->ResourceItemGetURLResult);
      
      $lEditorUrl = new DOMXPath($lDom);
      $lEditorUrl = $lEditorUrl->query("//urlInfo/@url");
      $lRet = $lEditorUrl->item(0)->nodeValue;
  
      return $lRet;
    } catch(Exception $lExc){
      $this->msg( $lExc->getMessage(), mtAdmin, mlError);
    }
  }
  
  /**
   * Generates the PDF created by the user
   * @param string $docID - Document ID
   */
  public function generatePdf($aTemplateId) {
    try {
      $lRet = "";
      $lItem = new ResourceItemGetXML();
      $lItem->apiKey = $this->mKey;
      $lItem->resourceName = "PdfExportSettings";
      $lItem->itemID = 'c3406d98-a591-42b3-b6da-7c0f9286ffaf'; //TODO: Get PDF Export ID
  
      $lVariables = $this->mService->ResourceItemGetXML($lItem);
      $lSettings = $lVariables->ResourceItemGetXMLResult;
      $lCreatePDF = new DocumentCreatePDF();
      $lCreatePDF->apiKey = $this->mKey;
      $lCreatePDF->itemID = $aTemplateId;
      $lCreatePDF->settingsXML = $lSettings;
      $lCreatePDF->taskPriority = 1;
  
      $lResponse = $this->mService->DocumentCreatePDF($lCreatePDF);
      $lDom = new DOMDocument();
      $lDom->loadXML($lResponse->DocumentCreatePDFResult);
  
      $lPath = new DOMXPath($lDom);
      $lItems = $lPath->query("//task");
      foreach($lItems as $lItem) {
        $lRet = $lItem->getAttribute("id");
      }
  
      return $lRet;
    } catch(Exception $lExc){
      $this->msg( $lExc->getMessage(), mtAdmin, mlError);
    }
  }
  
  /**
   * Gets the task status of a given PDF being generated
   * @param string $aTaskId - task id to keep track of when it is complete
   * @return array - Completion Status
   */
  public function actGetPdfStatus($aTaskId) {
    try {
      $lTaskStatus = new TaskGetStatus();
      $lTaskStatus->apiKey = $this->mKey;
      $lTaskStatus->taskID = $aTaskId;
      $lTask = $this->mService->TaskGetStatus($lTaskStatus);
      
      $lDom = new DOMDocument();
      $lDom->loadXML($lTask->TaskGetStatusResult);
  
      $lPath = new DOMXPath($lDom);
      $lItems = $lPath->query("//task");
      foreach($lItems as $lItem) {
        $lSuccess = $lItem->getAttribute("succeeded");
        $lFinish = $lItem->getAttribute("finished");
  
        $lMsg = $lStatus = "";
        if(strtolower($lFinish) === "true") {
          if(strtolower($lSuccess) === "true") { //finished & succeeded
            $lDom = new DOMDocument();
            $lDom->loadXML($lItem->getAttribute("result"));
            $lPath = new DOMXPath($lDom);
            $lResponse = $lPath->query("//result");
  
            $lUrl = "";
            foreach($lResponse as $lRes) {
              $lUrl = $lRes->getAttribute("url");
            }
            $lMsg = $lUrl;
            $lStatus = "success";
          } else { //error message
            $lMsg = $lPath->getAttribute("errorMessage");
            $lStatus = "fault";
          }
        }
      }
  
      return array($lMsg, $lStatus);
    } catch(Exception $lExc){
      $this->msg( $lExc->getMessage(), mtAdmin, mlError);
    }
  }
  
  public function getTemplateVariables($aTemplateId) {
    $lRet = array();
     
    $lItem = new ResourceItemGetXML();
    $lItem->apiKey = $this->mKey;
    $lItem->resourceName = "Documents";
	$lItem->itemID = $aTemplateId;
    $lResponse = $this->mService->ResourceItemGetXML($lItem);
  
    $lDom = new DOMDocument();
    $lDom->loadXML($lResponse->ResourceItemGetXMLResult);
    $lPath = new DOMXPath($lDom);
	
    $lVariables = $lPath->query("//variables/item");
    foreach($lVariables as $lVariable) {
      $lId = $lVariable->getAttribute("id");
      $lName = trim($lVariable->getAttribute("name"));
      $lVisible = $lVariable->getAttribute("visible");
	  $lDisplayName = $lVariable->getAttribute("displayName");
      $lLangName = strpos($lName, '_L');
      $lTag = explode('_', $lName);
      $lTag = $lTag[0];
      
      $lIndex = strpos($lTag, "lang");
      if($lIndex !== false) {
        $lRet['00'][$lTag][] = array("tag_id" => $lId, "display_name" => $lDisplayName, "name" => $lName);
      } else {
        if($lVisible !== "false") {
          $lSet = ($lLangName > -1 ? substr($lName, $lLangName+2, strlen($lName)) : "00");
          $lRet[$lSet][$lTag][] = array("tag_id" => $lId, "display_name" => $lDisplayName, "name" => $lName);
        }
      }
    }
     
    return $lRet;
  }

  /**
	* Show chili editor for template selected
	* @param string $docid - Document ID
	* @return array $data - Storing various information about editor and Document ID
	*/
  public function showEditor($aTemplateId) {
	try {
	    $lId = "";
	    $lAlt = $lRet = array();
	
	    $lInfo = new DocumentGetInfo();
	    $lInfo->apiKey = $this->mKey;
	    $lInfo->itemID = $aTemplateId;
	    $lInfo->extended = true;
	    $lResponse = $this->mService->DocumentGetInfo($lInfo);
	    
	    $lDom = new DOMDocument();
	    $lDom->loadXML($lResponse->DocumentGetInfoResult);
	    
	    $lPath = new DOMXPath($lDom);
	    $lAltLayouts = $lPath->query("//alternateLayouts/item");
	    for($lI = 0; $lI < $lAltLayouts->length; $lI++) {
	      $lAltId = $lAltLayouts->item($lI)->getAttribute("id");
	      $lAltName = $lAltLayouts->item($lI)->getAttribute("name");
	      
	      array_push($lAlt, array($lAltId, $lAltName));
	    }
	
	    $lItemCopy = new ResourceItemCopy();
	    $lItemCopy->apiKey = $this->mKey;
	    $lItemCopy->resourceName = "Documents";
	    $lItemCopy->itemID = $aTemplateId;
	    $lItemCopy->newName = $this->mJobId;
	    $lItemCopy->folderPath = "Orders";
	    $lResponse = $this->mService->ResourceItemCopy($lItemCopy);
	    
	    $lDom = new DOMDocument();
	    $lDom->loadXML($lResponse->ResourceItemCopyResult);
	    foreach($lDom->getElementsByTagName("item") as $lItem) {
	      $lIds = $lItem->getAttribute("id");
	      if(!empty($lIds)) {
	        $lId = $lIds;
	        break;
	      }
	    }
	
	    // set the workspace administration to false so that we are logged in as end-user&nbsp;
	    $lWorkspace = new SetWorkspaceAdministration();
	    $lWorkspace->apiKey = $this->mKey;
	    $lWorkspace->allowWorkspaceAdministration = true;
	    $this->mService->SetWorkspaceAdministration($lWorkspace);
	
	    // get the url of the document with specific workspace and viewpreferences
	    $lEditorUrl = new DocumentGetEditorURL();
	    $lEditorUrl->apiKey = $this->mKey;
	    $lEditorUrl->itemID = $lId;
	    $lEditorUrl->forAnonymousUser = false;
	    $lEditorUrl->constraintsID = "";//"46e2b6ba-cb07-4401-bab4-3ae4e8d0dfd8";
	    $lEditorUrl->viewPrefsID = "";
	    $lEditorUrl->viewerOnly = false;
	    $lEditorUrl->workSpaceID = "8a4bfe13-f520-4df8-99fc-2587e207b684";
	
	    // parse the xml result so that we can get the url return by the webservice
	    $lResponse = $this->mService->DocumentGetEditorURL($lEditorUrl);
	    $lDom = new DOMDocument();
	    $lDom->loadXML($lResponse->DocumentGetEditorURLResult);
	    $lPath = new DOMXPath($lDom);
	    $lUrl = $lPath->query("//urlInfo/@url");
	    $lValue = $lUrl->item(0)->nodeValue;
	
	    $lRet = array(
	      'template_id' => $lId,
	      'editorURL' => $lValue,
	      'alts' => $lAlts
	    );
	
	    return $lRet;
	  } catch(Exception $lExc){
        $this->msg( $lExc->getMessage(), mtAdmin, mlError);
      }
	}
}