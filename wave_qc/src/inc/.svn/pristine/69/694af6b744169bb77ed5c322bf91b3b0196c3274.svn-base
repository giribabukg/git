<?php
class CInc_Htm_Fie_Reg extends CCor_Obj {

  protected $mTyp = array();
  protected $mRes;

  public function __construct() {
    $this -> addType('string',    'Standard');
    $this -> addType('memo',      'Memo', 'text');
    $this -> addType('rich',      'Richtext', 'mediumtext');
    $this -> addType('date',      'Date', "varchar(10)");
    $this -> addType('datetime',  'Datetime', "datetime NOT NULL default '0000-00-00 00:00:00'");
    $this -> addType('int',       'Integer', "bigint(20)");
    $this -> addType('boolean',   'Checkbox', "enum('','X')");
    $this -> addType('uselect',   'User selection', 'bigint(20) unsigned');
    $this -> addType('gselect',   'Group selection', 'bigint(20) unsigned');
    $this -> addType('valselect', 'Value selection');
    $this -> addType('select',    'Key/Value selection');
    $this -> addType('tselect',   'Helptable selection');
    $this -> addType('cselect',   'Cascading selection');
    $this -> addType('ccomplete', 'Cascading autocomplete');
    $this -> addType('resselect', 'Ressource selection');
    $this -> addType('pickselect', 'Picklist selection');
    $this -> addType('pick',      'Picklist');
    $this -> addType('newpick',   'New Picklist');
    $this -> addType('ean',       'Barcode');
    $this -> addType('hidden',    'Hidden');
    $this -> addType('image',     'Image', 'tinytext');
    $this -> addType('file',      'File Upload');
    $this -> addType('tradio',    'Radio');

    $this -> mRes = new CCor_Res_Reg();
  }

  protected function & addType($aTyp, $aName, $aSql = '') {
    $lTyp = new CCor_Dat();
    $lTyp['typ'] = $aTyp;
    $lTyp['cap'] = $aName;
    $lTyp['sql'] = $aSql;
    $this -> mTyp[$aTyp] = & $lTyp;
    return $lTyp;
  }

  public function getTypes() {
    return $this -> mTyp;
  }

  public function & getType($aType) {
    $lRet = NULL;
    if ($this -> isValid($aType)) {
      $lRet = & $this -> mTyp[$aType];
    }
    return $lRet;
  }

  public function getSqlType($aType, $aMaxLen = 0) {
    $lRec = $this -> getType($aType);
    if (!empty($lRec)) {
      $lRet = $lRec['sql'];
    }
    if (empty($lRet)) {
      $lMax = intval($aMaxLen);
      $lMax = (empty($lMax)) ? 75 : $lMax;
      $lRet = 'varchar('.$lMax.')';
    }
    return $lRet;
  }

  public function isValid($aType) {
    return isset($this -> mTyp[$aType]);
  }

  public function typeToString($aType) {
    $lTyp = & $this -> getType($aType);
    if (NULL == $lTyp) {
      return '[unknown type]';
    } else {
      return $lTyp['cap'];
    }
  }

  public function paramToString($aType, $aParam) {
    if (empty($aParam)) {
      return '';
    }
    $lFnc = 'getParam'.$aType;
    if ($this -> hasMethod($lFnc)) {
      return $this -> $lFnc($aParam);
    }
    return $aParam;
  }

  public function getParamDef($aType) {
    $lFnc = 'getDef'.$aType;
    if ($this -> hasMethod($lFnc)) {
      return $this -> $lFnc();
    }
    return array();
  }

  protected function getParamValselect($aPar) {
    $lArr = toArr($aPar);
    if (isset($lArr['lis'])) {
      return implode(', ', $lArr['lis']);
    } else {
      return $aPar;
    }
  }

  protected function getParamResselect($aPar) {
    $lArr = unserialize($aPar);
    $lRes = $this -> mRes -> getType($lArr['res']);
    $lRet = 'Ressource '.$lRes['cap'].' ('.$lArr['key'].'/'.$lArr['val'].')';
    return $lRet;
  }

