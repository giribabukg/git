<?php
class CInc_App_Sender extends CCor_Obj {

  public function __construct($aType, $aParams, $aJob, $aMsg = array()) {
    $this -> mType = $aType;
    $this -> mPar = $aParams;
    $this -> mJob = $aJob;
    $this -> mJobid = $aJob -> getId();
    $this -> mHisId = $aParams['hisId'];
    $this -> mMailType = $aParams['mailtype'];
    $this -> mMailSend = $aParams['email'];

    $this -> mTpl = new CApp_Tpl();
    $this -> mTpl -> setJobValues($aJob);
    $this -> mTpl -> addUserPat(CCor_Usr::getAuthId(), 'from');
    $this -> mTplDef = CCor_Res::get('tpl');
    $this -> mUsrDef = CCor_Res::get('usr');
    $this -> mRolDef = CCor_Res::extract('id', 'alias', 'rol');

    $lSub = (isset($aMsg['subject'])) ? $aMsg['subject'] : '';
    $this -> mTpl -> setPat('msg.subject', $lSub);
    $lBody = (isset($aMsg['body'])) ? $aMsg['body'] : '';
    $this -> mTpl -> setPat('msg.body', $lBody);
    $lAdd = (isset($aMsg['add'])) ? $aMsg['add'] : '';

    // To ensure that links are correct after move to jobtype event, we need to check $aJob -> mSrc and $aJob -> mVal[src].
    // We have the old source in mSrc and the new source in mVal[src].
    $lJobArr = $aJob -> toArray();

    $lJobSrcBymSrc = $aJob -> getSrc();
    $lJobSrcBymVal = $lJobArr['src'];

    $this -> mSrc = ($lJobSrcBymSrc != $lJobSrcBymVal) && !empty($lJobSrcBymVal) ? $lJobSrcBymVal : $lJobSrcBymSrc;

    $lBase = CCor_Cfg::get('base.url');
    $this -> mTpl -> setPat('link', $lBase.'index.php?act=job-'.$this -> mSrc.'.edt&jobid='.$aJob -> getId().'&_mid='.MID);

    $lLnk = $lBase.'index.php?act=job-'.$this -> mSrc.'-fil&jobid='.$aJob -> getId().'&_mid='.MID.'&sub=';
    $this -> mTpl -> setPat('link.files.pdf', $lLnk.'pdf');
    $this -> mTpl -> setPat('link.files.doc', $lLnk.'doc');
    $this -> mTpl -> setPat('link.files.rtp', $lLnk.'rtp');

    $lTplBody = $this -> mTplDef[$this -> mPar['tpl']]['msg'];
    $lJobFiles = new CInc_Utl_Fil_Mod($this -> mJob, $this -> mJobid);
    $lDocLink = (strpos($lTplBody,'deeplink.files.doc') !== false) ? $lJobFiles->getFolderDeepLinks('doc') : '-';
    $lPdfLink = (strpos($lTplBody,'deeplink.files.pdf') !== false) ? $lJobFiles->getFolderDeepLinks('pdf') : '-';
    $lWecLink = (strpos($lTplBody,'deeplink.files.wec') !== false) ? $lJobFiles->getFolderDeepLinks('wec') : '-';

    $this -> mTpl -> setPat('deeplink.files.doc', $lDocLink);
    $this -> mTpl -> setPat('deeplink.files.pdf', $lPdfLink);
    $this -> mTpl -> setPat('deeplink.files.wec', $lWecLink);

    if (isset($lAdd['annotations'])) {
      $this -> mTpl -> setPat('annotations', $lAdd['annotations']);
    }

    if (isset($lAdd['reason'])) {
      $this -> mTpl -> setPat('reason', $lAdd['reason']);
    }

    $pid = (isset($lAdd['apl_id'])) ? '&prtid='.$lAdd['apl_id'] : '';
    $lApl = $lBase.'index.php?act=job-apl-page&src='.$this -> mSrc.'&jid='.$aJob -> getId().$pid.'&_mid='.MID;
    $this -> mTpl -> setPat('link.apl', $lApl);

    $lApl = $lBase.'index.php?act=job-apl&src='.$this -> mSrc.'&jobid='.$aJob -> getId().'&_mid='.MID;
    $this -> mTpl -> setPat('link.job.apl', $lApl);

    $lStr = '';
    if (isset($lAdd['fil'])) {
      $lStr = lan('lib.up.file').': '.$lAdd['fil'].LF;
      $lStr.= lan('job-fil.menu').': '.$lLnk.'doc'.LF;
    }
    if (isset($lAdd['files'])) {
      if (is_array($lAdd['files'])) {
        foreach ($lAdd['files'] as $lfi) {
          $lStr.= lan('lib.up.file').': '.$lfi.LF;
          $lStr.= lan('job-fil.menu').': '.$lLnk.'doc'.LF;
        }
      }
    }
    $this -> mTpl -> setPat('link.upload', $lStr);

    $lDat = (isset($lAdd['apl_date'])) ? $lAdd['apl_date'] : '';
    $this -> mTpl -> setPat('apl.deadline', $lDat);

    $this -> mSpecialAplUsr = (isset($lAdd['special_apl_usr'])) ? $lAdd['special_apl_usr'] : array();
    $lMinPos = MAX_SEQUENCE; // Behelfsvorbelegung
    if (!empty($this -> mSpecialAplUsr)) {
      foreach($this -> mSpecialAplUsr as $lTpl => $lArr1) {
        if ('apl' != $lTpl) {
          foreach($lArr1 as $lArr2) {
            foreach($lArr2 as $lUid => $lUidPosApl) {
              $lPos = $lUidPosApl['pos'];
              if ($lMinPos > $lPos) {
                $lMinPos = $lPos;
              }
              #echo '<pre>---sender.php---';var_dump( $lUid,$lUidPosApl,'#############');echo '</pre>';
            }
          }
        }//end_if
      }
    }
    if ($lMinPos != MAX_SEQUENCE) {
      $this -> mMinAplPos = $lMinPos;
    } else {
      $this -> mMinAplPos = 0;
    }

  }
  
