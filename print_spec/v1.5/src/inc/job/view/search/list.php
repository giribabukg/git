<?php
class CInc_Job_View_Search_List extends CCor_Ren {

  public function __construct($aSrc, $aJobId = 0) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl">'.LF;
    $lRet.= '<tr><td class="cap" colspan="3">'.htm(lan('lib.search.save')).'</td></tr>';
    $lRet.= '<tr><td class="sub" colspan="3">'.LF;
    $lRet.= '<form action="" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="job-view-search.snew" />'.LF;
    $lRet.= '<input type="hidden" name="src" value="'.htm($this -> mSrc).'" />'.LF;
    $lRet.= '<input type="hidden" name="jobid" value="'.htm($this -> mJobId).'" />'.LF;

    $lRet.= '<table cellpadding="2" cellspacing="0"><tr>'.LF;
    $lRet.= '<td>'.lan('lib.name').'</td>';
    $lRet.= '<td><input type="text" name="name" class="inp w200" /></td>';
    $lRet.= '<td>'.btn(lan('lib.ok'),'', 'img/ico/16/ok.gif', 'submit').'</td>';
    if ($this -> mJobId != 0) {
      $lRet.= '<td>'.btn(lan('lib.cancel'),'go("index.php?act='.$this -> mSrc.'&amp;jobid='.$this -> mJobId.'")','img/ico/16/cancel.gif').'</td>';
    } else {
      $lRet.= '<td>'.btn(lan('lib.cancel'),'go("index.php?act='.$this -> mSrc.'")','img/ico/16/cancel.gif').'</td>';
    }
    $lRet.= '</tr></table>'.LF;

    $lRet.= '</form>'.LF;
    $lRet.= '</td></tr>'.LF;
    $lRet.= '<tr>';
    $lRet.= '<td class="th2 w16">&nbsp;</td>';
    $lRet.= '<td class="th2 w400">'.lan('lib.name').'</td>';
    $lRet.= '<td class="th2 w16">&nbsp;</td>';
    $lRet.= '</tr>'.LF;

    $lUid = CCor_Usr::getAuthId();
    $lSql = 'SELECT * FROM al_usr_search WHERE src_id='.$lUid.' ';
    $lSql.= 'AND mand="'.MID.'" ';

    switch ($this -> mSrc) {
      case 'job-pro':
        $lSql.= ' AND ref="pro" ';
        break;
      case 'job-sku':
        $lSql.= ' AND ref="sku" ';
        break;
      default:
        $lSql.= ' AND ref="job" ';
    }

    $lSql.= 'ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    $lCtr = 1;
    $lCls = 'td1';
    foreach ($lQry as $lRow) {
      $lId = $lRow['id'];
      $lRet.= '<tr>';
      $lRet.= '<td class="'.$lCls.' ar">'.$lCtr.'.</td>';
      $lRet.= '<td class="'.$lCls.'">';
      $lRet.= '<a href="index.php?act=job-view-search.replace&amp;src='.htm($this -> mSrc).'&amp;id='.$lId.'&amp;jobid='.$this -> mJobId.'" class="nav">';
      $lRet.= htm($lRow['name']);
      $lRet.= '</a>';
      $lRet.= '</td>'.LF;
      $lRet.= '<td class="'.$lCls.' ac">';
      $lRet.= '<a class="nav" href="javascript:Flow.Std.cnfDel(\'index.php?act=job-view-search.del&amp;src='.$this -> mSrc.'&amp;id='.$lId.'&amp;jobid='.$this -> mJobId.'\', \''.LAN.'\')">';
      $lRet.= img('img/ico/16/del.gif');
      $lRet.= '</a>';
      $lRet.= '</td>'.LF;
      $lRet.= '</tr>'.LF;
      $lCtr++;
      $lCls = ($lCls == 'td1') ? 'td2' : 'td1';
    }
    $lRet.= '</table>'.LF;
    return $lRet;
  }

}