<?php
/**
 * Mail Api
 *
 * @author Geoffrey Emmans <emmans@qbf.de>
 * @package api
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 12014 $
 * @date $Date: 2016-01-11 22:01:54 +0800 (Mon, 11 Jan 2016) $
 * @author $Author: ahajali $
 */

class CInc_Api_Mail_Item extends CCor_Obj {

  const MAX_SUBJECT_LENGTH = 65;

  protected $mFrom;
  protected $mSendAs; // always send as portal email?
  protected $mDiffFrom = ''; // different form
  protected $mDiffReplyTo = ''; // different reply-to
  protected $mTo;
  protected $mEol = "\r\n";
  protected $mHdr = array();
  protected $mBnd; // mime boundary
  protected $mHeader = '';
  protected $mBody = '';
  protected $mSubject = '';
  protected $mText = '';
  protected $mAttach;
  protected $mPrepared = FALSE;
  protected $mCharSet = 'UTF-8';
  protected $mAplStatesId = 0;

  /**
   * Constructor
   *
   * @param string $aFrom
   * @param string $aFromName
   * @param string $aTo
   * @param string $aToName
   * @param string $aSubject
   * @param string $aText
   * @return CApi_Mail_Item The mail object
   */
  public function __construct($aFrom, $aFromName, $aTo, $aToName, $aSubject = '', $aText = '', $aHisInsertId = 0) {
    // START #22795 "From and reply-to in email headers editable"
    $lUsr = CCor_Usr::getInstance();
    $this -> mHisId = $aHisInsertId;
    $lUid = $lUsr -> getId();
    $lSql = 'SELECT id,email_from,email_replyto FROM al_usr WHERE id='.$lUid.' AND email="'.trim($aFrom).'";';
    $lQry = new CCor_Qry($lSql);
    $lRes = $lQry -> getAssoc();
    $lId = $lRes['id'];
    if ($lId) {
      $this -> mDiffFrom = $lRes['email_from'];
      $this -> mDiffReplyTo = $lRes['email_replyto'];
    }
    // END #22795 "From and reply-to in email headers editable"

    $this -> mSendAs = CCor_Cfg::get('smtp.sendAs', FALSE);

    $this -> setFrom($aFrom, $aFromName);
    $this -> setTo($aTo, $aToName);

    $this -> setHdr('Date', date('D, j M Y H:i:s O'));

    if (!empty($aSubject)) {
      $this -> setSubject($aSubject);
    }

    if (!empty($aText)) {
      $this -> mText = $aText;
    }

    $this -> setHdr('MIME-Version', '1.0');
    $this -> setHdr('Content-Type', 'text/plain; charset=UTF-8');
    $this -> setHdr('Content-Transfer-Encoding', '8bit');

    $lMessageId = CCor_Cfg::get('smtp.MessageId');
    $lUni = md5(uniqid(time()));
    $this -> setHdr('Message-ID', '<'.$lUni.$lMessageId.'>');
    $this -> mBnd = 'b1_'.$lUni;

    $this -> mState = mlWarn;
    $this -> mErrNo  = aeQueue;
    $this -> mErrMsg = 'Not sent yet';
  }

  protected function getMimeType($aFilename) {
    $lExt = substr(strtolower(strrchr($aFilename, '.')), 1);

    switch ($lExt) {
      case 'doc':
        $lRet = 'application/msword';
        break;
      case 'pdf':
        $lRet = 'application/pdf';
        break;
      case 'xls':
        $lRet = 'application/msexcel';
        break;
      case 'zip':
        $lRet = 'application/zip';
        break;
      case 'gif':
        $lRet = 'image/gif';
        break;
      default:
        $lRet = 'application/octet-stream';
        break;
    }
    $this -> dbg($lExt.' is '.$lRet);

    return $lRet;
  }

  /**
   * Get the error number of last operation
   *
   * @access public
   * @return int Error/Status Number of last call
   */
  public function getErrNo() {
    return $this -> mErrNo;
  }

  /**
   * Set the state number of smtp object
   * mlInfo    direct sending possible
   * mlWaiting wait for sending
   *
   * @access public
   */
  public function setState($aPos) {
    if (0 < $aPos) {
      $this -> mState = mlWaiting;
      $this -> mErrMsg = lan('lib.email.waiting'); // Email will be sent later
    }
  }

