<?php
class CInc_Api_Pixelboxx_Query_Extendedsearch extends CApi_Pixelboxx_Query {
  
  protected function init() {
    $this->mMethod = 'extendedSearch';
    $this->mAttr = array ();
    #$this->addAttribute ( 'size', 'filesize' );
    #$this->addAttribute ( 'name', 'filename' );
    #$this->addAttribute ( 'date', 'importdate' );
    $this->addThumbOption();
  }
  
  public function addAttribute($aAlias, $aNative) {
    $this->mAttr [$aAlias] = $aNative;
  }
  
  public function addThumbOption() {
    $lAtt = array('type' => 'thumb', 'location' => 'include', 'mandatory' => 'true');
    $lRaw = array('mandatory' => 'true', 'WantedRawData' => $lAtt);
    $this->mParam['SearchParams']['WithRawData'] = $lRaw;
  }
  
  public function addMetaOption() {
    // @TODO: Set language? $lLang = (empty($aLang)) ? LAN : $aLang;
    $lAtt = array('attributes' => 'all', 'accesscontrol' => 'false', 'mandatory' => 'false');
    $this->mParam['SearchParams']['WithMetadata'] = $lAtt;
  }
  
  public function setFolder($aFolderDoi) {
    $this->mParam['SearchParams']['FolderId'] = $aFolderDoi;
  }
  
  /**
   * 
   * @param array $aTerm An Array representation of one term (but can be nested in AND/OR)
   */
  public function setTerm($aTerm) {
    $this->mParam['SearchTerm'] = $aTerm;
  }
  
  public function getTermString($aValue, $aOp = 'eq') {
    $lOpt = array();
    $lOpt['op']   = $aOp;
    $lOpt['_'] = $aValue;
    $lRet = array('StringTerm' => $lOpt);
    return $lRet;
  }
  
  public function getTermAttribute($aAttribute, $aOp = 'eq', $aValue = null) {
    $lOpt = array();
    $lOpt['name'] = $aAttribute;
    $lOpt['op']   = $aOp;
    $lOpt['_'] = $aValue;
    $lRet = array('AttributeTerm' => $lOpt);
    return $lRet;
  }
  
  public function getTermAnd($aSubtermArray) {
    $lOpt = array();
    $lOpt['SearchTerm'] = $aSubtermArray;
    $lRet = array('AndTerm' => $lOpt);
    return $lRet;
  }
  
  public function getTermFolder($aFolderDoi, $aRecursive = true) {
    $lOpt = array();
    $lOpt['Folder'] = $aFolderDoi;
    $lOpt['recursive'] = !!$aRecursive; // typecast to bool
    $lRet = array('InFolderTerm' => $lOpt);
    return $lRet;
  }  
  protected function addAttributeTag() {
    $lAtt = array ();
    foreach ( $this->mAttr as $lKey => $lVal ) {
      $lItm = array (
          'mandatory' => 'false',
          '_' => $lVal 
      );
      $lAtt [] = $lItm;
    }
    $lTag = array (
        'attributes' => 'explicit',
        'accesscontrol' => 'false',
        'mandatory' => 'false',
        'Attribute' => $lAtt 
    );
    $this->mParam ['WithMetadata'] = $lTag;
  }
  
  public function getSearch($aFolderId = null) {
    #$this->setParam ( 'FolderId', $aFolderId );
    if (! empty ( $this->mAttr )) {
      #$this->addAttributeTag ();
    }
    $lRes = $this->query ( $this->mMethod, $this->mParam );
    //return $lRes;
    if (! $this->hasPath ( $lRes, 'Folder.Content.Item' )) {
      return array();
    }
    
    $lRet = array ();
    $lRoot = $lRes->Folder->Content->Item;
    if ($this->hasPath ( $lRoot, 'Object' )) {
      $lRoot = array (
          $lRoot 
      );
    }
    foreach ( $lRoot as $lRow ) {
      //var_dump($lRow);
      $lItm = array ();
      $lItm ['name'] = ( string ) $lRow->Object->name;
      $lItm ['doi'] = ( string ) $lRow->Object->doi;
      if (isset ( $lRow->Object->Attributes->A )) {
        $lAttr = $lRow->Object->Attributes->A;
        foreach ( $lAttr as $lAtt ) {
          $lNat = $lAtt->n;
          $lVal = ( string ) $lAtt->{'_'};
          if ($lKey = array_search ( $lNat, $this->mAttr )) {
            if ('importdate' == $lNat) {
              $lVal = strtotime ( $lVal );
            }
            $lItm [$lKey] = $lVal;
          }
        }
      }
      if ($this->hasPath($lRow, 'Object.Attributes.A')) {
        $lMeta = $lRow->Object->Attributes->A;
        foreach ($lMeta as $lMetaRow) {
          $lItm['meta.'.$lMetaRow->n] = $lMetaRow->{'_'};
        }
      }
      if ($this->hasPath($lRow, 'Object.RawDataList.RawData.Location.IncludedData')) {
        $lItm['image'] = $lRow->Object->RawDataList->RawData->Location->IncludedData;
      }
      #if (! empty ( $this->mAttr )) {
      #  foreach ( $this->mAttr as $lKey => $lVal ) {
      #  }
      #}
      $lRet [] = $lItm;
    }
    // var_dump($lRet);
    return $lRet;
  }
}