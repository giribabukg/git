<?php
class CInc_Api_Core_Xml extends CCor_Obj {

    /**
     * @var string
     */
    protected $mXml;

    /**
     * @var DOMDocument
     */
    protected $mDoc;

    /**
     * @var DOMXpath
     */
    protected $mXpath;

    /**
     * @var array
     */
    protected static $mDictionaries;


    /**
     * @var bool Have we got a valid XML document
     */
    protected $mIsValid;

    public function __construct($aXml = null) {
        if (!is_null($aXml)) {
            $this->setXml($aXml);
        }
        $this->setDictionary('core.xml');
    }

    protected function addError($aErrorText) {
        $this->msg($aErrorText, mtApi, mlError);
    }

    public function setXml($aXml) {
        $this->mXml = $aXml;
        $this->mDoc = new DOMDocument;
        $this->mDoc->preserveWhiteSpace = false;
        $lRes = @$this->mDoc->loadXML($this->mXml);
        $this->mIsValid = !!$lRes; // make sure we have a bool
        if (!$this->mIsValid) {
            $this->addError('Error parsing XML');
            return;
        }
        $this->mXpath = new DOMXPath($this->mDoc);
    }

    public function isValid() {
        return $this->mIsValid;
    }

    public function setDictionary($aFieldMapName) {
        if (isset(self::$mDictionaries[$aFieldMapName])) {
            $this->mDic = self::$mDictionaries[$aFieldMapName];
            return;
        }
        $this->mDic = array();
        $lRows = CCor_Res::getByKey('alias', 'fiemap', $aFieldMapName);
        foreach ($lRows as $lAlias => $lRow) {
            $lItm['native'] = $lRow['native'];
            $lItm['read_filter'] = $lRow['read_filter'];
            $this->mDic[$lAlias] = $lItm;
        }
        self::$mDictionaries[$aFieldMapName] = $this->mDic;
        //var_dump($this->mDic);
    }

    public function getRawNodeValue($aXpathQuery) {
        $lRet = null;
        $lNodes = $this->mXpath->query($aXpathQuery);
        if ($lNodes->length == 1) {
            $lRet = $lNodes->item(0)->value;
            $lRet = html_entity_decode($lRet);
        }
        return $lRet;
    }

    public function getValue($aNative, $aApplyFilter = true) {
        if (!isset($this->mDic[$aNative])) {
            return null;
        }
        $lEntry = $this->mDic[$aNative];
        $lVal = $this->getRawNodeValue($lEntry['native']);
        if (!$aApplyFilter) {
            return $lVal;
        }
        if (!empty($lEntry['read_filter'])) {
            //$this->dbg('Filtering '.$aNative.' '.$lVal.' with '.$lEntry['read_filter']);
            $lVal = CApp_Filter::filter($lVal, $lEntry['read_filter']);
        }
        return $lVal;
    }

    public function getAllValues($aApplyFilter = true) {
        $lRet = array();
        foreach ($this->mDic as $lNative => $lRow) {
            $lRet[$lNative] = $this->getValue($lNative, $aApplyFilter);
        }
        return $lRet;
    }

 }
