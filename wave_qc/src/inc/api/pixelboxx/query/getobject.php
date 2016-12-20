<?php
class CInc_Api_Pixelboxx_Query_Getobject extends CApi_Pixelboxx_Query {
  
  protected function init() {
    $this->mMethod = 'getObject';
  }
  
  public function getThumb($aDoi) {
    $this->setParam('ObjectId', $aDoi);
    
    $lAtt = array('type' => 'thumb', 'location' => 'include', 'mandatory' => 'true');
    $lRaw = array('mandatory' => 'true', 'WantedRawData' => $lAtt);
    $this->setParam('WithRawData', $lRaw);
    
    $lRes = $this->query($this->mMethod, $this->mParam);
    
    if (!$this->hasPath($lRes, 'Object.RawDataList.RawData.Location.IncludedData')) {
      return false;
    }
    return $lRes->Object->RawDataList->RawData->Location->IncludedData;
  }
  
  public function getMetadata($aDoi) {
    // @TODO: Set language? $lLang = (empty($aLang)) ? LAN : $aLang;
    $this->setParam('ObjectId', $aDoi);
    $lAtt = array('attributes' => 'all', 'accesscontrol' => 'false', 'mandatory' => 'false');
    $this->setParam('WithMetadata', $lAtt);
    
    $lRes = $this->query($this->mMethod, $this->mParam);
    
    if (!$this->hasPath($lRes, 'Object.Attributes.A')) {
      return false;
    }
    $lRows = $lRes->Object->Attributes->A;
    $lRet = new CCor_Dat();
    foreach ($lRows as $lRow) {
      $lRet[$lRow->n] = $lRow->{'_'}; 
    }
    return $lRet;
  }
  
  public function download($aDoi) {
    $this->setParam('ObjectId', $aDoi);
    
    $lAtt = array('type' => 'fine', 'location' => 'include', 'mandatory' => 'true');
    $lRaw = array('mandatory' => 'true', 'WantedRawData' => $lAtt);
    $this->setParam('WithRawData', $lRaw);
    
    // @TODO: Refine - only fetch mime type & file name
    $lAtt = array('attributes' => 'all', 'accesscontrol' => 'false', 'mandatory' => 'false');
    $this->setParam('WithMetadata', $lAtt);
        
    $lRes = $this->query($this->mMethod, $this->mParam);
    
    if (!$this->hasPath($lRes, 'Object.Attributes.A')) {
      return false;
    }
    $lRows = $lRes->Object->Attributes->A;
    $lRet = new CCor_Dat();
    foreach ($lRows as $lRow) {
      $lRet[$lRow->n] = $lRow->{'_'};
    }
    
    if (!$this->hasPath($lRes, 'Object.RawDataList.RawData.Location.IncludedData')) {
      return false;
    }
    $lRet['file_data'] = $lRes->Object->RawDataList->RawData->Location->IncludedData;
    return $lRet;
  }

}