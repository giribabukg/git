<?php
/**
 * User Membership Form
 *
 * @author Geoffrey Emmans <emmans@qbf.de>
 * @package usr
 * @subpackage mem
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 14379 $
 * @date $Date: 2016-06-06 16:28:05 +0200 (Mon, 06 Jun 2016) $
 * @author $Author: psarker $
 */

class CInc_Usr_Mem_Form extends CCor_Ren {

  public function __construct($aUid) {
    $this -> mUid = intval($aUid);
    $this -> mAct = 'usr-mem.sedt';
    $this -> getData();

    $this -> mGru = CCor_Res::get('gru');
    $this -> mChildren = array();
    foreach ($this->mGru as $lGid => $lRow) {
      $lParent = intval($lRow['parent_id']);
      $this->mChildren[$lParent][] = $lRow;
    }
    $this -> mUsr = CCor_Usr::getInstance();
    $Any = new CCor_Anyusr($this -> mUsr -> getAuthId());
    $this -> mUsrAdminLevel = $Any -> getAdminLevel();
  }

  protected function getData() {
    $this -> mMem = array();
    $lQry = new CCor_Qry('SELECT gid FROM al_usr_mem m,al_gru g WHERE m.mand IN (0, '.MID.') AND m.uid="'.$this -> mUid.'" AND m.gid = g.id AND g.del="N"');
    foreach ($lQry as $lRow) {
      $this -> mMem[$lRow['gid']] = TRUE;
    }
  }

  protected function hasChildren($aGid) {
    return isset($this->mChildren[$aGid]);
  }

  protected function getRow($aRow) {
    if($aRow['admin_level'] < $this -> mUsrAdminLevel AND $aRow["admin_level"] != 0) return;
    $lId = $aRow['id'];
    $lRoot = (0 == $aRow['parent_id']);
    $lHas = $this -> hasChildren($lId);
    $lNam = 'val['.$lId.']';
    $lRet = '<tr>'.LF;
    $lRet.= '<td class="td2 w16">'.LF;
    $lSel = (isset($this -> mMem[$aRow['id']])) ? ' checked="checked"' : '';
    $lHtmId = 'chk'.$lId;
    $lDis = ($this->getMemberCount($lId) >= $aRow["max_usr"] && $aRow["max_usr"] > 0 && $lSel === "") ? 'disabled="disabled"' : "";

    $lRet.= '<input type="checkbox" id="'.$lHtmId.'" name="'.$lNam.'"'.$lSel.' onclick="mem(this)" '.$lDis.' />'.LF;
    $lRet.= '</td>'.LF;

    if ($lRoot) {
      $lRet.= '<td class="td2 w100p b">';
    } else {
      $lRet.= '<td class="td1 w100p">';
    }

    if ($lHas) {
      $lRet.= '<a href="javascript:Flow.Std.togTr(\'tr'.$lId.'\')">';
      $lRet.= htm($aRow['name']).' ...';
      $lRet.= '</a>';
    } else {
      $lRet.= '<label for="'.$lHtmId.'" class="db cp nav">'.htm($aRow['name']).'</label>';
    }
    $lRet.= '</td>'.LF;
    $lRet.= '</tr>'.LF;

    if ($lHas) {
      $lRet.= '<tr id="tr'.$lId.'" class="togtr" style="display:none">';
      $lRet.= '<td class="td1 tg">&nbsp;</td>';
      $lRet.= '<td class="p0">';
      $lRet.= '<table cellpadding="2" cellspacing="0" border="0" class="w100p">';
      $lRet.= $this -> getGroups($lId);
      $lRet.= '</table>';
      $lRet.= '</td>';
      $lRet.= '</tr>';
    }
    return $lRet;
  }

  protected function getGroups($aParent) {
    $lRet = '';
    foreach($this->mChildren[$aParent] as $lRow) {
      $lRet.= $this -> getRow($lRow);
    }
    return $lRet;
  }

  protected function getCont() {
    $lRet = '<form action="index.php" method="post">'.LF;
    $lRet.= '  <input type="hidden" name="act" value="'.$this -> mAct.'" />'.LF;
    $lRet.= '  <input type="hidden" name="id" value="'.$this -> mUid.'" />'.LF;
    $lRet.= '  <table cellpadding="2" cellspacing="0" class="tbl w500">'.LF;
    $lRet.= '    <tr>'.LF;
    $lRet.= '      <td class="cap" colspan="3">'.htm(lan('usr-mem.menu')).'</td>'.LF;
    $lRet.= '    </tr>'.LF;
    $lRet.= '    <tr>'.LF;
    $lRet.= '      <td class="sub" colspan="3" style="padding:16px;">'.LF;
    $lRet.= btn(lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit').LF;
    $lRet.= btn(lan('lib.reset'), '', 'img/ico/16/cancel.gif', 'reset').LF;
    $lRet.= btn(lan('lib.expandall'), 'Flow.Std.showAllTr()','img/ico/16/nav-down-lo.gif').LF;
    $lRet.= btn(lan('lib.collapseall'), 'Flow.Std.hideAllTr()','img/ico/16/nav-up-lo.gif').LF;
    $lRet.= '      </td>'.LF;
    $lRet.= '    </tr>'.LF;
    $lRet.= '    <tr>'.LF;
    $lRet.= '      <td class="th1 w16">&nbsp;</td>'.LF;
    $lRet.= '      <td class="th1" colspan="2">'.htm(lan('lib.group')).'</td>'.LF;
    $lRet.= '    </tr>';
    if ($this -> hasChildren(0)) {
      $lRet.= $this -> getGroups(0);
    }
    $lRet.= '  </table>'.LF;
    $lRet.= '</form>';
    return $lRet;
  }

  protected function getMemberCount($aId) {
    $lSql = 'SELECT COUNT(DISTINCT(m.uid)) AS num FROM al_usr_mem m, al_usr u '; #, al_usr_mand um ';
    $lSql.= 'WHERE m.uid=u.id AND u.del="N" ';
    $lSql.= 'AND m.gid IN ('.$aId.') ';
    $lSql.= 'AND m.mand IN (0,'.MID.') ';

    $lSql.= 'GROUP BY m.gid';
    $lQry = CCor_Qry::getStr($lSql);

    return $lQry;
  }
}