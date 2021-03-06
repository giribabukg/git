<?php
/**
 * Core: Modification - Base
 *
 * Description
 *
 * @package    COR
 * @subpackage    Modification
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 12582 $
 * @date $Date: 2016-02-17 19:43:59 +0800 (Wed, 17 Feb 2016) $
 * @author $Author: ahajali $
 */
abstract class CCor_Mod_Base extends CCor_Obj {

  protected $mKey = array('id');
  protected $mVal = array();
  protected $mOld = array();
  protected $mUpd = array();
  protected $mChg = array(); // array of changed values
  protected $mFie = array();
  protected $mStrict = TRUE;
  protected $mTest = FALSE;

  // No constructor because of possible singleton decendants

  // Field setup handling

  public function & addField($aDef) {
    $lKey = $aDef['alias'];
    $lFie = array();
    $lFie['key'] = $aDef['alias'];
    $lFie['typ'] = $aDef['typ'];
    $lFie['par'] = $aDef['param'];

    $this -> mFie[$lKey] = & $lFie;
    return $lFie;
  }

  public function & getField($aAlias) {
    if (!$this -> hasField($aAlias)) {
      $lRet = NULL;
      return $lRet;
    }
    return $this -> mFie[$aAlias];
  }

  public function hasField($aAlias) {
    return isset($this -> mFie[$aAlias]);
  }

  public function isValidValue($aAlias, $aVal) {
  }

  public function isValid() {
    return TRUE;
  }

  // Get from post

  public function getFromReq(ICor_Req $aReq, $aOld = TRUE) {
    $lVal = $aReq -> getVal('val');
    foreach ($lVal as $lKey => $lVal) {
      $this -> setVal($lKey, $lVal);
    }
    if (TRUE == $aOld) {
      $lOld = $aReq -> getVal('old');
      foreach ($lOld as $lKey => $lVal) {
        $this -> setOld($lKey, $lVal);
      }
    }
  }

  public function getPost(ICor_Req $aReq, $aOld = TRUE) {
    $this -> mVal = array();
    $this -> mOld = array();
    $this -> mChg = array();
    $this -> mUpd = array();

    $this -> mReqVal = $aReq -> getVal('val');
    $this -> mReqOld = $aReq -> getVal('old');
    #$this -> dump($this -> mReqVal);

    if ($aOld) {
      foreach($this -> mKey as $lKey) {
        $this -> setVal($lKey, $this -> getReqVal($lKey));
        $this -> setOld($lKey, $this -> getReqOld($lKey));
      }

      foreach ($this -> mFie as $lFie) {
        $lKey = $lFie['key'];
        $lTyp = $lFie['typ'];
        $lNew = $this -> getFieVal($lFie);
        if (in_array($lTyp, array('multipleselect')) AND !empty($lNew)) {
          $lNew = implode(',', $lNew);
        }
        $lNew = trim($lNew);
        $this -> setVal($lKey, $lNew);
        $lOld = trim($this -> getFieOld($lFie));
        $this -> setOld($lKey, $lOld);
        #$this -> dbg('old '.$lKey.' = '.$lOld);
        if (in_array($lTyp, array('uselect', 'gselect'))) {
          if (empty($lOld) and empty($lNew)) {
            continue;
          }
        }
        if ((string)$lNew !== (string)$lOld) {
          $this -> mUpd[$lKey] = $lNew;
          $this -> dbg($lKey.' has changed from "'.$lOld.'" to "'.$lNew.'"');
        }
      }

    } else {
      foreach ($this -> mFie as $lFie) {
        $lKey = $lFie['key'];
        $lNew = $this -> getFieVal($lFie);
        $this -> setVal($lKey, $lNew);
      }
    }
  }

  protected function getFieVal($aFie) {
    $lFnc = 'getReq'.ucfirst($aFie['typ']);
    if ($this -> hasMethod($lFnc)) {
      return $this -> $lFnc($aFie);
    }
    return (isset($this -> mReqVal[$aFie['key']])) ? $this -> mReqVal[$aFie['key']] : '';
  }

  protected function getFieOld($aFie) {
    return (isset($this -> mReqOld[$aFie['key']])) ? $this -> mReqOld[$aFie['key']] : '';
  }

  protected function getReqVal($aAlias) {
    return (isset($this -> mReqVal[$aAlias])) ? $this -> mReqVal[$aAlias] : '';
  }

  protected function getReqOld($aAlias) {
    return (isset($this -> mReqOld[$aAlias])) ? $this -> mReqOld[$aAlias] : '';
  }

  protected function getReqDate($aFie) {
    $lNew = (isset($this -> mReqVal[$aFie['key']])) ? $this -> mReqVal[$aFie['key']] : '';
    if (empty($lNew)) {
      return '';
    }
    $lDat = new CCor_Date();
    $lDat -> setInp($lNew);
    return $lDat -> getSql();
  }

  protected function getReqBoolean($aFie) {
    return (empty($this -> mReqVal[$aFie['key']])) ? '' : 'X';
  }

  // Value handling

  public function setVal($aAlias, $aValue) {
    $this -> mVal[$aAlias] = $aValue;
  }

  public function forceVal($aAlias, $aValue) {
    $this -> mOld[$aAlias] = $aValue;
    $this -> mVal[$aAlias] = $aValue;
    $this -> mUpd[$aAlias] = $aValue;
  }

  public function getVal($aAlias, $aStd = NULL) {
    return (isset($this -> mVal[$aAlias])) ? $this -> mVal[$aAlias] : $aStd;
  }
  
  public function getValues() {
    return $this->mVal;
  }

  public function hasVal($aAlias) {
    return (isset($this -> mVal[$aAlias]));
  }

