<?php
class CInc_Job_Apl2_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    $this -> mModCnt = $aMod;
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job-apl.menu');
    $this -> mSrc = $this -> getReq('src', '');
    $this -> mJid = $this -> getReq('jobid', '');
    $this -> mMmKey = 'job';
  }

  protected function getStdUrl() {
    return 'index.php?act='.$this -> mModCnt.'&src='.$this -> mSrc.'&jobid='.$this -> mJid;
  }

  protected function actStd() {
    $lFac = new CJob_Fac($this->mSrc, $this->mJid);
    $lJob = $lFac->getDat();

    $lHeader = $lFac->getHeader();
    $lRet = $lHeader->getContent();
    $lTabs = $lFac->getTabs('apl');
    $lRet.= $lTabs->getContent();

    $lList = new CJob_Apl2_List($this->mSrc, $this->mJid);
    $lRet.= '<div id="apl-dlg" style="display:none">';
    $lRet.= '<b>Comment:</b><br />';
    $lRet.= '<textarea id="apl-comment" rows="5" class="inp w400"></textarea>';

    $lRet.= '<div id="apl-upload" style="display:none">';
    CCor_Cfg::set('flink', true);
    $lRes = CCor_Res::getByKey('alias', 'fie');
    $lFie = $lRes['file_upload_dms'];
    $lFac = new CHtm_Fie_Fac($this->mSrc, $this->mJid);
    $lRet.= $lFac->getInput($lFie);
    $lRet.= '</div>';

    $lRet.= '</div>';
    $lRet.= $lList->getContent();

    //Add phrase approval/review dialog if phrase module is activated
    $lType = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
    $lPhraseTyp = $lType[$this -> mSrc];
    if(!empty($lPhraseTyp)){
      $lTpl = new CCor_Tpl();
      $lPhraseCss =  $lTpl -> getProjectFilename('css'.DS.'phrase.css');

      $lRet.= '<link rel="stylesheet" type="text/css" href="'.$lPhraseCss.'" />'.LF;
      $lRet.= '<link rel="stylesheet" href="htm/default/css/bootstrap.min.css" />'.LF;
      $lRet.= '<script src="js/bootstrap.min.js"></script>'.LF;
      $lRet.= '<style>body{ margin: 5px !important; }.h2 { font-size: 10pt !important; margin: 0 !important; }.modal-body .row.template{ margin: 0; text-align: center; }</style>'.LF;

      $lRet.= btn('Add Content Dialog', '', 'img/d.gif', 'button', array('id' => 'addDialogBtn', 'data-toggle' => "modal", 'data-target' => "#add_translation", 'class' => "dn")).NB;
      $lDlg = new CJob_Cms_Content_Dialog($this -> mJid, $this -> mSrc, $lJob, 'add_translation');
      $lRet.= $lDlg -> getContent();

      $lRet.= btn('Approve/Review Dialog', '', 'img/d.gif', 'button', array('id' => 'approveReviewDialogBtn', 'data-toggle' => "modal", 'data-target' => "#approve_review", 'class' => "dn")).NB;
      $lDlg = new CJob_Cms_Content_Dialog($this -> mJid, $this -> mSrc, $lJob, 'approve_review');
      $lRet.= $lDlg -> getContent();
    }

    $this->render($lRet);
  }

  protected function actSet() {
    $lId = $this->getInt('id');
    $lState = CApl_State::createFromId($lId);
    $lState->setState($this->getInt('flag'), $this->getReq('comment'));

    $lSubId = $lState['sub_loop'];

    $lDat = CApl_Subloop::createFromId($lSubId);

    $lSub = new CJob_Apl2_Sub($lDat);
    $lLoop = $lDat->getParent();
    if ($lLoop) {
      $lLoop->sendMails();
      if ($lLoop->isComplete()) {
        $lLoop->setCompleted();
      }
    }
    $lRet['content'] = $lSub->getContent();
    $lRet['icons'] = CJob_Apl2_Loop::getSubIconSummary($lDat);
    echo Zend_Json::encode($lRet);
    exit;
  }

  protected function actReset() {
    //var_dump($_REQUEST);
    $lId = $this->getInt('id');
    $lDat['comment'] = '';
    $lDat['status'] = 0;
    $lDat['done'] = 'N';
    $lState = CApl_State::createFromId($lId);
    $lState->update($lDat);

    $lSubId = $lState['sub_loop'];

    $lDat = CApl_Subloop::createFromId($lSubId);
    $lSub = new CJob_Apl2_Sub($lDat);
    $lRet['content'] = $lSub->getContent();
    $lRet['icons'] = CJob_Apl2_Loop::getSubIconSummary($lDat);
    echo Zend_Json::encode($lRet);
    exit;
  }

  protected function actRestart() {
    $lId = $this->getInt('id');
    $lComment = $this->getReq('comment');
    $lState = CApl_State::createFromId($lId);
    $lState->setState(CApp_Apl_Loop::APL_STATE_AMENDMENT, $lComment);
    //exit;

    $lSid = $this->getInt('sid');
    $lAll = $this->getInt('all');
    $lNewSub = CApl_Subloop::restart($lSid, $lComment, $lAll);

    $lSub = new CJob_Apl2_Sub($lNewSub);
    $lRet['content'] = $lSub->getContent();
    $lRet['icons'] = CJob_Apl2_Loop::getSubIconSummary($lNewSub);
    echo Zend_Json::encode($lRet);
    exit;
  }

  protected function actShowprevious() {
    $lUsr = CCor_Usr::getInstance();
    $lOld = $lUsr->getPref('job-apl.showallsub');
    $lUsr->setPref('job-apl.showallsub', !$lOld);
    $this->redirect();
  }

  protected function actReloadcommit() {
    $lLoop = new CApp_Apl_Loop($this->mSrc, $this->mJid);
    $lLid = $lLoop->getLastLoop();
    $lRet = CApp_Apl_Loop::getAplCommitList($lLid, '#');
    echo $lRet;
    exit;
  }

}
