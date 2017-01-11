<?php
class CInc_Cnd_Cnt extends CCor_Cnt {

  /**
   * Constructor
   *
   * @access public
   * @param $aReq
   * @param $aMod
   * @param $aAct
   */
  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);

    $this -> mTitle = lan('cnd.title');

    $lModule = 'cnd';
    $lUser = CCor_Usr::getInstance();
    if (!$lUser -> canRead($lModule)) {
      $this -> setProtection('*', $lModule, rdNone);
    }
  }

  /**
   * Action: Standard
   *
   * @access protected
   */
  protected function actStd() {
    $lVie = new CCnd_List();
    $this -> render($lVie);
  }

  /**
   * Action: Edit
   *
   * @access protected
   */
  protected function actEdt(){
    $lId = $this -> getReqInt('id');

    $lVie = new CCnd_Form_Edit($lId);
    $this -> render($lVie);
  }

  /**
   * Action: Edit
   *
   * @access protected
   */
  protected function actSedt() {
    $lMod = new CCnd_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  /**
   * Action: New
   *
   * @access protected
   */
  protected function actNew() {
    $lVie = new CCnd_Form_Base('cnd.snew', lan('cnd.new'));
    $this -> render($lVie);
  }

  /**
   * Action: New
   *
   * @access protected
   */
  protected function actSnew() {
    $lMod = new CCnd_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> insert();
    $this -> redirect();
  }

  /**
   * Action: Delete
   *
   * @access protected
   */
  protected function actDel() {
    $lId = $this -> getReqInt('id');

    $lFlags = CCor_Qry::getInt("SELECT flags FROM al_cnd_master WHERE id=".addslashes($lId));
    if (($lFlags & 4) > 0) {
      CCor_Qry::exec("DELETE FROM al_cnd WHERE mand=".addslashes(MID)." AND pro_id=".addslashes($lId)." AND cnd_id=".addslashes($lId));
    }

    CCor_Qry::exec('DELETE FROM al_cnd_master WHERE id='.addslashes($lId));
    CCor_Qry::exec('DELETE FROM al_cnd_items WHERE cnd_id='.addslashes($lId));

    CCor_Cache::clearStatic('cor_res_cndmaster_'.MID);

    $this -> redirect();
  }

}