<?php
class CInc_Job_Fil_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('job.files');
  }

  protected function actUpload() {
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jid');
    $lSub = $this -> getReq('sub');
    $lDiv = $this -> getReq('div');

    $lForm = new CJob_Fil_Form($lSrc, $lJid, $lSub, $lDiv);
    $lForm -> render();
  }

  protected function actSupload() {
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jobid');
    $lSub = $this -> getReq('sub');
    $lDiv = $this -> getReq('div');
    $lCat = $this -> getReq('category');

    if ($lSub == 'dms') {
      $this -> actSuploaddms();
      exit;
    }

    if ($lSub == 'pixelboxx') {
      $this->actSuploadpixelboxx();
      exit;
    }
    
    if ($lSub != 'pdf') {
      $lCls = new CApp_Finder($lSrc, $lJid);
      $lDir = $lCls -> getPath($lSub);

      $lFil = $_FILES['file'];
      $lUpl = new CCor_Upload();

      if ('dalim' == $lSub) {
        $lRes = $lUpl -> doUploadVersion($lFil['tmp_name'], $lDir, $lFil['name'], '_');
        if ($lRes) {
          $lUtil = new CApi_Dalim_Utils();
          $this -> msg('Register DOC '.$lJobId.'/'.$lRes, mtApi, mlInfo);
          $lUtil -> registerDocument($lJid.'/'.$lRes);
        }
      } else {
        $lRes = $lUpl -> doUpload($lFil['tmp_name'], $lDir, $lFil['name'], umAddIndex);
      }

      if (!$lRes) {
      } else {
        CCor_Usr::insertJobFile($lSrc, $lJid, $lSub, $lRes, $lCat);
        $lHis = new CApp_His($lSrc, $lJid);
        $lMsg = sprintf(lan('filupload.success'), $lFil['name']);
        $lHis -> add(htFileupload, lan('filupload.his.msg'), $lMsg);
      }

      $lParams = array(
        'act' => 'job-'.$lSrc.'-fil.get',
        'src' => $lSrc,
        'jid' => $lJid,
        'sub' => $lSub,
        'div' => $lDiv,
        'age' => 'job',
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
      if (!$lRes) {
      } else {
        CCor_Usr::insertJobFile($lSrc, $lJid, $lSub, $lFil, $lCat);
        $lHis = new CApp_His($lSrc, $lJid);
        $lMsg = sprintf(lan('filupload.success'), $lFil);
        $lHis -> add(htFileupload, lan('filupload.his.msg'), $lMsg);
      }

      $lParams = array(
        'act' => 'job-'.$lSrc.'-fil.get',
        'src' => $lSrc,
        'jid' => $lJid,
        'sub' => $lSub,
        'div' => $lDiv,
        'age' => 'job',
        'loading_screen' => TRUE
      );
      $lParamsJSONEnc = json_encode($lParams);
      echo '<script type="text/javascript">parent.window.Flow.Std.ajxUpd('.$lParamsJSONEnc.')</script>';
      exit;
    }
  }

  protected function actSuploaddms() {
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jobid');
    $lSub = $this -> getReq('sub');
    $lDiv = $this -> getReq('div');
    $lCat = $this -> getReq('category');

    $lUid = CCor_Usr::getAuthId();
    $lFil = $_FILES['file'];
    $lTemp = $lFil['tmp_name'];
    $lOldName = $lFil['name'];
    $lTempBase = basename($lTemp);

    $lShare = CCor_Cfg::get('dms.upload.folder', '/media/dmsshare');
    $lRes = move_uploaded_file($lTemp, $lShare.$lTempBase);

    if ($lRes) {
      $lUser = CCor_Usr::getInstance();
      $lAuthor = $lUser -> getVal('fullname');
      $lQry = new CApi_Dms_Query();
// TODO: do UnitTests instead of this!
// $lStub = new CApi_Dms_Stub();
// $lQry -> setClient($lStub);
      $lQry -> uploadFile($lTempBase, $lOldName, $lAuthor, MANDATOR_ENVIRONMENT, $lSrc, $lJid);
      CCor_Usr::insertJobFile($lSrc, $lJid, 'dms', $lRes, 1105);
    }

    $lParams = array(
      'act' => 'job-'.$lSrc.'-fil.get',
      'src' => $lSrc,
      'jid' => $lJid,
      'sub' => $lSub,
      'div' => $lDiv,
      'age' => 'job',
      'loading_screen' => TRUE
    );
    $lParamsJSONEnc = json_encode($lParams);
    echo '<script type="text/javascript">parent.window.Flow.Std.ajxUpd('.$lParamsJSONEnc.')</script>';
    exit;
  }

  protected function actSuploadpixelboxx() {
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jobid');
    $lSub = $this -> getReq('sub');
    $lDiv = $this -> getReq('div');
    $lCat = $this -> getReq('category');
  
    $lUid = CCor_Usr::getAuthId();
    $lFil = $_FILES['file'];
    $lTemp = $lFil['tmp_name'];
    $lOldName = $lFil['name'];
    $lTempBase = basename($lTemp);
    
    $lUser = CCor_Usr::getInstance();
    
    $lQry = new CApi_Pixelboxx_Query_Importfile();
    $lQry->addAttribute('category', $lCat);
    $lAuthor = $lUser->getVal('fullname');
    $lQry->addAttribute('byline', $lAuthor);
    
    $lSql = 'SELECT pbox_doi FROM al_job_shadow_' . MID . ' WHERE src=' .
        esc($lSrc) . ' AND jobid=' . esc($lJid);
    $lDoi = CCor_Qry::getStr($lSql);
    
    $lRes = $lQry->upload($lDoi, $lTemp, $lOldName);
    
    if ($lRes) {
      $lUser = CCor_Usr::getInstance();
      $lAuthor = $lUser->getVal('fullname');
      $lQry = new CApi_Dms_Query();
      #$lStub = new CApi_Dms_Stub(); $lQry->setClient($lStub);
      $lQry->uploadFile($lTempBase, $lOldName, $lAuthor, MANDATOR_ENVIRONMENT, $lSrc, $lJid);
      CCor_Usr::insertJobFile($lSrc, $lJid, 'pixelboxx', $lRes, 1105);
    }
    $lParams = array(
      'act' => 'job-'.$lSrc.'-fil.get',
      'src' => $lSrc,
      'jid' => $lJid,
      'sub' => $lSub,
      'div' => $lDiv,
      'age' => 'job',
      'loading_screen' => TRUE
    );
    $lParamsJSONEnc = json_encode($lParams);
    echo '<script type="text/javascript">parent.window.Flow.Std.ajxUpd('.$lParamsJSONEnc.')</script>';
    exit;
  }
  
  protected function actCompareWec() {
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jid');
    $lDoc = $this -> getReq('doc');

    $lUsr = CCor_Usr::getInstance();
    $lWecUid = $lUsr -> getInfo('wec_uid');

    $lWec = new CApi_Wec_Client();
    $lWec -> loadConfig(CCor_Cfg::get('wec.useext', FALSE));

    $lUrl = $lWec -> getUsedURL().'OpenViewer.jsp?';
    $lUrl.= 'username='.urlencode($lUsr -> getInfo('wec_usr'));
    $lUrl.= '&password='.urlencode(base64_encode($lUsr -> getInfo('wec_pwd')));
    foreach ($lDoc as $lName) {
      $lArr = explode('/', $lName, 2);
      $lUrl.= '&projectid='.$lArr[0];
      $lUrl.= '&documentname='.$lArr[1];
    }
    $lUrl.= '&comparemode=1';

    $lRetUrl = CCor_Cfg::get('base.url').'index.php?act=utl-wec.close';
    $lRetUrl.= '&src='.$lSrc.'&jid='.$lJid.'&sub=wec';

    $lUrl.= '&returnurl='.urlencode($lRetUrl);

    echo $lUrl;
    exit;
  }

  protected function actFilelinkUpload() {
    $lFId = $this -> getReq('fid'); // alias (e.g. file_upload)
    $lSrc = $this -> getReq('src'); // job source/type: art, rep, etc.
    $lJId = $this -> getReq('jid'); // job id
    $lSub = $this -> getReq('sub'); // sub (currently available: dalim, dms, doc, pdf, rtp, wec) from job field
    $lDiv = $this -> getReq('div'); // div id
    $lAge = $this -> getReq('age'); // active (job) or archived (arc) job
    $lCat = $this -> getReq('cat'); // active (job) or archived (arc) job

    $lCls = new CApp_Finder($lSrc, $lJId);
    if ($lFId) { // single file upload
      $lAlias = CCor_Res::extract('alias', 'param', 'fie', array('typ' => 'file'));
      $lParam = (isset($lAlias[$lFId])) ? toArr($lAlias[$lFId]) : NULL;

      $lDir = (isset($lParam['folder'])) ? $lParam['folder'] : '';
      $lDir = $lCls -> getDynPath($lDir);

      $lURL = (isset($lParam['url'])) ? $lParam['url'] : '';
      $lURL = $lCls -> getDynPath($lURL);

      $lOverwrite = (isset($lParam['overwrite'])) ? TRUE : FALSE;
    } else { // mutliple file upload
      if ($lSub != 'doc') {
        $lDir = CCor_Cfg::get('flink.destination.'.$lSub.'.dir', '');
        $lDir = $lCls -> getDynPath($lDir);

        $lURL = CCor_Cfg::get('flink.destination.'.$lSub.'.url', '');
        $lURL = $lCls -> getDynPath($lURL);

        $lOverwrite = CCor_Cfg::get('flink.destination.'.$lSub.'.overwrite', FALSE);
      } else {
        $lDir = $lCls -> getPath($lSub);
        $lURL = '';
      }
    }

    $lRet = array(
      'fid' => $lFId,
      'src' => $lSrc,
      'jid' => $lJId,
      'sub' => $lSub,
      'div' => $lDiv,
      'age' => $lAge,
      'cat' => $lCat,
      'upload_dir' => $lDir,
      'upload_url' => $lURL,
      'discard_aborted_uploads' => FALSE,
      'overwrite' => $lOverwrite
    );

    $lUploadHandler = new CJob_Fil_Uploadhandler($lRet);
  }

  protected function actFilelinkSupload() {
    $lFId = $this -> getReq('fid'); // alias (e.g. file_upload)
    $lSrc = $this -> getReq('src'); // job source/type: art, rep, etc.
    $lJId = $this -> getReq('jid'); // job id
    $lSub = $this -> getReq('sub'); // sub (currently available: dalim, dms, doc, pdf, rtp, wec) from job field
    $lFil = $this -> getReq('fil'); // new filename
    $lOld = $this -> getReq('old'); // old filename
    $lCat = $this -> getReq('cat'); // category

    $lAlias = CCor_Res::extract('alias', 'param', 'fie', array('typ' => 'file'));
    $lParam = (isset($lAlias[$lFId])) ? toArr($lAlias[$lFId]) : NULL;

    $lDir = (isset($lParam['folder'])) ? $lParam['folder'] : '';

    $lParam['flink'] = TRUE;
    $lParam['oldfilename'] = $lOld;
    $lParam['category'] = $lCat;

    $lUpload = new CJob_Fil_Upload($lSrc, $lJId);
    $lUpload -> uploadFile($lDir.$lFil, $lFil, $lParam);
  }

  protected function actFilelinkUploadReg() {
      $lSrc = $this -> getReq('src'); // job source/type: art, rep, etc.
      $lJId = $this -> getReq('jid'); // job id
      $lSub = $this -> getReq('sub'); // sub (currently available: dalim, dms, doc, pdf, rtp, wec) from job field
      $lFil = $this -> getReq('fil'); // filename
      $lCat = $this -> getReq('cat'); // category

      CCor_Usr::deleteJobFile($lSrc, $lJId, $lSub, $lFil);
      CCor_Usr::insertJobFile($lSrc, $lJId, $lSub, $lFil, $lCat);

      $lHis = new CApp_His($lSrc, $lJId);
      $lMsg = sprintf(lan('filupload.success'), $lFil);
      $lHis -> add(htFileupload, lan('filupload.his.msg'), $lMsg);

      $lMod = new CJob_Mod($lSrc, $lJId);
      $lMod -> triggerEvent('eve_upload');

      return TRUE;
  }

  protected function actFilelinkUploadTxt() {
      $lSrc = $this -> getReq('src'); // job source/type: art, rep, etc.
      $lJId = $this -> getReq('jid'); // job id
      $lSub = $this -> getReq('sub'); // sub (currently available: dalim, dms, doc, pdf, rtp, wec) from job field
      $lFil = $this -> getReq('fil'); // filename
      $lTxt = $this -> getReq('txt'); // text

      CCor_Usr::updateTxt($lSrc, $lJId, $lSub, $lFil, $lTxt);

      return TRUE;
  }

  protected function actDeldalim() {
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jid');
    $lDiv = $this -> getReq('div');
    $lFn  = $this -> getReq('fn');

    $lFiles = new CApi_Dalim_Files($lSrc, $lJid);
    $lFiles -> delete($lFn);

    $lParams = array(
      'act' => 'job-'.$lSrc.'-fil.get',
      'src' => $lSrc,
      'jid' => $lJid,
      'sub' => 'dalim',
      'div' => $lDiv,
      'age' => 'job',
      'loading_screen' => TRUE
    );
    $lParamsJSONEnc = json_encode($lParams);
    echo '<script type="text/javascript">parent.window.Flow.Std.ajxUpd('.$lParamsJSONEnc.')</script>';
    exit;
  }

  protected function actReregdalim() {
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jid');
    $lDiv = $this -> getReq('div');
    $lFn  = $this -> getReq('fn');

    $lFiles = new CApi_Dalim_Files($lSrc, $lJid);
    $lFiles -> unregister($lFn);
    $lFiles -> register($lFn);

    $lParams = array(
      'act' => 'job-'.$lSrc.'-fil.get',
      'src' => $lSrc,
      'jid' => $lJid,
      'sub' => 'dalim',
      'div' => $lDiv,
      'age' => 'job',
      'loading_screen' => TRUE
    );
    $lParamsJSONEnc = json_encode($lParams);
    echo '<script type="text/javascript">parent.window.Flow.Std.ajxUpd('.$lParamsJSONEnc.')</script>';
    exit;
  }

  protected function actChangeCategory() {
    $lSrc = $this -> getReq('src'); // job source/type: art, rep, etc.
    $lJId = $this -> getReq('jid'); // job id
    $lSub = $this -> getReq('sub'); // sub (currently available: dalim, dms, doc, pdf, rtp, wec) from job field
    $lFil = $this -> getReq('fil'); // filename
    $lNew = $this -> getReq('new'); // new category
    $lDiv = $this -> getReq('div'); // div id

    $lSQL = 'UPDATE al_job_files SET category='.esc($lNew).' WHERE src='.esc($lSrc).' AND jobid='.esc($lJId).' AND sub='.esc($lSub).' AND filename='.esc($lFil);
    CCor_Qry::exec($lSQL);

    return TRUE;
  }

  protected function actSwitchCategoryView() {
    $lSrc     = $this -> getReq('src');
    $lCatView = $this -> getReq('catview');

    $lRet = (intval($lCatView) == 0) ? 1 : 0;

    $lUser = CCor_Usr::getInstance();
    $lUser -> setPref('job-'.$lSrc.'.fil.cat.view', $lRet);

    return TRUE;
  }

  protected function actWecUpl() {
    $lDiv = $this -> getReq('div');
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jid');
    $lSub = $this -> getReq('sub');
    $lNam = $this -> getReq('name');

    if (!in_array($lSrc, array('pro', 'sku')) && $lSub == 'pdf') {
      $lUploadViaAlink = CCor_Cfg::get('wec.upload.alink', TRUE);

      if ($lUploadViaAlink) {
        $lWec = new CApp_Wec($lSrc, $lJid);
        $lWecPrjId = $lWec -> createWebcenterProject();

        $lQry = new CApi_Alink_Query('uploadToWebCenter');
        $lQry -> addParam('prjprefix', CCor_Cfg::get('wec.prjprefix'));
        $lQry -> addParam('sid', MAND);
        $lQry -> addParam('jobid', $lJid);
        $lQry -> addParam('filename', $lNam);
        $lRes = $lQry -> query();
        $lErr = $lRes -> getVal('errno');
        if (0 == $lErr) {
          CCor_Usr::uploadedJobFile($lSrc, $lJid, $lSub, $lNam);

          $lQue = new CApp_Queue('wecthumb');
          $lQue -> setParam('src', $lSrc);
          $lQue -> setParam('jobid', $lJid);
          $lQue -> insert();
        }
      } else {
        $lSql = 'SELECT wec_prj_id FROM al_job_shadow_'.MID.' ';
        $lSql.= 'WHERE jobid='.esc($lJid);

        $lWecPrjId = CCor_Qry::getStr($lSql);

        if (!$lWecPrjId) {
          $lAppWec = new CApp_Wec($lSrc, $lJid);
          $lWecPrjId = $lAppWec -> createWebcenterProject();
        }

        $lOnServer = CCor_Cfg::get('wec.upload.onserver');

        if ($lOnServer) {
          $lCls = new CApp_Finder($lSrc, $lJid);
          $lURL = $lCls -> getDynPath($lOnServer);

          $lWec = new CApi_Wec_Client();
          $lWec -> loadConfig();

          $lQry = new CApi_Wec_Query_Upload($lWec);
          $lRes = $lQry -> upload($lWecPrjId, $lURL.$lNam, $lNam);
          if ($lRes) {
            CCor_Usr::insertJobFile($lSrc, $lJid, 'pdf', $lNam, 1105);
            CCor_Usr::uploadedJobFile($lSrc, $lJid, 'pdf', $lNam);

            $lQue = new CApp_Queue('wecthumb');
            $lQue -> setParam('src', $lSrc);
            $lQue -> setParam('jobid', $lJid);
            $lQue -> insert();

          }
        }
      }
    }

    $lCls = 'CJob_Fil_Src_'.ucfirst($lSub);
    $lVie = new $lCls($lSrc, $lJid, $lSub, $lDiv);
    $lVie -> render();
    exit;
  }

  protected function actDel() {
    $lDiv = $this -> getReq('div');
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jid');
    $lSub = $this -> getReq('sub');
    $lNam = $this -> getReq('name');

    if (in_array($lSrc, array('pro', 'sku')) || $lSub != 'pdf') {
      $lFin = new CApp_Finder($lSrc, $lJid);
      $lFil = $lFin -> getName($lNam, $lSub);
      if (is_readable($lFil)) {
        // delete file
        unlink($lFil);

        // delete item from database
        CCor_Usr::deleteJobFile($lSrc, $lJid, $lSub, $lNam);

        // add notification to history
        $lPathinfo = pathinfo($lFil);
        $lHis = new CApp_His($lSrc, $lJid);
        $lMsg = sprintf(lan('fildelete.success'), $lPathinfo['basename']);
        $lHis -> add(htFiledelete, lan('fildelete.his.msg'), $lMsg);
      }
    } else {
      // delete file
      $lQry = new CApi_Alink_Query('deleteFile');
      $lQry -> addParam('sid', MAND);
      $lQry -> addParam('jobid', $lJid);
      $lQry -> addParam('filename', $lNam);
      $lRes = $lQry -> query();
      $lErr = $lRes-> getVal('errno');
      if (0 == $lErr) {
        // delete item from database
        CCor_Usr::deleteJobFile($lSrc, $lJid, $lSub, $lNam);

        // add notification to history
        $lPathinfo = pathinfo($lFil);
        $lHis = new CApp_His($lSrc, $lJid);
        $lMsg = sprintf(lan('fildelete.success'), $lPathinfo['basename']);
        $lHis -> add(htFiledelete, lan('fildelete.his.msg'), $lMsg);
      }
    }
    $this->renderList();
  }

  protected function renderList() {
    $lDiv = $this -> getReq('div');
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jid');
    $lSub = $this -> getReq('sub');
    $lAge = $this -> getReq('age');
    
    $lCls = 'CJob_Fil_Src_'.ucfirst($lSub);
    $lVie = new $lCls($lSrc, $lJid, $lSub, $lDiv);
    $lVie -> render();
    exit;
  }
  
  protected function actPboxdetails() {
    $lDoi = $this -> getReq('doi');
    $lDiv = $this -> getReq('div');
    $lSrc = $this -> getReq('src');
    $lJid = $this -> getReq('jid');
    $lSub = $this -> getReq('sub');
    $lAge = $this -> getReq('age');
    
    $lTpl = new CJob_Fil_Pixelboxx_Details($lDoi, $lDiv, $lSrc, $lJid, $lSub, $lAge);
    $lTpl->render();
  }

  protected function actPboxdel() {
    $lDoi = $this -> getReq('doi');
    $lQry = new CApi_Pixelboxx_Query_Deleteobject();
    $lQry->delete($lDoi);
    $this->renderList();
  }
  
  protected function actPboxaddcart() {
    $lEnc = $this -> getReq('enc');
    $lArr = Zend_Json::decode($lEnc);
         
    $lCart = new CApi_Pixelboxx_Cart();
    $lCart->addToCart($lArr['doi'], $lArr['name'], $lArr['size'], $lArr['date']);
    $lCart->save();
    $this->renderList();
  }
  
  protected function actPboxremovecart() {
    $lDoi = $this -> getReq('doi');
     
    $lCart = new CApi_Pixelboxx_Cart();
    $lCart->remove($lDoi);
    $lCart->save();
    $this->renderList();
  }
}