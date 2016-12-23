<?php
class CInc_Hom_Mand_Form extends CCor_Ren {

  public function __construct($aMod = '') {
      $this -> mMod = $aMod;
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
  if (!empty($this -> mMod)) {
      $lAct = 'scpy';
    } else {
      $lAct = 'post';
    }
    $lRet.= '<input type="hidden" name="act" value="hom-mand.'.$lAct.'" />'.LF;

    $lRet.= '<div class="tbl" style="width:420px;">'.LF;
    $lRet.= $this -> getHeader();
    $lRet.= $this -> getForm();
    $lRet.= $this -> getButtons();
    $lRet.= '</div>'.LF;

    $lRet.= '</form>'.LF;
    return $lRet;
  }

  protected function getHeader() {
    $lRet = '<div class="th1" style="padding:4px;">'.LF;
    if (!empty($this -> mMod)) {
      $lTitle = $this -> mMod;
    } else {
      $lTitle = 'lib.mand.chg';
    }
    $lRet.= htmlan($lTitle);
    $lRet.= '</div>'.LF;
    return $lRet;
  }


  protected function getForm() {
    $lRet = '<div class="frm" style="padding:16px;">'.LF;
    $lRet.= '<table cellpadding="4" cellspacing="0" border="0">'.LF;

    $lUsr = CCor_Usr::getInstance();
    $lSql = 'SELECT mand FROM al_usr_mand WHERE uid = '.$lUsr->getId().' AND mand = 0';
    $this->mMandId = CCor_Qry::getInt($lSql);

     if ($lUsr -> canRead('mand')) {
      $lRet.= '<tr>';
      $lRet.= '<td class="nw">'.htmlan('lib.mand').'</td>'.LF;
      $lRet.= '<td>';

   // If user is under mand 0, than he can see all mandatores, else user can see only the mands he is member of.
      if ($this->mMandId === 0)
        {
           $lArr = CCor_Res::extract('code', 'name_'.LAN, 'mand');
        }
        else {
           $lArr = $lUsr->getMandMembership();
        }
      $lCur = $lUsr -> getPref('sys.mand');
      $lCurUsr = $lUsr ->getId();
      $lMandList = CCor_Res::extract('code', 'disabled', 'mand');

      $lRet.= '<input type="hidden" name="old[sys.mand]" value="'.$lCur.'" />'.LF;
      $lRet.= '<select name="val[sys.mand]" class="w200">';
      if (empty($this -> mMod)) { // Change
        if ($lUsr -> canInsert('sys-admin')) {
          $lCust = CCor_Cfg::get('cust.pfx', 'pfx');
          $lRet.= '<option value="'.$lCust.'">'.htmlan('mand.unab').'</option>'.LF;

          $lNewMand = CCor_Cfg::get('cust.NewMand', 'NewMand');
          if (isset($lArr[$lNewMand])) {
            $lRet.= '<option value="'.$lNewMand.'">'.htm($lArr[$lNewMand]).'</option>'.LF;
          }
          $lDummyMand = CCor_Cfg::get('cust.DummyMand', 'DummyMand');
          if (isset($lArr[$lDummyMand])) {
            $lRet.= '<option value="'.$lDummyMand.'">'.htm($lArr[$lDummyMand]).'</option>'.LF;
          }
          $lRet.= '<option value="-1">-------------------------</option>'.LF;
        }
        unset($lArr[$lNewMand]);
        unset($lArr[$lDummyMand]);
      }
      //Disabled mand should not be selectable
      foreach ($lArr as $lKey => $lVal) {
        if($lMandList[$lKey] == "Y") {
          $lDisabled = "disabled style='background-color:#ffaaaa;'";
          $lVal .= " [" . lan("lib.lock") . "]";
        }
        else {
          $lDisabled = "";
        }
        $lSel = ($lCur == $lKey) ? ' selected="selected"' : '';
        $lRet.= '<option value="'.$lKey.'"'.$lSel.' '.$lDisabled.'>'.htm($lVal).'</option>'.LF;
      }

      $lRet.= '</select>'.LF;
      $lRet.= '</td>';
      $lRet.= '</tr>'.LF;
    }

    $lRet.= '</table>'.LF;
    $lRet.= BR.BR;
    $lRet.= '</div>'.LF;
    return $lRet;
  }

  protected function getButtons() {
    $lRet = '<div class="frm" style="padding:16px; text-align:right;">'.LF;
    $lRet.= btn(lan('lib.ok'), 'this.form.submit()', '<i class="ico-w16 ico-w16-ok"></i>', 'submit').NB;
    $lRet.= btn(lan('lib.cancel'), "go('index.php?act=hom-wel')", '<i class="ico-w16 ico-w16-cancel"></i>');
    $lRet.= '</div>'.LF;
    return $lRet;
  }

}