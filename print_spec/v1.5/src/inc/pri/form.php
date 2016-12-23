<?php
class CInc_Pri_Form extends CHtm_Form {
  
  protected function getForm() {
    $lRet = '<div class="frm" style="padding:16px;">'.LF;
    $lRet.= '<table cellpadding="4" cellspacing="0" border="0">'.LF;
    $lRet.= '<tr>'.LF;
    $lRet.= '<td>File (max. 5MB)</td>';
    $lRet.= '<td><input type="file" name="file" maxlength="5242880" /></td>';
    $lRet.= '</tr>'.LF;
    $lRet.= '</table>';
    $lRet.= '</div>';
    return $lRet;
  }  

}