  /**
   * Set the insert id of al_job_apl_states
   *
   * @access public
   */
  public function setAplStatesId($aAplId, $aPos) {
    if (0 < $aPos AND 0 < $aAplId) {
      $this -> mAplStatesId = $aAplId;
    }
  }

  public function addAttachString($aName, $aMime, $aData) {
    $this -> setHdr('Content-Type', 'multipart/mixed; boundary="'.$this -> mBnd.'"');
    $lItm = array();
    $lItm['name'] = $aName;
    $lItm['mime'] = $aMime;
    $lItm['data'] = base64_encode($aData);
    $this -> mAttach[] = $lItm;
  }

  public function addAttachFile($aFilename, $aAsName = '') {
    if (!is_readable($aFilename)) {
      $this -> msg(basename($aFilename).' is not a valid file!', mtApi, mlError);
    }
    $lNam = ('' == $aAsName) ? $aFilename : $aAsName;
    $this -> setHdr('Content-Type', 'multipart/mixed; boundary="'.$this -> mBnd.'"');
    $lItm = array();
    $lItm['name'] = basename($lNam);
    $lItm['mime'] = $this -> getMimeType($lNam);
    $lItm['data'] = base64_encode(file_get_contents($aFilename));
    $this -> mAttach[] = $lItm;
  }

  protected function encodeHeader($aValue) {
    $lCh = array("ä" => "ae", "ü" => "ue", "ö" => "oe", "Ä" => "Ae", "Ü" => "Ue", "Ö" => "Oe", "ß" => "ss");

    if (Zend_Mime::isPrintable($aValue)) {
      return $aValue;
    } else {
      $lRet = strtr($aValue, $lCh);
      return $lRet;
    }
  }

  public function setHdr($aKey, $aVal) {
    $this -> mHdr[$aKey] = $this -> encodeHeader($aVal);
  }

  public function setRawHdr($aKey, $aVal) {
    $this -> mHdr[$aKey] = $aVal;
  }

  public function setFrom($aFromMail, $aFromName = '') {
    $lCh = array("ä" => "ae", "ü" => "ue", "ö" => "oe", "Ä" => "Ae", "Ü" => "Ue", "Ö" => "Oe", "ß" => "ss");

    $lFromMail = $aFromMail;
    if (CCor_Cfg::get('smtp.from')) {
      $lFromMail = CCor_Cfg::get('smtp.from');
    }
    if (!empty($this -> mDiffFrom)) {
      $lFromMail = $this -> mDiffFrom;
    }
    $this -> mFrom = $lFromMail;

    $lReplyTo = $lFromMail;
    if (CCor_Cfg::get('smtp.reply-to')) {
      $lReplyTo = CCor_Cfg::get('smtp.reply-to');
    }
    if (!empty($this -> mDiffReplyTo)) {
      $lReplyTo = $this -> mDiffReplyTo;
    }

    $this -> mFromName = $aFromName;
    $aFromName = strtr($aFromName, $lCh);

    if (!empty($aFromName)) {
      $lFrom  = '"'.$aFromName.'" <'.$lFromMail.'>';
      $lReply = '"'.$aFromName.'" <'.$lReplyTo.'>';
    } else {
      if (!empty($lFromMail)) {
        $lFrom  = $lFromMail;
        $lReply = $lReplyTo;
      } else {
        return FALSE;
      }
    }
    if (!empty($this -> mSendAs)) {
      $lName = preg_replace('/(\"|\'|<|>)/', '', $aFromName);
      $lMatch = array();
      if (preg_match('/<\s*(.*)\s*>/', $this -> mSendAs, $lMatch)) {
        $lMail = $lMatch[1];
      } else {
        $lMail = preg_replace('/(\"|\'|<|>)/', '', $this -> mSendAs);
      }
      $lFrom = '"'.$lName.'" <'.$lMail.'>';
    }
    $this -> setHdr('From', $lFrom);
    $this -> setHdr('Reply-To', $lReply);
  }

