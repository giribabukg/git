<?php

class CInc_Fie_Validate_List extends CHtm_List {


  public function __construct() {
    parent::__construct('fie-validate');

    $this->setAtt('width', '100%');
    $this->mTitle = lan('fie-validate.menu');

    $this->addColumn('ctr');
    $this->addColumn('name', lan('lib.name'), true);
    $this->addColumn('alias', lan('fie.alias'), true);
    $this->addColumn('mand', lan('lib.mand'), true);
    $this->addColumn('validate_type', lan('fie-validate.function'), true);
    $this->addColumn('params', lan('lib.param'), false);
    if ($this->mCanInsert) {
      $this->addCpy();
    }
    if ($this->mCanDelete) {
      $this->addDel();
    }
    $this->mIsGlobal = CFie_Validate_Mod::areWeOnGlobal();

    if ($this->mCanInsert) {
      $this->addBtn(lan('fie-validate.new'), "go('index.php?act=fie-validate.new')", '<i class="ico-w16 ico-w16-plus"></i>');
    }

    $this->mLpp = 25;
    $this->getPrefs();

    $this->mIte = new CCor_TblIte('al_fie_validate');
    $this->mIte->addCnd('mand IN(0,' . intval(MID) .','.CFie_Validate_Mod::WAVE_GLOBAL.')');

    if (!empty($this->mSer)) {
      if (!empty($this->mSer['name'])) {
        $lVal = esc('%' . $this->mSer['name'] . '%');
        $lCnd = '(name LIKE ' . $lVal . ' OR ';
        $lCnd .= 'validate_type LIKE ' . $lVal . ')';
        $this->mIte->addCnd($lCnd);
      }
    }

    $this->mIte->setOrder($this->mOrd, $this->mDir);
    $this->mIte->setLimit($this->mPage * $this->mLpp, $this->mLpp);
    $this->mMaxLines = $this->mIte->getCount();

    $this->mIte = $this->mIte->getArray('id');
    $this->loadParams();

    $this->addPanel('nav', $this->getNavBar());
    $this->addPanel('vie', $this->getViewMenu());
    $this->addPanel('sca', '| ' . htmlan('lib.search'));
    $this->addPanel('ser', $this->getSearchForm());
  }

  protected function loadParams() {
    $this->mVparams = array();
    if (empty($this->mIte)) {
      return;
    }
    $lIds = array_keys($this->mIte);
    $lSql = 'SELECT * FROM al_fie_validate_options WHERE validate_id IN (';
    foreach ($lIds as $lId) {
      $lSql .= esc($lId) . ',';
    }
    $lSql = strip($lSql);
    $lSql .= ')';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this->mVparams[$lRow['validate_id']][$lRow['option_name']] = $lRow['option_value'];
    }
  }

  protected function getLink() {
    $lMand = $this->getInt('mand');
    if (($lMand == CFie_Validate_Mod::WAVE_GLOBAL) && (!$this->mIsGlobal)) {
      return '';
    }
    return parent::getLink();
  }

  protected function getSearchForm() {
    $lRet = '';
    $lRet .= '<form action="index.php" method="post">' . LF;
    $lRet .= '<input type="hidden" name="act" value="fie-validate.ser" />' . LF;
    $lRet .= '<table cellpadding="2" cellspacing="0" border="0"><tr>' . LF;
    $lVal = (isset($this->mSer['name'])) ? htm($this->mSer['name']) : '';
    $lRet .= '<td><input type="text" name="val[name]" class="inp" value="' . $lVal . '" /></td>' . LF;
    $lRet .= '<td>' . btn(lan('lib.search'), '', '', 'submit') . '</td>';
    if (!empty($this->mSer)) {
      $lRet .= '<td>' . btn(lan('lib.show_all'), 'go("index.php?act=fie-validate.clser")') . '</td>';
    }
    $lRet .= '</tr></table>';
    $lRet .= '</form>' . LF;

    return $lRet;
  }

  protected function getTdTyp() {
    $lTyp = $this->getVal('typ');
    $lTxt = $this->mReg->typeToString($lTyp);
    return $this->tda(htm($lTxt));
  }

  protected function getTdParams() {
    $lId = $this->getVal('id');
    $lTxt = '';
    if (!empty($this->mVparams[$lId])) {
      $lParts = array();
      $lParams = $this->mVparams[$lId];
      foreach ($lParams as $lKey => $lVal) {
        $lVal = htm($lVal);
        $lVal = shortStr($lVal);
        $lParts[] = htm(ucfirst($lKey)) . ': ' . $lVal;
      }
      $lTxt = implode(', ', $lParts);
    }
    return $this->tda($lTxt);
  }

  protected function getTdMand() {
    $lVal = $this -> getVal($this -> mColKey);
    if (empty($lVal)) {
      $lRet = "Global";
    } elseif ($lVal == -1) {
      $lRet = lan('lib.mand.all');
    } elseif ($lVal == CFie_Validate_Mod::WAVE_GLOBAL) {
      $lRet = 'WAVE GLOBAL';
    } else {
      $lArr = CCor_Res::extract('id', 'name_'.LAN, 'mand');
      $lRet = $lArr[$lVal];
    }
    $lCls = '';
    if (($lVal == CFie_Validate_Mod::WAVE_GLOBAL) && !$this->mIsGlobal) {
      $lCls.= ' cy';
    }

    $lRet = $this ->tdClass($this -> a(htm($lRet)), $lCls, true);
    return $lRet;
  }

  protected function getTdDel() {
    $lMand = $this->getInt('mand');
    if (($lMand == CFie_Validate_Mod::WAVE_GLOBAL) && (!$this->mIsGlobal)) {
      return $this->td();
    }
    return parent::getTdDel();
  }

}
