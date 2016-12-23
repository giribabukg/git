<?php
class CInc_Job_Fil_Src_Doc extends CJob_Fil_Files {

  public function __construct($aSrc, $aJobId, $aSub = 'doc', $aDiv = '', $aFrom = 'sub', $aAge = 'job', $aDebug = FALSE, $aUploadButton = TRUE) {
    $lSub = 'doc';

    parent::__construct($aSrc, $aJobId, $lSub, $aDiv, $aFrom, $aAge, FALSE, $aUploadButton);

    $lDelAllowed = FALSE;

    switch ($this -> mSrc) {
      case 'pro':
        $this -> mTitle = lan('job-pro.item');
        $lDelAllowed = TRUE;
        break;
      case 'sku':
        $this -> mTitle = lan('job-sku.item');
        break;
      default:
        $this -> mTitle = lan('job-fil.'.$this -> mSub);
        if ('arc' != $this -> mAge) {
          $lDelAllowed = TRUE;
        }
    }

    // START #818 job-doc right does not work
    $this -> mUpload = FALSE;
    $lJobFilDoc = $this -> mUsr -> canInsert('job-fil-doc');
    $lArcFilDoc = $this -> mUsr -> canInsert('arc-fil-doc');

    if ((('arc' != $this -> mAge && $lJobFilDoc) || $lArcFilDoc) && !bitset($this -> mFlags, jfOnhold)) {
      $this -> mUpload = TRUE;
    }
    // STOP #818 job-doc right does not work

    $this -> mCompare = FALSE;

    $this -> addCtr();

    if (CCor_Cfg::get('job-fil.comment', TRUE)) {
      $this -> addColumn('mor', '', FALSE, array('width' => '16', 'id' => 'mor'));
    }

    $this -> addColumn('name', lan('lib.file.name'), TRUE, array('width' => '50%', 'id' => 'name'));

    if (!CCor_Cfg::get('job-fil.comment', TRUE)) {
      $this -> addColumn('comment', lan('lib.file.edit'), TRUE, array('width' => '50%', 'id' => 'comment'));
    }

    if (!empty($this -> mCategory)) {
      $this -> addColumn('category', lan('lib.file.cat'), TRUE, array('id' => 'category'));
    }
    $this -> addColumn('size', lan('lib.file.size'), TRUE, array('id' => 'size'));
    $this -> addColumn('user', lan('lib.file.user'), TRUE, array('id' => 'user'));
    $this -> addColumn('date', lan('lib.file.date'), TRUE, array('id' => 'date'));
    if ($lDelAllowed AND $this -> mUsr -> canDelete('job-fil-doc')) {
      $this -> addDel();
    }

    $this -> mIte = $this -> getIterator();
  }

  public function getIterator() {
    $lRet = array();
    $lCls = new CApp_Finder($this -> mSrc, $this -> mJobId);
    $lDir = $lCls -> getPath('doc');

    if (file_exists($lDir)) {
      $lUsr = CCor_Res::extract('id', 'fullname', 'usr');
      $lArr = array();
      $lSql = 'SELECT id,mand,uid,filename,category,txt FROM al_job_files WHERE src='.esc($this -> mSrc).' ';
      $lSql.= ' AND sub='.esc($this -> mSub);
      $lSql.= ' AND jobid='.esc($this -> mJobId);
      if ($this -> mOrd != '') {
        $lSql.= ' ORDER BY '.esc($this -> mOrd).' '.$this -> mDir;
      }
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        #$lFilename = utf8_decode($lRow['filename']);
        $lFilename = $lRow['filename'];
        $lArr[$lFilename] = $lRow['uid'];
        $lArr[$lFilename.'.cat'] = $lRow['category'];
        $lArr[$lFilename.'.txt'] = $lRow['txt'];
      }
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
            $lItm['txt']  = '';
            if (isset($lArr[$lNam])) {
              $lUid = $lArr[$lNam];
              $lItm['uid'] = $lUid;
              if (isset($lUsr[$lUid])) {
                $lItm['user'] = $lUsr[$lUid];
              } else {
                $lItm['user'] = 'user '.$lUid;
              }
            }
            if (isset($lArr[$lNam.'.cat'])) {
              $lItm['category'] = $lArr[$lNam.'.cat'];
            }
            if (isset($lArr[$lNam.'.txt'])) {
              $lItm['txt'] = $lArr[$lNam.'.txt'];
            }
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
      if ($this -> mUsr -> canEdit('job-fil-doc')) {
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
      if ($this -> mUsr -> canEdit('job-fil-doc')) {
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
}