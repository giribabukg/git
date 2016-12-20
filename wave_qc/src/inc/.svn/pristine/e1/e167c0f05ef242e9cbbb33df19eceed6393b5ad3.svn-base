<?php
class CInc_Htm_Menu extends CCor_Ren {

  protected $mHtm;

  public function __construct($aCaption, $aClass = '', $aCaptWithHtm = true) {

    $this -> mHtm = $aCaptWithHtm; // Html-Codieren der Caption?
    $this -> mCaption = $aCaption;
    $this -> mClass = $aClass;

    $this -> mItems = array();
    $this -> mHasImg = FALSE;
    $this -> mHasIco = FALSE;


    $this -> mDiv = getNum('d'); // surrounding div
    $this -> mDivId = getNum('d'); // div of poupmenu
    $this -> mLnkId = getNum('l');
  }

  public function getId() {
    return $this -> mDiv;
  }

  /**
   * Add menu item
   *
   * @param string $aUrl      Main link of menu item
   * @param string $aCaption  Caption of menu item
   * @param string $aImg      Info image on the left (optional)
   * @param string $aUrl2     Link on the right (optional)
   * @param string $aImg2     Image for right link (optional)
   */

  public function addItem($aUrl, $aCaption, $aImg = '', $aUrl2 = '', $aImg2 = '', $aAplTd = '') {
    $lItm = array();
    $lItm['url']  = $aUrl;
    $lItm['cap']  = $aCaption;
    $lItm['img']  = $aImg;
    $lItm['url2'] = $aUrl2;
    $lItm['img2'] = $aImg2;
    $lItm['apltd'] = $aAplTd; // Für gesamte KorrekturumlaufStatus
    if (!empty($aImg)) {
      if($aImg[0] === "<") {
       $this -> mHasIco = TRUE;
      }
      else {
       $this -> mHasImg = TRUE;
      }
    }
    $this -> mItems[] = $lItm;
  }

  /**
   * Is the menu empty (e.g. because user has no rights for menu items)?
   *
   * @return bool True if menu has no menu items
   */
  public function isEmpty() {
    return empty($this -> mItems);
  }

  public function addJsItem($aUrl, $aCaption, $aImg = '', $aUrl2 = '', $aImg2 = '') {
    $this -> addItem('javascript:'.$aUrl, $aCaption, $aImg, $aUrl2, $aImg2);
  }

  public function addTh1($aCaption) {
    $this -> addItem('th1', $aCaption);
  }

  function addTh2($aCaption) {
    $this -> addItem('th2', $aCaption);
  }

