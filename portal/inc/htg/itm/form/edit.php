<?php
/**
 * Hilfstabellen unter Daten, k�nnen vom Kunden bearbeitet werden
 *
 * @package    htg
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 09:50:56 +0100 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CInc_Htg_Itm_Form_Edit extends CHtb_Itm_Form_Base {

  public function __construct($aId, $aDom) {
    parent::__construct('htg-itm.sedt', lan('htb-itm.edt'), 'htg-itm&dom='.$aDom);
    $this -> mId = intval($aId);
    $this -> setParam('val[id]', $this -> mId);
    $this -> setParam('old[id]', $this -> mId);
    $this -> setDom($aDom);
    $this -> load();
    $this -> setParam('mand', $this -> getVal('mand'));
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_htb_itm WHERE mand IN(0,'.MID.') AND id='.$this -> mId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}