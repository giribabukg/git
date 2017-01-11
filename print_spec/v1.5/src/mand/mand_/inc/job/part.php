<?php
/**
 * @author Geoffrey Emmans <emmans@qbf.de>
 * @package job
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 4064 $
 * @date $Date: 2014-04-03 09:46:30 +0200 (Thu, 03 Apr 2014) $
 * @author $Author: ahajali $
 */
class CJob_Part extends CCust_Job_Part {
  
  protected function onBeforeContent() {
    parent::onBeforeContent();
    
    $lBrand = $this -> mJob['brand'];
    $lPicklist = CCor_Res::get('pcklist', array('domain'=> 'qcinst'));
    $lInstructions  = $this -> findPatterns('inst.');
    if($lInstructions && !empty($lBrand)){
      foreach($lInstructions as $lArea) {
        $lInstruction = '';
    	foreach ($lPicklist as $lRow){
    	  if($lRow['col2'] == $lArea && $lRow['col1'] == $lBrand) {
    		$lInstruction = $lRow['col3'];
    	  }
    	}
    	$this -> setPat("inst.".$lArea, $lInstruction);
	  }
    }
  }
}