  public function hasValues($aList) {
    if (!is_array($aList)) {
      $aList = explode(',', $aList);
    }
    if (empty($aList)) {
      return FALSE;
    }
    foreach ($aList as $lAlias) {
      if (!$this -> hasVal($lAlias)) return FALSE;
    }
    return TRUE;
  }

  // old value handling

  public function setOld($aAlias, $aValue) {
    $this -> mOld[$aAlias] = $aValue;
  }

  public function getOld($aAlias, $aStd = NULL) {
    return (isset($this -> mOld[$aAlias])) ? $this -> mOld[$aAlias] : $aStd;
  }

  public function hasOld($aAlias) {
    return (isset($this -> mOld[$aAlias]));
  }


  public function fieldHasChanged($aAlias) {
    /*
    $lOld = $this -> getOld($aAlias);
    $lNew = $this -> getVal($aAlias);
    if ($lOld != $lNew) {
      $this -> dbg($aAlias.' has changed from '.$lOld.' to '.$lNew);
    }
    return ($lNew != $lOld);
    */
    return (isset($this -> mUpd[$aAlias]));
  }

  public function hasChanged() {
    if (!empty($this->mUpd)) return TRUE;
    foreach ($this -> mOld as $lKey => $lVal) {
      if ($this -> fieldHasChanged($lKey)) {
        return TRUE;
      }
    }
    return FALSE;
  }
  
  public function getUpdate() {
    return $this -> mUpd;
  }

  /**
   * Update Record
   * Will call beforePost,isValid,doUpdate and afterPost
   *
   * @access public
   * @return boolean Update was successful
   */

  public function update() {
    $this -> mCancel = FALSE;
    $this -> dbg('update');

    // 1. before post
    $this -> beforePost(FALSE);
    if ($this -> mCancel) {
      return FALSE;
    }

    // 2. Cancel if nothing has changed
    if (!$this -> hasChanged()) {
      $this -> dbg('nothing changed');
      return TRUE; // TODO: Decision: really return true?
    }

    // 3. check validity
    if (!$this -> isValid(FALSE)) {
      return FALSE;
    }

    // 4. update
    if (!$this -> doUpdate()) {
      $this -> mCancel = TRUE;
      return FALSE;
    }

    // 5. after post
    $this -> afterPost(FALSE);
    $this -> afterChange();
    return TRUE;
  }

  /**
   * Do the actual update DB/Api - functions
   *
   * @access protected
   * @return boolean Insert was successful
   */

  abstract protected function doUpdate();

  /**
   * Insert a record
   * Will call beforePost,isValid,doUpdate and afterPost
   *
   * @access public
   * @return boolean Insert was successful
   */

  public function insert() {
    $this -> mCancel = FALSE;

    // 1. before post
    $this -> beforePost(TRUE);
    if ($this -> mCancel) {
      return FALSE;
    }

    // 2. check validity
    if (!$this -> isValid(TRUE)) {
      return FALSE;
    }

    // 3. insert
    if (!$this -> doInsert()) {
      $this -> mCancel = TRUE;
      return FALSE;
    }

    // 4. after post
    $this -> afterPost(TRUE);
    $this -> afterChange();
    return TRUE;
  }

  /**
   * Do the actual insert DB/Api - functions
   *
   * @access protected
   * @return boolean Insert was successful
   */

  abstract protected function doInsert();

  /**
   * Last minute stuff before insert or update is actually performed
   *
   * e.g. set keyword of job or set position of record to max(pos)+1
   * Will be called by insert or update
   *
   * @param boolean $aNew True if inserting, False if updating
   * @access protected
   */

  protected function beforePost($aNew = FALSE) {
  }

  /**
   * Additional stuff to do after successful insert or update
   *
   * e.g. add items to self-learning lists etc.
   * Will be called by insert or update
   *
   * @param boolean $aNew True if inserting, False if updating
   * @access protected
   */

  protected function afterPost($aNew = FALSE) {
  }

  // delete functions

  /**
   * Delete a record
   * Will call beforeDelete,doDelete and afterDelete
   *
   * @param string $aId ID of record to be deleted
   * @access public
   */

  public function delete($aId) {
    $this -> mCancel = FALSE;

    // 1. before delete
    $this -> beforeDelete($aId);
    if ($this -> mCancel) {
      return FALSE;
    }

    // 2. delete
    $this -> doDelete($aId);
    if ($this -> mCancel) {
      return FALSE;
    }

    // 3. after delete
    $this -> afterDelete($aId);
    $this -> afterChange();
    return TRUE;
  }

  /**
   * Additional stuff to do before actual delete
   *
   * e.g. set mCancel = TRUE if record should not be deleted
   * Will be called by delete
   *
   * @param string $aId ID of record to be deleted
   * @access protected
   */

  protected function beforeDelete($aId) {
  }

  /**
   * Perform the actual delete
   *
   * @param string $aId ID of record to delete
   * @access protected
   */

  abstract protected function doDelete($aId);

  /**
   * Additional stuff to do after successful delete
   *
   * e.g. delete all records with a foreign key on this record
   * Will be called by delete
   *
   * @param boolean $aNew True if inserting, False if updating
   * @access protected
   */

  protected function afterDelete($aId) {
  }

  /**
   * Additional stuff to do after successful update, insert or delete
   *
   * e.g. invalidate cache
   *
   * @access protected
   */
  protected function afterChange() {
  }

  public function setTestMode($aFlag = TRUE) {
    $this -> mTest = $aFlag;
  }

  public function getFieldnames() {
    $lArr = array();
    foreach ($this -> mFie as $lFie) {
      $lArr[] = $lFie['key'];
    }
    return $lArr;
  }

}