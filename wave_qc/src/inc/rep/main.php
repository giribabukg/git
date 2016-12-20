<?php
class CInc_Rep_Main extends CCor_Tpl {
  
  protected function onBeforeContent() {
    $this -> mUsr = CCor_Usr::getInstance();
    $this -> mMod = 'rep';
    $this -> mSrc = $this -> mUsr -> getPref($this -> mMod.'.src', 'apldays');

    $this -> openProjectFile('rep/page.htm');
    
    $lSql = "SELECT name from al_reports WHERE code=".esc($this -> mSrc);
    $lTitle = CCor_Qry::getStr($lSql);
    $this -> setPat('rep.title', $lTitle);
    
    $lVie = new CRep_Tree($this -> mSrc);
    $this -> setPat('rep.tree', $lVie -> getContent());
    
    $this -> setPat('rep.search.opt', $this -> getOptions());
    $this -> setPat('rep.search.form', $this -> getSearchForm());
    $this -> setPat('rep.search.filter', $this -> getFilterForm());

    $lChart = new CRep_Chart($this -> mSrc);
    $this -> setPat('rep.js', $lChart -> getContent());
  }
  
  protected function getOptions() {
    $lMen = new CHtm_Menu(lan('lib.opt'), "w50 fr");
    $lMen -> addTh2(lan('lib.opt.view'));
    $lMen -> addItem('index.php?act='.$this -> mMod.'.spr', lan('lib.opt.spr'), 'ico/16/search.gif');
    
    $lOpt = '<table cellspacing="0" cellpadding="2" border="0">';
    $lOpt.= '<tr>';
    $lOpt.= '<td>';
    $lOpt.= $lMen->getContent();
    $lOpt.= '</td><td>';
    $lOpt.= $this -> getExportButton();
    $lOpt.= '</td></tr></table>';
    
    return $lOpt;
  }
  
  protected function getSearchForm(){
    $this -> mSer = $this -> mUsr -> getPref($this -> mMod.'.ser', '');
    if(is_string($this -> mSer)){
      $this -> mSer = unserialize($this -> mSer);
    }
    $lUsr = CCor_Usr::getInstance();
    $this -> mDefs = CCor_Res::get('fie');
    $lFac = new CHtm_Fie_Fac();
    $lSerFie = $this -> mUsr -> getPref('rep.sfie');
    if(empty($lSerFie)) return '';
    
    $lRet = '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mMod.'.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lRet.= '<td class="caption w50 p0">Search Report</td>';
    $lRet.= '<td>';
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    
    $lCnt = 0;
    $lFie = explode(',', $lSerFie);
    foreach ($lFie as $lFid) {
      if (isset($this -> mDefs[$lFid])) {
        if ($lCnt > 2) {
          $lRet.= '</tr><tr>';
          $lCnt = 0;
        }
        $lDef = $this -> mDefs[$lFid];
        if (isset($lDef['NoChoice']) && !empty($lDef['NoChoice'])){
          unset($lDef['NoChoice']);
        }
        $lNam = $lDef['name_'.LAN];
        $lAli = $lDef['alias'];
        $lRet.= '<td>'.htm($lNam).'</td>'.LF;
        $lVal = (isset($this -> mSer[$lAli])) ? $this -> mSer[$lAli] : '';
        $lRet.= '<td>';
        $lRet.= $lFac -> getInput($lDef, $lVal, fsSearch);
        $lRet.= '</td>';

        $lCnt++;
      }
    }

    $lRet.= '</tr></table></td>';
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td >'.btn(lan('lib.show_all'),'go("index.php?act='.$this -> mMod.'.clser")','img/ico/16/cancel.gif').'</td>';
    }

    $lRet.= '</tr></table>'.LF;
    $lRet.= '</form>';
	
	return $lRet;
  }
  
  protected function getFilterForm() {
    $this -> mFil = $this -> mUsr -> getPref($this -> mMod.'.fil', '');
    if(is_string($this -> mFil)){
      $this -> mFil = unserialize($this -> mFil);
    }
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mMod.'.fil" />'.LF;

    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lRet.= '<td class="caption w50 p0">Filter</td>';
    $lRet.= '<td>';
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
	
    $lArr = array("" => "", "day" => "Daily", "week" => "Weekly", "month" => "Monthly");
    $lAtrr = array("name" => "val[period]");
    $lRet.= '<td>'.htm("Period").'</td>'.LF;
    $lVal = (isset($this -> mFil["period"])) ? $this -> mFil["period"] : '';
    $lRet.= '<td>'.getSelect("period", $lArr, $lVal, $lAtrr).'</td>'.LF;
    
    $lRet.= '<td>'.htm("From").'</td>'.LF;
    $lVal = (isset($this -> mFil["from"])) ? $this -> mFil["from"] : '';
    $lRet .= '<td><input type="text" readonly="readonly" value="'.$lVal.'" class="inp w70 field_from datepicker" name="val[from]"></td>';
    
    $lRet.= '<td>'.htm("To").'</td>'.LF;
    $lVal = (isset($this -> mFil["to"])) ? $this -> mFil["to"] : '';
    $lRet .= '<td><input type="text" readonly="readonly" value="'.$lVal.'" class="inp w70 field_from datepicker" name="val[to]"></td>';
    
    $lRet.= '</tr></table></td>';
    $lRet.= '<td>'.btn(lan('lib.filter'),'','','submit').'</td>';
    if (!empty($this -> mFil)) {
      $lRet.= '<td >'.btn(lan('lib.show_all'),'go("index.php?act='.$this -> mMod.'.clfil")','img/ico/16/cancel.gif').'</td>';
    }

    $lRet.= '</tr></table>'.LF;
    $lRet.= '</form>';

    return $lRet;
  }
  
  protected function getExportButton() {
    $lResCsv = 'alert("csv export feature coming soon!")';#go("index.php?act='.$this -> mMod.'.excel&src='.$this -> mSrc.'&key=excel.'.$this -> mSrc.'")';
    
    return btn(lan('xls-exp'), $lResCsv, 'img/ico/16/excel.gif');
  }
}