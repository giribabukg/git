<?php
class CInc_Ajx_Tip_Usr extends CCor_Ren {

  public function __construct($aId) {
    $this -> mId = intval($aId);
  }

  protected function getCont() {
  	$lRet = '';
  	
  	$lSql = 'SELECT firstname,lastname,email,location,phone FROM al_usr WHERE mand IN(0,' . MID . ') AND id=' . $this -> mId;
  	$lQry = new CCor_Qry($lSql);
  	$lCount = $lQry -> getAffectedRows();
    
  	if($lCount > 0){
	  	$lRow = $lQry -> getDat();
	    
	  	$lRet.= '<div class="th1">'.lan("ajx.tip.usr.title").'</div>';
	  	$lRet.= '<div class="frm p8" style="height:100%">';
	  	$lRet.= '<div>';
	  	$lRet.= '<b>'.lan("ajx.tip.usr.name").'</b> ' . htm($lRow['firstname'] . ' ' . $lRow['lastname']) . BR;
	  	$lRet.= '<b>'.lan("ajx.tip.usr.email").'</b> ' . htm($lRow['email']) . BR;
	  	$lRet.= '<b>'.lan("ajx.tip.usr.phone").'</b> ' . htm($lRow['phone']) . BR;
	  	$lRet.= '<b>'.lan("ajx.tip.usr.location").'</b> ' . htm($lRow['location']) . BR;
	  	$lRet.= '</div>';
	  	$lRet.= '</div>';
  	}

    return $lRet;
  }

}