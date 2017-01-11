<?php
class CInc_Htm_Fpr extends CCor_Ren {

  protected $mAct;    // Controller action to set if ok is pressed
  protected $mCancel; // URL for cancel button. Leave empty if no cancel button should be available
  protected $mTitle;
  protected $mPar = array();
  protected $mDescriptions = array();

  public function __construct($aAct, $aCancel = NULL, $aMaxDest = 0) {
    $this -> mAct = $aAct;
    $this -> mSrc = array();
    $this -> mDst = array();
    $this -> mMaxDest = $aMaxDest;

    if (NULL === $aCancel) {
      $lPos = strpos($aAct, '.');
      if (FALSE !== $lPos) {
        $this -> mCancel = substr($aAct, 0 , $lPos);
      } else {
        $this -> mCancel = '';
      }
    } else {
      $this -> mCancel = $aCancel;
    }
    $this -> mTitle       = lan('lib.opt.fpr');
    $this -> mSrcCaption  = lan('lib.opt.fpr.available');
    $this -> mDestCaption = lan('lib.opt.fpr.selected');

    $this->mShowUpDown = TRUE;
  }

  public function setSrc($aArr) {
    $this -> mSrc = $aArr;
  }

  public function setTitle($aTxt) {
    $this -> mTitle = $aTxt;
  }

  public function setParam($aKey, $aValue) {
    $this -> mPar[$aKey] = $aValue;
  }

  public function getParam($aKey) {
    if (isset($this -> mPar[$aKey])) {
      return $this -> mPar[$aKey];
    } else {
      return NULL;
    }
  }

  public function setSel($aArr) {
    if (empty($aArr)) {
      $this -> mDst = array();
      return;
    }
    if (is_string($aArr)) {
      $aArr = explode(',', $aArr);
    }
    $this -> mDst = $aArr;
  }

  protected function getHead() {
    $lRet = '<div class="tbl" style="width:740px;">'.LF;
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mAct.'" />'.LF;
    $lRet.= '<div class="th1">'.htm($this -> mTitle).'</div>'.LF;
    return $lRet;
  }

  public function getSelection() {
    $lRet = '<div class="frm">'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0" width="100%">';
    $lRet.= '<tr>'.LF;

    $lRet.= '<td class="w200 p16">'.LF;
    $lRet.= $this -> getSrcPanel();
    $lRet.= '</td>'.LF;

    $lRet.= '<td class="w100 p16">'.LF;
    if($this->mMaxDest != 0) {
      $lRet.= btn(lan('lib.add'), 'Flow.Std.fprSel(\'src\',\'dst\','.$this->mMaxDest.')', '<i class="ico-w16 ico-w16-nav-next-lo"></i>', 'button', array('class' => 'btn w100')).BR.BR;
    }
    else {
      $lRet.= btn(lan('lib.add'), 'Flow.Std.fprSel(\'src\',\'dst\')', '<i class="ico-w16 ico-w16-nav-next-lo"></i>', 'button', array('class' => 'btn w100')).BR.BR;
    }
    $lRet.= btn(lan('lib.remove'), 'Flow.Std.fprSel(\'dst\',\'src\')', '<i class="ico-w16 ico-w16-nav-prev-lo"></i>', 'button', array('class' => 'btn w100')).BR.BR;
    $lRet.= BR;

    if ($this->mShowUpDown) {
      $lRet.= btn(lan('lib.move_up'), 'Flow.Std.fprUp(\'dst\')', '<i class="ico-w16 ico-w16-nav-up-lo"></i>', 'button', array('class' => 'btn w100')).BR.BR;
      $lRet.= btn(lan('lib.move_down'), 'Flow.Std.fprDn(\'dst\')', '<i class="ico-w16 ico-w16-nav-down-lo"></i>', 'button', array('class' => 'btn w100')).BR.BR;
    }

    $lRet.= '</td>'.LF;

    $lRet.= '<td  class="w200 p16">'.LF;
    $lRet.= $this -> getDstPanel();
    $lRet.= '</td>'.LF;

    $lRet.= '</tr>'.LF;
    $lRet.= '</table>';
    $lRet.= '</div>';
    return $lRet;
  }

