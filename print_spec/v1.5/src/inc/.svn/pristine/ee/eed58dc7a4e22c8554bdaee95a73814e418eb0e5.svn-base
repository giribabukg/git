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
  public function check($aVal = "", $aLang = 'MA') {
    $lRet = 0;
    
    $lSql = 'SELECT * FROM `al_cms_content` WHERE `content`='.esc($aVal).' AND `mand`='.MID;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lSql = 'SELECT `language` FROM `al_cms_ref_lang` WHERE `content_id`='.$lRow['content_id'].' AND `parent_id`='.$lRow['parent_id'];
      $lLang = CCor_Qry::getStr($lSql);
      if($lLang == $aLang) {
        $lRet = intval($lRow['content_id']);
      }
    }
    
    return $lRet;
  }

  /**
   * Check if the nutrition combo already exists in the database
   * @param string $aVal - nutrition value
   * @param string $aCategory - nutrition type
   * @return integer $lRet - content id or zero (not found)
   */
  public function checkNutri($aVal = "", $aCategory = 'MA') {
    $lSql = 'SELECT `content_id` FROM `al_cms_ref_nutri` WHERE `type`='.esc($aCategory).' AND `val`='.esc($aVal);
    $lRet = CCor_Qry::getInt($lSql);
    
    return ($lRet > 0) ? $lRet : 0;
  }

  /**
   * Gather all content within the system that is for a mandator
   * @return array $lRet - all content with ids
   */
  public function getAll($aCat, $aLang = 'MA') {
    $lRet = array();
    
    $lSql = 'SELECT b.`content_id`, b.`parent_id`, c.`content`, c.`tokens`, c.`status`, d.`category`, a.`language`, a.`maxver` as ver ';
    $lSql.= 'FROM (SELECT `parent_id`, max(`version`) AS maxver, `language` ';
    $lSql.= 'FROM `al_cms_ref_lang` WHERE `language`="'.$aLang.'" GROUP BY `parent_id`,`language`) as a ';
    $lSql.= 'INNER JOIN `al_cms_ref_lang` b ON (a.`parent_id`=b.`parent_id` AND a.`maxver`=b.`version` AND a.`language`=b.`language`) ';
    $lSql.= 'INNER JOIN (SELECT * FROM `al_cms_content` WHERE `mand`='.MID.') c ON (b.`content_id`=c.`content_id`) ';
    $lSql.= 'INNER JOIN `al_cms_ref_category` d ON (b.`content_id`=d.`content_id`) ';
    if(!empty($aCat)) {
      $lCat = explode('_', $aCat);
      $lCat = $lCat[0];
      $lSql.= 'WHERE d.`category`='.esc($lCat).' ';
    }
    $lSql.= 'ORDER BY c.`parent_id`, c.`content` ASC';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lCid = intval($lRow['content_id']);
      $lArr = array(
      	'content_id' => $lCid,
      	'parent_id' => intval($lRow['parent_id']),
      	'content' => $lRow['content'],
      	'tokens' => $lRow['tokens'],
      	'status' => $lRow['status'],
      	'language' => $lRow['language'],
      	'version' => $lRow['ver'],
      	'categories' => $lRow['category']
      );

      $lArr['metadata'] = $this -> getMetadata($lCid);
      $lArr['jobs'] =  $this -> getJobs($lCid);
      
      $lArr['content'] = strip_tags($lArr['content']);
      
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
  public function getContent($aCid = 0) {
    $lRet = array();
    
    $lSql = 'SELECT `parent_id`, `content`, `tokens`, `status`  FROM `al_cms_content` WHERE `content_id`='.esc($aCid).' AND `mand`='.MID;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet['content_id'] = $aCid;
      $lRet['parent_id'] = intval($lRow['parent_id']);
      $lRet['content'] = $lRow['content'];
      $lRet['tokens'] = $lRow['tokens'];
      $lRet['status'] = $lRow['status'];
    }
    
    $lRet['categories'] = self::getCategory($aCid);
    $lRet['metadata'] = $this -> getMetadata($aCid);
    $lRet['jobs'] = $this -> getJobs($aCid);
    
    $lLangVer = $this -> getLangVer($aCid);
    $lRet = array_merge($lRet, $lLangVer);
    
    return $lRet;
  }
  
  /**
   * Set content into content table
   * @param array $aDat - content id, group id, content, tokens
   */
  public function setContent($aDat = array()) {
    if(!empty($aDat)){
      list($lCid, $lPid, $lCont, $lTokens) = $aDat;
      
      //update content
      $lSql = 'SELECT count(*) FROM `al_cms_content` WHERE `content_id`='.$lCid.' AND `mand`='.MID;
      $lCount = CCor_Qry::getInt($lSql);
      if($lCount < 1) { // insert content
        $lSql = 'INSERT INTO `al_cms_content` (`content_id`, `parent_id`, `mand`, `content`, `tokens`) ';
        $lSql.= 'VALUES ('.$lCid.', '.$lPid.', '.MID.', '.esc($lCont).', '.esc($lTokens).')';       
      } else {
        $lSql = 'UPDATE `al_cms_content` SET `parent_id`='.$lPid.', `content`='.esc($lCont).',';
        $lSql.= '`tokens`='.esc($lTokens).' WHERE `content_id`='.$lCid;
      }
      CCor_Qry::exec($lSql);
    }
  }
  
  /**
   * Set nutrition content into nutri table
   * @param array $aDat - type, value
   */
  public function setNutri($aDat = array()) {
    if(!empty($aDat)){
      list($lCategory, $lCont) = $aDat;
      $lContentId = CCms_Mod::getMax('content_id');
  
      $lSql = 'INSERT INTO `al_cms_ref_nutri` (`content_id`, `type`, `val`) VALUES ('.$lContentId.', '.esc($lCategory).', '.esc($lCont).')';
      $lQry = $lQry = new CCor_Qry($lSql);
      
      return $lQry->getInsertId();
    }
  }
  
  /**
   * Get all nutrition information based on content id
   * @param integer $aCid - content id
   * @return array $lRet - content details
   */
  public function getNutri($aCid = 0) {
    $lRet = array();
    
    $lSql = 'SELECT `type`, `val`  FROM `al_cms_ref_nutri` WHERE `content_id`='.esc($aCid);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet['content_id'] = $lRet['parent_id'] = $aCid;
      $lRet['category'] = $lRow['type'];
      $lRet['content'] = $lRow['val'];
    }
    
    return $lRet;
  }
  
  /**
   * Sets the client_key reference to a piece of content
   * @param number $aCid - content_id
   * @param string $aClientKey - client_key for product information
   */
  public function setClientKey($aCid, $aClientKey = '') {
    //update content
    if(!empty($aClientKey)) {
      $lSql = 'SELECT count(*) FROM `al_cms_ref_product` WHERE `content_id`='.$aCid.' AND `client_key`='.esc($aClientKey);
      $lCount = CCor_Qry::getInt($lSql);
      if($lCount < 1) { // insert reference
        $lSql = 'INSERT INTO `al_cms_ref_product` (`content_id`, `client_key`) VALUES ('.$aCid.', '.esc($aClientKey).')';
        CCor_Qry::exec($lSql);
      }
      
      //TODO: removal of client_key reference?
    }
  }
  
  /**
   * Get all metadata based on a content id
   * @param integer $aCid - content id
   * @return array $lRet - metadata array with type => value as key => value
   */
  public function getMetadata($aCid) {
    $lRet = array();
    
    $lSql = 'SELECT * FROM `al_cms_ref_meta` WHERE `content_id`='.esc($aCid);
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
    $lContentId = intval($aCid);
    
    foreach($lMetadata as $lMetaId => $lVal) {
      $lMetaId = intval($lMetaId);
      if($lMetaId < 1) continue;
      
      $lSql = 'SELECT count(*) FROM `al_cms_ref_meta` WHERE `content_id`='.$lContentId.' AND `meta_id`='.$lMetaId.' AND `val`='.esc($lVal);
      $lCount = CCor_Qry::getInt($lSql);
      if($lCount < 1) {
        // insert new metadata reference
        $lSql = 'INSERT INTO `al_cms_ref_meta` (`content_id`, `meta_id`, `val`) VALUES ('.$lContentId.', '.$lMetaId.', '.esc($lVal).')';
        CCor_Qry::exec($lSql);
      }
    }
    
    //TODO: removal of metadata reference?
  }
  
  /**
   * Get the language and version of the content id
   * @param integer $aCid - content id
   * @return array $lRet - language and version
   */
  public function getLangVer($aCid = 0) {
    $lRet = array();
    
    $lSql = 'SELECT * FROM `al_cms_ref_lang` WHERE content_id='.esc($aCid);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet = array( "version" => intval($lRow['version']), "language" => $lRow['language'] );
    }
    
    return $lRet;
  }
  
  /**
   * Set the language and version for the content
   * @param array $aDat - group id, content id, version, language
   */
  public function setLangVer($aDat = array()) {
    if(!empty($aDat)){
      list($lPid, $lCid, $lVer, $lLang) = $aDat;

      $lSql = 'SELECT count(*) FROM `al_cms_ref_lang` WHERE `content_id`='.$lCid.' AND `parent_id`='.$lPid;
      $lCount = CCor_Qry::getInt($lSql);
      if($lCount < 1) {
        $lSql = 'INSERT INTO `al_cms_ref_lang` (`parent_id`, `content_id`, `version`, `language`) VALUES ('.$lPid.', '.$lCid.', '.$lVer.', '.esc($lLang).')';
      }
      CCor_Qry::exec($lSql);
    }
  }
  
  /**
   * Get categories for content id
   * @param integer $aCid - content id
   * @return array $lRet - categories
   */
  public static function getCategory($aCid = 0) {
    $lRet = array();
    
    $lSql = 'SELECT `category` FROM `al_cms_ref_category` WHERE `content_id`='.esc($aCid);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lRet[] = $lRow['category'];
    }
    
    return $lRet;
  }
  
  /**
   * Set categories for a content id
   * @param integer $aId - content id
   * @param integer $aCategory - category id
   */
  public function setCategory($aId, $aCategory) {
    $lId = $aId;
    $lCategory = $aCategory;
    
    $lSql = 'SELECT count(*) FROM `al_cms_ref_category` WHERE `content_id`='.$lId.' AND `category`='.esc($lCategory);
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
        if(!empty($lRow['template_id'])) {
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
  public function setTemplate($aName, $aCombos, $aTyp, $aPhraseTyp = 'job') {
    $lAction = FALSE;
  
    $lSql = 'SELECT count(DISTINCT `name`) FROM `al_cms_template` WHERE `name`='.esc($aName).' AND `type`='.esc($aPhraseTyp).' AND `mand`='.MID;
    $lCount = CCor_Qry::getInt($lSql);
    if($lCount > 0) { //name exists already
      $lAction = TRUE;
      if($aTyp == 'update') {
        $lSql = 'DELETE FROM `al_cms_template` WHERE `name`='.esc($aName).' AND `type`='.esc($aPhraseTyp).' AND `mand`='.MID;
        CCor_Qry::exec($lSql);
      } else if($aTyp == 'new') {
        $aName = $aName.' ('.($lCount+1).')'; //add count to name
      }
    } else {
      if($aTyp == 'new')
        $lAction = TRUE;
    }
    
    $lSql = 'SELECT max(`template_id`) FROM `al_cms_template`';
    $lTemplateId = CCor_Qry::getInt($lSql) + 1;
  
    // insert template
    foreach($aCombos as $lIdx => $lCombo){
      $lSql = 'INSERT INTO `al_cms_template` (`template_id`, `mand`, `name`, `type`, `category`, `layout`, `amount`) ';
      $lSql.= 'VALUES ('.$lTemplateId.','.MID.','.esc($aName).', '.esc($aPhraseTyp).', '.esc($lCombo['category']).', '.esc($lCombo['layout']).', '.intval($lCombo['amount']).')';
      CCor_Qry::exec($lSql);
    }
  
    return true;
  }
  
  /**
   * Get the max of content id from both content and product information
   * @param string $aTyp - column in tables
   * @return integer $lMax - content id to use
   */
  public static function getMax($aTyp) {
    if($aTyp !== 'content_id') {
      $lSql = 'SELECT max(`'.$aTyp.'`) FROM `al_cms_content`';
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
    $lSql = 'SELECT DISTINCT `jobid` FROM `al_cms_ref_job` WHERE `content_id`='.esc($aCid);
    
    return CCor_Qry::getArr($lSql);
  }

}