  public function execute() {
    #echo $this -> mType.'<pre>---sender.php---'.get_class().'---';print_r($this);echo '</pre>';
    $lTpl= '';
    if(isset($this -> mPar['tablekeyid'])){
      $lTpl.= $this -> mPar['tablekeyid'].'.'.$this -> mPar['tpl'];
    }
    switch ($this -> mType) {
      case 'usr' :
        $lUid = $this -> mPar['sid'];
        #echo '<pre>---app/sender.php--usr-';var_dump($this -> mPar['sid'],$this -> mPar,'#############');print_r($this -> mSpecialAplUsr);echo '</pre>';
        if (empty($this -> mSpecialAplUsr)) {
          $this -> sendItem($lUid);
        } elseif ( isset($this -> mSpecialAplUsr[$lTpl]) ) {
          foreach($this -> mSpecialAplUsr[$lTpl] as $lUidArr) {
            foreach($lUidArr as $lUid => $lUidPosApl) {
              #echo '<pre>---sender.php---';var_dump($lUidPosApl,'#############');echo '</pre>';
              $lPos = $lUidPosApl['pos'];
              if ($this -> mMinAplPos == $lPos) {
                $lPos = 0; // die niedrigste Zahl fängt an, diese kann aber > 0 sein
              }
              if (isset($lUidPosApl['apl_id'])) {
                $lAplId = $lUidPosApl['apl_id'];
              } else {
                $lAplId = 0;
              }
              $lDat = (isset($lUidPosApl['ddl'])) ? $lUidPosApl['ddl'] : '';
              $this -> mTpl -> setPat('usr_ddl', $lDat);
              #echo $lUid.BR;
        $this -> dbg('lUid:'.$lUid.' lTpl:'.$lTpl.' lUid:'.$lUid.' lPos:'.$lPos.' lAplId:'.$lAplId.' key:'.$this -> mPar['tablekeyid'].' UsrDdl:'.$lDat);
              $this -> sendItem($lUid, $lPos, $lAplId);
            }
          }
        }
        BREAK;
      case 'gru' :
        $lGid = $this -> mPar['sid'];
        #echo '<pre>---app/sender.php--gru-';var_dump($this -> mPar['sid'],$this -> mPar,'#############');print_r($this -> mSpecialAplUsr);echo '</pre>';
        if (empty($this -> mSpecialAplUsr)) {
          if (!empty($lGid)) {
            $lSql = 'SELECT m.uid FROM al_usr u, al_usr_mem m WHERE u.id=m.uid AND u.del="N" AND m.gid='.$lGid;
            $lQry = new CCor_Qry($lSql);
            foreach ($lQry as $lRow) {
              $this -> sendItem($lRow['uid']);
            }
          }
        } elseif ( isset($this -> mSpecialAplUsr[$lTpl]) ) {
          foreach($this -> mSpecialAplUsr[$lTpl] as $lUidArr) {
            foreach($lUidArr as $lUid => $lUidPosApl) {
              #echo '<pre>---sender.php---';var_dump($lUidPosApl,'#############');echo '</pre>';
              $lPos = $lUidPosApl['pos'];
              if ($this -> mMinAplPos == $lPos) {
                $lPos = 0; // die niedrigste Zahl fängt an, diese kann aber > 0 sein
              }
              if (isset($lUidPosApl['apl_id'])) {
                $lAplId = $lUidPosApl['apl_id'];
              } else {
                $lAplId = 0;
              }
              $lDat = (isset($lUidPosApl['ddl'])) ? $lUidPosApl['ddl'] : '';
              $this -> mTpl -> setPat('usr_ddl', $lDat);
              #echo $lUid.BR;
          $this -> dbg('lGid:'.$lGid.' lTpl:'.$lTpl.' lUid:'.$lUid.' lPos:'.$lPos.' lAplId:'.$lAplId.' key:'.$this -> mPar['tablekeyid'].' UsrDdl:'.$lDat);
              $this -> sendItem($lUid, $lPos, $lAplId, $lUidArr);
            }
          }
        }
        BREAK;
      case 'rol' :
        $lAli = $this -> mPar['sid'];
        #echo '<pre>---app/sender.php---';var_dump($this -> mPar['sid'],$this -> mPar,'#############');print_r($this -> mSpecialAplUsr);echo '</pre>';
        // TTS-481: Auch in Jobmaske festgelegte Rollen koennen aus eMail-Versand entfernt werden
        if (empty($this -> mSpecialAplUsr)) {
          $lUid = $this -> mJob[$lAli];
          $this -> sendItem($lUid);
        } elseif ( isset($this -> mSpecialAplUsr[$lTpl]) ) {
          foreach($this -> mSpecialAplUsr[$lTpl] as $lUidArr) {
            foreach($lUidArr as $lUid => $lUidPosApl) {
              #echo '<pre>---sender.php---';var_dump($lUidPosApl,'#############');echo '</pre>';
              $lPos = $lUidPosApl['pos'];
              if ($this -> mMinAplPos == $lPos) {
                $lPos = 0; // die niedrigste Zahl fängt an, diese kann aber > 0 sein
              }
              if (isset($lUidPosApl['apl_id'])) {
                $lAplId = $lUidPosApl['apl_id'];
              } else {
                $lAplId = 0;
              }
              $lDat = (isset($lUidPosApl['ddl'])) ? $lUidPosApl['ddl'] : '';
              $this -> mTpl -> setPat('usr_ddl', $lDat);
              #echo $lUid.BR;
           $this -> dbg('lAli:'.$lAli.' lTpl:'.$lTpl.' lUid:'.$lUid.' lPos:'.$lPos.' lAplId:'.$lAplId.' key:'.$this -> mPar['tablekeyid'].' UsrDdl:'.$lDat);
              $this -> sendItem($lUid, $lPos, $lAplId, $lUidArr);
            }
          }
        }
        BREAK;
      case 'apl' :
        #echo '<pre>---app/sender.php--usr-';var_dump($this -> mPar['sid'],$this -> mPar,'#############');print_r($this -> mSpecialAplUsr);echo '</pre>';
        if (empty($this -> mSpecialAplUsr)) {

          if (isset($this -> mPar['sid']) AND !empty($this -> mPar['sid'])) {
            $lApl = $this -> mPar['sid'];
            $lArr = explode(',', $lApl);
            foreach($lArr as $lUid) {
              $this -> sendItem($lUid);
            }
          }
          $lTpl = 'apl';
          if ( isset($this -> mSpecialAplUsr[$lTpl]) ) {
            foreach($this -> mSpecialAplUsr[$lTpl] as $lUid) {
              $this -> sendItem($lUid);
            }
          }
        } elseif ( isset($this -> mSpecialAplUsr[$lTpl]) ) {
          foreach($this -> mSpecialAplUsr[$lTpl] as $lUidArr) {
            foreach($lUidArr as $lUid => $lUidPosApl) {
              #echo '<pre>---sender.php---';var_dump($lUidPosApl,'#############');echo '</pre>';
              $lPos = $lUidPosApl['pos'];
              if ($this -> mMinAplPos == $lPos) {
                $lPos = 0; // die niedrigste Zahl fängt an, diese kann aber > 0 sein
              }
              if (isset($lUidPosApl['apl_id'])) {
                $lAplId = $lUidPosApl['apl_id'];
              } else {
                $lAplId = 0;
              }
              $lDat = (isset($lUidPosApl['ddl'])) ? $lUidPosApl['ddl'] : '';
              $this -> mTpl -> setPat('usr_ddl', $lDat);
              #echo $lUid.BR;
           $this -> dbg('email2APL| lTpl:'.$lTpl.' lUid:'.$lUid.' lPos:'.$lPos.' lAplId:'.$lAplId.' key:'.$this -> mPar['tablekeyid'].' UsrDdl:'.$lDat);
              $this -> sendItem($lUid, $lPos, $lAplId, $lUidArr);
            }
          }
        }
        BREAK;
      case 'gpm' :
        if ( $this -> mTpl-> getRep('{val.griesson_id}') ) {
          $this -> sendItem2eMail($this -> mPar['frm'], $this -> mPar['to']);
        }
        BREAK;
    }
    return TRUE;
  }

