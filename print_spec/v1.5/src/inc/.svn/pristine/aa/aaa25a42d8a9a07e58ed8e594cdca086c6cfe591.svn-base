<?php
class CInc_Cnd_Itm_Cnt extends CCor_Cnt {

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

    $this -> mTitle = lan('cnd-itm.title');
    $this -> mCndId = $this -> getReq('cnd_id');

    $lModule = 'cnd';
    $lUser = CCor_Usr::getInstance();
    if (!$lUser -> canRead($lModule)) {
      $this -> setProtection('*', $lModule, rdNone);
    }
  }

  /**
   * Action: StandardURL
   *
   * @access protected
   */
  protected function getStdUrl() {
    return 'index.php?act=cnd-itm&cnd_id='.$this -> mCndId;
  }

  /**
   * Action: Standard
   *
   * @access protected
   */
  protected function actStd() {
    $lVie = new CCnd_Itm_List($this -> mCndId);
    $this -> render($lVie);
  }

  /**
   * Action: Edit
   *
   * @access protected
   */
  protected function actEdt() {
    $lId = $this -> getReqInt('id');
    $lVie = new CCnd_Itm_Form_Edit($lId, $this -> mCndId);
    $this -> render($lVie);
  }

  /**
   * Action: Edit
   *
   * @access protected
   */
  protected function actSedt() {
    $lMod = new CCnd_Itm_Mod();
    $lMod -> getPost($this -> mReq);
    if ($lMod -> hasChanged()) {
      $lMod -> update();
    }
    $this -> redirect();
  }

  /**
   * Action: New
   *
   * @access protected
   */
  protected function actNew() {
    $lVie = new CCnd_Itm_Form_Base('cnd-itm.snew', lan('cnd-itm.new'), 'cnd-itm&cnd_id='.$this -> mCndId);
    $lVie -> setCndId($this -> mCndId);
    $this -> render($lVie);
  }

  /**
   * Action: New
   *
   * @access protected
   */
  protected function actSnew() {
    $lMod = new CCnd_Itm_Mod();
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

    CCor_Qry::exec('DELETE FROM al_cnd_items WHERE id='.addslashes($lId));
    CCnd_Itm_Mod::createCondition($this -> mCndId);

    $this -> redirect();
  }

}