  protected function getDefResselect() {
    $lRet = array();

    $lReg = new CCor_Res_Reg();
    $lArr = $lReg -> getResArray();
    $lLis = array(0 => '');
    foreach ($lArr as $lRow) {
      $lLis[$lRow['key']] = $lRow['cap'];
    }
    $lRet[] = fie('res', 'Ressource', 'select', $lLis);
    $lRet[] = fie('key', 'Key Field');
    $lRet[] = fie('val', 'Value Field');
    return $lRet;
  }


  protected function getParamUselect($aPar) {
    $lArr = unserialize($aPar);
    $lRet = '';
    if (isset($lArr['del'])) {
      $lVal = $lArr['del'];
      if ($lVal == 'N') {
        $lRet.= 'active, ';
      } else {
        $lRet.= 'inactive, ';
      }
    }
    if (isset($lArr['gru'])) {
      $lGid = $lArr['gru'];
      $lRes = CCor_Res::getInstance();
      $lGru = $lRes -> extract('id','name', 'gru');
      if (isset($lGru[$lGid])) {
        $lRet.= $lGru[$lGid].', ';
      } else {
        $lRet.= '[unknown], ';
      }
    }
    return strip($lRet, 2);
  }

  protected function getDefUselect() {
    $lRet = array();
    $lRet[] = fie('gru', lan('lib.group'), 'gselect');
    return $lRet;
  }

  protected function getDefGselect() {
    $lRet = array();
    $lRet[] = fie('gid', lan('gru.parent'), 'gselect');
    return $lRet;
  }

  protected function getParamGselect($aPar) {
    $lArr = unserialize($aPar);
    $lRet = '';
    if (isset($lArr['gid'])) {
      $lGid = $lArr['gid'];
      $lRes = CCor_Res::getInstance();
      $lGru = $lRes -> extract('id','name', 'gru');
      if (isset($lGru[$lGid])) {
        $lRet.= $lGru[$lGid].', ';
      } else {
        $lRet.= '[unknown], ';
      }
    }
    return strip($lRet, 2);
  }

  protected function getParamPick($aPar) {
    $lArr = toArr($aPar);
    if (isset($lArr['dom'])) {
      return 'Helptable '.$lArr['dom'];
    } else {
      return $aPar;
    }
  }

  protected function getParamNewpick($aPar) {
    $lArr = toArr($aPar);
    if (isset($lArr['dom'])) {
      return 'Pickliste '.$lArr['dom'];
    } else {
      return $aPar;
    }
  }

  protected function getParamTselect($aPar) {
    $lArr = toArr($aPar);
    if (isset($lArr['dom'])) {
      return 'Helptable '.$lArr['dom'];
    } else {
      return $aPar;
    }
  }

  protected function getDefTselect() {
    $lRet = array();
    $lPar = array('res' => 'htbmaster', 'key' => 'domain', 'val' => 'description');
    $lRet[] = fie('dom', 'Domain', 'resselect', $lPar);
    return $lRet;
  }

  protected function getDefPick() {
    $lRet = array();
    $lPar = array('res' => 'htbmaster', 'key' => 'domain', 'val' => 'description');
    $lRet[] = fie('dom', 'Domain', 'resselect', $lPar);
    return $lRet;
  }

  protected function getDefNewpick() {
    $lRet = array();
    $lPar = array('res' => 'pckmaster', 'key' => 'domain', 'val' => 'description_'.LAN);

    $lRet[] = fie('dom', 'Domain', 'resselect', $lPar);
    return $lRet;
  }

  protected function getParamPickselect($aPar) {
    $lArr = unserialize($aPar);
    $lAlias = (isset($lArr['alias'])) ? ' ('.$lArr['alias'].')' : '';
    $lRes = $this -> mRes -> getType($lArr['dom']);
    $lRet = 'Picklist '.$lArr['dom'].$lAlias;
    return $lRet;
  }

