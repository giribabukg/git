<?php
abstract class CInc_App_Condition_Base extends CCor_Obj {

  protected $mOps = array(
    '='         => 'op.eq',
    '<>'        => 'op.neq',
    '<'         => 'op.lt',
    '>'         => 'op.gt',
    'contains'  => 'op.contains',
    '!contains' => 'op.!contains',
    'begins'    => 'op.begins',
    'ends'      => 'op.ends'
  );

  protected $mOpsSQL = array(
    '='         => array('operator' => '=',        'before_value' => '\'',  'after_value' => '\''),
    '<>'        => array('operator' => '<>',       'before_value' => '\'',  'after_value' => '\''),
    '<'         => array('operator' => '<',        'before_value' => '\'',  'after_value' => '\''),
    '>'         => array('operator' => '>',        'before_value' => '\'',  'after_value' => '\''),
    'contains'  => array('operator' => 'like',     'before_value' => '\'%', 'after_value' => '%\''),
    '!contains' => array('operator' => 'not like', 'before_value' => '\'%', 'after_value' => '%\''),
    'begins'    => array('operator' => 'like',     'before_value' => '\'',  'after_value' => '%\''),
    'ends'      => array('operator' => 'like',     'before_value' => '\'%', 'after_value' => '\'')
  );

  protected $mContext;

  protected $mDefaultKey = 'data';

  const MAX_LINES = 10;

  public function loadFromDb($aId) {
    $lId = intval($aId);
    $lQry = new CCor_Qry('SELECT * FROM al_cond WHERE id='.$lId);
    if (!$lRow = $lQry -> getDat()) return FALSE;
    $this -> setParams($lRow['params']);
  }

  public function setParams($aParams) {
    $this -> mParams = toArr($aParams);
  }

  public function getParams() {
    return $this -> mParams;
  }

  /**
   * Set a context variable used in the conditions
   * @param string $aKey
   * @param array $aValue
   * @return CInc_App_Condition_Base
   */
  public function setContext($aKey, $aValue) {
    $this -> mContext[$aKey] = $aValue;
    return $this;
  }

  /**
   *
   * @param string $aKey The unique key (e.g. job or usr)
   * @param mixed $aDefault Default value if key is not set
   * @return array
   */
  public function getContext($aKey, $aDefault = NULL) {
    return (isset($this -> mContext[$aKey])) ? $this -> mContext[$aKey] : $aDefault;
  }

  protected function get($aSubKey, $aDefault = NULL) {
    $lKey = $this -> mDefaultKey;
    if (!isset($this -> mContext[$lKey])) return $aDefault;
    if (!isset($this -> mContext[$lKey][$aSubKey])) return $aDefault;
    return $this -> mContext[$lKey][$aSubKey];
  }

  /**
   * @return bool Indicates whether the condition evaluates to true
   */
  public function isMet() {
    return FALSE;
  }

  public function getValues() {
    return NULL;
  }

  public function requestToArray($aParams) {
    return toArr($aParams);
  }

  protected function isValidTerm($aField, $aOp, $aValue) {
    $lCurValue = $this -> get($aField);
    $this -> dbg('Is field '.$aField.' '.$aOp.' '.$aValue.'? Current value: '.$lCurValue);

    $lRet = false;
    switch ($aOp) {
      case '=' :
        $lRet = ((string)$lCurValue == (string)$aValue);
        break;
      case '<>' :
      case '!=' :
        $lRet =  ((string)$lCurValue <> (string)$aValue);
        break;
      case '<' :
        $lRet = ($lCurValue < $aValue);
        break;
      case '>' :
        $lRet = ($lCurValue > $aValue);
        break;
      case 'ends' :
        $lCurValueLen = strlen($lCurValue);
        $aValueLen = strlen($aValue);
        if ($aValueLen > $lCurValueLen) return FALSE;
        $lRet = substr_compare($lCurValue, $aValue, -$aValueLen, $aValueLen, TRUE) === 0;
        break;
      case 'begins' :
        $lCurValueLen = strlen($lCurValue);
        $aValueLen = strlen($aValue);
        if ($aValueLen > $lCurValueLen) return FALSE;
        $lRet = substr_compare($lCurValue, $aValue, 0, $aValueLen, TRUE) === 0;
        break;
      case 'contains' :
        $lRet = stristr( $lCurValue, $aValue) == TRUE;
        break;
      case '!contains' :
        $lRet = stristr( $lCurValue, $aValue) == FALSE;
    }
    return $lRet;
  }

