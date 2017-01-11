<?php
abstract class CInc_Cms_Mod extends CCor_Mod_Base {
  
  protected function doInsert() { //used in main menu application
    return '';
  }
  
  protected function doUpdate() { //used in main menu application
    return '';
  }

  protected function doDelete($aId) { //used in main menu application
    return '';
  }

  /**
   * Check if the content string already exists in the database
   * @param string $aVal - content string
   * @return integer $lRet - content id or zero (not found)
   */
  public static function contentExist($aVal = "", $aLang = 'MA') {
    $lRet = 0;
    
    $lSql = 'SELECT * FROM `al_cms_content` WHERE `content`='.esc($aVal).' AND `mand`='.intval(MID);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lSql = 'SELECT `language` FROM `al_cms_ref_lang` WHERE `content_id`='.esc($lRow['content_id']).' AND `parent_id`='.esc($lRow['parent_id']);
      $lLang = CCor_Qry::getStr($lSql);
      if($lLang == $aLang) {
        $lRet = intval($lRow['content_id']);
      }
    }
    
    return $lRet;
  }

  /**
   * Check if content string relates to category tried to be associated with
   * @param string $aVal
   * @param string $aCategory
   * @return boolean
   */
  public static function checkCategory($aCont) {
    if(strpos($aCont['layout'], 'nutri') > -1) return TRUE;
    
    $lSql = 'SELECT c.`content_id` FROM `al_cms_ref_category` rc INNER JOIN `al_cms_content` c ON c.content_id=rc.content_id ';
    $lSql.= 'WHERE c.`content`='.esc($aCont['content']).' AND rc.`category`='.esc($aCont['category']).' AND `mand`='.intval(MID);
    $lRes = CCor_Qry::getInt($lSql);
    
    return ($lRes > 0) ? TRUE : FALSE;
  }

  /**
   * Check if the nutrition combo already exists in the database
   * @param string $aVal - nutrition value
   * @param string $aCategory - nutrition type
   * @return integer $lRet - content id or zero (not found)
   */
  public static function checkNutri($aVal = "", $aCategory = 'MA') {
    $lSql = 'SELECT `content_id` FROM `al_cms_ref_nutri` WHERE `type`='.esc($aCategory).' AND `val`='.esc($aVal);
    $lRet = CCor_Qry::getInt($lSql);
    
    return ($lRet > 0) ? $lRet : 0;
  }

  /**
   * Gather all content within the system that is for a mandator
   * @return array $lRet - all content with ids
   */
  public static function getAll($aCat, $aLang = 'MA') {
    $lRet = array();
    
    $lSql = 'SELECT a.`content_id`, a.`parent_id`, b.`content`, b.`tokens`, b.`format`, b.`status`, c.`category`, a.`language`, a.`maxver` as ver ';
    $lSql.= 'FROM (SELECT `content_id`, `parent_id`, max(`version`) AS maxver, `language` ';
    $lSql.= 'FROM `al_cms_ref_lang` WHERE `language`='.esc($aLang).' GROUP BY `parent_id`,`language`) as a ';
    //$lSql.= 'INNER JOIN `al_cms_ref_lang` b ON (a.`parent_id`=b.`parent_id` AND a.`maxver`=b.`version` AND a.`language`=b.`language`) ';
    $lSql.= 'INNER JOIN (SELECT * FROM `al_cms_content` WHERE `mand`='.intval(MID).') as b ON (a.`content_id`=b.`content_id`) ';
    $lSql.= 'INNER JOIN `al_cms_ref_category` as c ON (a.`content_id`=c.`content_id`) ';
    if(!empty($aCat)) {
      $lCat = explode('_', $aCat);
      $lCat = $lCat[0];
      $lSql.= 'WHERE c.`category`='.esc($lCat).' ';
    }
    $lSql.= 'ORDER BY b.`parent_id`, b.`content` ASC';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lCid = intval($lRow['content_id']);
      $lArr = array(
      	'content_id' => $lCid,
      	'parent_id' => intval($lRow['parent_id']),
      	'content' => strip_tags($lRow['content']),
      	'tokens' => $lRow['tokens'],
        'format' => $lRow['format'],
      	'status' => $lRow['status'],
      	'language' => $lRow['language'],
      	'version' => $lRow['ver'],
      	'categories' => $lRow['category']
      );

      $lArr['metadata'] = self::getMetadata($lCid);
      $lArr['jobs'] =  self::getJobs($lCid);
      
      if($lArr['language'] == 'MA'){
        $lRet[] = $lArr;
      }
    }
    
    return $lRet;
  }
  
  /**
   * Get all content information based on content id
   * @param integer $aCid - content id
   * @return array $lRet - content details
   */
  public static function getContent($aCid = 0) {
    $lRet = array();
    $lCid = intval($aCid);
    
    $lSql = 'SELECT c.`parent_id`, c.`content`, c.`tokens`, c.`format`, c.`status`, rc.`category`, rl.`language`, rl.`version` FROM `al_cms_content` c ';
    $lSql.= 'INNER JOIN `al_cms_ref_category` rc ON (c.`content_id`=rc.`content_id`) ';
    $lSql.= 'INNER JOIN `al_cms_ref_lang` rl ON (c.`content_id`=rl.`content_id`) ';
    $lSql.= 'WHERE c.`content_id`='.esc($lCid).' AND c.`mand`='.intval(MID);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet = array(
          'content_id' => $lCid,
          'parent_id' => intval($lRow['parent_id']),
          'content' => $lRow['content'],
          'tokens' => $lRow['tokens'],
          'format' => $lRow['format'],
          'status' => $lRow['status'],
          'language' => $lRow['language'],
          'version' => $lRow['version'],
          'categories' => $lRow['category']
      );
    }
    
    $lRet['metadata'] = self::getMetadata($lCid);
    $lRet['jobs'] =  self::getJobs($lCid);
    
    return $lRet;
  }
  
  /**
   * Set content into content table
   * @param array $aDat - content id, group id, content, tokens
   */
  public static function setContent($aDat = array()) {
    if(!empty($aDat)){
      list($lCid, $lPid, $lCont, $lTokens, $lFormat, $lStatus) = $aDat;
      $lFormat = ($lFormat == NULL) ? 'NULL' : esc($lFormat);
      
      //update content
      $lSql = 'SELECT count(*) FROM `al_cms_content` WHERE `content_id`='.esc($lCid).' AND `mand`='.MID;
      $lCount = CCor_Qry::getInt($lSql);
      if($lCount < 1) { // insert content
        $lSql = 'INSERT INTO `al_cms_content` (`content_id`, `parent_id`, `mand`, `content`, `status`, `tokens`, `format`) ';
        $lSql.= 'VALUES ('.esc($lCid).', '.esc($lPid).', '.intval(MID).', '.esc($lCont).', '.esc($lStatus).', '.esc($lTokens).', '.$lFormat.')';
      } else {
        $lSql = 'UPDATE `al_cms_content` SET `parent_id`='.esc($lPid).', `content`='.esc($lCont).', ';
        $lSql.= ($lStatus !== 'draft') ? '`status`='.esc($lStatus).', ' : '';
        $lSql.= '`tokens`='.esc($lTokens).', `format`='.$lFormat.' WHERE `content_id`='.esc($lCid);
      }
      CCor_Qry::exec($lSql);
    }
  }
  
  /**
   * Set nutrition content into nutri table
   * @param array $aDat - type, value
   */
  public static function setNutri($aDat = array()) {
    $lRet = 0;
    
    if(!empty($aDat)){
      list($lCategory, $lCont) = $aDat;
      $lContentId = CCms_Mod::getMax('content_id');
  
      $lSql = 'INSERT INTO `al_cms_ref_nutri` (`content_id`, `type`, `val`) VALUES ('.$lContentId.', '.esc($lCategory).', '.esc($lCont).')';
      $lQry = $lQry = new CCor_Qry($lSql);
      
      $lRet = $lQry->getInsertId();
    }
    
    return $lRet;
  }
  
  /**
   * Get all nutrition information based on content id
   * @param integer $aCid - content id
   * @return array $lRet - content details
   */
  public static function getNutri($aCid = 0) {
    $lRet = array();
    $lCid = intval($aCid);
    
    $lSql = 'SELECT `type`, `val`  FROM `al_cms_ref_nutri` WHERE `content_id`='.esc($lCid);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet = array(
        'content_id' => $lCid,
        'parent_id' => $lCid,
        'category' => $lRow['type'],
        'content' => $lRow['val']
      );
    }
    
    return $lRet;
  }
  
  /**
   * Sets the client_key reference to a piece of content
   * @param number $aCid - content_id
   * @param string $aClientKey - client_key for product information
   */
  public function setClientKey($aCid, $aClientKey = '') {
    $lCid = intval($aCid);
    //update content
    if(!empty($aClientKey)) {
      $lSql = 'SELECT count(*) FROM `al_cms_ref_product` WHERE `content_id`='.esc($lCid).' AND `client_key`='.esc($aClientKey);
      $lCount = CCor_Qry::getInt($lSql);
      if($lCount < 1) { // insert reference
        $lSql = 'INSERT INTO `al_cms_ref_product` (`mand`, `content_id`, `client_key`) VALUES ('.intval(MID).', '.esc($lCid).', '.esc($aClientKey).')';
        CCor_Qry::exec($lSql);
      }
    }
  }
  
  /**
   * Get all metadata based on a content id
   * @param integer $aCid - content id
   * @return array $lRet - metadata array with type => value as key => value
   */
  public static function getMetadata($aCid) {
    $lRet = array();
    $lCid = intval($aCid);
    
    $lSql = 'SELECT * FROM `al_cms_ref_meta` WHERE `content_id`='.esc($lCid);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lTyp = $lRow['meta_id'];
      
      $lRet[$lTyp][] = $lRow['val'];
    }
    
    return $lRet;
  }
  
  /**
   * Set metadata to a content id
   * @param integer $aCid - content id
   * @param array $aMetadata = all metadata to assign
   */
  public function setMetadata($aCid, $aMetadata) {
    $lMetadata = $aMetadata;
    $lCid = intval($aCid);
    
    foreach($lMetadata as $lMetaId => $lVal) {
      $lMetaId = intval($lMetaId);
      if($lMetaId < 1) continue;
      
      $lSql = 'SELECT count(*) FROM `al_cms_ref_meta` WHERE `content_id`='.esc($lCid).' AND `meta_id`='.esc($lMetaId).' AND `val`='.esc($lVal);
      $lCount = CCor_Qry::getInt($lSql);
      if($lCount < 1) {
        // insert new metadata reference
        $lSql = 'INSERT INTO `al_cms_ref_meta` (`content_id`, `meta_id`, `val`) VALUES ('.esc($lCid).', '.esc($lMetaId).', '.esc($lVal).')';
        CCor_Qry::exec($lSql);
      }
    }
  }

  /**
   * Set the language and version for the content
   * @param array $aDat - group id, content id, version, language
   */
  public function setLangVer($aDat = array()) {
    if(!empty($aDat)){
      list($lPid, $lCid, $lVer, $lLang) = $aDat;

      $lSql = 'SELECT count(*) FROM `al_cms_ref_lang` WHERE `content_id`='.esc($lCid).' AND `parent_id`='.esc($lPid);
      $lCount = CCor_Qry::getInt($lSql);
      if($lCount < 1) {
        $lSql = 'INSERT INTO `al_cms_ref_lang` (`parent_id`, `content_id`, `version`, `language`) VALUES ('.esc($lPid).', '.esc($lCid).', '.esc($lVer).', '.esc($lLang).')';
        CCor_Qry::exec($lSql);
      }
    }
  }
  
  /**
   * Set categories for a content id
   * @param integer $aId - content id
   * @param integer $aCategory - category id
   */
  public function setCategory($aId, $aCategory) {
    list($lId, $lCategory) = array(intval($aId), $aCategory);
    
    $lSql = 'SELECT count(*) FROM `al_cms_ref_category` WHERE `content_id`='.esc($lId);
    $lCount = CCor_Qry::getInt($lSql);
    if($lCount < 1) {
      // insert new category reference
      $lSql = 'INSERT INTO `al_cms_ref_category` (`content_id`, `category`) VALUES ('.esc($lId).', '.esc($lCategory).')';
      CCor_Qry::exec($lSql);
    }
  }

  /**
   * get template combos by a given name
   * @param string $aName - template name
   * @return array $lRet - templates combos
   */
  public function getTemplate($aName = '', $aJobid = 0, $aPhraseTyp = 'job') {
    $lRet = array();
  
    if(!empty($aName)){
      $lSql = 'SELECT `template_id`, `category`, `layout`, `amount` FROM `al_cms_template` WHERE `name`='.esc($aName).' AND `type`='.esc($aPhraseTyp).' ORDER BY id ASC';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lRet[] = array('category' => $lRow['category'], 'layout' => $lRow['layout'], 'amount' => $lRow['amount']);

        $lUsr = CCor_Usr::getInstance();
        if(!empty($lRow['template_id']) && $lUsr -> getPref('phrase.'.$aJobid.'.template', NULL) == NULL) {
          $lUsr -> setPref('phrase.'.$aJobid.'.template', $lRow['template_id']);
        }
      }
    }
  
    return $lRet;
  }
  
  /**
   * Set a template with all combos
   * @param string $aName - name of template
   * @param array $aCombos - template combos
   * @param string $aTyp - modification type (new[create], update[update])
   * @return boolean - completed queries
   */
  public static function setTemplate($aName, $aCombos, $aMethod, $aPhraseTyp = 'job') {
    $lAction = FALSE;
  
    $lSql = 'SELECT count(DISTINCT `name`) FROM `al_cms_template` WHERE `name`='.esc($aName).' AND `type`='.esc($aPhraseTyp).' AND `mand`='.intval(MID);
    $lCount = CCor_Qry::getInt($lSql);
    if($lCount > 0) { //name exists already
      $lAction = TRUE;
      if($aMethod == 'update') {
        $lSql = 'DELETE FROM `al_cms_template` WHERE `name`='.esc($aName).' AND `type`='.esc($aPhraseTyp).' AND `mand`='.intval(MID);
        CCor_Qry::exec($lSql);
      } else if($aMethod == 'new') {
        $aName = $aName.' ('.($lCount+1).')'; //add count to name
      }
    } else {
      if($aMethod == 'new')
        $lAction = TRUE;
    }
    
    $lTemplateId = self::getMax('template_id', 'al_cms_template');
    $lTemplateId += ($lTemplateId > 1) ? 1 : 0;
    // insert template
    foreach($aCombos as $lIdx => $lCombo){
      $lSql = 'INSERT INTO `al_cms_template` (`template_id`, `mand`, `name`, `type`, `category`, `layout`, `amount`) ';
      $lSql.= 'VALUES ('.$lTemplateId.','.intval(MID).','.esc($aName).', '.esc($aPhraseTyp).', '.esc($lCombo['category']).', '.esc($lCombo['layout']).', '.intval($lCombo['amount']).')';
      CCor_Qry::exec($lSql);
    }
  
    return true;
  }
  
  /**
   * Get the max of content id from both content and product information
   * @param string $aTyp - column in tables
   * @return integer $lMax - content id to use
   */
  public static function getMax($aTyp, $aTbl = '') {
    if($aTyp !== 'content_id') {
      $lTbl = (empty($aTbl)) ? 'al_cms_content' : $aTbl;
      $lSql = 'SELECT max(`'.$aTyp.'`) FROM `'.$lTbl.'`';
    } else {
      $lSql = 'SELECT ifnull(max(`'.$aTyp.'`), 0) `'.$aTyp.'` FROM (SELECT `'.$aTyp.'` FROM `al_cms_content` UNION ALL SELECT `'.$aTyp.'` FROM `al_cms_ref_nutri`) a';
    }
    
    $lRes = CCor_Qry::getInt($lSql);
    $lMax = ($lRes > 0) ? $lRes + 1 : 1;
    
    return $lMax;
  }

  /**
   * Get all jobs which reference the content id
   * @param integer $aCid - content id
   * @return array - all jobids
   */
  public static function getJobs($aCid = 0) {
    $lCid = intval($aCid);
    $lSql = 'SELECT DISTINCT `jobid` FROM `al_cms_ref_job` WHERE `content_id`='.esc($lCid);
    $lJobs = CCor_Qry::getArr($lSql);
    
    return ($lJobs == FALSE) ? array() : $lJobs;
  }

}