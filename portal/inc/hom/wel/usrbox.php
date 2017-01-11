<?php
class CInc_Hom_Wel_Usrbox extends CCor_Ren {

  public function __construct($aUid) {
    $this -> mUid = intval($aUid);
  }

  protected function getCont() {
    $lQry = new CCor_Qry('SELECT * FROM al_usr WHERE id='.$this -> mUid);
    if ($lRow = $lQry -> getDat()) {
      $lRet = '<table cellpadding="4" cellspacing="0" border="0">'.LF;
      $lRet.= '<tr>';
      $lRet.= '<td valign="top">';

      $lMnd = $lRow['mand'];
      
      $lImage = 'img/usr/usr-'.$this -> mUid.'.gif';
     
      /** First check if the user has Pic.
       * Look first in Client Order and then in Customer.
       * If No Picture then show default User Picture = usr-o.gif
       */
      if ((!file_exists(MAND_PATH.$lImage)) && (!file_exists(CUST_PATH.$lImage)) ) {
        $lImage = 'img/usr/usr-0.gif';
      }
      
      $lRet.= img($lImage);

      $lRet.= '</td>'.LF;
      $lRet.= '<td valign="top">'.LF;

      $lCtrHtb = CCor_Res::get('htb', 'sl1');

      $lRet.= '<table cellpadding="2" cellspacing="0" border="0">'.LF;
      $lRet.= '<tr><td><b>'.htm(lan('lib.name')).'</b></td>';
      $lRet.= '<td>'.htm($lCtrHtb[$lRow['anrede']].' '.$lRow['firstname'].' '.$lRow['lastname']).'</td></tr>'.LF;
      $lRet.= '<tr><td><b>'.htm(lan('lib.company')).'&nbsp;</b></td>';
      $lRet.= '<td>'.htm($lRow['company']).'</td></tr>'.LF;
      $lRet.= '<tr><td><b>'.htm(lan('lib.email')).'</b></td>';
      $lRet.= '<td>'.htm($lRow['email']).'</td></tr>'.LF;
      $lRet.= '<tr><td><b>'.htm(lan('lib.phone')).'</b></td>';
      $lRet.= '<td>'.htm($lRow['phone']).'</td></tr>'.LF;
      $lRet.= '</table>';

      $lRet.= '</td>'.LF;
      $lRet.= '</tr>'.LF;
      $lRet.= '</table>'.LF;
    } else {
      $lRet = 'Currently not assigned';
    }
    return $lRet;
  }

}