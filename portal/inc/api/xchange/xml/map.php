<?php
class CInc_Api_Xchange_Xml_Map extends CApi_Xchange_Xml_Base {

  protected function doParse($aXml) {
    $lDoc = new DOMDocument;
    $lDoc->preserveWhiteSpace = false;
    @$lDoc->loadXML($aXml);
    $lXpath = new DOMXpath($lDoc);

    $lMap = $this->getParam('map', 'core.xml');
    $lMapping = $this->loadMap($lMap);
    $lRet = array();
    foreach ($lMapping as $lAlias => $lRow) {
      $lNat = $lRow['native'];
      if (empty($lNat)) {
        $lRet[$lAlias] = null;
        continue;
      }


      $lVal = null;
      $lNodes = $lXpath->query($lNat);
      if ($lNodes->length == 1) {
        $lVal = $lNodes->item(0)->value;
      }
      $lFil = $lRow['filter_rule'];
      if (!empty($lFil)) {
        echo $lFil.BR;
        $lVal = CApp_Filter::filter($lVal, $lFil);
      }
      $lRet[$lAlias] = $lVal;
    }
    return $lRet;
  }

  protected function loadMap($aMapName) {
    $lSql = 'SELECT alias,native,filter_rule FROM al_fie_map_items ';
    $lSql.= 'WHERE map_id=(SELECT id FROM al_fie_map_master WHERE name='.esc($aMapName).')';
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lMap[$lRow['alias']] = $lRow;
    }
    return $lMap;
  }



}
