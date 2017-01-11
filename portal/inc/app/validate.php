<?php
class CInc_App_Validate extends CCor_Obj {

    public static function getTypes() {
        $lRet = array();
        $lRet[] = 'int';
        $lRet[] = 'float';
        $lRet[] = 'string';
        $lRet[] = 'date';
        $lRet[] = 'enum';
        return $lRet;
    }

    public static function getAllTypeOptions() {
        $lRet = '';
        $lTypes = self::getTypes();

        foreach ($lTypes as $lType) {
            $lOpt = array();
            $lFunc = 'self::getOptions'.$lType;
            if (is_callable($lFunc)) {
                $lOpt = call_user_func(array(self, 'getOptions'.$lType));
            }
            $lRet[$lType] = $lOpt;
        }
        return $lRet;
    }

    public function __construct() {
        $this->rules = array();
    }

    public function loadRules() {
        $lSql = 'SELECT * FROM al_fie_core';
        $lQry = new CCor_Qry($lSql);
        foreach ($lQry as $lRow) {
            $this->rules[$lRow['name']] = $lRow;
        }
    }

    public function setRulesFromFields($aFields) {
        $lAllRules = CCor_Res::getByKey('id', 'validate');
        foreach ($aFields as $lRow) {
            $lValid = $lRow['validate_rule'];
            if (empty($lValid)) {
                continue;
            }
            $this->mFie[$lRow['alias']] = $lRow['name_'.LAN];
            if (!isset($lAllRules[$lValid])) {
                $this->msg('Unknown rule ID '.$lValid, mtDebug, mlError);
                continue;
            }
            $lRule = $lAllRules[$lValid];
            $lParams = isset($lRule['options']) ? $lRule['options'] : array();
            $this->addRule($lRow['alias'], $lRule['validate_type'], $lParams, $lRow['name']);
        }
    }

    public function addRule($aField, $aFunction, $aParamArray, $aFieldname = '') {
        $lRow = array();
        $lRow['validation_function'] = $aFunction;
        $lParams = $aParamArray;
        if (is_string($lParams)) {
            $lParams = unserialize($lParams);
        }
        $lRow['validation_params']   = $lParams;
        $lRow['field_name']   = is_null($aFieldname) ? $aField : $aFieldname;
        $this->rules[$aField] = $lRow;
    }

    protected function addError($aField, $aMessage) {
        $this->errors[$aField][] = $aMessage;
    }

    protected function getErrorFormat($aCode, $aArg = '') {
        $lMsg = lan('validate.'.$aCode);
        $lRet = sprintf($lMsg, $aArg);
        return $lRet;
    }

    protected function addErrorFormat($aField, $aCode, $aArg = '') {
        $lMsg = lan('validate.'.$aCode);
        $lRet = sprintf($lMsg, $aArg);
        $this->addError($aField, $lRet);
    }

    public function getErrorsFor($aField) {
        $lRet = array();
        if (isset($this->errors[$aField])) {
            $lRet = $this->errors[$aField];
        }
        return $lRet;
    }

    public function getAllErrors() {
        $lRet = array();
        if (empty($this->errors)) {
            return $lRet;
        }
        foreach ($this->errors as $lField => $lRows) {
            foreach ($lRows as $lRow) {
                $lName = isset($this->mFie[$lField]) ? $this->mFie[$lField] : $lField;
                $lRet[$lField] = $lName.': '.$lRow;
            }
        }
        return $lRet;
    }

    public function isValid($aArr, $aIgnoreEmpty = true) {
        $lRet = true;
        foreach ($aArr as $lKey => $lVal) {
            if ($aIgnoreEmpty && ('' == $lVal)) {
                $this->mValid[$lKey] = true;
                continue;
            }
            if (!isset($this->rules[$lKey])) {
                //$this->addWarning($lKey, 'No rule found for '.$lKey);
            } else {
                $lRule = $this->rules[$lKey];
                $lRes = $this->isValidField($lKey, $lVal, $lRule);
                if (!$lRes) {
                    $lRet = false;
                }
            }
        }
        //var_dump($this->errors);
        return $lRet;
    }

