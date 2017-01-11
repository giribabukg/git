<?php
/**
 * Hilfstabellen unter Daten, können vom Kunden bearbeitet werden
 *
 * @package    htg
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 09:50:56 +0100 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CInc_Htg_Form_Edit extends CHtb_Form_Base {

  public function __construct($aId) {
    parent::__construct('htg.sedt', lan('htb.edt'));
    $this -> mId = intval($aId);
    $this -> setParam('val[id]', $this -> mId);
    $this -> setParam('old[id]', $this -> mId);
    $this -> load();
  }

  protected function load() {
    $lQry = new CCor_Qry('SELECT * FROM al_htb_master WHERE id='.$this -> mId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
}