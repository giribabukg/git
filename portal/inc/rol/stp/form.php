<?php
class CInc_Rol_Stp_Form extends CCor_Ren {

  protected $mRid = 0;
  protected $mCrp = 0;
  protected $mHas = FALSE;
  protected $mMem = array();
  protected $mRig = array();
  protected $mStp = array();
  protected $mSid = array();
  protected $mSrc;
  /*
   * For which Steps has the Role right
   */
  protected $mRoleStpRig = array();

  public function __construct($aRoleId, $aCrp) {
    $this -> mRid = intval($aRoleId);
    $this -> mCrp = intval($aCrp);
	$this -> mSrc = CApp_Crpimage::getCriticalPathSrc($aCrp);

    $lArr = CCor_Res::extract('id', 'name_'.LAN, 'crpmaster');
    $this -> mTitle = $lArr[$this -> mCrp];
    $this -> mHas = FALSE;
  }

  protected function getCheck($aStp) {
    $lRet = '';
    $lSid = $aStp['id'];
    if (isset($this -> mRig[$lSid])) {
      $lRet.= '<input type="hidden" name="old['.$lSid.']" value="1" />';
      $lRet.= '<input type="checkbox" name="val['.$lSid.']" checked="checked" value="1" />';
    } else {
      $lRet.= '<input type="hidden" name="old['.$lSid.']" value="0" />';
      $lRet.= '<input type="checkbox" name="val['.$lSid.']" value="1" />';
    }
    $this -> mHas = TRUE;
    return $lRet;
  }

  protected function getPriv() {
    $this -> mRig = array();
    if (empty($this -> mSid)) return;

    $lQry = new CCor_Qry();

    $lSid = implode(',', $this -> mSid);

    $lSql = 'SELECT stp_id FROM al_rol_rig_stp WHERE role_id='.$this -> mRid.' AND stp_id IN ('.$lSid.') AND show_mytask = "Y"';
    $lQry -> query($lSql);
    foreach ($lQry as $lRow) {
      $this -> mRig[$lRow['stp_id']] = true;
    }
  }

  protected function getCont() {
    $lFlagNames = CCor_Res::extract('id', 'name_'.LAN, 'fla');
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="rol-stp.sedt" />'.LF;
    $lRet.= '<input type="hidden" name="id" value="'.$this -> mRid.'" />'.LF;
    $lRet.= '<input type="hidden" name="crp" value="'.$this -> mCrp.'" />'.LF;

    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w400">'.LF;
    $lRet.= '<thead>'.LF;
    $lRet.= '<tr><td class="cap" colspan="4">'.htm($this -> mTitle).'</td></tr>'.LF;
    $lRet.= '<tr>';
    $lRet.= '<td class="th2 w16">&nbsp;</td>';
    $lRet.= '<td class="th2 w16">&nbsp;</td>';
    $lRet.= '<td class="th2 w16">&nbsp;</td>';
    $lRet.= '<td class="th2 w100p">Name</td>';
    $lRet.= '</tr>'.LF;
    $lRet.= '</thead>'.LF;
    $lRet.= '<tbody>'.LF;

    // Find out, for which Step has the Role right to exec.
    // Only allowed Steps are be showed in the form.
    $this -> mRoleStpRig = array();
    $lSql = 'SELECT stp_id,role_id,crp_id from al_rol_rig_stp where role_id='.$this->mRid.' AND crp_id='.$this->mCrp;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mRoleStpRig[] = $lRow['stp_id'];
    }
    $this -> mSta = array();
    $lSql = 'SELECT id,display,name_'.LAN.' AS name FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$this -> mCrp.' ORDER BY display';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mSta[$lRow['id']] = $lRow;
    }

    $this -> mStp = array();
    $this -> mSid = array();
    $lArr = array_keys($this -> mSta);
    if (!empty($lArr)) {
      $lSql = 'SELECT stp.id,stp.from_id,stp.name_'.LAN.' AS name,sta.display,stp.flag_act FROM al_crp_step stp, al_crp_status sta WHERE sta.mand='.MID.' AND stp.from_id IN ('.implode(',', $lArr).') AND stp.to_id=sta.id';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        if (in_array($lRow['id'],$this -> mRoleStpRig)){
          // For this Step has the Role right to change.
          $this -> mStp[$lRow['from_id']][] = $lRow;
          $this -> mSid[] = $lRow['id'];
       }
      }
    }
    $this -> getPriv();
  

    foreach ($this -> mSta as $lRow) {
      $lRet.= '<tr>';
	  $lPath = CApp_Crpimage::getSrcPath($this->mSrc, 'img/crp/'.$lRow['display'].'b.gif');
      $lRet.= '<td class="td1 c p4">'.img($lPath).'</td>';
      $lRet.= '<td class="td1 b p4" colspan="3">'.htm($lRow['name']).'</td>';
      $lRet.= '</tr>'.LF;

      if (!empty($this -> mStp[$lRow['id']])) {
        $lAll = $this -> mStp[$lRow['id']];
        foreach ($lAll as $lStp) {
          $lRet.= '<tr>';
          $lRet.= '<td class="td2 ac">'.$this -> getCheck($lStp).'</td>';
		  $lPath = CApp_Crpimage::getSrcPath($this->mSrc, 'img/crp/'.$lStp['display'].'l.gif');
          $lRet.= '<td class="td2 ac">'.img($lPath).'</td>';
          $lRet.= '<td class="td2 p4" colspan="2">'.htm($lStp['name']).'</td>';
          $lRet.= '</tr>'.LF;
          
        }
      }
    }
    $lRet.= '<tr>'.LF;
    $lRet.= '<td colspan="4" class="btnPnl">';
    $lRet.= btn(lan('lib.ok'), '', 'img/ico/16/ok.gif', 'submit').NB;
    $lRet.= btn(lan('lib.reset'), 'this.form.reset()', 'img/ico/16/cancel.gif');
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;

    $lRet.= '</tbody>'.LF;
    $lRet.= '</table>'.LF;

    $lRet.= '</form>'.LF;
    return $lRet;
  }

}