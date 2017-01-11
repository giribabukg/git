<?php
class CInc_Api_Core_Map extends CCor_Obj {

    /**
     * @var CApi_Core_Xml
     */
    protected $mXml;

    /**
     * @var array
     */
    protected static $mMap;

    /**
     * @var array
     */
    protected static $mFie;

    public function __construct($aXml) {
        $this->setXml($aXml);
        $this->loadJobFields();
    }

    public function setXml($aXml) {
        $this->mXml = new CApi_Core_Xml($aXml);
    }

    protected function addError($aErrorText) {
        $this->msg($aErrorText, mtApi, mlError);
    }

    public function getMap($aMapCode) {
        if (!isset(self::$mMap[$aMapCode])) {
            if ('all' == $aMapCode) {
                self::$mMap[$aMapCode] = array_keys(self::$mFie);
            } else {
                $lRes = CCor_Res::extract('alias', 'alias', 'fiemap', $aMapCode);
                self::$mMap[$aMapCode] = array_keys($lRes);
            }
        }
        return self::$mMap[$aMapCode];
    }

    protected function loadJobFields() {
        self::$mFie = array();
        $lRows = CCor_Res::extract('alias', 'native_core', 'fie');
        foreach ($lRows as $lAlias => $lNative) {
            if (!empty($lNative)) {
                self::$mFie[$lAlias] = $lNative;
            }
        }
    }

    public function getValue($aAlias, $aApplyFilters = true) {
        if (!isset(self::$mFie[$aAlias])) {
            $this->addError('Unknown Alias '.$aAlias);
            return null;
        }
        $lNative = self::$mFie[$aAlias];
        //echo 'Native is '.$lNative;
        return $this->mXml->getValue($lNative, $aApplyFilters);
    }

    public function getValueByNative($aNative, $aApplyFilters = true) {
        return $this->mXml->getValue($aNative, $aApplyFilters);
    }

    public function getMappedValues($aMap, $aApplyFilters = true) {
        $lRet = new CCor_Dat();
        $lMap = $this->getMap($aMap);
        foreach ($lMap as $lAlias) {
            //echo 'Get value for '.$lAlias.BR;
            $lVal = $this->getValue($lAlias, $aApplyFilters);
            if (!is_null($lVal)) {
                $lRet[$lAlias] = $lVal;
            }
        }
        return $lRet;
    }

}
