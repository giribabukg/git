<?php
class CInc_Api_Dalim_Applet extends CCor_Ren {

  public function __construct($aSessionId) {
    $this->mBase = CCor_Cfg::get('dalim.baseurl'); # http://.../DialogOEMServer/

    $lXml = '<applet vspace="0" hspace="0" align="middle" mayscript="true" '
            .'scriptable="true" code="com.dalim.dialog.DialogView.class" '
            .'width="100%" height="100%" name="Dialogue" />';

    $this->mXml = simplexml_load_string($lXml);
    $this->mXml->addAttribute('codebase' , $this->mBase);

    $this->addParam('baseDocumentURL', $this->mBase.'dummy/dummy');
    $this->addParam('archive', './appletProvider?action=sendApplet&ts=201207142055&archive=WebViewerAppletES');
    $this->addParam('EspritDialogServer', 'true');
    $this->addParam('progressbar', 'true');
    $this->addParam('flatplan', 'true');
    $this->addParam('cache_option', 'plugin');
    $this->addParam('cache_archive', './applet/WebViewerAppletES.jar');
    $this->addParam('cache_version', '1.5.0.1');
    $this->addParam('ESPRIT_externalID', uniqid());
    $this->addParam('id', 1);
    $this->setSessionId($aSessionId);

    #$this->setUser();
  }

  public function addClose($aAct) {
    $lVolume = CCor_Cfg::get('dalim.volume');
    $this->addParam('closeHTMLPage', '/../index.php?act='.$aAct);
  }

  public function addParam($aKey, $aValue) {
    $lTag = $this->mXml->addChild('param');
    $lTag->addAttribute('name',  $aKey);
    $lTag->addAttribute('value', $aValue);
  }

  public function setSessionId($aSessionId) {
    $this->addParam('key', 'Dialog.'.$aSessionId);
  }

  public function setUser($aUser = null) {
    if (is_null($aUser)) {
      $lUsr = CCor_Usr::getInstance();
      $aUser = $lUsr->getVal('fullname');
    }
    $this->addParam('userLogin', utf8_decode($aUser));
  }

  public function addDocument($aDocument) {

  }

  protected function getCont() {
    $lDom = dom_import_simplexml($this->mXml);
    return $lDom->ownerDocument->saveXML($lDom->ownerDocument->documentElement);
  }
}

/*
<applet codebase="http://192.168.0.3:8080/DialogOEMServer/"  vspace='0' hspace='0' align='middle' mayscript='true' scriptable='true' code='com.dalim.dialog.DialogView.class' width='100%' height='100%' name='Dialogue'>
<param name='cache_option' value='plugin'/>
<param name='cache_archive' value='./applet/WebViewerAppletES.jar'/>
<param name='cache_version' value='1.5.0.1'/>
<param name='language' value='en'/>
<param name='ESPRIT_externalID' value='12345'/>
<param name='baseDocumentURL' value='http://192.168.0.3:8080/DialogOEMServer/dummy/dummy'/>
<param name='key' value='Dialog.F60499E25AA31128FCEF576ACEBDD718'/>
<param name='userLogin' value='G. Emmans'/>
<param name='doc1' value='A:/myjobs/demo_v1.pdf'/>
</applet>


<param name="archive"
value="./appletProvider?action=sendApplet&ts=201207142055&archive=WebViewerAppletES" />
<param name="EspritDialogServer" value="true" />
<param name="progressbar" value="true" />
<param name="flatplan" value="true" />
<param name="language" value="en" />

<param name="id" value="001" />
<param name="user" value="geoffrey" />
<param name="ESPRiT_externalID" value="0" />
<param name="key" value="Dialog.776B8F27271FFBD3ACAAD52D1761B668" />
*/
