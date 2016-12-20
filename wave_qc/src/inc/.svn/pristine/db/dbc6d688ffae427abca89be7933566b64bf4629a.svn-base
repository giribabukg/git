<?php
class CInc_Htm_List extends CHtm_Tag {

  protected $mAllRows = array();
  protected $mCrpFlagsAliase = array();
  protected $mCrpAllFlagsAli = array();
  protected $mCrpAllFlagsDdl = array();
  protected $mFootCols = array();
  protected $mFootColCnt = 0;
  /**
   * @var string
   */
  public $mHighlight = '';
  /**
   * MakeFunction: Es sollen alle Werte von 'dur' addiert werden. Je 'pos' wird aber nur der groesste Wert aufsummiert.
   * @var array
   */
  public $mMakeFunction = array();
  /**
   * @var boolean
   */
  public $mShowHdr = TRUE;
  public $mShowSubHdr = TRUE;
  public $mShowSerHdr = TRUE;
  public $mStdLink = '';

  public function __construct($aMod, $aPriv = '') {
    parent::__construct('table');
    $this -> mMod = $aMod;
    $this -> mPrf = $aMod;
    if (empty($aPriv)) {
      $this -> mPriv = $aMod;
    } else {
      $this -> mPriv = $aPriv;
    }

    $this -> mDefaultOrder = '';

    $this -> mCols = array();
    $this -> mFie  = array();
    $this -> mBtn  = array();
    $this -> mLnk  = array();
    $this -> mPnl  = array();
    $this -> mButton = array();
    $this -> mParam = array();

    // std values
    $this -> mTitle = lan('lib.list');
    $this -> mPage  = 0;
    $this -> mFirst = 0;
    $this -> mLpp   = 25;
    $this -> mDefaultLpp  = 25;
    $this -> mMaxLines = 0;

    $this -> mGrp = '';
    $this -> mOldGrp = NULL;
    $this -> mOrd = '';
    $this -> mDir = 'asc';

    $this -> mNavBar     = TRUE;
    $this -> mShowColHdr = TRUE;


    $this -> mCpyWarn    = FALSE;

    $this -> mCanEdit   = FALSE;
    $this -> mCanInsert = FALSE;
    $this -> mCanDelete = FALSE;

    $this -> getPriv($this -> mPriv);

    if (empty($this -> mStdLink)) {
      $this -> mStdLink = $this -> getStdUrl();
    }
    $this -> mOrdLnk = $this -> mStdLink.'.ord&amp;fie=';
    if($this -> mCanEdit)
      $this -> mStdLnk = $this -> mStdLink.'.edt&amp;id=';
    else
      $this -> mStdLnk = '';
    $this -> mDelLnk = $this -> mStdLink.'.del&amp;id=';
    $this -> mCpyLnk = $this -> mStdLink.'.cpy&amp;id=';
    $this -> mLppLnk = $this -> mStdLink.'.lpp&amp;lpp=';
    $this -> mIdField = 'id';
    $this -> mCapCls = 'cap';

    $this -> mIte = new ArrayIterator(array());

    // start values
    $this -> mCls = 'td1';
    $this -> mCtr = 1;

    // Std Html Attributes
    $this -> setAtt('class', 'tbl');
    $this -> setAtt('cellpadding', '2');
    $this -> setAtt('cellspacing', '0');

  }

  protected function getStdUrl() {
    return 'index.php?act='.$this -> mMod;
  }

  protected function getPriv($aKey = NULL) {
    $lKey = (NULL === $aKey) ? $this -> mMod : $aKey;
    $lUsr = CCor_Usr::getInstance();
    $this -> mCanRead   = $lUsr -> canRead($lKey);
    $this -> mCanEdit   = $lUsr -> canEdit($lKey);
    $this -> mCanInsert = $lUsr -> canInsert($lKey);
    $this -> mCanDelete = $lUsr -> canDelete($lKey);
  }

  protected function getPrefs($aKey = NULL, $aAnyUsrID = NULL) {
    $lKey = (NULL === $aKey) ? $this -> mPrf : $aKey;

    if (is_null($aAnyUsrID)) {
    $lUsr = CCor_Usr::getInstance();
    } else {
      $lUsr = new CCor_Anyusr($aAnyUsrID);
    }
    $this -> mLpp  = $lUsr -> getPref($lKey.'.lpp', $this -> mDefaultLpp);
    $this -> mPage = $lUsr -> getPref($lKey.'.page');
    $this -> mFirst = $this -> mLpp * $this -> mPage;
    $this -> mOrd  = $lUsr -> getPref($lKey.'.ord', $this -> mDefaultOrder);
    $this -> mDir  = 'asc';
    if (substr($this -> mOrd, 0, 1) == '-') {
      $this -> mOrd = substr($this -> mOrd,1);
      $this -> mDir = 'desc';
    }
    $lGrp = $lUsr -> getPref($lKey.'.grp');
    if (NULL !== $lGrp) {
      $this -> setGroup($lGrp);
    }
    $this -> mSerFie = $lUsr -> getPref($lKey.'.sfie');
    $this -> mFilFie = $lUsr -> getPref($lKey.'.ffie');

    $this -> mSer = $lUsr -> getPref($lKey.'.ser');
    if (($lSer = @unserialize($this -> mSer)) !== FALSE) {
      $this -> mSer = $lSer;
    }

    $this -> mFil = $lUsr -> getPref($lKey.'.fil');
    if (($lFil = @unserialize($this -> mFil)) !== FALSE) {
      $this -> mFil = $lFil;
    }
  }

