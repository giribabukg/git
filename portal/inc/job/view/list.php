<?php
class CInc_Job_View_List extends CCor_Ren {

  public function __construct($aSrc, $aJobId = 0) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
  }

  protected function getCont() {
    $lUid = CCor_USr::getAuthId();
    $lUsr = CCor_Usr::getInstance();

    $lRet = '';
    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl">'.LF;

    $lRet.= '<tr><td class="cap" colspan="3">'.htm(lan('lib.view.save')).'</td></tr>';
    $lRet.= '<tr><td class="sub" colspan="3">'.LF;
    $lRet.= '<form action="" method="post">'.LF;
    $lRet.= '<input type="hidden" name="act" value="job-view.snew" />'.LF;
    $lRet.= '<input type="hidden" name="src" value="'.htm($this -> mSrc).'" />'.LF;
    $lRet.= '<input type="hidden" name="jobid" value="'.htm($this -> mJobId).'" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0"><tr>'.LF;
    $lRet.= '<td>'.lan('lib.name').'</td>';
    $lRet.= '<td><input type="text" name="name" class="inp w200" /></td>';

    if ($lUsr -> canInsert('vie')) {
      $lRet.= '</tr><tr>';
      $lRet.= '<td>'.lan('lib.view.save_as').'</td>'.LF;
      $lRet.= '<td><select name="src_id" id="src_id" size="1" class="w200">';
      $lRet.= '<option value='.$lUid.' selected>';
      $lRet.= lan('lib.opt.my_views');
      $lRet.= '</option>';
      $lRet.= '<option value="">';
      $lRet.= lan('lib.opt.global_views');
      $lRet.= '</option>';
      $lRet.= '</select>';
      $lRet.= '</td>'.LF;
    } else {
      $lRet.= '<input type="hidden" name="src_id" value="'.$lUid.'" />'.LF;
    }

    $lRet.= '</tr><tr align="right">';
    $lRet.= '<td>'.btn(lan('lib.ok'),'', 'img/ico/16/ok.gif', 'submit').'</td>';
    if ($this -> mJobId != 0) {
      $lRet.= '<td>'.btn(lan('lib.cancel'),'go(\'index.php?act='.$this -> mSrc.'&jobid='.$this -> mJobId.'\')','img/ico/16/cancel.gif').'</td>';
    } else {
      $lRet.= '<td>'.btn(lan('lib.cancel'),'go(\'index.php?act='.$this -> mSrc.'\')','img/ico/16/cancel.gif').'</td>';
    }
    $lRet.= '</tr></table>'.LF;
    $lRet.= '</form>'.LF;
    $lRet.= '</td></tr>'.LF;

    $lSql = 'SELECT * FROM al_usr_view WHERE src_id= 0 AND mand='.MID.' AND ref="'.$this -> mSrc.'"  ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    $lCls = 'td1';
    if ($lRow = $lQry -> getAssoc()) {
      $lRet.= '<tr>';
      $lRet.= '<td class="cap" colspan="2">'.lan('lib.opt.global_views').'</td>';
      $lRet.= '</tr>'.LF;

      $lRet.= '<tr>';
      $lRet.= '<td class="th2 w400">'.lan('lib.name').'</td>';
      $lRet.= '<td class="th2 w16">&nbsp;</td>';
      $lRet.= '</tr>'.LF;

      foreach ($lQry as $lRow) {
        $lId = $lRow['id'];
        $lRet.= '<tr>';
        $lRet.= '<td class="'.$lCls.'">';
        $lRet.= '<a href="index.php?act=job-view.replace&amp;src='.htm($this -> mSrc).'&amp;id='.$lId.'" class="nav">';
        $lRet.= htm($lRow['name']);
        $lRet.= '</a>';
        $lRet.= '</td>'.LF;
        $lRet.= '<td class="'.$lCls.' ac">';
        if ($lUsr -> canInsert('vie')) {
          $lRet.= '<a class="nav" href="javascript:Flow.Std.cnf(\'index.php?act=job-view.del&amp;src='.$this -> mSrc.'&amp;id='.$lId.'\', \'cnfDel\')">';
        $lRet.= img('img/ico/16/del.gif');
        $lRet.= '</a>';
        }
        $lRet.= '</td>'.LF;
        $lRet.= '</tr>'.LF;
        $lCls = ($lCls == 'td1') ? 'td2' : 'td1';
      }
    }

    $lSql = 'SELECT * FROM al_usr_view WHERE src_id='.$lUid.' AND mand='.MID.' AND ref="'.$this -> mSrc.'"  ORDER BY name';
    $lQry = new CCor_Qry($lSql);
    $lCls = 'td1';
    if ($lRow = $lQry -> getAssoc()) {
      $lRet.= '<tr>';
      $lRet.= '<td class="cap" colspan="2">'.lan('lib.opt.my_views').'</td>';
      $lRet.= '</tr>'.LF;

      $lRet.= '<tr>';
      $lRet.= '<td class="th2 w400">Name</td>';
      $lRet.= '<td class="th2 w16">&nbsp;</td>';
      $lRet.= '</tr>'.LF;

      foreach ($lQry as $lRow) {
        $lId = $lRow['id'];
        $lRet.= '<tr>';
        $lRet.= '<td class="'.$lCls.'">';
        $lRet.= '<a href="index.php?act=job-view.replace&amp;src='.htm($this -> mSrc).'&amp;id='.$lId.'.&amp;jobid='.$this -> mJobId.'" class="nav">';
        $lRet.= htm($lRow['name']);
        $lRet.= '</a>';
        $lRet.= '</td>'.LF;
        $lRet.= '<td class="'.$lCls.' ac">';
        $lRet.= '<a class="nav" href="javascript:Flow.Std.cnf(\'index.php?act=job-view.del&amp;src='.$this -> mSrc.'&amp;id='.$lId.'&amp;jobid='.$this -> mJobId.'\', \'cnfDel\')">';
        $lRet.= img('img/ico/16/del.gif');
        $lRet.= '</a>';
        $lRet.= '</td>'.LF;
        $lRet.= '</tr>'.LF;
        $lCls = ($lCls == 'td1') ? 'td2' : 'td1';
      }
    }

    $lRet.= '</table>'.LF;
    return $lRet;
  }
}