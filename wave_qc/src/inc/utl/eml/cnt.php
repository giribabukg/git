<?php
class CInc_Utl_Eml_Cnt extends CCor_Cnt {
  
  protected function actGet() {
    $lId  = $this -> getReqInt('id');
    $lQry = new CCor_Qry('SELECT mail_header,mail_body FROM al_sys_mails WHERE id='.$lId);
    if ($lRow = $lQry -> getAssoc()) {
      header("Content-type: message/rfc822");
      header("Content-Disposition: attachment; filename=\"email.eml\"");
      echo $lRow['mail_header'];
      echo $lRow['mail_body'];
    }
  }
  
}