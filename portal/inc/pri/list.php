<?php
class CInc_Pri_List extends CHtm_List {

  public function __construct() {
    parent::__construct('pri');
    $this -> setAtt('width', '100%');
    $this -> mTitle = lan('printer').' '.lan('lib.specsheet');

    $this -> addCtr();
    $this -> addColumn('name', lan('printer').' '.lan('lib.name'), TRUE);
    $this -> addColumn('file', lan('lib.specsheet'));
    if ($this -> mCanInsert) {
      $this -> addColumn('upload', lan('lib.upload'));
    }
    if ($this -> mCanDelete) {
      $this -> addDel();
    }

    $this -> getPrefs();
    $this -> mIte = new CCor_TblIte('al_gru');
    $this -> mIte -> addCnd('parent_id=4');     // <- BÖSE, BÖSE, BÖSE - scheinbar wird pri nicht genutzt !!!
    if (!empty($this -> mSer['name'])) {
      $lVal = addslashes($this -> mSer['name']);
      $lCnd = '(name LIKE "%'.$lVal.'%")';
      $this -> mIte -> addCnd('(name LIKE "%'.$lVal.'%")');
    }
    $this -> mIte -> setOrder('name');
    $this -> addPanel('sca', htmlan('lib.search'));
    $this -> addPanel('ser', $this -> getSearchForm());

    $this -> getFileList();
  }

  protected function getFileList() {
    $this -> mFil = array();
    $lFin = new CApp_Finder('pri',0);
    $lDir = $lFin -> getPath();
    $lIte = new DirectoryIterator($lDir);
    foreach ($lIte as $lLin) {
      if ($lIte -> isFile()) {
        $lNam = $lIte -> getFilename();
        $lPos = strpos($lNam, '_');
        if (FALSE !== $lPos) {
          $lArr = explode('_', $lNam,2);
          $this -> mFil[$lArr[0]] = $lNam;
        }
      }
    }
  }


  protected function getSearchForm() {
    $lRet = '';
    $lRet.= '<form action="index.php" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="pri.ser" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0"><tr>'.LF;
    $lVal = (isset($this -> mSer['name'])) ? htm($this -> mSer['name']) : '';
    $lRet.= '<td><input type="text" name="val[name]" class="inp" value="'.$lVal.'" /></td>'.LF;
    $lRet.= '<td>'.btn(lan('lib.search'),'','','submit').'</td>';
    if (!empty($this -> mSer)) {
      $lRet.= '<td>'.btn(lan('lib.all'),'go("index.php?act=pri.clser")').'</td>';
    }
    $lRet.= '</tr></table>';
    $lRet.= '</form>'.LF;

    return $lRet;
  }

  protected function getTdName() {
    $lVal = $this -> getVal('name');
    return $this -> td(htm($lVal));
  }

  protected function getFileName($aGid) {
    if (!isset($this -> mFil[$aGid])) {
      return FALSE;
    } else {
      return $this -> mFil[$aGid];
    }
  }

  protected function getTdFile() {
    $lGid = $this -> getInt('id');
    $lFil = $this -> getFileName($lGid);
    if ($lFil) {
      $lRet = '<a href="index.php?act=utl-fil.down&amp;src=pri&amp;id='.$lGid.'&amp;fn='.url($lFil).'" target="_blank" class="nav">';
      $lRet.= 'Download</a>';
      return $this -> td($lRet);
    } else {
      return $this -> td('');
    }
  }

  protected function getTdUpload() {
    $lGid = $this -> getInt('id');
    $lRet = '<a href="index.php?act=pri.upl&amp;id='.$lGid.'" target="_blank" class="nav">';
    $lRet.= 'Upload</a>';
    return $this -> td($lRet);
  }

}