  public function & addColumn($aAlias = '', $aCaption = '', $aSortable = FALSE, $aHtmAttr = array(), $aFieAttr = array()) {
    if (empty($aAlias)) {
      $aAlias = getNum('col');
    }
    $lCol = new CHtm_Column($aAlias, $aCaption, $aSortable, $aHtmAttr, $aFieAttr);
    $this -> mCols[$aAlias] = & $lCol;
    $this -> mColCnt = count($this -> mCols);
    return $lCol;
  }

  public function hasColumn($aAlias) {
    return (isset($this->mCols[$aAlias]));
  }

  public function hasAnyColumn($aAlias) {
    if (!is_array($aAlias)) {
      return $this->hasColumn($aAlias);
    }
    foreach ($aAlias as $lAlias) {
      if ($this->hasColumn($lAlias)) return true;
    }
    return false;
  }

  public function addColumnBefore($aBefore, $aAlias = '', $aCaption = '', $aSortable = FALSE, $aHtmAttr = array(), $aFieAttr = array()) {
    if (! $this->hasAnyColumn($aBefore)) {
      return $this->addColumn($aAlias, $aCaption, $aSortable, $aHtmAttr, $aFieAttr);
    }
    $lOldCols = $this->mCols;
    $lWasFound = false;
    $this->mCols = array();
    foreach ($lOldCols as $lKey => $lVal) {
      if (!$lWasFound) {
        if (is_array($aBefore)) {
          $lFound = in_array($lKey, $aBefore);
        } else {
          $lFound = ($lKey == $aBefore);
        }
        if ($lFound) {
          $lWasFound = true;
          $lRet = $this->addColumn($aAlias, $aCaption, $aSortable, $aHtmAttr, $aFieAttr);
        }
      }
      $this->mCols[$lKey] = $lVal;
    }
    if (!$lWasFound) {
      $lRet = $this->addColumn($aAlias, $aCaption, $aSortable, $aHtmAttr, $aFieAttr);
    }
    $this->mColCnt = count($this->mCols);
    return $lRet;
  }

  public function & addFooter($aAlias = '', $aCaption = '', $aSortable = FALSE, $aHtmAttr = array(), $aFieAttr = array()) {
    if (empty($aAlias)) {
      $aAlias = getNum('col');
    }
    $lCol = new CHtm_Column($aAlias, $aCaption, $aSortable, $aHtmAttr, $aFieAttr);
    $this -> mFootCols[$aAlias] = & $lCol;
    $this -> mFootColCnt = count($this -> mFootCols);

    return $lCol;
  }

  public function & addField($aDef) {
    $lAlias = $aDef['alias'];
    $lSort  = bitSet($aDef['flags'], ffSort);
    $this -> mFie[$lAlias] = $lAlias;
    $lCol = & $this -> addColumn($lAlias, $aDef['name_'.LAN], $lSort, array(), $aDef);
    return $lCol;
  }

  public function & addChk() {
    $lCol = & $this -> addColumn('chk', '', FALSE, array('width' => '16', 'id' => 'chk'));
    return $lCol;
  }

  public function & addCtr() {
    $lCol = & $this -> addColumn('ctr', '', FALSE, array('width' => '16', 'id' => 'ctr'));
    return $lCol;
  }

  public function & addDel() {
    $lCol = & $this -> addColumn('del', '', FALSE, array('width' => '16', 'id' => 'del'));
    return $lCol;
  }

  public function & addIsThisInUse() {
  	$lCol = & $this -> addColumn('inuse', '', FALSE, array('width' => '16', 'id' => 'inuse'));
    return $lCol;
  }

  public function & addMor() {
    $lCol = & $this -> addColumn('mor', '', FALSE, array('width' => '16', 'id' => 'mor'));
    return $lCol;
  }

  public function & addSrc() {
    $lCol = & $this -> addColumn('src', '', FALSE, array('width' => '16', 'id' => 'src'));
    return $lCol;
  }

  public function & addUnassign() {
    $lCol = & $this -> addColumn('unassign', '', FALSE, array('width' => '16', 'id' => 'unassign'));
    return $lCol;
  }

  public function & addCpy($aWarn = FALSE) {
    if ($aWarn) {
      $this -> mCpyWarn = TRUE;
    }
    $lCol = & $this -> addColumn('cpy', '', FALSE, array('width' => '16', 'id' => 'cpy'));
    return $lCol;
  }

  public function & addBtn($aCaption, $aUrl, $aImg = '',$aRightRanged = FALSE) {
    $lBtn = new CCor_Dat();
    $lBtn['cap'] = $aCaption;
    $lBtn['url'] = $aUrl;
    $lBtn['img'] = $aImg;
    $lBtn['rightranged'] = $aRightRanged; // Use to show Button on the Right.
    $this -> mBtn[] = & $lBtn;
    return $lBtn;
  }

  public function & addLink($aCaption, $aUrl) {
    $lLnk = new CCor_Dat();
    $lLnk['cap'] = $aCaption;
    $lLnk['url'] = $aUrl;
    $this -> mLnk[] = & $lLnk;
    return $lLnk;
  }

