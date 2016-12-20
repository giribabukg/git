<?php
class CInc_Htm_Vmenu extends CCor_Ren {

  public function __construct($aCaption = '') {
    $this -> mKey     = '';
    $this -> mSubKey  = '';

    $this -> mItems   = array();
    $this -> mSub     = array();
    $this -> mCaption = $aCaption;
  }

  public function setKey($aKey) {
    $this -> mKey = $aKey;
  }

  public function setSubKey($aKey) {
    $this -> mSubKey = $aKey;
  }

  /**
   * Add menu item
   *
   * @param string $aKey        Unique identifier (used for highlighting active tab)
   * @param string $aCaption    Caption of Tab
   * @param string $aUrl        Url or javascript to execute when tab is clicked
   * @param string $aImg        Optional Icon to display left of the caption
   **/

  public function addItem($aKey, $aUrl, $aCaption, $aTargetNewPage = FALSE) {
    $lItm = array();
    $lItm['key']  = $aKey;
    $lItm['cap']  = $aCaption;
    $lItm['url']  = $aUrl;
    $lItm['target'] = $aTargetNewPage;

    $this -> mItems[$aKey] = $lItm;
  }

  public function addSubItem($aParent, $aKey, $aUrl, $aCaption) {
    $lItm = array();
    $lItm['key']  = $aKey;
    $lItm['cap']  = $aCaption;
    $lItm['url']  = $aUrl;

    $this -> mSub[$aParent][$aKey] = $lItm;
  }

  public function addPanel($aCont) {
    $lKey = getNum('vm');
    $this -> addItem($lKey, 'pnl', $aCont);
  }

  public function addSpacer() {
    $lKey = getNum('vm');
    $this -> addItem($lKey, 'spa', '');
  }

  function getItem($aItm) {
    $lRet = '';
    $lCls = 'lo';
    $lKey = $aItm['key'];
    if ($lKey == $this -> mKey) {
      $lCls = 'hi';
    }
    if ($aItm['url'] == '#') {
      $lRet.= '<span class="db cp '.$lCls.'" onclick="Flow.tog(\'vmenu-'.$lKey.'\')">';
      $lRet.= htm($aItm['cap']);
      if (!empty($this -> mSub[$lKey])) {
        $lRet.= ' ...';
      }
      $lRet.= '</span>'.LF;
    } else {
    	if($aItm["target"]) {
    		$lRet.= '<a href="'.$aItm['url'].'" target="_blank" class="db '.$lCls.'">';
    	} else {
    		$lRet.= '<a href="'.$aItm['url'].'" class="db '.$lCls.'">';
    	}      
      $lRet.= htm($aItm['cap']);
      $lRet.= '</a>'.LF;
    }
    if (!empty($this -> mSub[$lKey])) {
      $lHide = true;
      foreach ($this -> mSub[$lKey] as $lItm) {
        if ($lItm['key'] == $this->mSubKey) {
          $lHide = false;
        }
      }
      foreach ($this -> mSub[$lKey] as $lItm) {
        $lRet.= $this -> getSubItem($lKey, $lItm, $lHide);
      }
    }
    return $lRet;
  }

  function getSubItem($aParentKey, $aItm, $aHide = true) {
    $lRet = '';
    $lCls = 'lo';
    $lKey = $aItm['key'];
    if ($lKey == $this -> mSubKey) {
      $lCls = 'hi';
    }
    $lCls.= ($aHide) ? ' dn' : ' db';
    $lRet.= '<a href="'.$aItm['url'].'" class="vsub vmenu-'.$aParentKey.' '.$lCls.'">';
    $lRet.= htm($aItm['cap']);
    $lRet.= '</a>'.LF;
    return $lRet;
  }

  protected function getSpacer() {
    $lRet = '<div class="db spa">&nbsp;</div>';
    return $lRet;
  }

  protected function getCont() {
    $lRet = $this -> getComment('start');
    $lRet.= '<div class="vmenu">'.LF;
    if (!empty($this -> mCaption)) {
      $lRet.= '<div class="cap">'.htm($this -> mCaption).'</div>';
    }
    foreach ($this -> mItems as $lItm) {
      $lUrl = $lItm['url'];
      if ('pnl' == $lUrl) {
        $lRet.= '<div class="lo">';
        $lRet.= $lItm['cap'];
        $lRet.= '</div>';
      } else if ('spa' == $lUrl) {
        $lRet.= $this -> getSpacer();
      } else {
        $lRet.= $this -> getItem($lItm);
      }
    }
    $lRet.= '</div>'.LF;
    $lRet.= $this -> getComment('end');
    return $lRet;
  }
}