    public function isValidField($aName, $aValue, $aRule) {
        $lVfunc = $aRule['validation_function'];
        $lFunc = 'isValidType'.$lVfunc;
        if ($this->hasMethod($lFunc)) {
            $lParams = $aRule['validation_params'];
            if (is_string($lParams)) {
                $lParams = Zend_Json::decode($lParams);
            }
            $lRet = $this->$lFunc($aName, $aValue, $lParams);
        } else {
            $this->addError($aName, 'Unknown rule '.$lVfunc);
            $lRet = false;
        }
        $this->mValid[$aName] = $lRet;
        return $lRet;
    }

    public function wasValid($aName) {
        // avoid notice
        return isset($this->mValid[$aName]) ? $this->mValid[$aName] : null;
    }

    // int

    protected static function getOptionsInt() {
        $lRet = array();
        $lRet['min'] = fie('min', 'Min. value', 'int');
        $lRet['max'] = fie('max', 'Max. value', 'int');
        return $lRet;
    }

    protected function isValidTypeInt($aName, $aValue, $aParams) {
        $lVal = filter_var($aValue, FILTER_VALIDATE_INT);
        if (false === $lVal) {
            $this->addErrorFormat($aName, 'int.invalid', $aValue); // '%s is not a valid integer'
            return false;
        }
        $lVal = intval($aValue);
        if (strcmp($aValue, $lVal) !==0) {
            $this->addErrorFormat($aName, 'int.invalid', $aValue); // '%s is not a valid integer'
            return false;
        }

        if (isset($aParams['min'])) {
            $lMin = intval($aParams['min']);
            if ($lVal < $lMin) {
                $this->addErrorFormat($aName, 'num.min', $lMin); // 'must be %s or greater'
                return false;
            }
        }
        if (isset($aParams['max'])) {
            $lMax = intval($aParams['max']);
            if ($lVal > $lMax) {
                $this->addErrorFormat($aName, 'num.max', $lMax); // 'must be %s or smaller'
                return false;
            }
        }
        return true;
    }

    protected static function getOptionsFloat() {
        $lRet = array();
        $lRet['min'] = fie('min', 'Min. value', 'string');
        $lRet['max'] = fie('max', 'Max. value', 'string');
        $lRet['digits']   = fie('digits', 'Digits', 'int');
        $lRet['decimals'] = fie('decimals', 'Decimals', 'int');
        return $lRet;
    }

    protected function isValidTypeFloat($aName, $aValue, $aParams) {
        if (!is_numeric($aValue)) {
            $this->addErrorFormat($aName, 'float.invalid', $aValue); // '%s is not a valid decimal number'
            return false;
        }
        $lVal = floatval($aValue);

        if (isset($aParams['min'])) {
            $lMin = floatval($aParams['min']);
            if ($lVal < $lMin) {
                $this->addErrorFormat($aName, 'num.min', $lMin); // 'must be %s or greater'
                return false;
            }
        }
        if (isset($aParams['max'])) {
            $lMax = floatval($aParams['max']);
            if ($lVal > $lMax) {
                $this->addErrorFormat($aName, 'num.max', $lMax); // 'must be %s or smaller'
                return false;
            }
        }

        $lParts = explode('.', (string)(abs($lVal)));
        $lBefore = strlen($lParts[0]);
        if ($lParts[0] == 0) {
            $lBefore = 0;
        }
        if (count($lParts) == 1) {
            $lDecimals = 0;
            $lAllDigits = $lBefore;
        } else {
            $lDecimals = strlen($lParts[1]);
            $lAllDigits = $lBefore + $lDecimals;
        }

        if (isset($aParams['digits'])) {
            $lMax = intval($aParams['digits']);
            if ($lMax < $lAllDigits) {
                $this->addErrorFormat($aName, 'float.digits.max', $lMax); // 'cannot have more than %s digits'
                return false;
            }
        }
        if (isset($aParams['decimals'])) {
            $lMax = intval($aParams['decimals']);
            if ($lMax < $lDecimals) {
                $this->addErrorFormat($aName, 'float.decimals.max', $lMax); // 'cannot have more than %s decimals'
                return false;
            }
        }
        return true;
    }

