<?php
interface ICor_Ren {

  public function getContent();

}

class CInc_Cor_Ren extends CCor_Obj implements ICor_Ren {

  public function getContent($aCnt = '') {
    $lRet = $this -> onBeforeContent();
    $lRet.= $this -> getCont();
    $lRet.= $this -> onAfterContent();
    return $lRet;
  }

  function getComment($aTxt = '') {
    return LF.'<!-- '.get_class($this).' '.$aTxt.' -->'.LF;
  }

  protected function getCont() {
    return '';
  }

  protected function onBeforeContent() {
    return '';
  }

  protected function onAfterContent() {
    return '';
  }

  public function render($aCnt = '') {
    echo $this -> getContent();
  }

}