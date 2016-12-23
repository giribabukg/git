<?php
class CInc_Utl_Ctr_Picker extends CCor_Ren {

  public function __construct(ICor_Req $aReq) {
    $this -> mColCount = 5;
    $lSel = $aReq -> getVal('sel');

    $this -> mLis = $aReq -> getVal('lis');
    $this -> mDom = $aReq -> getVal('dom');
    $this -> mPar = $aReq -> getVal('parent');

    $this -> mSelStr = $lSel;
    $lArr = explode(',', $lSel);
    $this -> mSelArr = array();
    foreach ($lArr as $lItm) {
      $lVal = trim($lItm);
      if (!empty($lVal)) {
        $this -> mSelArr[$lVal] = 1;
      }
    }
  }

  protected function getHtbArray() {
    $lRet = array();
    $lQry = new CCor_Qry('SELECT value,value_'.LAN.' as value_en FROM al_htb_itm WHERE mand IN(0,'.MID.') AND domain="'
    .addslashes($this -> mDom).'" ORDER BY value');
    foreach ($lQry as $lRow) {
      $lRet[$lRow -> value] = $lRow -> value_en;
    }
    return $lRet;
  }

  protected function getLearnList() {
    if (empty($this -> mLis)) return '';
    $lRet = '';
    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w100p">'.LF;
    $lRet.= '<tr><td class="th1">'.htm(lan('lib.choices')).'</td></tr>'.LF;
    $lSql = 'SELECT DISTINCT(val) AS val FROM al_fie_choice WHERE alias="'.addslashes($this -> mLis).'" ';
    $lSql.= 'ORDER BY val';
    $lQry = new CCor_Qry($lSql);

    $lCls = 'td1';
    foreach ($lQry as $lRow) {
      $lVal = $lRow['val'];
      $lRet.= '<tr><td class="'.$lCls.' nw">';
      $lRet.= '<a href="javascript:Flow.Std.modalClose(\''.htm($lVal).'\')" class="db">';
      $lRet.= htm($lVal);
      $lRet.= '</a></td>';
      $lRet.= '</tr>'-LF;
      $lCls = ('td1' == $lCls) ? 'td2' : 'td1';
    }
    $lRet.= '</table>';
    return $lRet;
  }

  protected function getPickList() {
    // asort($this -> mHtb);
    $lRet = '';
    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w100p">'.LF;
    $lRet.= '<tr><td colspan="'.$this -> mColCount.'" class="th1">Picker</td></tr>'.LF;
    $lCnt = 1;
    foreach ($this -> mHtb as $lCtr => $lNam) {
      if ($lCnt == 1) {
        $lRet.= '<tr>'.LF;
      }
      $lRet.= $this -> getCtr($lCtr, $lNam);
      $lCnt++;
      if ($lCnt > $this -> mColCount) {
        $lCnt = 1;
        $lRet.= '</tr>'.LF;
      }
    }
    if ($lCnt != 1) {
      $lRet.= '<td colspan="'.($this -> mColCount + 1 - $lCnt).'" class="td2">&nbsp;</td>';
      $lRet.= '</tr>';
    }
    $lRet.= '</table>';
    return $lRet;
  }

  protected function getDetailedList() {
    asort($this -> mHtb);
    $lRet = '';
    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w100p">'.LF;
    $lRet.= '<tr><td class="th1 w50 ac">Code</td><td class="th1 w200">'.htm(lan('lib.description')).'</td></tr>';
    foreach ($this -> mHtb as $lCtr => $lNam) {
      $lRet.= '<tr>';
      $lRet.= $this -> getCtr($lCtr);
      $lId = getNum('d');
      $lCls = (isset($this -> mSelArr[$lCtr])) ? 'pckAct' : 'pckNrm';
      $lRet.= '<td id="'.$lId.'" class="'.$lCls.' ctr'.$lCtr.'" onclick="Flow.Std.pckCtr(\''.$lId.'\',\''.$lCtr.'\')">';
      $lRet.= htm($lNam);
      $lRet.= '</td>';
      $lRet.= '</tr>';
    }
    $lRet.= '</table>';
    return $lRet;
  }

