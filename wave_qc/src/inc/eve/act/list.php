<?php
class CInc_Eve_Act_List extends CHtm_List {

  /**
   * Registry for action types
   *
   * @var CApp_Event_Action_Registry
   */
  protected $mReg;
  protected $mMaxPos = 0; // größte bisher vergebene Position

  public function __construct($aEventId) {
    parent::__construct('eve-act');
    $this -> mEve = intval($aEventId);
    $this -> setAtt('class', 'tbl w700');
    $this -> mTitle = lan('eve.act');

    $this -> getPriv('eve');

    $this -> mStdLnk = 'index.php?act=eve-act.edt&amp;id='.$this -> mEve.'&amp;sid=';
    $this -> mDelLnk = 'index.php?act=eve-act.del&amp;id='.$this -> mEve.'&amp;sid=';
    $this -> mNewLnk = 'index.php?act=eve-act.new&amp;id='.$this -> mEve.'&amp;typ=';

    #$this -> addColumn('up');
    $this -> addCtr();
    #$this -> addColumn('dn');
    $this -> addColumn('active', '', FALSE, array('width' => '16'));
    $this -> addColumn('pos', lan('lib.pos'));
    $this -> addColumn('typ', lan('lib.type'));
    $this -> addColumn('param', lan('lib.param'));
    $this -> addColumn('dur', lan('lib.duration'));
    #$this -> addColumn('per', 'Asssertion');
    $this -> mDefaultOrder = 'pos';

    $this -> addFooter('ctr', '', FALSE, array('class' => 'tfoot', 'colspan' => 5));
    #$this -> addFooter('active');
    #$this -> addFooter('pos');
    #$this -> addFooter('typ');
    #$this -> addFooter('param');
    $this -> addFooter('dur', '', FALSE, array('class' => 'tfoot'));
    $this -> mMakeFunction = array(
      'funct' => 'amount',
      'value' => 'dur',
      'restr' => array('pos', 'max')
    ); // Es sollen alle Werte von 'dur' addiert werden. Je 'pos' wird aber nur der groesste Wert aufsummiert.

    if ($this -> mCanDelete) {
      $this -> addDel();
      $this -> addFooter('del', '', FALSE, array('class' => 'tfoot'));

    }
    #echo '<pre>---list.php---'.get_class().'---';var_dump($this -> mFootCols,$this -> mFootColCnt,'#############');echo '</pre>';

    $this -> mReg = new CApp_Event_Action_Registry();
    $this -> addPanel('new', $this -> getNewMenu());

    $this -> getPrefs();

    $this -> mIte = new CCor_TblIte('al_eve_act', TRUE);
    $this -> mIte -> addCnd('eve_id='.$this -> mEve);
    $this -> mIte -> addCnd('mand='.MID);
    $this -> mIte -> setOrder($this -> mOrd, $this -> mDir);

    $this -> mIte -> setLimit($this -> mPage * $this -> mLpp, $this -> mLpp);
    $this -> mMaxLines = $this -> mIte -> getCount();
    #$this -> addPanel('vie', $this -> getViewMenu());
  }

  protected function getNewMenu() {
    $lMen = new CHtm_Menu(lan('eve.act.new'));
    $lMen -> addTh2(lan('lib.opt.typ'));
    $lTyp = $this -> mReg -> getActions();
    $lImg = 'ico/16/new-hi.gif';
    foreach ($lTyp as $lRow) {
      $lMen -> addItem($this -> mNewLnk.$lRow['type'], $lRow['name'], $lImg);
    }
    $lRet = '<div class="p8">'.$lMen -> getContent().'</div>';
    return $lRet;

  }

  protected function getTdActive() {
    $lVal = $this -> getCurVal();
    $lSid = $this -> getInt('id');
    $lRet = '<a href="index.php?act=eve-act.';
    if ($lVal) {
      $lRet.= 'deact&amp;id='.$this -> mEve.'&amp;sid='.$lSid.'" class="nav">';
      $lRet.= img('img/ico/16/flag-03.gif');
    } else {
      $lRet.= 'act&amp;id='.$this -> mEve.'&amp;sid='.$lSid.'" class="nav">';
      $lRet.= img('img/ico/16/flag-00.gif');
    }
    $lRet.= '</a>';
    return $this -> td($lRet);
  }

  protected function getTdTyp() {
    $lTyp = $this -> getCurVal();
    $lNam = $this -> mReg -> getName($lTyp);
    return $this -> td($this -> a($lNam, true));
  }

  protected function getTdParam() {
    $lTyp = $this -> getVal('typ');
    $lPar = toArr($this -> getCurVal());
    $lTxt = $this -> mReg -> paramToString($lTyp, $lPar);
    return $this -> td($this -> a($lTxt, true));
  }

  protected function getTdPos() {
    $lPos = $this -> getCurVal('pos');
    $lNam = $lPos + 1;
    if (101 == $lNam) $lNam = lan('lib.eve.deferred');
    return $this -> tdc($this -> a($lNam, true));
  }

  protected function getTdDur() {
    $lDur = $this -> getInt('dur');
    return $this -> tdc($this -> a($lDur, true));
  }

  protected function countFunction() {
    return CEve_Act_Cnt::countDurationTime($this -> mAllRows);
  }

}