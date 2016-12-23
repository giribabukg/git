<?php
/**
 * Class to simulate answers of DMS system for local testing.
 *
 * Will return a fixed response for each command.
 *
 * @package    API
 * @subpackage DMS
 * @copyright  Copyright (c) 5Flow GmbH (http://www.5flow.eu)
 * @version $Rev: 687 $
 * @date $Date: 2013-01-18 03:56:42 +0100 (Fr, 18 Jan 2013) $
 * @author $Author: gemmans $
 */
class CInc_Api_Dms_Stub extends CApi_Dms_Client {

  /**
   * @param string $aCommand command to execute (e.g. openfile)
   * @param array|null $aParams Hash array of GET parameters
   * @param string|null $aRawPost If we send a raw POST, send this as POST body
   */
  public function query($aCommand, $aParams = null, $aRawPost = null) {
    $lMatch = array();
    $lCount = preg_match('/dmsapi([a-z]+).aspx/', $aCommand, $lMatch);
    if ($lCount) {
      $lFunc = 'query'.$lMatch[1];
      if ($this->hasMethod($lFunc)) {
        $lRet = $this->$lFunc();
        $this->msg($lRet, mtApi, mlInfo);
        return $lRet;
      }
    }
    return false;
  }

  protected function queryGetFileList() {
    $lRet = '';
    $lRet.= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
    <files>
    <file>
    <version>
    <fileid>42</fileid>
    <fileversionid>193</fileversionid>
    <filename>Document.docx</filename>
    <author>Geoffrey Emmans</author>
    <date>11.10.2013 09:00:14</date>
    <version>4,00000</version>
    </version>
    <version>
    <fileid>42</fileid>
    <fileversionid>192</fileversionid>
    <filename>Document.docx</filename>
    <author>Geoffrey Emmans</author>
    <date>11.10.2013 08:58:58</date>
    <version>3,00000</version>
    </version>
    <version>
    <fileid>42</fileid>
    <fileversionid>191</fileversionid>
    <filename>Document.docx</filename>
    <author>Geoffrey Emmans</author>
    <date>10.10.2013 15:50:46</date>
    <version>2,00000</version>
    <locked_by>Emmans</locked_by>
    <locked_since>11.10.2013 08:56:09</locked_since>
    </version>
       </file>

       <file>

    <version>
    <fileid>42</fileid>
    <fileversionid>190</fileversionid>
    <filename>Document.docx</filename>
    <author>Geoffrey Emmans</author>
    <date>10.10.2013 15:47:33</date>
    <version>1,00000</version>
    </version>
    </file>

    </files>
    ';
    return $lRet;
  }

  protected function queryUploadFile() {
    $lRet = '';
    $lRet.= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
    <response>
    <errornumber>200</errornumber>
    <errormessage>OK</errormessage>
    <fileid>1</fileid>
    <fileversionid>1</fileversionid>
    <version>1</version>
    </response>';
    return $lRet;
  }

  protected function getDefaultResponse($aNum = 200, $aMessage = 'OK') {
    $lRet = '';
    $lRet.= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
    <response>
    <errornumber>'.$aNum.'</errornumber>
    <errormessage>'.$aMessage.'</errormessage>
    </response>';
    return $lRet;
  }

  protected function queryUnlockFile() {
    return $this->getDefaultResponse();
  }

  protected function queryUpdateMetaData() {
    return $this->getDefaultResponse();
  }

  protected function queryGetThumbnail() {
    return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAA9JREFUeNpi+P//P0CAAQAF/gL+Lc6J7gAAAABJRU5ErkJggg==');
  }

  protected function queryOpenFile() {
    return 'Opened a file!';
  }

}