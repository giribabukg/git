<?php
class CInc_Htm_Tabs extends CCor_Ren {

  public function __construct($aActiveTab = '') {
    $this -> mTabs  = array();
    $this -> mLinks = array();
    $this -> mActiveTab = $aActiveTab;
    $this -> mAtt = array();

  }

  /**
   * Add a Tab
   *
   * @param string $aKey        Unique identifier (used for highlighting active tab)
   * @param string $aCaption    Caption of Tab
   * @param string $aUrl        Url or javascript to execute when tab is clicked
   * @param bool   $aEnabled    False, if tab should be deactivated
   */

  public function addTab($aKey, $aCaption, $aUrl, $aEnabled = TRUE) {
    $lTab = array();

    $lTab['key']     = $aKey;
    $lTab['typ']     = 'tab';
    $lTab['caption'] = $aCaption;
    $lTab['url']     = $aUrl;
    $lTab['enabled'] = $aEnabled;
    $lTab['hidden']  = FALSE;

    $this -> mTabs[$aKey] = $lTab;
  }

  /**
   * Remove a tab that has been added previously
   *
   * @param string $aKey Unique Key of the tab to remove
   */
  public function removeTab($aKey) {
    unset($this->mTabs[$aKey]);
  }

  public function setDisabled($aKey, $aFlag = TRUE) {
    if (isset($this -> mTabs[$aKey])) {
      $this -> mTabs[$aKey]['enabled'] = !$aFlag;
    }
  }

  public function addLink($aCaption, $aUrl) {
    $lLnk = array();
    $lLnk['cap'] = $aCaption;
    $lLnk['url'] = $aUrl;
    $this -> mLinks[] = $lLnk;
  }

  public function setHidden($aKey, $aVal = TRUE) {
    if (isset($this -> mTabs[$aKey])) {
      $this -> mTabs[$aKey]['hidden'] = $aVal;
    }
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<table cellpadding="0" cellspacing="0" border="0"><tr><td>'.LF;
    if (!empty($this -> mTabs)) {
      $lRet.= '<table cellpadding="1" cellspacing="0" border="0" class="tabTbl"><tr>'.LF;
      foreach ($this -> mTabs as $lKey => $lTab) {
        if ($lTab['hidden']) continue;
        if ($lTab['typ'] == 'btn') {
          $lRet.= '<td class="nw">';
          $lRet.= htm($lTab['caption']);
          $lRet.= '</td>';
        } else if ($lTab['enabled']) {
          $lCls = ($lKey == $this -> mActiveTab) ? 'tabAct' : 'tabNorm';
          $lRet.= "<td class=\"$lCls nw\" id=\"tab$lKey\" onmouseover=\"Flow.Std.tabHi(this,'$lKey')\" onmouseout=\"Flow.Std.tabLo(this,'$lKey')\">";
          $lRet.= "<a href=\"".$lTab['url']."\" class=\"tabLink\" onclick='this.blur()'>";
          $lRet.= htm($lTab['caption']);
          $lRet.= '</a>';
          $lRet.= '</td>'.LF;
        } else {
          $lRet.= '<td class="tabDis">';
          $lRet.= htm($lTab['caption']);
          $lRet.= '</td>';
        }
      }
      $lRet.= "</tr></table>\n";
    }
    $lRet.= '</td>';
    if (!empty($this -> mLinks)) {
      $lRet.= '<td>';
      $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>';
      foreach ($this -> mLinks as $lLnk) {
        #$lRet.= '<td>|</td>';
        $lRet.= '<td>';
        $lRet.= '<a href="'.$lLnk['url'].'" class="nav nw">';
        $lRet.= '&nbsp;';
        $lRet.= htm($lLnk['cap']);
        $lRet.= '&nbsp;';
        $lRet.= '</a>';
        $lRet.= '</td>';
      }
      $lRet.= '</tr></table></td>';
    }

    $lRet.= '</tr></table>';

    $lRet.= "<script type=\"text/javascript\">actTab='$this->mActiveTab';</script>";

    return $lRet;
  }
}