<?php
class CInc_Job_Fil_View_Form extends CCor_Ren {

  protected $mJobTypes = array();

  public function __construct() {
    $this -> mCap = lan('job-fil-view.cat');

    $this -> mJobTypes = $this -> getJobTypes();
  }

  function getJobTypes() {
    $lActiveJobTypes = CCor_Cfg::get('menu-aktivejobs');
    foreach ($lActiveJobTypes as $lKey => $lValue) {
      $lActiveJobTypes[$lKey] = ltrim($lValue, 'job-');
      if ('all' == strtolower($lActiveJobTypes[$lKey])) {
        unset($lActiveJobTypes[$lKey]);
      }
    }

    $lArchivedJobTypes = CCor_Cfg::get('menu-archivjobs');
    foreach ($lArchivedJobTypes as $lKey => $lValue) {
      $lArchivedJobTypes[$lKey] = ltrim($lValue, 'job-');
      if ('all' == strtolower($lArchivedJobTypes[$lKey])) {
        unset($lArchivedJobTypes[$lKey]);
      }
    }

    $lJobTypes = array_merge($lActiveJobTypes, $lArchivedJobTypes);
    $lJobTypes = array_unique($lJobTypes);
    sort($lJobTypes);

    return $lJobTypes;
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '  <input type="hidden" name="act" value="job-fil-view.scat" />'.LF;
    $lRet.= '  <table cellpadding="2" cellspacing="0" class="tbl w600">'.LF;
    $lRet.= '    <tr>'.LF;
    $lRet.= '      <td colspan="8" class="cap">'.htm($this -> mCap).'</td>'.LF;
    $lRet.= '    </tr>'.LF;
    $lRet.= '    <tr>'.LF;
    $lRet.= '      <td class="th2"></td>'.LF;
    $lRet.= '      <td class="th2 w150 ac">'.htm(lan('lib.cat.view')).'</td>'.LF;
    $lRet.= '      <td class="th2 w150 ac">'.htm(lan('lib.cat.switch.button')).'</td>'.LF;
    $lRet.= '    </tr>'.LF;
    foreach ($this -> mJobTypes as $lKey => $lValue) {
      $lRet.= '  <tr>';
      $lRet.= '    <td class="td1">'.htm(lan('job-'.$lValue.'.menu')).'</td>'.LF;
      $lRet.= '    <td class="td1 w150 ac">'.$this -> getCategoryView($lValue).'</td>'.LF;
      $lRet.= '    <td class="td1 w150 al">'.$this -> getCatgegorySwitchButton($lValue).'</td>'.LF;
      $lRet.= '  </tr>'.LF;
    }
    $lRet.= '    <tr>'.LF;
    $lRet.= '      <td class="btnPnl" colspan="8">'.LF;
    $lRet.= btn(lan('lib.ok'), '', '<i class="ico-w16 ico-w16-ok"></i>', 'submit').NB;
    $lRet.= btn(lan('lib.reset'), 'this.form.reset()', '<i class="ico-w16 ico-w16-cancel"></i>');
    $lRet.= '      </td>'.LF;
    $lRet.= '    </tr>'.LF;
    $lRet.= '  </table>'.LF;
    $lRet.= '</form>'.LF;
    return $lRet;
  }

  protected function getCategoryView($aSrc) {
    $lSql = 'SELECT val FROM al_sys_pref WHERE code="job-'.$aSrc.'.fil.cat.view" AND mand='.MID;
    $lVal = CCor_Qry::getInt($lSql);
    $lVal = (int)$lVal;

    $lRet = '';
    if ($lVal == 1) {
      $lRet.= '<input type="hidden" name="old[job-'.$aSrc.'.fil.cat.view]" value="1" />';
      $lRet.= '<select name="val[job-'.$aSrc.'.fil.cat.view]">';
      $lRet.= '<option value="0">'.lan('lib.columns').'</option>';
      $lRet.= '<option value="1" selected="selected">'.lan('lib.rows').'</option>';
    } else {
      $lRet.= '<input type="hidden" name="old[job-'.$aSrc.'.fil.cat.view]" value="0" />';
      $lRet.= '<select name="val[job-'.$aSrc.'.fil.cat.view]">';
      $lRet.= '<option value="0" selected="selected">'.lan('lib.columns').'</option>';
      $lRet.= '<option value="1">'.lan('lib.rows').'</option>';
    }
    $lRet.= '</select>';

    return $lRet;
  }

  protected function getCatgegorySwitchButton($aSrc) {
    $lSql = 'SELECT val FROM al_sys_pref WHERE code="job-'.$aSrc.'.fil.cat.switch.button" AND mand='.MID;
    $lVal = CCor_Qry::getInt($lSql);
    $lVal = (int)$lVal;

    $lRet = '';
  	switch  ($lVal) {
  	  case 0:
        $lRet.= '<input type="hidden" name="old[job-'.$aSrc.'.fil.cat.switch.button]" value="0" />';
        $lRet.= '<input type="radio" name="val[job-'.$aSrc.'.fil.cat.switch.button]" value="0" checked="checked" />'.lan('lib.not.available').'<br>';
        $lRet.= '<input type="radio" name="val[job-'.$aSrc.'.fil.cat.switch.button]" value="1" />'.lan('lib.always.available').'<br>';
        $lRet.= '<input type="radio" name="val[job-'.$aSrc.'.fil.cat.switch.button]" value="2" />'.lan('lib.rights.needed');
      break;
  	  case 1:
        $lRet.= '<input type="hidden" name="old[job-'.$aSrc.'.fil.cat.switch.button]" value="1" />';
        $lRet.= '<input type="radio" name="val[job-'.$aSrc.'.fil.cat.switch.button]" value="0" />'.lan('lib.not.available').'<br>';
        $lRet.= '<input type="radio" name="val[job-'.$aSrc.'.fil.cat.switch.button]" value="1" checked="checked" />'.lan('lib.always.available').'<br>';
        $lRet.= '<input type="radio" name="val[job-'.$aSrc.'.fil.cat.switch.button]" value="2" />'.lan('lib.rights.needed');
  	  break;
  	  case 2:
        $lRet.= '<input type="hidden" name="old[job-'.$aSrc.'.fil.cat.switch.button]" value="2" />';
        $lRet.= '<input type="radio" name="val[job-'.$aSrc.'.fil.cat.switch.button]" value="0" />'.lan('lib.not.available').'<br>';
        $lRet.= '<input type="radio" name="val[job-'.$aSrc.'.fil.cat.switch.button]" value="1" />'.lan('lib.always.available').'<br>';
        $lRet.= '<input type="radio" name="val[job-'.$aSrc.'.fil.cat.switch.button]" value="2" checked="checked" />'.lan('lib.rights.needed');
  	  break;
  	}

    return $lRet;
  }
}