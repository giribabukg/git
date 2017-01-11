<?php
/**
 * ToDo: Description
 *
 * @author Geoffrey Emmans <emmans@qbf.de>
 * @package JOB
 * @subpackage TYP
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 09:50:56 +0100 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CInc_Job_Typ_Tabs extends CJob_Tabs {
    
  public function __construct($aSrc, $aJobId = 0, $aActiveTab = 'job') {
    parent::__construct('job-'.$aSrc, $aJobId , $aActiveTab);
  }
 

}