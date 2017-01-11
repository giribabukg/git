<?php
class CInc_Job_Fil_Files extends CHtm_List {

  /*
   * File Category, only used if isset al_htb_item['fil']
   */
  protected $mCategory = array();
  protected $mDbFiles = array();
  protected $mJobId;
  protected $mSrc;
  protected $mSub;
  protected $mAge = 'job';
  protected $mCatView;
  protected $mFlags;

  /*
   * User Infos: Rechte
  */
  public $mUsr;

  public function __construct($aSrc, $aJobId, $aSub = '', $aDiv = '', $aFrom = '', $aAge = 'job', $aWecUpload = TRUE, $aUploadButton = TRUE) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
    $this -> mSub = $aSub;
    $this -> mDiv = $aDiv;
    $this -> mFrom = $aFrom;
    $this -> mAge = $aAge;
    $this -> mWecUpload = $aWecUpload;
    $this -> mUploadButton = $aUploadButton;

    $lSql = 'SELECT flags FROM al_job_shadow_'.MID;
    $lSql.= ' WHERE src='.esc($this -> mSrc);
    $lSql.= ' AND jobid='.esc($this -> mJobId);
    $this -> mFlags = CCor_Qry::getInt($lSql);

    $this -> mUsr = CCor_Usr::getInstance();
    $this -> mUsrId = $this -> mUsr -> getId();

    $this -> mShowSubHdr = FALSE;

    parent::__construct($this -> mAge.'-'.$this -> mSrc.'-fil');

    $this -> mOrdLnk = $this -> mStdLink.'.ord&amp;sub='.$this -> mSub.'&amp;jobid='.$this -> mJobId.'&amp;fie=';
    $this -> mLnkSrcJobId = '&src='.$this -> mSrc.'&jid='.$this -> mJobId;
    $this -> mLinkDefault = 'index.php?act=utl-fil.down'.$this -> mLnkSrcJobId.'&sub='.$this -> mSub.'&fn=';

    $this -> loadCategories();

    $this -> mDefaultOrder = 'date';
    $this -> mOrd  = $this -> mUsr -> getPref($this -> mPrf.'.'.$this -> mSub.'.ord', $this -> mDefaultOrder);
    $this -> mDir  = 'asc';
    if (substr($this -> mOrd, 0, 1) == '-') {
      $this -> mOrd = substr($this -> mOrd, 1);
      $this -> mDir = 'desc';
    }

    $this -> setAtt('class', 'p0 w100p');

