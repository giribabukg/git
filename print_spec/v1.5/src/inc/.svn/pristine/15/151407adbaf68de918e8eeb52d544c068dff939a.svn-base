<?php
class CInc_Api_Xchange_Xmlparser extends CCor_Obj {

  protected function getFields() {
    if (!isset($this->mFields)) {
      $this->createFields();
    }
    return $this->mFields;
  }

  protected function createFields() {
    $this->mFields = array();
    $this->addField('project_no', 'ProjectNumber');
    $this->addField('project_name', 'ProjectName');
    $this->addField('apm', 'APM', 'userlastfirst');
    $this->addField('pspm', 'PSPM', 'userlastfirst');
    $this->addField('ddl_01', 'ArtDate');
	  $this->addField('ddl_03', 'FreezeDate');
    $this->addField('ddl_06', 'LockDate');
    $this->addField('ddl_07', 'PackDate');
    $this->addField('ddl_05', 'ShipDate');
    $this->addField('ddl_02', 'ArriveDate');
    $this->addField('ddl_04', 'PostDue');
  }

  protected function addField($aAlias, $aSource, $aFormatter = null) {
    if (empty($aAlias)) return;
    $this->mFields[$aAlias] = array('src' => $aSource, 'fmt' => $aFormatter);
  }

  public function getFieldSql() {
    $lRet = '';
    $lFields = $this->getFields();
    foreach ($lFields as $lAlias => $lRow) {
      $lSql = 'ALTER TABLE al_xchange_projects_'.MID.' ADD '.$lAlias.' VARCHAR(255);'.LF;
      $lRet.= $lSql;
    }
    return $lRet;
  }

  protected function getJobFields() {
    if (!isset($this->mJobFields)) {
      $this->createJobFields();
    }
    return $this->mJobFields;
  }

  protected function createJobFields() {
    $this->mJobFields = array();
    $this->addJobField('itemno', 'ItemNumber');
    $this->addJobField('agency', 'ArtonWAVE', 'agency');
    $this->addJobField('artsgnsts', 'ArtSignStatus');
    $this->addJobField('businessunit', 'BusinessUnit');
    $this->addJobField('category', 'Category', 'category');
    $this->addJobField('pick_list', 'PickListPromoDescription');
    $this->addJobField('prodescription', 'PromotionDescription');
    $this->addJobField('dbqty',         'DatabaseQuantities');
    $this->addJobField('is_difference', 'Difference');
    $this->addJobField('bqcheck',       'BaanQtyCheck');
    $this->addJobField('invonord',      'InventoryOnOrder');
    $this->addJobField('picktotal', 'PickTotal');
    $this->addJobField('overrunqty', 'OverrunQuantity');
    $this->addJobField('productionqty', 'QuantitytoProduce');
    $this->addJobField('idlunitcost', 'IDLUnitCost');
    $this->addJobField('exttot', 'ExtendedTotal');
    $this->addJobField('producedby', 'Producedby');
	$this->addJobField('e_artdate', 'e_artdate');
    $this->addJobField('e_packdate', 'e_packdate');
    $this->addJobField('e_freezedate', 'e_freezedate');
    $this->addJobField('e_shipdate', 'e_shipdate');
    $this->addJobField('e_lockdate', 'e_lockdate');
    $this->addJobField('e_arrivedue', 'e_arrivedue');
    $this->addJobField('e_postdue', 'e_postdue');
	$this->addJobField('signtype', 'signtype');
    $this->addJobField('dimensions', 'dimensions');
    $this->addJobField('material', 'material');
    $this->addJobField('iscolor', 'iscolor');
    $this->addJobField('isfinish', 'isfinish');
  }

  protected function addJobField($aAlias, $aSource, $aFormatter = null) {
    $this->mJobFields[$aAlias] = array('src' => $aSource, 'fmt' => $aFormatter);
  }

  public function getJobFieldSql() {
    $lRet = '';
    $lFields = $this->getJobFields();
    foreach ($lFields as $lAlias => $lRow) {
      $lSql = 'ALTER TABLE al_xchange_jobs_'.MID.' ADD '.$lAlias.' VARCHAR(255);'.LF;
      $lRet.= $lSql;
    }
    return $lRet;
  }


  public function parse($aXml) {
    try {
      $lRet = $this->doParse($aXml);
      return $lRet;
    } catch (Exception $exc) {
      $this->msg();
    }
  }

