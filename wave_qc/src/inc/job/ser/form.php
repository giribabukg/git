<?php
/**
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 11402 $
 * @date $Date: 2015-11-14 00:16:41 +0800 (Sat, 14 Nov 2015) $
 * @author $Author: ahajali $
 */
class CInc_Job_Ser_Form extends CHtm_Form {

  public function __construct($aFil = array()) {
    parent::__construct('job-ser.ser', lan('job-ser.menu'), FALSE);

    $this -> mFil = $aFil;
    $this -> getSearchFields();
  }

  protected function getSearchFields() {
    $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
  	$this -> setAtt('class', 'tbl w800');
  	if ($lWriter == 'alink') {
  	  $this -> addDef(fie('anf', lan('job.draft'), 'boolean'));
  	}
    $this -> addDef(fie('job', lan('job.menu'), 'boolean'));
    $this -> addDef(fie('arc', lan('arc.menu'), 'boolean'));

    $lUsr = CCor_Usr::getInstance();
    $this -> mDef = CCor_Res::getByKey('id', 'fie');
    $lSpr = $lUsr -> getPref('job-ser.sfie');
    if (!empty($lSpr)) {
      $lArr = explode(',', $lSpr);
      foreach ($lArr as $lFid) {
        if (!isset($this -> mDef[$lFid])) continue;
        $lDef = $this -> mDef[$lFid];
        // Beim abh�ngige Auuftragsfelder wird standart Wert mit Variable 'NoChoice' definiert
        // was aber in der Suche nicht n�tig ist.
        if (isset($lDef['NoChoice']) && !empty($lDef['NoChoice'])){
          unset($lDef['NoChoice']);
        }
        $this -> addDef($lDef);
      }
    }
    $lSer = $lUsr -> getPref('job-ser.ser');
    if (empty($lSer)) {
      if ($lWriter == 'alink') {
        $this -> setVal('anf', 'X');
      }
      $this -> setVal('job', 'X');
      $this -> setVal('arc', 'X');
    } else {
      // force unserialize check
      $lUnser = @unserialize($lSer);
      if ($lUnser !== FALSE) {
        $lSer = $lUnser;
      }

      if (is_array($lSer)) {
        foreach ($lSer as $lKey => $lVal) {
          $this -> setVal($lKey, $lVal);
        }
      }

    }
  }

  protected function getButtons() {
    $lRet = '<div class="btnPnl">'.LF;
    $lRet.= btn(lan('lib.search'), '', 'img/ico/16/search.gif', 'submit').NB;
    $lRet.= btn(lan('lib.opt.spr'), "go('index.php?act=job-ser.spr')", 'img/ico/16/field.gif').NB;
    $lRet.= btn(lan('lib.opt.fpr'), "go('index.php?act=job-ser.fpr')", 'img/ico/16/col.gif');
    $lRet.= '</div>'.LF;
    return $lRet;
  }

  protected function getFieldForm() {
    $lRet = parent::getFieldForm();
    $lRet.= $this -> getFilterBar();

    return $lRet;
  }

