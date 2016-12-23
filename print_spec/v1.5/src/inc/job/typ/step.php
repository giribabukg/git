<?php
/**
 * Jobs: Art - Critical Path Step
 *
 * ToDo: Description
 *
 * @package    JOB
 * @subpackage    Typ
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 4968 $
 * @date $Date: 2014-07-02 21:16:58 +0800 (Wed, 02 Jul 2014) $
 * @author $Author: ahanslik $
 */
class CInc_Job_Typ_Step extends CJob_Step {

  public function __construct($aSrc, $aJobId, $aJob = NULL) {
    parent::__construct($aSrc, $aJobId, $aJob);
  }

  protected function getMod($aJobId = NULL) {
    return new CJob_Typ_Mod($this -> getSrc(), $aJobId);
  }

  /**
   * provides all values from a job
   *
   * @param string $aJobId
   * @return all Values from a Job
   * @see inc/job/CInc_Job_Step#getDat($aJobId)
   */
  protected function getDat($aJobId) {
    $lRet = new CJob_Typ_Dat($this -> getSrc());
    $lRet -> load($aJobId);
    return $lRet;
  }

  protected function onCopy($aOldJobId, $aNewJobId) {
    parent::onCopy($aOldJobId, $aNewJobId);

    $lArr = array();
    $lArr['jid'] = (string)$aNewJobId;
    $lArr['src'] = $this -> getSrc();

    $lArr['name'] = intval($aNewJobId);
    $lArr['tpl']  = CApi_Wec_WebcenterTemplate::getTemplate($aOldJobId);

    CApp_Queue::add('wecprj', $lArr);
  }
}