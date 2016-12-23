<?php
class CInc_Arc_Fil_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('arc.file');
  }

  protected function actUpload() {
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jid');
    $lSub = $this -> getReq('sub');
    $lDiv = $this -> getReq('div');

    $lForm = new CArc_Fil_Form($lSrc, $lJid, $lSub, $lDiv);
    $lForm -> render();
  }

  protected function actSupload() {
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jobid');
    $lSub = $this -> getReq('sub');
    $lDiv = $this -> getReq('div');

    if ($lSub != 'pdf') {
      $lCls = new CApp_Finder($lSrc, $lJid);
      $lDir = $lCls -> getPath($lSub);

      $lFil = $_FILES['file'];

      $lUpl = new CCor_Upload();
      $lRes = $lUpl -> doUpload($lFil['tmp_name'], $lDir, $lFil['name'], umAddIndex);
      if (!$lRes) {
      } else {
        CCor_Usr::insertJobFile($lSrc, $lJid, $lSub, $lRes);
      }

      $lParams = array(
        'act' => 'job-'.$lSrc.'-fil.get',
        'src' => $lSrc,
        'jid' => $lJid,
        'sub' => $lSub,
        'div' => $lDiv,
        'age' => 'arc',
        'loading_screen' => TRUE
      );
      $lParamsJSONEnc = json_encode($lParams);
      echo '<script type="text/javascript">parent.window.Flow.Std.ajxUpd('.$lParamsJSONEnc.')</script>';
      exit;
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

      $lParams = array(
        'act' => 'job-'.$lSrc.'-fil.get',
        'src' => $lSrc,
        'jid' => $lJid,
        'sub' => $lSub,
        'div' => $lDiv,
        'age' => 'arc',
        'loading_screen' => TRUE
      );
      $lParamsJSONEnc = json_encode($lParams);
      echo '<script type="text/javascript">parent.window.Flow.Std.ajxUpd('.$lParamsJSONEnc.')</script>';
      exit;
    }
  }

}