<?php
class CInc_Job_Apl_Loop_Form extends CHtm_Form {

  protected $mSrc = '';
  protected $mJobId = 0;
  protected $mFlag = 0;
  protected $mUserlist = array();
  protected $mIsPrjMaster = FALSE;
  protected $mPrjMaster = 0;
  protected $mUsrId = 0;
  protected $mUserFiles = '';
  protected $mActionDesc = '';
#  protected $mUpload = '';

  public function __construct($aSrc, $aJobId, $lCap, $aFla, $aApl, $aPrjMaster, $aPage = FALSE) {
    if ($aPage) {
      $lAct    = 'job-apl-page.sapl';
      $lCancel = 'job-apl-page&src='.$aSrc.'&jid='.$aJobId;
    } else {
      $lAct    = 'job-'.$aSrc.'.sapl';
#      $lCancel = 'job-'.$aSrc.'.edt&jobid='.$aJobId;
      $lCancel = 'job-apl&src='.$aSrc.'&jobid='.$aJobId;
    }
    parent::__construct($lAct, $lCap, $lCancel);

    $lUsr = CCor_Usr::getInstance();
    $this -> mUsrId = $lUsr -> getVal('id');
    $this -> mPrjMaster = $aPrjMaster;
    $this -> mIsPrjMaster = ($this -> mUsrId == $aPrjMaster);
    $this -> mFlag = $aFla;
    $this -> mSrc   = $aSrc;
    $this -> mJobId = $aJobId;

    $this -> setAtt('class', 'w800');
    $this -> setAtt('style', 'width:801px');//fuer den Kommentar-Block
    $this -> setParam('src', $this -> mSrc);
    $this -> setParam('jid', $this -> mJobId);
    $this -> setParam('jobid', $this -> mJobId);
    $this -> setParam('flag', $aFla);
    $this -> setParam('apl', $aApl);
    $this -> getActionField();
    $this -> addDef(fie('msg', lan('lib.msg'), 'memo', NULL, array('style' => 'width:500px;', 'rows' => '18')));
  }

  public function setUsers($aUsers) {
    $this -> mUserlist = $aUsers;
  }

  public function setJob($aJob) {
    $this->mJob = $aJob;
  }

  public function getJob() {
    if (!isset($this->mJob)) {
      $lFac = new CJob_Fac($this -> mSrc, $this -> mJobId);
      $this -> mJob = $lFac -> getDat();
    }
    return $this -> mJob;
  }

  protected function getForm() {
    $lCom = parent::getForm();
    $lRet = '';

    // Annotationen
    if (!CCor_Cfg::get('wec.api.annotation', False)) {
      $this -> dbg('Read Annotations across XFDF Files');
      $lJob = $this->getJob();
      $lAnn = new CJob_Apl_Page_Annotations($lJob);
      $lRet.= '<div class="tbl w800"><div class="th1">'.lan('lib.annotations').'</div></div>';
      $lRet.= $lAnn -> getAnnotationList($this -> mIsPrjMaster).LF;
      $lRet.= $lAnn -> getHiddenElements().LF;
    }

    // Kommentar
    $lRet.= '<div class="th1">'.lan('lib.msg').'</div>'.$this -> mActionDesc.$lCom;
    $lNeedSignature = CCor_Cfg::getFallback('job.apl.'.$this->mSrc.'.signature', 'job.apl.signature', false);
    if ($lNeedSignature) {
      $lRet.= $this->getSignatureForm();
    }
    return $lRet;
  }
  
  protected function getActionField() {
    $lAplType = CCor_Res::extract('code', 'flags', 'apltypes'); //apl type flags
    $lGru = CCor_Res::extract('id', 'name', 'gru'); //list of groups
    
    $lSql = 'SELECT typ FROM al_job_apl_loop WHERE 1 AND src='.esc($this -> mSrc).' AND typ LIKE '.esc('apl%');
    $lSql.= ' AND mand='.intval(MID).' AND jobid='.esc($this -> mJobId).' AND status="open" ORDER BY id DESC';
    $lType = CCor_Qry::getStr($lSql);//get current apl type
    
    //check against type if flag is set bitset
    $lFlags = bitset($lAplType[$lType], atAction);
    if($lFlags){
      $lApl = new CApp_Apl_Loop($this -> mSrc, $this -> mJobId, $lType);
      $lLoopId = $lApl -> getLastOpenLoop();
      
      $lArr = array();
      $lSql = 'SELECT * FROM al_job_apl_states WHERE 1 AND loop_id='.intval($lLoopId).' AND del="N" AND user_id='.$this -> mUsrId;
      $lQry = new CCor_Qry($lSql);//get current apl type
      foreach ($lQry as $lRow){
        $lId = $lRow['id'];
        $lStatus = $lRow['status'];
        $lGruId = $lRow['gru_id'];
        $lName = $lRow['name'];
        
        if($lStatus < 1) {
          $lArr[$lId] = ($lGruId > 0) ? $lGru[$lGruId] : $lName;
        }
      }
      
      $this -> mActionDesc = '<div style="padding:4px;background: #ed7860;" class="frm"><b style="color: #FFF;">'.lan('action_for.msg').'</b></div>';
      if(count($lArr) < 2){
        $this -> setVal('action_for', implode(",", array_keys($lArr)));
      }
      $this -> addDef(fie('action_for', lan('job-apl.actionfor'), 'checkboxlist', $lArr));
      
      $lJs = '';
      $lJs.= 'jQuery(function() {'.LF;
      $lJs.= '  var lForm = jQuery("#'.$this -> mFrmId.'");'.LF;
      $lJs.= '  lForm.submit(function(aEvent) {'.LF;
      $lJs.= '    aEvent.preventDefault();'.LF;
      $lJs.= '    var action = jQuery("*[name=\'val[action_for]\']").val();'.LF;
      $lJs.= '    if (action !== "") {'.LF;
      $lJs.= '      lForm.unbind("submit");'.LF;
      $lJs.= '      lForm.submit();'.LF;
      $lJs.= '    } else {'.LF;
      $lJs.= '      alert("'.lan('action_for.msg').'");'.LF;
      $lJs.= '    }'.LF;
      $lJs.= '  }); //submit'.LF;
      $lJs.= '}); //ondocready'.LF;
      $lPag = CHtm_Page::getInstance();
      $lPag->addJs($lJs);
    }
  }

