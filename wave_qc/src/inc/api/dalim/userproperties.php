<?php
class CInc_Api_Dalim_Userproperties extends CCor_Ren {

  public function __construct() {
    $this->init();
  }

  public function init() {
    $this->mVal = $this->getDefaults();
  }

  public function getDefaults() {
    $lRet = array(
      'Dialog.CanUsePenTool' => 'true',
      'Dialog.CanUseEditBoxTool' => 'false',
      'Dialog.CanUseDensitometerTool' => 'true',
      'Dialog.CanPrint' => 'true',
      'Dialog.CanUseZoomPixel' => 'true',
      'Dialog.CanChooseSeparation' => 'true',
      'Dialog.CanChooseJpegQuality' => 'true',
      'Dialog.CanDoDifferences' => 'true',
      'Dialog.CanDoInkConsumption' => 'true',
      'Dialog.CanSeeHistoric' => 'true',
      'Dialog.CanDownloadOriginalPDF' => 'true',
      'Dialog.Download' => 'true',
      'Dialog.MaxDownload' => '300',
      'Dialog.CanCreateSession' => 'true',
      'Dialog.MaxZoomLimit' => '10000',
      'Dialog.CanCreateNote' => 'true',
      'Dialog.CanCreateTextNote' => 'true',
      'Dialog.CanSeeNoteReport' => 'true',
      'Dialog.CanModifyNote.Enabled' => 'true',
      'Dialog.CanModifyNote.Value' => 'self',
      'Dialog.CanDeleteNote.Enabled' => 'true',
      'Dialog.CanDeleteNote.Value' => 'self',
      'Dialog.CanSeeNote.Enabled' => 'true',
      'Dialog.CanSeeNote.Value' => 'all',
      'user.color' => '1 1 0 0'
    );
    return $lRet;
  }


  public function get($aKey, $aDefault = NULL) {
    $lKey = (string) $aKey;
    if (isset($this->mVal[$lKey])) {
      return $this->mVal[$lKey];
    }
    return $aDefault;
  }

  public function set($aKey, $aValue) {
    $lKey = (string) $aKey;
    if (!isset($this->mVal[$lKey])) {
      throw new InvalidArgumentException('Unknown Dalim User Property '.$lKey);
    }
    $this->mVal[$lKey] = (string) $aValue;
    return $this;
  }

  public function setReaderAccess() {
    $this->set('Dialog.CanCreateNote', 'false');
    $this->set('Dialog.CanCreateTextNote', 'false');
    $this->set('Dialog.CanModifyNote.Enabled', 'false');
    $this->set('Dialog.CanCreateTextNote', 'false');
    $this->set('Dialog.CanDeleteNote.Enabled', 'false');
    $this->set('Dialog.CanCreateTextNote', 'false');
    $this->set('Dialog.CanCreateTextNote', 'false');
  }

  protected function getCont() {
    $lDoc = new DOMDocument('1.0', 'UTF-8');
    $lDoc->formatOutput = true;

    $lRoot = $lDoc -> appendChild($lDoc -> createElement('Properties'));

    foreach ($this->mVal as $lKey => $lVal) {
      $lNode = $lDoc->createElement('Property');
      $lNode->setAttribute('Key', $lKey);
      $lNode->setAttribute('Value', $lVal);
      $lRoot->appendChild($lNode);
    }
    return $lDoc->saveXML();
  }


}
