<?php
/**
 * Title
 *
 * Description
 *
 * @package    package
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Job_Cms_Search_Dialog extends CCor_Tpl {

  /**
   * Open HTML search dialog page and replace patterns with content
   * @param number $aId - ID of section to place content into
   * @param string $aSearch - Search string
   * @param array $aSuggs - Suggestions to be displayed
   * @param number $aJobId
   * @param string $aSrc - job type
   */
  public function __construct($aId = 0, $aSearch = '', $aSuggs = array(), $aJobId = 0, $aSrc, $aJob) {
    $this -> mId = $aId;
    $this -> mJobid = $aJobId;
    $this -> mSrc = $aSrc;
    $this -> mJob = $aJob;

    $this->openProjectFile('job/cms/search_dialog.htm');

    $this -> getCategories();
    $this -> getSuggestions($aSuggs);

    $this -> setPat('id', $this -> mId);
    $this -> setPat('search', strip_tags($aSearch));
    $this -> setPat('suggestions', $this -> getSuggestionsOptions());
    $this -> setPat('categories', $this -> getCategoryOptions());
    $this -> setPat('metadata', $this -> getMetadataOptions());
  }

  /**
   * Get HTML representation of metadata options.
   * @return string $lRet
   */
  protected function getMetadataOptions(){
    $lChkBox = array();
    $lRet = '';
    $lFie = CCor_Res::getByKey('alias', 'fie', array("mand" => MID));

    $lMod = new CJob_Cms_Mod($this -> mSrc, $this -> mJobid, $this -> mJob);
    $lJobMeta = $lMod -> getMetadata();

    foreach ($lFie as $lKey => $lDef) {
      $lId = intval($lDef['id']);
      if (array_key_exists($lId, $lJobMeta)) {
        $lName = $lDef['name_'.LAN];
        $lVal = $lJobMeta[$lId];
        
        $lChkBox = '<input type="checkbox" id="'.$lId.'" value="'.$lVal.'" ';
        $lChkBox.= 'onclick="javascript:gIgn=1; Flow.cmsSearch.check(this.id);" name="meta'.$lId.'">&nbsp;<b>'.$lName.'</b>: '.$lVal;
        $lChkBoxes[] = $lChkBox;
      }
    }
    
    if(sizeof($lChkBoxes) > 0) {
      $lDiv = getNum('do'); // outer div
      $lDivId = getNum('di'); // inner div
      $lLnkId = getNum('l'); // link
  
      $lRet.= '<div id="'.$lDiv.'" class="fl w70">'.LF;
      $lRet.= '  <a class="nav" id="'.$lLnkId.'" href="javascript:Flow.Std.popMen(\''.$lDivId.'\',\''.$lLnkId.'\')">'.lan('lib.metadata').'</a>'.LF;
      $lRet.= '  <div id="'.$lDivId.'" class="smDiv" style="display:none">';
      $lRet.= '    <table border="0" cellspacing="0" cellpadding="2" class="tbl mw200">';
      
      for($lI = 0; $lI < count($lChkBoxes); $lI++) {
        $lRet.= '    <tr>';
        $lRet.= '      <td class="td1 nw">'.$lChkBoxes[$lI].'</td>';
        $lRet.= '    </tr>';
      }
      
      $lRet.= '    </table>';
      $lRet.= '  </div>';
      $lRet.= '</div>'.LF;
    }
    
    return $lRet;
  }

  /**
   * Restructure all suggestions into an array
   * @param unknown $aSuggs
   */
  protected function getSuggestions($aSuggs) {
    $this -> mSuggs = array();

    foreach ($aSuggs as $lIdx => $lSugg) {
      $lKey = $lSugg['content_id'];

      $this -> mSuggs[$lKey] = $lSugg;
    }
  }

  /**
   * Get HTML representation of suggestions.
   * @return string $lRet
   */
  protected function getSuggestionsOptions() {
    $lRet = '';

    foreach ($this -> mSuggs as $lId => $lSugg) {
      $lRet.= '<div class="p8 m5 '.$lSugg['language'].' suggestion" id="'.$lId.'" onclick="Flow.cmsSearch.toggle(this);">';
      $lRet.= '<span class="m5 version">'.$lSugg['version'].'</span>';
      $lRet.= '<span class="content">'.trim($lSugg['content']).'</span>';

      $lTooltip = '<b>Category:</b> '.ucfirst($lSugg['categories'][0]) . BR;
      $lTooltip.= '<b>Language:</b> '.ucfirst($lSugg['language']) . BR;
      $lTooltip.= '<b>Version:</b> '.ucfirst($lSugg['version']) . BR;
      $lTooltip.= '<b>Status:</b> '.ucfirst($lSugg['status']) . BR;
      $lRet.= '<span class="info fr" data-toggle="tooltip" data-tooltip-head="" data-tooltip-body="'.htm($lTooltip).'">';
      $lRet.= img("img/ico/16/ml-1.gif").'</span>';

      $lRet.= '</div>';
    }

    return $lRet;
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

      $this -> mCategories[$lKey] = $lVal;
    }
    asort($this -> mCategories);
  }

  /**
   * Get HTML representation of category options with auto selection of category if not global searching
   * @return string $lRet
   */
  protected function getCategoryOptions() {
    $lRet = '';
    $lCategory = explode("_", $this -> mId);
    $lCategory = $lCategory[0];

    foreach ($this -> mCategories as $lKey => $lName) {
      $lSel = ($lKey == $lCategory) ? ' selected="selected"' : '';
      $lRet.= '<option value="'.$lKey.'"'.$lSel.'>'.htm($lName).'</option>'.LF;
    }

    return $lRet;
  }

}