  public function setTo($aToMail, $aToName = '') {
    $lCh = array("ä" => "ae", "ü" => "ue", "ö" => "oe", "Ä" => "Ae", "Ü" => "Ue", "Ö" => "Oe", "ß" => "ss");

    $this -> mTo = $aToMail;
    $this -> mToName = $aToName;
    $aToName = strtr($aToName, $lCh);

    if (!empty($aToName)) {
      $lTo = '"'.$aToName.'" <'.$aToMail.'>';
    } else {
      if (!empty($aToMail)) {
        $lTo = $aToMail;
      } else {
        return FALSE;
      }
    }
    $this -> setHdr('To', $lTo);
  }

  public function setSubject($aSubject) {
    $lCh = array("ä" => "ae", "ü" => "ue", "ö" => "oe", "Ä" => "Ae", "Ü" => "Ue", "Ö" => "Oe", "ß" => "ss");

    $lSub = strtr($aSubject, "\r\n\t", '   ');
    $lSub = strtr($lSub, $lCh);

    if (strlen($lSub) > self::MAX_SUBJECT_LENGTH) {
      $lSub = substr($lSub, 0, self::MAX_SUBJECT_LENGTH).'...';
    }
    $this -> mSubject = $lSub;
    $this -> setHdr('Subject', $lSub);
  }

  public function setText($aText) {
    $this -> mText = $aText;
  }

  public function getText() {
    return $this -> mText;
  }

  public function getHeader() {
    $lRet = '';
    foreach ($this -> mHdr as $lKey => $lVal) {
      $lRet.= $lKey.': '.$lVal.$this -> mEol;
    }
    $lRet.= $this -> mEol;
    return $lRet;
  }

  public function setHeader($aHeader, $aIgnore = '') {
    $aIgnore = explode(',', strtoupper($aIgnore));
    $lArr = explode($this -> mEol, $aHeader);
    $lKey = '';
    $lVal = '';
    $lRet = array();
    foreach($lArr as $lR) {
      if ($lR != '') {
        $lPos = strpos($lR, ':');
        if ($lPos > 0) {
          $lKey = substr($lR, 0 , $lPos);
          $lVal = substr($lR, $lPos+2 , strlen($lR));
          $lRet[$lKey] = $lVal;
        } else {
          $lVal = $lVal . $this -> mEol . $lR;
          $lRet[$lKey] = $lVal;
        }
      }
    }
    foreach($lRet as $lKey => $lVal) {
      if (!in_array(strtoupper($lKey), $aIgnore)) {
        $this -> setRawHdr($lKey, $lVal);
      }
    }
  }

  public function getBody() {
    if (empty($this -> mAttach)) {
      return trim($this -> mText);
    } else {
      $this -> mEol = "\r\n";
      $lRet = '';
      $lRet.= $this -> mEol.'This is a multi-part message in MIME format.'.$this -> mEol;
      $lRet.= '--'.$this -> mBnd.$this -> mEol;
      $lRet.= 'Content-Type: text/plain; charset=UTF-8; format=flowed'.$this -> mEol;
      #$lRet.= 'Content-Transfer-Encoding: 7bit'.$this -> mEol;
      $lRet.= $this -> mEol;
      $lRet.= trim($this -> mText).$this -> mEol.$this -> mEol;
      $lRet.= '--'.$this -> mBnd.$this -> mEol;
      foreach ($this -> mAttach as $lKey => $lVal) {
        $lRet.= 'Content-Type: '.$lVal['mime'].';'.$this -> mEol.' name="'.$lVal['name'].'"'.$this -> mEol;
        $lRet.= 'Content-Transfer-Encoding: base64'.$this -> mEol;
        $lRet.= 'Content-Disposition: attachment;'.$this -> mEol.' filename="'.$lVal['name'].'"'.$this -> mEol;
        $lRet.= $this -> mEol;
        $lRet.= chunk_split($lVal['data'], 76, $this -> mEol);
        #$lRet.= $this -> mEol;
        $lRet.= '--'.$this -> mBnd.$this -> mEol;
      }
      return trim($lRet).'--'.$this -> mEol;
    }
  }

  protected function prepare() {
    $this -> mHeader = $this -> getHeader();
    $this -> mBody = $this -> getBody();
    $this -> mPrepared = TRUE;
  }

