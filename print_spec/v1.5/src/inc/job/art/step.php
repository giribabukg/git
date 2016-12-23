<?php
/**
 * Jobs: Art - Critical Path Step
 *
 *  Description
 *
 * @package    JOB
 * @subpackage    Art
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 4968 $
 * @date $Date: 2014-07-02 21:16:58 +0800 (Wed, 02 Jul 2014) $
 * @author $Author: ahanslik $
 */
class CInc_Job_Art_Step extends CJob_Step {

  public function __construct($aJobId, $aJob = NULL) {
    parent::__construct('art', $aJobId, $aJob);
  }

  protected function getMod($aJobId = NULL) {
    return new CJob_Art_Mod($aJobId);
  }

  /**
   * provides all values from a job
   *
   * @param string $aJobId
   * @return all Values from a Job
   * @see inc/job/CInc_Job_Step#getDat($aJobId)
   */
  protected function getDat($aJobId) {
    $lRet = new CJob_Art_Dat();
    $lRet -> load($aJobId);
    return $lRet;
  }
}