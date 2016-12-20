<?php
class CInc_Ajx_Tip_Grp extends CCor_Ren {

  public function __construct($aId) {
    $this -> mId = intval($aId);
  }

  protected function getCont() {
  	$lRet = '';
  	
  	$lSql = "SELECT usr.firstname as first, usr.lastname as last FROM al_usr usr INNER JOIN al_usr_mem mem ON usr.id=mem.uid WHERE ";
  	$lSql.= "mem.gid=" . $this -> mId . " AND usr.del='N' AND usr.mand IN(0," . MID . ")";
  	$lQry = new CCor_Qry($lSql);
  	$lCount = $lQry -> getAffectedRows();
  	
  	if($lCount > 0){
	    $lRet.= '<div class="th1">Group Members</div>';
	    $lRet.= '<div class="frm p8" style="height:100%">';
	    $lRet.= '<div>';
		foreach($lQry as $lRows){
			$lRet .= htm($lRows['first'] . ' ' . $lRows['last']) . BR;
		}
	    $lRet.= '</div>';
	    $lRet.= '</div>';
  	}

    return $lRet;
  }

}