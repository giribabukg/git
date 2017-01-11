<?php
class CInc_Hom_Wel_Backupbox extends CCor_Ren {

  public function __construct() {
    $lUsr = CCor_Usr::getInstance();
    $lUid = CCor_Usr::getAuthId();

    $this -> mImOnHoliday = $lUsr -> getPref('usr.onholiday');
    $this -> mMyBackupUsr = $lUsr -> getVal('backup');

    $this -> mIsOnHoliday = CCor_Qry::getStr('SELECT val FROM al_usr p, al_usr_pref q WHERE p.id=q.uid AND q.mand='.MID.' AND q.code="usr.onholiday" AND p.backup='.$lUid);
    $this -> mImBackupUsr = CCor_Qry::getInt('SELECT u.id FROM al_usr u, al_usr_mand m WHERE u.id=m.uid AND u.del="N" AND m.mand In(0,'.MID.') AND u.backup='.$lUid);

    $this -> mUsrIDsArr = array();

    $lBackupGroupLimits = CCor_Cfg::get('backup-groups.limit');
    if ($lBackupGroupLimits) {
      $this -> mSpecialGroups = array();
      $this -> getLimitedBackUpLists(array_flip($lBackupGroupLimits));
      $lUsrMem = $this -> mSpecialGroups;
      $lUsrMem = implode(',', array_keys($this -> mSpecialGroups));
    } else $lUsrMem = CCor_Usr::getMembershipImplode(); // group ids of current user

    if (!empty($lUsrMem)) {
      $lCntUsrMem = count(explode(',', $lUsrMem)); // number of groups of current user
      $lAllUsrGroups = array();
      $lAllUsrGroups = explode(',', $lUsrMem);
      $lNoParentGroups = array(); // group ids with no child groups of current user
      foreach ($lAllUsrGroups as $lValue) {
        if (!$lUsr->isMemberOf($lValue)) continue;
      	$lSql = 'SELECT * FROM al_gru WHERE parent_id='.$lValue;
      	$lGIDs = CCor_Qry::getInt($lSql);
      	if (empty($lGIDs)) {
      		$lNoParentGroups[] = $lValue;
      	}
      }
      $lUsrMem = implode(',', $lNoParentGroups);
    } else {
    	$lCntUsrMem = 0;
    }

    if ($lCntUsrMem > 0 && !empty($lUsrMem)) { // Get all Usres IDs that are in the same group as this User and also they are member of this mand.
      $lUsrIDsArr = array();
      $lSQL = 'SELECT a.id FROM al_usr a, al_usr_mem b, al_usr_mand c WHERE a.id=b.uid AND a.id<>'.$lUid.' AND a.del<>"Y" AND b.mand='.MID.' AND b.gid IN ('.$lUsrMem.') AND c.mand='.MID.' AND a.id=c.uid GROUP BY a.id';
      $lUsrIDs = new CCor_Qry($lSQL);
      foreach ($lUsrIDs -> getObjects() as $lKey => $lValue) {
      	$lUsrIDsArr[] = $lValue -> id;
      }
      if (count($lUsrIDsArr) > 0) {
        $this -> mUsrIDsArr = $lUsrIDsArr;
      }
    }
  }

  protected function getLimitedBackUpLists($aGroups) {
    foreach ($aGroups as $lKey => $lVal) {
      $lChildren = CCor_Res::extract('id', 'name', 'gru', array('gid' => $lKey));
      $this -> mSpecialGroups = $this -> mSpecialGroups + $lChildren;
      if ($lChildren) self::getLimitedBackUpLists($lChildren);
    }
  }

