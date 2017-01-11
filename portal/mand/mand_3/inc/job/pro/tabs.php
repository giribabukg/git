<?php
/**
 * @package job
 * @subpackage art
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 08:50:56 +0000 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CJob_Pro_Tabs extends CJob_Tabs {

  protected $mSrc = 'pro';

  public function __construct($aJobId = 0, $aActiveTab = 'job') {
    parent::__construct('job-'.$this -> mSrc, $aJobId, $aActiveTab);
    
    $this -> setHidden('det', TRUE);
  }

}