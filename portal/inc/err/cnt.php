<?php
class CInc_Err_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = 'Error Test';
  }

  protected function actStd() {
    $lMsg = CCor_Msg::getInstance();
    $lMsg -> addMsg('Test User Info', mtUser, mlInfo);
    $lMsg -> addMsg('Test User Warn', mtUser, mlWarn);
    $lMsg -> addMsg('Test User Error', mtUser, mlError);
    $lMsg -> addMsg('Test User Fatal', mtUser, mlFatal);

    $lMsg -> addMsg('Test Debug Info', mtDebug, mlInfo);
    $lMsg -> addMsg('Test Debug Warn', mtDebug, mlWarn);
    $lMsg -> addMsg('Test Debug Error', mtDebug, mlError);
    $lMsg -> addMsg('Test Debug Fatal', mtDebug, mlFatal);

    $lMsg -> addMsg('Test PHP Info', mtPhp, mlInfo);
    $lMsg -> addMsg('Test PHP Warn', mtPhp, mlWarn);
    $lMsg -> addMsg('Test PHP Error', mtPhp, mlError);
    $lMsg -> addMsg('Test PHP Fatal', mtPhp, mlFatal);

    $lMsg -> addMsg('Test Sql Info', mtSql, mlInfo);
    $lMsg -> addMsg('Test Sql Warn', mtSql, mlWarn);
    $lMsg -> addMsg('Test Sql Error', mtSql, mlError);
    $lMsg -> addMsg('Test Sql Fatal', mtSql, mlFatal);

    $lMsg -> addMsg('Test Api Info', mtApi, mlInfo);
    $lMsg -> addMsg('Test Api Warn', mtApi, mlWarn);
    $lMsg -> addMsg('Test Api Error', mtApi, mlError);
    $lMsg -> addMsg('Test Api Fatal', mtApi, mlFatal);

    $this -> redirect('index.php?act=act');
  }

}