  public function sendItem($aUid, $aPos = 0, $aAplId = 0, $aUserList=NULL) {
    $lUid   = intval($aUid);
    $lPos   = intval($aPos);
    $lAplId = intval($aAplId);

    $lResult = CCor_Qry::getInt('SELECT p.backup FROM al_usr p, al_usr_pref q WHERE p.id='.$lUid.' AND p.id=q.uid AND p.backup>0 AND q.mand='.MID.' AND q.code="usr.onholiday" AND q.val="Y"');
    if ($lResult > 0) {
      $lUid = $lResult;
    }

    $lResult = CCor_Qry::getStr('SELECT val FROM al_usr_pref WHERE uid='.$lUid.' AND code like "rcv.email"');
    $lRcvEmail = $lResult == 'N' ? false : true;

    if (empty($lUid)) return;
    if (!isset($this -> mUsrDef[$lUid])) return;

    $lUsr = $this -> mUsrDef[$lUid];
    $lEmail_Arr = explode(';', $lUsr['email']);
    $lTid = intval($this -> mPar['tpl']);
    $lTpl = $this -> mTplDef[$lTid];
    $this -> dbg(MID.' app/sender/sendItem: Uid='.$lUid.' Pos='.$lPos.' AplId='.$lAplId.' Tid='.$lTid , mlInfo);

    if (!is_null($aUserList) || count($aUserList) > 1) {
      $lNames = $this->getInCopyNames($aUserList);
      $this -> mTpl -> setUserNamesLis($lNames);
    }
    $this -> mTpl -> setPat('usr.incopy', '-');

    $this -> mTpl -> setSubject($lTpl['subject']);

    $this -> mTpl -> setBody($lTpl['msg']);
    $this -> mTpl -> addUserPat($lUid, 'to');

    $lSub = $this -> mTpl -> getSubject();
    $lSub = preg_replace('/\{bez\.[^\}]*\}/', '', $lSub);
    $lSub = preg_replace('/\{val\.[^\}]*\}/', '', $lSub);
    $lSub = preg_replace('/\{reason\}/', '', $lSub); // reason only

    $lBod = $this -> mTpl -> getBody();
    $lBod = preg_replace('/\{bez\.[^\}]*\}/', '', $lBod);
    $lBod = preg_replace('/\{val\.[^\}]*\}/', '', $lBod);
    $lBod = preg_replace('/\{reason\}/', '', $lBod); // reason only

    $lRet = array();
    if (!empty($lEmail_Arr[0])) {
      $lSen = CCor_Usr::getInstance(); // = FROM
      $lSender = $lSen -> getVal('first_lastname');
      $lSenderEmail = $lSen -> getVal('email');
      $lSenderId = $lSen -> getVal('id');
      $lSenderEmail_Arr = explode(';',$lSenderEmail);
      if (!empty($lSenderEmail_Arr[0])) {
        $lSenderEmail = $lSenderEmail_Arr[0];
        foreach ($lEmail_Arr as $lEmail) {
          if ($lRcvEmail !== False) {
            $lUsrEmail = trim($lEmail);
            $lItm = new CApi_Mail_Item($lSenderEmail, $lSender, $lUsrEmail, $lUsr['first_lastname'], '', '', $this -> mHisId, $this -> mMailSend);
            $lItm -> setSubject($lSub);
            $lItm -> setText($lBod);
            $lItm -> setState($lPos);
            $lItm -> setAplStatesId($lAplId, $lPos);
            $lItm -> setSenderID($lSenderId);
            $lItm -> setReciverId($lUid);
            $lItm -> setJobId($this -> mJobid);
            $lItm -> setJobSrc($this -> mSrc);
            $lItm -> setMailType($this -> mMailType);
            $lRet[] = $lItm -> insert();
          } else {
            $this -> dbg($lUsr['first_lastname'].' has deactivate receiving Emails', mlInfo);
          }
        }
      } else {
        $this -> dbg('No Sender-Emailadress: ('.$lSen -> getVal('id').') '.$lSender, mlWarn);
      }
    } else {
      $this -> dbg('No Emailadress: ('.$lUid.') '.$lUsr['first_lastname'], mlWarn);
    }
    return $lRet;
  }