  protected function getCont() {
    $this -> mHtb = $this -> getHtbArray();
    uksort($this -> mHtb, 'strcasecmp');
    $lpn = 'cnt';
    $lUsr = CCor_Usr::getInstance();
    $lRet = '';

    $lRet.= $this -> getJs();

    $lRet.= '<div class="tbl">';
    $lRet.= '<div class="th1">Result</div>';
    $lRet.= '<div class="frm p8">';
    $lRet.= '<input type="text" id="lRes" readonly class="inp w250" value="'.htm($this -> mSelStr).'" />'.NB.LF;
    $lRet.= btn(lan('lib.ok'), 'Flow.Std.pckOkay()', 'img/ico/16/ok.gif').LF;
    $lRet.= '</div>';
    $lRet.= '</div>'.BR.LF;

    $lTab = new CHtm_Tabs('pck');
    $lTab -> addTab('cho', lan('lib.choices'), "javascript:Flow.Std.pagSel('cho')");
    $lTab -> addTab('pck', 'Picker', "javascript:Flow.Std.pagSel('pck')");
    $lTab -> addTab('lon', 'Detail', "javascript:Flow.Std.pagSel('lon')");

    $lRet.= $lTab -> getContent();

    $lRet.= '<div style="display:none" id="pagcho">'.LF;
    #if (empty($this -> mLis)) return '';

    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w100p">'.LF;
    $lRet.= '<tr><td class="th1" colspan=2>'.htm(lan('lib.choices')).'</td></tr>'.LF;
    $lSql = 'SELECT DISTINCT(val),id FROM al_fie_choice WHERE alias="'.addslashes($this -> mLis).'" ';
    $lSql.= 'ORDER BY val';
    $lQry = new CCor_Qry($lSql);

    $lCls = 'td1';
    foreach ($lQry as $lRow) {
      $lVal = $lRow['val'];
      $lRet.= '<tr><td class="'.$lCls.' nw">';
      $lRet.= '<a href="javascript:Flow.Std.modalClose(\''.htm($lVal).'\')" class="db">';
      $lRet.= htm($lVal);
      $lRet.= '</a></td>';

      if ($lUsr -> canRead($lpn)) {
        $lRet.= '<td class="td1 nw w16 ac">
                  <a href="javascript:Flow.Std.cnfDel(\'index.php?act=utl-ctr.del&amp;id='.$lRow['id'].'&amp;dom=ctr&amp;lis=laendervariante\', \''.LAN.'\')" class="nav">'.
                  img('img/ico/16/del.gif').'</a></td>';
      }

      $lRet.= '</tr>';
      $lCls = ('td1' == $lCls) ? 'td2' : 'td1';
    }
    $lRet.= '</table>';
    $lRet.= '</div>'.LF;

    $lRet.= '<div style="display:block" id="pagpck">'.LF;
    $lRet.= $this -> getPickList();
    $lRet.= '</div>'.LF;

    $lRet.= '<div style="display:none" id="paglon">'.LF;
    $lRet.= $this -> getDetailedList();
    $lRet.= '</div>'.LF;

    return $lRet;
  }

  protected function getCtr($aCtr, $aName = '') {
    $lId = getNum('d');
    $lCls = (isset($this -> mSelArr[$aCtr])) ? 'pckAct' : 'pckNrm';
    $lRet = '<td id="'.$lId.'" class="'.$lCls.' ac" data-val="'.htm($aCtr).'" onclick="Flow.Std.pckCtr(\''.$lId.'\',\''.$aCtr.'\')" ';
    if (!empty($aName)) {
      $lAttr = 'return overlib(\''.htm($aName).'\',BELOW,LEFT)';
      $lRet.= 'onmouseover="'.$lAttr.'" onmouseout="return nd();"';
    }
    $lRet.= '>';

    $lRet.= htm($aCtr);
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getJs() {
    $lRet = '<script type="text/javascript">'.LF;
    $lRet.= 'var gCtr = new Array();'.LF;
    if (!empty($this -> mSelArr)) {
      foreach ($this -> mSelArr as $lKey => $lVal) {
        $lRet.= 'gCtr.push("'.$lKey.'");'.LF;
      }
    }
    $lRet.= '</script>'.LF;
    return $lRet;
  }

}