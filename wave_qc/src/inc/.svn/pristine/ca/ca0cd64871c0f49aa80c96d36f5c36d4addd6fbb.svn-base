<?php
class CInc_Arc_Fil_Files extends CCor_Ren {

  protected $mSrc;
  protected $mJobId;
  protected $mSub;

  public function __construct($aSrc, $aJobId, $aSub = '', $aDiv = '') {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
    $this -> mSub = $aSub;
    $this -> mDiv = $aDiv;
  }

  protected function getIterator() {
    $lObj = $this -> factory();
    return $lObj -> getFileList();
  }

  protected function factory() {
    $lCls = 'CArc_Fil_Src_'.ucfirst($this -> mSub);
    return new $lCls($this -> mSrc, $this -> mJobId);
  }

  protected function getCont() {
    $lRet = '';

    if ('doc' == $this -> mSub) {
      $lRet.= '<script type="text/javascript">';
      $lRet.= 'function jobDelFile(aName) {';
      $lRet.= 'if (confirm("Do you really want to delete?")) {';
      $lRet.= 'Flow.Std.ajxImg("'.$this -> mDiv.'","Processing..."); new Ajax.Updater("'.$this -> mDiv.'","index.php",{parameters:';
      $lRet.= '{act:"arc-'.$this -> mSrc.'-fil.del",jid:"'.$this -> mJobId.'",sub:"'.$this -> mSub.'",div:"'.$this -> mDiv.'",name:aName } ';
      $lRet.= '});';
      $lRet.= '}}';
      $lRet.= '</script>'.LF;
    }

    $lRet.= '<table cellpadding="2" cellspacing="0" border="0">';

    if ('doc' == $this -> mSub) {
      $lRet.= '<tr><td colspan="8" class="sub p8">';

      $lJs = 'Flow.Std.ajxImg("'.$this -> mDiv.'","'.lan('lib.file.from').'"); new Ajax.Updater("'.$this -> mDiv.'","index.php",{parameters:';
      $lJs.= '{act:"arc-fil.upload",src:"'.$this -> mSrc.'",jid:"'.$this -> mJobId.'",sub:"'.$this -> mSub.'",div:"'.$this -> mDiv.'" } ';
      $lJs.= '});';

      $lRet.= btn(lan('lib.upload'), $lJs, 'img/ico/16/new-hi.gif');
      $lRet.= '</td></tr>';
    }
    $lUsr = CCor_Usr::getInstance();
    if ('pdf' == $this -> mSub && $lUsr -> canInsert('job-pdf')) {
      $lRet.= '<tr><td colspan="8" class="sub p8">';

      $lJs = 'Flow.Std.ajxImg("'.$this -> mDiv.'","'.lan('lib.file.from').'"); new Ajax.Updater("'.$this -> mDiv.'","index.php",{parameters:';
      $lJs.= '{act:"job-fil.upload",src:"'.$this -> mSrc.'",jid:"'.$this -> mJobId.'",sub:"'.$this -> mSub.'",div:"'.$this -> mDiv.'" } ';
      $lJs.= '});';

      $lRet.= btn(lan('lib.upload'), $lJs, 'img/ico/16/new-hi.gif');
      $lRet.= '</td></tr>';
    }

    $lRet.= '<tr>';
    $lRet.= '<td class="th2 w16"><img src="img/d.gif" width="12" alt="" /></td>';
    $lRet.= '<td class="th2 w100p">'.lan('lib.file.name').'</td>';
    if ('rtp' == $this -> mSub) {
      $lRet.= '<td class="th2">'.lan('lib.version').'</td>';
    }
    $lRet.= '<td class="th2">'.lan('lib.file.size').'</td>';
    if ('doc' == $this -> mSub) {
      $lRet.= '<td class="th2 nw">'.lan('lib.file.owner').'</td>';
    }
    $lRet.= '<td class="th2">'.lan('lib.file.date').'</td>';
    if ('doc' == $this -> mSub) {
      $lRet.= '<td class="th2 w16">&nbsp;</td>';
    }
    $lRet.= '</tr>';

    $lUid = CCor_Usr::getAuthId();

    $lCls = 'td1';
    $lCtr = 1;
    $lIte = $this -> getIterator();
    if (!empty($lIte))
    foreach ($lIte as $lRow) {
      $lRet.= '<tr class="hi">';
      if ('rtp' == $this -> mSub) {
        $lRet.= '<td class="'.$lCls.' ar"><input type="checkbox" /></td>';
      } else {
        $lRet.= '<td class="'.$lCls.' ar">'.$lCtr.'.</td>';
      }
      $lRet.= '<td class="'.$lCls.'">';
      if (!empty($lRow['link'])) {
        $lLnk = $lRow['link'];
      } else {
        $lLnk = 'index.php?act=utl-fil.down&src='.$this -> mSrc.'&jid='.$this -> mJobId.'&sub='.$this -> mSub.'&fn='.urlencode($lRow['name']);
      }
      $lRet.= '<a href="'.htm($lLnk).'" class="nav">';
      $lRet.= htm($lRow['name']);
      $lRet.= '</a>';
      $lRet.= '</td>';

      if ('rtp' == $this -> mSub) {
        $lRet.= '<td class="'.$lCls.' ac nw">'.$lRow['version'].'</td>';
      }
      $lRet.= '<td class="'.$lCls.' ar nw">'.$this -> fmtSize($lRow['size']).'</td>';
      if ('doc' == $this -> mSub) {
        $lRet.= '<td class="'.$lCls.' nw">'.htm($lRow['user']).'</td>';
      }
      $lRet.= '<td class="'.$lCls.' nw">'.$this -> fmtDate($lRow['date']).'</td>';
      if ('doc' == $this -> mSub) {
        $lOwn = $lRow['uid'];
        if ($lOwn == $lUid) {
          $lRet.= '<td class="'.$lCls.' ac">';
          $lRet.= '<a class="nav" onclick="Flow.Std.jobDelFile(\''.$this -> mDiv.'\',';
          $lRet.= '\''.htm($this -> mSrc).'\',';
          $lRet.= '\''.htm($this -> mJobId).'\',';
          $lRet.= '\''.htm($this -> mSub).'\',';
          $lRet.= '\''.htm(addslashes($lRow['name'])).'\',';
          $lRet.= '\''.LAN.'\')">';
          $lRet.= img('img/ico/16/del.gif');
          $lRet.= '</a>';
          $lRet.= '</td>';
        } else {
          $lRet.= '<td class="'.$lCls.' ac"></td>';
        }
      }
      $lUsr = CCor_Usr::getInstance();
      if ($lUsr -> canDelete('job-pdf') AND 'pdf' == $this -> mSub) {
          $lDel = 'Flow.Std.jobDelFile(\''.$this -> mDiv.'\',';
          $lDel.= '\''.htm($this -> mSrc).'\',';
          $lDel.= '\''.htm($this -> mJobId).'\',';
          $lDel.= '\''.htm($this -> mSub).'\',';
          $lDel.= '\''.htm(addslashes($lRow['name'])).'\',';
          $lDel.= '\''.LAN.'\')';
          $lRet.= '<td class="'.$lCls.' ac">';
          $lRet.= '<a class="nav" onclick="'.$lDel.'">';
          $lRet.= img('img/ico/16/del.gif');
          $lRet.= '</a>';
          $lRet.= '</td>';
      }
      $lRet.= '</tr>';
      $lCls = ($lCls == 'td1') ? 'td2' : 'td1';
      $lCtr++;
    }
    $lRet.= '</table>';
    #$lRet.= var_export($lIte, TRUE);
    #$lRet.= '</div>';
    return $lRet;
  }

  protected function fmtSize($aBytes) {
    $lVal = $aBytes;
    $lRet = $lVal.' Bytes';
    if ($lVal > 1024) {
      $lRet = number_format($lVal/1024,1).' kB';
    }
    $lMb = 1024 * 1024;
    if ($lVal > $lMb) {
      $lRet = number_format($lVal/$lMb,1).' MB';
    }
    return $lRet;
  }

  protected function fmtDate($aTimestamp) {
    return date('D '.lan('lib.datetime.short'), $aTimestamp);
  }

}