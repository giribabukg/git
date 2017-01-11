<?php
class CInc_Hol_Form extends CCor_Ren {

  public function __construct($aDate) {
    $this -> mDat = $aDate;
    $lDat = new CCor_Date($aDate);
    $this -> mTitle = lan('hol.on').' '.$lDat -> getFmt(lan('lib.date.week'));
    $this -> getRec();
  }

  //ToDo SQL ist falsch, da es zwei Datensaetze geben kann: mand = 0/MID
  protected function getRec() {
    $lQry = new CCor_Qry('SELECT * FROM `al_sys_holidays` WHERE datum="'.addslashes($this -> mDat).'"');
    if ($lRow = $lQry -> getAssoc()) {
      $this -> mRec = $lRow;
    }
  }

  protected function getCont() {
    $lRet = '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="hol.sedt" />'.LF;
    $lRet.= '<input type="hidden" name="day" value="'.$this -> mDat.'" />'.LF;
    $lRet.= '<input type="hidden" name="mand" value="'.MID.'" />'.LF;
    $lRet.= '<div class="tbl" style="width:350px">'.LF;

    $lRet.= '<div class="th1">'.LF;
    $lRet.= htm($this -> mTitle);
    $lRet.= '</div>'.LF;

    $lRet.= '<div class="frm" style="padding:16px;">'.LF;
    $lRet.= '<table cellpadding="4" cellspacing="0" border="0">'.LF;

    $lMid  = '';
    $lLan = array();
    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $lLan[$lLang] = '';
    }
    $lFree = 'Y';
    $lFix  = 'N';
    if (isset($this -> mRec)) {
      $lMid  = $this -> mRec['mand'];
      foreach ($this -> mAvailLang as $lLang => $lName) {
        $lLan[$lLang] = $this -> mRec['name_'.$lLang];
      }
      $lFree = $this -> mRec['free'];
      $lFix  = $this -> mRec['fix'];
    }
    /*
     $lRet.= '<tr>';
    $lRet.= '<td>'.lan('lib.mand').'</td>';
    $lRet.= '<td><input type="text" name="mand" value="'.htm($lMid).'" class="inp w200" /></td>';
    $lRet.= '</tr>';
    */
    foreach ($lLan as $lLang => $lName) {
      $lRet.= '<tr>';
      $lRet.= '<td>'.lan('lib.name').' '.lan('lan.'.$lLang).'</td>';
      $lRet.= '<td><input type="text" name="'.$lLang.'" value="'.htm($lName).'" class="inp w200" /></td>';
      $lRet.= '</tr>';
    }

    $lRet.= '<tr>';
    $lRet.= '<td>'.lan('hol.free').'</td>';
    if ('N' == $lFree) {
      $lCheckY = '';
      $lCheckN = ' checked="checked"';
    } else {
      $lCheckY = ' checked="checked"';
      $lCheckN = '';
    }
    $lRet.= '<td><input type="radio" name="free" value="Y"'.$lCheckY.'>'.lan('lib.yes');
    $lRet.= '<input type="radio" name="free" value="N"'.$lCheckN.'>'.lan('lib.no').'</td>';
    $lRet.= '</tr>';

    $lRet.= '<tr>';
    $lRet.= '<td>'.lan('hol.fixdate').'</td>';
    if ('N' == $lFix) {
      $lCheckY = '';
      $lCheckN = ' checked="checked"';
    } else {
      $lCheckY = ' checked="checked"';
      $lCheckN = '';
    }
    $lRet.= '<td><input type="radio" name="fix" value="Y"'.$lCheckY.'>'.lan('lib.yes');
    $lRet.= '<input type="radio" name="fix" value="N"'.$lCheckN.'>'.lan('lib.no').'</td>';
    $lRet.= '</tr>';

    $lRet.= '</table>'.LF;
    $lRet.= '</div>'.LF;
    $lRet.= $this -> getButtons();
    $lRet.= '</div>';


    $lRet.= '</form>';
    return $lRet;
  }

  protected function getButtons() {
    $lRet = '<div class="frm" style="padding:16px; text-align:right;">'.LF;
    $lRet.= btn(lan('lib.ok'), 'this.form.submit()', 'img/ico/16/ok.gif', 'submit').NB;
    if (isset($this -> mRec)) {
      $lRet.= btn('Delete', "Flow.Std.cnf('index.php?act=hol.del&day=".$this -> mDat."', 'cnfDel')", 'img/ico/16/ok.gif').NB;
    }
    $lRet.= '</div>'.LF;
    return $lRet;
  }

}