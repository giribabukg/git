<?php
class CInc_Api_Core_Query extends CCor_Obj {

    public function __construct($aClient = null) {
        $this->mClient = $aClient;
        $this->mParam = array();
        $this->init();
    }

    protected function init() {
    }

    public function setClient($aClient) {
        $this->mClient = $aClient;
    }

    public function getClient() {
        if (!isset($this->mClient)) {
            $this->mClient = new CApi_Core_Client();
            $this->mClient->loadAuthFromConfig();
        }
        return $this->mClient;
    }

    public function setParam($aKey, $aVal) {
        $this -> mParam[$aKey] = $aVal;
    }

    public function setParamPath($aPath, $aVal) {
        if ($aVal == 'false') $aVal = false;
        $lPath = $aPath;
        if (substr($lPath, 0, 1) == '/') {
            $lPath = substr($lPath, 1);
        }
        $lParts = explode('/', $lPath);
        $lCur = &$this->mParam;
        foreach ($lParts as $lPart) {
            if (!isset($lCur[$lPart])) {
                $lCur[$lPart] = array();
            }
            $lCur = &$lCur[$lPart];
        }
        $lCur = $aVal;
    }

    public function addParamPath($aPath, $aVal) {
        $lPath = $aPath;
        if (substr($lPath, 0, 1) == '/') {
            $lPath = substr($lPath, 1);
        }
        $lParts = explode('/', $lPath);
        $lCur = &$this->mParam;
        foreach ($lParts as $lPart) {
            if (!isset($lCur[$lPart])) {
                $lCur[$lPart] = array();
            }
            $lCur = &$lCur[$lPart];
        }
        $lCur[] = $aVal;
    }

    public function dumpParam() {
        //var_dump($this->mParam); exit;
    }

    public function getParam($aKey, $aStd = NULL) {
        return (isset($this -> mParam[$aKey])) ? $this -> mParam[$aKey] : $aStd;
    }

    protected function hasPath($aRes, $aPath) {
        $lPath = (is_array($aPath)) ? $aPath : explode('.', $aPath);
        if (!$aRes) return false;
        $lRoot = $aRes;
        foreach ($lPath as $lKey) {
            if (!$lRoot || !$lRoot->$lKey)  {
                $this->dbg('Result does not have '.$lKey, mlError);
                return false;
            }
            $lRoot = $lRoot->$lKey;
        }
        return true;
    }

    public function query() {
        $lClient = $this->getClient();
        $lRet = $lClient->query($this->mMethod, $this->mParam);
        return $lRet;
    }

}
