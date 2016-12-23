<?php
class CInc_Job_Next extends CCor_Ren {

  public function __construct($aSrc, $aJobId) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = intval($aJobId);
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= $this -> getComment('start');

    $lEmp = TRUE;
    $lRet.= '<br /><table cellpadding="2" cellspacing="0" border="0">'.LF;

    $lUsrArr = CCor_Res::get('usr');
    $lFie = CCor_Res::extract('alias', 'name_'.LAN, 'fie');

    $lSql = 'SELECT alias,user_id,subject,deadline FROM al_usr_act ';
    $lSql.= 'WHERE 1 ';
    $lSql.= 'AND ref_src="'.addslashes($this -> mSrc).'" ';
    $lSql.= 'AND ref_id='.$this -> mJobId.' ';
    $lSql.= 'AND (status & 5 = 5) ';
    $lSql.= 'ORDER BY deadline';

    $lDat = new CCor_Date();
    $lUid = CCor_Usr::getAuthId();

    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if (isset($lUsrArr[$lRow['user_id']])) {
        $lUsr = $lUsrArr[$lRow['user_id']];
        $lNam = substr($lUsr['firstname'], 0, 1).'. '.$lUsr['lastname'];
        $lCom = $lUsr['company'];
      } else {
        $lNam = 'not assigned';
        $lCom = '';
      }

      $lRol = '';
      if (isset($lFie[$lRow['alias']])) {
        $lRol = $lFie[$lRow['alias']];
      }

      $lDat -> setSql($lRow['deadline']);
      $lTxt = $lDat -> getFmt(lan('lib.date.week'));
      if ($lDat -> isPast()) {
        $lTxt = '<span style="color:red; font-weight:bold">'.$lTxt.NB.'</span>';
      }
      $lRet.= '<tr><td><b>Next Action</b></td><td>'.img('img/ico/9/attention.gif').'</td><td>'.htm($lRow['subject']).'</td></tr>'.LF;
      $lCls = ($lUid == $lRow['user_id']) ? ' class="box"' : '';
      $lRet.= '<tr><td colspan="2"><b>Who?</b></td><td'.$lCls.'>'.htm($lNam).'</td></tr>'.LF;
      if (!empty($lCom)) {
        $lRet.= '<tr><td colspan="2"><b>Company</b>&nbsp;</td><td>'.htm($lCom).'</td></tr>'.LF;
      }
      if (!empty($lRol)) {
        $lRet.= '<tr><td colspan="2"><b>Role</b>&nbsp;</td><td>'.htm($lRol).'</td></tr>'.LF;
      }
      $lRet.= '<tr><td colspan="2"><b>Deadline</b></td><td>'.$lTxt.'</td></tr>'.LF;
      $lRet.= '<tr><td colspan="3">&nbsp;</td></tr>'.LF;
      $lEmp = FALSE;
    }

    $lRet.= '</table>'.LF;
    $lRet.= $this -> getComment('end');
    if ($lEmp) {
      return '';
    }
    return $lRet;
  }

}