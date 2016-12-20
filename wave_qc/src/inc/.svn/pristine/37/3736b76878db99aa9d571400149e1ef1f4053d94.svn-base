<?php
class CInc_Job_Fil_Src_Dalim extends CJob_Fil_Files {

  const VERSION_DELIMITER = '_';

  public function __construct($aSrc, $aJobId, $aSub = 'doc', $aDiv = '', $aFrom = 'sub', $aAge = 'job', $aDebug = FALSE, $aUploadButton = TRUE) {
    $lSub = 'dalim';

    parent::__construct($aSrc, $aJobId, $lSub, $aDiv, $aFrom, $aAge, FALSE, $aUploadButton);

    $lUsr = CCor_Usr::getInstance();
    $this -> mCanDelete = $lUsr -> canDelete('job-pdf');
    if ('arc' == $this -> mAge) {
      $this -> mCanDelete = FALSE;
    }

    $this -> mTitle = lan('job-fil.'.$this -> mSub);

    if (('arc' != $this -> mAge) && !bitset($this -> mFlags, jfOnhold)) {
      $this -> mUpload = TRUE;
    } else {
      $this -> mUpload = FALSE;
    }

    $this -> mDownloadPrevious = CCor_Cfg::get('dalim.download.previous', FALSE);

    $this -> mCompare = TRUE;

    $this -> addColumn('mor');
    $this -> addColumn('name', lan('lib.file.name'), TRUE, array('width' => '90%', 'id' => 'name'));
    $this -> addColumn('version', lan('lib.file.version'), TRUE, array('id' => 'version'));
    $this -> addColumn('size', lan('lib.file.size'), TRUE, array('id' => 'size'));
    $this -> addColumn('user', lan('lib.file.user'), TRUE, array('id' => 'user'));
    $this -> addColumn('date', lan('lib.file.date'), TRUE, array('id' => 'date'));

    $this -> mIte = $this -> getIterator();
    $this -> mGroupClass = uniqid('c_');
    $this -> mUpload = FALSE;
    $this -> mIsFirst = TRUE;
  }

  protected function getCompareButton() {
    $lRet ='<td align="right">';
    $lJs = 'Flow.dalim.compare("'.$this -> mSrc.'","'.$this -> mJobId.'")';
    $lRet.= btn(lan('lib.compare'), $lJs, 'img/ico/16/copy-hi.gif');
    $lRet.= '</td>';
    return $lRet;
  }

