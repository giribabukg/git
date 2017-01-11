<?php
class CInc_Usr_External_List extends CHtm_List {

  public function __construct() {
    parent::__construct('usr-external');

    $this -> setAtt('width', '100%');
    $this -> mTblId = getNum('t');
    $this -> setAtt('id', $this -> mTblId);
    $this -> mTitle = lan('usr-external.menu');

    $this -> addColumn('ctr');
    $this -> addColumn('sel',     '',                   FALSE, array('width' => '16'));
    $this -> addColumn('anrede',     lan('lib.salutation'), TRUE, array('width' => '16'));
    $this -> addColumn('firstname',  lan('lib.firstname'), TRUE);
    $this -> addColumn('lastname',   lan('lib.lastname'), TRUE);
    $this -> addColumn('phone',      lan('lib.phone'), TRUE);
    $this -> addColumn('email',      lan('lib.email'), TRUE);
    $this -> addColumn('created',    'Created', TRUE);
    $this -> addDel();
   
    
    $this -> getPrefs();
    $this -> mIte = new CCor_TblIte('al_usr_tmp_external');
    
    if ( ! empty($this -> mSer)) {
      $lName = (empty($this -> mSer['name'])) ? '' : trim($this -> mSer['name']);
      if ( ! empty($lName)) {
        $lName = esc('%' . $lName . '%');
        $lCnd = '(firstname LIKE ' . $lName . ' ';
        $lCnd .= 'OR lastname LIKE ' . $lName . ')';
        $this -> mIte -> addCnd($lCnd);
      }
    }
    if (!empty($this -> mFil)) {
        if ('act' == $this -> mFil) {
          $this -> mIte -> addCnd('flags=1');
        } elseif ('del' == $this -> mFil) {
          $this -> mIte -> addCnd('flags=0');
        } 
        $this -> mIte -> addCnd('mand IN(0,'.MID.')');
      }
    
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);
    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();

    $this -> addPanel('nav', $this -> getNavBar());
    $this -> addPanel('vie', $this -> getViewMenu());
    $this -> addPanel('cap', '| '.htmlan('lib.field'));
    $this -> addPanel('sta', $this -> getFilterMenu());
    $this -> addPanel('sca', '| '.htmlan('lib.search'));
    $this -> addPanel('ser', $this -> getSearchForm());
  }

  protected function getFilterMenu() {
      $lRet = '';
      $lRet.= '<form action="index.php" method="post">'.LF;
      $lRet.= '<input type="hidden" name="act" value="usr-external.fil" />'.LF;
      $lRet.= '<select name="val" size="1" onchange="this.form.submit()">'.LF;
      $lArr['act'] = lan('ext.usr.waiting');
      $lArr['del'] = lan('ext.usr.initial');
    
      $lFil = (isset($this -> mFil)) ? $this -> mFil : '';
      foreach ($lArr as $lKey => $lVal) {
        $lRet.= '<option value="'.htm($lKey).'" ';
        if ($lKey == $lFil) {
          $lRet.= ' selected="selected"';
        }
        $lRet.= '>'.htm($lVal).'</option>'.LF;
      }
      $lRet.= '</select>'.LF;
      $lRet.= '</form>';
      return $lRet;
    }

  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="usr-external.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['name'])) ? htm($this -> mSer['name']) : '';
    $lRet.= '<td><input type="text" name="val[name]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.all'),'go("index.php?act=usr-external.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getTdSel() {
    $lId = $this -> getInt('id');
    $lRet = '<input type="checkbox" value="'.$lId.'" />';
    return $this -> tdc($lRet);
  }

  protected function getBody() {
    $lRet = $this -> getRows();

    $lJs = 'function selExtUsrAll(aEl) {'.LF;
    $lJs.= 'var lVal = aEl.checked;';
    $lJs.= '$("'.$this -> mTblId.'").getElementsBySelector("[type=\'checkbox\']").each(function(aNod) {aNod.checked=lVal;});';
    $lJs.='}'.LF;
    $lJs.= 'function ConExtUsrAll(aEl) {'.LF;
    $lJs.= 'var lSel = new Array();';
    $lJs.= '$("'.$this -> mTblId.'").getElementsBySelector("[type=\'checkbox\']").each(function(aNod) {if ((aNod.checked) && (aNod.value!="on")) lSel.push(aNod.value)});'.LF;
    $lJs.= 'Flow.Std.cnf("index.php?act=usr-external.conextusrselected&ids=" + lSel.join(","), "cnfExtUsr");';
    $lJs.='}'.LF;
    
    $lJs.= 'function ConExtUsrAllDel(aEl) {'.LF;
    $lJs.= 'var lSel = new Array();';
    $lJs.= '$("'.$this -> mTblId.'").getElementsBySelector("[type=\'checkbox\']").each(function(aNod) {if ((aNod.checked) && (aNod.value!="on")) lSel.push(aNod.value)});'.LF;
    $lJs.= 'Flow.Std.cnf("index.php?act=usr-external.inactivedelsel&ids=" + lSel.join(","), "cnfDel");';
    $lJs.='}'.LF;
    
    $lPag = CHtm_Page::getInstance();
    $lPag -> addJs($lJs);
    
    $lRet.= '<tr><td class="th2">&nbsp;</td>';
    $lRet.= '<td class="th2"><input type="checkbox" onclick="selExtUsrAll(this)" /></td>';
    $lRet.= '<td class="th2" colspan="4">';
    if ('act' == $this -> mFil) {
    $lRet.= '<a href="javascript:ConExtUsrAll(this)">'.lan('ext.usr.confirm').'</a>';
    }
    elseif ('del' == $this -> mFil){
    $lRet.= '<a href="javascript:ConExtUsrAllDel(this)">'.lan('ext.usr.inactive.delete').'</a>';
    }
    $lRet.= '</td>';
    $lRet.= '</tr>';
    
    return $lRet;
  }
  
}