  protected function sendItem2eMail($aFrom, $aTo) {
    if (empty($aFrom)) return;

    $lTid = intval($this -> mPar['tpl']);
    $lTpl = $this -> mTplDef[$lTid];
    $this -> mTpl -> setSubject($lTpl['subject']);
    $this -> mTpl -> setBody($lTpl['msg']);

    $lSub = $this -> mTpl -> getSubject();
#var_dump($lSub);
    if('ST_Link_.xml' != $lSub){ // nur wenn es auch eine Griesson_Id gibt!
      $lBod = $this -> mTpl -> getBody();

      $lStart = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?'.'>';
      $lBod_link = explode($lStart,$lBod);
      $lBod_link = $lBod_link[1];
      $lBod_link = str_replace('&','&amp;',$lBod_link);
      $lBod = $lStart.$lBod_link;
      # var_dump($lBod);

      $lMime = 'application/xml';

      $lSen = CCor_Usr::getInstance();
      $lItm = new CApi_Mail_Item($aFrom, '',$aTo , '');

      $lItm -> setSubject($lSub);
      $lItm -> addAttachString($lSub, $lMime, $lBod); // setText($lBod);
     # var_dump($lItm);
      $lItm -> insert();
    }

  }

  /**
   * @param array $aUsrArray - User Ids
   * @return string
   */
  protected function getInCopyNames($aUsrArray) {
#    $lRet = LF.'Sent to:'.LF;
    $lRet = '';
    foreach ($aUsrArray as $lUid => $lVal) {
      $lList = CCor_Res::extract('id', 'fullname', 'usr', array('id' => $lUid));
      foreach ($lList as $lKey => $lVal) {
        $lRet.= '- '.$lVal.LF;
      }
    }
    return $lRet;
  }
  
  public function setMailType($aType) {
    $this -> mMailType = $aType;
  }

}