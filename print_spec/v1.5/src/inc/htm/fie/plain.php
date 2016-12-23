<?php
class CInc_Htm_Fie_Plain extends CCor_Obj {

  protected function getDef($aKey, $aStd = NULL) {
    return (isset($this -> mDef[$aKey])) ? $this -> mDef[$aKey] : $aStd;
  }

  protected function expect($aKey) {
    if (!isset($this -> mDef[$aKey])) {
      $this -> dbg('Field definition key '.$aKey.' not set', mlWarn);
    }
  }

  public function getPlain($aDef, $aVal = NULL) {
    $this -> mDef = $aDef;
    $this -> expect('alias');
    $this -> expect('typ');

    $lFnc = 'getAlias'.ucfirst($this -> getDef('alias'));
    if ($this -> hasMethod($lFnc)) {
      $lRet = $this -> $lFnc($aVal);
      return $lRet;
    }

    $lFnc = 'getType'.ucfirst($this -> getDef('typ'));
    if ($this -> hasMethod($lFnc)) {
      $lRet = $this -> $lFnc($aVal);
      return $lRet;
    }
    return $aVal;
  }

  protected function getTypeMemo($aVal) {
    return nl2br($aVal);
  }

  protected function getTypeBoolean($aVal) {
    if ($aVal) {
      return 'X';
    } else {
      return '';
    }
  }

  protected function getTypeUselect($aVal) {
    $lPar = toArr($this -> getDef('param'));
    $lArr = CCor_Res::extract('id', 'fullname', 'usr',  $lPar);
    if (isset($lArr[$aVal])) {
      return $lArr[$aVal];
    } else {
      #return htm('User ID '.$aVal);
      return '';
    }
  }

  protected function getTypeGselect($aVal) {
    $lPar = toArr($this -> getDef('param'));
    $lArr = CCor_Res::extract('id', 'name', 'gru',  $lPar);
    if (isset($lArr[$aVal])) {
      return $lArr[$aVal];
    } else {
      #return htm('Group ID '.$aVal);
      return '';
    }
  }

  protected function getTypeSelect($aVal) {
    $lArr = toArr($this -> getDef('param'));
    if (isset($lArr[$aVal])) {
      return $lArr[$aVal];
    } else {
      return $aVal;
    }
  }

  protected function getTypeResselect($aVal) {
    $lPar = $this -> getDef('param');
    $lPar = toArr($lPar);

    $lRes = (isset($lPar['res'])) ? $lPar['res'] : 'usr';
    $lKey = (isset($lPar['key'])) ? $lPar['key'] : 'id';
    $lVal = (isset($lPar['val'])) ? $lPar['val'] : 'name_'.LAN;
    $lFil = (isset($lPar['fil'])) ? $lPar['fil'] : '';

    $lArr = CCor_Res::extract($lKey, $lVal, $lRes, $lFil);
    if (isset($lArr[$aVal])) {
      return $lArr[$aVal];
    } else {
      return $aVal;
    }
  }

  protected function getTypeTselect($aVal) {
    $lPar = $this -> getDef('param');
    $lPar = toArr($lPar);

    $lDom = (isset($lPar['dom'])) ? $lPar['dom'] : '';
    $lArr = CCor_Res::get('htb', $lDom);
    if (isset($lArr[$aVal])) {
      return $lArr[$aVal];
    } else {
      return $aVal;
    }
  }

  protected function getTypeDate($aVal) {
    $lDat = new CCor_Date($aVal);
    return  $lDat -> getFmt(lan('lib.date.long'));
  }

  /**
	 * Returns either the Input-Value OR the "CriticalPath-Synonym of the Value.
	 *
   * Example:
   *   $lCrp = array(
   *   10  => 'Draft',
   *   20  => 'Request',
   *   30  => 'Production',
   *   40  => 'PDF Proof',
   *   50  => 'Approval Loop',
   *   60  => 'Amendment/Proof Request',
   *   70  => 'Final Approval',
   *   80  => 'Printer Request',
   *   90  => 'Delivered',
   *   200 => 'Archive'
	 *	);
	 *
   * @param int $aVal
   * @return int OR string
   */
  protected function getAliasWebstatus($aVal) {
    $lCrp = CCor_Res::extract('status', 'name_'.LAN, 'crp');
    if (isset($lCrp[$aVal])) {
      return $lCrp[$aVal];
    } else {
      return $aVal;
    }
  }

  protected function getTypePickselect($aVal) {
    $lPar = toArr($this -> getDef('param'));
    $lPar = toArr($lPar);

    $lDom = (isset($lPar['dom'])) ? $lPar['dom'] : '';
    $lAlias = (isset($lPar['alias'])) ? $lPar['alias'] : '' ;
    $lSteerAlias = (isset($lPar['steerAlias'])) ? $lPar['steerAlias'] : '';

    if ($lDom != '' AND $lAlias != ''){
      $lParam = Array('domain' => $lDom);
      // Find out the ColId of Alias in the Picklist
      $lArr = Array();
      $lSqlColFind = 'SELECT DISTINCT(col) from al_pck_columns where domain ="'.$lDom.'" AND alias ="'.$lAlias.'"';
      $lColId = CCor_Qry::getInt($lSqlColFind);
      $lCol = 'col'.$lColId;
      $lArr = CCor_Res::extract($lCol, $lCol,'pcklist', $lParam);
      if (!empty($lArr)){
        return $lArr;
      } else {
        return '';
      }
    } else {
      return '';
    }
  }
}