<?php

class CInc_Fie_Form_Nativesearchdialog extends CCor_Ren {

  protected $mTerm;

  public function __construct() {
    $lUsr = CCor_Usr::getInstance();
    $this->setSearchTerm($lUsr->getPref('fie.nativesearch'));
  }

  public function setSearchTerm($aTerm) {
    $this->mTerm = $aTerm;
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= '<div class="map-parent">'; // for search functions

    $lRet.= '<input type="text" class="inp w200 term" value="'.htm($this->mTerm).'" />'.NB;

    $lRet.= '<button onclick="Flow.FieldMap.search(this,\'fie.nativesearch\')">'.htmlan('lib.search').'</button>';

    $lRows = CCor_Res::extract('alias', 'alias', 'fiemap', array('map' => 'core.xml'));
    $lTerm = strtolower($this->mTerm);

    $lRet.= BR.BR;
    $lRet .= '<div style="width:100%; height:400px; overflow-y:auto; overflow-x:hidden">';
    $lRet .= '<table class="tbl" cellpadding="4" style="width:95%">';
    $lRet.= '<tr><td class="th2">Native</td></tr>';

    $lCls = 'td1';
    foreach ($lRows as $lVal) {
      $lRet.= '<tr class="hi val cp '.$lCls.'">';
      $lRet.= '<td>'.htm($lVal).'</td>';
      $lRet.= '</tr>'.LF;
      $lCls = ($lCls == 'td1') ? 'td2' : 'td1';
    }
    $lRet.= '</table>';

    $lRet.= '</div>';
    $lRet.= '</div>';

    return $lRet;
  }

}