  protected function getDefPickselect() {
    $lRet = array();
    $lLis = CCor_Res::extract('domain','description_'.LAN,'pckmaster');
    if (empty($lLis)){
      $lRet = $this -> getDefTselect();
      return $lRet;
    }

    $lPag = CHtm_Page::getInstance();

    $lSqlColFind = 'SELECT DISTINCT (alias),domain,col from al_pck_columns ';
    $lQry = new CCor_Qry($lSqlColFind);

    $lKeyList = Array();
    $lDomList = Array();

    foreach ($lQry as $lRow){
      $lDomList[$lRow['domain']][$lRow['alias']] = $lRow['alias'];
      $lKeyList[$lRow['alias']] = $lRow['alias'];

    }

    $lDomId = getNum('in');
    $lAliasId = getNum('in');
    $lSteerAliasId = getNum('in');

    $lJs = 'ListChange(\''.$lDomId.'\',\''.$lAliasId.'\');ListChange(\''.$lDomId.'\',\''.$lSteerAliasId.'\');';
    $lPar = array("id" => $lDomId, "onchange" => $lJs);
    $lRet[] = fie('dom', 'Ressource', 'select', $lLis, $lPar);
    $lPar = array("id" => $lAliasId);
    $lRet[] = fie('alias', 'Key Field (from pick list)','select',$lKeyList, $lPar);
    $lPar = array("id" => $lSteerAliasId);
    $lRet[] = fie ('steerAlias','Steer Alias (from pick list)','select',$lKeyList, $lPar);


    $lDomArray = Array();
    $lJs = 'GruMem["'.$lAliasId.'"] = new Array();'.LF;
    foreach ($lLis as $lKey => $lVal){
      if (isset($lDomList[$lKey])){
        $lJs.= 'GruMem["'.$lAliasId.'"]["'.$lKey.'"] = new Array();'.LF;
        $lDomArray = $lDomList[$lKey];
        foreach ($lDomArray as $lTemp ) {
          $lJs.= 'GruMem["'.$lAliasId.'"]["'.$lKey.'"]["'.$lTemp.'"] = "'.$lTemp.'";'.LF;
        }
      }
    }
    $lPag -> addJs($lJs);

    $lJs = 'GruMem["'.$lSteerAliasId.'"] = new Array();'.LF;
    foreach ($lLis as $lKey => $lVal){
      if (isset($lDomList[$lKey])){
        $lJs.= 'GruMem["'.$lSteerAliasId.'"]["'.$lKey.'"] = new Array();'.LF;
        $lDomArray = $lDomList[$lKey];
        foreach ($lDomArray as $lTemp ) {
          $lJs.= 'GruMem["'.$lSteerAliasId.'"]["'.$lKey.'"]["'.$lTemp.'"] = "'.$lTemp.'";'.LF;
        }
      }
    }
    $lPag -> addJs($lJs);
    return $lRet;
  }

  protected function getParamTradio($aPar) {
    $lArr = toArr($aPar);
    if (isset($lArr['dom'])) {
      return 'Helptable '.$lArr['dom'];
    } else {
      return $aPar;
    }
  }

  protected function getDefTradio() {
    $lRet = array();
    $lPar = array('res' => 'htbmaster', 'key' => 'domain', 'val' => 'description');
    $lRet[] = fie('dom', 'Domain', 'resselect', $lPar);
    $lArr = array('' => '', NB => 'Space', BR => 'Break');
    $lRet[] = fie('sep', 'Separator', 'select', $lArr);
    return $lRet;
  }

