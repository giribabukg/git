<?php
class CInc_Job_Flag_Fil_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('job.files');
  }

  protected function actUpload() {
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jid');
    $lSub = $this -> getReq('sub');
    $lDiv = $this -> getReq('div');
    $lFid = $this -> getReq('fid');
    $lFlag = $this -> getReq('typ');

    $lVie = new CJob_Flag_Fil_Form($lSrc, $lJid, $lFlag, $lSub, $lDiv, $lFid);
    $lVie -> render();
  }

  protected function actSupload() {
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jobid');
    $lSub = $this -> getReq('sub');
    $lDiv = $this -> getReq('div');
    $lFid = $this -> getReq('fid');
    $lFlag = $this -> getReq('typ');

    $lUid = CCor_Usr::getAuthId();
    $lUrl = 'job-flag-fil.get';
    
    if ($lSub != 'pdf') {
      $lCls = new CApp_Finder($lSrc, $lJid);
      $lDir = $lCls -> getPath($lSub);

      $lFil = $_FILES['file'];
      $lUpl = new CCor_Upload();
      $lRes = $lUpl -> doUpload($lFil['tmp_name'], $lDir, $lFil['name'], umAddIndex);
      if (!$lRes) {
      } else {
        CCor_Usr::insertJobFile($lSrc, $lJid, $lSub, $lRes);
        $lHis = new CApp_His($lSrc, $lJid);
        $lMsg = sprintf(lan('filupload.success'),$lFil['name']);
        $lHis -> add(htFileupload, lan('filupload.his.msg'), $lMsg);
        $lFile = htm($lRes);
        $lFile = $lRes;
        if (!empty($lUid)) {
          $lApl = new CApp_Apl_Loop($lSrc, $lJid, $lFlag);
          $lApl -> addToFlagFiles($lUid, $lFile);
        }
        echo '<script type="text/javascript">parent.window.Flow.Std.ajxAplPageFil("'.$lDiv.'","'.$lSrc.'","'.$lJid.'","'.$lUrl.'","'.$lFlag.'","'.$lSub.'","'.$lFile.'","'.$lFid.'","'.$lFlag.'")</script>';
        exit;
      }
    } else {
      $lFil = $_FILES['file']['name'];
      $lFilCon = file_get_contents($_FILES['file']['tmp_name']);

      $lQry = new CApi_Alink_Query('putFile');
      $lQry -> addParam('sid', MAND);
      $lQry -> addParam('jobid', $lJid);
      $lQry -> addParam('filename', $lFil);
      $lQry -> addParam('data', base64_encode($lFilCon));
      $lQry -> addParam('mode', 2);
      $lRes = $lQry -> query();
      if (!$lRes) {
      } else {
        CCor_Usr::insertJobFile($lSrc, $lJid, $lSub, $lFil);
        $lFile = htm($lRes);
        $lFile = $lRes;
        if (!empty($lUid)) {
          $lApl = new CApp_Apl_Loop($lSrc, $lJid, $lFlag);
          $lApl -> addToFlagFiles($lUid, $lFile);
        }
        echo '<script type="text/javascript">parent.window.Flow.Std.ajxAplPageFil("'.$lDiv.'","'.$lSrc.'","'.$lJid.'","'.$lUrl.'","'.$lFlag.'","'.$lSub.'","'.$lFile.'","'.$lFid.'")</script>';
        exit;
      }
    }
    echo '<script type="text/javascript">parent.window.Flow.Std.ajxAplPageFil("'.$lDiv.'","'.$lSrc.'","'.$lJid.'","'.$lUrl.'","'.$lFlag.'","'.$lSub.'")</script>';
    exit;
  }

  protected function actGet() {
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jid');
    $lSub = $this -> getReq('sub');
    $lDiv = $this -> getReq('div');
    $lFid = $this -> getReq('fid');
    $lFlag = $this -> getReq('typ');

    $lJs = 'Flow.Std.ajxImg("'.$lDiv.'","'.lan('lib.file.from').'"); new Ajax.Updater("'.$lDiv.'","index.php",{parameters:';
    $lJs.= '{act:"job-flag-fil.upload",src:"'.$lSrc.
           '",jid:"'.$lJid.
           '",sub:"'.$lSub.
           '",div:"'.$lDiv.
           '",typ:"'.$lFlag.
           '",fid:"'.$lFid.
           '" } ';
    $lJs.= '});';

    $lAtt = array('class' => 'btn w200');
    $lRet = '<form id="'.getNum($lDiv).'">';
    $lRet.= btn(lan('lib.upload'), $lJs, 'img/ico/16/new-hi.gif', 'button', $lAtt).NB.BR.BR;
    $lRet.= '</form>';
    echo $lRet;
  }

}