  public function & addPanel($aKey = NULL, $aCont = '') {
    $lKey = (NULL === $aKey) ? getNum('pnl') : $aKey;
    $this -> mPnl[$lKey] = $aCont;
    return $this -> mPnl[$lKey];
  }

  public function & addButton($aKey = NULL, $aCont = '') {
    $lKey = (NULL === $aKey) ? getNum('btn') : $aKey;
    $this -> mButton[$lKey] = $aCont;
    return $this -> mButton[$lKey];
  }

  public function & getColumn($aAlias) {
    $lRet = NULL;
    if (isset($this -> mCols[$aAlias])) {
      $lRet = $this -> mCols[$aAlias];
    }
    return $lRet;
  }

  public function setHidden($aAlias, $aFlag = TRUE) {
    $lCol = & $this -> getColumn($aAlias);
    if (NULL !== $lCol) {
      $lCol -> setHidden($aFlag);
    }
  }

  public function setColCaption($aAlias, $aCaption) {
    $lCol = & $this ->  getColumn($aAlias);
    if (NULL !== $lCol) {
      $lCol -> setCaption($aCaption);
    }
  }

  public function setGroup($aGroup) {
    $this -> mGrp = $aGroup;
    $this -> mIte -> setGroup($aGroup);
  }

  protected function getCont() {
    $lRet = parent::getTag();
    $lRet.= $this -> getHead();
    $lRet.= '<tbody>'.LF;
    $lRet.= $this -> getBody();
    $lRet.= '</tbody>'.LF;

    $lRet.= $this -> getColumnFooter();

    $lRet.= parent::getEndTag();
    return $lRet;
  }

  protected function getHead() {
    $lRet = '';

    if ($this -> mShowHdr) {
      $lRet = $this -> getTitle();
    }
    if ($this -> mShowSubHdr) {
      $lRet.= $this -> getSubHeader();
    }
    $lRet.= $this -> getFilterBar();
    if ($this -> mShowSerHdr) {
      $lRet.= $this -> getSearchBar();
    }
    if ($this -> mShowColHdr) {
      $lRet.= $this -> getColumnHeaders();
    }
    if (!empty($lRet)) {
      $lRet = '<thead>'.LF.$lRet.'</thead>'.LF;
    }
    return $lRet;
  }

  protected function getBody() {
    $lRet = $this -> getRows();
    return $lRet;
  }

