<?php
class CInc_App_Condition_Phrase extends CApp_Condition_Base {

  protected $mValues = NULL;

  public function isMet() {
    $lWhichPhraseCatagory = $this->mParams['which'];
    $lOperator = $this->mParams['op'];
    $lValue = $this->mParams['val'];
    
    $lSql = 'SELECT content_id FROM al_cms_ref_category WHERE category='.esc($lWhichPhraseCatagory);
    $lQry = new CCor_Qry($lSql);
    $lWhichPhraseCatagories = $lQry -> getImplode('content_id');
    if (empty($lWhichPhraseCatagories)) return false;
    
    return $this -> checkPhraseContent($lWhichPhraseCatagories, $lOperator, $lValue);
    return false;
  }

  public function getValues() {
    return $this -> mValues;
  }
  
  protected function checkPhraseContent($aWhichPhraseCatagories, $aOperator, $aValue) {
    $lSqlCond = $this -> getSqlConditionPart($aOperator, $aValue);
    $lSql = 'SELECT content_id FROM al_cms_content WHERE content_id IN ('.$aWhichPhraseCatagories.') ';
    $lSql.= 'AND content '.$lSqlCond;
    $lQry = new CCor_Qry($lSql);
    $lContentIds = $lQry -> getImplode('content_id');
    if (empty($lContentIds)) return false;
    
    $lJobid = $this->get('jobid');
    $lSql = 'SELECT id FROM al_cms_ref_job WHERE jobid = '.esc($lJobid).'AND content_id IN ('.$lContentIds.')';
    $lResult = CCor_Qry::getInt($lSql);
    $this -> dbg('Is PhraseCatagory '.$aWhichPhraseCatagories.' '.$aOperator.' '.$aValue);
    return $lResult;
  }
  
  protected function getSqlConditionPart($aOperator, $aValue) {
    $lSqlCond = '';
    switch ($aOperator) {
      case '=' :
        $lSqlCond = '= '.esc($aValue);
        break;
      case '<>' :
      case '!=' :
        $lSqlCond = '<> '.esc($aValue);
        break;
      case '<' :
        $lSqlCond = '< '.esc($aValue);
        break;
      case '>' :
        $lSqlCond = '> '.esc($aValue);
        break;
      case 'ends' :
        $lSqlCond = 'LIKE "%'.(string)$aValue.'"';
        break;
      case 'begins' :
        $lSqlCond = 'LIKE "'.(string)$aValue.'%"';
        break;
      case 'contains' :
        $lSqlCond = 'LIKE "%'.(string)$aValue.'%"';
        break;
      case '!contains' :
        $lSqlCond = 'NOT LIKE "%'.(string)$aValue.'%"';
        break;
      case 'word' :
        $lSqlCond = 'RLIKE "[[:<:]]'.(string)$aValue.'[[:>:]]"';
    }
    return $lSqlCond;
  }
  
  public function getSubForm($aPar = NULL) {
    $lPar = toArr($aPar);
    $lFac = new CHtm_Fie_Fac();
    $lFac -> mOld = false;
    $lFac -> mValPrefix = 'par';

    $lRet = '<tr>';
    $lRet.= '<td>Phrase Category</td>';
    $lVal = isset($lPar['which']) ? $lPar['which'] : '';
    $lArr = array();
    $lPhraseCatagories = CCor_Res::get('categories');
    unset($lPhraseCatagories['Nutrition']);
    
    $lRet.= '<td>';
    $lDef = fie('which', '', 'select', $lPhraseCatagories);
    $lRet.= $lFac->getInput($lDef, $lVal);
    $lRet.= '</td></tr>';
    
    
    $lRet.= '<tr>';
    $lRet.= '<td>'.htm(lan('lib.op')).'</td>';
    $lValue = isset($lPar['op']) ? $lPar['op'] : '';
    $lRet.= '<td>';
    $lOps = array();
    $this -> mOps['word'] = 'op.word';
    foreach ($this -> mOps as $lKey => $lVal) {
      $lOps[$lKey] = lan('lib.'.$lVal);
    }
    $lDef = fie('op', '', 'select', $lOps);
    $lRet.= $lFac -> getInput($lDef, $lValue);
    $lRet.= '</td></tr>';

    $lRet.= '<tr>';
    $lRet.= '<td>'.htm(lan('lib.value')).'</td>';
    $lVal = isset($lPar['val']) ? $lPar['val'] : '';
    $lRet.= '<td>';
    $lDef = fie('val', '');
    $lRet.= $lFac -> getInput($lDef, $lVal);
    $lRet.= '</td></tr>';

    return $lRet;
  }
  
}