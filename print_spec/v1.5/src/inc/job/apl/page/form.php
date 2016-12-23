<?php
class CInc_Job_Apl_Page_Form extends CHtm_Form {

  protected $mUserFiles;

  public function __construct($aSrc, $aJobId, $aNewAct = '') {
    if (!empty($aNewAct)) {
      $lNewAct = $aNewAct;
    } else {
      $lNewAct = 'job-apl-page.sapl';
    }
    parent::__construct($lNewAct, lan('lib.msg'), false);
    $this -> mSrc = $aSrc;
    $this -> mJid = $aJobId;
    $this -> mUserFiles = '';

    $this -> setAtt('class', 'tbl w800');

    $this -> setParam('src',  $aSrc);
    $this -> setParam('jid',  $aJobId);
    $this -> setParam('jobid', $aJobId);
    $this -> setParam('flag', 0); // dummy, wird durch buttons gesetzt
    $this -> setParam('apl',  0); // vorheriger Gesamt-Umlauf-Status
                                  // wird im Controller gesetzt

    $this -> addDef(fie('msg', '', 'memo', NULL,
      array('style' => 'width:500px;', 'rows' => '18')));
  }

  public function setJob($aJob) {
    $this->mJob = $aJob;
  }

  public function getUpload() {
    $lApl = new CApp_Apl_Loop($this -> mSrc, $this -> mJid, 'apl');
    $lFiles = $lApl -> getCurrentUserFiles(CCor_Usr::getAuthId());

    $lAtt = array('class' => 'btn w200');
    $lDiv = "dx18";
    $lSub = "doc";

    $lJs = 'Flow.Std.ajxImg("'.$lDiv.'","'.lan('lib.file.from').'"); new Ajax.Updater("'.$lDiv.'","index.php",{parameters:';
    $lJs.= '{act:"job-apl-page-fil.upload",src:"'.$this -> mSrc.'",jid:"'.$this -> mJid.'",sub:"'.$lSub.
           '",div:"'.$lDiv.
           '",fid:"'.$this -> mFrmId.
           '",uid:"'.CCor_Usr::getAuthId().'" } ';
    $lJs.= '});';

    $lRet = '';
    $lTid = 'FilIdX';

    $lRet.= '<table cellpadding="0" cellspacing="0" class="frm" width="100%">'.LF;

    $lRet.= '<tr id="'.$lTid.'">';
    $lRet.= '<td colspan="4">';

    $lRet.= '<table cellpadding="4" cellspacing="0" class="frm" width="100%" style="border-top:0px;border-left:0px;border-bottom:0px;border-right:0px;">'.LF;

    $lArr = explode(LF, $lFiles);
    $lFiles = '';
    $ic = 0;
    foreach ($lArr as $lFile) {
      if (!(trim($lFile) == '')) {
        $ic++;
        if ($ic == 1) {
          $lFiles.= $lFile;
        } else {
          $lFiles.= LF.$lFile;
        }
      }
    }
    if ($ic == 0) $ic = 1;

    $this -> mUserFiles = $lFiles;
    $lRet.= '<tr>';
    $lRet.= '<td width="5%"></td>';
    $lRet.= '<td>';
    $lRet.= '<td>';
    $lAli = 'userfiles';
    $lRet.= '<textarea class="frm" name="'.$lAli.'" cols="50" rows="'.$ic.'" style="border-top:0px;border-left:0px;border-bottom:0px;border-right:0px;"'.
            ' onchange="javascript:ajxAplFiles(\''.$lAli.'\',\''.$this -> mFrmId.'\')" '.
            '>'.$lFiles.'</textarea>';
    $lRet.= '</td>';

    $lRet.= '<td>';
    $lRet.= '<div id="'.$lDiv.'" style="text-align:right;">';
    $lRet.= '<form id="'.getNum($lDiv).'">';
    $lRet.= btn(lan('lib.upload'), $lJs, 'img/ico/16/new-hi.gif', 'button', $lAtt).NB.BR.BR;
    $lRet.= '</form>';
    $lRet.= '</div>';
    $lRet.= '</td>';

    $lRet.= '</tr>';
    $lRet.= '</table>';

    $lRet.= '</td>';
    $lRet.= '</tr>';
    $lRet.= '</table>';
    #$lRet.= '</div>';

    $lPnl = new CHtm_Panel(lan('job-fil.menu').': '.lan('job-fil.doc'), $lRet, 'crp.dlg.upl');
    $lPnl -> setAtt('class', 'th1');
    $lPnl -> setDivAtt('class', '');

    $lRet = $lPnl -> getContent();
    $lRet = '<div class="tbl w800">'.$lRet.'</div>';

    return $lRet;
  }

  protected function getCont() {
    $lRet = $this -> onBeforeContent();
    $lRet.= $this -> getComment('start');
    $lRet.= $this -> getFormTag();
    $lRet.= $this -> getHiddenFields();

    $lRet.= $this -> getTag();

    $lRet.= $this -> getTitle();
    $lRet.= $this -> getDescription();
    $lRet.= '<div class="frm p16">';
    $lRet.= '<div style="float:left; margin-right:16px">';
    $lRet.= $this -> getForm();
    $lRet.= '</div>';
    $lRet.= '<div style="float:left">';
    if ($this -> mButtons == TRUE) {
      $lRet.= $this -> getButtons();
    }
    $lRet.= '</div>';
    $lRet.= '<div style="clear:both"></div>';
    $lRet.= '</div>';

    $lRet.= $this -> getEndTag();

    $lRet.= '</form>'.LF;
    $lRet.= $this -> getJs();
    $lRet.= $this -> getComment('end');
    return $lRet;
  }

  protected function getForm() {
    $lRet = '<table cellpadding="4" cellspacing="0" border="0">'.LF;
    $lRet.= $this -> getFieldForm();
    $lRet.= '</table>';
    return $lRet;
  }

  protected function getButtons() {
    $lRet = '';
    $lRet.= '<input type="hidden" name="listuserfiles" value="'.htm($this -> mUserFiles).'" />';
    $lRet.= '<div class="p8">'.LF;
    $lRet.= $this->getButtonContent();
    $lRet.= '</div>'.LF;
    return $lRet;
  }

  protected function getButtonContent() {
    $lRet = '';
    $lAtt = array('class' => 'btn w200');
    $lAplButtons = CCor_Cfg::get('buttons.apl', array());
    if (!empty($lAplButtons)) {
      foreach ($lAplButtons as $lAplKey => $lAplBtn) {
        $lRet.= btn(lan('apl.'.$lAplBtn), 'this.form.flag.value='.$lAplKey.'; this.form.submit()', 'img/ico/16/flag-0'.$lAplKey.'.gif', 'button', $lAtt).NB.BR.BR;
      }
    }
    $lRet.= btn(lan('apl.comment'), 'this.form.flag.value=0; this.form.submit()', 'img/ico/16/flag-00.gif', 'button', $lAtt).NB.BR.BR;
    return $lRet;
  }

}