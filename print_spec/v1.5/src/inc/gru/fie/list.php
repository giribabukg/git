<?php
class CInc_Gru_Fie_List extends CHtm_List {

  public function __construct($aSrc = 'pro', $aCode = 'ids') {
    parent::__construct('gru-fie');

    $this -> mTitle = lan('gru-fie');
    $this -> mSubTitle = false;

    $this -> mSrc = $aSrc;
    $this -> mKey = $aCode;

    $this -> getDefs();
    #$this -> getRig();
    $this -> loadPriv();

    $this -> addPanel('rig', $this -> getRigForm());
    $this -> addPanel('btn', $this -> getButtons());

    $this -> addColumn('group', 'Group',  false, array('width' => '200'));
    $this -> addColumn('read',  'Read',   false, array('width' => '50'));
    $this -> addColumn('edit',  'Edit',   false, array('width' => '50'));
  }

  protected function getDefs() {
    $this -> mDef = array();
    $lSql = 'SELECT src,code,name FROM al_fie_blocks ORDER BY src,code,name';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this -> mDef[$lRow -> src][$lRow -> code] = $lRow -> name;
    }
  }

  protected function loadPriv() {
    $this -> mPriv = array();
    $lQry = new CCor_Qry('SELECT gid,level FROM al_gru_fie WHERE src="'.addslashes($this -> mSrc).'" AND code="'.addslashes($this -> mKey).'"');
    foreach($lQry as $lRow) {
      $this -> mPriv[$lRow -> gid] = intval($lRow -> level);
    }
  }

  protected function getSel($aSrc, $aCap) {
    $lCap = (isset($lArr[$aSrc])) ? $lArr[$aSrc] : $aSrc;
    $lRet = '<optgroup label="'.htm($aCap).'">';
    foreach ($this -> mDef[$aSrc] as $lCod => $lNam) {
      $lSel = (($this -> mSrc == $aSrc) and ($this -> mKey == $lCod)) ? ' selected="selected"' : '';
      $lRet.= '<option value="'.$aSrc.'-'.$lCod.'"'.$lSel.'>';
      $lRet.= htm($lNam);
      $lRet.= '</option>';
    }
    $lRet.= '</optgroup>';
    return $lRet;
  }

  protected function getRigForm() {
    $lRet = '';
    $lRet.= '<table cellpadding="4" cellspacing="0" border="0"><tr>';
    $lRet.= '<td nowrap="nowrap"><b>Field Block&nbsp;</b></td>';
    $lRet.= '<td>';

    $lArr = array();
    $lArr['pro'] = 'Project';
    $lArr['pde'] = 'Print Development';
    $lArr['mba'] = 'MBA/MAW';
    $lArr['tpl'] = 'Variants';
    $lArr['pac'] = 'Packaging';

    $lRet.= '<select name="rig" onchange="go(\'index.php?act=gru-fie.select&amp;rig=\'+this.value)">';
    foreach ($lArr as $lKey => $lCap) {
      $lRet.= $this -> getSel($lKey, $lCap);
    }
    $lRet.= '</select>';
    $lRet.= '</td>';
    $lRet.= '</tr></table>';
    return $lRet;
  }

  protected function getButtons() {
    $lRet = '<table cellpadding="4" cellspacing="0" border="0"><tr>';
    $lRet.= '<td>'.btn(lan('lib.ok'), 'this.form.submit()', 'img/ico/16/ok.gif', 'submit').'</td>';
    $lRet.= '<td>'.btn(lan('lib.reset'), 'this.form.reset()', 'img/ico/16/cancel.gif', 'reset').'</td>';
    $lRet.= '</tr></table>';
    return $lRet;
  }

  protected function getGruPriv($aGid) {
    return (isset($this -> mPriv[$aGid])) ? $this -> mPriv[$aGid] : 0;
  }

  protected function getSubCheck($aGid, $aLvl) {
    $lRet = '<td class="td1 ac">';
    $lId  = 'chk_'.$aGid.'_'.$aLvl;
    $lNam = 'val['.$aGid.']['.$aLvl.']';
    $lChk = (bitset($this -> mSubPriv, $aLvl)) ? ' checked="checked"' : '';
    $lJs = ' onclick="rigSub(this,'.$aLvl.','.$this -> mPid.')"';

    $lRet.= '<input type="checkbox" id="'.$lId.'" name="'.$lNam.'"'.$lJs.$lChk.' />';
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getSubRow() {
    $lRet = '<tr>';

    $lRet.= '<td class="td2">&nbsp;&nbsp;';
    $lRet.= htm($this -> mSubRow['name']);
    $lRet.= '</td>';

    $lGid = $this -> mSubRow['id'];

    $this -> mSubPriv = $this -> getGruPriv($lGid);

    $lRet.= $this -> getSubCheck($lGid, rdRead);
    $lRet.= $this -> getSubCheck($lGid, rdEdit);

    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  private function getSubRows($aParent) {
    $this -> mChildren = array();
    $lRet = '';
    $lQry = new CCor_Qry('SELECT id,name FROM al_gru WHERE parent_id='.$aParent.' ORDER BY name');
    foreach ($lQry as $this -> mSubRow)  {
      $this -> mChildren[] = $this -> mSubRow['id'];
      $lRet.= $this -> getSubRow();
    }
    return $lRet;
  }

  protected function getCheck($aGid, $aLvl) {
    $lRet = '<td class="tg1 ac">';
    $lId  = 'chk_'.$aGid.'_'.$aLvl;
    $lNam = 'val['.$aGid.']['.$aLvl.']';
    $lChk = (bitset($this -> mParPriv, $aLvl)) ? ' checked="checked"' : '';
    $lJs = '';
    if (!empty($this -> mChildren)) {
      $lJs = ' onclick="rigChk(this,'.$aLvl.',\''.implode(',', $this -> mChildren).'\')"';
    }
    $lRet.= '<input type="checkbox" id="'.$lId.'" name="'.$lNam.'"'.$lJs.$lChk.' />';
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getRow() {
    $this -> mPid = $this -> mRow['id'];
    if ($this -> mPid == 75) {
      // restrictive groups
      return '';
    }
    $lSub = $this -> getSubRows($this -> mPid);

    $lRet = '<tr>';
    $lRet.= '<td class="tg1">';
    $lRet.= htm($this -> mRow['name']);
    $lRet.= '</td>';

    $this -> mParPriv = $this -> getGruPriv($this -> mPid);

    $lRet.= $this -> getCheck($this -> mPid, rdRead);
    $lRet.= $this -> getCheck($this -> mPid, rdEdit);

    $lRet.= '</tr>'.LF;
    $lRet.= $lSub;
    return $lRet;
  }

  protected function getRows() {
    $lRet = '';
    $this -> mCls = 'td1';
    $this -> mCtr = 1;

    $lQry = new CCor_Qry('SELECT id,name FROM al_gru WHERE parent_id=0 ORDER BY name');
    foreach ($lQry as $this -> mRow)  {
      $lRet.= $this -> getRow();
    }
    #$this -> doAfterRows();
    return $lRet;
  }

  protected function getCont() {
    $lRet = '<form action="index.php" method="post">';
    $lRet.= '<input type="hidden" name="act" value="gru-fie.sedt" />';
    $lRet.= '<input type="hidden" name="src" value="'.$this -> mSrc.'" />';
    $lRet.= '<input type="hidden" name="code" value="'.$this -> mKey.'" />';
    $lRet.= parent::getCont();
    $lRet.= '</form>';
    return $lRet;
  }

}