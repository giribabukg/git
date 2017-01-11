<?php
interface ICor_Ren {

  public function getContent();

}

class CInc_Cor_Ren extends CCor_Obj implements ICor_Ren {

  public function getContent($aCnt = '') {
    // error_log('....CInc_Cor_Ren....getContent......'."\n",3,'logggg.txt');
    $lRet = $this -> onBeforeContent();
    $lRet.= $this -> getCont();
    $lRet.= $this -> onAfterContent();
    // error_log('....CInc_Cor_Ren....$lRet......'.var_export($lRet, true)."\n",3,'logggg.txt');
    return $lRet;
  }

  function getComment($aTxt = '') {
    return LF.'<!-- '.get_class($this).' '.$aTxt.' -->'.LF;
  }

  protected function getCont() {
    // error_log('....CInc_Cor_Ren....getConttttttt......'."\n",3,'logggg.txt');
    return '';
  }

  protected function onBeforeContent() {
    // error_log('....CInc_Cor_Ren....onBeforeContent......'."\n",3,'logggg.txt');
    return '';
  }

  protected function onAfterContent() {
    // error_log('....CInc_Cor_Ren....onAfterContent......'."\n",3,'logggg.txt');
    return '';
  }

  public function render($aCnt = '') {
    // error_log('....CInc_Cor_Ren....render......'."\n",3,'logggg.txt');
    echo $this -> getContent();
  }

}