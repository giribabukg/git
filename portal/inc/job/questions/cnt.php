<?php
class CInc_Job_Questions_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job-questions.menu');
  }
  
  protected function actStd() {
    $lJobid = $this -> getReq('jobid');
    $lFac = new CJob_Fac($this->mSrc, $lJobid);
    $lFac -> getDat();
    $lRet = '';
    $lVie = $lFac -> getHeader();
    $lRet.= $lVie -> getContent();
    $lVie = $lFac -> getTabs('questions');
    $lRet.= $lVie -> getContent();
    $lVie = new CJob_Questions_Form($this->mSrc, '', $lJobid, 'sedt');
    $lVie -> setJobId($lJobid);
    $lRet.= $lVie -> getContent();
  
    $this -> render($lRet);
  }

  static function getStatus($aJobId) {
    $lCnt = array(3 => 0, 2 => 0, 1 => 0);

    $lMod = new CQuestions_Mod();
    $lQuestions = $lMod->getQuestions($aJobId);

    $lTbl ="<table>";
    $lTbl .= '<tr><td class="th1" colspan="2">'.lan("job-questions.menu").'</tr></td>';
      foreach($lQuestions as $lRow) {
        if($lRow["status"] == "3") {
          $lCnt[3]++;
        }
        else if($lRow["status"] == "2") {
          $lCnt[2]++;
        }
        else if($lRow["status"] == "1") {
          $lCnt[1]++;
        }
      }
      $lTbl .= '<tr><td><i class="ico-w16 ico-w16-flag-03"></i></td><td class=""> '.lan('lib.questions.ok').': '.$lCnt[3].'</td></tr>';
      $lTbl .= '<tr><td><i class="ico-w16 ico-w16-flag-02"></i></td><td class=""> '.lan('lib.pending').': '.$lCnt[2].'</td></tr>';
      $lTbl .= '<tr><td><i class="ico-w16 ico-w16-flag-01"></i></td><td class=""> '.lan('lib.missingInfo').': '.$lCnt[1].'</td></tr>';
    $lTbl .= '</table>';
    
    $lRet = '<span data-toggle="tooltip" data-tooltip-body="'.htm($lTbl).'">';
    foreach($lCnt as $lRow => $lkey) {
      if($lkey >= 1) {
        $lRet .= '<i class="ico-w16 ico-w16-flag-0'.$lRow.'_5px"></i>';
      }
    }
    $lRet .= '</span>';

    return $lRet;
  }
}