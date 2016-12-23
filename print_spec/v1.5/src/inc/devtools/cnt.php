<?php
class CInc_Devtools_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = 'Devtools';
    $this -> mMmKey = 'opt';

    $lPn = 'dbg';
    $lUsr = CCor_Usr::getInstance();
    if (! $lUsr -> canRead($lPn)) {
      $this -> setProtection('*', $lPn, rdNone);
    }

    $this -> mSomeFields = array('product_cat');
    $this -> mSrc = 'art';
  }

  public function actStd() {
    $this -> render($this -> getMenu());
  }

  protected function getMenu() {
    $lMethods = get_class_methods(get_class($this));
    $lInherited = get_class_methods('CCor_Cnt');
    $lMenu = new CHtm_Vmenu('DevTools');
    foreach ($lMethods as $lMethod) {
      if (substr($lMethod, 0, 3) != 'act') continue;
      if (substr($lMethod, 3, 1) == 'S') continue;
      if (in_array($lMethod, $lInherited)) continue;
      $lAct = substr($lMethod, 3);
      $lMenu -> addItem($lMethod, 'index.php?act=devtools.' . strtolower($lAct), $lAct);
    }
    $lMenu -> setKey('act' . ucfirst($this -> mAct));
    return $lMenu;
  }

  public function renderMenu($aContent) {
    $this -> render(CHtm_Wrap::wrap($this -> getMenu(), $aContent));
  }

  protected function actCreateclass() {
    $lForm = new CHtm_Form('devtools.screateclass', 'Create Inc/Cust/Mand Classes');
    $lForm -> addDef(fie('name', 'Name'));
    $this -> renderMenu($lForm);
  }

  protected function actScreateclass() {
    $lVal = $this -> getReq('val');
    $lName = strtolower($lVal ['name']);
    $lParts = explode('_', $lName);
    if (substr($lParts [0], 0, 1) == 'c') {
      $lParts [0] = substr($lParts [0], 1);
    }
    if (in_array($lParts [0], array(
        'inc','cust'
    ))) {
      array_shift($lParts);
    }
    $lSubDir = implode(DS, $lParts);
    $lArr = array_map('ucfirst', $lParts);
    $lSuffix = implode('_', $lArr);

    $lClasses ['inc'] = 'CInc_' . $lSuffix . ' extends CCor_Obj';
    $lClasses ['inc/_cust0/inc'] = 'CCust_' . $lSuffix . ' extends CInc_' . $lSuffix;
    $lClasses ['inc/_mand0/inc'] = 'C' . $lSuffix . ' extends CCust_' . $lSuffix;

    $lForm = new CHtm_Form('devtools.sscreateclass', 'Create Inc/Cust/Mand Classes');
    $lForm -> setAtt('class', 'tbl w800');
    foreach ($lClasses as $lPrefix => $lClassname) {
      $lFilename = $lPrefix . DS . $lSubDir . '.php';
      if (! file_exists($lFilename)) {
        $lForm -> addDef(fie($lFilename, $lPrefix, 'string', NULL, array(
            'class' => 'inp w400'
        )));
        $lForm -> setVal($lFilename, $lClassname);
      }
    }
    $this -> renderMenu($lForm);
  }

  protected function makeDir($aPath, $aMode = 0777) {
    if (empty($aPath)) return;
    $lSub = substr($aPath, 0, strrpos($aPath, DS));
    if ('' != $lSub) {
      $this -> makeDir($lSub, $aMode);
    }
    if (! file_exists($aPath)) {
      $this -> dbg('Make Dir ' . $aPath);
      mkdir($aPath, $aMode);
    }
  }

  protected function actSscreateclass() {
    $lVal = $this -> getReq('val');
    foreach ($lVal as $lPath => $lCode) {
      $lThePath = pathinfo(__FILE__, PATHINFO_DIRNAME);
      $lThePath = realpath($lThePath . DS . '..' . DS . '..');
      $lThePath .= DS . $lPath;
      $lThePath = pathinfo($lThePath, PATHINFO_DIRNAME);

      $this -> makeDir($lThePath);
      file_put_contents($lPath, '<?php' . LF . 'class ' . $lCode . ' {' . LF . LF . '}');
    }
    $this -> actCreateclass();
  }

  protected function getEncodingMethods() {
    $lRet = array();
    $lRet ['base64_encode'] = 'ENC base64_encode';
    $lRet ['base64_decode'] = 'DEC base64_decode';

    $lRet ['utf8_encode'] = 'DEC utf8_encode';
    $lRet ['utf8_decode'] = 'DEC utf8_decode';

    $lRet ['htmlentities'] = 'ENC htmlentities';
    $lRet ['html_entity_decode'] = 'DEC html_entity_decode';

    $lRet ['htmlspecialchars'] = 'ENC htmlspecialchars';
    $lRet ['htmlspecialchars_decode'] = 'DEC htmlspecialchars_decode';

    $lRet ['urlencode'] = 'ENC urlencode';
    $lRet ['urldecode'] = 'DEC urldecode';

    $lRet ['quoted_printable_encode'] = 'ENC quoted_printable_encode';
    $lRet ['quoted_printable_decode'] = 'ENC quoted_printable_decode';

    if (function_exists('json_encode')) {
      $lRet ['json_encode'] = 'ENC json_encode';
      $lRet ['json_decode'] = 'DEC json_decode';
    }
    if (function_exists('convert_uuencode')) {
      $lRet ['convert_uuencode'] = 'ENC convert_uuencode';
      $lRet ['convert_uudecode'] = 'DEC convert_uudecode';
    }

    $lRet ['mysql_real_escape_string'] = 'ENC mysql_real_escape_string';
    $lRet ['md5'] = 'ENC MD5';
    $lRet ['sha1'] = 'ENC SHA1';

    $lRet ['password'] = 'ENC Password';

    return $lRet;
  }

  protected function doEncode($aVal, $aMethod) {
    $lAllowed = $this -> getEncodingMethods();
    if (! isset($lAllowed [$aMethod])) {
      $this -> msg('Encoding method ' . $aMethod . ' not allowed', mtUser, mlWarn);
      return $aVal;
    }
    $lFnc = 'doencode' . $aMethod;
    if ($this -> hasMethod($lFnc)) {
      return $this -> $lFnc($aVal);
    }
    if (function_exists($aMethod)) {
      return $aMethod($aVal);
    }
    return $aVal;
  }

  protected function doencodePassword($aVal) {
    return CApp_Pwd::encryptPassword($aVal);
  }

  protected function actEncode() {
    $lForm = new CHtm_Form('devtools.sencode', 'Encode');
    $lForm -> setAtt('class', 'tbl w800');
    $lForm -> addDef(fie('memo', 'Value', 'memo', NULL, array(
        'class' => 'inp w600'
    )));
    $lArr = $this -> getEncodingMethods();
    $lForm -> addDef(fie('method', 'Method', 'select', $lArr));
    $this -> renderMenu($lForm);
  }

  protected function actSencode() {
    $lAll = $this -> getReq('val');
    $lVal = $lAll ['memo'];

    $lMethod = $lAll ['method'];
    $lNew = $this -> doEncode($lVal, $lMethod);

    $lOldLen = strlen($lVal);
    $lNewLen = strlen($lNew);
    $lPercent = ($lOldLen == 0) ? 0 : number_format(($lNewLen/$lOldLen)*100,1);

    $lForm = new CHtm_Form('devtools.sencode', 'Encode '.$lOldLen.' to '.$lNewLen.' ('.$lPercent.'%)');
    $lForm -> setAtt('class', 'tbl w800');
    $lForm -> addDef(fie('old', 'Old', 'memo', NULL, array(
        'class' => 'inp w600'
    )));
    $lForm -> addDef(fie('memo', 'Value', 'memo', NULL, array(
        'class' => 'inp w600'
    )));
    $lArr = $this -> getEncodingMethods();
    $lForm -> addDef(fie('method', 'Method', 'select', $lArr));

    #$lForm -> setVal('old', $lVal);
    #$lForm -> setVal('memo', $lNew);

    $lForm -> setVal('old', $lNew);
    $lForm -> setVal('memo', $lVal);

    $lForm -> setVal('method', $lMethod);
    $this -> renderMenu($lForm);
  }

  protected function actBench() {
    $lRet = '';

    $lSum = 0;
    $lQry = new CApi_Alink_Query('getInfo');
    $lQry -> addParam('sid', 'huw');
    for($i = 0; $i < 20; $i++) {
      $lStart = xdebug_time_index();
      $lRes = $lQry -> query();
      // ar_dump($lRes);
      $lTim = xdebug_time_index() - $lStart;
      $lSum += $lTim;
      $lRet .= $i . ' Query : ' . substr(($lTim), 0, 4) . BR;
    }
    $lRet .= BR . 'SUM : ' . substr(($lSum), 0, 4) . BR;
    $this -> renderMenu($lRet);
  }

  protected function actDalimregister() {
    $lForm = new CHtm_Form('devtools.sdalimregister', 'Register Dalim File');
    $lForm -> addDef(fie('doc', 'Document'));
    $lForm -> setVal('doc', 'myjobs/');
    $this -> renderMenu($lForm);
  }

  protected function actSdalimregister() {
    $lVal = $this -> getReq('val');

    $lUtil = new CApi_Dalim_Utils();
    $lPdf = $lUtil -> registerDocument($lVal ['doc']);

    $lForm = new CHtm_Form('devtools.sdalimregister', 'Register Dalim File');
    $lForm -> addDef(fie('doc', 'Document'));
    $lForm -> setVal('doc', $lVal ['doc']);
    $this -> renderMenu($lForm);
  }

  protected function actConfig() {
    $lCfg = CCor_Cfg::getInstance();
    $lRows = $lCfg -> getValues();
    ksort($lRows);

    $lRet = '';
    $lRet .= '<table cellpadding="2" class="tbl">';
    $lRet .= '<tr><td class="th1">Key</td><td class="th1">Value</td></tr>';
    foreach ($lRows as $lKey => $lVal) {
      $lRet .= '<tr>';
      $lRet .= '<td class="td2">' . $lKey . '</td>';
      $lRet .= '<td class="td1">';
      if (is_array($lVal)) {
        $lRet .= htm(var_export($lVal, TRUE));
      } else {
        $lRet .= htm($lVal);
      }

      $lRet .= '</td></tr>';
    }
    $lRet .= '</table>';
    $this -> renderMenu($lRet);
  }

  protected function actPhpinfo() {
    $lArr2 = array();
    $lArr2['PHP'] = PHP_VERSION;
    $lExt = get_loaded_extensions(false);
    natcasesort($lExt);
    $lArr2['Extensions'] = implode(', ', $lExt);

    ob_start();
    phpinfo();
    $lArr = array('phpinfo' => array());
    if (preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER)) {
      foreach ( $matches as $match )
        if (strlen($match [1]))
          $lArr[$match [1]] = array();
        elseif (isset($match[3]))
          $lArr[end(array_keys($lArr))] [$match [2]] = isset($match [4]) ? array(
              $match [3],
              $match [4]
          ) : $match [3];
        else
          $lArr [end(array_keys($lArr))][] = $match[2];
    }
    $lRet = '<table class="tbl w800" cellpadding="2">';
    $lRet.= '<tr><td class="cap cp" colspan="3" onclick="Flow.Std.togByClass(\'app-sect\')">PHPINFO</td></tr>'.LF;

    unset($lArr['PHP License']);

    $lNum = 1;
    foreach($lArr as $name => $section) {
      $lNum++;
      $lCls = 's_'.$lNum;
      $lRet.= '<tr><td colspan="3" class="th2 cp" onclick="Flow.Std.togByClass(\''.$lCls.'\')">'.$name.'</td></tr>'.LF;
      foreach($section as $key => $val) {
        if(is_array($val))
          $lRet.= '<tr class="'.$lCls.' app-sect"><td class="td2">'.$key.'</td><td class="td1">'.$val[0].'</td><td class="td1">'.$val[1]."</td></tr>\n";
        elseif(is_string($key))
          $lRet.= '<tr class="'.$lCls.' app-sect"><td class="td2">'.$key.'</td><td class="td1" colspan="2">'.$val."</td>\n";
        }
    }

    $lRet.= '</table>';

    $this -> renderMenu($lRet);
  }

  protected function actApc() {
    $loaded = extension_loaded('apc');
    $enabled = ini_get('apc.enabled');
    $ret = ($loaded) ? 'Loaded' : 'Not loaded';
    $ret .= ($enabled) ? ' and enabled' : ' and not enabled';
    $this -> renderMenu($ret);
  }

  protected function actTransfix() {
    $lForm = new CHtm_Form('devtools.stransfix', 'Transition Quotation Jobs');
    $lForm -> addDef(fie('count', 'Jobs to transition per run'));
    $lLimit = $this -> getInt('count');
    if (empty($lLimit)) $lLimit = 2;
    $lForm -> setVal('count', $lLimit);

    $lRet = $lForm -> getContent() . BR . BR;

    $lFie = CCor_Res::extract('alias', 'native', 'fie');
    $lSrcNative = $lFie ['src'];

    $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
    if ('portal' == $lWriter) {
      $lIte = new CCor_TblIte('all');
      $lIte -> addField('src');
      $lIte -> addField('eingang');
      $lIte -> addField('rechnung');
      $lIte -> addField('status');
      $lIte -> addField('keyword');
      $lIte -> addField('webstatus');
      $lIte -> addField('updatetime');
      $lIte -> addCondition('jobid', '>', 'A');
      $lIte -> addCondition('webstatus', '>', 10);
      $lJobs = $lIte -> getIterator();
    } else {
      $lQry = new CApi_Alink_Query_Getjoblist();
      $lQry -> addField('src', $lSrcNative);
      $lQry -> addField('eingang', 'eingang');
      $lQry -> addField('rechnung', 'rechnung');
      $lQry -> addField('status', 'status');
      $lQry -> addField('keyword', 'stichw1');
      $lQry -> addField('webstatus', 'webstatus');
      $lQry -> addField('updatetime', 'updatetime');
      $lQry -> addCondition('jobid', '>', 'A');
      $lQry -> addCondition('webstatus', '>', 10);
      $lJobs = $lQry -> getIterator();
    }

    $lTbl = new CHtm_List('devtools');
    $lTbl -> mIte = $lJobs;
    $lTbl -> addCtr();
    $lTbl -> addColumn('jobid', 'JobID', false);
    $lTbl -> addColumn('src', 'SRC', false);
    $lTbl -> addColumn('status', 'STATUS', false);
    $lTbl -> addColumn('rechnung', 'Rechnung', false);
    $lTbl -> addColumn('keyword', 'Keyword', false);
    $lTbl -> addColumn('webstatus', 'Webstatus', false);
    $lTbl -> addColumn('eingang', 'Created', false);
    $lTbl -> addColumn('updatetime', 'Last Updated', false);
    $lRet .= $lTbl -> getContent();

    $lRet .= BR . 'NOW:' . BR . '<textarea class="w100p" rows="20">';
    foreach ($lJobs as $lRow) {
      $lRet .= 'UPDATE auftrag SET Rechnung=NULL ';
      $lRet .= 'WHERE jobid=' . esc($lRow ['jobid']) . ';' . LF;
    }
    $lRet .= '</textarea>';

    $lRet .= BR . 'Backup:' . BR . '<textarea class="w100p" rows="20">';
    foreach ($lJobs as $lRow) {
      $lRet .= 'UPDATE auftrag SET Rechnung=' . esc($lRow ['rechnung']) . ' ';
      $lRet .= 'WHERE jobid=' . esc($lRow ['jobid']) . ';' . LF;
    }
    $lRet .= '</textarea>';

    $this -> renderMenu($lRet);
  }

  protected function actStransfix() {
    $lVal = $this -> getReq('val');
    $lLimit = intval($lVal ['count']);
    if (empty($lLimit)) {
      $this -> redirect('devtools.transfix');
    }

    $lFie = CCor_Res::extract('alias', 'native', 'fie');
    $lSrcNative = $lFie ['src'];

    $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
    if ('portal' == $lWriter) {
      $lIte = new CCor_TblIte('all');
      $lIte -> addField('src');
      $lIte -> addField('wertinfo');
      $lIte -> addCondition('jobid', '>', 'A');
      $lIte -> addCondition('webstatus', '>', 10);
      $lIte -> setLimit(0, $lLimit);
      $lJobs = $lIte -> getIterator();
    } else {
      $lQry = new CApi_Alink_Query_Getjoblist();
      $lQry -> addField('src', $lSrcNative);
      $lQry -> addField('wertinfo', 'wertinfo');
      $lQry -> addCondition('jobid', '>', 'A');
      $lQry -> addCondition('webstatus', '>', 10);
      $lQry -> setLimit(0, $lLimit);
      $lJobs = $lQry -> getIterator();
    }

    foreach ($lJobs as $lJob) {
      $lTran = ($lJob ['wertinfo'] !== "" ? true : false);
      $this -> transJob($lJob ['src'], $lJob ['jobid'], $lTran);
    }
    $this -> redirect('index.php?act=devtools.transfix&count=' . $lLimit);
  }

  protected function transJob($aSrc, $aJid, $aTran) {
    if ($aTran) {
      $lClass = 'CJob_' . $aSrc . '_Mod';
      $lMod = new $lClass($aJid);
      $lMod -> forceVal('webstatus', '0');
      $lRes = $lMod -> update();
    } else {
      $this -> msg('Trying to transition ' . $aSrc . ' ' . $aJid);
      $lClass = 'CJob_' . $aSrc . '_Step';
      $lStep = new $lClass($aJid);
      $lStep -> copyAnfToJob();
    }
  }

  protected function actWebquery() {
    $lId = $this -> getInt('id');

    $lForm = new CHtm_Form('devtools.swebquery', 'Webquery');
    $lForm -> setAtt('class', 'tbl w100p');
    $lForm -> setParam('id', $lId);
    $lForm -> addDef(fie('name', 'Name', 'string', null, array(
        'class' => 'inp w800'
    )));
    $lForm -> addDef(fie('url', 'URL', 'string', null, array(
        'class' => 'inp w800'
    )));
    $lForm -> addDef(fie('body', 'Body', 'memo', null, array(
        'class' => 'inp w800 prettyprint lang-xml linenums prettyprinted'
    )));

    if (! empty($lId)) {
      $lSql = 'SELECT * FROM al_dev_webqueries WHERE id=' . $lId;
      $lQry = new CCor_Qry($lSql);
      if ($lRow = $lQry -> getDat()) {
        $lTpl = new CCor_Tpl();
        $lTpl -> setDoc($lRow ['body']);
        $lPatterns = $lTpl -> findPatterns();
        if (! empty($lPatterns)) {
          foreach ($lPatterns as $lAlias) {
            $lValue = '';
            if (strpos($lAlias, '=')) {
              list($lAlias, $lValue) = explode('=', $lAlias, 2);
            }
            $lForm -> addDef(fie($lAlias, ucfirst($lAlias)));
            if ('' != $lValue) {
              $lForm -> setVal($lAlias, $lValue);
            }
          }
        }
        $lForm -> assignVal($lRow);
      }
    }
    $lTbl = $this -> getWebqueryList();

    $this -> renderMenu(CHtm_Wrap::wrap($lForm, $lTbl));
  }

  protected function getWebqueryList() {
    $lTbl = new CHtm_List('devtools', 'Webqueries');
    $lTbl -> addColumn('name', 'Name');
    $lTbl -> addDel();
    $lQry = new CCor_Qry('SELECT id,name FROM al_dev_webqueries ORDER BY name');
    $lTbl -> mIte = $lQry;
    $lTbl -> mStdLnk = 'index.php?act=devtools.webquery&id=';
    $lTbl -> mDelLnk = 'index.php?act=devtools.swebquerydel&id=';
    $lTbl -> mCanEdit = true;
    $lTbl -> setAtt('class', 'tbl w200');
    $lTbl -> mTitle = 'Webqueries';
    return $lTbl;
  }

  protected function actSwebquery() {
    $lVal = $this -> getReq('val');

    $lForm = new CHtm_Form('devtools.swebquery', 'Webquery');
    $lForm -> setAtt('class', 'tbl w100p');
    $lForm -> addDef(fie('name', 'Name', 'string', null, array(
        'class' => 'inp w800'
    )));
    $lForm -> addDef(fie('url', 'URL', 'string', null, array(
        'class' => 'inp w800'
    )));
    $lForm -> addDef(fie('body', 'Body', 'memo', null, array(
        'class' => 'inp w800 prettyprint lang-xml linenums prettyprinted'
    )));

    $lTpl = new CCor_Tpl();
    $lTpl -> setDoc($lVal ['body']);
    $lPatterns = $lTpl -> findPatterns();
    if (! empty($lPatterns)) {
      foreach ($lPatterns as $lAlias) {
        $lValue = '';
        $lPat = $lAlias;
        if (strpos($lAlias, '=')) {
          list($lAlias, $lValue) = explode('=', $lAlias, 2);
        }
        $lFields [$lPat] = $lAlias;
        $lForm -> addDef(fie($lAlias, ucfirst($lAlias)));
      }
    }
    $lForm -> assignVal($lVal);

    $lTpl -> setDoc($lVal ['body']);
    foreach ($lFields as $lPattern => $lAlias) {
      $lValue = (isset($lVal [$lAlias])) ? $lVal [$lAlias] : '';
      $lTpl -> setPat($lPattern, $lValue);
    }
    $lForm -> setVal('body', $lTpl -> getContent());
    $lTbl = $this -> getWebqueryList();

    $this -> renderMenu(CHtm_Wrap::wrap($lForm, $lTbl));
  }

  protected function actSwebquerydel() {
    $lId = $this -> getInt('id');
    $lSql = 'DELETE FROM al_dev_webqueries WHERE id=' . $lId;
    CCor_Qry::exec($lSql);
    $this -> redirect('index.php?act=devtools.webquery');
  }


  protected function actUpdateValues() {
    $lForm = new CHtm_Form('devtools.stUpdateValues', 'Select the Helptable Field');
    $lForm -> setAtt('class', 'tbl w800');
    $this->mJobFields = CCor_Res::getByKey('alias', 'fie');
    foreach ($this->mJobFields as $lKey => $lVal) {
      if ($lVal['typ'] == 'cselect' || $lVal['typ'] == 'newpick' || $lVal['typ'] == 'tselect') {
        $lFields[$lKey] = $lVal['name_'.LAN];
      }
    }
    $lForm -> addDef(fie('field', 'JobField', 'select', $lFields));
    $this -> renderMenu($lForm);
  }

  protected function actStUpdateValues() {
    $lAll = $this -> getReq('val');
    $lVal = $lAll ['field'];
    $lPar = array('dom' => 'tou', 'sep' => '&nbsp;');
    $lField = CCor_Res::getByKey('alias', 'fie', array('alias' => $lVal));
    $lForm = new CHtm_Form('devtools.schooseitem', 'Select the item to change');
    $lForm -> setAtt('class', 'tbl w800');

    $lForm -> addDef($lField[$lVal]);
    $lForm -> addDef(fie('new_value', 'Rename value into:'));
    $lForm -> addDef(fie('alias', '', 'hidden'));
    $lForm -> addDef(fie('update','What to update?', 'tradio',$lPar));

    $lForm -> setVal('update', 'portal');
    $lForm -> setVal('alias', $lField[$lVal]['alias']);
    $this -> renderMenu($lForm);
  }

  protected function actSchooseitem() {
    $lRequest = $this -> getReq('val');
    $lFieldAlias = $lRequest['alias'];
    $lOldValue = $lRequest[$lFieldAlias];
    $lNewValue = $lRequest['new_value'];
    $lUpdateIn = $lRequest['update'];
    $lTbl = '';
    if ($lOldValue != $lNewValue) {
      if ($lUpdateIn == 'portal') {
        $this -> updatePortalData($lFieldAlias, $lOldValue, $lNewValue);
      } elseif ($lUpdateIn == 'live') {
        $lTbl = $this -> getLiveJobIds($lFieldAlias, $lOldValue);
        if (isset($lRequest['do_jobupdate'])) {
          $lTenJobs = array_slice($this -> mJobIds, 0, 2);
          $this -> updateLiveJobs($lTenJobs, $lFieldAlias, $lNewValue);
        }
      }
      elseif ($lUpdateIn == 'archive') {
        $lTbl = $this -> updateArchiveJobs($lFieldAlias, $lOldValue, $lNewValue);
      }
    }

    $lField = CCor_Res::getByKey('alias', 'fie', array('alias' => $lFieldAlias));
    $lForm = new CHtm_Form('devtools.schooseitem', 'Select the item to change');
    $lPar = array('dom' => 'tou', 'sep' => '&nbsp;');

    #	  $lForm -> addDef(fie('new_value', 'Rename value into:','','',array('disabled'=>'disabled')));
    $lForm -> addDef(fie($lFieldAlias, $lField[$lFieldAlias]['name_'.LAN]));
    $lForm -> addDef(fie('new_value', 'Rename value into:'));
    $lForm -> addDef(fie('alias', '', 'hidden'));
    $lForm -> addDef(fie('do_jobupdate', '', 'hidden'));
    $lForm -> addDef(fie('update','What to update?', 'tradio',$lPar));

    $lForm -> setVal('new_value', $lNewValue);
    $lForm -> setVal('update', $lUpdateIn);
    $lForm -> setVal($lFieldAlias, $lOldValue);
    $lForm -> setVal('alias', $lFieldAlias);
    $lForm -> setVal('do_jobupdate', 1);

    $this -> renderMenu(CHtm_Wrap::wrap($lForm, $lTbl));
  }

  protected function getLiveJobIds($aAlias, $aVal) {
    $lFie = CCor_Res::extract('alias', 'native', 'fie');
    $this -> mSrcNative = $lFie [$aAlias];

    $lWriter = CCor_Cfg::get('job.writer.default', 'alink');
    if ('portal' == $lWriter) {
      $lRequest = new CCor_TblIte('all');
      $lRequest -> addField('jobnr');
      $lRequest -> addField($aAlias);
      $lRequest -> addCondition($aAlias, '=', $aVal);
    } else {
      $lRequest = new CApi_Alink_Query_Getjoblist($this -> mSrc);
      $lRequest -> addField('jobnr', 'jobnr');
      $lRequest -> addField($aAlias, $this -> mSrcNative);
      $lRequest -> addCondition($aAlias, '=', $aVal);
    }

    $this -> mJobIds = array_keys($lRequest -> getArray('jobnr'));

    $lTbl = new CHtm_List('devtools', 'JobIds');
    $lTbl -> addCtr();
    $lTbl -> addColumn('jobid', 'JobId');
    $lTbl -> mIte = $lRequest -> getArray('jobnr');

    $lTbl -> setAtt('class', 'tbl w200');
    $lTbl -> mTitle = 'JobIds need to updated';
    return $lTbl;
  }

  protected function updateLiveJobs($aJobIds, $aAlias, $aNewValue) {
    $lFac = new CJob_Fac('art');
    foreach ($aJobIds as $lJobId) {
      $lMod = $lFac -> getMod($lJobId);
      $lArr = array($aAlias => $aNewValue);
      $lMod -> forceUpdate($lArr);
    }
  }

  protected function updatePortalData($aAlias, $aOldValue, $aNewValue) {
    $lField = CCor_Res::getByKey('alias', 'fie', array('alias' => $aAlias));
    $lFieldParam = unserialize($lField[$aAlias]['param']);
    $lDomain = $lFieldParam['dom'];
    $lFieldType = $lField[$aAlias]['typ'];

    if ($lFieldType == 'cselect' || $lFieldType == 'newpick') {
      $lPickListColumn = $this -> getPickListColInfos($aAlias, $lDomain);
      $lHelptableDomain = $lPickListColumn['htb'];
      $lPickListColoumnPos = 'col'.$lPickListColumn['col'];

      // Update pick list itms
      $lSql = 'UPDATE `al_pck_items` SET `'.$lPickListColoumnPos.'` = '.esc($aNewValue).' WHERE  domain='.esc($lDomain).' AND mand='.MID.' AND `'.$lPickListColoumnPos.'` = '.esc($aOldValue).'';
      CCor_Qry::exec($lSql);
    }
    else {
      $lHelptableDomain = $lDomain;
    }

    // Update help table itm
    $lSql = 'UPDATE `al_htb_itm` SET value_en='.esc($aNewValue).' WHERE domain='.esc($lHelptableDomain).' AND value_en='.esc($aOldValue).' AND mand='.MID;
    CCor_Qry::exec($lSql);

    // Update Group Names
    $this -> updateGroupNames($aOldValue, $aNewValue, $aAlias);

    // Update Approval Template
    $this -> updateAplEventTemplates($aOldValue, $aNewValue, $aAlias);
  }

  protected function getPickListColInfos($aAlias, $aDom) {
    $lSql = 'SELECT * FROM `al_pck_columns` WHERE alias='.esc($aAlias).' AND domain = '.esc($aDom).' AND mand='.MID;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lPckListInfos = $lRow;
    }
    return $lPckListInfos;
  }


  protected function updateGroupNames($aOldString, $aNewString, $aAlias) {
    $lSql = 'SELECT * FROM al_gru  WHERE name like "%'.$aOldString.'%" AND mand='.MID;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lGruOldName = $lRow['name'];
      $lGruNewName = str_replace($aOldString, $aNewString, $lGruOldName);
      $lSql = 'UPDATE `al_gru` SET `name`='.esc($lGruNewName).' WHERE `id`='.$lRow['id'].' AND name='.esc($lGruOldName).' AND mand='.MID.' LIMIT 1;';
      CCor_Qry::exec($lSql);
      $lSql = 'UPDATE `al_gru_infos` SET `val`='.esc($aNewString).' WHERE  `gid`='.$lRow['id'].' AND `alias`='.esc($aAlias).' AND val='.esc($aOldString).' LIMIT 1;';
      CCor_Qry::exec($lSql);
    }
  }

  protected function updateAplEventTemplates($aOldString, $aNewString, $aAlias) {
    $lSql = 'SELECT * FROM al_eve_types WHERE code LIKE "apl%" AND fields LIKE "%'.$aAlias.'%" AND mand = '.MID;
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      if (!empty($lRow['code'])) {
        $lAplCodes[] = $lRow['code'];
      }
    }
    if (!is_null($lAplCodes)) {
      foreach ($lAplCodes as $lVal) {
        $lSql = 'SELECT * FROM al_eve WHERE typ = '.esc($lVal).' AND name_en LIKE "%'.$aOldString.'%";';
        $lQry = new CCor_Qry($lSql);
        foreach ($lQry as $lRow) {
          $lEventOldName = $lRow['name_en'];
          $lEventNewName = str_replace($aOldString, $aNewString, $lEventOldName);
          $lSql = 'UPDATE `al_eve` SET `name_en`='.esc($lEventNewName).' WHERE `id`='.$lRow['id'].' AND `mand`='.MID.' LIMIT 1;';
          CCor_Qry::exec($lSql);

          $lSql = 'UPDATE `al_eve_infos` SET `val`='.esc($aNewString).' WHERE `eve_id`='.$lRow['id'].' AND `alias`='.esc($aAlias).' LIMIT 1;';
          CCor_Qry::exec($lSql);
        }
      }
    }
  }

  protected function updateArchiveJobs($aAlias, $aOldString, $aNewString) {
    $lSql = 'UPDATE al_job_arc_'.MID.' SET `'.$aAlias.'` = '.esc($aNewString).' WHERE `'.$aAlias.'` = '.esc($aOldString);
    $lQry = new CCor_Qry();
    $lQry -> exec($lSql);
    return $lQry -> getAffectedRows();
  }

  protected function actExtract() {
    $lRet = '';
    $lFile = file('D:/logs/during/after_access.log');
    $lTake = 'after';
    $lPat = '/.*\[dur ([0-9]*)\] \"(.*index.php.*) HTTP.*/';
    $lMatches = array();
    $lTpl = new CCor_Tpl();
    $lTpl->setDoc('INSERT INTO access_log SET dur="{match1}",url="{match2}",action="{match3}",take="'.$lTake.'";');

    foreach ($lFile as $lRow) {
      if (preg_match($lPat, $lRow, $lMatches)) {
        $lTpl->clear();
        foreach ($lMatches as $lIndex => $lMatch) {
          $lTpl->setPat('match'.$lIndex, $lMatch);
        }
        $lTpl->setPat('match3', '');
        if (isset($lMatches[2])) {
          $lAct = array();
          if (preg_match('/index\.php\?act=(.*)([\&]+.*|$)/U', $lMatches[2], $lAct)) {
            //var_dump($lAct);
            $lTpl->setPat('match3', $lAct[1]);
          }
        }
        $lRet.= $lTpl->getContent().BR;
      }
    }
    $this->renderMenu($lRet);
  }

  protected function actExtractalinklog() {
    $lRet = '';
    $lFile = file('D:/logs/during/after_alinklog.txt');
    $lTake = 'after';

    $lMatches = array();

    $lTpl = new CCor_Tpl();
    $lTpl->setDoc('INSERT INTO alink_log SET dur={dur},query={query},method={method},take="'.$lTake.'";');

    $lCount = 1;
    $lState = 1;
    $lPatQuery = '/\<query\>\<method\>(.*)\<\/method\>.*/';
    $lPatResponse = '/.*SEND.*Bytes ([0-9]*) msec.*/';
    foreach ($lFile as $lRow) {
      $lCount++;
      #if ($lCount > 200) break;
      #$lRet.= $lRow.BR;
      if (1 == $lState) {
        if (preg_match($lPatQuery, $lRow, $lMatches)) {
          $lItem['query'] = $lMatches[0];
          $lItem['method'] = $lMatches[1];
          $lState = 2;
          #$lRet.= var_export($lItem, true).BR;
        }
      } else {
        if (preg_match($lPatResponse, $lRow, $lMatches)) {
          $lItem['dur'] = intval($lMatches[1]);
          foreach ($lItem as $lKey => $lVal) {
            $lTpl -> setPat($lKey, esc($lVal));
          }
          $lRet.= htm($lTpl->getContent()).BR;
          $lTpl->clear();
          $lItem = array();
          $lState = 1;
        }
      }
    }

    //28.11.13 18:19:11 RECV  [henkeluw] 0014911 Bytes <?xml version="1.0" encoding="UTF-8"
    $lMatches = array();
    $lTpl = new CCor_Tpl();
    $lTpl->setDoc('INSERT INTO alink_log SET dur="{match1}",query="{match2}",method="{match3}";');

    foreach ($lFile as $lRow) {
      if (preg_match($lPat, $lRow, $lMatches)) {
        $lTpl->clear();
        foreach ($lMatches as $lIndex => $lMatch) {
          $lTpl->setPat('match'.$lIndex, $lMatch);
        }
        $lTpl->setPat('match3', '');
        if (isset($lMatches[2])) {
          $lAct = array();
          if (preg_match('/index\.php\?act=(.*)([\&]+.*|$)/U', $lMatches[2], $lAct)) {
            //var_dump($lAct);
            $lTpl->setPat('match3', $lAct[1]);
          }
        }
        $lRet.= $lTpl->getContent().BR;
      }
    }
    $this->renderMenu($lRet);
  }

  protected function actDump() {
    $lSql = 'SHOW TABLES;';
    $lQry = new CCor_Qry($lSql);
    $lRows = $lQry -> getAssocs();
    $lFrm = new CHtm_Form('devtools.sdump', 'Dump DB');

    $lRet = '<table class="tbl">';
    foreach ($lRows as $lArr) {
      foreach ($lArr as $lTbl) {
        $lRet.= '<tr>';
        $lRet.= '<td class="td1 w16">';
        $lRet.= '<input type="checkbox" name="val[struc]['.$lTbl.']" />';
        $lRet.= '</td>';
        $lRet.= '<td class="td1 w16">';
        $lRet.= '<input type="checkbox" name="val[data]['.$lTbl.']" />';
        $lRet.= '</td>';
        $lRet.= '<td class="td2">';
        $lRet.= $lTbl;
        $lRet.= '</td>';
        $lRet.= '</tr>';
        #$lFrm->addDef(fie($lTbl, 'Table '.$lTbl, 'boolean'));
      }
    }

    $this->renderMenu($lRet);
  }

  protected function actCheckthumbs() {
    $lRet = '';
    $lSvcWecInst = CSvc_Wec::getInstance();
    $lStatics = $lSvcWecInst -> getStatics();

    $lSql = 'SELECT * FROM al_job_files ';
    $lSql.= 'WHERE mand='.MID.' AND sub="pdf" ORDER BY DateLastChange,jobid';

    $lTodos = array();
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lJid = $lRow['jobid'];
      $lDynamics = $lSvcWecInst -> getDynamics($lJid);
      $lThumbDir = $lDynamics['thumbnail_dir'];
      $lImageDir = $lDynamics['image_dir'];
      $this->dbg('--- '.$lThumbDir.' --- '.$lImageDir);
      if (!file_exists($lThumbDir.$lDynamics['thumbnail_file'])) {
      var_dump($lRow);
        #$this->msg('Thumbnails: Job '.$lJid.' thumbnail not present, '.$lRow['DateLastChange']);
        $lMsg = $lRow['DatelastChange'].' Thumbnails: Job '.$lJid.' thumbnail not present';
        $lRet.= $lMsg.BR;
        $lTodos[$lJid] = $lRow['filename'];
      }
      if (!file_exists($lImageDir.$lDynamics['image_file'])) {
              var_dump($lRow);
        $lMsg = $lRow['DatelastChange'].' Thumbnails: Job '.$lJid.' image not present';
        $lRet.= $lMsg.BR;
        $lTodos[$lJid] = $lRow['filename'];
      }
    }
    if (!empty($lTodos)) {
      foreach ($lTodos as $lJid => $lRes) {
         $lQue = new CApp_Queue('dalimthumb');
         $lQue->setParam('jid', $lJid);
         $lQue->setParam('doc', $lJid.'/'.$lRes);
         #$lQue->insert();
      }
    }

    #$lDynamics = $lSvcWecInst -> getDynamics($lJobId);
    #if (file_exists($lDynamics['thumbnail_dir'].$lDynamics['thumbnail_file'])) {
    #  $lImg = '<img src="'.$lDynamics['image_dir'].$lDynamics['image_file'].'"
    #$
    $this->renderMenu($lRet);
  }

  protected function actUnserializeEventsIntoReportTable() {
    $lRet = '';
    $lTableCheck = CCor_Qry::getStr('SHOW TABLES LIKE "al_job_apl_loop_events"');
    if (!$lTableCheck) {
      $lSql = 'CREATE TABLE `al_job_apl_loop_events` (`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT, `datum` DATE NOT NULL DEFAULT "0000-00-00", ';
      $lSql.='`uid` BIGINT(20) UNSIGNED NOT NULL DEFAULT "0", ';
      $lSql.='`mand` BIGINT(20) UNSIGNED NOT NULL DEFAULT "0", ';
      $lSql.='`jobid` VARCHAR(50) NOT NULL, ';
      $lSql.='`loop_id` BIGINT(255) NOT NULL, ';
      $lSql.='`event_id` BIGINT(255) NOT NULL, ';
      $lSql.='`event_prefix` TEXT NOT NULL, ';
      $lSql.='PRIMARY KEY (`id`)) ';
      $lSql.='COLLATE="utf8_general_ci" ENGINE=MyISAM AUTO_INCREMENT=1;';
      CCor_Qry::exec($lSql);
      $lRet.= '- Table has "al_job_apl_loop_events" has been created'.BR;
    }
    $lIsTableEmpty = CCor_Qry::getInt('SELECT COUNT(*) FROM `al_job_apl_loop_events`');
    if ($lIsTableEmpty > 0) {
      $lRet.= '- Table "al_job_apl_loop_events" is Not empty. No inserts has been done'.BR;
    }
    else {
      $lSql = 'SELECT * FROM `al_job_apl_loop`';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lEvents = unserialize($lRow['event_ids']);
        foreach ($lEvents as $lEventPrefix => $lEventId) {
          $lSql = 'INSERT INTO `al_job_apl_loop_events` (`datum`, `mand`, `jobid`, `loop_id`, `event_id`, `event_prefix`)';
          $lSql.= ' VALUES (' ;
          $lSql.= esc($lRow['start_date']).', ';
          $lSql.= $lRow['mand'].', ';
          $lSql.= $lRow['jobid'].', ';
          $lSql.= $lRow['id'].', ';
          $lSql.= $lEventId.', ';
          $lSql.= esc($lEventPrefix).');';
          CCor_Qry::exec($lSql);
        }
      }
      $lRet.= '- Data has been transported from "al_job_apl_loop" into "al_job_apl_loop_events"'.BR;
    }
    $this->renderMenu($lRet);
  }

  protected function actOnlineProofingToolReport() {
    $lRet = "<script src='http://code.highcharts.com/highcharts.js'></script>
      <script src='js/optr.js'></script>
      <style>
        .highcharts-legend{ margin-top: 50px; }
        #lt, #gt, #graph, #periods, #container { float: left; }
        #lt, #gt { width: 20px; display:none; }
        #lt:hover, #gt:hover { background: #DFE5E6; cursor: pointer; }
        #graph { width: 760px; }
        #periods, #container { width: 800px; }
      </style>";
    $lRet .= "<div id='container'>";
    $lRet .= "  <div id='lt'>".img("img/ico/16/nav-prev-lo.gif")."</div>";
    $lRet .= "  <div id='graph'></div>";
    $lRet .= "  <div id='gt'>".img("img/ico/16/nav-next-lo.gif")."</div>";
    $lRet .= "</div>";
    $lRet .= "<div id='periods'></div>";


    $this->renderMenu($lRet);
  }

  protected function actPartial() {
    $lFac = new CJob_Fac('rep', 'A000000248');
    $lJob = $lFac->getDat();
    $lPartial = new CJob_Partialform($lJob);
    $lPartial->addPart('rep', 'col');
    $lPartial->addPart('rep', 'co2');
    $this->render($lPartial);
  }

  protected function actGetcolors() {
    $lJid = $this->getReq('jid');
    
    $lSql = 'SELECT * FROM al_job_arc_3 WHERE jobid='.esc($lJid).' LIMIT 1';
    $lQry = new CCor_Qry($lSql);
    if ($lRow = $lQry->getDat()) {
      $lJob = $lRow;
    } else {
    $lFac = new CJob_Fac('rep', $lJid);
    $lJob = $lFac->getDat();
    }
    $lDg = intval($lJob['druckdurchgang']);

    $lPartial = new CJob_Partialform($lJob);
    $lPartial->addPart('rep', 'col');
    if ($lDg > 1) {
      $lPartial->addPart('rep', 'co2');
    }
    if ($lDg > 2) {
      $lPartial->addPart('rep', 'co3');
    }
    echo $lPartial->getContent();
  }

  protected function actGetTheircolors() {
    $lClient = new Zend_Http_Client();
    $lParams['jid'] = 'A000000247';
    $lParams['act'] = 'devtools.getcolors';
    $lParams['mid'] = '3';
    $lClient -> setParameterGet($lParams);
    $lBaseUrl = CCor_Cfg::get('base.url');
    //echo $lBaseUrl;exit;
    $lClient -> setUri($lBaseUrl.'cli.php');

    try {
      $lRes = $lClient -> request();
      if ($lRes -> isError()) {
        $this->msg('Error '.$lRes->getStatus(), mtApi, mlError);
        $this->render('Error');
        exit;
      } else {
        $lRet = $lRes -> getBody();
      }
    } catch (Exception $lExc) {
      $this -> msg($lExc -> getMessage(), mtApi, mlError);
    }
    echo $lRet;
  }

  protected function actGetXFDF() {
    $lRet = '';

    $lStartTime = microtime(TRUE);

//     $lOldJobs = CCor_Cfg::get('har.migrated.jobs');
    $lOldJobIDs = array(
        2200006932,
        2200006937,
        2200006938,
        2200006939,
        2200009041,
        2200009187,
        2200009218,
        2200009257,
        2200009320,
        2200009331
    );

    foreach ($lOldJobIDs as $lKey => $lJobID) {
      $lSQL = 'SELECT src FROM al_job_his WHERE src_id='.esc($lJobID);
      $lSrc = CCor_Qry::getStr($lSQL);

      $lSQL2 = 'SELECT MAX(id) FROM al_job_apl_loop WHERE mand=2 AND typ="apl" AND jobid='.esc($lJobID);
      $lHID = CCor_Qry::getStr($lSQL2);

      if ($lSrc && $lHID) {
//         print_r($lSrc.' : '.$lJobID.' : '.$lHID."<br>");

        $lFac = new CJob_Fac($lSrc, $lJobID);
        $lJob = $lFac -> getDat();

        $lAnn = new CJob_Apl_Page_Annotations($lJob);
        $lArr = array();
        $lArr['xfdf'] = $lAnn -> getXml();

        if (is_array($lArr)) {
          $lSer = serialize($lArr);
        }

//         print_r($lSer."<br><br>");

        $lSql = 'UPDATE al_job_apl_loop SET ';
        $lSql.= 'add_data_new2="'.addslashes(trim($lSer)).'" ';
        $lSql.= 'WHERE id='.esc($lHID).' AND jobid='.esc($lJobID);

//         print_r($lSql."<br><br>");

        CCor_Qry::exec($lSql);

//         SELECT *  FROM `al_job_his` WHERE `mand` = 2 AND `datum` > '2014-07-21 04:08:00' AND `typ` = 1024 group by src_id
      }
    }

    $lEndTime = microtime(TRUE) - $lStartTime.' Sekunden benötigt';
    $this -> renderMenu('Fertig: '.$lEndTime);
  }

  protected function actSgetXFDF() {
  }

  protected function actFerrerofilesfoldermigration() {
    $lForm = new CHtm_Form('devtools.sferrerofilesfoldermigration', 'Ferrero Files Folder Migration');



    $lSQL = 'SELECT COUNT(*) FROM `_ferrero_jobs`;';
    $lSum = CCor_Qry::getStr($lSQL);

    $lSQL = 'SELECT COUNT(*) FROM `_ferrero_jobs` WHERE `src`<>\'\' ORDER BY `jobid` ASC;';
    $lSumWithSrc = CCor_Qry::getStr($lSQL);

    $lSQL = 'SELECT COUNT(*) FROM `_ferrero_jobs` WHERE `src`=\'\' ORDER BY `jobid` ASC;';
    $lSumWithOutSrc = CCor_Qry::getStr($lSQL);

    $lSQL = 'SELECT COUNT(*) FROM `_ferrero_jobs` WHERE `src`<>\'\' AND `done`<>\'Y\' AND `done`<>\'A\' ORDER BY `jobid` ASC;';
    $lWithSrcTBD = CCor_Qry::getStr($lSQL);

    $lSQL = 'SELECT COUNT(*) FROM `_ferrero_jobs` WHERE `src`=\'\' AND `done`<>\'Y\' AND `done`<>\'B\' AND `done`<>\'C\' ORDER BY `jobid` ASC;';
    $lWithOutSrcTBD = CCor_Qry::getStr($lSQL);

    $lSQL = 'SELECT COUNT(*) FROM `_ferrero_jobs` WHERE `done`=\'A\' ORDER BY `jobid` ASC;';
    $lWithSrcA = CCor_Qry::getStr($lSQL);

    $lSQL = 'SELECT COUNT(*) FROM `_ferrero_jobs` WHERE `done`=\'B\' ORDER BY `jobid` ASC;';
    $lWithOutSrcB = CCor_Qry::getStr($lSQL);

    $lSQL = 'SELECT COUNT(*) FROM `_ferrero_jobs` WHERE `done`=\'C\' ORDER BY `jobid` ASC;';
    $lWithOutSrcC = CCor_Qry::getStr($lSQL);



    $lForm -> addDef(fie('sum', 'Sum', 'string'));
    $lForm -> setVal('sum', $lSum);

    $lForm -> addDef(fie('withsrc', 'Sum: jobs with source (src)', 'string'));
    $lForm -> setVal('withsrc', $lSumWithSrc);

    $lForm -> addDef(fie('withoutsrc', 'Sum: jobs without source (src)', 'string'));
    $lForm -> setVal('withoutsrc', $lSumWithOutSrc);

    $lForm -> addDef(fie('withsrctdb', 'To be done: jobs with source (src)', 'string'));
    $lForm -> setVal('withsrctdb', $lWithSrcTBD);

    $lForm -> addDef(fie('withoutsrctdb', 'To be done: jobs without source (src)', 'string'));
    $lForm -> setVal('withoutsrctdb', $lWithOutSrcTBD);

    $lForm -> addDef(fie('withsrcA', 'Errors: Jobs with source (src) that have no directory!', 'string'));
    $lForm -> setVal('withsrcA', $lWithSrcA);

    $lForm -> addDef(fie('withoutsrcB', 'Errors: Jobs without source (src) that have no MOPID!', 'string'));
    $lForm -> setVal('withoutsrcB', $lWithOutSrcB);

    $lForm -> addDef(fie('withoutsrcC', 'Errors: Jobs without source (src) that have no directory!', 'string'));
    $lForm -> setVal('withoutsrcC', $lWithOutSrcC);



    $this -> render(CHtm_Wrap::wrap($this -> getMenu(), $lForm));
  }

  // Done: Y = Yes
  // Done: A = Directory could not be found!
  // Done: B = There is no MOPID, therefore there is no migration!
  // Done: C = Directory could not be found!
  protected function actSferrerofilesfoldermigration() {
    #$lVal = $this -> getReq('val');
    #$lCount = strtolower($lVal['something']);

    $lMid = 1003;

    $lSQL = 'SHOW COLUMNS FROM `_ferrero_jobs` LIKE \'done\'';
    $lCol = CCor_Qry::getStr($lSQL);
    if (!$lCol) {
      $lSQL = 'ALTER TABLE `_ferrero_jobs` ADD COLUMN `done` CHAR(1) NOT NULL AFTER `src`;';
      CCor_Qry::exec($lSQL);
    }

    $lSql = 'SELECT `src`,`jobid`,`mopid` FROM `_ferrero_jobs` WHERE `src`<>\'\' AND `done`<>\'Y\' AND `done`<>\'A\' ORDER BY `jobid` ASC;'; // PUT IN CORRECT COLUMN NAME HERE !!!
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lSrc = trim($lRow['src']);
      $lJobId = trim($lRow['jobid']);
      $lMOPId = trim($lRow['mopid']); // PUT IN CORRECT COLUMN NAME HERE !!!

      $lCls = new CApp_Finder($lSrc, $lJobId, NULL, $lMid); // PUT IN CORRECT MID HERE !!!
      $lDir = $lCls -> getPath();

      if (file_exists($lDir)) {
        $lAliceDir = rtrim($lDir, DS);
        $lParentDir = dirname($lAliceDir);
        $lBobDir = $lParentDir.DS.$lMOPId;

        rename($lAliceDir, $lBobDir);
        CCor_Qry::exec('UPDATE `_ferrero_jobs` SET `done`=\'Y\' WHERE `src`=\''.$lSrc.'\' AND jobid=\''.$lJobId.'\' AND mopid=\''.$lMOPId.'\';'); // PUT IN CORRECT COLUMN NAME HERE !!!
      } else {
        CCor_Qry::exec('UPDATE `_ferrero_jobs` SET `done`=\'A\' WHERE `src`=\''.$lSrc.'\' AND jobid=\''.$lJobId.'\' AND mopid=\''.$lMOPId.'\';'); // PUT IN CORRECT COLUMN NAME HERE !!!
      }

      #mkdir($lDir, 0777, TRUE);
    }

    $lJobTypes = CCor_Cfg::get('menu-aktivejobs');
    $lCountJobTypes = count($lJobTypes);
    $lDummyArray = array_fill(0, $lCountJobTypes, 'job-');
    $lJobTypes = array_map('ltrim', $lJobTypes, $lDummyArray);

    $lSql = 'SELECT `src`,`jobid`,`mopid` FROM `_ferrero_jobs` WHERE `src`=\'\' AND `done`<>\'Y\' AND `done`<>\'B\' AND `done`<>\'C\' ORDER BY `jobid` DESC;'; // PUT IN CORRECT COLUMN NAME HERE !!!
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lSrc = trim($lRow['src']);
      $lJobId = trim($lRow['jobid']);
      $lMOPId = trim($lRow['mopid']); // PUT IN CORRECT COLUMN NAME HERE !!!

      if (!$lMOPId) {
        CCor_Qry::exec('UPDATE `_ferrero_jobs` SET `done`=\'B\' WHERE `jobid`=\''.$lJobId.'\';'); // PUT IN CORRECT COLUMN NAME HERE !!!
      } else {
        foreach ($lJobTypes as $lKey => $lJobType) {
          $lCls = new CApp_Finder($lJobType, $lJobId, NULL, $lMid); // PUT IN CORRECT MID HERE !!!
          $lDir = $lCls -> getPath();

          if (file_exists($lDir)) {
            $lAliceDir = rtrim($lDir, DS);
            $lParentDir = dirname($lAliceDir);
            $lBobDir = $lParentDir.DS.$lMOPId;

            rename($lAliceDir, $lBobDir);
            CCor_Qry::exec('UPDATE `_ferrero_jobs` SET `done`=\'Y\' WHERE `jobid`=\''.$lJobId.'\';'); // PUT IN CORRECT COLUMN NAME HERE !!!
            CCor_Qry::exec('UPDATE `_ferrero_jobs` SET `src`=\''.$lJobType.'\' WHERE `jobid`=\''.$lJobId.'\';'); // PUT IN CORRECT COLUMN NAME HERE !!!

            break;
          } else {
            CCor_Qry::exec('UPDATE `_ferrero_jobs` SET `done`=\'C\' WHERE `jobid`=\''.$lJobId.'\';'); // PUT IN CORRECT COLUMN NAME HERE !!!
          }
        }
      }

      #mkdir($lDir, 0777, TRUE);
    }

    $this -> redirect('index.php?act=devtools.createdummyfolders');
  }
  
  protected function actDalimnoteid() {
    $lRet = '';
    $lSql = 'SELECT * FROM al_dalim_notes';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lXml = $lRow['xml'];
      $lDoc = simplexml_load_string($lXml);
      $lId = $lDoc['DisplayID'];
      if (!empty($lId)) {
        $lSql = 'UPDATE al_dalim_notes SET num='.intval($lId);
        $lSql.= ' WHERE id='.$lRow['id'].';';
        $lRet.= $lSql.LF;
      }
      $lParent = $lDoc['ParentID'];
      if (!empty($lParent)) {
        $lSql = 'UPDATE al_dalim_notes SET parent_id='.intval($lParent);
        $lSql.= ' WHERE id='.$lRow['id'].';';
        $lRet.= $lSql.LF;
      }
    }
    $lRet = '<textarea cols="50" rows="20">'.$lRet.'</textarea>';
    $this->renderMenu($lRet);
  }
}