<?php
class CInc_Usr_Crp_Status_Form extends CCor_Ren {

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
      $lRet.= img('img/ico/16/check-hi.gif',array('data-toggle' => 'tooltip', 'data-tooltip-head' => lan('lib.rig.inh'), 'data-tooltip-body' => $lMem));
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

    $lSql = 'SELECT sta_id FROM al_usr_rig_status WHERE usr_id='.$this -> mUid.' AND sta_id IN ('.$lSid.')';
    $lQry -> query($lSql);
    foreach ($lQry as $lRow) {
      $this -> mRig[$lRow['sta_id']] = true;
    }

    if (!empty($this -> mMem)) {
      $lSql = 'SELECT gru_id,sta_id FROM al_gru_rig_status WHERE gru_id IN ('.implode(',', array_keys($this -> mMem)).') ';
      $lSql.= 'AND sta_id IN ('.$lSid.')';
      $lQry -> query($lSql);
      foreach ($lQry as $lRow) {
        $this -> mMemRig[$lRow['sta_id']][] = $this -> mMem[$lRow['gru_id']];
      }
    }
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="usr-crp-status.sedt" />'.LF;
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
    $this -> mSid = array();

    // SUBSTRING( LPAD( BIN( flags ) , 8, '0' ) , 5, 1 ) = '1' = Feld "Flags" ist converted to BINARY to find out if the 4.Bit(008 -> Edit Rights) ist equal to 1.
    // There must be "008 - Editing privileges necessary " in the Helptabel.
    $lSql = 'SELECT id,display,name_'.LAN.' AS name FROM al_crp_status WHERE mand='.MID.' AND crp_id='.$this -> mCrp.' AND SUBSTRING( LPAD( BIN( flags ) , 8, \'0\' ) , 5, 1 ) = \'1\' ORDER BY display';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mSta[$lRow['id']] = $lRow;
      $this -> mSid[] = $lRow['id'];
    }

    //$lArr = array_keys($this -> mSta);

    $this -> getPriv();

    foreach ($this -> mSta as $lRow) {
      $lRet.= '<tr>';
      $lRet.= '<td class="td2 ac">'.$this -> getCheck($lRow).'</td>';
	  $lPath = CApp_Crpimage::getSrcPath($this->mSrc, 'img/crp/'.$lRow['display'].'b.gif');
      $lRet.= '<td class="td1 c p4">'.img($lPath).'</td>';
      #$lRet.= '<td class="td1">&nbsp;</td>';
      $lRet.= '<td class="td1 b p4" colspan="2">'.htm($lRow['name']).'</td>';
      $lRet.= '</tr>'.LF;

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