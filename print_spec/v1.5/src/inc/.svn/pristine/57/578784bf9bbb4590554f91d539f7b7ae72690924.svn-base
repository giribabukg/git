<?php
class CInc_App_Event_Action_Wec_DocumentAction extends CApp_Event_Action {

  public function execute() {
    $lJob = $this -> mContext['job'];

    // new job information
    $lSrc = $lJob['src'];
    $lJobID = $lJob['jobid'];
    $lWecPrjId = $lJob['wec_prj_id'];

    // old job information
    $lOrigSrc = $lJob['orig_src'];
    $lOrigJobId = $lJob['orig_jobid'];
    $lOrigWecPrjId = $lJob['orig_wecprjid'];

    // TODO: lines 18 - 26 are needed as long as we can not force job value changes by triggering events
    $lFac = new CJob_Fac($lSrc, $lJobID);
    $lJobDet = $lFac -> getDat();

    $lWecPrjId = $lJobDet['wec_prj_id'];
    // foolishness ends here: lines 18 - 26 are needed as long as we can not force job value changes by triggering events

    if (!$lWecPrjId) return FALSE;

    // log-in
    $lWec = new CApi_Wec_Client();
    $lWec -> loadConfig(FALSE, TRUE);

    // TODO: lines 36 - 42 are needed as long as WebCenter can not access folder id in a new project
    $lDummyPath = CCor_Cfg::get('wec.dummy.path');
    $lDummyFile = CCor_Cfg::get('wec.dummy.file');
    $lUploadDocumentFolder = CCor_Cfg::get('wec.upload.document.folder');
    $lUploadDocumentComment = CCor_Cfg::get('wec.upload.document.comment');

    $lQry = new CApi_Wec_Query_Upload($lWec);
    $lRes = $lQry -> upload($lWecPrjId, $lDummyPath.$lDummyFile, $lDummyFile, $lUploadDocumentFolder, $lUploadDocumentComment);
    // foolishness ends here: lines 36 - 42 are needed as long as WebCenter can not access folder id in a new project

    // TODO: lines 46 - 48 are needed as long as WebCenter can not access folder id in a new project
    $lQry = new CApi_Wec_Query_Doclist($lWec);
    $lRes = $lQry -> getList($lWecPrjId);
    $lDummyDocumentVersionID = $lRes[0]['wec_ver_id'];
    $lDummyDocumentID = $lRes[0]['wec_doc_id'];
    $lFolderId = $lRes[0]['folder_id'];
    // foolishness ends here: lines 46 - 48 are needed as long as WebCenter can not access folder id in a new project

    // TODO: lines 54 - 57 are needed as long as WebCenter can not access folder id in a new project
    $lQry = new CApi_Wec_Query($lWec);
    $lQry -> setParam('projectid', $lWecPrjId);
    $lQry -> setParam('documentid', $lDummyDocumentID);
    $lXml = $lQry -> query('DeleteDocument.jsp');
    // foolishness ends here: lines 54 - 57 are needed as long as WebCenter can not access folder id in a new project

    // document list of old job
    $lOrigWecDocLst = new CApi_Wec_Query_Doclist($lWec);
    $lOrigWecDocRes = $lOrigWecDocLst -> getList($lOrigWecPrjId);

    if (!is_array($lOrigWecDocRes)) return FALSE;

    $lOrigWecVerID = max(CSvc_Wectns::array_col($lOrigWecDocRes, 'wec_ver_id'));
    $lOrigWecArr = CSvc_Wectns::array_search($lOrigWecDocRes, 'wec_ver_id', $lOrigWecVerID);
    $lOrigWecDocID = $lOrigWecArr['wec_doc_id'];

    // copying
    $lQry = new CApi_Wec_Query($lWec);
    $lQry -> setParam('type', 5);
    $lQry -> setParam('docid', $lOrigWecDocID);
    $lQry -> setParam('projectid', $lOrigWecPrjId);
    $lQry -> setParam('proj_folder_pairid_list', $lWecPrjId.','.$lFolderId);
    $lXml = $lQry -> query('DocumentAction.jsp');

    $lRes = new CApi_Wec_Response($lXml);
    if (!$lRes -> isSuccess()) {
      return FALSE;
    }

    if (0 === strpos($lXml, '<error>')) {
      return FALSE;
    }

    return $lXml;
  }
}