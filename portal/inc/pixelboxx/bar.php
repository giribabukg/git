<?php
class CInc_Pixelboxx_Bar extends CCor_Ren {
  
  public function __construct() {
    
  }
  
  public function setPrefsFrom($aPrefix) {
    $this->setPrefs(CPixelboxx_Utils::getPrefsFrom($aPrefix));
  }
  
  public function setPrefs($aPreferences) {
    $this->mPref = $aPreferences;
  }
  
  public function setPref($aKey, $aValue) {
    $this->mPref[$aKey] = $aValue;
  }
  
  public function getPref($aKey, $aDefault = null) {
    return isset($this->mPref[$aKey]) ? $this->mPref[$aKey] : $aDefault;
  }
  
  protected function getCont() {
    $lRet = '';
    $lRet.= '<div class="pbox-bar p16">';
    $lRet.= $this->getMenu();
    $lRet.= $this->getSearchform();
    $lRet.= '</div>';
    return $lRet;
  }
  
  protected function getMenu() {
    $lCap = img('ico/16/opt.gif').' '.htm(lan('lib.opt'));
    $lMen = new CHtm_Menu($lCap, '', false);
    $lMen -> addTh2(lan('lib.opt.itemsperpage'));
    $lOk = '<i class="ico-w16 ico-w16-ok"></i>';
    $lLink = 'index.php?act=pixelboxx.setpref&pref=';
    $lPrefLpp = $this->getPref('lpp', 25);
    $lArr = array(10,25,50,100);
    foreach ($lArr as $lLpp) {
      $lImg = ($lLpp == $lPrefLpp) ? $lOk : '';
      $lMen -> addItem($lLink.'lpp&val='.$lLpp, $lLpp.' '.lan('lib.opt.items'), $lImg);
    }
    
    // other preferences
    $lMen -> addTh2(lan('hom.pref'));
    $lMen -> addItem('index.php?act=pixelboxx.spr', lan('lib.opt.spr'), '<i class="ico-w16 ico-w16-search"></i>');
    
    // auto collapse the tree toggle
    $lPrefKey = 'collapse';
    $lPref = $this->getPref($lPrefKey, 1);
    $lOk  = '<i id="pref-collapse-img" class="ico-w16 ico-w16-ok"></i>';
    $lNok = '<i id="pref-collapse-img" class="ico-w16 ico-w16-ok" style="display:none"></i>';
    $lImg = (1 == $lPref) ? $lOk : $lNok;
    //$lMen->addItem($lTogLink.$lPrefKey, 'Auto-Collapse', $lImg);
    $lMen->addJsItem('Flow.Pboxx.toggleAutoCollapse()', 'Auto-Collapse', $lImg);
    
    // view style grid/cards/list
    $lMen -> addTh2(lan('lib.opt.viewstyle'));
    
    $lPrefKey = 'view';
    $lPref = $this->getPref($lPrefKey, 'grid');
    
    $lImg = ('grid' == $lPref) ? $lOk : '';
    $lMen -> addItem($lLink.'view&val=grid', 'Grid View', $lImg);
    
    $lImg = ('grid' != $lPref) ? $lOk : '';
    $lMen -> addItem($lLink.'view&val=list', 'List View', $lImg);
    
    return $lMen->getContent();
  }
  
  protected function getSearch2Form() {
    $lSel = $this->getPref('sfie');
    if (empty($lSel)) {
      return '';
    }
    $lRet = '';
    $lRet.= '<form class="bc-search-form">';
    $lAll = CPixelboxx_Utils::getMetaFields();
    $lSel = explode(',', $lSel);
    foreach ($lSel as $lAlias) {
      if (!in_array($lAlias, $lAll)) {
        continue;
      }
      $lRet.= $lAlias.' , ';
    }
    $lRet.= '</form>';
    return $lRet;
  }
  
  protected function getSearchForm() {
    //var_dump($this->mPref);
    $lSpr = explode(',', $this -> getPref('sfie'));
    if (empty($lSpr)) {
      return '';
    }
    $lSearch = $this->getPref('ser');
    if (is_string($lSearch)) {
      $lSearch = unserialize($lSearch);
    }
    $lRet = '';
    $lRet.= '<div style="border-top:1px solid #ccc;">';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="pixelboxx.ser" />'.LF;
  
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lRet.= '<td class="caption w50">'.htm(lan('job-ser.menu')).'</td>';
    $lRet.= '<td>';
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
  
    $lSpr = explode(',', $this -> getPref('sfie'));
    $lFac = new CHtm_Fie_Fac();
    $lDefs = CCor_Res::getByKey('alias', 'fie');
  
    $lCnt = 0;
    foreach ($lSpr as $lAlias) {
      if (isset($lDefs[$lAlias])) {
        if ($lCnt > 2) {
          $lRet.= '</tr><tr>';
          $lCnt = 0;
        }
        $lDef = $lDefs[$lAlias];
        // Bei abhaengigen Auftragsfeldern wird standard Wert mit Variable 'NoChoice' definiert
        // was aber in der Suche nicht noetig ist.
        if (isset($lDef['NoChoice']) && !empty($lDef['NoChoice'])){
          unset($lDef['NoChoice']);
        }
        $lNam = $lDef['name_'.LAN];
        $lRet.= '<td>'.htm($lNam).'</td>'.LF;
        $lVal = (isset($lSearch[$lAlias])) ? $lSearch[$lAlias] : '';
        if ('[empty]' == $lVal) {
          $lVal = '';
        }
        $lRet.= '<td>';
        $lRet.= $lFac -> getInput($lDef, $lVal, fsSearch);
        $lRet.= '</td>';
  
        $lCnt++;
      }
    }
    $lRet.= '</tr></table></td>';
    $lRet.= '<td valign="top">'.btn(lan('lib.search'),'','<i class="ico-w16 ico-w16-search"></i>','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td valign="top">'.btn(lan('lib.show_all'),'go("index.php?act='.$this -> mMod.'.clser")','<i class="ico-w16 ico-w16-cancel"></i>').'</td>';
    }
  
    $lRet.= '</tr></table>'.LF;
    $lRet.= '</form>';
    $lRet.= '</div>';
  
    return $lRet;
  }

}