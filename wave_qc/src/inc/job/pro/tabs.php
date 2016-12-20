<?php
/**
 * @author Geoffrey Emmans <emmans@qbf.de>
 * @package job
 * @subpackage art
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 8950 $
 * @date $Date: 2015-05-29 15:07:09 +0800 (Fri, 29 May 2015) $
 * @author $Author: ahanslik $
 */
class CInc_Job_Pro_Tabs extends CJob_Tabs {

  public function __construct($aJobId = 0, $aActiveTab = 'job') {
    parent::__construct('job-pro', $aJobId, $aActiveTab);
  }
}