    protected static function getOptionsString() {
        $lRet = array();
        $lRet['minlen'] = fie('minlen', 'Min. length', 'int');
        $lRet['maxlen'] = fie('maxlen', 'Max. length', 'int');
        $lRet['regex']     = fie('regex', 'Regex pattern', 'string');
        $lRet['regexclue'] = fie('regexclue', 'Pattern clue', 'string');
        return $lRet;
    }

    protected function isValidTypeString($aName, $aValue, $aParams) {
        if (!is_scalar($aValue)) { // we do accept integers/floats as strings too
            $this->addErrorFormat($aName, 'string.invalid', gettype($aValue)); // 'type $s is not a valid string'
            return false;
        }
        $lVal = (string)($aValue);

        if (isset($aParams['minlen'])) {
            $lMin = intval($aParams['minlen']);
            if (strlen($lVal) < $lMin) {
                $this->addErrorFormat($aName, 'string.length.min', $lMin); // 'must be %s characters or longer'
                return false;
            }
        }
        if (isset($aParams['maxlen'])) {
            $lMax = intval($aParams['maxlen']);
            if (strlen($lVal) > $lMax) {
                $this->addErrorFormat($aName, 'string.length.max', $lMax); // 'must be %s characters or shorter'
                return false;
            }
        }
        if (isset($aParams['regex'])) {
            $lPattern = $aParams['regex'];
            if (!preg_match($lPattern, $lVal)) {
                $lMsg = $this->getErrorFormat('string.regex'); // 'does not match the specified pattern';
                if (isset($aParams['regexclue'])) {
                    $lMsg.= ' ('.$aParams['regexclue'].')';
                }
                $this->addError($aName, $lMsg);
                return false;
            }
        }
        return true;
    }

    protected function isValidTypeDate($aName, $aValue, $aParams) {
        if (!is_scalar($aValue)) {
            $this->addErrorFormat($aName, 'date.invalid.type', gettype($aValue));// 'Type %s is not a valid date');
            return false;
        }
        $lVal = (string)($aValue);
        $lParts = explode('-', $lVal);
        if (count($lParts) != 3) {
            $this->addErrorFormat($aName, 'date.invalid', $aValue);// '%s is not a valid date');
            return false;
        }
        $lYear = $lParts[0];
        if (strlen($lYear) != 4) {
            $this->addErrorFormat($aName, 'date.year.invalid', $lYear);// '%s is not a valid year');
            return false;
        }
        $lMonth = $lParts[1];
        if (strlen($lMonth)!=2 || $lMonth < 1 || $lMonth > 12) {
            $this->addErrorFormat($aName, 'date.month.invalid', $lMonth);// '%s is not a valid month');
            return false;
        }
        $lDay = $lParts[2];
        if (strlen($lDay)!=2 || $lDay < 1 || $lDay > 31) {
            $this->addErrorFormat($aName, 'date.day.invalid', $lDay);// '%s is not a valid date');
            return false;
        }
        return true;
    }

    protected static function getOptionsEnum() {
        $lRet = array();
        $lRet['items'] = fie('items', 'Valid options', 'memo');
        return $lRet;
    }

    protected function isValidTypeEnum($aName, $aValue, $aParams) {
        if (!is_scalar($aValue)) { // we do accept integers/floats as strings too
            $this->addErrorFormat($aName, 'string.invalid', gettype($aValue)); // 'type $s is not a valid string'
            return false;
        }
        $lVal = (string)($aValue);

        if (isset($aParams['items'])) {
            $lValidItems = preg_split('/\n|\r\n?/', $aParams['items']);
            if (!in_array($lVal, $lValidItems)) {
                $this->addErrorFormat($aName, 'enum.invalid', $lVal); // 'value %s is not a valid option'
                return false;
            }
        }
        return true;
    }


}
