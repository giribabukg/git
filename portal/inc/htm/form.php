<?php
class CInc_Htm_Form extends CHtm_Tag {

  protected $mAct;    // Controller action to set if ok is pressed
  protected $mCancel; // URL for cancel button. Leave empty if no cancel button should be available

  protected $mFie = array();
  protected $mPar = array();
  protected $mVal = array();
  protected $mFac;
  protected $mDescription = array();

  public function __construct($aAct, $aCaption, $aCancel = NULL) {
    parent::__construct('div');
    $this -> setAtt('class', 'tbl w400');
    $this -> mFrmId = getNum('f');
    $this -> mFormTag = '<form id="'.$this -> mFrmId.'" action="index.php" method="post" enctype="multipart/form-data">'.LF;
    $this -> setParam(sec_token, $_SESSION[sec_token]);

    $this -> mAct = $aAct;
    $this -> mCap = $aCaption;


    $this -> mReadOnly = fsStandard; // kann Editierfelder auf READONLY setzen
    $this -> mButtons = TRUE; // kann die Button-Zeile (Ok, Abbrechen) entfernen
    $this -> mAltLan = FALSE;

    $this -> setParam('act', $aAct);

    if (NULL === $aCancel) {
      $lPos = strpos($aAct, '.');
      if (FALSE !== $lPos) {
        $this -> mCancel = substr($aAct, 0, $lPos);
      } else {
        $this -> mCancel = '';
      }
    } else {
      $this -> mCancel = $aCancel;
    }

    $this -> getFac();
  }

  public function setAct($aAct) {
    $this->mAct = $aAct;
  }

  public function setCancel($aAct) {
    $this->mCancel = $aAct;
  }

  public function setReadOnly() {
    $this -> mReadOnly = fsDisabled;
  }

  public function setButtons($aBtn = TRUE) {
    $this -> mButtons = $aBtn;
  }

  public function setAltLan($aAltLan = FALSE) {
    $this -> mAltLan = $aAltLan;
  }

  public function setOld($aFlag = TRUE) {
    $this -> getFac();
    $this -> mFac-> mOld = $aFlag;
  }

  /*
   * in old wird sonst der gleiche Wert geschrieben wie im Val, um spaeter Unterschiede feststellen zu koennen
   * genutzt, wenn bereits ein neuer Wert im Formular angezeigt werden soll, der sich vom alten unterscheiden kann.
   * dieser Neue muss mittels update gespeichert werden, diese Aenderung kann in d. Historie
   */
  public function setDifferentOld($aKey, $aValue) {
    $this -> mFac-> mDifferentOld[$aKey] = $aValue;
  }

  protected function getFac() {
    if (isset($this -> mFac)) {
      return;
    }
    $this -> mFac = new CHtm_Fie_Fac();
  }

  public function addDef($aDef) {
    $lAlias = $aDef['alias'];
    $this -> mFie[$lAlias] = $aDef;
  }

  public function getDef($aAlias) {
    if (isset($this -> mFie[$aAlias])) {
      return $this -> mFie[$aAlias];
    } else {
      return NULL;
    }
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

  public function getVal($aKey, $aDefault = '') {
    if (isset($this -> mVal[$aKey])) {
      return $this -> mVal[$aKey];
    } else {
      return $aDefault;
    }
  }

  public function setVal($aKey, $aValue) {
    $this -> mVal[$aKey] = $aValue;
  }

  public function assignVal($aArr) {
    if (empty($aArr)) {
      return;
    }
    foreach ($aArr as $lKey => $lVal) {
      $this -> setVal($lKey, $lVal);
    }
  }

  protected function getCont() {
    $lRet = $this -> onBeforeContent();
    $lRet.= $this -> getComment('start');
    $lRet.= $this -> getFormTag();
    $lRet.= $this -> getHiddenFields();

    $lRet.= $this -> getTag();

    $lRet.= $this -> getTitle();
    $lRet.= $this -> getDescription();
    $lRet.= $this -> getForm();
    if ($this -> mButtons == TRUE) {
      $lRet.= $this -> getButtons();
    }

    $lRet.= $this -> getEndTag();

    $lRet.= '</form>'.LF;
    $lRet.= $this -> getJs();
    $lRet.= $this -> getComment('end');
    return $lRet;
  }

  protected function getFormTag() {
    return $this -> mFormTag;
  }

  public function setFormTag($aCnt) {
    $this -> mFormTag = $aCnt;
  }

  public function setUploadFormTag() {
    $this -> setFormTag('<form id="'.$this -> mFrmId.'" action="index.php" method="post" enctype="multipart/form-data">');
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

  protected function getTitle() {
    return '<div class="th1">'.htm($this -> mCap).'</div>'.LF;
  }

  public function setDescription($aDescription = '') {
    $this -> mDescription[] = $aDescription;
  }

  protected function getDescription() {
    $lRet = '';
    if (!empty($this -> mDescription)) {
      foreach ($this -> mDescription as $lDesc) {
        $lRet .= htm($lDesc).'<br />';
      }
      $lRet = '<div class="th3">'.$lRet.'</div>'.LF;
    }
      return $lRet;

  }

  protected function getForm() {
    $lRet = '<div class="frm" style="padding:16px;">'.LF;
    $lRet.= '<table cellpadding="4" cellspacing="0" border="0">'.LF;

    $lRet.= $this -> getFieldForm();

    $lRet.= '</table>';
    $lRet.= '</div>';
    return $lRet;
  }

  protected function getFieldForm() {
    $lRet = '';
    if (!empty($this -> mFie)) {
      foreach ($this -> mFie as $lAlias => $lDefInfo) {
        $lDef = $lDefInfo;
        $lRet.= '<tr>'.LF;
        if ($this -> mAltLan == FALSE) {
          $lRet.= '<td class="nw">'.htm($lDef['name_'.LAN]).'</td>'.LF;
        } ELSE {
          $lRet.= '<td class="nw">'.htm(lan($lAlias)).'</td>'.LF;
        }
        $lRet.= '<td>'.LF;
        $lRetImg = '';
        if (isset($lDef['_img'])) {
          if (!empty($lDef['_img'])) {
            $lRetImg = ' '.$lDef['_img'].LF;
          }
          $lDef -> offsetUnset('_img');
        }
        $lRetHr = '';
        if (isset($lDef['_hr'])) {
          if (isset($lDef['_hr'])) {
            $lRetHr = '<tr><td colspan="2"><p><hr /></p></td></tr>'.LF;
          }
          $lDef -> offsetUnset('_hr');
        }
        $lRet.= $this -> mFac -> getInput($lDef, $this -> getVal($lAlias), $this -> mReadOnly);
        $lRet.= $lRetImg;
        $lRet.= '</td>'.LF;
        $lRet.= '</tr>'.LF;
        $lRet.= $lRetHr;
      }
    }
    return $lRet;
  }

  protected function getButtons($aBtnAtt = array(), $aBtnTyp = 'button') {
    $lRet = '<div class="btnPnl">'.LF;
    $lRet.= btn(lan('lib.ok'), '', '<i class="ico-w16 ico-w16-ok"></i>', 'submit', $aBtnAtt).NB;
    if (!empty($this -> mCancel)) {
      $lRet.= btn(lan('lib.cancel'), "go('index.php?act=".$this -> mCancel."')", '<i class="ico-w16 ico-w16-cancel"></i>', $aBtnTyp, $aBtnAtt);
    }
    $lRet.= '</div>'.LF;
    return $lRet;
  }

  protected function getJs() {
    $lRet = '';
    return $lRet;
  }
}