  public function send() {
    $this -> prepare();
    $lTrans = CApi_Mail_Smtp::getInstance();
    $lRet = $lTrans -> sendMail($this -> mFrom, $this -> mTo, $this -> mHeader, $this -> mBody);
    if ($lRet) {
      $this -> mState = mlInfo;
      $this -> mErrNo  = aeOkay;
      $this -> mErrMsg = 'Okay';
    } else {
      $this -> mState = $lTrans -> getState();
      $this -> mErrNo  = $lTrans -> getErrNo();
      $this -> mErrMsg = $lTrans -> getErrMsg();
    }
    return $lRet;
  }

  public function insert($aSecrets = '', $aRealMID = NULL) {
    $lRet = false;
    $this -> prepare();
    if (!empty($this -> mFrom)) {
      $lFromArr = explode(';', $this -> mFrom);
      $lFromUsr = $lFromArr[0];
    } else {
      $lFromUsr = $this -> mFrom;
    }

    if (!empty($this -> mTo)) {
      $lToArr = explode(';',$this -> mTo);

      $lSql = 'INSERT INTO al_sys_mails SET ';
      if (NULL == $aRealMID) {
        $lSql.= 'mand='.MID.',';
      } else {
        $lSql.= 'mand='.$aRealMID.',';
      }
      if (0 < $this -> mAplStatesId) {
        $lSql.= 'apl_states='.$this -> mAplStatesId.',';
      }

      if (isset($this -> mSenderId) AND !empty($this -> mSenderId)) $lSql.= 'sender_id="'.addslashes($this -> mSenderId).'",';
      if (isset($this -> mReciverId) AND !empty($this -> mReciverId)) $lSql.= 'receiver_id="'.addslashes($this -> mReciverId).'",';
      if (isset($this -> mJobid) AND !empty($this -> mJobid)) $lSql.= 'jobid="'.addslashes($this -> mJobid).'",';
      if (isset($this -> mSrc) AND !empty($this -> mSrc)) $lSql.= 'src="'.addslashes($this -> mSrc).'",';
      if (isset($this -> mMailType) AND !empty($this -> mMailType)) $lSql.= 'mail_type="'.addslashes($this -> mMailType).'",';
      if (isset($this -> mMailsStatus) AND !empty($this -> mMailsStatus)) $lSql.= 'mail_status="'.addslashes($this -> mMailsStatus).'",';
      if (isset($this -> mNeedResponse) AND !empty($this -> mNeedResponse)) $lSql.= 'response="'.addslashes($this -> mNeedResponse).'",';
      
      
      $lSql.= 'from_name="'.addslashes($this -> mFromName).'",';
      $lSql.= 'from_mail="'.addslashes($lFromUsr).'",';

      $lSql.= 'to_name="'.addslashes($this -> mToName).'",';
      $lSql.= 'mail_entry=NOW(),';
      $lSql.= 'mail_date=NOW(),';
      $lSql.= 'mail_subject="'.addslashes($this -> mSubject).'",';

      $lSql.= 'mail_header="'.addslashes($this -> mHeader).'",';

      $lRes = $this -> mBody;

      if (!empty($aSecrets)) {
        $lPw = strrpos($this -> mBody, "Password: "); // steht so in der Datenbank!!!
        if ($lPw == true) {
          $lDel = substr($this -> mBody, $lPw + 10, 8);
          $lRes = str_replace($lDel, "********", $this -> mBody);
        }
      }
      $lSql.= 'mail_body="'.addslashes($lRes).'",';

      if (CCor_Cfg::get('smtp.disabled', FALSE)) {
        $ldisabled = lan('lib.email.nosending');#'NO email delivery';
        $lSql.= 'mail_state='.esc(mlNoSending).',';
        $lSql.= 'mail_errmsg='.esc($ldisabled).',';
      } else {
        $lSql.= 'mail_state="'.$this -> mState.'",';
        $lSql.= 'mail_errmsg="'.addslashes($this -> mErrMsg).'",';
      }
      $lSql.= 'mail_errno='.esc($this -> mErrNo).',';
      $lSql.= 'his_id='.esc($this -> mHisId).',';

      foreach ($lToArr as $lToUsr) {
        $lSqlUsr = $lSql;
        $lSqlUsr.= 'to_mail="'.addslashes($lToUsr).'"';
        $lQry = new CCor_Qry($lSqlUsr);
        $lRet = $lQry -> getInsertId();
        #echo '<pre>---item.php---';var_dump($lToUsr,'#############');echo '</pre>';
      }
    }

    return $lRet;
  }

