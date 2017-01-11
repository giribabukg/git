<?php
class CInc_Job_Fil_Src_Pdf extends CJob_Fil_Files {

  public function __construct($aSrc, $aJobId, $aSub = 'pdf', $aDiv = '', $aFrom = 'sub', $aAge = 'job', $aDebug = FALSE, $aUploadButton = TRUE) {
    $lSub = 'pdf';
    parent::__construct($aSrc, $aJobId, $lSub, $aDiv, $aFrom, $aAge, TRUE, $aUploadButton);

    // Title und Upload im Header
    $this -> mTitle = lan('job-fil.'.$this -> mSub);
    if (('arc' != $this -> mAge AND $this -> mUsr -> canInsert('job-'.$this -> mSub)) && !bitset($this -> mFlags, jfOnhold)) {//Recht: PDF 'job-pdf'
      $lUpload = TRUE;
    } else {
      $lUpload = FALSE;
    }
    $this -> mUpload = $lUpload;
    $this -> mCompare = FALSE;
    $this -> mFileMask = CCor_Cfg::get('job.files.pdf.mask', '');

    $this -> addCtr(); # $this -> addColumn('dgif', '', FALSE, array('width' => '16'));

    if (CCor_Cfg::get('job-fil.comment', TRUE)) {
      $this -> addColumn('mor', '', FALSE, array('width' => '16', 'id' => 'mor'));
    }

    $this -> addColumn('name',  lan('lib.file.name'), TRUE, array('width' => '50%', 'id' => 'name'));

    if (!CCor_Cfg::get('job-fil.comment', TRUE)) {
      $this -> addColumn('comment', lan('lib.file.comment'), TRUE, array('width' => '50%', 'id' => 'comment'));
    }

    if (!empty($this -> mCategory)) {
      $this -> addColumn('category', lan('lib.file.category'), TRUE, array('id' => 'category'));
    }
    #$this -> addColumn('version', lan('lib.version'), TRUE);
    $this -> addColumn('size',  lan('lib.file.size'), TRUE, array('id' => 'size'));
    #$this -> addColumn('owner', lan('lib.file.user'), TRUE);
    $this -> addColumn('date',  lan('lib.file.time.modification'), TRUE, array('id' => 'date'));
    if ('arc' != $this -> mAge AND $this -> mUsr -> canDelete('job-pdf')) {
      $this -> addDel();
    }
    
    # global vision
    $this -> addColumn('gv',  '', false, array('width' => '16px'));
    $this -> addColumn('onlygv',  '', false, array('width' => '16px'));
    $this -> mGvLnk = 'index.php?act=job-fil.globalvision&jid=';
    
    $lWecSrc = CCor_Cfg::get('wec.jobs', array());
    if ('arc' != $this -> mAge AND !empty($lWecSrc) AND in_array($this -> mSrc , $lWecSrc)) {
      $this -> mWecUpload = TRUE;
    } else {
      $this -> mWecUpload = FALSE;
    }
    if ($this -> mUsr -> canInsert('job-wec-pdf') AND $this -> mWecUpload) {
      $this -> addColumn('wecupload', '', FALSE, array('width' => '16', 'id' => 'wecupload'));
    }

    $this -> getMoreFileInfo();
    $lUploadViaAlink = CCor_Cfg::get('wec.upload.alink', TRUE);
    if ($lUploadViaAlink) {
      $this -> mIte = $this -> getAlinkIterator();
    }
    else $this -> mIte = $this -> getFolderIterator();
  }

  protected function getMoreFileInfo() {
    //enthaelt Infos zum Loeschen durch User, Webcenterupload-Moegl., File-Kategorie
    $lSql = 'SELECT * FROM al_job_files WHERE jobid LIKE '.esc('%'.$this -> mJobId.'%').' AND src='.esc($this -> mSrc).' AND sub='.esc($this -> mSub).' ORDER BY id ASC';
    $lDbResFiles = new CCor_Qry($lSql);
    foreach ($lDbResFiles as $lDbFile) {
      $lDbFile instanceof CCor_Dat;
      $lArray = $lDbFile -> toArray();
      $this -> mDbFiles[$lArray['filename']] = $lArray;
    }
  }

  public function getFolderIterator() {
    $lRet = array();
    $lFinder = new CApp_Finder($this -> mSrc, $this -> mJobId);
    $lDir = $lFinder -> getDynPath(CCor_Cfg::get('flink.destination.pdf.dir'));
    $this -> msg('CInc_Job_Fil_Src_Pdf uses getFolderIterator at: '.$lDir);

    if (file_exists($lDir)) {
      try {
        $lIte = new DirectoryIterator($lDir);
        foreach ($lIte as $lLin) {
          $lItm = array();
          if ($lIte -> isFile()) {
            $lNam = $lIte -> getFilename();
            $lItm['name']  = $lNam;
            $lItm['size']  = $lIte -> getSize();
            $lItm['date']  = $lIte -> getMTime();
            $lItm['uid']   = 0;
            $lItm['user']  = '';
            $lItm['category']  = '';
            $lItm['txt'] = '';
            $lRet[] = $lItm;
          }
        }
        $lRet = $this -> array_sort($lRet, $this -> mOrd, $this -> mDir);
      } catch (Exception $lExc) {
        $this -> dbg($lExc -> getMessage(), mlWarn);
      }
    }
    return $lRet;
  }

