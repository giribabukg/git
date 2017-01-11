<?php
class CInc_Fie_Map_Cnt extends CCor_Cnt {

  public function __construct(ICor_Req $aReq, $aMod, $aAct) {
    parent::__construct($aReq, $aMod, $aAct);
    $this -> mTitle = lan('fie-map.menu');
    $this -> mMmKey = 'opt';

    $lpn = 'fie-map';
    $lUsr = CCor_Usr::getInstance();
    if (!$lUsr -> canRead($lpn)) {
      //$this -> setProtection('*', $lpn, rdNone);
    }
  }

  protected function actStd() {
    $lVie = new CFie_Map_List();
    $this -> render($lVie);
  }

  protected function actEdt() {
    $lId = $this -> getReqInt('id');
    $lFrm = new CFie_Map_Form('fie-map.sedt', 'Edit Map');
    $lFrm->load($lId);
    $this -> render($lFrm);
  }

  protected function actSedt() {
    $lMod = new CFie_Map_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> update();
    $this -> redirect();
  }

  protected function actNew() {
    $lFrm = new CFie_Map_Form('fie-map.snew', 'New Map');
    $this -> render($lFrm);
  }

  protected function actSnew() {
    $lMod = new CFie_Map_Mod();
    $lMod -> getPost($this -> mReq);
    $lMod -> setVal('mand', MID);
    $lMod -> insert();
    $this -> redirect();
  }

  protected function actDel() {
    $lId = $this -> getInt('id');
    $lMod = new CFie_Map_Mod();
    $lMod -> delete($lId);
    $this -> redirect();
  }

  protected function actSub() {
    $lMapId = $this->getInt('id');
    $lSub = new CFie_Map_Sub($lMapId);
    echo $lSub->getContent();
    exit;
  }

  protected function actDelitem() {
    $lMapId = $this->getInt('mid');
    $lId = $this->getInt('fid');

    $lSql = 'DELETE FROM al_fie_map_items WHERE id='.$lId;
    CCor_Qry::exec($lSql);

    CFie_Map_Mod::clearCache();

    $lSub = new CFie_Map_Sub($lMapId);
    echo $lSub->getContent();
    exit;
  }

  protected function actAddJobFields() {
    $lMapId = $this->getInt('mid');
    $lCore  = $this->getInt('core');
    $lDialog = new CFie_Map_Adddialog($lCore, $lMapId);
    $lDialog->render();
    exit;
  }

  protected function actSaddjobfields() {
    $lMapId = $this->getInt('mid');
    $lVal = $this->getReq('val');
    $lCore  = $this->getInt('core');

    $lSql = 'SELECT * FROM al_fie_map_master WHERE id='.$lMapId;
    $lQry = new CCor_Qry($lSql);
    $lDat = $lQry->getDat();

    $lUseNative = ('X' == $lDat['has_native']);
    $lUseWriteFilter = $lCore && ('X' == $lDat['has_write_filter']);
    if ($lUseWriteFilter) {
      $lWriteFilter = CCor_Res::extract('alias', 'write_filter', 'fiemap', 'core.xml');
    }
    if (!empty($lVal)) {
      $lJobFie = CCor_Res::getByKey('id', 'fie');
      $lNativeField = $lCore ? 'native_core' : 'native';
      $lMod = new CFie_Map_Item_Mod();
      foreach ($lVal as $lFieldId) {
        if (!isset($lJobFie[$lFieldId])) {
          continue;
        }
        $lFie = $lJobFie[$lFieldId];
        $lMod->setVal('map_id', $lMapId);
        $lMod->setVal('alias', $lFie['alias']);

        if ($lUseNative) {
          $lMod->setVal('native', $lFie[$lNativeField]);
        }
        if ($lUseWriteFilter) {
          $lNative = $lFie[$lNativeField];
          $lFilter = isset($lWriteFilter[$lNative]) ? $lWriteFilter[$lNative] : '';
          $lMod->setVal('write_filter', $lFilter);
        }
        $lMod->insert();
      }
    }
    $lSub = new CFie_Map_Sub($lMapId);
    echo $lSub->getContent();
    exit;
  }

  protected function actNewitem() {
    $lMap = $this->getInt('map');
    $lForm = new CFie_Map_Item_Form();
    $lForm->setMapId($lMap);
    echo $lForm->getForm();
    exit;
  }

  protected function actSnewitem() {
    $lMapId = $this->getInt('mid');
    $lVal = $this->getReq('val');
    $lMod = new CFie_Map_Item_Mod();
    $lMod->setVal('map_id', $lMapId);
    //var_dump($lVal); exit;
    foreach ($lVal as $lKey => $lValue) {
      $lMod->setVal($lKey, $lValue);
    }
    $lMod->insert();

    $lSub = new CFie_Map_Sub($lMapId);
    echo $lSub->getContent();
    exit;
  }

  protected function actEdititem() {
    $lId = $this->getInt('fid');
    $lForm = new CFie_Map_Item_Form();
    $lForm->load($lId);
    echo $lForm->getForm();
    exit;
  }

  protected function actSedititem() {
    $lMapId = $this->getInt('mid');
    $lFieldId = $this->getInt('fid');
    $lVal = $this->getReq('val');
    $lMod = new CFie_Map_Item_Mod();
    $lMod->forceVal('id', $lFieldId);
    //var_dump($lVal); exit;
    foreach ($lVal as $lKey => $lValue) {
      $lMod->forceVal($lKey, $lValue);
    }
    $lMod->update();

    $lSub = new CFie_Map_Sub($lMapId);
    echo $lSub->getContent();
    exit;
  }

  protected function actSend() {
    $lMapId = $this->getInt('mid');
    $lRes = CFie_Map_Mod::sendMap($lMapId);
    echo ($lRes) ? 'ok' : 'error';
  }

  protected function actExport() {
    $lMapId = $this->getInt('mid');
    $lData = CFie_Map_Mod::getMapAsArray($lMapId);
    $lJson = Zend_Json::encode($lData);
    $lJson = Zend_Json::prettyPrint($lJson, array("indent" => " "));
    $lName = $lData['name'];
    $lName = strtr($lName, array('.' => '_', '/' => '_', '\\' => '_'));
    header('Content-Type: text/json');
    header('Content-Disposition: attachment; filename="FieldMap_'.$lName.'.json"');
    echo $lJson;
  }

  protected function actImport() {
    $lJson = $this->getVal('json');
    try {
      $lData = Zend_Json::decode($lJson);
    } catch (Exception $ex) {
      echo $ex->getMessage();
    }
    CFie_Map_Mod::importMap($lData, false);
    echo 'ok';
    exit;
  }

}