    $this -> mCatView = $this -> getCategoryView();
  }

  protected function loadCategories() {
    $this -> mCategory = CCor_Res::get('htb', array('fil', 'id', 'value'));
  }

  protected function getTitle() {
    $lRet = '<tr>'.LF;
    $lRet.= '<td nowrap="nowrap" class="'.$this -> mCapCls.'" '.$this -> getColspan().'>'.LF;

    $lRet.= '<table style="width: 100%">';
    $lRet.= '<tr style="width: 100%">'.LF;

    $lRet.= '<td nowrap="nowrap" class="captxt p8" style="width: 1%">'.LF;
    $lRet.= $this -> getTitleContent();
    $lRet.= '</td>'.LF;

    if ($this -> mUpload && $this -> mUploadButton && !CCor_Cfg::get('flink', FALSE)) {
      $lRet.= $this -> getUploadButton();
    } elseif ($this -> mUpload && $this -> mUploadButton && CCor_Cfg::get('flink', FALSE)) {
      $lRet.= $this -> getFlinkButton();
    }

    if ($this -> mCompare) {
      $lRet.= $this -> getCompareButton();
    }

    $lRet.= $this -> getNoticeTextInHeader();

    if (($this -> mCatView['cat.switch.button'] == 1) OR (($this -> mCatView['cat.switch.button'] == 2) AND $this -> mUsr -> canEdit('job-'.$this -> mSrc.'.fil.cat.switch.button'))) {
      $lRet.= $this -> getSwitchCategoryViewButton();
    }

    $lComments = CCor_Cfg::get('job-fil.comment', TRUE); // 0: closed; 1: open when content; 2: open
    $lCommentsOpen = CCor_Cfg::get('job-fil.comment.open', 0); // 0: closed; 1: open when content; 2: open
    if (($lComments == TRUE) && ($lCommentsOpen == 1 || $lCommentsOpen == 2)) {
      $lRet.= $this -> getToggleShowCommentsButton();
    }

    $lRet.= '</tr>';
    $lRet.= '</table>'.LF;

    $lRet.= '</td>'.LF;
    $lRet.= '</tr>'.LF;

    return $lRet;
  }

  protected function getUploadButton() {
    $lParams = array(
      'act' => 'job-fil.upload',
      'src' => $this -> mSrc,
      'jid' => $this -> mJobId,
      'sub' => $this -> mSub,
      'div' => $this -> mDiv,
      'loading_screen' => TRUE
    );
    $lParamsJSONEnc = json_encode($lParams);
    $lJs = 'Flow.Std.ajxUpd('.$lParamsJSONEnc.');';

    $lRet = '';
    $lRet.='<td class="w50" align="right">';
    $lRet.= btn(lan('lib.upload'), $lJs, 'img/ico/16/new-hi.gif', 'button', array('class' => 'btn white'));
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getFlinkButton() {
    $lFId = uniqid('flink_');

    // get upload_max_filesize from php.ini
    $lMFS = getBytes(ini_get('upload_max_filesize'));
    $lMFS = $lMFS < 2097152 ? $lMFS : 2097152;

    // get all translations relating to flink.* from al_sys_lang as there is no filter for the language cache right now
    $lLan = array();
    $lSql = 'SELECT code,value_'.LAN.' AS value FROM al_sys_lang WHERE mand IN (0,'.MID.') AND code like "flink.%" ORDER BY code ASC;';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lLan[$lRow['code']] = $lRow['value'];
    }

    // get datetime format
    $lFFD = lan('lib.datetime.short');

    // get user fullname
    $lUFN = $this -> mUsr -> getFullName();

    // get file types
    $lFiletype = CCor_Cfg::get('flink.destination.'.$this -> mSub.'.filetype', '');

    // format file type
    $lFiletypeLst = array();
    if ($lFiletype) {
      $lFiletypeDotLst = preg_split("/(,|;)/", $lFiletype);
      foreach ($lFiletypeDotLst as $lKey => $lValue) {
        $lValue = trim($lValue, '\0\t\n\x0B\r *');
        $lValue = strtolower($lValue);
        $lFiletypeLst[] = $lValue;
        $lFiletypeDotLst[$lKey] = substr($lValue, 0, 1) == '.' ? $lValue : '.'.$lValue;
      }

      $lFiletypeLst = implode(',', $lFiletypeLst);
      $lFiletype = implode(',', $lFiletypeDotLst);
      $lFiletype = 'accept="'.$lFiletype.'"';
    }

    $lArgs = array(
      'age' => $this -> mAge,
      'src' => $this -> mSrc,
      'jid' => $this -> mJobId,
      'sub' => $this -> mSub,
      'cat' => $this -> mCategory,
      'div' => $this -> mDiv,
      'fid' => $lFId,
      'mfs' => $lMFS,
      'lan' => $lLan,
      'ffd' => $lFFD,
      'ufn' => $lUFN,
      'fty' => $lFiletypeLst
    );
    $lArgsJSONEnc = json_encode($lArgs);

    $lRet = '<script type="text/javascript">';
    $lRet.= '    jQuery(function() {';
    $lRet.= '        Flow.flink.addMultiple('.$lArgsJSONEnc.');';
    $lRet.= '    })';
    $lRet.= '</script>';

    $lRet.= '<td align="left">';
    $lRet.= '    <div class="dn" id="flink_files_select" title="'.lan('lib.file.upload').'">';
    $lRet.= '        <table>';
    $lRet.= '            <thead id="flink_head_list">';
    $lRet.= '                <tr class="hi">';
    $lRet.= '                    <td class="th2 ar w16" id="ctr"></td>';
    $lRet.= '                    <td class="th2 nw" id="name">'.lan('lib.file.name').'</td>';
    $lRet.= '                    <td class="th2 nw w16" id="category">'.lan('lib.file.category').'</td>';
    $lRet.= '                    <td class="th2 nw w16" id="size">'.lan('lib.file.size').'</td>';
    $lRet.= '                    <td class="th2 nw" id="user">'.lan('lib.file.user').'</td>';
    $lRet.= '                    <td class="th2 nw w16" id="date">'.lan('lib.file.time.modification').'</td>';
    $lRet.= '                    <td class="th2 nw w100p" id="edit">'.lan('lib.file.comment').'</td>';

    // >progress all< button
    $lRet.= '                    <td class="th2 nw w100 p4" id="flink_head_list_div_progress">';
    $lRet.= '                        <div class="w100" id="progress_value">';
    $lRet.= '                            <div id="progress_text"></div>';
    $lRet.= '                        </div>';
    $lRet.= '                    </td>';

    // >upload all< button
    $lRet.= '                    <td class="th2 nw w16" id="flink_head_list_button_upload">';
    $lRet.= '                        <button class="btn" id="flink_head_list_button_upload_all" type="button" onclick="Flow.flink.uploadAllMultiple();">';
    $lRet.= '                            <div class="al">';
    $lRet.= '                                <table class="al w25p" cellpadding="2" cellspacing="0" border="0">';
    $lRet.= '                                    <tbody>';
    $lRet.= '                                        <tr>';
    $lRet.= '                                            <td>';
    $lRet.= '                                                <img src="img/ico/16/upload-hi.gif" alt="+">';
    $lRet.= '                                            </td>';
    $lRet.= '                                            <td class="nw al" id="flink_head_list_button_upload_all_text_raw">'.lang('flink.upload.all', null, '').'</td>';
    $lRet.= '                                        </tr>';
    $lRet.= '                                    </tbody>';
    $lRet.= '                                </table>';
    $lRet.= '                            </div>';
    $lRet.= '                        </button>';
    $lRet.= '                    </td>';

    // >cancel all< button
    $lRet.= '                    <td class="th2 nw w16" id="flink_head_list_button_cancel">';
    $lRet.= '                        <button class="btn" id="flink_head_list_button_cancel_all" type="button" onclick="Flow.flink.cancelAllMultiple();">';
    $lRet.= '                            <div class="al">';
    $lRet.= '                                <table lass="al w25p" cellpadding="2" cellspacing="0" border="0">';
    $lRet.= '                                    <tbody>';
    $lRet.= '                                        <tr>';
    $lRet.= '                                            <td>';
    $lRet.= '                                                <img src="img/ico/16/ml-4.gif" alt="-">';
    $lRet.= '                                            </td>';
    $lRet.= '                                            <td class="nw al" id="flink_head_list_button_cancel_all_text_raw">'.lang('flink.cancel.all', null, '').'</td>';
    $lRet.= '                                        </tr>';
    $lRet.= '                                    </tbody>';
    $lRet.= '                                </table>';
    $lRet.= '                            </div>';
    $lRet.= '                        </button>';
    $lRet.= '                    </td>';

    // >togggle all< button
    $lRet.= '                    <td class="th2 nw w16 checkbox_toggle" id="flink_head_list_checkbox_toggle">';
    $lRet.= '                        <input id="flink_head_list_checkbox_toggle_all" onclick="Flow.flink.toggleAllMultiple('.htmlspecialchars(json_encode($lLan)).');" type="checkbox">';
    $lRet.= '                        <label class="bld" for="flink_head_list_checkbox_toggle_all">'.lang('flink.toggle.all', null, '').'</label>';
    $lRet.= '                    </td>';

    $lRet.= '                </tr>';
    $lRet.= '            </thead>';
    $lRet.= '            <tbody id="flink_body_list">';
    $lRet.= '            </tbody>';
    $lRet.= '        </table>';
    $lRet.= '    </div>';
    $lRet.= '    <div id="flink_container" class="flink_button">';
    $lRet.= '        <input name="files[]" type="file" id="'.$lFId.'" multiple '.$lFiletype.'/>';
    $lRet.= '    </div>';
    $lRet.= btn(lan('flink.add'), 'jQuery("#'.$lFId.'").trigger("click");', 'img/ico/16/new-hi.gif', 'button', array('id' => 'flink_head_button_add', 'disabled' => 'disabled'));
    $lRet.= '</td>';

    return $lRet;
  }

  protected function getCompareButton() {
    $lRet = '';
    $lRet.='<td align="left">';
    $lJs = 'var lSel = new Array();';
    $lJs.= 'jQuery(".wec-comp:checked").each(function(){lSel.push(jQuery(this).val())});';

    $lJs.= 'jQuery.post(';
    $lJs.= '"index.php?act=job-fil.comparewec&src='.$this -> mSrc.'&jid='.$this -> mJobId.'",';
    $lJs.= '{doc:lSel},';
    $lJs.= 'function(aData){location.href=aData;}';
    $lJs.= ');'; // jQuery.post

    $lRet.= btn(lan('lib.compare'), $lJs, 'img/ico/16/copy-hi.gif');
    $lRet.= '</td>';

    return $lRet;
  }

  protected function getNoticeTextInHeader() {
    return '';
  }

  protected function getSwitchCategoryViewButton() {
    $lParams = array(
        'act' => 'job-'.$this -> mSrc.'-fil.get',
        'src' => $this -> mSrc,
        'jid' => $this -> mJobId,
        'sub' => $this -> mSub,
        'div' => $this -> mDiv,
        'age' => $this -> mAge,
        'loading_screen' => TRUE
    );
    $lParamsJSONEnc = json_encode($lParams);
  
    $lTxt = lan('lib.cat.view.switch') === 'lib.cat.view.switch' ? '' : lan('lib.cat.view.switch');
    $lImg = array(0 => 'img/ico/16/table-select-row.png', 1 => 'img/ico/16/table-select-column.png');
  
    $lRet = '';
    $lRet.='<td align="right" style="width: 1%">';
    $lJs = 'jQuery.ajax({
      type : "post",
      url : "index.php?act=job-fil.switchcategoryview",
      data : {src: "'.$this -> mSrc.'", catview: "'.$this -> mCatView['cat.view'].'"}
      }).always(function (data) {
        Flow.Std.ajxUpd('.$lParamsJSONEnc.');
      });';
    $lRet.= btn($lTxt, $lJs, $lImg[$this -> mCatView['cat.view']]);
    $lRet.= '</td>';
  
    return $lRet;
  }

  protected function getToggleShowCommentsButton() {
    $lJs = 'jQuery("tr[data-mark=\"comment\"]").toggle();';

    $lRet = '';
    $lRet.='<td align="right" style="width: 1%">';
    $lRet.= btn(lan('job-fil.toggle-showcomments'), $lJs, 'img/ico/16/log.gif');
    $lRet.= '</td>';
  
    return $lRet;
  }
  
  protected function getTdName(){
    $lRet = '';

    $lNam = $this -> getVal('name');
    $lLink = $this -> getVal('link');

    $this -> mTheFileLink = '';
    if (!empty($lLink)) {
      $lLnk = $lLink;
    } else {
      $lLnk = $this -> mLinkDefault.urlencode($lNam);
    }
    $this -> mFileLink = $lLnk;
    $lLnk = htm($lLnk);
    $this -> mTheFileLink = '<a href="'.$lLnk.'">';

    if ($this -> mUsr -> canEdit('job-fil-doc')) {
      $lRet.= $this -> mTheFileLink;
      $lRet.= htm($lNam);
      $lRet.= '</a>';
    } else {
      $lRet.= htm($lNam);
    }
    return $this -> td($lRet);
  }

  protected function getTdCategory() {
    $lVal = $this -> getVal('category');
    $lNam = trim($this -> getVal('name'));

    if ($lVal > -1 && isset($this -> mCategory[$lVal])) {
      $lCat = $this -> mCategory[$lVal];
    } else {
      $lCat = lan('lib.file.category.not.selected');
    }

    if ($this -> mUsr -> canEdit('job-fil')) {
      $lArgs = array(
        'age' => $this -> mAge,
        'src' => $this -> mSrc,
        'jid' => $this -> mJobId,
        'sub' => $this -> mSub,
        'cat' => $this -> mCategory,
        'fil' => htm($lNam),
        'old' => $lVal,
        'new' => -1,
        'div' => $this -> mDiv
      );
      $lArgsJSONEncode = json_encode($lArgs);
      $lArgsHTMLSpecialChars = htmlspecialchars($lArgsJSONEncode);

      $lRet = '<div class="dn" id="category-change" title="'.lan('lib.file.category').'">';
      $lRet.= '  <p class="w100p h100p ac vam" id="select">&nbsp;</p>';
      $lRet.= '</div>';
      $lRet.= '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw">';
      $lRet.= '  <a class="nav" onclick="Flow.cat.change('.$lArgsHTMLSpecialChars.');">'.$lCat.'</a>';
      $lRet.= '</td>'.LF;

      return $lRet;
    } else {
      return $this -> td($lCat);
    }
  }

  protected function getTdSize() {
    $lVal = $this -> getVal('size');
    return $this -> td($this -> fmtSize($lVal));
  }

  protected function getTdUser() {
    $lRet = '';
    $lNam = $this -> getVal('user');
    $lRet.= htm($lNam);
    return $this -> td($lRet);
  }

  protected function getTdDate() {
    $lVal = $this -> getVal('date');
    return $this -> td($this -> fmtDate($lVal));
  }

  protected function getTdVersion() {
    $lRet = '';
    $lNam = $this -> getVal('version');
    $lRet.= '<span class="app-version">'.htm($lNam).'</span>';
    return $this -> tdc($lRet);
  }

  protected function getTdDel() {
    $lNam = $this -> getVal('name');
    $lTrimNam = trim($lNam);
    $lUsr = $this -> getVal('uid');

    if (isset($this -> mDbFiles[$lTrimNam]) AND $this -> mUsrId == $this -> mDbFiles[$lTrimNam]['uid']) { //pdf
      $lDelAllowed = TRUE;
    } elseif (!empty($lUsr) AND $this -> mUsrId == $lUsr) { //doc
      $lDelAllowed = TRUE;
    } else {
      $lDelAllowed = FALSE;
    }

    if ($lDelAllowed) {
      $lDel = "javascript:Flow.Std.jobDelFile('".$this -> mDiv."',";
      $lDel.= "'".htm($this -> mSrc)."',";
      $lDel.= "'".htm($this -> mJobId)."',";
      $lDel.= "'".htm($this -> mSub)."',";
      $lDel.= "'".htm(addslashes($lNam))."',";
      $lDel.= "'".LAN."')";

      $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw w16 ac">';
      $lRet.= '<a class="nav" onclick="'.$lDel.'">';
      $lRet.= img('img/ico/16/del.gif');
      $lRet.= '</a>';
      $lRet.= '</td>'.LF;
      return $lRet;
    } else {
      return $this -> td();
    }
  }

  protected function getTdWecupload() { //Aufruf in pdf
    $lNam = $this -> getVal('name');
    $lTrimNam = trim($lNam);
    if ( !(isset($this -> mDbFiles[$lTrimNam]) AND $this -> mUsrId == $this -> mDbFiles[$lTrimNam]['uid'] AND 'N' == $this -> mDbFiles[$lTrimNam]['ToWec']) ) return $this -> td();

    $lUpl = "Flow.Std.jobWecUploadFile('".$this -> mDiv."',";
    $lUpl.= "'".htm($this -> mSrc)."',";
    $lUpl.= "'".htm($this -> mJobId)."',";
    $lUpl.= "'".htm($this -> mSub)."',";
    $lUpl.= "'".htm($lNam)."',";
    $lUpl.= "'".LAN."')";

    $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw w16 ac">';
    $lRet.= '<a class="nav" onclick="'.$lUpl.'">';
    $lRet.= img('img/ico/16/wecupl.gif');
    $lRet.= '</a>';
    $lRet.= '</td>';
    return $lRet;
  }

  protected function getMoreFileInfo() {
    //enthaelt Infos zum Loeschen durch User, Webcenterupload-Moegl., File-Kategorie
    $lSql = 'SELECT * FROM al_job_files WHERE jobid LIKE '.esc('%'.$this -> mJobId.'%').' AND src='.esc($this -> mSrc).' ORDER BY id ASC';#.' AND sub='.esc($this -> mSub);
    $lDbResFiles = new CCor_Qry($lSql);
    foreach ($lDbResFiles as $lDbFile) {
      $lDbFile instanceof CCor_Dat;
      $lArray = $lDbFile -> toArray();
      $this -> mDbFiles[ $lArray['filename'] ]= $lArray;
    }
  }

  protected function fmtSize($aBytes) {
    $lVal = $aBytes;
    $lRet = $lVal.' Bytes';
    if ($lVal > 1024) {
      $lRet = number_format($lVal / 1024, 1).' kB';
    }
    $lMb = 1024 * 1024;
    if ($lVal > $lMb) {
      $lRet = number_format($lVal / $lMb, 1).' MB';
    }
    return $lRet;
  }

  protected function fmtDate($aTimestamp) {
    return date(lan('lib.datetime.short'), $aTimestamp);
  }

  public function array_sort($aArr, $aSortField, $order='asc') {
    $lArr = Array();
    $lArr = $aArr;
    $lSortField = $aSortField;

    $new_array = array();
    $sortable_array = array();

    if (count($lArr) > 0) {
      foreach ($lArr as $k => $v) {
        if (is_array($v)) {
          foreach ($v as $k2 => $v2) {
            if ($k2 == $lSortField) {
              $sortable_array[$k] = $v2;
            }
          }
        } else {
          $sortable_array[$k] = $v;
        }
      }

      switch ($order) {
        case 'asc':
          asort($sortable_array);
          break;
        case 'desc':
          arsort($sortable_array);
          break;
      }

      foreach ($sortable_array as $k => $v) {
        $new_array[$k] = $lArr[$k];
      }
    }

    return $new_array;
  }

  protected function getHead() {
    $lRet = '';

    if ($this -> mShowHdr) {
      $lRet = $this -> getTitle();
    }
    if ($this -> mShowSubHdr) {
      $lRet.= $this -> getSubHeader();
    }

    $lRet.= $this -> getFilterBar();

    if ($this -> mShowSerHdr) {
      $lRet.= $this -> getSearchBar();
    }
    if ($this -> mShowColHdr) {
      $lRet.= $this -> getColumnHeaders();
    }

    if (!empty($lRet)) {
      $lRet = '<thead id="files_head_list">'.LF.$lRet.'</thead>'.LF;
    }

    return $lRet;
  }

  protected function getCont() {
    if ($this -> mCatView['cat.view'] == 1) {
      $this -> removeColumn('category');
      $this -> mIte = $this -> array_sort($this -> mIte, 'category');
    }

    $lRet = parent::getTag();
    $lRet.= $this -> getHead();
    $lRet.= '<tbody id="files_body_list">'.LF;
    $lRet.= $this -> getBody();
    $lRet.= '</tbody>'.LF;

    $lRet.= $this -> getColumnFooter();

    $lRet.= parent::getEndTag();
    return $lRet;
  }

  protected function getGroupHeader() {
    $lRet = '';

    if ($this -> mCatView['cat.view'] == 1) {
      if (!empty($this -> mGrp)) {
        $lNew = $this -> getVal($this -> mGrp);
        if ($lNew !== $this -> mOldGrp) {
          $lVal = $lNew;
          if (isset($this -> mCategory[$lNew])) {
            $lVal = $this -> mCategory[$lNew];
          }
          $lRet = TR;
          $lRet.= '<td class="tg1" '.$this -> getColspan().'>';
          $lRet.= htm($lVal).NB;
          $lRet.= '</td>';
          $lRet.= _TR;
          $this -> mOldGrp = $lNew;
          $this -> mCls = 'td1';
        }
      }
    }

    return $lRet;
  }

  protected function getCategoryView() {
    $lUsrView = $this -> mUsr -> getPref('job-'.$this -> mSrc.'.fil.cat.view');

    $lSql = 'SELECT val FROM al_sys_pref WHERE code="job-'.$this -> mSrc.'.fil.cat.switch.button" AND mand='.MID;
    $lSwitchButton = CCor_Qry::getInt($lSql);

    if (($lUsrView AND ($lSwitchButton == 1)) OR ($lUsrView AND ($lSwitchButton == 2) AND $this -> mUsr -> canRead('job-'.$this -> mSrc.'.fil.cat.rig'))) {
      return array('cat.view' => $lUsrView, 'cat.switch.button' => $lSwitchButton);
    } else {
      $lSql = 'SELECT val FROM al_sys_pref WHERE code="job-'.$this -> mSrc.'.fil.cat.view" AND mand='.MID;
      $lSysView = CCor_Qry::getInt($lSql);

      if ($lSysView) {
        return array('cat.view' => $lSysView, 'cat.switch.button' => $lSwitchButton);
      } else {
        $lSql = 'REPLACE INTO al_sys_pref (code, mand, grp, name_de, name_en, val) VALUES ("job-'.$this -> mSrc.'.fil.cat.view", "-1", "gen", "Category view (job type = '.$this -> mSrc.')", "Kategorie Ansicht (Auftragsart = '.$this -> mSrc.')", 0);';
        CCor_Qry::exec($lSql);

        $lSql = 'REPLACE INTO al_sys_pref (code, mand, grp, name_de, name_en, val) VALUES ("job-'.$this -> mSrc.'.fil.cat.view", "0", "gen", "Category view (job type = '.$this -> mSrc.')", "Kategorie Ansicht (Auftragsart = '.$this -> mSrc.')", 0);';
        CCor_Qry::exec($lSql);

        return array('cat.view' => 0, 'cat.switch.button' => $lSwitchButton);
      }
    }
  }
}