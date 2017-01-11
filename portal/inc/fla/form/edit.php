<?php
class CInc_Fla_Form_Edit extends CFla_Form_Base {

  public function __construct($aId) {
    $this -> mId = intval($aId);

    $this -> load();

    $lImg = 'img/flag/';
    $lImage = array('eve_'.flEve_act.'_ico', 'eve_'.flEve_conf.'_ico', 'amend_ico', 'approv_ico', 'condit_ico');
    foreach ($lImage as $lI) {
      if (!empty($this -> mVal[$lI])) {
        $this -> mImages[$lI] = img($lImg.$this -> mVal[$lI].'.gif');
      }
    }
    parent::__construct('fla.sedt', lan('flag.edt'));
    $this -> setParam('val[id]', $this -> mId);
    $this -> setParam('old[id]', $this -> mId);

    #echo '<pre>---edit.php---'.get_class().'---';var_dump($this -> mVal,'#############');echo '</pre>';
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_fla WHERE mand='.MID.' AND id='.$this -> mId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Step record not found', mtUser, mlError);
    }
  }
}