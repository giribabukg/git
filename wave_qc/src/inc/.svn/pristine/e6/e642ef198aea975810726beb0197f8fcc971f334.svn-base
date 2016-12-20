<?php
class CInc_Utl_Pck_List extends CCor_Ren {
  
  public function __construct($aSrc, $aJobId, $aList) {
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
    $this -> mLis = $aList;
  }
  
  protected function getCont() {
    $lRet = '';
    
    $lRet.= '<table cellpadding="2" cellspacing="0" class="tbl w100p">'.LF;
    $lRet.= '<tr><td class="th1" colspan="3">Pickliste</td></tr>'.LF;
    
    $lSql = 'SELECT def.id,def.name_'.LAN.', chk.status FROM al_chk_items def, al_job_chk chk WHERE 1 ';
    $lSql.= 'AND def.src="'.$this -> mLis.'" ';
    $lSql.= 'AND def.id=chk.check_id ';
    $lSql.= 'AND chk.src="'.$this -> mSrc.'" ';
    $lSql.= 'AND chk.src_id='.$this -> mJobId.' ';
    
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lId  = $lRow['id'];
      $lSta = $lRow['status'];
      
      $lRet.= '<tr>';
      $lRet.= '<td class="td1 w16">';
      #$lRet.= '<a href="javascript:chkOk(\''.$lId.'\')">';
      $lImg = ($lSta == 1) ? 3 : 0;
      $lRet.= img('img/ico/16/flag-0'.$lImg.'.gif', array('id' => 'ok'.$lId));
      #$lRet.= '</a>';
      $lRet.= '</td>';
      
      $lRet.= '<td class="td1 w16">';
      #$lRet.= '<a href="javascript:chkNok(\''.$lId.'\')">';
      $lImg = ($lSta == -1) ? 1 : 0;
      $lRet.= img('img/ico/16/flag-0'.$lImg.'.gif', array('id' => 'ok'.$lId));
      #$lRet.= '</a>';
      $lRet.= '</td>';
      $lRet.= '<td class="td1">';
      $lRet.= htm($lRow['name_'.LAN]);
      $lRet.= '</td>';
    }
    
    $lRet.= '</table>'.BR;
    
    /*
    $lRet.= '<div class="tbl">';
    $lRet.= '<div class="th1">Comment</div>';
    $lRet.= '<div class="frm c p16">';
    $lRet.= '<textarea cols="20" rows="5" class="inp w400">Please add a comment...</textarea>';
    $lRet.= '</div>';
    $lRet.= '<div class="btnPnl">';
    $lRet.= btn(lan('lib.ok'), 'this.form.submit()', 'img/ico/16/ok.gif', 'submit').NB;
    $lRet.= btn(lan('lib.cancel'), "window.close()", 'img/ico/16/cancel.gif');
    $lRet.= '</div>';
    $lRet.= '</div>';
    */
    return $lRet;
  }
}