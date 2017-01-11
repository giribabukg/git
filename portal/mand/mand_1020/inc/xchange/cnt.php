<?php
class CXchange_Cnt extends CCust_Xchange_Cnt {

  protected function actStd() {
    $lTab = new CXchange_Tabs('job');
    $lVie = new CXchange_Joblist();
    $lRet = $lTab->getContent();
    $lRet.= $lVie->getContent();
    $this -> render($lRet);
  }
}
