<?php
class CInc_Content_Mod extends CCor_Mod_Table {

  /**
   * Constructor
   *
   * @access public
   */
  public function __construct() {
    parent::__construct('al_text_content');

    $this -> addField(fie('alias'));
    $this -> addField(fie('name_en'));
    $this -> addField(fie('name_de'));
    $this -> addField(fie('content_en'));
    $this -> addField(fie('content_de'));
    $this -> mValid = true;
  }

  /**
   * Before post
   *
   * @access protected
   */
  protected function beforePost($aNew = FALSE) {
    if ($aNew) {
      $this -> setVal('mand', MID);
    }
  }
  
  public function isValid() {
  	return $this -> mValid;
  }
 
  public function duplicateCheck($aReqAliasVal) {
  	$lSql = 'SELECT alias FROM al_text_content WHERE alias = "'.$aReqAliasVal.'"';
  	$lAlias = CCor_Qry::getStr($lSql);
  	if ($lAlias) {
  		$this -> mValid = false;
  		$this -> msg('The alias name you are trying to save :"'.$aReqAliasVal.'" is already exist!!!',mtUser,mlWarn);
  	}
  	else $this -> mValid = true;
  	return $this -> mValid;
  }
}