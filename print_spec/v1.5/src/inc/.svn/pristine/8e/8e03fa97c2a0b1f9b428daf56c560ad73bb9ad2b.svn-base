<?php
/**
 * Approval Loop Button Panel
 *
 * @package    Job
 * @subpackage Approval Loop
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Job_Apl_Page_Buttons extends CCor_Ren {

  public function __construct($aJob) {
    $this -> mJob = $aJob;
    if (!empty($this -> mJob)) { // Keine Anzeige der Aktionsbuttons
      $this -> mJobId = $aJob['jobid'];
      $this -> mSrc = $aJob['src'];
    }
  }

  protected function getCont() {
    $lRet = '';
    if (!empty($this -> mJob)) { // Keine Anzeige der Aktionsbuttons
      if (!$this->canSetState()) {
        return '';
      }
      $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl" style="width:225px">'.LF;
      $lRet.= '<tr><td class="cap">'.htm(lan('lib.actions')).'</td></tr>'.LF;
      $lRet.= '<tr><td class="frm p8 ac">';

      $lRet.= $this->getButtons();

      $lRet.= '</td></tr>'.LF;

      $lRet.= '</table>';
    }
    return $lRet;
  }

  protected function getButtons() {
    $lRet = '';
    $lAtt = array('class' => 'btn w200');
    $lLnk = 'index.php?act=job-apl-page.apl&src='.$this -> mSrc.'&jid='.$this -> mJobId.'&flag=';

    $lAplButtons = CCor_Cfg::get('buttons.apl', array());
    if (!empty($lAplButtons)) {
      foreach ($lAplButtons as $lAplKey => $lAplBtn) {
        $lRet.= btn(lan('apl.'.$lAplBtn), 'go("'.$lLnk.$lAplKey.'")', 'img/ico/16/flag-0'.$lAplKey.'.gif', 'button', $lAtt).BR.BR;
      }
    }
    return $lRet;
  }

  protected function canSetState() {
    $lUid = CCor_Usr::getAuthId();
    $lApl = new CApp_Apl_Loop($this->mSrc, $this->mJobId);
    return $lApl->isUserActiveNow($lUid);
  }

}