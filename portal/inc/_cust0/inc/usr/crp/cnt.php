<?php
/**
 * Customer - Useradministration: Critical Path - Controller
 *
 * Description
 *
 * @package    USR
 * @subpackage    Critical Path
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 09:50:56 +0100 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CCust_Usr_Crp_Cnt extends CInc_Usr_Crp_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('lib.priv');
  }

  protected function actStd() {
    $lUid = $this -> getReqInt('id');
    $lCrp = $this -> getReqInt('crp');
    $lMen = new CUsr_Menu($lUid, 'crp');
    $lMen -> setSubKey('crp_'.$lCrp);
    $lFrm = new CUsr_Crp_Form($lUid, $lCrp);
    $this -> render(CHtm_Wrap::wrap($lMen, $lFrm));
  }

  protected function actSedt() {
    $lUid = $this -> getReqInt('id');
    $lCrp = $this -> getReqInt('crp');

    $lMod = new CUsr_Crp_Mod();
    $lMod -> getPost($this -> mReq);

    $this -> redirect('index.php?act=usr-crp&id='.$lUid.'&crp='.$lCrp);
  }


}