  protected function getDefCselect() {
    $lRet = array();
    $numCols = 4;
    $lLis = CCor_Res::extract('domain','description_'.LAN,'pckmaster');
    if (empty($lLis)){
      $lRet = $this -> getDefTselect();
      return $lRet;
    }

    $lPag = CHtm_Page::getInstance();

    $lSqlColFind = 'SELECT DISTINCT (alias),domain,col from al_pck_columns ';
    $lQry = new CCor_Qry($lSqlColFind);

    $lKeyList = Array();
    $lDomList = Array();

    foreach ($lQry as $lRow){
      $lDomList[$lRow['domain']][$lRow['alias']] = $lRow['alias'];
      $lKeyList[$lRow['alias']] = $lRow['alias'];
    }
    $lDomId = getNum('in');
    $lAliasId = getNum('in');

    $lJs = 'ListChange(\''.$lDomId.'\',\''.$lAliasId.'\');';
    for ($i=1; $i<=$numCols; $i++) {
      $lCurId = getNum('in');
      $lIds[$i] = $lCurId;
      $lJs.= 'ListChange(\''.$lDomId.'\',\''.$lCurId.'\');';
    }
    $lPag->addJs('document.observe("dom:loaded", function() {'.$lJs.'});'); // already call that when form is loaded
    $lPar = array("id" => $lDomId, "onchange" => $lJs);
    $lRet[] = fie('dom', 'Picklist', 'select', $lLis, $lPar);

    $lPar = array("id" => $lAliasId);
    $lRet[] = fie('alias', 'Value column','select',$lKeyList, $lPar);

    $lFields = array('' => '') + CCor_Res::extract('alias', 'name_'.LAN, 'fie');
    for ($i=1; $i<=$numCols; $i++) {
      $lRet[] = fie('fie_'.$i, $i.'. Field', 'select', $lFields);
      $lPar = array('id' => $lIds[$i]);
      $lRet[] = fie('col_'.$i, $i.'. Picklist column','select', $lKeyList, $lPar);
    }
    $lJs = $this->getMem($lAliasId, $lLis, $lDomList);
    for ($i=1; $i<=$numCols; $i++) {
      $lJs.= $this->getMem($lIds[$i], $lLis, $lDomList);
    }
    $lPag -> addJs($lJs);
    return $lRet;
  }

  protected function getDefCcomplete() {
    return $this->getDefCselect();
  }

  private function getMem($aAliasId, $aList, $aDomList) {
    $lRet = 'GruMem["'.$aAliasId.'"] = new Array();'.LF;
    foreach ($aList as $lKey => $lVal){
      if (isset($aDomList[$lKey])){
        $lRet.= 'GruMem["'.$aAliasId.'"]["'.$lKey.'"] = new Array();'.LF;
        $lDomArray = $aDomList[$lKey];
        if (!empty($lDomArray))
        foreach ($lDomArray as $lTemp ) {
          $lRet.= 'GruMem["'.$aAliasId.'"]["'.$lKey.'"]["'.$lTemp.'"] = "'.$lTemp.'";'.LF;
        }
      }
    }
    return $lRet;
  }

  protected function getParamCselect($aPar) {
    return $this -> getParamPickselect($aPar);
  }

  protected function getParamCcomplete($aPar) {
    return $this -> getParamPickselect($aPar);
  }

  protected function getDefFile() {
    $this -> mCategory = CCor_Res::get('htb', array('fil', 'id', 'value'));

    $lRet = array();
    $lRet[] = fie('dest', lan('job-fil-src'), 'tselect', array('dom' => 'filedest'));
    $lRet[] = fie('folder', lan('lib.folder'));
    $lRet[] = fie('url', lan('lib.url'));
    $lRet[] = fie('category', lan('lib.file.category'), 'select', $this -> mCategory);
    $lRet[] = fie('filetype', lan('lib.fileextension'), 'pick', array('dom' => 'filetype'));
    $lRet[] = fie('prefix', lan('lib.prefix'));
    $lRet[] = fie('overwrite', lan('lib.overwrite'), 'boolean');

    return $lRet;
  }

  protected function getParamFile($aPar) {
    $lArr = toArr($aPar);
    $lRet = '';
    if (isset($lArr['dest'])) {
      $lRet[] = 'Destination '.$lArr['dest'];
    }
    if (isset($lArr['folder'])) {
      $lRet[] = lan('lib.folder').' '.$lArr['folder'];
    }
    return implode(',', $lRet);
  }
}