  function getTh1($aItm) {
    $lRet = '<tr>';
    $lRet.= '<td class="th1 nw" colspan="3">';
    $lRet.= $aItm['cap'];
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  function getTh2($aItm) {
    $lRet = '<tr>';
    $lRet.= '<td class="th2 nw" colspan="3">';
    $lRet.= $aItm['cap'];
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  function getTd($aItm) {
    $lRet = TR;
    if ($this -> mHasImg) {
      $lRet.= '<td class="td2 w16 ac">';
      if (empty($aItm['img'])) {
        $lRet.= '<img src="img/d.gif" width="16" alt="" />';
      } else {
        $lRet.= img("img/".$aItm['img']);
      }
      $lRet.= '</td>';
    }
    else if($this -> mHasIco) {
      $lRet.= '<td class="td2 w16 ac">';
      $lRet .= $aItm["img"];
      $lRet.= '</td>';
    }

    if (empty($aItm['img2'])) {
      if (is_array($aItm['cap'])) {
        foreach($aItm['cap'] as $lPart) {
          $lRet.= '<td class="td1 nw" colspan="2">';
          $lRet.= '<a href="'.$aItm['url'].'" class="nav db">';
          $lRet.= htm($lPart);
          $lRet.= '</a>';
          $lRet.= '</td>';
        }
      } else {
        $lRet.= '<td class="td1 nw" colspan="2">';
        $lRet.= '<a href="'.$aItm['url'].'" class="nav db">';
        $lRet.= htm($aItm['cap']);
        $lRet.= '</a>';
        $lRet.= '</td>';
      }
    } else {
      if (is_array($aItm['cap'])) {
        foreach($aItm['cap'] as $lPart) {
          $lRet.= '<td class="td1 nw">';
          $lRet.= '<a href="'.$aItm['url'].'" class="nav db">';
          $lRet.= htm($lPart);
          $lRet.= '</a>';
          $lRet.= '</td>';
        }
      } else {
        $lRet.= '<td class="td1 nw">';
        $lRet.= '<a href="'.$aItm['url'].'" class="nav db">';
        $lRet.= htm($aItm['cap']);
        $lRet.= '</a>';
        $lRet.= '</td>';
      }
      $lImg = getNum('i');
      $lRet.= '<td class="td2 w16 ac">';
      if (!empty($aItm['url2'])) {
        $lRet.= '<a href="'.$aItm['url2'].'" class="nav">';
        $lSrc = (empty($aItm['img2'])) ? 'img/ico/16/del.gif' : $aItm['img2'];
        $lRet.= img($lSrc);
        $lRet.= '</a>';
      } else {
        $lSrc = $aItm['img2'];
        $lRet.= img($lSrc);
      }
      $lRet.= '</td>';

      // F�r gesamte KorrekturumlaufStatus
      if ($aItm['apltd']){
      $lRet.= $aItm['apltd'];
      }

    }
    $lRet.= '</tr>'.LF;

    return $lRet;
  }

  protected function getCont() {
    if (empty($this -> mItems)) {
      return '';
    }
    $lRet = $this -> getComment('start');
    $lRet.= '<div id="'.$this -> mDiv.'" class="'.$this -> mClass.'">';

    $lRet.= '<a class="nav" id="'.$this -> mLnkId.'" href="javascript:Flow.Std.popMen(\''.$this -> mDivId.'\',\''.$this -> mLnkId.'\')">';
    if($this -> mHtm)
      $lRet.= htm($this -> mCaption);
    else
      $lRet.= $this -> mCaption;
    $lRet.= '</a>';

    $lRet.= $this -> getMenuDiv();

    $lRet.= '</div>';
    $lRet.= $this -> getComment('end');
    return $lRet;
  }

  public function getMenuDiv($aScroll = '') {
    $lRet = '';
    $lRet.= '<div id="'.$this -> mDivId.'" class="smDiv" ';
    $lStyle = 'style="display:none' . (!empty($aScroll) ? ';background-color:#FFFFFF; border:1px solid #000000;overflow:auto;' : '') . ';">';
    $lRet.= $lStyle;

    $lRet.= '<table cellpadding="2" cellspacing="0" border="0" class="tbl mw200">';
    foreach ($this -> mItems as $lItm) {
      $lUrl = $lItm['url'];
      if ($lUrl == 'th1') {
        $lRet.= $this -> getTh1($lItm);
      } else if ($lUrl == 'th2') {
        $lRet.= $this -> getTh2($lItm);
    }else{
        $lRet.= $this -> getTd($lItm);
    }
    }
    $lRet.= '</table>';
    $lRet.= '</div>';
    return $lRet;
  }

  public function getMenuWaveDiv($aScroll = ''){
    $lRet = '';

    $lRet.= '<div id="'.$this -> mDivId.'" class="smDiv" ';
    $lStyle = 'style="display:none' . (!empty($aScroll) ? ';background-color:#FFFFFF; border:1px solid #000000;overflow:auto' : '') . ';">';
    $lRet.= $lStyle;
    $lRet.= $this->getMenuSubList();
    $lRet.= '</div>';

    return $lRet;
  }

  function getTh1MM($aItm) {
    $lRet .= '<li class="th1 nw">';
    $lRet.= $aItm['cap'];
    $lRet .= '</li>' . LF;
    return $lRet;
  }

  function getTh2MM($aItm) {
    $lRet = '<li class="th2 nw">';
    $lRet.= $aItm['cap'];
    $lRet .= '</li>' . LF;
    return $lRet;
  }

  function getTdMM($aItm) {
    $lAct = $_GET['act'];
    $lItmAct = str_replace("index.php?act=", "", $aItm['url']);

    $lLastThree = substr($lAct, -3);
    if($lLastThree == 'edt' || $lLastThree == 'new' || $lLastThree == 'his' || $lLastThree === 'fil'){
      $lAct = substr($lAct, 0 , -4);
    } else {
      $lAct = substr($lAct, 0 , -4) . '-' . $_GET['src'];
    }

    $lRet = ($lAct == $lItmAct ? '<li class="active">' : '<li>');

    // F�r gesamte KorrekturumlaufStatus
    if($aItm['apltd']){
      $lRet .= $aItm['apltd'];
    }

    if(empty($aItm['img2'])){
      if(is_array($aItm['cap'])){
        foreach($aItm['cap'] as $lPart){
          $lRet .= '<a href="' . $aItm['url'] . '" class="nav db">';
          $lRet .= htm($lPart);
          $lRet .= '</a>';
        }
      }else{
        $lRet .= '<a href="' . $aItm['url'] . '" class="nav db">';
        $lRet .= htm($aItm['cap']);
        $lRet .= '</a>';
      }
    }else{
      if(is_array($aItm['cap'])){
        foreach($aItm['cap'] as $lPart){
          $lRet .= '<a href="' . $aItm['url'] . '" class="nav db">';
          $lRet .= htm($lPart);
          $lRet .= '</a>';
        }
      }else{
        $lRet .= '<a href="' . $aItm['url'] . '" class="nav db">';
        $lRet .= htm($aItm['cap']);
        $lRet .= '</a>';
      }

      $lImg = getNum('i');
      if(!empty($aItm['url2'])){
        $lRet .= '<a href="' . $aItm['url2'] . '" class="nav">';
        $lSrc = (empty($aItm['img2'])) ? 'img/ico/16/del.gif' : $lItm['img2'];
        $lRet .= img($lSrc);
        $lRet .= '</a>';
      }else{
        $lSrc = $aItm['img2'];
        $lRet .= img($lSrc);
      }
    }
    $lRet .= '</li>' . LF;

    return $lRet;
  }

  public function getMenuSubList() {
    $lRet = '';
    $lRet .= '<ul class="subMenuItms">';

    //echo "<pre>"; print_r($this->mItems); echo "</pre>";
    foreach($this->mItems as $lItm){
      $lUrl = $lItm['url'];
      if($lUrl == 'th1'){
        $lRet .= $this->getTh1MM($lItm);
      }else if($lUrl == 'th2'){
        $lRet .= $this->getTh2MM($lItm);
      }else{
        $lRet .= $this->getTdMM($lItm);
      }
    }
    $lRet .= '</ul>';

    return $lRet;
  }

  public function getSubMenu(){
    $lRet = '';
    if(THEME === 'default'){
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0" class="tbl mw200">';
    foreach ($this -> mItems as $lItm) {
      $lUrl = $lItm['url'];
      if ($lUrl == 'th1') {
        $lRet.= $this -> getTh1($lItm);
      } else if ($lUrl == 'th2') {
        $lRet.= $this -> getTh2($lItm);
      } else {
        $lRet.= $this -> getTd($lItm);
      }
    }
    $lRet.= '</table>';
    } else {
      $lRet.= $this->getMenuSubList();
    }

    return $lRet;
  }
}
