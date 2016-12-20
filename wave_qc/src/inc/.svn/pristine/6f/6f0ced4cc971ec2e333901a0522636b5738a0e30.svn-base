<?php
class CInc_Api_Dalim_Utils extends CCor_Obj {

  public function __construct() {
    $this->mDefaultVolume = CCor_Cfg::get('dalim.volume');
  }

  protected function getVolumeDoc($aDocument, $aVolume) {
    $lVolume = is_null($aVolume) ? $this->mDefaultVolume : $aVolume;
    return $lVolume.':'.$aDocument;
  }

  protected function getCommand($aCommand, $aDocument, $aVolume = NULL) {
    $lDoc = $this->getVolumeDoc($aDocument, $aVolume);
    $lQuery = new CApi_Dalim_Query();
    $lQuery->setParam('Document', $lDoc);
    $lRes = $lQuery->query('admin/'.$aCommand);
    $this->dbg($aCommand.','.$lDoc);
    return $lRes;
  }

  public function registerDocument($aDocument, $aVolume = NULL) {
    return $this->getCommand('RegisterDocument', $aDocument, $aVolume);
  }

  public function unregisterDocument($aDocument, $aVolume = NULL) {
    return $this->getCommand('UnregisterDocument', $aDocument, $aVolume);
  }

  public function getThumbnail($aDocument, $aVolume = NULL) {
    return $this->getCommand('GetThumbnail', $aDocument, $aVolume);
  }

  public function getLowRes($aDocument, $aVolume = NULL) {
    return $this->getCommand('GetLowRes', $aDocument, $aVolume);
  }

  public function getDocumentInfos($aDocument, $aVolume = NULL) {
    return $this->getCommand('GetDocumentInfos', $aDocument, $aVolume);
  }

  public function getDocumentNotes($aDocument, $aVolume = NULL) {
    return $this->getCommand('GetDocumentNotes', $aDocument, $aVolume);
  }

  public function getPdfReport($aDocument, $aVolume = NULL, $aFilename = NULL) {
    $lDoc = $this->getVolumeDoc($aDocument, $aVolume);
    if (is_null($aFilename)) {
      $aFilename = 'xyz.pdf';
    }
    $lDoc = $this->getVolumeDoc($aDocument, $aVolume);
    $lQuery = new CApi_Dalim_Query();
    $lQuery->setParam('doc', $lDoc);
    $lQuery->setParam('action', 'getPDFNotesReport');
    $lRes = $lQuery->query('servlet/Download/'.$aFilename);
    return $lRes;
  }
  
  public function getHires($aDocument, $aVolume = NULL, $aFilename = NULL) {
    $lDoc = $this->getVolumeDoc($aDocument, $aVolume);
    if (is_null($aFilename)) {
      $aFilename = basename($aDocument);
    }
    $lDoc = $this->getVolumeDoc ( $aDocument, $aVolume );
    $lQuery = new CApi_Dalim_Query ();
    $lQuery->setParam('doc', $lDoc);
    $lQuery->setParam('Resolution', 72);
    $lQuery->setParam('Original', 'true');
    $lQuery->setParam('withNotes', 'true');
    $lRes = $lQuery->query ('servlet/HighResolution/' . $aFilename);
    return $lRes;
  }

  public function addVolume($aAlias, $aVirtualPath) {
    $lQuery = new CApi_Dalim_Query();
    $lQuery->setParam('Volume', $aAlias.':'.$aVirtualPath);
    $lRes = $lQuery->query('admin/AddVolume');
    return $lRes;
  }

  public function removeVolume($aAlias, $aVirtualPath) {
    $lQuery = new CApi_Dalim_Query();
    $lQuery->setParam('Volume', $aAlias.':'.$aVirtualPath);
    $lRes = $lQuery->query('admin/RemoveVolume');
    return $lRes;
  }
  
  public static function checkFields() {
    $lSql = 'SHOW COLUMNS FROM al_dalim_notes LIKE "num"';
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry->getAssoc();
    if (!$lRow) {
      $lSql = 'ALTER TABLE `al_dalim_notes` '
          .'ADD COLUMN `num` INT UNSIGNED NOT NULL DEFAULT "0" AFTER `id`,'
          .'ADD COLUMN `parent_id` BIGINT UNSIGNED NOT NULL DEFAULT "0" AFTER `num`';
      $lQry->query($lSql);  
    }
  }

}
