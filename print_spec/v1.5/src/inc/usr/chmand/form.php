<?php
/**
 * User Mandator Membership Form
 *
 * @author Akram Hajali <a.hajali@5flow.eu>
 * @package usr
 * @subpackage chmand
 * @copyright  Copyright (c) 2011-2012 5Flow GmbH (http://www.5flow.eu)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 09:50:56 +0100 (Di, 21 Feb 2012) $
 * @author $Author: ahajali $
 */

class CInc_Usr_Chmand_Form extends CCor_Ren {

  public function __construct($aUid, $aUmand) {
    $this -> mUid = intval($aUid);
    $this -> mAct = 'usr-chmand.sedt';
    $this -> getData();
    $this -> mMand = CCor_Res::get('mand');
    $this -> mUmand = intval($aUmand);
  }

  protected function getData() {
    $this -> mchmand = array();
    $lQry = new CCor_Qry('SELECT mand FROM al_usr_mand WHERE uid= '.$this -> mUid);
    foreach ($lQry as $lRow) {
      $this -> mchmand[$lRow['mand']] = TRUE;
    }
  }

  protected function getRow($aRow) {
    $lId = $aRow['id'];
    $lNam = 'val['.$lId.']';
    $lRet = '<tr>'.LF;
    $lRet.= '<td class="td2 w16">'.LF;
    $lSel = (isset($this -> mchmand[$aRow['id']])) ? ' checked="checked"' : '';

    $lRet.= '<input type="checkbox" id="chk'.$aRow['id'].'" name="'.$lNam.'"'.$lSel.' onclick="chmand(this)" />'.LF;
    $lRet.= '</td>'.LF;

    $lRet.= '<td class="td1 w100p">';
    $lRet.= htm($aRow['name_en']);
    $lRet.= '</td>'.LF;
    $lRet.= '</tr>'.LF;

    return $lRet;
  }

  protected function getMands() {
    $lRet = '';
    foreach ($this -> mMand as $lRow) {
      $lRet.= $this -> getRow($lRow);
    }
    return $lRet;
  }

  protected function getCont() {
    $lRet = '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mAct.'" />'.LF;
    $lRet.= '<input type="hidden" name="id" value="'.$this -> mUid.'" />'.LF;

    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w500">'.LF;
    $lRet.= '<tr><td class="cap" colspan="3">'.htm(lan('lib.mand')).'</td></tr>'.LF;

    $lRet.= '<tr><td class="sub" colspan="3" style="padding:16px;">'.LF;
    $lRet.= btn(lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit').LF;
    $lRet.= btn(lan('lib.reset'), '', 'img/ico/16/cancel.gif', 'reset').LF;
    $lRet.= btn(lan('lib.expandall'), 'Flow.Std.showAllTr()','img/ico/16/nav-down-lo.gif').LF;
    $lRet.= btn(lan('lib.collapseall'), 'Flow.Std.hideAllTr()','img/ico/16/nav-up-lo.gif').LF;

    #$lRet.= btn(lan('lib.cancel'), '', 'img/ico/16/cancel.gif', 'reset').LF;
    $lRet.= '</td></tr>'.LF;
    $lRet.= '<tr><td class="th1 w16">&nbsp;</td><td class="th1" colspan="2">'.htm(lan('lib.mand')).'</td></tr>';

    $lRet.= $this -> getMands();

    $lRet.= '</table>'.LF;

    $lRet.= '</form>';
    return $lRet;
  }


}