  protected function getFoot() {
    $lRet = '</form>'.LF;
    $lRet.= '</div>'.LF;
    return $lRet;
  }

  protected function getBeforeSelection() {
    return '';
  }

  protected function getAfterSelection() {
    return '';
  }

  protected function getHiddenFields() {
    if (empty($this -> mPar)) {
      return;
    }
    $lRet = '';
    foreach ($this -> mPar as $lKey => $lVal) {
      $lRet.= '<input type="hidden" name="'.$lKey.'" value="'.htm($lVal).'" />'.LF;
    }
    return $lRet;
  }

  protected function getCont() {
    $lRet = $this -> getComment('start');
    $lRet.= $this -> getHead();
    $lRet.= $this -> getHiddenFields();

    $lRet.= $this -> getBeforeSelection();
    $lRet.= $this -> getSelection();
    $lRet.= $this -> getAfterSelection();
    $lRet.= $this -> getButtons();
    $lRet.= $this -> getFoot();

    $lRet.= $this -> getComment('end');
    return $lRet;
  }

  protected function getSrcPanel() {
    $lRet = '';
    $lRet.= '<b>'.htm($this -> mSrcCaption).'</b>'.BR.LF;
    $lRet.= '<select name="src[]" id="src" size="20" class="inp w200" multiple="multiple">'.LF;
    if (!empty($this -> mSrc))
    foreach ($this -> mSrc as $lKey => $lVal) {
      if (in_array($lKey, $this -> mDst)) {
        continue;
      }
      $lRet.= '<option value="'.htm($lKey).'">';
      $lRet.= htm($lVal);
      $lRet.= '</option>'.LF;
    }
    $lRet.= '</select>'.LF;
    return $lRet;
  }

  protected function getDstPanel() {
    $lRet = '';
    $lRet.= '<b>'.htm($this-> mDestCaption).'</b>'.BR.LF;
    $lRet.= '<select name="dst[]" id="dst" size="20" class="inp w200" multiple="multiple">'.LF;
    if (!empty($this -> mDst))
    foreach ($this -> mDst as $lKey) {
      if (isset($this -> mSrc[$lKey])) {
        $lVal = $this -> mSrc[$lKey];
        $lRet.= '<option value="'.htm($lKey).'">';
        $lRet.= htm($lVal);
        $lRet.= '</option>'.LF;
      }
    }
    $lRet.= '</select>'.LF;
    return $lRet;
  }

  protected function getButtons() {
    $lRet = '<div class="btnPnl">'.LF;
    $lRet.= btn(lan('lib.ok'), 'Flow.Std.fprAll(\'dst\')', '<i class="ico-w16 ico-w16-ok"></i>', 'submit', array('class' => 'btn w100')).NB;
    if (!empty($this -> mCancel)) {
      $lRet.= btn(lan('lib.cancel'), "go('index.php?act=".$this -> mCancel."')", '<i class="ico-w16 ico-w16-cancel"></i>', 'button', array('class' => 'btn w100'));
    }
    $lRet.= '</div>'.LF;
    return $lRet;
  }

  public function getTooltips() {
    $lReturn = '';
    $lReturn.= 'jQuery(function(){';
    $lReturn.= '  jQuery("#src").click(function(){ Flow.fprSrcTip(); });';
    $lReturn.= '  jQuery("#dst").click(function(){ Flow.fprDstTip(); });';
    $lReturn.= '  jQuery("#src").mouseout(function(){ Flow.hideTip(); });';
    $lReturn.= '  jQuery("#dst").mouseout(function(){ Flow.hideTip(); });';
    $lReturn.= '});';
    return $lReturn;
  }
}