  function multiexplode($aDelimiters, $aString) {
    $lStrReplace = str_replace($aDelimiters, $aDelimiters[0], $aString);
    $lResult = explode($aDelimiters[0], $lStrReplace);
    return  $lResult;
  }

  public function getAlinkIterator() {
    $lRet = array();
    if ($this -> mUsr -> canRead('job-pdf')) {
      $lQry = new CApi_Alink_Query_Getpdflist($this -> mJobId, $this -> mFileMask, '', $this -> mOrd, $this -> mDir);
      $lTmp = array();
      foreach ($lQry as $lRow) {
        $lItm = array();
        $lItm['name'] = (string) $lRow['filename'];
        $lItm['size'] = (int) $lRow['filesize'];
        $lFileDate = (string) $lRow['filedate'];
        $lDat = preg_split('@[/.:-\s]@', $lFileDate);
        if (!$lDat) {
          $lDat = $this -> multiexplode(array('.', ':', '-', ' '), $lFileDate);
        }
        $lSplitChar = substr($lRow["filedate"], 2, 1);
        if ($lSplitChar == "/") { // US date format
          $lItm['date'] = mktime($lDat[3], $lDat[4], $lDat[5], $lDat[0], $lDat[1], $lDat[2]);
        } else { // German date format
          $lItm['date'] = mktime($lDat[3], $lDat[4], $lDat[5], $lDat[1], $lDat[0], $lDat[2]);
        }
        $lItm['category'] = '';
        if (isset($this -> mDbFiles[$lItm['name']])) {
          $lItm['category'] = (!empty($this -> mDbFiles[$lItm['name']]['category']) ? $this -> mDbFiles[$lItm['name']]['category'] : '');
        }
        $lItm['txt'] = '';
        if (isset($this -> mDbFiles[$lItm['name']])) {
          $lItm['txt'] = (!empty($this -> mDbFiles[$lItm['name']]['txt']) ? $this -> mDbFiles[$lItm['name']]['txt'] : '');
        }
        $lRet[] = $lItm;
      }

      $lRet = $this -> array_sort($lRet, $this -> mOrd, $this -> mDir);
    }
    return $lRet;
  }

  protected function getMorJScript() {
    $lRet = 'href="javascript:Flow.Std.togTr(\''.$this -> mMoreId.'\')"';
    return $lRet;
  }

  protected function getTdMor() {
    if (CCor_Cfg::get('job-fil.comment', TRUE)) {
      $lShowComments = CCor_Cfg::get('job-fil.comment.open', 0); // 0: closed; 1: open when content; 2: open

      $lTxt = $this -> getVal('txt');

      $this -> mAfterRow = TRUE;
      $this -> mMoreId = getnum('tr');

      $lRet = '<a class="nav"'.$this -> getMorJScript().'>';
      $lRet.= '...</a>';

      if (($lShowComments == 1 && !empty($lTxt)) || ($lShowComments == 2)) {
        $lRet.= '<script>jQuery(function(){Flow.Std.togTr(\''.$this -> mMoreId.'\');});</script>';
      }

      return $this -> tdc($lRet);
    }
  }

  protected function getTdComment() {
    if (!CCor_Cfg::get('job-fil.comment', TRUE)) {
      $lName = $this -> getVal('name');
      $lTxt = $this -> getVal('txt');

      $this -> mMoreId = getnum('tr');

      $lArgs = array(
        'age' => $this -> mAge,
        'src' => $this -> mSrc,
        'jid' => $this -> mJobId,
        'sub' => $this -> mSub,
        'fil' => $lName,
        'div' => $this -> mDiv,
        'td' => $this -> mMoreId,
      );
      $lArgsJSONEncode = json_encode($lArgs);

      $lRet = '<div class="outerdiv" id="'.$this -> mMoreId.'_outerdiv">';
      $lRet.= '  <table class="w100p h100p">';
      $lRet.= '    <tr>'.LF;
      $lRet.= '      <td style="white-space:normal">';
      $lRet.= '        <div id="'.$this -> mMoreId.'_innerdiv">';
      $lRet.= '          <div class="w100p h100p p4" id="'.$this -> mMoreId.'_txtdiv">';
      if ($this -> mUsr -> canEdit('job-pdf')) {
        $lRet.= '          <script type="text/javascript">'.LF;
        $lRet.= '            jQuery(function() {'.LF;
        $lRet.= '              Flow.File.init('.$lArgsJSONEncode.');'.LF;
        $lRet.= '            })'.LF;
        $lRet.= '          </script>'.LF;
      }
      $lRet.= $lTxt;
      $lRet.= '          </div>';
      $lRet.= '        </div>';
      $lRet.= '      </td>';
      $lRet.= '    </tr>';
      $lRet.= '  </table>';
      $lRet.= '</div>';

      return $this -> tdc($lRet);
    }
  }

