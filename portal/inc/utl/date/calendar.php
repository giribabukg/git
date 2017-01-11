<?php
class CInc_Utl_Date_Calendar extends CCor_Ren {
  
  protected $mTabs;
  protected $mActive;
  
  public function __construct($aMod, $aDate = NULL, $aDate2 = NULL) {
    $this -> mMod = $aMod;
    $lDat = (NULL == $aDate) ? date('Y-m-d') : substr($aDate, 0, 10);
    $this -> mDate = $lDat;
    $lMonth = intval(substr($lDat,5,2));
    $lYear  = intval(substr($lDat,0,4));
    $lTim = mktime(0,0,0, $lMonth, 1, $lYear);
    $this -> mQuart = date('Y-m-d', $lTim);
    $this -> mDate = $lDat;
    
    $lUsr = CCor_Usr::getInstance();
    $this -> mActive = $lUsr -> getPref('sys.cal', 'mon');
  }
  
  protected function getCont() {
    $lRet = '';
    $lRet.= '<table cellpadding="0" cellspacing="0" border="0"><tr>'.LF;
    $lRet.= '<td valign="top">'.LF;
    $lDat = new CCor_Date($this -> mQuart);
    $lPrv = $lDat -> getFirstOfMonthPlus(-1);
    $lCal = new CHtm_Cal($this -> mMod, $lPrv -> getSql());
    $lCal -> setPickMode();
    $lCal -> setNav(0, +2);
    $lRet.= $lCal ->  getContent();
    $lRet.= '</td>';
    $lRet.= '<td class="w16"><img src="img/d.gif" width="16" alt="" /></td>';
    $lRet.= '<td valign="top">'.LF;
    $lCal = new CHtm_Cal($this -> mMod, $this -> mQuart);
    $lCal -> setNav(-1, +1);
    $lCal -> setPickMode();
    $lRet.= $lCal ->  getContent();
    $lRet.= '</td>';
    $lRet.= '<td class="w16"><img src="img/d.gif" width="16" alt="" /></td>';
    $lRet.= '<td valign="top">'.LF;
    $lNex = $lDat -> getFirstOfMonthPlus(+1);
    $lCal = new CHtm_Cal($this -> mMod, $lNex -> getSql());
    $lCal -> setPickMode();
    $lCal -> setNav(-2, 0);
    $lRet.= $lCal ->  getContent();
    $lRet.= '</td>';
    $lRet.= '</tr></table>'.LF;
    
    return $lRet;
  }
  
}