  protected function getSignatureForm() {
    $lId = uniqid();

    $lRet = '';

    $lRet.= '<div class="p16">';

    $lRet.= '<table cellpadding="2">';
    $lRet.= '<tr><td>Username</td>';
    $lRet.= '<td><input type="text" class="inp200" id="sig_user" name="sig[user_'.$lId.']" /></td></tr>';
    $lRet.= '<tr><td>Password</td>';
    $lRet.= '<td><input type="password" class="inp200" id="sig_pass" name="sig[pass_'.$lId.']" /></td></tr>';

    $lRet.= '</table>';
    $lRet.= '</div>';

    $lJs = '';
    $lJs.= 'jQuery(function() {'.LF;
    $lJs.= 'var lForm = jQuery("#'.$this -> mFrmId.'");'.LF;
    $lJs.= 'lForm.submit(function(aEvent) {'.LF;
    $lJs.= 'aEvent.preventDefault();'.LF;
    $lJs.= 'var user = jQuery("#sig_user").val();'.LF;
    $lJs.= 'var pass = jQuery("#sig_pass").val();'.LF;
    $lJs.= 'var params = {"user":user, "pass":pass};'.LF;
    $lJs.= 'jQuery.post("index.php?act=ajx.checkCredentials", params, function(aData){'.LF;
    $lJs.= 'if ("ok" == aData) {'.LF;
    $lJs.= '  lForm.unbind("submit");'.LF;
    $lJs.= '  lForm.submit();'.LF;
    $lJs.= '} else {'.LF;
    $lJs.= '  jQuery("#sig_user").addClass("cr");jQuery("#sig_pass").addClass("cr");'.LF;
    $lJs.= '  alert("Invalid username or password!");'.LF;
    $lJs.= '}'.LF;

    $lJs.= '}); //callback'.LF;
    $lJs.= '}); //submit'.LF;
    $lJs.= '}); //ondocready'.LF;
    $lPag = CHtm_Page::getInstance();
    $lPag->addJs($lJs);

    $lPnl = new CHtm_Panel('Signature', $lRet);
    //$lPnl -> setDivAtt('class', '');
    $lPnl -> setAtt('class', 'th2');
    $lPnl -> setDivAtt('class', 'frm');
    $lRet = $lPnl -> getContent();

    return $lPnl -> getContent();
  }


  protected function getButtons() {
    $lRet = '';
    $lRet.= '<input type="hidden" name="listuserfiles" value="'.htm($this -> mUserFiles).'" />';
    $lRet.= parent::getButtons();
    return $lRet;
  }

  protected function getTitle() {
    return '<div class="th1">'.img('ico/16/flag-0'.$this ->mFlag.'.gif').' '.htm($this -> mCap).'</div>'.LF;
  }

  protected function preTitle() {
    return parent::getTitle();
  }

  protected function getFormTag() {
    $lRet = '';
    $lRet = '<div class="tbl w800">';
    $lRet.= '<!-- '.get_class($this).' getJs preTitle -->'.LF;
    $lRet.= $this -> preTitle();
    $lRet.= '</div>';
    $lRet.= '<!-- '.get_class($this).' getJs mUpLoad -->'.LF;
    $lRet.= $this -> getUpload();
    $lRet.= parent::getFormTag();
    return $lRet;
  }

  protected function getJs() {
    $lRet = '';
    return $lRet;
  }

  public function getUpload() {
    $lUid = CCor_Usr::getAuthId();
    $lApl = new CApp_Apl_Loop($this -> mSrc, $this -> mJobId, 'apl');
    $lFiles = $lApl -> getCurrentUserFiles($lUid);

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
    $lJs.= '{act:"job-apl-page-fil.upload",src:"'.$this -> mSrc.'",jid:"'.$this -> mJobId.'",sub:"'.$lSub.
           '",div:"'.$lDiv.
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