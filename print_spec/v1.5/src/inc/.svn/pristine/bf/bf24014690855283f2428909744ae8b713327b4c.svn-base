<?php
class CInc_Utl_Ctr_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = htm(lan('ctr.menue'));
  }

  protected function actStd() {
    $lPic = new CUtl_Ctr_Picker($this -> mReq);

    $lPag = new CUtl_Page();
    $lPag -> setPat('pg.cont', $lPic -> getContent());
    $lPag -> setPat('pg.title', $this -> mTitle);
    $lPag -> setPat('pg.lib.wait', htm(lan('lib.wait')));

    echo $lPag -> getContent();
    //exit;
  }

  public function actDel() {
    $lCod = $this -> getReq('id');
    $lSel = $this -> getReq('sel');
    $lSql = "DELETE FROM al_fie_choice where id=".$lCod;
    CCor_Qry::exec($lSql);

    $this -> redirect('index.php?act=utl-ctr&dom=ctr&lis=laendervariante&sel='.$lSel);
  }

}