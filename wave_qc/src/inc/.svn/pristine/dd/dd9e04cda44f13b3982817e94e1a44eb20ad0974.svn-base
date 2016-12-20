<?php
class CInc_Job_Multi_Ord_Cnt extends CCor_Cnt {

  protected $mJobFields = NULL;

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('job.multiple-edit.menu');
    $this -> mMmKey = 'opt';

    $lPriv = 'job.multiple-edit';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lPriv)) {
      $this -> setProtection('*', $lPriv, rdNone);
    }

    $this -> mJobFields = CCor_Res::extract('id', 'name_'.LAN, 'fie', array('flags' => 4096));
  }

  protected function actStd() {
    $lMenu = new CJob_Multi_Menu('ord');
    $lForm = new CJob_Multi_Ord_Form();
    $this -> render(CHtm_Wrap::wrap($lMenu, $lForm));
  }

  protected function getStdUrl() {
    return 'index.php?act=job-multi-ord';
  }

  protected function actGetJobFieldsOrderByAlphabet() {
    if (empty($this -> mJobFields)) {
      exit;
    }

    $lRet = '';
    $lRet.= '<ul id="job-multi-ord.items">'.LF;
    foreach ($this -> mJobFields as $lKey => $lValue) {
      $lRet.= '  <li class="ui-state-default">';
      $lRet.= '    <span class="ui-icon ui-icon-arrowthick-2-n-s" data-value="'.$lKey.'"></span>'.$lValue.LF;
      $lRet.= '  </li>'.LF;
    }
    $lRet.= '</ul>'.LF;
    echo $lRet;
    exit;
  }

  protected function actGetJobFieldsOrderBySystem() {
    if (empty($this -> mJobFields)) {
      exit;
    }

    $lJobFieldsCSV = CCor_Qry::getStr("SELECT val FROM al_sys_pref WHERE code='job.multiple-edit.ord' AND mand=".MID.";");
    if (empty($lJobFieldsCSV)) {
      exit;
    }

    $lJobFieldsArr = explode(',', $lJobFieldsCSV);

    $lRet = '';
    $lRet.= '<ul id="job-multi-ord.items">'.LF;
    foreach ($lJobFieldsArr as $lKey => $lValue) {
      $lRet.= '  <li class="ui-state-default">';
      $lRet.= '    <span class="ui-icon ui-icon-arrowthick-2-n-s" data-value="'.$lValue.'"></span>'.$this -> mJobFields[$lValue].LF;
      $lRet.= '  </li>'.LF;
    }
    $lRet.= '</ul>'.LF;
    echo $lRet;
    exit;
  }

  protected function actOrd() {
    $lMen = new CJob_Multi_Menu('ord');

    $lVie = new CJob_Multi_Ord_Form();
    $this -> render(CHtm_Wrap::wrap($lMen, $lVie));
  }

  protected function actSord() {
    $lOrd = $this -> mReq -> getVal('ord');

    if (!empty($lOrd)) {
      $lQry = new CCor_Qry();
      $lSql = 'DELETE FROM al_sys_pref WHERE mand='.MID.' AND code="job.multiple-edit.ord";';
      $lQry -> query($lSql);

      $lQry = new CCor_Qry();
      $lSql = 'INSERT INTO al_sys_pref SET mand='.MID.', val="'.$lOrd.'", code="job.multiple-edit.ord";';
      $lQry -> query($lSql);
    }

    $this -> redirect('index.php?act=job-multi.ord');
  }
}