  protected function getFilterBar() {
    $lRet = '';
    $lRet.= '<tr>'.LF;
    $lRet.= '<td class="nw">';
    $lRet.= $this -> getFilterForm();
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function getFilterForm() {
    $lRet = '';
    $lRet.= lan('lib.flags');

    $lVal = (isset($this -> mFil['flags'])) ? $this -> mFil['flags'] : '';
    $lRet.= $this -> getFilterFlags($lVal, NULL, NULL);

    return $lRet;
  }

  protected function getFilterFlags($aVal, $aOpt, $aCap = '') {
    $lRet = '';

    // what job flags are there at all?
    $lJfl = array(0 => '['.lan('lib.all').']');
    $lSql = 'SELECT val, name_'.LAN.' AS name FROM al_jfl WHERE mand IN (0, '.MID.') ORDER BY val;';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lJfl[intval($lRow['val'])] = $lRow['name'];
    }

    // what job flags are currently set (array)?
    $lFlags = Array();
    if (isset($this -> mFil['flags'])) {
      $lFlags = $this -> mFil['flags']; // User preferences filter flags
    }

    // what job flags are currently set (bit)?
    $lJflFiltered = 0;
    foreach ($lFlags as $lKey => $lValue) {
      $lJflFiltered += $lValue;
    }

    // IDs
    $lDiv = getNum('do'); // outer div
    $lDivId = getNum('di'); // inner div
    $lLnkId = getNum('l'); // link

    $lRet.= '<td>'.LF;
    $lRet.= '  <div id="'.$lDiv.'">'.LF;
    $lRet.= '    <a class="nav" id="'.$lLnkId.'" href="javascript:Flow.Std.popMen(\''.$lDivId.'\',\''.$lLnkId.'\')">'.LF;
    $lRet.= '      <b>'.$aCap.NB.NB.'</b>'.LF;

    foreach ($lJfl as $lKey => $lValue) {
      if ($lKey === 0) {
        continue;
      }

      if (bitset($lJflFiltered, $lKey) OR empty($lFlags)) {
        $lPath = getImgPath('img/jfl/'.$lKey.'.gif');
        $lRet.= img($lPath, array('style' => 'margin-right:1px', 'title' => $lValue));
      } else {
        $lPath = getImgPath('img/jfl/'.$lKey.'l.gif');
        $lRet.= img($lPath, array('style' => 'margin-right:1px', 'title' => $lValue));
      }
    }

    $lRet.= '    </a>'.LF;
    $lRet.= $this -> getFilterFlagsWithCheckbox($lJfl, $lFlags, $lDivId);
    $lRet.= '  </div>'.LF;
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getFilterFlagsWithCheckbox($aAllJfl, $aCheckedJfl = array(), $aDivId) {
    $lAllJfl = $aAllJfl;
    $lCheckedJfl = $aCheckedJfl;
    $lDivId = $aDivId;

    $lRet = "";
    $lChkBoxes = array();
    $lAllFlags = FALSE;
    $lValues = array();

    if (!empty($lCheckedJfl)) {
      // what job flags are currently set (array)?
      foreach ($lCheckedJfl as $lKey => $lVal) {
        $lValues[$lKey] = $lKey;
      }

      // what job flags are currently set (bit)?
      $lJflFiltered = 0;
      foreach ($lValues as $lKey => $lValue) {
        $lJflFiltered += $lValue;
      }

      // what job flags are currently set (bit)?
      $lAllJflFiltered = 0;
      foreach ($lAllJfl as $lKey => $lValue) {
        $lAllJflFiltered += $lKey;
      }
    } else {
      $lAllFlags = TRUE;
    }

    if (in_array(0, $lValues)) {
      $lAllFlags = TRUE;
    }


    $lFlagsArr = $aAllJfl;
    $lCount = count($lFlagsArr);
    $lFlags = "";
    foreach ($lFlagsArr as $lKey => $lValue) {
      if ($lKey == 0) continue;
      $lFlags.= "".$lKey.",";
    }
    $lFlags = substr($lFlags, 0, -1);

    foreach ($lAllJfl as $lKey => $lValue) {
      $lChkBox = '<input type="checkbox" name="val[flags]['.$lKey.']" value="'.$lKey.'" ';
      $lChkBox.= 'id="flagcheckbox'.$lKey.'" ';
      if (in_array($lKey, $lValues) OR $lAllFlags === TRUE) {
        $lChkBox.= 'checked="checked"';
      }
      if ($lKey == 0) {
        $lChkBox.= ' onclick="javascript:gIgn=1;checkAllFlags(\'' .$lFlags . '\')"';
      } else {
        $lChkBox.= ' onclick="javascript:gIgn=1;uncheckAllFlags(\'flagcheckbox'.$lKey.'\', \'' .$lFlags . '\');"';
      }
      $lChkBox.= '>&nbsp;';

      $lPath = getImgPath('img/jfl/'.$lKey.'.gif');
      $lChkBox.= img($lPath, array('style' => 'margin-right:1px'));

      $lChkBox.= '&nbsp;'.$lValue;
      $lChkBoxes[] = $lChkBox;
    }

    $lRet = '<div id="'.$aDivId.'" class="smDiv" style="display:none">';
    $lRet.= '  <table border="0" cellspacing="0" cellpadding="2" class="tbl mw200">';

    for ($lDummy = 0; $lDummy < count($lChkBoxes); $lDummy++) {
      $lRet.= "    <tr>";
      $lRet.= '      <td class="td1 nw">'.$lChkBoxes[$lDummy].'</td>';
      $lRet.= "    </tr>";
    }

    $lRet.= '  </table>';
    $lRet.= '</div>';

    return $lRet;
  }
}