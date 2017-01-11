<?php
class CJob_Print extends CCust_Job_Print {

  public function __construct($aSrc, $aJob, $aPage = 'job', $aJobId = '') {
    parent::__construct($aSrc, $aJob, $aPage, $aJobId);

    $this -> addPart('job', 'externals', 'art');
  }

  protected function onBeforeContent() {
    parent::onBeforeContent();

    $lServiceOrderID = $this -> mJob['service_order_id'];
    $this -> setPat('service_order_id', $lServiceOrderID);
  }
}