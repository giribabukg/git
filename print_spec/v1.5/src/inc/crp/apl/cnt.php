<?php
class CCrp_Apl_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('lib.status');
    $this -> mMmKey = 'opt'; // Highlight von Hauptmenu : Options

    $lpn = 'crp';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      $this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lCrp = $this -> getReqInt('id');
    
    $lMen = new CCrp_Menu($lCrp, 'apl');
    $lVie = new CCrp_Apl_List();
    $this -> render(CHtm_Wrap::wrap($lMen,$lVie));
  }
  
  protected function act_Std() {
    $lCrp = $this -> getReqInt('id');
    
    $lMen = new CCrp_Menu($lCrp, 'apl');
    $lVie = new CHtm_Fpr($this -> mMod.'.sfpr');
    $lDefFie = CCor_Res::get('fie');

    $lArr = array();
    $lGruArr = array();
    $lUsrArr = array();
    foreach ($lDefFie as $lFie) {
      if ($lFie['typ'] == 'gselect') {
        if (!empty($lFie['name_'.LAN])) {
          $lGruArr[$lFie['alias']] = $lFie['name_'.LAN];
        }
      }
      if ($lFie['typ'] == 'uselect') {
        if (!empty($lFie['name_'.LAN])) {
          $lUsrArr[$lFie['alias']] = $lFie['name_'.LAN];
        }
      }
    }
    $lArr = $lGruArr + $lUsrArr;
    #echo '<pre>---cnt.php---'.get_class().'---';var_dump($lArr,'#############');echo '</pre>';
    $lVie -> setSrc($lArr);
    $lUsr = CCor_Usr::getInstance();
    $lVie -> setSel($lUsr -> getPref($this -> mPrf.'.cols'));

    $this -> render(CHtm_Wrap::wrap($lMen,$lVie));
  }

  protected function actSfpr() {
    $lDst = $this -> mReq -> getVal('dst');
    $lUsr = CCor_Usr::getInstance();
    if (!empty($lDst)) {
      $lDstStr = implode(',', $lDst);
    } else {
      $lDstStr = '';
    }
    $lUsr -> setPref($this -> mPrf.'.cols', $lDstStr);
    $this -> redirect();
  }
  
}