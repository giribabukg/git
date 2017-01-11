<?php
class CInc_Hom_Pwd_Form extends CCor_Ren {

  protected function getCont() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="hom-pwd.post" />'.LF;

    $lRet.= '<div class="tbl" style="width:380px;">'.LF;
    $lRet.= $this -> getHeader();
    $lRet.= $this -> getForm();
    $lRet.= $this -> getButtons();
    $lRet.= '</div>'.LF;

    $lRet.= '</form>'.LF;
    return $lRet;
  }

  protected function getHeader() {
    $lRet = '<div class="th1" style="padding:4px;">'.LF;
    $lRet.= htm(lan('hom.pwd.change'));
    $lRet.= '</div>'.LF;
    return $lRet;
  }

  protected function getForm() {
    $lRet = '<div class="frm" style="padding:16px;">'.LF;
    $lRet.= '<table cellpadding="4" cellspacing="0" border="0">'.LF;

    $lRet.= '<tr>';
    $lRet.= '<td class="nw">'.htm(lan('hom.pwd.old')).'</td>'.LF;
    $lRet.= '<td>';
    $lRet.= '<input type="password" name="val[old]" class="inp w200" />';
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;

    $lRet.= '<tr><td colspan="2">&nbsp;</td></tr>'.LF;

    $lRet.= '<tr>';
    $lRet.= '<td class="nw">'.htm(lan('hom.pwd.new')).'</td>'.LF;
    $lRet.= '<td>';
    $lRet.= '<input type="password" name="val[new]" class="inp w200" />';
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;

    $lRet.= '<tr>';
    $lRet.= '<td class="nw">'.htm(lan('hom.pwd.confirm')).'</td>'.LF;
    $lRet.= '<td>';
    $lRet.= '<input type="password" name="val[cnf]" class="inp w200" />';
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;

    $lRet.= '</table>'.LF;
    $lRet.= BR.BR;
    $lRet.= '</div>'.LF;
    return $lRet;
  }

  protected function getButtons() {
    $lRet = '<div class="frm" style="padding:16px; text-align:right">'.LF;
    $lRet.= btn(lan('lib.ok'), 'javascript:Flow.checkPwd(); return false;', '<i class="ico-w16 ico-w16-ok"></i>', 'submit').NB;
    $lRet.= btn(lan('lib.cancel'), "go('index.php?act=hom-wel')", '<i class="ico-w16 ico-w16-cancel"></i>');
    $lRet.= '</div>'.LF;
    return $lRet;
  }

}
