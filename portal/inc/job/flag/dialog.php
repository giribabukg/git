<?php
class CInc_Job_Flag_Dialog extends CHtm_Form {

  protected $mApl;
  protected $mFlagEve = array();
  protected $mSrc = '';
  protected $mJobId = 0;
  protected $mUserlist = array();
  protected $mIsPrjMaster = FALSE;
  protected $mPrjMaster = 0;
  protected $mUserFiles = '';
#  protected $mUpload = '';

  public function __construct($aSrc, $aJobId, $aId, $lCap, $aVote, $aFlag, $aApl, $aImg = '') {
    $lAct    = 'job-'.$aSrc.'.sflag';
    $lCancel = 'job-'.$aSrc.'.edt&jobid='.$aJobId;
    $this -> mApl = $aApl;  //class CApp_Apl_Loop

    parent::__construct($lAct, $lCap, $lCancel);
    $this -> mImg = $aImg;

    $this -> mFlagTyp = $aFlag;
    $lAllFlags = CCor_Res::get('fla');
    if (isset($lAllFlags[$aFlag])) {
      $this -> mFlagEve = $lAllFlags[$aFlag];
    }

    $lUsr = CCor_Usr::getInstance();
    $this -> mSrc   = $aSrc;
    $this -> mJobId = $aJobId;

    $this -> setAtt('class', 'w800');
    $this -> setAtt('style', 'width:801px');//fuer den Kommentar-Block
    $this -> setParam('src', $this -> mSrc);
    $this -> setParam('jobid', $this -> mJobId);
    $this -> setParam('id', $aId);
    $this -> setParam('vote', $aVote);
    $this -> setParam('typ', $aFlag);
    $this -> addDef(fie('msg', lan('lib.msg'), 'memo', NULL, array('style' => 'width:500px;', 'rows' => '18')));
  }

  public function setUsers($aUsers) {
    $this -> mUserlist = $aUsers;
  }

  protected function getForm() {
    $lCom = parent::getForm();
    $lRet = '';

    // Kommentar
    $lRet.= '<div class="th1">'.lan('lib.msg').'</div>'.$lCom;

    return $lRet;
  }

  protected function getButtons() {
    $lRet = '';
    $lRet.= '<input type="hidden" name="listuserfiles" value="'.htm($this -> mUserFiles).'" />';
    $lBtnAtt = array();#array('class' => 'btn w100');
    $lRet.= parent::getButtons($lBtnAtt);
    return $lRet;
  }

  protected function getTitle() {
    return '';
  }

  protected function preTitle() {
    $lRet = '<div class="th1">';
    if (!empty($this -> mImg)) {
      $lRet.= img($this -> mImg).' ';
    }
    $lRet.= htm($this -> mCap).'</div>'.LF;
    return $lRet;
  }

  protected function getFormTag() {
    $lRet = '';
    $lRet = '<div class="tbl w800">';
    $lRet.= '<!-- '.get_class($this).' getJs preTitle -->'.LF;
    $lRet.= $this -> preTitle();
    $lRet.= '</div>';
    if ( bitset($this -> mFlagEve['flags_conf'], flagUploadFile) ) {
      $lRet.= '<!-- '.get_class($this).' getJs mUpLoad -->'.LF;
      $lRet.= $this -> getUpload();
    }
    $lRet.= parent::getFormTag();
    return $lRet;
  }

  protected function getJs() {
    $lRet = '';
    return $lRet;
  }

  public function getUpload() {
    $lUid = CCor_Usr::getAuthId();
    $lApl = $this -> mApl; //class CApp_Apl_Loop
    $lFiles = $lApl -> getCurrentUserFlagFiles($lUid);

    $lArr = explode(LF, $lFiles);
    $lFiles = '';
    $lRows = 0;
    foreach ($lArr as $lFile) {
      if (!(trim($lFile) == '')) {
        $lRows++;
        if ($lRows == 1) {
          $lFiles.= $lFile;
        } else {
          $lFiles.= LF.$lFile;
        }
      }
    }
    if ($lRows == 0) $lRows = 1;
    $this -> mUserFiles = $lFiles;

    $lAtt = array('class' => 'btn w200');
    $lDiv = "dx18upload";
    $lSub = "doc";

    $lJs = 'Flow.Std.ajxImg("'.$lDiv.'","'.lan('lib.file.from').'"); new Ajax.Updater("'.$lDiv.'","index.php",{parameters:';
    $lJs.= '{act:"job-flag-fil.upload",src:"'.$this -> mSrc.'",jid:"'.$this -> mJobId.'",sub:"'.$lSub.
           '",div:"'.$lDiv.
           '",typ:"'.$this -> mFlagTyp.
           '",fid:"'.$this -> mFrmId.
           '",uid:"'.$lUid.'" } ';
    $lJs.= '});';

    $lRet = '';
    $lTid = 'FilIdX';

    $lRet.= '<table cellpadding="0" cellspacing="0" class="frm" width="100%">'.LF;
    $lRet.= '<tr id="'.$lTid.'">';
    $lRet.= '<td colspan="4">';

    $lRet.= '<table cellpadding="4" cellspacing="0" class="frm" width="100%" style="border-top:0px;border-left:0px;border-bottom:0px;border-right:0px;">'.LF;
    $lRet.= '<tr>';
    $lRet.= '<td width="5%"></td>';
    $lRet.= '<td>';
    $lAli = 'userfiles';

    $lRet.= '<textarea class="frm" name="'.$lAli.'" cols="50" rows="'.$lRows.'" style="border-top:0px;border-left:0px;border-bottom:0px;border-right:0px;"'.
            ' onchange="javascript:ajxAplFiles(\''.$lAli.'\',\''.$this -> mFrmId.'\')" '.
            '>'.$this -> mUserFiles.'</textarea>';
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

    $lPnl = new CHtm_Panel(lan('job-fil.menu').': '.lan('job-fil.doc'), $lRet, 'crp.dlg.upl');
    $lPnl -> setAtt('class', 'th1');
    $lPnl -> setDivAtt('class', 'w800');
    $lRet = $lPnl -> getContent();
    $lRet = '<div class="tbl w800">'.$lRet.'</div>';
    #$lRet.= '</div></div>';

    return $lRet;
  }

}