  public function resend($aBody) {
    $this -> mHeader = $this -> getHeader();
    $this -> mBody = $aBody;

    $lTrans = CApi_Mail_Smtp::getInstance();
    $lRet = $lTrans -> sendMail($this -> mFrom, $this -> mTo, $this -> mHeader, $this -> mBody);
    if ($lRet) {
      $this -> mState = mlInfo;
      $this -> mErrNo  = aeOkay;
      $this -> mErrMsg = 'Okay';
    } else {
      $this -> mState = $lTrans -> getState();
      $this -> mErrNo  = $lTrans -> getErrNo();
      $this -> mErrMsg = $lTrans -> getErrMsg();
    }
    return $lRet;
  }

  public function replace($aId) {
    $lSql = 'Update al_sys_mails SET ';

    $lSql.= 'from_name="'.addslashes($this -> mFromName).'",';
    $lSql.= 'from_mail="'.addslashes($this -> mFrom).'",';

    $lSql.= 'to_name="'.addslashes($this -> mToName).'",';
    $lSql.= 'to_mail="'.addslashes($this -> mTo).'",';

    $lSql.= 'mail_date=NOW(),';
    $lSql.= 'mail_subject="'.addslashes($this -> mSubject).'",';

    $lSql.= 'mail_header="'.addslashes($this -> mHeader).'",';
    $lSql.= 'mail_body="'.addslashes($this -> mBody).'",';
    $lSql.= 'mail_state="'.$this -> mState.'",';
    $lSql.= 'mail_errmsg="'.addslashes($this -> mErrMsg).'",';
    $lSql.= 'mail_errno="'.$this -> mErrNo.'"';
    $lSql.= ' where id=' . $aId . '';
    return CCor_Qry::exec($lSql);
  }

  public function setNewMailState($aAplId) {
    $lSql = 'Update al_sys_mails SET ';
    $lSql.= 'mail_state='.esc(mlWarn).',';//kann gesendet werden
    $lSql.= 'apl_states=0';//kann wieder weg
    $lSql.= ' where apl_states='.esc($aAplId);
    #echo '<pre>---item.php---';var_dump($lSql,'#############');echo '</pre>';
    return CCor_Qry::exec($lSql);
  }

   /**
   * Cancel Email of apl user, which is deleted from APL.
   * @param Int $aAplStatesId States Id of deleted Apl user.
   * @param array $aArrStatesId States Ids from Group Members
   * @return unknown_type
   */
  public function cancelMailOfDeletedAplUser($aAplStatesId, $aArrStatesId = Array()) {
    $lSql = 'Update al_sys_mails SET ';
    $lSql.= 'mail_state='.esc(mlNoSending).',';//kann nicht gesendet werden
    $lSql.= 'apl_states=0 where 1 ';//kann wieder weg
    if (!empty($aArrStatesId)){
      // States Ids from Group members.
      $lStatesIdsStr = array_map("esc", $aArrStatesId);//jedes Element wird ".mysql_escaped."
      $lStatesIdsStr = implode(',', $lStatesIdsStr);
      $lSql.= ' AND apl_states IN ('.$lStatesIdsStr.')';
    }else {
      $lSql.= ' AND apl_states = '.esc($aAplStatesId).'';
    }
    #echo '<pre>---item.php---';var_dump($lSql,'#############');echo '</pre>';
    return CCor_Qry::exec($lSql);
  }
  
  public function setSenderID($aId) {
    $this -> mSenderId = $aId;
  }
  
  public function setReciverId($aId) {
    $this -> mReciverId = $aId;
  }
  
  public function setJobId($aJobid) {
    $this -> mJobid = $aJobid;
  }
  
  public function setJobSrc($aSrc) {
    $this -> mSrc = $aSrc;
  }
  
  public function setMailType($aType) {
    $this -> mMailType = $aType;
  }
  
  public function setMailstatus($aStatus) {
    $this -> mMailsStatus = $aStatus;
  }
  
  public function setMailNeedResponse($aNeedResponse) {
    $this -> mNeedResponse = $aNeedResponse;
  }
  
}