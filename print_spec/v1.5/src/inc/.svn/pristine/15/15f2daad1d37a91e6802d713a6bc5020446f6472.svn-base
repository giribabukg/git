<?php
class CInc_Htm_Panel extends CHtm_Tag {

  public function __construct($aCaption, $aCont = '', $aPersist = NULL) {
    parent::__construct('div');
    $this -> setAtt('class', 'th1');
    $this -> setAtt('style', 'padding:4px');
    $this -> mCaption = $aCaption;
    $this -> mCnt     = $aCont;
    $this -> mPersist = $aPersist;
    $this -> mCollapsed = FALSE;
    if (!empty($this -> mPersist)) {
      $lUsr = CCor_Usr::getInstance();
      $lVal = $lUsr -> getPref('cpl.'.$aPersist);
      $this -> mCollapsed = (bool)$lVal;
    }
    $this -> mDivId   = getNum('d');
    $this -> mImgId   = getNum('i');
    $this -> mLnkId   = getNum('a');

    $this -> mDiv = new CHtm_Tag('div');
    $this -> mDiv -> setAtt('class', 'tbl');
  }

  public function setDivAtt($aKey, $aVal) {
    $this -> mDiv -> setAtt($aKey, $aVal);
  }

  public function getHead($aAtt = array()) {
    $lRet = $this -> mDiv -> getTag();
    $lRet.= $this -> getTag();
    $lLnk = new CHtm_Tag('a');
    $lLnk -> addAtt('id', $this -> mLnkId);
    $lLnk -> addAtt('class', 'db');
    if ($this -> mPersist) {
      $lLnk -> addAtt('href', "javascript:Flow.Std.togCpl('$this->mDivId','$this->mImgId','$this->mLnkId','cpl.$this->mPersist')");
    } else {
      $lLnk -> addAtt('href', "javascript:Flow.Std.togCpl('$this->mDivId','$this->mImgId','$this->mLnkId')");
    }
    $lRet.= $lLnk -> getTag();
    $lImg = ($this -> mCollapsed) ? 'expand' : 'collapse';
    $lRet.= "<i class='ico-w16 ico-w16-cpl-".$lImg."' id='".$this->mImgId."' style='float:left;' title='".lan("lib.".$lImg)."'></i>&nbsp;";
    $lRet.= htm($this -> mCaption);
    $lRet.= '</a>';
    $lRet.= '</div>';

    $lDiv = new CHtm_Tag('div');
    $lDiv -> addAtt('id', $this -> mDivId);
    if ($this -> mCollapsed){
      $lDiv -> addAtt('style', 'display:none');
    }
    if (!empty($aAtt)) {
      $lDiv -> addAttributes($aAtt);
    }
    $lRet.= $lDiv -> getTag();

    return $lRet;
  }

  protected function getCont() {
    $lRet = $this -> getHead();
    if (is_string($this -> mCnt)) {
      $lRet.= $this -> mCnt;
    } else {
      $lRet.= $this -> mCnt -> getContent();
    }
    $lRet.= '</div>'.LF;
    $lRet.= '</div>'.LF;
    return $lRet;
  }
}