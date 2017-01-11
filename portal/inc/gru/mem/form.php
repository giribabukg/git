<?php
/**
 * Group Members Form
 *
 * @package gru
 * @subpackage mem
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 5847 $
 * @date $Date: 2010-08-11 14:01:56 +0200 (Mi, 11 Aug 2010) $
 * @author $Author: gemmans $
 */

class CInc_Gru_Mem_Form extends CHtm_Fpr {

  public function __construct($aGid, $aMaxDest = 0) {
    parent::__construct('gru-mem.sedt', '', $aMaxDest);
    $this -> mGid = intval($aGid);
    $this -> loadInfos();
    $this -> loadUsers();
    $this -> getData();

    $this -> mGru = CCor_Res::get('gru');
    $this -> mTitle = lan('usr-mem.menu');
    $this -> mShowUpDown = FALSE;

    $this -> mSrcCaption  = lan('lib.available');
    $this -> mDestCaption = lan('lib.selected');

  }

  protected function getHead() {
    $lRet = parent::getHead();
    $lOld = implode(',', $this -> mDst);
    $lRet.= '<input type="hidden" name="old" value="'.$lOld.'" />'.LF;
    $lRet.= '<input type="hidden" name="gid" value="'.$this->mGid.'" />'.LF;
    return $lRet;
  }

  protected function getData() {
    $this->mMem = array();
    $lQry = new CCor_Qry('SELECT uid FROM al_usr_mem WHERE gid="'.$this -> mGid.'"');
    foreach ($lQry as $lRow) {
      $this -> mMem[$lRow['uid']] = TRUE;
    }
    # sorting names
    $this->mDst = array_keys($this -> mMem);
    $sortSelectedData = array_intersect_key($this->mUsr, array_flip($this->mDst));
    $this -> mDst = array_keys($sortSelectedData);
    
    //Start Sorted selected Data
    $sortSelectedData = array_intersect_key($this->mUsr, array_flip($this->mDst));
    $this -> mDst = array_keys($sortSelectedData);
	//Finish Sorted selected Data
    
    foreach ($this->mDst as $lUid) {
      if (!isset($this->mUsr[$lUid])) {
        if (isset($this->mAllUsr[$lUid])) {
          $this->mUsr[$lUid] = $this->mAllUsr[$lUid];
        }
      }
    }
    asort($this->mUsr);
    $this->setSrc($this->mUsr);
  }

  protected function loadInfos() {
    $this -> mInfos = array();
    $lQry = new CCor_Qry('SELECT alias,val FROM al_gru_infos WHERE gid="'.$this -> mGid.'"');
    foreach ($lQry as $lRow) {
      $this -> mInfos[$lRow['alias']] = $lRow['val'];
    }
  }

  protected function getParentGroup() {
    return 0;
  }

  protected function loadUsers() {
    $lParent = $this->getParentGroup();
    $this->dbg('Filtered to GID '.$lParent);

    $this->mAllUsr = CCor_Res::extract('id', 'fullname', 'usr');
    asort($this->mAllUsr);
    if (empty($lParent)) {
      $this->mUsr = $this->mAllUsr;
    } else {
      $this->mUsr = CCor_Res::extract('id', 'fullname', 'usr', array('gru' => $lParent));
    }

    $this->setSrc($this->mUsr);
    $this->setSel(array());
  }


}