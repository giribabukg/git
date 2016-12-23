<?php
class CInc_Utl_Mem_Cnt extends CCor_Cnt {
  
  protected function actJs() {
    $lVie = new CUtl_Mem_List();
    echo $lVie -> getJs();
    exit;
  }
  
}