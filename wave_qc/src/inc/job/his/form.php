<?php
class CInc_Job_His_Form extends CHtm_Form {

  public function __construct($aSrc, $aJobId, $aAct = 'snew', $aStage = 'job') {
    parent::__construct($aStage.'-'.$aSrc.'-his.'.$aAct, lan('lib.msg.new'), $aStage.'-'.$aSrc.'-his&jobid='.$aJobId);
    $this -> setParam('jobid', $aJobId);
    $this -> setAtt('class', 'tbl w800');

    $lArr = array();
    $lArr['class'] = 'inp w700';
    $this -> addDef(fie('subject', lan('lib.sbj'), 'string', NULL, $lArr));
    $lArr['rows']  = 24;
    $this -> addDef(fie('msg', lan('lib.msg'), 'memo', NULL, $lArr));
  }

  protected function getJs() {
    return '';
  }

  public function load($aId) {
    $lId = intval($aId);
    $lQry = new CCor_Qry('SELECT * FROM al_job_his WHERE id='.$lId.' AND mand='.MID);
    if ($lRow = $lQry -> getDat()) {
      $this -> assignVal($lRow);
      $this -> setParam('old[id]', $lId);
      $this -> setParam('val[id]', $lId);
    }
  }

}