  protected function afterRow() {
    $lName = $this -> getVal('name');
    $lTxt = $this -> getVal('txt');
    $lRet = parent::afterRow();

    $lArgs = array(
      'age' => $this -> mAge,
      'src' => $this -> mSrc,
      'jid' => $this -> mJobId,
      'sub' => $this -> mSub,
      'fil' => $lName,
      'div' => $this -> mDiv,
      'td' => $this -> mMoreId,
    );
    $lArgsJSONEncode = json_encode($lArgs);

    if ($this -> mAfterRow) {
      $lRet.= '<tr id="'.$this -> mMoreId.'" style="display:none" data-mark="comment">';
      $lRet.= '  <td class="td1 tg">&nbsp;</td>';

      $lCol =  $this -> mColCnt - 1;
      $lRet.= '  <td class="frm p0" colspan="'.$lCol.'">';

      $lRet.= '    <div class="outerdiv" id="'.$this -> mMoreId.'_outerdiv">';
      $lRet.= '      <table class="w100p h100p">';
      $lRet.= '        <tr>'.LF;
      $lRet.= '          <td>';
      $lRet.= '            <div id="'.$this -> mMoreId.'_innerdiv">';
      $lRet.= '              <div class="w100p h100p p4" id="'.$this -> mMoreId.'_txtdiv">';
      if ($this -> mUsr -> canEdit('job-pdf')) {
      $lRet.= '                  <script type="text/javascript">'.LF;
      $lRet.= '                      jQuery(function() {'.LF;
      $lRet.= '                          Flow.File.init('.$lArgsJSONEncode.');'.LF;
      $lRet.= '                      })'.LF;
      $lRet.= '                  </script>'.LF;
      }
      $lRet.= $lTxt;
      $lRet.= '              </div>';
      $lRet.= '            </div>';
      $lRet.= '          </td>';
      $lRet.= '        </tr>';
      $lRet.= '      </table>';
      $lRet.= '    </div>';
      $lRet.= '  </td>';
      $lRet.= '</tr>'.LF;
    }

    return $lRet;
  }
  
  protected function getGvLink() {
    $lName = $this -> getVal('name');
    $lId = $this -> getVal($this -> mIdField);
    $lRet = $this -> mGvLnk.$this -> mJobId.'&src='.$this -> mSrc.'&fname='.$lName;
    return htm($lRet);
  }
  
  protected function getTdGv() {
    if (CCor_Cfg::get('globalvision.available', TRUE)) {
      $lName = $this -> getVal('name');
      $lFileName = preg_replace('/\\.[^.\\s]{3,4}$/', '', $lName);
      $lTrimNam = trim($lName);
      if ( !(isset($this -> mDbFiles[$lTrimNam]) AND $this -> mUsrId == $this -> mDbFiles[$lTrimNam]['uid'] AND 'N' == $this -> mDbFiles[$lTrimNam]['ToGv']) ) return $this -> td();
      $lRet = '';
      $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw w16 ac">';
      $lLink = 'Flow.Std.cnf(\'' . $this -> getGvLink() . '\', \'cnfUpl\')';
      $lRet .= '<a class="nav" href="javascript:' . $lLink . '">';
      $lRet .= 'GV';
      $lRet .= img('img/ico/16/upload-hi.gif');
      $lRet .= '</a>';
  
      $lRet.= '</td>'.LF;
      return $lRet;
    }
  }
  protected function getTdOnlyGv() {
    if (CCor_Cfg::get('globalvision.available', TRUE)) {
      $lName = $this -> getVal('name');
      $lFileName = preg_replace('/\\.[^.\\s]{3,4}$/', '', $lName);
      $lTrimNam = trim($lName);
      if ( !(isset($this -> mDbFiles[$lTrimNam]) AND $this -> mUsrId == $this -> mDbFiles[$lTrimNam]['uid'] AND 'N' == $this -> mDbFiles[$lTrimNam]['ToGv']) ) return $this -> td();
      $lRet = '';
      $lRet = '<td class="'.$this -> mCls.($this -> mHighlight ? 'r': '').' nw w16 ac">';
      $lLink = 'Flow.Std.cnf(\'' . $this -> getGvLink().'&OnlyGv='.'OnlyGv' . '\', \'cnfUplGv\')';
      $lRet .= '<a class="nav" href="javascript:' . $lLink . '">';
      $lRet .= img('img/ico/16/upload-hi.gif');
      $lRet .= '</a>';
      // }
      $lRet.= '</td>'.LF;
      return $lRet;
    }
  }
}