<?php

/**
  * Pixelboxx File List Provider
  *
  * @package    Job
  * @subpackage Files
  * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
  * @version $Rev: 4696 $
  * @date $Date: 2014-06-06 20:47:21 +0200 (Fr, 06 Jun 2014) $
  * @author $Author: ahajali $
  */
class CInc_Job_Fil_Src_Pixelboxx extends CJob_Fil_Files {

  public function __construct($aSrc, $aJobId, $aSub = 'wec', $aDiv = '', $aFrom = 'sub',
      $aAge = 'job', $aDebug = FALSE)
  {
    $lSub = 'pixelboxx';

    parent::__construct($aSrc, $aJobId, $lSub, $aDiv, $aFrom, $aAge);

    $this->mSrc = $aSrc;
    $this->mJobId = $aJobId;
    $this->mSub = $aSub;
    $this->mDiv = $aDiv;

    $lUsr = CCor_Usr::getInstance();

    $this->mDebug = $aDebug;

    // Title and Upload in Header
    $this->mTitle = lan('job-fil.pixelboxx');
    $this->mUpload = TRUE;
    $this->mCompare = FALSE;

    $this->addCtr();
    $this->addColumn('name', lan('lib.file.name'), FALSE,
        array(
            'width' => '90%',
            'id' => 'name'
        ));
    $this->addColumn('size', lan('lib.file.size'), FALSE, array(
        'id' => 'size'
    ));
    $this->addColumn('date', lan('lib.file.date'), FALSE, array(
        'id' => 'date'
    ));

    $lSql = 'SELECT pbox_doi FROM al_job_shadow_' . MID . ' WHERE src=' .
         esc($this->mSrc) . ' AND jobid=' . esc($this->mJobId);
    $this->mDoi = CCor_Qry::getStr($lSql);
    $this->dbg('Pixelboxx DOI:' . $this->mDoi);

    $this->mClient = new CApi_Pixelboxx_Client();
    $this->mClient->loadAuthFromUser();
    $this->mIte = $this->getIterator();
  }

  public function getIterator()
  {
    $lQry = new CApi_Pixelboxx_Query_Getfolder();
    $lRes = $lQry->getFolder($this->mDoi);
    return $lRes;
  }

  protected function getRows()
  {
    $lRet = '';
    $this->mCtr = $this->mFirst + 1;
    foreach ($this->mIte as $this->mRow) {
      $lRet.= $this->beforeRow();
      $lRet.= $this->getRow();
      $lRet.= $this->afterRow();
    }
    return $lRet;
  }

  protected function getTdName()
  {
    $lRet = '';
    //(aDoi, aDiv, aSrc, aJid, aSub, aAge)
    $lDoi = $this->getVal('doi');
    $lRet.= '<a class="cp" onclick="Flow.pixelboxx.showDetails(\''.$lDoi.'\',\''.$this->mDiv.'\',\''.$this->mSrc.'\',\''.$this->mJobId.'\',\''.$this->mSub.'\',\''.$this->mAge.'\')">';
    $lNam = $this->getVal('name');
    $lRet.= htm($lNam);
    $lRet.= '</a>';
    return $this->td($lRet);
  }


}