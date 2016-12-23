<?php
/**
 * Title
 *
 * Description
 *
 * @package    package
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */

class CInc_Job_Apl_Page_Wrap extends CCor_Ren {

  public static function wrap($aImg, $aHis, $aBtn) {
    $lRet = '<table cellpadding="2" cellspacing="0" class="w100p">';
    
    if (empty($aBtn -> mJob)) {
      $lRet.= '<tr>'.LF;
      $lRet.= '<td valign="top" style="padding-right:16px">'.LF;
      $lRet.= '</td>'.LF;
      $lRet.= '<td valign="top" colspan="2">'.LF;
      if($aHis -> ShowAplButtons()) {
        $lRet.= lan('apl.no_apl').BR.'&nbsp;';
      } else {
        $lRet.= lan('apl.no_access').BR.'&nbsp;';
      }
      $lRet.= '</td>'.LF;
      $lRet.= '</tr>'.LF;
    }
    
    $lRet.= '<tr>'.LF;

    $lRet.= '<td valign="top" style="padding-right:16px">'.LF;
    $lRet.= $aImg -> getContent();
    $lRet.= '</td>'.LF;

    $lRet.= '<td valign="top" class="w100p" style="padding-right:16px">'.LF;
    $lRet.= $aHis -> getContent();
    $lRet.= '</td>'.LF;

    $lRet.= '<td valign="top">'.LF;
    $lRet.= $aBtn -> getContent();
    $lRet.= '</td>'.LF;

    $lRet.= '</tr></table>'.LF;
    return $lRet;
  }

}