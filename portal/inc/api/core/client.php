<?php
class CInc_Api_Core_Client extends CCor_Obj {

    public function __construct() {
        $this->resetOptions();
    }

    public function getTransport() {
        if (!isset($this->mTrans)) {
            $this->mTrans = $this->createTransport();
        }
        return $this->mTrans;
    }

    protected function createTransport() {
        $lWsdl = $this->getWsdlLocation();
        $lClient = new Zend_Soap_Client($lWsdl, $this->mOpt);
        return $lClient;
    }

    public function getWsdlLocation() {
        $lRet = CCor_Cfg::get('core.wsdl', 'SalesOrderCreate.wsdl');
        if (file_exists($lRet)) {
            return $lRet;
        }
        $lFile = pathinfo(__FILE__);
        $lRet = $lFile['dirname'].DS.'data'.DS.$lRet;
        return $lRet;
    }

    public function resetOptions() {
        $this->mOpt = array();
        $this->setOption('soap_version', SOAP_1_1);
        //$this->setOption('trace', 1);
        //$this->setOption('default_socket_timeout', 120);
        return $this;
    }


    public function setOption($aOption, $aValue) {
        if (empty($aOption)) {
            throw new InvalidArgumentException('Cannot set empty option');
        }
        $this->mOpt[$aOption] = $aValue;
        $this->mTrans = NULL;
        return $this;
    }

    public function getOption($aOption, $aDefault = NULL) {
        return (isset($this->mOpt[$aOption])) ? $this->mOpt[$aOption] : $aDefault;
    }

    public function setLogin($aUsername, $aPassword) {
        $this->setOption('login', $aUsername);
        $this->setOption('password', $aPassword);
        return $this;
    }

    public function loadAuthFromConfig() {
        $lUser = CCor_Cfg::get('core.user', 'INT_WAVE_PD1');
        $lPass = CCor_Cfg::get('core.pass', 'Sch@Wk2Pd!');
        $this->setLogin($lUser, $lPass);
        return $this;
    }

    public function __call($aMethod, $aParams) {
        return $this->query($aMethod, $aParams);
    }

    public function query($aMethod, $aParams) {
        try {
            $this->msg('Core: REQ '.$aMethod, mtApi, mlInfo);
            $lTrans = $this->getTransport();
            $lRet = $lTrans->$aMethod($aParams);
            $this->msg('Core: REQ '.$aMethod.' / '.$lTrans->getLastRequest(), mtApi, mlInfo);
            $this->msg('Core: RES '.$aMethod.' / '.$lTrans->getLastResponse(), mtApi, mlInfo);
            return $lRet;
        } catch (Exception $ex) {
            $this->msg('Core: REQ '.$aMethod.' / '.$lTrans->getLastRequest(), mtApi, mlInfo);
            $this->msg('Core: '.$aMethod.' / '.$ex->getMessage(), mtApi, mlError);
            return false;
        }
    }


}