  protected function getTitle() {
    $lRet = '<tr>'.LF;
    $lRet.= '<td class="'.$this -> mCapCls.'"'.$this -> getColspan().'>';
    $lRet.= $this -> getTitleContent();
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function getTitleContent() {
    return htm($this -> mTitle);
  }

  protected function getSubHeader() {
    $lRet = '<tr>'.LF;
    $lRet.= '<td class="sub"'.$this -> getColspan().'>';
    $lRet.= $this -> getSubHeaderContent();
    $lRet.= '</td>';
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function getSubHeaderContent() {
    $lRet = '';
    $lRightRanged= FALSE; // Use to show Button on the Right.
    if (!empty($this -> mBtn)) {
      foreach ($this -> mBtn as $lBtn) {
        if (!$lBtn['rightranged']){
          $lRet.= '<td>'.LF;
          $lRet.= btn($lBtn['cap'], $lBtn['url'], $lBtn['img']);
          $lRet.= '</td>'.LF;
        }else{
          $lRightRanged= TRUE;
        }
      }
    }
    if (!empty($this -> mLnk)) {
      foreach ($this -> mLnk as $lLnk) {
        $lRet.= '<td>'.LF;
        $lRet.= '<a href="'.$lLnk['url'].'" class="nav nw">'.htm($lLnk['cap']).'</a>';
        $lRet.= '</td>'.LF;
      }
    }
    if (!empty($this -> mPnl)) {
      foreach ($this -> mPnl as $lKey => $lCnt) {
        if (!empty($lCnt)) {
          $lRet.= '<td id="'.$lKey.'">'.LF;
          $lRet.= $lCnt;
          $lRet.= '</td>'.LF;
        }
      }
    }
    if (!empty($this -> mButton)) {
      foreach ($this -> mButton as $lKey => $lCnt) {
        if (!empty($lCnt)) {
          $lRet.= '<td id="'.$lKey.'">'.LF;
          $lRet.= $lCnt;
          $lRet.= '</td>'.LF;
        }
      }
    }
    if ($lRightRanged){
      foreach ($this -> mBtn as $lBtn) {
        if ($lBtn['rightranged']){
          $lRet.= '<td>'.LF;
          $lRet.= btn($lBtn['cap'], $lBtn['url'], $lBtn['img']);
          $lRet.= '</td>'.LF;
        }
      }
    }

    if (!empty($lRet)) {
      $lRet = '<table cellpadding="2" border="0"><tr>'.$lRet.'</tr></table>';
    }
    return $lRet;
  }

  protected function getNavBar() {
    if (!$this -> mNavBar) {
      return '';
    }
    if (isset($this -> mJobId)){
      $lJobId = $this -> mJobId;
    }else {
      $lJobId = '';
    }

    $lNav = new CHtm_NavBar($this -> mMod, $this -> mPage, $this -> mMaxLines, $this -> mLpp, $lJobId);
    return $lNav -> getContent();
  }

  protected function & getViewMenuObject() {
    $lMen = new CHtm_Menu(lan('lib.opt'));
    $lMen -> addTh2(lan('lib.opt.lpp'));
    $lOk = '<i class="ico-w16 ico-w16-ok"></i>';
    $lArr = array(25,50,100,200);
    foreach ($lArr as $lLpp) {
      $lImg = ($lLpp == $this -> mLpp) ? $lOk : '<img src="img/d.gif" alt="">';
      $lMen -> addItem($this -> mLppLnk.$lLpp, $lLpp.' '.lan('lib.opt.lines'), $lImg);
    }
    return $lMen;
  }

  protected function getViewMenu() {
    $lMen = & $this -> getViewMenuObject();
    $lRet = '<table cellpadding="0" cellspacing="0" border="0"><tr><td>'.LF;
    $lRet.= $lMen -> getContent();
    $lRet.= '</td></tr></table>'.LF;
    return $lRet;
  }

  protected function getFilterBar() {
    return '';
  }

  protected function getSearchBar() {
    return '';
  }

  protected function getColHdr($aCol) {
    $lColAtt = $aCol -> getAttributes();

    $lTag = new CHtm_Tag('td');
    $lTag -> setAtt('class', 'th2 nw');
    $lTag -> addAttributes($lColAtt);
    $lRet = $lTag -> getTag();

    $lCap = htm($aCol -> getCaption());
    $lColAttId = array_key_exists('id', $lColAtt) ? $lColAtt['id'] : 1;
    if ('' == $lCap && $lColAttId != 'chk') {
      $lCap = '&nbsp;';
    } elseif ($lColAttId == 'chk') {
      $lCap = '<input type="checkbox" name="all" onchange="Flow.Std.toggleSelection();" />';
    }

    if ($aCol -> isSortable()) {
      $lFie = $aCol -> getAlias();
      $lDir = '';
      $lImg = '';
      if ((!empty($lFie)) and ($lFie == $this -> mOrd)) {
        if ('asc' == $this -> mDir) {
          $lDir = '-';
          $lImg = '<i class="ico-w16 ico-w16-ord-asc"></i>';
        } else {
          $lImg = '<i class="ico-w16 ico-w16-ord-desc"></i>';
        }
        $lImg.= '&nbsp;';
      }
      $lRet.= '<a href="'.$this -> mOrdLnk.$lDir.$lFie.'">';
      $lRet.= $lImg;
      $lRet.= $lCap;
      $lRet.= '</a>';
    } else {
      $lRet.= $lCap;
    }
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getColFooter($aCol) {
    $lAli = $aCol -> getAlias();
    $lColAtt = $aCol -> getAttributes();

    $lTag = new CHtm_Tag('td');
    $lTag -> setAtt('class', 'td nw');
    $lTag -> addAttributes($lColAtt);
    $lRet = $lTag -> getTag();

    $lCap = htm($aCol -> getCaption());
    if ('' == $lCap) {
      $lCap = '&nbsp;';
    }

    $lRet.= $lCap;
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getColumnHeaders() {
    if (empty($this -> mCols)) {
      $this -> dbg('No columns specified');
      return '';
    }
    $lRet = '<tr>'.LF;
    foreach($this -> mCols as & $lCol) {
      if ($lCol -> isHidden()) {
        continue;
      }
      // TODO: getHdrAlias dispatcher
      $lRet.= $this -> getColHdr($lCol);
    }
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function countFunction() {
    return array('val' => '', 'key' => '');
  }

  protected function getColumnFooter() {
    if (empty($this -> mFootCols)) {
      return '';
    }

    $lFunc = $this -> countFunction();

    $lRet = '<tfoot><tr>'.LF;
    foreach ($this -> mFootCols as & $lCol) {
      $lAli = $lCol -> getAlias();
      if (!empty($lFunc['key']) AND $lFunc['key'] == $lAli) {
        $lCol -> setCaption($lFunc['val']);
      } else {
        $lCap = htm($lCol -> getCaption());
        if ('' == $lCap) {
          $lCap = '&nbsp;';
        }
        #$lRet.= '<td>'.$lCap.'</td>';

      }
      $lRet.= $this -> getColFooter($lCol);
    }
    $lRet.= '</tr></tfoot>'.LF;
    return $lRet;
  }

  protected function td($aCnt = '', $aId = '', $aParams = '') {
    $lId = ($aId != '') ? 'id="'.$aId.'"' : '';
    $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw" '.$lId.' ';
    if (!empty($aParams)) {
      foreach ($aParams as $lKey => $lVal) {
        $lRet.= ' '.$lKey.'="'.htm($lVal).'"';
      }
    }
    $lRet.=' data-alias="'.$this -> mColKey.'" >';
    if ('' == $aCnt) {
      $aCnt = NB;
    }
    $lRet.= $aCnt;
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function tdc($aCnt = '') {
    $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' ac nw" data-alias="'.$this -> mColKey.'" >';
    if ('' == $aCnt) {
      $aCnt = NB;
    }
    $lRet.= $aCnt;
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function tdClass($aCnt = '', $aCls = '', $aAddClass = TRUE) {
    $lCls = $this -> mCls.($this -> mHighlight ? 'r': '').' nw';
    if (!empty($aCls)) {
      if ($aAddClass) {
        $lCls.= ' '.$aCls;
      } else {
        $lCls = $aCls;
      }
    }
    $lRet = '<td class="'.$lCls.'" data-alias="'.$this -> mColKey.'" >';
    if ('' == $aCnt) {
      $aCnt = NB;
    }
    $lRet.= $aCnt;
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  //muß per Default false sein, da der Content oft bereits Kodiert ist oder aus Images besteht.
  protected function a($aCnt = '', $aCntWithHtm = false) {
    if (empty($this -> mCurLnk)) {
      return $aCnt.NB;
    }
    $lRet = '<a href="'.$this -> mCurLnk.'">';
    if($aCntWithHtm)
      $lRet.= htm($aCnt);
    else
      $lRet.= $aCnt;

    if ('' == $aCnt) {
      $lRet.= NB;
    }
    $lRet.= '</a>';
    return $lRet;
  }

  protected function tda($aCnt = '') {
    return $this -> td($this -> a($aCnt));
  }

  protected function getTdCtr($aStat = FALSE) {
    $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' ar w16">';
    $lRet.= $this -> mCtr.'.';
    if ($aStat) $lRet.= '<b>R<b>';
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getDelLink() {
    $lId = $this -> getVal($this -> mIdField);
    $lRet = $this -> mDelLnk.$lId;
    return $lRet;
  }

  protected function getCpyLink() {
    $lId = $this -> getVal($this -> mIdField);
    $lRet = $this -> mCpyLnk.$lId;
    return $lRet;
  }

  protected function getTdDel() {
    $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw w16 ac">';
    $lRet.= '<a class="nav" href="javascript:Flow.Std.cnfDel(\''.$this -> getDelLink().'\', \''.LAN.'\')">';
    $lRet.= '<i class="ico-w16 ico-w16-del"></i>';
    $lRet.= '</a>';
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getTdCpy() {
    $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw w16 ac">';
    if ($this -> mCpyWarn) {
      $lRet.= '<a class="nav" href="javascript:Flow.Std.cnfCpy(\''.$this -> getCpyLink().'\', \''.LAN.'\')">';
    } else {
      $lRet.= '<a class="nav" href="'.$this -> getCpyLink().'">';
    }
    $lRet.= '<i class="ico-w16 ico-w16-copy"></i>';
    $lRet.= '</a>';
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getTdInuse() {
  	$lRet = '';
  	return $lRet;
  }

  protected function getColTd() {
    $lFnc = 'getTd'.$this -> mColKey;
    if ($this -> hasMethod($lFnc)) {
      return $this -> $lFnc();
    }

    if (!empty($this -> mCrpAllFlagsAli) AND isset($this -> mCrpAllFlagsAli[$this -> mColKey])) {
      return $this -> getCrpFlagTd($this -> mColKey);
    }
    if (!empty($this -> mCrpAllFlagsDdl) AND isset($this -> mCrpAllFlagsDdl[$this -> mColKey])) {
      return $this -> getCrpFlagDdlTd($this -> mColKey);
    }

    $this -> mCurCol = & $this -> getColumn($this -> mColKey);
    if (NULL !== $this -> mCurCol) {
      $lTyp = $this -> mCurCol -> getFieldAttr('typ');
      if (!empty($lTyp)) {
        $lFnc = 'getTdTyp'.$lTyp;
        if ($this -> hasMethod($lFnc)) {
          return $this -> $lFnc();
        }
      }
    }

    $lRet = $this -> td($this -> a(htm($this -> getVal($this -> mColKey))));
    return $lRet;
  }

  protected function getTdTypUselect() {
    $lUid = $this -> getVal($this -> mColKey);
    if (empty($lUid)) {
      return $this -> tda();
    }
    $lArr = CCor_Res::extract('id', 'fullname', 'usr');
    if (isset($lArr[$lUid])) {
      $lRet = $lArr[$lUid];
    } else {
      $lRet = '';
    }
    $lRet = $this -> td($this -> a(htm($lRet)));
    return $lRet;
  }

  protected function getTdTypTselect() {
    $lId  = (string)$this -> getVal($this -> mColKey);
    $lPar = toArr($this -> mCurCol -> getFieldAttr('param'));
    $lTbl = $lPar['dom'];
    $lArr = CCor_Res::get('htb', $lTbl);
    if (isset($lArr[$lId])) {
      $lRet = $lArr[$lId];
    } else {
      $lRet = $lId;
    }
    $lRet = $this -> td($this -> a(htm($lRet)));
    return $lRet;
  }

  protected function getTdTypGselect() {
    $lUid = $this -> getVal($this -> mColKey);
    $lArr = CCor_Res::extract('id', 'name', 'gru');
    if (isset($lArr[$lUid])) {
      $lRet = $lArr[$lUid];
    } else {
      $lRet = '';
    }
    $lRet = $this -> td($this -> a(htm($lRet)));
    return $lRet;
  }

  protected function getTdTypDate() {
    $lVal = $this -> getVal($this -> mColKey);
    $lDat = new CCor_Date($lVal);
    $lRet = $lDat -> getFmt(lan('lib.date.long'));
    $lRet = $this -> td($this -> a(htm($lRet)));
    return $lRet;
  }

  protected function getTdTypBoolean() {
    $lVal = $this -> getVal($this -> mColKey);
    $lImg = ('X' == $lVal) ? 'hi' : 'lo';
    $lImg = img('img/ico/16/check-'.$lImg.'.gif');
    $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw ac" data-alias="'.$this -> mColKey.'" >';
    $lRet.= $this -> a($lImg);
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getTdTypMemo() {
    $lVal = $this -> getVal($this -> mColKey);
    $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').'" data-alias="'.$this -> mColKey.'" >';
    $lRet.= $this -> a(htm($lVal));
    $lRet.= '</td>'.LF;
    return $lRet;
  }

  protected function getTdWebstatus() {
    $lVal = $this -> getVal($this -> mColKey);
    $lSta = $lVal / 10;
    $lRet = img('img/crp/'.$lSta.'b.gif');
    return $this -> tda($lRet);
  }

  protected function getLink() {
    if (empty($this -> mStdLnk)) {
      return '';
    } else {
      $lId = $this -> getVal($this -> mIdField);
      return $this -> mStdLnk.$lId;
    }
  }

  protected function getRow() {
    $lSrc = $this -> getVal('src');
    $lWebstatus = $this -> getVal('webstatus');
    $lHighlightAll = FALSE;
    $lAliasHighlightArr = Array();
    $lDdlSrcArr = Array(); // IF There is Deadline for current Jobart

    if (!empty($lSrc) AND 0 < $lWebstatus AND isset($this -> mDdl[$lSrc])) {
      $lDdlSrcArr = $this -> mDdl[$lSrc];

      if ($this -> mHighlightLine){ //$this -> mHighlightLine is configuration Variable
                                    //If $this -> mHighlightLine = TRUE, higlight all row.And only by current Webstatus
                                    //If $this -> mHighlightLine = FALSE, higlight only Deadline Fields.
        if (isset($lDdlSrcArr[$lWebstatus])){
          $lDdl = $lDdlSrcArr[$lWebstatus];
          $lDdlDate = $this -> getVal($lDdl);
          if (!empty($lDdlDate)) {
            $lHighlightAll = $this -> getHighlight($lDdlDate);
          }
        }
      } else { // Set $lAliasHighlightArr which Deadline fields by following Webstatus.
        foreach($lDdlSrcArr as $lKey=>$lVal){
          if ($lKey < $lWebstatus) continue; // Only for following Webstatus.
          $lDdlDate = $this -> getVal($lVal);
          if (!empty($lDdlDate)){
            if ($this -> getHighlight($lDdlDate)){
              $lAliasHighlightArr[] = $lVal;
            }
          }
        }
      }
    }

    $lRet = $this->getTrTag();

    foreach ($this -> mCols as $this -> mColKey => & $this -> mCol) {
      $lHighlight = false;
      if ($this -> mCol -> isHidden()) {
        continue;
      }
      $this -> mCurLnk = $this -> getLink();

      if (!empty($lDdlSrcArr)){ // Higlight only if Deadline is defined.
        if ($lHighlightAll){ // If $this -> mHighlightLine= True then for all Row.
          $lHighlight = TRUE;
        } else {
          if (in_array($this -> mColKey,$lAliasHighlightArr)){ // Only Deadline fields has highlight.
            $lHighlight = TRUE;
          }
        }
      }
      $this->mHighlight = $lHighlight;

      $lRet.= $this -> getColTd();
    }
    $lRet.= '</tr>'.LF;
    return $lRet;
  }

  protected function getTrTag() {
    return '<tr class="hi">';
  }

  protected function getVal($aAlias) {
    return (isset($this -> mRow[$aAlias])) ? $this -> mRow[$aAlias] : '';
  }

  protected function getTdMand() {
     $lVal = $this -> getVal($this -> mColKey);
      if (empty($lVal)) {
       $lRet = "Global";
     } elseif ($lVal == -1) {
       $lRet = lan('lib.mand.all');
     }else {
       $lArr = CCor_Res::extract('id', 'name_'.LAN, 'mand');
       $lRet = $lArr[$lVal];
     }

     $lRet = $this -> td($this -> a(htm($lRet)));
     return $lRet;
  }

  protected function getInt($aAlias) {
    return (isset($this -> mRow[$aAlias])) ? intval($this -> mRow[$aAlias]) : 0;
  }

  protected function getCurVal() {
    return (isset($this -> mRow[$this -> mColKey])) ? $this -> mRow[$this -> mColKey] : '';
  }

  protected function getCurInt() {
    return (isset($this -> mRow[$this -> mColKey])) ? intval($this -> mRow[$this -> mColKey]) : 0;
  }

  protected function getRows() {
    $lRet = '';
    $this -> mCtr = $this -> mFirst + 1;
    #$this -> mIte -> rewind();
    foreach ($this -> mIte as $this -> mRow) {
      $lRet.= $this -> beforeRow();
      $lRet.= $this -> getRow();
      $lRet.= $this -> afterRow();
    }
    return $lRet;
  }

  protected function getGroupHeader() {
    $lRet = '';
    if (!empty($this -> mGrp)) {
      $lNew = $this -> getVal($this -> mGrp);
      if ($lNew !== $this -> mOldGrp) {
        $lRet = TR;
        $lRet.= '<td class="tg1" '.$this -> getColspan().'>';
        $lRet.= htm($lNew).NB;
        $lRet.= '</td>';
        $lRet.= _TR;
        $this -> mOldGrp = $lNew;
        $this -> mCls = 'td1';
      }
    }
    return $lRet;
  }

  protected function beforeRow() {
    return $this -> getGroupHeader();
  }

  protected function afterRow() {
    $this -> togCls();
    $this -> incCtr();
    if (0 < $this -> mFootColCnt) {
      $this -> mAllRows[] = $this -> mRow;
    }
    return '';
  }

  protected function getColspan() {
    return ' colspan="'.$this -> mColCnt.'"';
  }

  protected function togCls() {
    $this -> mCls = ($this -> mCls == 'td1') ? 'td2' : 'td1';
  }

  protected function incCtr() {
    $this -> mCtr++;
  }

  /**
   * *.CSV content
   *
   * @return boolean|string
   */
  public function getCsvContent($aHtmCol = null, $aAnyUsrLanguage = null, $aWebStatusExt = NULL) {
    if (isset($aHtmCol)) {
      $this -> mCols = $aHtmCol;
    }

    if (isset($aAnyUsrLanguage)) {
      $lLan = $aAnyUsrLanguage;
    } else {
      $lLan = LAN;
    }

    $this -> removeColumn('cpy'); // copy
    $this -> removeColumn('ctr'); // counter
    $this -> removeColumn('del'); // delete
    $this -> removeColumn('mor'); // more
    $this -> removeColumn('sel'); // select

    if (!CCor_Cfg::get('csv-exp.bymail', true)) {
      $lHeader = "sep=;".CR.LF;
      foreach ($this -> mCols as $this -> mColKey => & $this -> mCol) {
        $this -> mCurCol = & $this -> getColumn($this -> mColKey);
        $lTyp = $this -> mCurCol -> getFieldAttr('typ');

        if ($lTyp != 'file' && $lTyp != 'hidden' && $lTyp != 'image') {
          $lHeader.= '"'.$this -> mCol -> getCaption().'";';
        }
      }

      echo $lHeader.CR.LF;
      flush();
    } else {
      $lContent = '';
    }

    // flags
    $lFla = CCor_Res::extract('val', 'name_'.$lLan, 'jfl');

    // TODO: clean up CCor_Res_Crp*
    $lSQL = 'SELECT p.code AS a,q.`status` AS b,q.name_'.LAN.' AS c';
    $lSQL.= ' FROM al_crp_master AS p, al_crp_status AS q';
    $lSQL.= ' WHERE p.id=q.crp_id AND p.mand='.MID.' AND q.mand='.MID;
    
    $lStatusArray = array();
    $lDummy = new CCor_Qry($lSQL);
    foreach ($lDummy as $lKey => $lValue) {
      $lStatusArray[$lValue['a']][$lValue['b']] = $lValue['c'];
    }
    foreach ($this -> mIte as $this -> mRow) {
      $lSrc = $this -> mRow['src'];
    
      $lCell = '';
      $lRow = '';
      foreach ($this -> mCols as $this -> mColKey => & $this -> mCol) {
        if ($this -> mCol -> isHidden()) {
          continue;
        }

        $this -> mCurCol = & $this -> getColumn($this -> mColKey);
        $lTyp = $this -> mCurCol -> getFieldAttr('typ');

        if ($this -> mColKey == 'src') {
          $lSrc = $this -> getVal($this -> mColKey);
          $lCell = lan('job-'.$lSrc.'.menu');
          $lRow.= '"'.$lCell.'";';
        } elseif ($this -> mColKey == 'flags') {
          $lCell = '';
          $lCur = $this -> getCurInt();
          foreach ($lFla as $lKeyFla => $lValFla) {
            if (bitSet($lCur, $lKeyFla)) {
              $lCell.= $lValFla.', ';
            }
          }
          $lCell = rtrim($lCell, ', ');
          $lRow.= '"'.$lCell.'";';
        } elseif ($this -> mColKey == 'webstatus' AND $aWebStatusExt) {
          $lWebstatus = $this -> getVal($this -> mColKey);
          $lWebstatusDescrition = $lStatusArray[$lSrc][$lWebstatus];
          $lRow.= '"'.$lWebstatus.'";';
          $lRow.= '"'.$lWebstatusDescrition.'";';
        } elseif ($lTyp == 'boolean') {
          $lCell = $this -> getVal($this -> mColKey);
          $lRow.= '"'.$lCell.'";';
        } elseif ($lTyp != 'file' && $lTyp != 'hidden' && $lTyp != 'image') {
          $lTemp = $this -> getColtd();
          $lTemp = strip_tags($lTemp);
          $lTemp = str_replace('&nbsp;', '', $lTemp);
          $lTemp = dehtm(trim($lTemp));
          $lCell = $lTemp;
          $lRow.= '"'.$lCell.'";';
        }
      }

      $lRow.= CR.LF;

      if (!CCor_Cfg::get('csv-exp.bymail', true)) {
        echo $lRow;
        flush();
      } else {
        $lContent.= $lRow;
      }
    }

    if (!CCor_Cfg::get('csv-exp.bymail', true)) {
      return;
    } else {
      return $lContent;
    }
  }

  /**
   * *.XLS content
   *
   * @return boolean|string
   */
  public function getExcel() {
    $lXls = new CApi_Xls_Writer();

    $this -> removeColumn('ctr');
    $this -> removeColumn('del');
    $this -> removeColumn('cpy');
    $this -> removeColumn('mor');

    foreach ($this -> mCols as $this -> mColKey => $this -> mCol) {
      if ($this -> mCol -> isHidden()) {
         continue;
      }

      $lXls -> addField($this -> mColKey, $this -> mCol -> getCaption());
    }

    $lXls -> writeCaptions();
    $lXls -> switchStyle();

    $lFla = CCor_Res::extract('val', 'name_'.LAN, 'jfl');
    $lVal = '';
    foreach ($this -> mIte as $this -> mRow) {
      foreach ($this -> mCols as $this -> mColKey => & $this -> mCol) {
        if ($this -> mCol -> isHidden()) {
          continue;
        }

        $this -> mCurCol = $this -> getColumn($this -> mColKey);
        $lTyp = $this -> mCurCol -> getFieldAttr('typ');

        if ($this -> mColKey == 'src') {
          $lSrc = $this -> getVal($this -> mColKey);
          $lVal = lan('job-'.$lSrc.'.menu');
        } elseif ($this -> mColKey == 'flags') {
          $lVal = '';
          $lCur = $this -> getCurInt();
          foreach ($lFla as $lKeyFla => $lValFla) {
            if (bitSet($lCur, $lKeyFla)) {
              $lVal = cat($lVal, $lValFla);
            }
          }
        } elseif ($lTyp == 'boolean') {
          $lVal = $this -> getVal($this -> mColKey);
        } elseif ($lTyp != 'file' && $lTyp != 'image') {
          $lVal = $this -> getVal($this -> mColKey);
          $lTemp = $this -> getColtd();
          $lTemp = strip_tags($lTemp);
          $lTemp = str_replace('&nbsp;', '', $lTemp);
          $lTemp = dehtm(trim($lTemp));
          $lVal = $lTemp;
        }

        $lXls -> writeAsString($lVal);
      }
      $lXls -> newLine();
      $this -> mCtr++;
      $lXls -> switchStyle();
    }

    return $lXls;
  }

  /**
   * *.CSV content
   *
   * @return boolean|string
   */
  public function getRepContent() {
  	$lLan = LAN;
  	$lCols = array();
  	$lMapper = CCor_Cfg::get('report.map');

  	//Display headers for each date
  	if (!CCor_Cfg::get('rep-exp.bymail', true)) {
  		$lQry = new CCor_Qry('SHOW COLUMNS FROM al_job_shadow_'.MID.'_report');
  		foreach ($lQry as $lRow) {
  			if(strpos($lRow['Field'], 'fti_cr_') !== FALSE){
  				$lCols[] = str_replace('fti_cr_', '', $lRow['Field']);
  			}
  		}

		$lHeader = "sep=;".CR.LF;
		$lHeader.= '"JobNo";"Row";';
  		foreach ($lCols as $lKey => & $lVal) {
  			$lKey = array_search ($lVal, $lMapper);
  			$lHeader.= '"'.$lKey.'";';
  		}

  		echo $lHeader.CR.LF;
  		flush();
  	} else {
  		$lContent = '';
  	}


  	foreach ($this -> mIte as $this -> mRow) {
  		$lJobnr = $this -> mRow['job_id'];
  		$lRow = '';
  		$lFields = array('jobid','row_id');
  		foreach ($lCols as $lKey => & $lVal) {
  			$lFields[] = 'fti_cr_'.$lVal;
  			$lFields[] = 'lti_cr_'.$lVal;
  		}

  		$lQry = new CCor_Qry('SELECT '.implode(",", $lFields).' FROM al_job_shadow_'.MID.'_report WHERE jobid='.esc($this -> mRow['jobid']).' ORDER BY jobid,id ASC;');
	    foreach ($lQry as $lRows) {
  			$lRow .= '"'.$this -> mRow['jobid'].'";"'.$lRows['row_id'].'";';

	    	foreach ($lCols as $lKey => & $lVal) {
	    		$lLti = $lFti = FALSE;
	    		/*if(strtotime($lRows['lti_cr_'.$lVal]) !== FALSE){
	    			$lRow.= $lRows['lti_cr_'.$lVal] . ";";
	    			$lLti = TRUE;
	    		}*/

	    		if($lRows['fti_cr_'.$lVal] !== '0000-00-00 00:00:00' && $lLti === FALSE) {
	    			$lRow.= $lRows['fti_cr_'.$lVal] . ";";
	    			$lFti = TRUE;
	    		}

	    		if($lLti === FALSE && $lFti === FALSE){
	    			$lRow.= ";";
	    		}
	    	}
	    	$lRow .= CR.LF;
	    }

  		if (!CCor_Cfg::get('rep-exp.bymail', true)) {
  			echo $lRow;
  			flush();
  		} else {
  			$lContent.= $lRow;
  		}
  	}

  	if (!CCor_Cfg::get('rep-exp.bymail', true)) {
  		return;
  	} else {
  		return $lContent;
  	}
  }


  /**
  * Remove Field from Fieldlist
  * @param string $aField Alias name of Field
  */
  public function removeColumn($aField) {
    foreach ($this -> mCols as $key => $col) {
      if ($key == $aField) {
        unset($this -> mCols[$key]);
      }
    }
  }

 /**
  * Set Hihlight
  * @param string $aDeadline Datum Wert
  * return boolean If Highlight, get TRUE
  */
  protected function getHighlight($aDeadline) {
    $lRet= FALSE;
    $lDdlDate = $aDeadline;
    if ($lDdlDate <= $this -> mEndDate) {
      $lRet = TRUE;

    }
    if (!empty($this -> mStartDate) AND $lDdlDate < $this -> mStartDate) {
      $lRet = FALSE;

    }
    return $lRet;
  }

}