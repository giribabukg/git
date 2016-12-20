<?php
class CInc_Xchange_Jobfields extends CCor_Dat {
  
  protected $mJobFields;
  protected $mTableFields;
  protected $mSearchFields;
  protected $mSelectJobFields;
  
  public function __construct() {
    $this->mJobFields = CCor_Res::getByKey('alias', 'fie');
    $this->mTableFields = array();
    $this->mSearchFields = array();
    $this->mSelectJobFields = array();
    $this->init();
  }
  
  protected function init() {
    // setup fields here
  }
  
  public static function getSearchField() {
    return '';
  }
  
  /**
   * Add a single field to the Integrate job field dictionary
   * @param string $aAlias Unique alias of the field (should be the same as a real job field)  
   * @param string $aTagname Tagname to extract this data from
   * @param string $aCaption Optional - Caption displayed in list. Will use job field caption otherwise
   * @param string $aFormatter Optional - if we need special formatting during the import process, specify here
   */
  protected function addField($aAlias, $aTagname, $aCaption = null, $aFormatter = null) {
    $lItm['alias'] = $aAlias;
    $lItm['tag'] = $aTagname;
    $lCaption = $aCaption;
    if (is_null($aCaption)) {
      if (isset($this->mJobFields[$aAlias])) {
        $lCaption = $this->mJobFields[$aAlias]['name_'.LAN];
      }
    }
    $lItm['caption'] = $lCaption;
    $lItm['fmt'] = $aFormatter;
    
    $this->mVal[$aAlias] = $lItm;
  }
  
  /**
   * Get fields that Integrate needs to manage rows
   * @return array
   */
  public function getInternalFields() {
    $lRet = array();
    $lRet['x_src'] = 'Job Type';
    $lRet['x_jobid'] = 'JobID';
    $lRet['x_status'] = 'Status';
    $lRet['x_import_date'] = 'Import';
    $lRet['x_assign_date'] = 'Assigned';
    $lRet['x_update_date'] = 'Updated';
    $lRet['x_xml'] = 'XML';
    return $lRet;
  }
  
  public function getFieldSql() {
    $lRet = '';
    $lFields = $this->getFields();
    foreach ($this->mVal as $lAlias => $lRow) {
      $lType = 'VARCHAR(100)';
      if ($lRow['fmt'] == 'bool') $lType = 'CHAR(1)';
      $lSql = 'ALTER TABLE al_xchange_jobs_'.MID.' ADD '.$lAlias.' '.$lType.';'.LF;
      #CCor_Qry::exec($lSql);
      $lRet.= $lSql.BR;
    }
    return $lRet;
  }
  
  protected function paramToArray($aAlias) {
    $lAlias = $aAlias;
    if (is_string($lAlias) && ('*' == $lAlias)) {
      foreach ($this->mVal as $lKey => $lVal) {
        $lRet[$lKey] = $lKey;
      }
      return $lRet;
    }
    if (is_string($lAlias) && false !== strpos($lAlias, ',')) {
      $lAlias = explode(',', $lAlias);
    }
    if (is_array($lAlias)) {
      foreach ($lAlias as $lVal) {
        $lRet[$lVal] = $lVal;
      }
    } else {
      $lRet[$aAlias] = $aAlias;
    }
    return $lRet;
  }
  
  /**
   * Add a single field or several fields to the Integrate job list
   * Will be used in the columns for the Integrate job list
   * @param string|array $aAlias A single alias, an array of aliases or a comma-separated list of aliases
   */
  public function addToTable($aAlias) {
    $lFields = $this->paramToArray($aAlias); 
    foreach ($lFields as $lVal) {
      $this->mTableFields[$lVal] = $lVal;
    }
  }
  
  public function removeFromTable($aAlias) {
    $lFields = $this->paramToArray($aAlias); 
    foreach ($lFields as $lVal) {
      unset($this->mTableFields[$lVal]);
    }
  }
  
  protected function getTableAliases() {
    return array_keys($this->mTableFields);
  }
  
  public function getTableFields() {
    $lRet = array();
    $lArr = $this->getTableAliases();
    foreach ($lArr as $lAlias) {
      if (!isset($this->mVal[$lAlias])) {
        continue;
      }
      $lRow = $this->mVal[$lAlias];
      $lRet[$lAlias] = $lRow['caption'];
    }
    return $lRet;
  }
  
  /**
   * Add a single field or several fields to the search fields
   * Will be used in the integrate job list search field
   * @param string|array $aAlias A single alias, an array of aliases or a comma-separated list of aliases
   */
  public function addToSearch($aAlias) {
    $lFields = $this->paramToArray($aAlias);
    foreach ($lFields as $lVal) {
      $this->mSearchFields[$lVal] = $lVal;
    }
  }
  
  public function removeFromSearch($aAlias) {
    $lFields = $this->paramToArray($aAlias);
    foreach ($lFields as $lVal) {
      unset($this->mSearchFields[$lVal]);
    }
  }
  
  protected function getSearchAliases() {
    return array_keys($this->mSearchFields);
  }
  
  public function getSearchFields() {
    $lRet = array();
    $lArr = $this->getSearchAliases();
    foreach ($lArr as $lAlias) {
      if (!isset($this->mVal[$lAlias])) {
        continue;
      }
      $lRow = $this->mVal[$lAlias];
      $lRet[$lAlias] = $lRow['caption'];
    }
    return $lRet;
  }
  
  /**
   * Add a single field or several fields to the select job list
   * Will be used in the integrate select job list
   * Fields do not have to be part of the other integrate fields, can be any job field
   * But adding '*' will still add all integrate fields 
   * (after all, these will be the most interesting ones)
   * @param string|array $aAlias A single alias, an array of aliases or a comma-separated list of aliases
   */
  public function addToSelect($aAlias) {
    $lFields = $this->paramToArray($aAlias);
    foreach ($lFields as $lVal) {
      $this->mSelectJobFields[$lVal] = $lVal;
    }
  }
  
  public function removeFromSelect($aAlias) {
    $lFields = $this->paramToArray($aAlias);
    foreach ($lFields as $lVal) {
      unset($this->mSelectJobFields[$lVal]);
    }
  }
  
  protected function getSelectAliases() {
    return array_keys($this->mSelectJobFields);
  }
  
  public function getSelectFields() {
    $lRet = array();
    $lArr = $this->getSelectAliases();
    foreach ($lArr as $lAlias) {
      if (!isset($this->mJobFields[$lAlias])) {
        continue;
      }
      $lRow = $this->mJobFields[$lAlias];
      $lRet[$lAlias] = $lRow['name_'.LAN];
    }
    return $lRet;
  }
  
}