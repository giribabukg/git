<?php
class CInc_Job_Fil_Src_Cloudflow  extends CJob_Fil_Files {

  const VERSION_DELIMITER = '_';

  public function __construct($aSrc, $aJobId, $aSub = 'doc', $aDiv = '', $aFrom = 'sub', $aAge = 'job', $aDebug = FALSE, $aUploadButton = TRUE) {
    $lSub = 'cloudflow';

    parent::__construct($aSrc, $aJobId, $lSub, $aDiv, $aFrom, $aAge, FALSE, $aUploadButton);
    $this->mJob = $this->loadJob();

    $lUsr = CCor_Usr::getInstance();
    $this -> mCanDelete = $lUsr -> canDelete('job-pdf');
    if ('arc' == $this -> mAge) {
      $this -> mCanDelete = FALSE;
    }
    $this -> mTitle = lan('job-fil.'.$this -> mSub);

    if (('arc' != $this -> mAge) && !bitset($this -> mFlags, jfOnhold)) {
      $this -> mUpload = TRUE;
    } else {
      $this -> mUpload = FALSE;
    }
    $this -> mCompare = TRUE;

    $this -> addColumn('mor');
    $this -> addColumn('chk', '', false, array('width' => '16'));
    $this -> addColumn('name', lan('lib.file.name'), TRUE, array('width' => '90%', 'id' => 'name'));
    $this -> addColumn('size', lan('lib.file.size'), TRUE, array('id' => 'size'));
    $this -> addColumn('date', lan('lib.file.time.modification'), TRUE, array('id' => 'date'));

    $this -> mApi = new CApi_Cloudflow_Client();
    $this -> mIte = $this -> getIterator();
    $this -> mGroupClass = uniqid('c_');
    $this -> mIsFirst = TRUE;
  }

  protected function loadJob() {
    if ('arc' == $this->mAge) {
      $lDat = new CArc_Dat($this->mSrc);
      $lDat->load($this->mJobId);
      return $lDat;
    }
    $lFac = new CJob_Fac($this->mSrc, $this->mJobId);
    $lDat = $lFac->getDat();
    return $lDat;
  }

  protected function getCompareButton() {
    $lRet ='<td align="right">';
    $lJs = 'Flow.proofscope.compare("'.$this -> mSrc.'","'.$this -> mJobId.'")';
    $lRet.= btn(lan('lib.compare'), $lJs, 'img/ico/16/copy-hi.gif');
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getSwitchCategoryViewButton() {
    return ''; // cloudflow does not have categories
  }

  public function getIterator() {
    $lRet = $this->mApi->getFileList($this->mJob);
    return $lRet;
  }

  protected function getTdchk() {
    $lRet = '';
    $lRet.= '<td class="td1 w16 ac">';
    $lDoc = $this -> getVal('url');
    $lRet.= '<input type="checkbox" value="'.htm($lDoc).'" class="beh-comp" />';
    $lRet.= '</td>';
    return $lRet;
  }


    protected function getTdName() {
    $lRet = '';
    $lNam = $this -> getVal('name');
    $lUrl = $this -> getVal('url');
    $lDisplay = $this -> getVal('display');
    $lLink = $this -> getVal('link');

    $this -> mTheFileLink = '';
    if (!empty($lLink)) {
      $lLnk = $lLink;
    } else {
      $lLnk = $this -> mLinkDefault.urlencode($lUrl);
    }
    $this -> mFileLink = $lLnk;
    $lLnk = htm($lLnk);
    $this -> mTheFileLink = '<a href="'.$lLnk.'" target="_blank">';

    $lRet.= $this -> mTheFileLink;
    $lRet.= htm($lNam);
    $lRet.= '</a>';
    return $this -> td($lRet);
  }

  protected function getTdMor() {
    $this -> mMoreId = uniqid('tr_');
    $lRet = '<a class="nav" onclick="Flow.togTrOne(\''.$this -> mMoreId.'\',\''.$this -> mGroupClass.'\')">';
    $lRet.= '...</a>';
    return $this -> tdc($lRet);
  }

  protected function afterRow() {
    $lRet = '<tr style="display:none" id="'.$this -> mMoreId.'" class="'.$this -> mGroupClass.'">';

    $lRet.= '<td class="td1 tg">&nbsp;</td>';
    $lRet.= '<td class="p8" colspan="5">';

    $lRet.= '<div class="box m8" style="float:left; margin-right:1em;">';
    $lUrl = 'cloudflow://'.$this->getVal('url');
    //$lRet.= $lUrl;
    $lImg = $this->mApi->getEmbedPreview($lUrl);
    if ($lImg) {
      $lRet.= '<img src="'.$lImg.'" width="400" />';
    } else {
      $lRet.= '<img src="" />';
    }
    $lRet.= '</div>';

    /*
    $lRet.= '<div class="m8" style="float:left; margin-right:1em;">';
    $lRet.= 'Metadata';
    $lRet.= '</div>';
    */

    $lRet.= '<div class="m8" style="float:left; margin-right:1em;">';
    $lDownloadLink = $this -> mLinkDefault.urlencode($lUrl);
    $lRet.= BR.BR;
    $lRet.= btn('Download', "go('".$lDownloadLink."','tab')", 'ico/16/pdf.png', 'button', array('class' => 'btn w300')).BR.BR;
    $lViewUrl = 'index.php?act=utl-fil.viewproofscope&file='.$lUrl;
    $lRet.= btn('Open in Proofscope', "go('".$lViewUrl."','tab')", 'ico/16/pdf_annotated.png', 'button', array('class' => 'btn w300'));
    $lRet.= '</div>';

    $lRet.= '<div style="clear:both"></div>';

    $lRet.= '</td>';
    $lRet.= '</tr>';
    return $lRet;
  }

}
