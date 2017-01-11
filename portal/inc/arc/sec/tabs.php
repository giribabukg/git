<?php
/**
 * @author Geoffrey Emmans <emmans@qbf.de>
 * @package job
 * @subpackage art
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 09:50:56 +0100 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CInc_Arc_Sec_Tabs extends CArc_Tabs {
    
  public function __construct($aJobId = 0, $aActiveTab = 'job') {
    parent::__construct('arc-sec',$aJobId , $aActiveTab);
  }
}