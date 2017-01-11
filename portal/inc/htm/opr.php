<?php
class CInc_Htm_Opr extends CCor_Ren {

  protected $mAct;    // Controller action to set if ok is pressed
  protected $mCancel; // URL for cancel button. Leave empty if no cancel button should be available
  protected $mTitle;
  protected $mPar = array();
  protected $mDescriptions = array();

  public function __construct($aAct, $aCancel = NULL) {
    $this -> mAct = $aAct;
    $this -> mSrc = array();

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
    $this -> mTitle       = lan('lib.opt.opr');
    $this -> mSrcCaption  = lan('lib.opt.opr.available');

    $this -> mShowUpDown = TRUE;
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
    $lRet = '<div class="tbl" style="width:800px;">'.LF;
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="'.$this -> mAct.'" />'.LF;
    $lRet.= '<div class="th1">'.htm($this -> mTitle).'</div>'.LF;
    return $lRet;
  }

  public function getSelection() {
    $lRet = '<div class="frm">'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0" width="100%">'.LF;
    $lRet.= '<tr>'.LF;

    $lRet.= '<td class="w640 p16">'.LF;
    $lRet.= $this -> getSrcPanel();
    $lRet.= '</td>'.LF;

    $lRet.= '<td class="w120 p0">'.LF;

    $lRet.= btn(lan('lib.move_up'), 'Flow.oprUp("src")', 'img/ico/16/nav-up-lo.gif', 'button', array('class' => 'btn w100')).BR.BR;
    $lRet.= btn(lan('lib.move_down'), 'Flow.oprDown("src")', 'img/ico/16/nav-down-lo.gif', 'button', array('class' => 'btn w100')).BR.BR;

    $lRet.= '</td>'.LF;

    $lRet.= '</tr>'.LF;
    $lRet.= '</table>'.LF;
    $lRet.= '</div>'.LF;
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
    $lRet.= '<select name="src[]" id="src" size="20" class="inp w100p" multiple="multiple">'.LF;
    if (!empty($this -> mSrc))
    foreach ($this -> mSrc as $lKey => $lVal) {
      $lRet.= '<option value="'.htm($lKey).'">';
      $lRet.= htm($lVal);
      $lRet.= '</option>'.LF;
    }
    $lRet.= '</select>'.LF;
    return $lRet;
  }

  protected function getButtons() {
    $lRet = '<div class="btnPnl">'.LF;
    $lRet.= btn(lan('lib.ok'), 'Flow.oprAll("src")', 'img/ico/16/ok.gif', 'submit', array('class' => 'btn w100')).NB;
    if (!empty($this -> mCancel)) {
      $lRet.= btn(lan('lib.cancel'), "go('index.php?act=".$this -> mCancel."')", 'img/ico/16/cancel.gif', 'button', array('class' => 'btn w100'));
    }
    $lRet.= '</div>'.LF;
    return $lRet;
  }

  public function getTooltips() {
    $lReturn = '';
    $lReturn.= 'jQuery(function(){';
    $lReturn.= '  jQuery("#src").click(function(){ Flow.oprSrcTip(); });';
    $lReturn.= '  jQuery("#src").mouseout(function(){ Flow.hideTip(); });';
    $lReturn.= '});';
    return $lReturn;
  }
}