<?php
class CInc_Usr_Crp_Form extends CCor_Ren {

  protected $mSrc;

  public function __construct($aUid, $aCrp) {
    $this -> mUid = intval($aUid);
    $this -> mCrp = intval($aCrp);
	$this -> mSrc = CApp_Crpimage::getCriticalPathSrc($aCrp);

    $lArr = CCor_Res::extract('id', 'name_'.LAN, 'crpmaster');
    $this -> mTitle = $lArr[$this -> mCrp];
    $this -> mHas = FALSE;
  }

  protected function getCheck($aStp) {
    $lRet = '';
    $lSid = $aStp['id'];
    if (isset($this -> mMemRig[$lSid])) {
      $lMem = implode('<br />', $this -> mMemRig[$lSid]);
      $lRet.= toolTip($lMem,'Right inherited from').img('img/ico/16/check-hi.gif').'</span>';
      return $lRet;
    }
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
    $this -> mMem = array();
    $this -> mRig = array();
    $this -> mMemRig = array();

    if (empty($this -> mSid)) return;

    $lQry = new CCor_Qry('SELECT m.gid,g.name FROM al_usr_mem m, al_gru g WHERE m.gid=g.id AND m.uid='.$this -> mUid);
    foreach ($lQry as $lRow) {
      $lGid = intval($lRow['gid']);
      $this -> mMem[$lGid] = $lRow['name'];
    }

    $lSid = implode(',',$this -> mSid);

    $lQry = new CCor_Qry();

    $lSql = 'SELECT stp_id FROM al_usr_rig_stp WHERE usr_id='.$this -> mUid.' AND stp_id IN ('.$lSid.')';
    $lQry -> query($lSql);
    foreach ($lQry as $lRow) {
      $this -> mRig[$lRow['stp_id']] = true;
    }

    if (!empty($this -> mMem)) {
      $lSql = 'SELECT gru_id,stp_id FROM al_gru_rig_stp WHERE fla_id=0 AND gru_id IN ('.implode(',', array_keys($this -> mMem)).') ';
      $lSql.= 'AND stp_id IN ('.$lSid.')';
      $lQry -> query($lSql);
      foreach ($lQry as $lRow) {
        $this -> mMemRig[$lRow['stp_id']][] = $this -> mMem[$lRow['gru_id']];
      }
    }
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="usr-crp.sedt" />'.LF;
    $lRet.= '<input type="hidden" name="id" value="'.$this -> mUid.'" />'.LF;
    $lRet.= '<input type="hidden" name="crp" value="'.$this -> mCrp.'" />'.LF;

    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w400">'.LF;
    $lRet.= '<thead>'.LF;
    $lRet.= '<tr><td class="cap" colspan="3">'.htm($this -> mTitle).'</td></tr>'.LF;
    $lRet.= '<tr>';
    $lRet.= '<td class="th2 w16">&nbsp;</td>';
    $lRet.= '<td class="th2 w16">&nbsp;</td>';
    $lRet.= '<td class="th2 w100p">Name</td>';
    $lRet.= '</tr>'.LF;
    $lRet.= '</thead>'.LF;
    $lRet.= '<tbody>'.LF;

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
      $lSql = 'SELECT stp.id,stp.from_id,stp.name_'.LAN.' AS name,sta.display FROM al_crp_step stp, al_crp_status sta WHERE sta.mand='.MID.' AND stp.from_id IN ('.implode(',', $lArr).') AND stp.to_id=sta.id';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $this -> mStp[$lRow['from_id']][] = $lRow;
        $this -> mSid[] = $lRow['id'];
      }
    }
    $this -> getPriv();

    foreach ($this -> mSta as $lRow) {
      $lRet.= '<tr>';
	  $lPath = CApp_Crpimage::getSrcPath($this->mSrc, 'img/crp/'.$lRow['display'].'b.gif');
      $lRet.= '<td class="td1 c p4">'.img($lPath).'</td>';
      #$lRet.= '<td class="td1">&nbsp;</td>';
      $lRet.= '<td class="td1 b p4" colspan="2">'.htm($lRow['name']).'</td>';
      $lRet.= '</tr>'.LF;

      if (!empty($this -> mStp[$lRow['id']])) {
        $lAll = $this -> mStp[$lRow['id']];
        foreach ($lAll as $lStp) {
          $lRet.= '<tr>';
          $lRet.= '<td class="td2 ac">'.$this -> getCheck($lStp).'</td>';
		  $lPath = CApp_Crpimage::getSrcPath($this->mSrc, 'img/crp/'.$lStp['display'].'l.gif');
          $lRet.= '<td class="td2 ac">'.img($lPath).'</td>';
//          $lSid = $aStp['id'];
          $lSid = $lStp['id'];
          if (isset($this -> mMemRig[$lSid])) {
            $lRet.= '<td class="td2 p4">';
            $lRet.= htm($lStp['name']);
            $lRet.= '</td>';
          } else {
            $lRet.= '<td class="td2 p4">'.htm($lStp['name']).'</td>';
          }


          $lRet.= '</tr>'.LF;
        }
      }
    }
    $lRet.= '<tr>'.LF;
    $lRet.= '<td colspan="3" class="btnPnl">';
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