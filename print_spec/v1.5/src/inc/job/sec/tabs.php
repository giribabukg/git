<?php
/**
 * Jobs: Sec - Tabs
 *
 *  Description
 *
 * @package    JOB
 * @subpackage    Sec
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 16:50:56 +0800 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CInc_Job_Sec_Tabs extends CJob_Tabs {
    
  public function __construct($aJobId = 0, $aActiveTab = 'job') {
    parent::__construct('job-sec',$aJobId , $aActiveTab);
  }
 

}