  /* abstract */ public function paramToString() {
  }

  /* abstract */ public function paramToSQL() {
  }

  protected function termToString($aParams) {
    $lFieldName = lan('lib.unknown');
    $lField = $aParams['alias'];
    $lFieldName = $lField;
    $lNames = CCor_Res::extract('alias', 'name_'.LAN, 'fie');
    $this -> dump($lNames);
    if (isset($lNames[$lField])) {
      $lFieldName = $lNames[$lField];
    }
    $lOp = $aParams['op'];
    if (isset($this -> mOps[$lOp])) {
      $lOp = lan('lib.'.$this -> mOps[$lOp]);
    }
    return '<b>'.htm($lFieldName).'</b> '.htm($lOp.' '.$aParams['val']);
  }

  protected function termToSQL($aParams) {
    $lAlias = $aParams['alias'];
    $lOperator = $this -> mOpsSQL[$aParams['op']]['operator'];
    $lBeforeValue = $this -> mOpsSQL[$aParams['op']]['before_value'];
    $lAfterValue = $this -> mOpsSQL[$aParams['op']]['after_value'];

    $lValue = trim($aParams['val']);
    if ($lValue == "" || $lValue == "'" || $lValue == "''") {
      $lValue = "";
    }

    if ($this -> isTypeDateTime($lAlias) || $this -> isTypeDate($lAlias)) {
      if (in_array($lOperator, array('<', '>'))) {
        $lResultOperator = $lOperator == '<' ? '+' : '-';

        if ($lValue) {
          $lPeriodStr = preg_match('/(?P<int>\d+)(?P<str>\w+)/', $lValue, $lPeriodArr);
          $lResultValue = $lPeriodArr['int'];
          $lPeriod = strtolower($lPeriodArr['str']);

          if (is_numeric($lResultValue) && in_array($lPeriod, array('d', 'day', 'w', 'week', 'm', 'month'))) {
            switch ($lPeriod) {
              case 'd':
                $lResultPeriod = 'DAY';
                break;
              case 'day':
                $lResultPeriod = 'DAY';
                break;
              case 'w':
                $lResultPeriod = 'WEEK';
                break;
              case 'week':
                $lResultPeriod = 'WEEK';
                break;
              case 'm':
                $lResultPeriod = 'MONTH';
                break;
              case 'month':
                $lResultPeriod = 'MONTH';
                break;
            };

            return '(date(NOW()) = date(date('.$lAlias.') '.$lResultOperator.' INTERVAL '.$lResultValue.' '.$lResultPeriod.'))';
          } else {
            return '(NULL)';
          }
        }
      } else {
        return '(NULL)';
      }
    } else {
      $lValue = $lBeforeValue.$lValue.$lAfterValue;

      return '('.$lAlias.' '.$lOperator.' '.$lValue.')';
    }
  }

  public function getJobField($aAlias) {
    $lAlias = strtolower($this -> mParams['alias']);
    $lValue = strtolower($this -> mParams['val']);
    if ($aAlias == $lAlias) {
      return $lValue;
    } else {
      return FALSE;
    }
  }

  public function getLast_Status_Change() {
    return $this -> getJobField('last_status_change');
  }

  public function getWebstatus() {
    return $this -> getJobField('webstatus');
  }

  public function isTypeDate($aAlias) {
    $lJobFieldType = CCor_Res::extract('alias', 'typ', 'fie');
    if ($lJobFieldType[$aAlias] == 'date') {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function isTypeDateTime($aAlias) {
    $lJobFieldType = CCor_Res::extract('alias', 'typ', 'fie');
    if ($lJobFieldType[$aAlias] == 'datetime') {
      return TRUE;
    } else {
      return FALSE;
    }
  }
}