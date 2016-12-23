<?php
class CInc_Htm_NavBar extends CCor_Ren {

  protected $mMod;
  protected $mPag;
  protected $mLpp;
  protected $mMax = 0;
  protected $mJobId = '';

  public function __construct($aMod, $aPage, $aMax = 0, $aLpp = 20, $aJobId = '') {
    $this -> mMod = $aMod;
    $this -> mPag = $aPage;
    $this -> mMax = $aMax;
    $this -> mLpp = $aLpp;
    $this -> mJobId = $aJobId;

    $this -> mUrl  = 'index.php';
    $this -> setParam('act', $aMod.'.page');

    // if the List need to JobId for underJobs
    if ($this -> mJobId != ''){
      $this -> setParam('jobid', $this -> mJobId);
    }
  }

  public function setParam($aKey, $aValue) {
    // can be used to pass further request vars to next page request
    // will be used in links and as hidden vars in form
    $this -> mParam[$aKey] = $aValue;
  }

  protected function getMaxPages() {
    if (0 == $this -> mLpp)  {
      return 0;
    } else {
      return ceil($this -> mMax / $this -> mLpp);
    }
  }

  protected function getLink($aPage) {
    $lPar = $this -> mParam;
    $lPar['page'] = $aPage;


    $lRet = $this -> mUrl.'?';
    foreach ($lPar as $lKey => $lVal) {
      $lRet.= $lKey.'='.$lVal.'&';
    }
    $lRet = htm(substr($lRet, 0, -1));
    return $lRet;
  }

  protected function getForm() {
    $lRet = '';
    $lRet.= '<td>&nbsp;'.htm(lan('nav.page')).'&nbsp;</td>';
    $lArr = array();
    for ($i = 0; $i < $this -> mMaxPag; $i++) {
      $lArr[$i] = $i + 1;
    }
    $lSel = new CHtm_Select('page', $lArr, $this -> mPag);
    $lSel -> addAtt('onchange', 'this.form.submit()');
    $lRet.= '<td>';
    $lRet.= $lSel -> getContent();
    $lRet.= '</td>';
    $lRet.= '<td>&nbsp;'.htm(lan('nav.of')).'&nbsp;'.$this -> mMaxPag;
    #$lRet.= ' ('.$this -> mMax.' total)';
    $lRet.= '&nbsp;</td>';
    return $lRet;
  }

  protected function getBtn($aImg, $aLnk = NULL) {
    $lRet = '';
    $lRet.= '<td>';
    if (NULL === $aLnk) {
      //Switch for CSS-Sprite Icons
      if($aImg[0] === "<") {
        $lRet.= '<span class="p2">'.$aImg.'</span>';
      }
      else {
        $lRet.= '<span class="p2">'.img($aImg).'</span>';
      }
    } else {
      $lRet.= '<a href="'.$aLnk.'" class="nav">';
      //Switch for CSS-Sprite Icons
      if($aImg[0] === "<") {
        $lRet.= $aImg;
      }
      else {
        $lRet.= img($aImg);
      }
      $lRet.= '</a>';
    }
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getFirstBtn() {
    if (0 == $this -> mPag) {
      $lRet = $this -> getBtn('<i class="ico-w16 ico-w16-nav-first-dis"></i>');
    } else {
      $lRet = $this -> getBtn('<i class="ico-w16 ico-w16-nav-first-lo"></i>', $this -> getLink(0));
    }
    return $lRet;
  }

  protected function getPrevBtn() {
    if (0 == $this -> mPag) {
      $lRet = $this -> getBtn('<i class="ico-w16 ico-w16-nav-prev-dis"></i>');
    } else {
      $lRet = $this -> getBtn('<i class="ico-w16 ico-w16-nav-prev-lo"></i>', $this -> getLink($this -> mPag -1));
    }
    return $lRet;
  }

  protected function getNextBtn() {
    if ($this -> mPag >= $this -> mMaxPag -1) {
      $lRet = $this -> getBtn('<i class="ico-w16 ico-w16-nav-next-dis"></i>');
    } else {
      $lRet = $this -> getBtn('<i class="ico-w16 ico-w16-nav-next-lo"></i>', $this -> getLink($this -> mPag + 1));
    }
    return $lRet;
  }

  protected function getLastBtn() {
    if ($this -> mPag >= $this -> mMaxPag -1) {
      $lRet = $this -> getBtn('<i class="ico-w16 ico-w16-nav-last-dis"></i>');
    } else {
      $lRet = $this -> getBtn('<i class="ico-w16 ico-w16-nav-last-lo"></i>', $this -> getLink($this -> mMaxPag -1));
    }
    return $lRet;
  }

  protected function getCont() {
    $this -> mMaxPag = $this -> getMaxPages();

    if ($this -> mMaxPag < 2) {
      // nothing to navigate
      return '';
    }
    $lRet = '';
    $lRet.= '<form action="'.$this -> mUrl.'" method="post">'.LF;
    foreach($this -> mParam as $lKey => $lVal) {
      $lRet.= '<input type="hidden" name="'.$lKey.'" value="'.htm($lVal).'" />'.LF;
    }
    $lRet.= '<table cellpadding="0" cellspacing="0" border="0"><tr>'.LF;

    $lRet.= $this -> getFirstBtn();
    $lRet.= $this -> getPrevBtn();

    $lRet.= $this -> getForm();

    $lRet.= $this -> getNextBtn();
    $lRet.= $this -> getLastBtn();

    $lRet.= '</tr></table>'.LF;
    $lRet.= '</form>'.LF;
    return $lRet;
  }

}