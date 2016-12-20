<?php
class CInc_Hom_Pref_Form extends CCor_Ren {

  protected function getCont() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="hom-pref.post" />'.LF;

    $lRet.= '<div class="tbl" style="width:100%;">'.LF;
    $lRet.= $this -> getHeader();
    $lRet.= $this -> getForm();
    $lRet.= $this -> getButtons();
    $lRet.= '</div>'.LF;

    $lRet.= '</form>'.LF;
    return $lRet;
  }

  protected function getHeader() {
    $lRet = '<div class="th1" style="padding:4px;">'.LF;
    $lRet.= htm(lan('hom.pref'));
    $lRet.= '</div>'.LF;
    return $lRet;
  }

  protected function getMsgLine($aCap, $aTyp) {
    $lUsr = CCor_Usr::getInstance();
    $lRet = '<tr>';
    $lRet.= '<td class="nw">'.$aCap.'</td>'.LF;
    $lRet.= '<td>';
    $lArr = array();
    $lArr[mlNone] = 'None';
    $lArr[mlFatal] = 'Fatal only';
    $lArr[mlFatal + mlError] = 'Fatal + Errors';
    $lArr[mlAll - mlInfo] = 'Fatal, Error and Warn';
    $lArr[mlAll] = 'All';

    $lCur = $lUsr -> getPref('sys.msg.mt'.$aTyp, 0);
    $lRet.= '<input type="hidden" name="old[sys.msg.mt'.$aTyp.']" value="'.$lCur.'" />'.LF;
    $lRet.= '<select name="val[sys.msg.mt'.$aTyp.']" class="w200">';
    foreach ($lArr as $lKey => $lVal) {
      $lSel = ($lCur == $lKey) ? ' selected="selected"' : '';
      $lRet.= '<option value="'.$lKey.'"'.$lSel.'>'.htm($lVal).'</option>'.LF;
    }
    $lRet.= '</select>'.LF;
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;

    return $lRet;
  }

  protected function getButtons() {
    $lRet = '<div class="frm" style="padding:16px; text-align:right;">'.LF;
    $lRet.= btn(lan('lib.ok'), 'this.form.submit()', '<i class="ico-w16 ico-w16-ok"></i>', 'submit').NB;
    $lRet.= btn(lan('lib.cancel'), "go('index.php?act=hom-wel')", '<i class="ico-w16 ico-w16-cancel"></i>');
    $lRet.= '</div>'.LF;
    return $lRet;
  }

  protected function getHomePrefStart($aUsr) {
    $lUsr = $aUsr;

    $lRet = '<tr>';
    $lRet.= '<td class="nw">'.lan('hom.pref.start').'</td>'.LF;
    $lRet.= '<td>';
    $lArr = array();
    $lArr['hom-wel'] = lan('hom-wel.menu');
    $lArr['hom-fla'] = lan('hom-fla.menu');
    if ($lUsr -> canRead('job-pro'))
    		$lArr['job-pro'] = lan('job-pro.menu');
    if ($lUsr -> canRead('job-all'))
      $lArr['job-all'] = lan('job-all.menu');
    if ($lUsr -> canRead('job-art'))
    		$lArr['job-art'] = lan('job-art.menu');
    if ($lUsr -> canRead('job-rep'))
    		$lArr['job-rep'] = lan('job-rep.menu');
    if ($lUsr -> canRead('job-sec'))
    		$lArr['job-sec'] = lan('job-sec.menu');
    if ($lUsr -> canRead('job-adm'))
    		$lArr['job-adm'] = lan('job-adm.menu');
    if ($lUsr -> canRead('job-mis'))
    		$lArr['job-mis'] = lan('job-mis.menu');
    if ($lUsr -> canRead('job-com'))
    		$lArr['job-com'] = lan('job-com.menu');
    if ($lUsr -> canRead('job-tra'))
    		$lArr['job-tra'] = lan('job-tra.menu');
    $lCur = $lUsr -> getPref('log.url', 'hom-wel');
    $lRet.= '<input type="hidden" name="old[log.url]" value="'.$lCur.'" />'.LF;
    $lRet.= '<select name="val[log.url]" class="w200">';
    foreach ($lArr as $lKey => $lVal) {
    		$lSel = ($lCur == $lKey) ? ' selected="selected"' : '';
    		$lRet.= '<option value="'.$lKey.'"'.$lSel.'>'.htm($lVal).'</option>'.LF;
    }
    $lRet.= '</select>'.LF;
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;

    return $lRet;
  }

  protected function getLanguageForm($aUsr) {
    $lUsr = $aUsr;
    $lRet = '';

    if ($lUsr -> canRead('usr.lang')) {
      $lRet.= '<tr>';
      $lRet.= '<td class="nw">'.lan('lib.lang').'</td>'.LF;
      $lRet.= '<td>';
      $this -> mAvailLang = CCor_Res::get('languages');
      $lArr = array();
      foreach ($this -> mAvailLang as $lLang => $lName) {
        $lArr[$lLang] = strtoupper($lLang).' - '.lan('lan.'.$lLang);
      }
      $lCur = $lUsr -> getPref('sys.lang');
      $lRet.= '<input type="hidden" name="old[sys.lang]" value="'.$lCur.'" />'.LF;
      $lRet.= '<select name="val[sys.lang]" class="w200">';
      foreach ($lArr as $lKey => $lVal) {
        $lSel = ($lCur == $lKey) ? ' selected="selected"' : '';
        $lRet.= '<option value="'.$lKey.'"'.$lSel.'>'.htm($lVal).'</option>'.LF;
      }
      $lRet.= '</select>'.LF;
      $lRet.= '</td>';
      $lRet.= '</tr>'.LF;
    }

    if ($lUsr -> canRead('job-cms')) {
      $lRet.= '<tr>';
      $lRet.= '<td class="nw">Phrase: '.lan('lib.masterlang').'</td>'.LF;
      $lRet.= '<td>';
      $this -> mAvailLang = CCor_Res::get('htb', 'dln');
      $lArr = array();
      foreach ($this -> mAvailLang as $lLang => $lName) {
        $lArr[$lLang] = $lName;
      }
      $lCur = $lUsr -> getPref('sys.masterlang');
      $lCur = (empty($lCur)) ? "EN" : $lCur;
      $lRet.= '<input type="hidden" name="old[sys.masterlang]" value="'.$lCur.'" />'.LF;
      $lRet.= '<select name="val[sys.masterlang]" class="w200">';
      foreach ($lArr as $lKey => $lVal) {
        $lSel = ($lCur == $lKey) ? ' selected="selected"' : '';
        $lRet.= '<option value="'.$lKey.'"'.$lSel.'>'.htm($lVal).'</option>'.LF;
      }
      $lRet.= '</select>'.LF;
      $lRet.= '</td>';
      $lRet.= '</tr>'.LF;
    }

    return $lRet;
  }

  protected function getTimeZoneForm() {
    $lUse = CCor_Cfg::get('sys.timezone.pref', false);
    if (!$lUse) return '';
    $lUsr = CCor_Usr::getInstance();
    $lRet = '<tr>';
    $lRet.= '<td class="nw">'.lan('lib.tz.date').'</td>'.LF;
    $lRet.= '<td>';
    $lLocale = new Zend_Locale('de');
    $lZones = $lLocale->getTranslationList('TerritoryToTimezone');
    $lArr = array();
    foreach ($lZones as $lZone => $lDummy) {
      $lArr[] = $lZone;
    }
    $lCur = $lUsr -> getPref('sys.timezone');
    $lRet.= '<input type="hidden" name="old[sys.timezone]" value="'.$lCur.'" />'.LF;
    $lRet.= '<select name="val[sys.timezone]" class="w200">';
    $lRet.= '<option value="">'.NB.'</option>';
    foreach ($lArr as $lVal) {
      $lSel = ($lCur == $lVal) ? ' selected="selected"' : '';
      $lRet.= '<option value="'.htm($lVal).'"'.$lSel.'>'.htm($lVal).'</option>'.LF;
    }
    $lRet.= '</select>'.LF;
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;
    return $lRet;

  }

  protected function getDalimPrefForm() {
    $lUse = CCor_Cfg::get('dalim.available', false);
    if (!$lUse) return '';
    $lUserHasRight = CJob_Fil_Folders::canRead('dalim');
    if (!$lUserHasRight) return '';
    $lUsr = CCor_Usr::getInstance();
    $lRet = '<tr>';
    $lRet.= '<td class="nw">Dalim Viewer</td>'.LF;
    $lRet.= '<td>';
    $lCur = $lUsr -> getPref('dalim.viewer', 'java');

    $lArr = array ('java' => 'Java', 'html5' => 'HTML 5 (New)');

    $lRet.= '<input type="hidden" name="old[dalim.viewer]" value="'.$lCur.'" />'.LF;
    $lRet.= '<select name="val[dalim.viewer]" class="w200">';
    foreach ($lArr as $lKey => $lVal) {
      $lSel = ($lCur == $lKey) ? ' selected="selected"' : '';
      $lRet.= '<option value="'.htm($lKey).'"'.$lSel.'>'.htm($lVal).'</option>'.LF;
    }
    $lRet.= '</select>'.LF;
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;
    return $lRet;

  }

  protected function getTooltipForm($aUsr) {
    $lUsr = $aUsr;

    $lRet = '<tr>';
    $lRet.= '<td class="nw">'.htm(lan('hom.pref.btn.tooltips')).'</td>'.LF;
    $lRet.= '<td>';
    $lArr = array();
    $lArr['Y'] = lan('lib.yes');
    $lArr['N'] = lan('lib.no');
    $lCur = $lUsr -> getPref('job.btntips', 'Y');
    $lRet.= '<input type="hidden" name="old[job.btntips]" value="'.$lCur.'" />'.LF;
    $lRet.= '<select name="val[job.btntips]" class="w200">';
    foreach ($lArr as $lKey => $lVal) {
      $lSel = ($lCur == $lKey) ? ' selected="selected"' : '';
      $lRet.= '<option value="'.$lKey.'"'.$lSel.'>'.htm($lVal).'</option>'.LF;
    }

    $lRet.= '<tr>';
    $lRet.= '<td class="nw">'.htm(lan('hom.pref.frm.tooltips')).'</td>'.LF;
    $lRet.= '<td>';
    $lArr = array();
    $lArr['Y'] = lan('lib.yes');
    $lArr['N'] = lan('lib.no');
    $lCur = $lUsr -> getPref('job.feldtips', 'Y');
    $lRet.= '<input type="hidden" name="old[job.feldtips]" value="'.$lCur.'" />'.LF;
    $lRet.= '<select name="val[job.feldtips]" class="w200">';
    foreach ($lArr as $lKey => $lVal) {
      $lSel = ($lCur == $lKey) ? ' selected="selected"' : '';
      $lRet.= '<option value="'.$lKey.'"'.$lSel.'>'.htm($lVal).'</option>'.LF;
    }

    $lRet.= '</select>'.LF;
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;

    $lRet.= '<tr>';
    $lRet.= '<td class="nw">'.htm(lan('hom.pref.bar.tooltips')).'</td>'.LF;
    $lRet.= '<td>';
    $lArr = array();
    $lArr['Y'] = lan('lib.yes');
    $lArr['N'] = lan('lib.no');
    $lCur = $lUsr -> getPref('job.bartips', 'Y');
    $lRet.= '<input type="hidden" name="old[job.bartips]" value="'.$lCur.'" />'.LF;
    $lRet.= '<select name="val[job.bartips]" class="w200">';
    foreach ($lArr as $lKey => $lVal) {
      $lSel = ($lCur == $lKey) ? ' selected="selected"' : '';
      $lRet.= '<option value="'.$lKey.'"'.$lSel.'>'.htm($lVal).'</option>'.LF;
    }
    $lRet.= '</select>'.LF;
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;

    return $lRet;
  }

  protected function getDebugForm($aUsr) {
    $lUsr = $aUsr;

    $lRet = '';
    if ($lUsr -> canRead('dbg')) {
      // Group ID 31 = QBF
      $lRet.= $this -> getMsgLine('Usr Msg', mtUser);
      $lRet.= $this -> getMsgLine('Dbg Msg', mtDebug);
      $lRet.= $this -> getMsgLine('Php Msg', mtPhp);
      $lRet.= $this -> getMsgLine('Sql Msg', mtSql);
      $lRet.= $this -> getMsgLine('Api Msg', mtApi);

      $lRet.= '<tr><td>Message Order</td><td>'.LF;
      $lCur = $lUsr -> getPref('sys.msg.ord');
      $lRet.= '<input type="hidden" name="old[sys.msg.ord]" value="'.$lCur.'" />'.LF;
      $lArr = array();
      $lArr[moLevel] = 'Message Level';
      $lArr[moType]  = 'Message Type';
      $lArr[moTime]  = 'Time';
      $lRet.= '<select name="val[sys.msg.ord]" class="w200">';
      foreach ($lArr as $lKey => $lVal) {
        $lSel = ($lCur == $lKey) ? ' selected="selected"' : '';
        $lRet.= '<option value="'.$lKey.'"'.$lSel.'>'.htm($lVal).'</option>'.LF;
      }
      $lRet.= '</select>'.LF;
      #$lRet.= '<input type="text" class="inp" name="val[sys.msg.ord]" value="'.$lCur.'" />'.LF;
      $lRet.= '</td></tr>'.LF;
    }

    return $lRet;
  }

  protected function getRecieveEmailForm($aUsr) {
    $lUsr = $aUsr;

    $lRet = '';
    if ($lUsr -> canRead('rcv-email')) {
      $lRet.= '<tr>';
      $lRet.= '<td class="nw">'.htm(lan('hom.pref.emails')).'</td>'.LF;
      $lRet.= '<td>';
      $lArr = array();
      $lArr['Y'] = lan('lib.yes');
      $lArr['N'] = lan('lib.no');
      $lCur = $lUsr -> getPref('rcv.email');
      $lRet.= '<input type="hidden" name="old[rcv.email]" value="'.$lCur.'" />'.LF;
      $lRet.= '<select name="val[rcv.email]" class="w200">';
      foreach ($lArr as $lKey => $lVal) {
        $lSel = ($lCur == $lKey) ? ' selected="selected"' : '';
        $lRet.= '<option value="'.$lKey.'"'.$lSel.'>'.htm($lVal).'</option>'.LF;
      }
      $lRet.= '</select>'.LF;
      $lRet.= '</td>';
      $lRet.= '</tr>'.LF;
    }

    return $lRet;
  }

  protected function getAplRedirectForm($aUsr) {
    $lUsr = $aUsr;

    $lRet = '<tr>';
    $lRet.= '<td class="nw">'.htm(lan('hom.pref.apl.redirect')).'</td>'.LF;
    $lRet.= '<td>';
    $lArr = array();
    $lArr['home'] = lan('hom-wel.menu');
    $lArr['job'] = lan('lib.jobform');
    $lArr['apl'] = lan('job-apl.menu');
    $lCur = $lUsr -> getPref('apl.redirect', 'home');
    $lRet.= '<input type="hidden" name="old[apl.redirect]" value="'.$lCur.'" />'.LF;
    $lRet.= '<select name="val[apl.redirect]" class="w200">';
    foreach ($lArr as $lKey => $lVal) {
      $lSel = ($lCur == $lKey) ? ' selected="selected"' : '';
      $lRet.= '<option value="'.$lKey.'"'.$lSel.'>'.htm($lVal).'</option>'.LF;
    }
    $lRet.= '</select>'.LF;
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;

    $lRet.= '</table>'.LF;
    $lRet.= BR.BR;
    $lRet.= '</div>'.LF;

    return $lRet;
  }

  protected function getForm() {
    $lUsr = CCor_Usr::getInstance();

    $lRet = '<div class="frm" style="padding:16px;">'.LF;
    $lRet.= '<table cellpadding="4" cellspacing="0" border="0">'.LF;

    $lRet.= $this -> getHomePrefStart($lUsr);
    $lRet.= $this -> getLanguageForm($lUsr);
    $lRet.= $this -> getDalimPrefForm();
    $lRet.= $this -> getTimezoneForm();
    $lRet.= $this -> getTooltipForm($lUsr);
    $lRet.= $this -> getDebugForm($lUsr);
    $lRet.= $this -> getRecieveEmailForm($lUsr);
    $lRet.= $this -> getAplRedirectForm($lUsr);

    /*
    $lRet.= '<tr>';
    $lRet.= '<td class="nw">Date format</td>'.LF;
    $lRet.= '<td>';
    $lArr = array();
    $lArr['d.m.Y'] = 'day.month.year';
    $lArr['m/d/Y'] = 'month/day/year';
    $lCur = $lUsr -> getPref('sys.date.fmt', 'd.m.Y');
    $lRet.= '<input type="hidden" name="old[log.url]" value="'.$lCur.'" />'.LF;
    $lRet.= '<select name="val[log.url]" class="w200">';
    foreach ($lArr as $lKey => $lVal) {
      $lSel = ($lCur == $lKey) ? ' selected="selected"' : '';
      $lRet.= '<option value="'.$lKey.'"'.$lSel.'>'.htm($lVal).'</option>'.LF;
    }
    $lRet.= '</select>'.LF;
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;
    */

    return $lRet;
  }
}