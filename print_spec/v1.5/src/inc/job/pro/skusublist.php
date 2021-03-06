<?php
/**
 * Jobs: Projects - Subproject
 *
 *  Description
 *
 * @package    JOB
 * @subpackage    Projects
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 9746 $
 * @date $Date: 2015-07-20 18:49:10 +0800 (Mon, 20 Jul 2015) $
 * @author $Author: ahanslik $
 */
class CInc_Job_Pro_Skusublist extends CJob_Sku_List {

  var $mSubJobs = array();
  var $mShowCsvExportButton = FALSE;
  var $mShowDeleteButton = FALSE;
  var $mShowCopyButton = FALSE;
  var $mShowHdr = FALSE;
  var $mShowSubHdr = FALSE;
  
  public function __construct($aProId, $aSubJobList = Array(), $aWithoutLimit = FALSE) {
   $this -> mSubJobList = $aSubJobList;
   $this -> mShowColumnMore = FALSE;
   
   
   parent::__construct();
   $this -> mHideFil =  1;
   $this -> mHideSer =  1;
  }

  protected function getIterator() {
    parent::getIterator();
    $this -> mIte -> addCondition('id', 'in', $this -> mSubJobList);
   }
  
  protected function getColumns(){
    $lRet = Array();
    $lRet = CCor_Cfg::get('job-sku.subfields');
    return $lRet;
  }
  
  protected function addColumns() {
    foreach ($this -> mCol as $lAlias) {
      if (isset($this -> mDefs[$lAlias])) {
        $lDef = $this -> mDefs[$lAlias];
        
        $this -> addField($lDef);
        $this -> onAddField($lDef);
      }
    }
   
  }
  
  /**
   * Function overwrite
   * @see inc/job/sku/CInc_Job_Sku_List#afterRow()
   */
  protected function afterRow() {
  }
  /**
   * Function overwrite
   * @see inc/job/sku/CInc_Job_Sku_List#addJs()
   */
  protected function addJs() {
  }
  
  /**
   * Function overwrite
   * @see inc/job/sku/CInc_Job_Sku_List#getViewMenuObject()
   */
  protected function & getViewMenuObject() {
    $lMen = new CHtm_Menu(lan('lib.opt'));
    return $lMen;
  }
}