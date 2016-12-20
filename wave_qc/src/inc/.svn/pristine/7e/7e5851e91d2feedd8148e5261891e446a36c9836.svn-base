<?php
class CInc_Pck_Itm_Form_Base extends CHtm_Form {

  public function __construct($aAct, $aCaption, $aFields, $aFieldCaptions, $aCancel = NULL) {
    parent::__construct($aAct, $aCaption, $aCancel);

    $lArr[0] = '[All]';
    $lArr[MID] = MANDATOR_NAME;

    $lUseTypes = TRUE;

    $fields = CCor_Res::getByKey('alias', 'fie');
    $this -> addDef(fie('mand', lan('lib.mand'), 'select', $lArr ));
    foreach ($aFields as $lRow){
      $lAlias = $lRow['alias'];
      //var_dump($lRow);
      if ($lUseTypes && ($lRow['ignoretype'] == 'N') && isset($fields[$lAlias])) {
        $fie = $fields[$lAlias];
        $fie['alias'] = 'col'.$lRow['col'];
        $this->addDef($fie);
      } else if (!empty($lRow['htb'])) {
        $this -> addDef(fie('col'.$lRow['col'], $aFieldCaptions[$lRow['alias']], 'tselect', array('dom' => $lRow['htb'])));
      } else {
      $this -> addDef(fie('col'.$lRow['col'], $aFieldCaptions[$lRow['alias']]));
      }
    }

    $this -> setVal('mand', MID); // als Default bei neuem Eintrag
  }

  public function setDom($aDom) {
    $this -> setParam('dom', $aDom);
    $this -> setParam('val[domain]', $aDom);
    $this -> setParam('old[domain]', $aDom);

    $lMst = CCor_Res::extract('domain', 'description_'.LAN, 'pckmaster');
    if(isset($lMst[$aDom]) AND !empty($lMst[$aDom])) {
      $this -> mCap.= ' ('.$lMst[$aDom].')';
    }
  }

}