  protected function getImOnHoliday() {
    $lUsr = CCor_Usr::getInstance();
    $lUid = CCor_Usr::getAuthId();

    $lRet = '<td class="w16">';
    if ($this -> mImOnHoliday == 'Y') {
      $lRet.= '<i class="ico-w16 ico-w16-ok"></i>';
    } else {
      $lRet.= '<i class="ico-w16 ico-w16-check-lo"></i>';
    }
    $lRet.= '</td>';

    $lRet.= '<td class="w400">';
    $lRet.= '<a href="index.php?act=hom-wel.imonholiday" class="nav">';
    $lRet.= htm(lan('lib.onholiday'));
    $lRet.= '</a>';
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getIsOnHoliday() {
    $lRet = '<td class="w16">';
    if ($this -> mIsOnHoliday == 'Y') {
      $lRet.= '<i class="ico-w16 ico-w16-ok"></i>';
    } else {
      $lRet.= '<i class="ico-w16 ico-w16-check-lo"></i>';
    }
    $lRet.= '</td>';

    $lRet.= '<td class="w400">';
    $lRet.= '<a href="index.php?act=hom-wel.isonholiday" class="nav">';
    $lRet.= htm(lan('lib.onholiday'));
    $lRet.= '</a>';
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getMyBackupUser() {
    $lUsr = CCor_Usr::getInstance();
    $lUid = CCor_Usr::getAuthId();

    $lRet = '<td class="w400" style="text-align:right">';
    $lRet.= lan('lib.mybackupuser').': ';
    $lRet.= '</td>'.LF;
    $lRet.= '<td class="w400">';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="hom-wel.mybackup" />'.LF;

    $lRet.= '<select name="mybackupuser" size="1" onchange="this.form.submit()">'.LF;
    $lRet.= '<option value="-1">'.lan('lib.nobackupuser').'</option>'.LF;
    if (count($this -> mUsrIDsArr) > 0) {
      $lUsrIDsImp = implode(',', $this -> mUsrIDsArr);

      $lAllUsr = new CCor_Qry('SELECT id, lastname, firstname FROM al_usr WHERE id IN ('.$lUsrIDsImp.') ORDER BY lastname');
      foreach ($lAllUsr as $lKey => $lValue) {
        $lRet.= '<option value="'.$lValue['id'].'" ';
        if ($this -> mMyBackupUsr == $lValue['id']) {
          $lRet.= ' selected="selected"';
        }
        $lRet.= '>'.htm($lValue['lastname'].', '.$lValue['firstname']).'</option>'.LF;
      }
    }
    $lRet.= '</select>'.LF;

    $lRet.= '</form>'.LF;
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getImBackupUser() {
    $lRet = '<td class="w400" style="text-align:right">';
    $lRet.= lan('lib.imbackupuser').': ';
    $lRet.= '</td>'.LF;
    $lRet.= '<td class="w400">';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="hom-wel.imbackup" />'.LF;

    $lRet.= '<select name="imbackupuser" size="1" onchange="this.form.submit()">'.LF;
    $lRet.= '<option value="-1">'.lan('lib.nobackupuser').'</option>'.LF;
    if (count($this -> mUsrIDsArr) > 0) {
      $lUsrIDsImp = implode(',', $this -> mUsrIDsArr);

      $lAllUsr = new CCor_Qry('SELECT id, lastname, firstname FROM al_usr WHERE id IN ('.$lUsrIDsImp.') ORDER BY lastname');
      foreach ($lAllUsr as $lKey => $lValue) {
        $lRet.= '<option value="'.$lValue['id'].'" ';
        if ($this -> mImBackupUsr == $lValue['id']) {
          $lRet.= ' selected="selected"';
        }
        $lRet.= '>'.htm($lValue['lastname'].', '.$lValue['firstname']).'</option>'.LF;
      }
    }
    $lRet.= '</select>'.LF;

    $lRet.= '</form>'.LF;
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getCont() {
    $lRet = '';

    $lRet.= '<div class="tbl w800">'.LF;
    $lRet.= '<div class="cap">'.htm(lan('lib.backupuser')).'</div>'.LF;
    $lRet.= '<div class="td1 p16">'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0" width="100%" height="100%">'.LF;
    $lRet.= '<tr>';
    $lRet.= $this -> getImOnHoliday().LF;
    $lRet.= $this -> getMyBackupUser().LF;
    $lRet.= '</tr>';
    $lRet.= '<tr>';
    $lRet.= $this -> getIsOnHoliday().LF;
    $lRet.= $this -> getImBackupUser().LF;
    $lRet.= '</tr>';
    $lRet.= '</table>'.LF;
    $lRet.= '</div>'.LF;
    $lRet.= '</div>'.LF;
    $lRet.= BR;

    return $lRet;
  }

}