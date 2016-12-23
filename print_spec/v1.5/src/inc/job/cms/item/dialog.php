<?php
/**
 * Title
 *
 * Description
 *
 * @package    package
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Job_Cms_Item_Dialog extends CCor_Tpl {

  /**
   * Open HTML item dialog page and replace patterns with content
   * @param number $aJobId
   */
  public function __construct($aJobId = 0, $aSrc) {
    $this -> mSrc = $aSrc;
    $this->openProjectFile('job/cms/item_dialog.htm');

    $this->getCategories();
    $this->getLayouts();
    
    $this->setPat('templates', $this -> getTemplates($aJobId));
    $this->setPat('categories', $this -> getCategoryOptions());
    $this->setPat('layout', $this -> getLayoutOptions());
    $this->setPat('removebtn', $this -> getRemoveButton());
  }

  /**
   * Get HTML representation of template options with job template auto selected.
   * @param string $aJobId
   * @return string $lRet
   */
  protected function getTemplates($aJobId) {

    $lUsr = CCor_Usr::getInstance();
    $lTemp = $lUsr -> getPref('phrase.'.$aJobId.'.template', '');
    $lRet = '<option value="">&nbsp;</option>';
    
    $lPhraseTypes = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
    $lJobTyp = $lPhraseTypes[$this -> mSrc];
    
    $lQry = new CCor_Qry('SELECT DISTINCT `template_id`, `name` FROM `al_cms_template` WHERE `type`='.esc($lJobTyp).' AND `mand`='.MID);
    foreach ($lQry as $lRow) {
      $lId = $lRow['template_id'];
      $lKey = $lRow['name'];
      $lSel = ($lId == $lTemp) ? 'selected="selected"' : '';
      
      $lRet.= '<option value="'.$lKey.'" '.$lSel.'>'.htm($lKey).'</option>'.LF;
    }
    
    return $lRet;
  }

  /**
   * Gather all layout options into an array
   */
  protected function getLayouts() {
    $this -> mLayouts = array("" => "");
    
    $lQry = new CCor_Qry('SELECT DISTINCT `value`, `value_'.LAN.'` FROM `al_htb_itm` WHERE domain="phl" AND `mand`='.MID);
    foreach ($lQry as $lRow) {
      $lKey = $lRow['value'];
      $lVal = $lRow['value_'.LAN];
      
      $this->mLayouts[$lKey] = $lVal;
    }
    asort($this -> mLayouts);
  }

  /**
   * Get HTML representation of layout options.
   * @return string $lRet
   */
  protected function getLayoutOptions() {
    $lRet = '';

    foreach ($this->mLayouts as $lKey => $lName) {
      $lRet.= '<option value="'.$lKey.'">'.htm($lName).'</option>'.LF;
    }
    
    return $lRet;
  }

  /**
   * Get the remove button for first combo row 
   * @return image html
   */
  protected function getRemoveButton() {
    $lAttr = array(
        "class" => "removeBtn",
    	"onclick" => "Flow.cmsDialog.removeItem(this);",
    	"style" => "cursor:pointer;"
    );
    
    return img("img/ico/16/cancel.gif", $lAttr);
  }
  
  /**
   * Gather all category options into an array
   */
  protected function getCategories() {
    $this -> mCategories = array("" => "");
    
    $lQry = new CCor_Qry('SELECT DISTINCT `value`, `value_'.LAN.'` FROM `al_htb_itm` WHERE domain="phc" AND `mand`='.MID);
    foreach ($lQry as $lRow) {
      $lKey = $lRow['value'];
      $lVal = $lRow['value_'.LAN];
      
      $this->mCategories[$lKey] = $lVal;
    }
    aSort($this -> mCategories);
  }

  /**
   * Get HTML representation of category options
   * @return string $lRet
   */
  protected function getCategoryOptions() {
    $lRet = '';

    foreach ($this->mCategories as $lKey => $lName) {
      $lRet.= '<option value="'.$lKey.'">'.htm($lName).'</option>'.LF;
    }
    
    return $lRet;
  }

}