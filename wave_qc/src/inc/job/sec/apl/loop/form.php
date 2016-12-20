<?php
class CInc_Job_Sec_Apl_Loop_Form extends CHtm_Form {

  protected $mUserlist;
  protected $mIsPrjMaster;
  protected $mPrjMaster;
  protected $mUserFiles;

  public function __construct($aSrc, $aJobId, $lCap, $aFla, $aApl, $aPrjMaster) {
    parent::__construct('job-'.$aSrc.'.sapl', $lCap, 'job-'.$aSrc.'.edt&jobid='.$aJobId);

    $lUsr = CCor_Usr::getInstance();
    $this -> mPrjMaster = $aPrjMaster;
    $this -> mIsPrjMaster = ($lUsr -> getVal('id') == $aPrjMaster);
    $this -> mSrc = $aSrc;
    $this -> mJid = $aJobId;
    $this -> mUserlist = array();
    $this -> mUserFiles = '';

    $this -> setAtt('style', 'width:700px');
    $this -> setParam('src', $this -> mSrc);
    $this -> setParam('jid', $this -> mJid);
    $this -> setParam('jobid', $this -> mJid);
    $this -> setParam('flag', $aFla);
    $this -> setParam('apl', $aApl);
    $this -> addDef(fie('msg', lan('lib.msg'), 'memo', NULL, array('style' => 'width:500px;', 'rows' => '18')));
  }

  public function setUsers($aUsers) {
    $this -> mUserlist = $aUsers;
  }

  protected function getForm() {
    $lCom = parent::getForm();
    $lRet = '';

    // Annotationen
    if (!CCor_Cfg::get('wec.api.annotation', False)) {
      $lFac = new CJob_Fac($this -> mSrc, $this -> mJid);
      $lJob = $lFac -> getDat();
      $lAnn = new CJob_Apl_Page_Annotations($lJob);
      $lRet.= '<div class="tbl w800" ><div class="th1" >'.lan('lib.annotations').'</div></div>';
      $lRet.= $lAnn -> getAnnotationList($this -> mIsPrjMaster).LF;
      $lRet.= $lAnn -> getHiddenElements().LF;
    }

    // Kommentar
    $lRet.= '<div class="th1">'.lan('lib.msg').'</div>'.$lCom;

    return $lRet;
  }

  protected function exgetFieldForm() {
    $lRet = parent::getFieldForm();


    return $lRet;
  }

  protected function getButtons() {
    $lRet = '';
    $lRet.= '<input type="hidden" name="listuserfiles" value="'.htm($this -> mUserFiles).'" />';
    $lRet.= parent::getButtons();
    return $lRet;
  }

  protected function getTitle() {
    return '';
  }

  protected function preTitle() {
    return parent::getTitle();
  }

  protected function getFormTag() {
    $lRet = '';

    $lRet = '<div class="tbl w800">';
    $lRet.= '<!-- '.get_class($this).'getFormTag preTitle -->'.LF;
    $lRet.= $this -> preTitle();
    $lRet.= '</div>';

    $lRet.= $this -> getUpload();
    $lRet.= parent::getFormTag();
    return $lRet;
  }

  protected function getJs() {
    $lRet = '';
    // $lRet.= $this -> getUpload();
    // $lRet.= parent::getJs();
    return $lRet;
  }


  public function getUpload() {

    $lApl = new CApp_Apl_Loop($this -> mSrc, $this -> mJid, 'apl');
    $lFiles = $lApl -> getCurrentUserFiles(CCor_Usr::getAuthId());

    $lAtt = array('class' => 'btn w200');
    $lDiv = "dx18upload";
    $lSub = "doc";


    $lJs = 'Flow.Std.ajxImg("'.$lDiv.'","'.lan('lib.file.from').'"); new Ajax.Updater("'.$lDiv.'","index.php",{parameters:';
    $lJs.= '{act:"job-apl-page-fil.upload",src:"'.$this -> mSrc.'",jid:"'.$this -> mJid.'",sub:"'.$lSub.
           '",div:"'.$lDiv.
           '",fid:"'.$this -> mFrmId.
           '",uid:"'.CCor_Usr::getAuthId().'" } ';
    $lJs.= '});';

    $lRet = '';
    $lTid = 'FilIdX';
    $lRet.= '<div class="tbl w800">';
    $lRet.= '<div class="th1" onclick="Flow.Std.togTr(\''.$lTid.'\')">'.htm(lan('job-fil.menu')).': '.lan('job-fil.doc').'</div>';

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
    $lRet.= '</div>';
    return $lRet;
  }

}