  public function getIterator() {
    $lRet = array();
    $lCls = new CApp_Finder($this -> mSrc, $this -> mJobId);
    $lDir = $lCls -> getPath('dalim');

    if (file_exists($lDir)) {
      $lUsr = CCor_Res::extract('id', 'fullname', 'usr');
      $lArr = array();
      $lSql = 'SELECT id,mand,uid,filename,category,lock_delete FROM al_job_files WHERE src='.esc($this -> mSrc).' ';
      $lSql.= 'AND jobid='.esc($this -> mJobId);

      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lArr[$lRow['filename']] = $lRow['uid'];
        $lArr[$lRow['filename'].'.cat'] = $lRow['category'];
        $lArr[$lRow['filename'].'.lock_delete'] = $lRow['lock_delete'];
      }
      try {
        $lIte = new DirectoryIterator($lDir);
        foreach ($lIte as $lLin) {
          $lItm = array();
          if ($lIte -> isFile()) {
            $lNam = $lIte -> getFilename();
            $lDisplay = $lNam;
            $lVersion = 1;
            $lPos = strrpos($lNam, self::VERSION_DELIMITER);
            if (false !== $lPos) {
              $lDisplay = substr($lNam,0,$lPos).'.'.$lIte -> getExtension();
              $lVersion = intval(substr($lNam, $lPos + 1));
            }
            $lItm['name']     = $lNam;
            $lItm['display']  = $lDisplay;
            $lItm['size']     = $lIte -> getSize();
            $lItm['date']     = $lIte -> getMTime();
            $lItm['uid']      = 0;
            $lItm['user']     = '';
            $lItm['category'] = '';
            $lItm['version']  = $lVersion;
            if (isset($lArr[$lNam])) {
              $lUid = $lArr[$lNam];
              $lItm['uid'] = $lUid;
              if (isset($lUsr[$lUid])) {
                $lItm['user'] = $lUsr[$lUid];
              } else {
                $lItm['user'] = 'user '.$lUid;
              }
            }
            if (isset($lArr[$lNam.'.lock_delete'])) {
              $lItm['lock_delete'] = $lArr[$lNam.'.lock_delete'];
            }
            $this -> mItems[$lDisplay][$lVersion] = $lItm;
            $lRet[$lDisplay] = $lItm;
          }
        }
        foreach ($this->mItems as $lDisplay => $lRows) {
          $lMax = $lRet[$lDisplay]['version'];
          foreach ($lRows as $lVersion => $lItm) {
            if ($lVersion > $lMax) {
              $lRet[$lDisplay] = $lItm;
              $lMax = $lVersion;
            }
          }
        }

        $lRet = $this -> array_sort($lRet, $this -> mOrd, $this -> mDir);
      } catch (Exception $lExc) {
        $this -> dbg($lExc -> getMessage(), mlWarn);
      }
    }
    return $lRet;
  }

  protected function getTdName() {
    $lRet = '';
    $lNam = $this -> getVal('name');
    $lDisplay = $this -> getVal('display');
    $lLink = $this -> getVal('link');

    $this -> mTheFileLink = '';
    if (!empty($lLink)) {
      $lLnk = $lLink;
    } else {
      $lLnk = $this -> mLinkDefault.urlencode($lNam);
    }
    $this -> mFileLink = $lLnk;
    $lLnk = htm($lLnk);
    $this -> mTheFileLink = '<a href="'.$lLnk.'" target="_blank">';

    $lRet.= $this -> mTheFileLink;
    $lRet.= htm($lDisplay);
    $lRet.= '</a>';
    return $this -> td($lRet);
  }

  protected function getTdMor() {
    $this -> mMoreId = uniqid('tr_');
    $lRet = '<a class="nav" onclick="Flow.togTrOne(\''.$this -> mMoreId.'\',\''.$this -> mGroupClass.'\')">';
    $lRet.= '...</a>';
    return $this -> tdc($lRet);
  }

  protected function afterRow() {
    $lStyle = $this -> mIsFirst ? 'table-row' : 'none';
    $this -> mIsFirst = FALSE;

    $lRet = '<tr style="display:'.$lStyle.'" id="'.$this -> mMoreId.'" class="'.$this -> mGroupClass.'">';

    $lRet.= '<td class="td1 tg">&nbsp;</td>';
    $lRet.= '<td class="p0" colspan="5">';

    $lRet.= '<table cellpadding="2" cellspacing="0" border="0" class="w100p">';
    $lDisplay = $this -> getVal('display');
    $lRows = $this -> mItems[$lDisplay];
    $this -> mSubGroupClass = uniqid('c_');
    krsort($lRows);

    $lIsFirst = TRUE;
    foreach ($lRows as $lRow) {
      $lRet.= $this -> getVersionRow($lRow, $lIsFirst);
      $lIsFirst = FALSE;
    }
    $lRet.= '</table>';

    $lRet.= '</td>';
    $lRet.= '</tr>';
    return $lRet;
  }

  protected function getVersionRow($aRow, $aIsFirst) {
    $lName = $aRow['name'];
    $lUrl = 'index.php?act=utl-dalim.open&doc='.$lName.'&src='.$this -> mSrc.'&jid='.$this -> mJobId;

    $lRet = '';
    $lRet.= '<tr>';

    $lMoreId = uniqid('tr_');

    $lRet.= '<td class="td1 w16 ac">';

    $lLnk = '<a class="nav" onclick="Flow.togTrOne(\''.$lMoreId.'\',\''.$this -> mSubGroupClass.'\')">';
    $lRet.= $lLnk;
    $lRet.= '...</a></td>';

    $lRet.= '<td class="td1 w16 ac">';
    $lDoc = $this -> mSrc.','.$this -> mJobId.','.$lName;
    $lRet.= '<input type="checkbox" value="'.htm($lDoc).'" class="beh-dalim-comp" />';
    $lRet.= '</td>';

    $lRet.= '<td class="td1 p4">'.$lLnk.'Version <span class="app-version">'.htm($aRow['version']).'</span>';
    $lRet.= ' '.NB.NB.'<span class="weak">'.htm(lan('lib.date')).' '.htm($this -> fmtDate($aRow['date']));
    $lRet.= '</span></a></td>';

    $lRet.= '</tr>'.LF;

    $lStyle = $aIsFirst ? 'table-row' : 'none';
    $lRet.= '<tr style="display:'.$lStyle.'" id="'.$lMoreId.'" class="'.$this -> mSubGroupClass.'"><td class="td1 tg">&nbsp;</td>';
    $lRet.= '<td colspan="4" class="p4 td1">';

    $lRet.= '<div class="w200 p8" style="float:left">';
    $lRet.= '<a href="'.$lUrl.'">';
    $lDoc = $this -> mJobId.'/'.$lName;
    $lRet.= '<img class="box" src="index.php?act=utl-dalim.thumb&amp;doc='.htm(urlencode($lDoc)).'" />';
    $lRet.= '</a>';
    $lRet.= '</div>';

    $lRet.= '<div class="w200 p8" style="float:left">';

    if ($aIsFirst || ($this -> mDownloadPrevious)) {
      $lDownloadLink = $this -> mLinkDefault.urlencode($lName);
      $lRet.= btn('Download', 'go(\''.$lDownloadLink.'\',\'tab\')', 'ico/16/pdf.png', 'button', array('class' => 'btn w300'));
      $lRet.= BR.BR;
    }

    $lNotesUrl = 'index.php?act=utl-dalim.downloadnotes&doc='.urlencode($this -> mJobId.'/'.$lName).'&fn='.$lName;
    $lRet.= btn('Download with annotations', "go('".$lNotesUrl."','tab')", 'ico/16/pdf_annotated.png', 'button', array('class' => 'btn w300'));
    $lRet.= BR.BR;
    
    $lNotesUrl = 'index.php?act=utl-dalim.downloadhiresnotes&doc='.urlencode($this->mJobId.'/'.$lName);
    $lRet.= btn('Download Hires (with notes)', "go('".$lNotesUrl."','tab')", 'ico/16/pdf_annotated.png', 'button', array('class'=> 'btn w300'));
    $lRet.= BR.BR;

    #$lUrl = htm($lUrl);
    $lRet.= btn('Open Viewer', 'go(\''.$lUrl.'\',\'tab\')', 'ico/16/pdf_annotated.png', 'button', array('class' => 'btn w300'));

    $lLock = $aRow['lock_delete'];
    if (($this -> mCanDelete) && ('N' == $lLock) && ($aIsFirst)) {
      $lRet.= BR.BR;
      $lJs = 'Flow.Std.ajxImg("'.$this -> mDiv.'","'.lan('lib.file.from').'"); ';
      $lJs.= 'Flow.Std.ajxUpd({act:"job-fil.deldalim", src:"'.$this -> mSrc.'", jid:"'.$this -> mJobId.'", fn:"'.$lName.'", div:"'.$this -> mDiv.'"});';
      $lRet.= btn('Delete Version', $lJs, 'ico/16/del.gif', 'button', array('class' => 'btn w300'));
    }

    $lUsr = CCor_Usr::getInstance();
    if (($this -> mUsr -> canRead('dbg'))) {
      $lRet.= BR.BR;
      $lJs = 'Flow.Std.ajxImg("'.$this -> mDiv.'","E'.lan('lib.file.from').'"); ';
      $lJs.= 'Flow.Std.ajxUpd({act:"job-fil.reregdalim", src:"'.$this -> mSrc.'", jid:"'.$this -> mJobId.'", fn:"'.$lName.'", div:"'.$this -> mDiv.'"});';
      $lRet.= btn('Reregister Version', $lJs, 'ico/16/del.gif', 'button', array('class' => 'btn w300'));
    }

    $lRet.= '</div>';

    $lRet.= '<div style="clear:both"></div>';

    $lRet.= '</td></tr>'.LF;
    return $lRet;
  }
}
