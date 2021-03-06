<?php
/**
 * @package job
 * @subpackage art
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 09:50:56 +0100 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CInc_Job_Tra_Tabs extends CJob_Tabs {

  protected $mSrc = 'tra';

  public function __construct($aJobId = 0, $aActiveTab = 'job') {
    parent::__construct('job-'.$this -> mSrc, $aJobId, $aActiveTab);
  }

}