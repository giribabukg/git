<?php
/**
 * @author Geoffrey Emmans <emmans@qbf.de>
 * @package job
 * @subpackage art
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 16:50:56 +0800 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CInc_Arc_Pro_Tabs extends CArc_Tabs {
    
  public function __construct($aJobId = 0, $aActiveTab = 'job') {
    parent::__construct('arc-pro',$aJobId , $aActiveTab);
  }
}