  protected function doParse($aXml) {
    $this->mDoc = simplexml_load_string($aXml);
    $lName = $this->mDoc->getName();

    if ($lName == 'Campaign') {
      $lRet = $this->doParseCampaign();
      if ($lRet) {
        return $this->doInsertCampaign($lRet, $aXml);
      } else {
        return false;
      }
    }
    if ($lName == 'CampaignItems') {
      $lRet = $this->doParseItems();
      return $lRet;
    }

    return false;
  }

  protected function doParseCampaign() {
    $lRet = array();
    $lBase = $this->mDoc->CreateCampaign;
    #var_dump($lBase);
    $lFields = $this->getFields();
    #echo $this->getFieldSql();

    foreach ($lFields as $lAlias => $lRow) {
      $lSource = $lRow['src'];
      $lFormatHelper = $lRow['fmt'];

      $lNode = $lBase->xpath($lSource);
      $lVal = (string)$lNode[0];
      $lFmt = $lRow['fmt'];
      if (!empty($lFmt)) {
        $lFunc = 'format'.$lFmt;
        if ($this -> hasMethod($lFunc)) {
          $lVal = $this -> $lFunc($lVal);
        }
      }

      $lRet[$lAlias] = $lVal;
    }
    return $lRet;
  }

  protected function doInsertCampaign($aData, $aXml = '') {
    $lDat = $aData;
    $lDat['x_src'] = 'pro';
    $lDat['x_import_date'] = date('Y-m-d H:i:s');
    $lDat['x_xml'] = $aXml;

    $lSql = 'INSERT INTO al_xchange_projects_'.MID.' SET ';
    foreach ($lDat as $lKey => $lVal) {
      $lSql.= $lKey.'='.esc($lVal).',';
    }
    $lSql = strip($lSql).';';
    return CCor_Qry::exec($lSql);
  }

  protected function formatUserlastfirst($aVal) {
    $lUsr = CCor_Res::extract('fullname', 'id', 'usr');
    if (!isset($lUsr[$aVal])) {
      $this->msg('Xchange: Unknown user '.$aVal);
      return null;
    }
    return $lUsr[$aVal];
  }

  protected function formatCategory($aVal) {
    $lGru = CCor_Res::extract('name', 'id', 'gru', 174);
    #var_dump($lGru);
    if (!isset($lGru[$aVal])) {
      $this->msg('Xchange: Unknown category '.$aVal);
      return null;
    }
    return $lGru[$aVal];
  }

  protected function formatAgency($aVal) {
    $lGru = CCor_Res::extract('name', 'id', 'gru', 170);
    #var_dump($lGru);
    if (!isset($lGru[$aVal])) {
      $this->msg('Xchange: Unknown Agency '.$aVal);
      return null;
    }
    return $lGru[$aVal];
  }

  protected function formatDate_us($aVal) {
    if (empty($aVal)) return '';
    $lArr = explode('/', $aVal);
    return $lArr[2].'-'.$lArr[0].'-'.$lArr[1];
  }

  protected function doParseItems() {
    $lRet = true;
    $lBase = $this->mDoc->CreateCampaignItems->Items;
    #var_dump($lBase);
    #$lSql = $this->getJobFieldSql(); echo $lSql;
    foreach ($lBase as $lItem) {
      $lRes = $this->parseItem($lItem);
      if (false == $lRes) {
        $lRet = false;
      }
    }
    return $lRet;
  }

  protected function parseItem($aNode) {
    $lBase = $aNode;
    #var_dump($lBase);
    $lFields = $this->getJobFields();
    foreach ($lFields as $lAlias => $lRow) {
      $lSource = $lRow['src'];
      $lFormatHelper = $lRow['fmt'];

      $lNode = $lBase->xpath($lSource);
      #var_dump($lNode);
      #echo $lAlias.' '.(string)$lNode[0].BR;
      $lVal = (string)$lNode[0];
      $lFmt = $lRow['fmt'];
      if (!empty($lFmt)) {
        $lFunc = 'format'.$lFmt;
        if ($this -> hasMethod($lFunc)) {
          $lVal = $this -> $lFunc($lVal);
        }
      }
      $lRet[$lAlias] = $lVal;
    }
    $this->doInsertItem($lRet, $lBase->asXml());
    return true;
  }

  protected function doInsertItem($aData, $aXml = '') {
    $lDat = $aData;
    $lDat['x_src'] = 'art';
    $lDat['x_import_date'] = date('Y-m-d H:i:s');
    $lDat['x_xml'] = $aXml;

    $lSql = 'INSERT INTO al_xchange_jobs_'.MID.' SET ';
    foreach ($lDat as $lKey => $lVal) {
      $lSql.= $lKey.'='.esc($lVal).',';
    }
    $lSql = strip($lSql).';';
    return CCor_Qry::exec($lSql);
  }
}