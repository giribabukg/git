<?php
class CInc_Health_Cnt extends CCor_Cnt {
    
    protected function actStd() {
        $lLis = new CHealth_List();
        $lLis->addJs();
        
        $lMsg = new CHealth_Messagelist();
        
        $this->render(CHtm_Wrap::wrap($lLis, $lMsg));
    }
    
    protected function actTest() {
        $lRunner = new CHealth_Runner();
        $lRes = $lRunner->runAll();
        #$lRes = exec('whoami');
        $this->redirect();
    }
    
    protected function actTestsystem() {
        $lSysId = $this->getInt('id');
        $lRunner = new CHealth_Runner();
        $lRes = $lRunner->runSystem($lSysId);
        
        $lRet = array('1' => 'error', 5 => 'warn');
        echo Zend_Json::encode($lRet);
    }
    
    protected function actRefresh() {
        $lRunner = new CHealth_Runner();
        $lRet = $lRunner->getStates();
        $lRet['states'] = $lRunner->getStates();
        #$lRes = exec('whoami');
        $lMsg = new CHealth_Messagelist();
        $lRet['msg'] = $lMsg->getContent();
        echo Zend_Json::encode($lRet);
        exit;
    }
    
    protected function actRunall() {
      $lRunner = new CHealth_Runner();
      $lRunner->runAll();
      $lRet['states'] = $lRunner->getStates();
      $lMsg = new CHealth_Messagelist();
      $lRet['msg'] = $lMsg->getContent();
      #$lRes = exec('whoami');
      echo Zend_Json::encode($lRet);
      exit;
    }
    
    

}