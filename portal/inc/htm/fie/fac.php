<?php
class CInc_Htm_Fie_Fac extends CCor_Obj {

  public function __construct($aSrc = NULL, $aJobId = 0) {
    $this -> mOld = TRUE;
    $this -> mSerial = FALSE; // serialized attributes
    $this -> mIds = array();
    $this -> mSrc = $aSrc;
    $this -> mJobId = $aJobId;
    $this -> mValPrefix = 'val';
    $this -> mValSuffix = '';
    $this -> mOldPrefix = 'old';
    $this -> mOldSuffix = '';
    $this -> mDifferentOld = array();

    $this -> mText = FALSE; // If True, Output only in text format.
  }

  public function setSerial($aFlag = TRUE) {
    $this -> mSerial = $aFlag;
  }

  public function getInput($aDef, $aVal = NULL, $aState = fsStandard, $aText = FALSE, $aIsColor = FALSE, $aValSteerBy = '', $aIsImage = FALSE) {
    // extra function in mand_1003! $aTextDisabled isn't used and not built-in in mand_1003!!!
    $this -> mDef = $aDef;
    $this -> expect('alias');
    $this -> expect('typ');
    $this -> mText = $aText; // Ausgabe als Text.
    $this -> mIsColor = $aIsColor; // Colorfield.
    $this -> mValSteerBy = $aValSteerBy; // Value of the Steering Field
    $this -> mIsImage = $aIsImage; // image
    $lRet = '';
    $lHidden = false;
    if ($this -> mOld AND !$this->mText) {
      if (!bitSet($aState, fsDisabled)) {
        #$lFeature = toArr($this -> getDef('feature')); //wird jetzt in cor/res/fie erledigt!
        // über ListChange() (s. getTypeGselect) wird ein weiteres Inputfeld gesteuert: wenn hier keine Auswahl
        // selektiert ist, muß im anderen Inputfeld die ganze Liste (abhängig v. der GId in NoChoice) angezeigt
        // werden. Damit der Wert nicht gespeichert wird, ist er auch im hidden-Feld zu setzen!
        // In getTypeGselect ist der Wert v. NoChoice automatisch bekannt, hier nur durch die Speicherung.
        if (empty($aVal) AND (NULL !== $this->getDef('NoChoice'))) {
          $aVal = $this->getDef('NoChoice');
        }
        if (!isset($this -> mDifferentOld[$this -> getDef('alias')])) {
          $lRet.= $this -> getOld($aVal);
        } else {// APL Timing: used in dialog.php
          $lRet.= $this -> getOld($this -> mDifferentOld[$this -> getDef('alias')]);
        }
      }
    }

    //Falls es getAlias... geben sollte, wird alles ab getType... NICHT mehr ausgeführt!
    $lFnc = 'getAlias'.ucfirst($this -> getDef('alias'));
    if ($this -> hasMethod($lFnc)) {
      $lRet.= $this -> $lFnc($aVal, $aState);
      if (bitSet($aState, fsDisabled)) {
        $lRet.= $this -> getOld($aVal);
        $lRet.= $this -> getDisabled($aVal);
      }
      return $lRet;
    }

    //Bis jetzt existiert nur das Hidden-Field!
    $lFnc = 'getType'.ucfirst($this -> getDef('typ'));
    if ($this -> hasMethod($lFnc)) {
      $lRet.= $this -> $lFnc($aVal, $aState);
      $lAtt = $this -> getDef('attr');
      $lDisabled = FALSE;
      if (NULL !== $lAtt) {
        $lAtt = toArr($lAtt);
        if (!empty($lAtt)) {
          if (isset($lAtt['disabled'])) {//mit oder ohne ??: AND !empty($this -> mJobId)) {
            $lDisabled = TRUE;
          }
        }
      }

      if ((bitSet($aState, fsDisabled) OR $lDisabled) AND !$this -> mIsColor AND !$this -> mIsImage) {
        $lRet.= $this -> getOld($aVal);
        $lRet.= $this -> getDisabled($aVal);
        $lHidden = true;
      }

      if(bitSet($aDef["flags"], 8192)) {
        $lCpy .= '<td><img src="img/ico/16/copy.gif" onclick="'.$this->getId().'.select();'.$this->getId().'.focus();document.execCommand(\'Copy\');" ></td>';
      }
      else {
        $lCpy = "";
      }

      $lCallerFunc = debug_backtrace();
      $last_call = $lCallerFunc[1]['function'];
      if ($last_call == 'onBeforeContent') {
        $lInfo = $this->getInfoButton();
        if (!empty($lInfo)) {
          $lInfo = '<td>'.$lInfo.'</td>';
        }
        else {
          $lInfo = "";
        }
      }
    if((!empty($lInfo) || !empty($lCpy)) && $lHidden == false){
      $lRet = '<table style="display:inline-block;"><tr><td>'.$lRet.'</td>'.$lCpy.$lInfo.'</tr></table>';
	  }

      return $lRet;
    }
    return $this -> getGeneric($aVal, $aState);
  }

  public function getId($aAlias = NULL) {
    $lAlias = (NULL == $aAlias) ? $this -> getDef('alias') : $aAlias;
    if (isset($this -> mIds[$lAlias])) {
      return $this -> mIds[$lAlias];
    }
    $lId = getNum('in');
    $this -> mIds[$lAlias] = $lId;
    return $lId;
  }

  public function getOld($aVal) {
    $lTag = new CHtm_Tag('input');
    $lTag -> setAtt('type', 'hidden');
    $lTag -> setAtt('name', $this -> getOldName());
    $lTag -> setAtt('value', $aVal);
    return $lTag -> getTag(TRUE);
  }

  public function getDisabled($aVal) {
    $lTag = new CHtm_Tag('input');
    $lTag -> setAtt('type', 'hidden');
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('value', $aVal);
    return $lTag -> getTag(TRUE);
  }

  public function getGeneric($aVal, $aState) {
    $lTag = new CHtm_Tag('input');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('type', 'text');
    $lTag -> setAtt('class', 'inp w200');
    $lTag -> setAtt('value', $aVal);
    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dis');
    }
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    return $lTag -> getTag(TRUE);
  }

  protected function getOldName() {
    if (!(is_array($this -> mDef['attr']) AND isset($this -> mDef['attr']['array_key']) AND !empty($this -> mDef['attr']['array_key']))) {
      $lRet = $this -> mOldPrefix.'['.$this -> getDef('alias').']'.$this -> mOldSuffix;
    } else {
      $lRet = $this -> mOldPrefix.'['.$this -> getDef('alias').']['.$this -> mDef['attr']['array_key'].']'.$this -> mOldSuffix; //used for msg in job/dialog
    }
    return $lRet;
  }

  protected function getName() {
    if (!(is_array($this -> mDef['attr']) AND isset($this -> mDef['attr']['array_key']) AND !empty($this -> mDef['attr']['array_key']))) {
      $lRet = $this -> mValPrefix.'['.$this -> getDef('alias').']'.$this -> mValSuffix;
    } else {
      $lRet = $this -> mValPrefix.'['.$this -> getDef('alias').']['.$this -> mDef['attr']['array_key'].']'.$this -> mValSuffix; //used for msg in job/dialog
    }
    return $lRet;
  }

  protected function getDef($aKey, $aStd = NULL) {
    return (isset($this -> mDef[$aKey])) ? $this -> mDef[$aKey] : $aStd;
  }

  protected function expect($aKey) {
    if (!isset($this -> mDef[$aKey])) {
      $this -> dbg('Field definition key '.$aKey.' not set', mlWarn);
    }
  }

  // Type specific inputs
  protected function getTypeString($aVal, $aState) {
    if ($this->mText ){ // Ausgabe als Text.
      return $aVal;
    }

    //Create Tag
    $lTag = new CHtm_Tag('input');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('type', 'text');
    $lTag -> setAtt('class', 'inp w200');
    $lTag -> setAtt('value', $aVal);
    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }

    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('readOnly', 'true');
      $lTag -> addAtt('class', 'dis');
    }
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    $lLis = $this -> getDef('learn');
    if (!empty($lLis)) {
      $lTag ->setAtt("data-change", "autocomplete");
      $lTag ->setAtt("data-autocomplete-body", "item.value + '<span class=\"informal\" style=\"position:absolute; right:1px;\" onclick=\"Flow.Std.delChoice(' + item.label + ');\"><img src=\"img/ico/9/del.gif\">&nbsp;</span>'");
      $lTag ->setAtt("data-source", "ajx.choice&dom=".$lLis);
    }
    return $lTag->getTag();
  }


  public function getTypeHidden($aVal) {
    if ($this -> mText) { // Ausgabe als Text.
      return $aVal;
    }
    $lTag = new CHtm_Tag('input');
    $lTag -> setAtt('type', 'hidden');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('value', $aVal);
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    if ($this -> mIsColor) {
      $lCnt = '<div id="cdiv1" style="border: 1px solid #CCC;"><img src="img/d.gif" width="32" height="16" alt="" /></div>';
      $lTag -> addCnt($lCnt);
      return $lTag -> getTag(FALSE);
    } else {
      return $lTag -> getTag(TRUE);
    }
  }

  protected function getTypeMemo($aVal, $aState) {
    if ($this->mText ){ // Ausgabe als Text.
      return $aVal;
    }
    if (bitset($aState, fsSearch)) {
      return $this -> getGeneric($aVal, $aState);
    }
    $lTag = new CHtm_Tag('textarea');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('cols', '20');
    $lTag -> setAtt('rows', CCor_Cfg::get('show_nr_rows', 25));
    $lTag -> setAtt('class', 'inp w200');
    if ($this -> getDef('alias') == 'jobdescription') {
      $lTag -> setAtt('class', 'inp w100p');
    }
    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    if (bitSet($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dis');
      $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    } else if (bitSet($aState, fsPrint)) {
      $lTag = new CHtm_Tag('div');
      $lTag -> setAtt('class', 'box w100p p2');
      $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
      $lTag -> setAtt('style', 'background-color:white');
      $lRet = $lTag -> getTag(FALSE);
      $lRet.= nl2br(htm($aVal));
      $lRet.= $lTag -> getEndTag();
      return $lRet;
    }

    $lRet = $lTag -> getTag(FALSE);
    $lRet.= htm($aVal);
    $lRet.= $lTag -> getEndTag();

    return $lRet;
  }

  protected function getTypeImage($aVal, $aState) {
    if ($this -> mText) { // Ausgabe als Text.
      return $aVal;
    }

    if (empty($aVal)) {
      $lSvcWecInst = CSvc_Wec::getInstance();
      $lAttributes = $lSvcWecInst -> getAttributes($this -> mJobId);

      if (file_exists($lAttributes['thumbnail_directoryname'].$lAttributes['thumbnail_filename'])) {
        $aVal = $lAttributes['thumbnail_directoryname'].$lAttributes['thumbnail_filename'];
      } else {
        $aVal = $lAttributes['thumbnail_notfound'];
      }

      if (file_exists($lAttributes['image_directoryname'].$lAttributes['image_filename'])) {
        $aValLarge = $lAttributes['image_directoryname'].$lAttributes['image_filename'];
        $lImg = '<img src="'.$lAttributes['image_directoryname'].$lAttributes['image_filename'].'" border="0px" width="300px" height="300px" alt="" />';
      } else {
        $aValLarge = $lAttributes['image_notfound'];
        $lImg = '<img src="'.$lAttributes['image_notfound'].'" border="0px" width="300px" height="300px" alt="" />';
      }
    }

    $lTag = new CHtm_Tag('img');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());

    $lSize = toArr($this -> getDef('feature'));
    if (isset($lSize['size']) AND ($lSize['size'] == strtolower('large'))) {
      $lTag -> setAtt('class', 'inp w300');
      $lTag -> setAtt('src', $aValLarge);
    } else {
      $lTag -> setAtt('src', $aVal);
      $lTag -> setAtt('class', 'inp w100');
      $lTag -> setAtt('onmouseover', 'return overlib(\''.($lImg).'\');');
      $lTag -> setAtt('onmouseout', 'return nd();');
    }
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));

    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }

    $lRet = $lTag -> getTag(FALSE);
    $lRet.= $lTag -> getEndTag();

    return $lRet;
  }

  protected function getTypeRich($aVal, $aState) {
    if ($this->mText ){ // Ausgabe als Text.
      return $aVal;
    }
    if (bitset($aState, fsSearch)) {
      return $this -> getGeneric($aVal, $aState);
    }
    $lTag = new CHtm_Tag('textarea');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('cols', '20');
    $lTag -> setAtt('rows', '25');
    $lTag -> setAtt('class', 'inp w200');
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    $lTag -> setAtt('style', 'min-height:400px;');
    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    $lRet = '';
    #if (bitset($aState, fsDisabled)) {
    #$lTag -> setAtt('disabled', 'disabled');
    #$lTag -> addAtt('class', 'dis');
    #} else {
    $lPag = CHtm_Page::getInstance();
    #}
    $lRet.= $lTag -> getTag(FALSE);
    $lRet.= htm(stripslashes($aVal));
    $lRet.= $lTag -> getEndTag();
    $lRet.= '<script type="text/javascript">Flow.Std.tMce("'.$this -> getId().'");</script>'.LF;

    return $lRet;
  }

  protected function getTypeBoolean($aVal, $aState) {
    if ($this->mText ){ // Ausgabe als Text bzw. als image.
      if ($aVal) {
        return img('img/ico/16/check-hi.gif');
      }
    }

    // todo : fsDisabled als grafik anzeigen
    $lTag = new CHtm_Tag('input');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('type', 'checkbox');
    if ($aVal) {
      $lTag -> setAtt('checked', 'checked');
    }
    #$lTag -> setAtt('value', $aVal);
    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dis');
    }
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    return $lTag -> getTag(TRUE);
  }

  protected function getTypeUselect($aVal, $aState) {
    $lSteeredArr = array();
    if(!empty($this -> mValSteerBy)) {
      //habe den Wert == die Vorauswahl der Steuergruppe, z.B. Supplier
      $lPar = array('gru' => $this -> mValSteerBy);
      $lSteeredArr = CCor_Res::extract('id', 'fullname', 'usr',  $lPar);
    }
    #$lFeature = toArr($this -> getDef('feature'));  //wird jetzt in cor/res/fie erledigt!
    $lPar = toArr($this -> getDef('param'));
    $lArr = CCor_Res::extract('id', 'fullname', 'usr',  $lPar);

    if ($this -> mText) { // Ausgabe als Text.
      if ($aVal) {
        return $lArr[$aVal];
      } else {
        return '';
      }
    }
    asort($lArr);
    $lTag = new CHtm_Tag('select');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('size', 1);
    $lTag -> setAtt('class', 'w200 inp');
    if(CCor_Cfg::get('show.user.details', false)) {
   		$lTag -> setAtt('onmouseover', 'Flow.usrDetTip(this, this.value)');
    	$lTag -> setAtt('onmouseout', 'Flow.hideTip();');
  	}

    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dissel');
    }

    $lGroupToShow = array();
    if (isset($this -> mDef['SteeredBy']) AND !empty($this -> mDef['SteeredBy'])) {

      if (!empty($lPar['gru'])) {
        $lParentGru = $lPar['gru'];
        $lStartGru = $lParentGru;
        if(NULL !== $this->getDef('WithNoSelect')) {
          $lArr = array();
        }

        $lGroupToShow[$lParentGru] = $lArr; //All Members of Parent
        $lGru = getGroups($lParentGru);
        if (!empty($lGru[$lParentGru])) {
          $lGroups = array_keys($lGru[$lParentGru]); // die Untergruppen von $lParentGru
          foreach ($lGroups as $lgid) {
            $lGroupToShow[$lgid] = CCor_Res::extract('id', 'fullname', 'usr',  array('gru' => $lgid));
          }
        }
      }
    }
    $lHtmlId = $this -> getId();
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    $lRet = $lTag -> getTag();

    if(empty($aVal) AND (NULL !== $this->getDef('PreSelect'))) {
      $aVal = $this->getDef('PreSelect');
    }
    $lRet.= '<option value="">&nbsp;</option>'.LF;
    if(!empty($lSteeredArr)) {
      $lArr = $lSteeredArr;
    }
    if (!empty($lArr)) {
      foreach ($lArr as $lKey => $lVal) {
        $lRet.= '<option value="'.$lKey.'"';
        $lRet.= ($lKey == $aVal) ? ' selected="selected"' : '';
        $lRet.= '>'.htm($lVal).'</option>'.LF;
      }
    }

    $lRet.= $lTag -> getEndTag();
    if (!empty($lGroupToShow)){
      $lRet.= '<script type="text/javascript">'.LF.'<!--'.LF;
      $lRet.= 'GruMem["'.$lHtmlId.'"] = new Array();'.LF;
      foreach ($lGroupToShow as $lgid => $larr) {
        $lRet.= 'GruMem["'.$lHtmlId.'"]['.$lgid.'] = new Array();'.LF;
        foreach ($larr as $lG => $lA) {
          $lRet.= 'GruMem["'.$lHtmlId.'"]['.$lgid.']['.$lG.'] = "'.$lA.'";'.LF;
        }
      }
      $lRet.= '//-->'.LF.'</script>'.LF;
    }

    $lParent = $this->getDef('GroupParent');
    if (!empty($lParent)) {
      $lGru = (isset($lPar['gru'])) ? $lPar['gru'] : 0;
      $lParentId = $this->getId($lParent);
      $lJs = 'jQuery(function(){'.LF;
      $lJs.= 'jQuery("#'.$lParentId.'").exists(function() {'.LF;
      $lJs.= 'Flow.uteams.addChild("'.$lParentId.'","'.$this->getId().'",'.$lGru.')'.LF;
      $lJs.= '});'.LF;
      $lJs.= '});'.LF.LF;

      $lPage = CHtm_Page::getInstance();
      $lPage->addJs($lJs);
    }
    return $lRet;
  }

  protected function getTypeGselect($aVal, $aState) {
    $lPar = toArr($this -> getDef('param'));
    $lPar['del'] = 'N';
    $lArr = CCor_Res::extract('id', 'name', 'gru',  $lPar);
    if ($this -> mText) { // Ausgabe als Text.
      if (isset($lArr[$aVal])) {
        return $lArr[$aVal];
      } else {
        return '';
      }
    }

    $lTag = new CHtm_Tag('select');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('size', 1);
    $lTag -> setAtt('class', 'w200 inp');
    if(CCor_Cfg::get('show.group.details', false)) {
    	$lTag -> setAtt('onmouseover', 'Flow.grpMemTip(this, this.value)');
    	$lTag -> setAtt('onmouseout', 'Flow.hideTip();');
    }

    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }

    $lSrc = $this -> getId();
    $lAli = $this -> getDef('alias');
    /*
     $lPer = 'per'.substr($lAli, 3);
    $lDst = $this -> getId($lPer);
    $lJs = 'grpChange(\''.$lSrc.'\',\''.$lDst.'\')';
    $lTag -> setAtt('onchange', $lJs);
    */
    #$lFeature = toArr($this -> getDef('feature')); //wird jetzt in cor/res/fie erledigt!
    // SteerAlias und NoChoice gehören normalerweise zusammen.
    // Dieses Feld gibt an, welches Alias = weiteres Inputfeld gesteuert werden soll
    if (NULL !== $this -> getDef('SteerAlias')) {
      $lSteerAlias = explode(",",$this -> getDef('SteerAlias'));
    } else $lSteerAlias = '';
    // über ListChange() wird ein weiteres Inputfeld gesteuert: wenn hier keine Auswahl selektiert ist,
    // muß im anderen Inputfeld die ganze Liste (abhängig v. der GId in NoChoice) angezeigt werden.
    // Damit der Wert nicht gespeichert wird, ist er auch im hidden-Feld (s.o.) zu setzen!
    // Hier ist der Wert v. NoChoice automatisch bekannt, beim Hidden-Field nur durch die Speicherung.
    // NoChoice wird in fie/mod automatisch hinzugefügt.
    if (NULL !== $this -> getDef('NoChoice')) {
      $lNoChoice = $this -> getDef('NoChoice');
    } else $lNoChoice = '';
    # if(empty($aVal) AND isset($lFeature['PreSelect'])) { // zuerst in Uselect eingebaut
    #   $aVal = $lFeature['PreSelect'];
    # }

    $lPageJs = 'jQuery(function(){'.LF;
    if (!empty($lSteerAlias)) {
      $lJs = '';
      foreach ($lSteerAlias as $lVal){
        $lDst = $this -> getId($lVal);
        $lDstDef = CCor_Res::getByKey('alias', 'fie', array('alias' => $lVal));
        $lTyp = $lDstDef[$lVal]['typ'];

        if($lTyp == 'uselect'){
          $lJs.= 'ListChange(\''.$lSrc.'\',\''.$lDst.'\');';
        } else if($lTyp == 'gselect'){
          $lJs.= 'grpChange(\''.$lSrc.'\',\''.$lDst.'\');';
          $lPageJs.= 'grpChange(\''.$lSrc.'\',\''.$lDst.'\');';
        }
      }
      $lTag -> setAtt('onchange', $lJs);
    }
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dissel');
    }
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));

    $lRet = $lTag -> getTag();
    $lRet.= '<option value="'.$lNoChoice.'">&nbsp;</option>'.LF;

    // check if a deactivated group is selected
    if (!empty($aVal) && !isset($lArr[$aVal])) {
      $lPar = toArr($this -> getDef('param'));
      $lPar['id'] = $aVal;
      $lArrDel = CCor_Res::extract('id', 'name', 'gru',  $lPar);
      if (!empty($lArrDel[$aVal])) {
        $lRet.= '<option value="'.intval($aVal).'" selected="selected">';
        $lRet.= htm($lArrDel[$aVal]).'</option>'.LF;
      }
    }
    if (!empty($lArr))
    foreach ($lArr as $lKey => $lVal) {
      $lRet.= '<option value="'.$lKey.'"';
      $lRet.= ($lKey == $aVal) ? ' selected="selected"' : '';
      $lRet.= '>'.htm($lVal).'</option>'.LF;
    }
    $lRet.= $lTag -> getEndTag();

    $lPageJs.= '});'.LF.LF;
    $lPage = CHtm_Page::getInstance();
    $lPage->addJs($lPageJs);

    return $lRet;
  }

  protected function getTypeValselect($aVal, $aState) {
    if ($this->mText){
      // Ausgabe als Text.
      return $aVal;
    }

    $lTag = new CHtm_Tag('select');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('size', 1);
    $lTag -> setAtt('class', 'w200 inp');

    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dissel');
    }
    $lPar = $this -> getDef('param');
    $lPar = toArr($lPar);
    $lArr = $lPar['lis'];

    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));

    $lRet = $lTag -> getTag();
    $lRet.= '<option value="">&nbsp;</option>'.LF;
    if (!empty($lArr))
      foreach ($lArr as $lVal) {
      $lRet.= '<option';
      $lRet.= ((string)$lVal == (string)$aVal) ? ' selected="selected"' : '';
      $lRet.= '>'.htm($lVal).'</option>'.LF;
    }
    $lRet.= $lTag -> getEndTag();
    return $lRet;
  }

  protected function getTypeSelect($aVal, $aState) {
    if ($this->mText){ // Ausgabe als Text.
      return $aVal;
    }
    $lTag = new CHtm_Tag('select');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('size', 1);
    $lTag -> setAtt('class', 'w200 inp');

    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dissel');
    }
    $lArr = toArr($this -> getDef('param'));

    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    $lRet = $lTag -> getTag();
    #$lRet.= '<option value="">&nbsp;</option>'.LF;
    if (!empty($lArr))
      foreach ($lArr as $lKey => $lVal) {
      $lRet.= '<option value="'.htm($lKey).'"';
      $lRet.= ((string)$lKey == (string)$aVal) ? ' selected="selected"' : '';
      $lTxt = ('' == $lVal) ? NB : htm($lVal);
      $lRet.= '>'.$lTxt.'</option>'.LF;
    }
    $lRet.= $lTag -> getEndTag();
    return $lRet;
  }

  protected function getTypeMultipleSelect($aVal, $aState) {
    if ($this->mText){ // Ausgabe als Text.
      return $aVal;
    }
    $lValArr = explode(',', $aVal);
    $lSelectArr = toArr($this -> getDef('param'));
    $lAmount = count($lSelectArr);
    if (0 < $lAmount AND $lAmount < 6) {
      $lSize = $lAmount;
    } else {
      $lSize = 5;
    }

    $lTag = new CHtm_Tag('select');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName().'[]');
    $lTag -> setAtt('size', $lSize);
    $lTag -> setAtt('class', 'w200 inp');
    $lTag -> setAtt('multiple', 'multiple');

    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dissel');
    }
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    $lRet = $lTag -> getTag();

    if (!empty($lSelectArr))
      foreach ($lSelectArr as $lKey => $lVal) {
      $lRet.= '<option value="'.htm($lKey).'"';
      if (is_string($lKey) || (0 < $lKey)) {
        $lRet.= ((!empty($lValArr) AND in_array($lKey, $lValArr)) ? ' selected="selected"' : '');
      }
      $lTxt = ('' == $lVal) ? NB : htm($lVal);
      $lRet.= '>'.$lTxt.'</option>'.LF;
    }
    $lRet.= $lTag -> getEndTag();
    return $lRet;
  }

  protected function getTypeResselect($aVal, $aState) {
    if ($this->mText){ // Ausgabe als Text. @TODO Buggy, where do these local vars come from
      $lArr = CCor_Res::extract($lKey, $lVal, $lRes, $lFil);
      return $lArr[$aVal];
    }

    $lTag = new CHtm_Tag('select');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('size', 1);
    $lTag -> setAtt('class', 'w200 inp');

    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dissel');
    }

    $lPar = $this -> getDef('param');
    $lPar = toArr($lPar);

    $lRes = (isset($lPar['res'])) ? $lPar['res'] : 'usr';
    $lKey = (isset($lPar['key'])) ? $lPar['key'] : 'id';
    $lVal = (isset($lPar['val'])) ? $lPar['val'] : 'name_en';
    $lFil = (isset($lPar['fil'])) ? $lPar['fil'] : '';

    $lArr = CCor_Res::extract($lKey, $lVal, $lRes, $lFil);

    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    $lRet = $lTag -> getTag();

    $lRet.= '<option value="">&nbsp;</option>'.LF;
    if (!empty($aVal) && !isset($lArr[$aVal])) {
      $lRet.= '<option value="'.htm($aVal).'" selected="selected">';
      $lRet.= htm($aVal).'</option>'.LF;
    }

    if (!empty($lArr))
      foreach ($lArr as $lKey => $lVal) {
      $lRet.= '<option value="'.htm($lKey).'"';
      $lRet.= ((string)$lKey == (string)$aVal) ? ' selected="selected"' : '';
      $lShow = ('' != $lVal ? $lVal : $lKey);
      $lRet.= '>'.htm($lShow).'</option>'.LF;
    }
    $lRet.= $lTag -> getEndTag();
    return $lRet;
  }

  protected function getTypePickselect($aVal, $aState) {
    if ($this->mText){ // Ausgabe als Text.
      return $aVal;
    }
    $lCol = '';
    $lPar = $this -> getDef('param');
    $lPar = toArr($lPar);
    if (isset($lPar['dom'])){
      $lDom = $lPar['dom'];
      $lParam = Array('domain'=> $lPar['dom']);
    }
    $lAlias = (isset($lPar['alias'])) ? $lPar['alias'] : '' ;
    $lSteerAlias = (isset($lPar['steerAlias'])) ? $lPar['steerAlias'] : '';

    // If either Parameter 'dom' or 'alias' empty, return standart select feld
    if ($lAlias == '' OR $lDom == '' ){
      $lRet = $this ->getTypeSelect($aVal,$aState);
      return $lRet;
    }

    // Find out the ColId of Alias in the Picklist
    $lSqlColFind = 'SELECT DISTINCT(col) from al_pck_columns where domain ="'.$lDom.'" AND alias ="'.$lAlias.'"';
    $lColId = CCor_Qry::getInt($lSqlColFind);
    if (empty($lColId)){
      $lRet = $this ->getTypeSelect($aVal,$aState);
      return $lRet;
    }
    $lCol = 'col'.$lColId;

    // Find out the ColoumnId of Print machinery name
    $lSqlColFind = 'SELECT DISTINCT(col) from al_pck_columns where domain ="'.$lDom.'" AND alias ="name"';
    $lColId = CCor_Qry::getInt($lSqlColFind);
    if (empty($lColId)){
      $lRet = $this ->getTypeSelect($aVal,$aState);
      return $lRet;
    }
    $lColIdPrintMachineName = 'col'.$lColId;

    if (isset($this -> mDef['SteeredBy']) AND !empty($this -> mDef['SteeredBy']) AND $lSteerAlias != '' ){
      $lFie = CCor_Res::extract('alias','feature','fie');
      $lSteeredByFeature = $lFie[$this -> mDef['SteeredBy']];
      $lSteeredByFeature = toArr($lSteeredByFeature);
      if (isset($lSteeredByFeature['NoChoice'])){
        $lSteeredByGroup = $lSteeredByFeature['NoChoice'];
      }

      $lSteeredArr = array();
      $lGroupToShow = array();

      // Find SteerAlias Coloumn
      $lSteerAliasCol = '';


      $lSqlColFind = 'SELECT DISTINCT(col) from al_pck_columns where domain ="'.$lDom.'" AND alias ="'.$lSteerAlias.'"';
      $lColId = CCor_Qry::getInt($lSqlColFind);
      if (empty($lColId)){
        $lRet = $this ->getTypeSelect($aVal,$aState);
        return $lRet;
      }
      $lSteerAliasCol = 'col'.$lColId;
      //habe den Wert == die Vorauswahl der Steuergruppe, z.B. Supplier

      /**TODO
       * Es soll auch mit Userselect Funktionieren.
      * Es kann aus SteeredBy gelesen werden on es sich um Userselect oder GruppenSelect Handelt.
      */
      $lGru = CCor_Res::get('gru');
      $lGruId = (isset($lGru[$this -> mValSteerBy]) AND $lGru[$this -> mValSteerBy]['kundenId'] != '') ? $lGru[$this -> mValSteerBy]['kundenId'] : $this -> mValSteerBy;
      $lGruArr = CCor_Res::extract('kundenId','id','gru');


      $lArr = CCor_Res::get('pcklist', $lParam);

      foreach ($lArr as $lRow){
        if (isset($lRow[$lSteerAliasCol]) AND !empty($lRow[$lSteerAliasCol])){
          if (isset($lGruArr[$lRow[$lSteerAliasCol]]) AND !empty($lGruArr[$lRow[$lSteerAliasCol]])){
            $lGroupToShow[$lGruArr[$lRow[$lSteerAliasCol]]][$lRow[$lCol]] = $lRow[$lColIdPrintMachineName];
            if ($lRow[$lSteerAliasCol] == $lGruId ){
              $lSteeredArr[$lRow[$lCol]]= $lRow[$lColIdPrintMachineName];
            }
          }
        }
      }
    }

    $lHtmlId = $this -> getId();

    $lTag = new CHtm_Tag('select');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('size', 1);
    $lTag -> setAtt('class', 'w200 inp');

    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }

    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dissel');
    }

    $lAllPrintMachinery = CCor_Res::extract($lCol,$lColIdPrintMachineName,'pcklist', $lParam);
    $lArr = $lAllPrintMachinery;

    $lEmp = (isset($lPar['emp'])) ? $lPar['emp'] : '';
    if (bitset($aState, fsSearch)) $lEmp = FALSE;

    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    $lRet = $lTag -> getTag();

    if (!$lEmp) {
      $lRet.= '<option value="">&nbsp;</option>'.LF;
    }

    if(!empty($lSteeredArr)) {
      $lArr = $lSteeredArr;
    }

    if ($this -> mValSteerBy == '' OR (isset($lSteeredByGroup) AND $this -> mValSteerBy == $lSteeredByGroup)){
      if(NULL !== $this->getDef('WithNoSelect')) {
        $lArr = array();
      }
    }

    if (!empty($lArr)){
      foreach ($lArr as $lKey => $lVal) {
        $lRet.= '<option value="'.htm($lKey).'"';
        $lRet.= ($lKey == $aVal) ? ' selected="selected"' : '';
        $lRet.= '>'.htm($lVal).'</option>'.LF;
      }
    }


    if( NULL == $this->getDef('WithNoSelect') AND isset($lSteeredByGroup)) {
      $lGroupToShow[$lSteeredByGroup]= $lAllPrintMachinery;
    }

    if (!empty($lGroupToShow)){
      $lRet.= '<script type="text/javascript">'.LF.'<!--'.LF;
      $lRet.= 'GruMem["'.$lHtmlId.'"] = new Array();'.LF;
      foreach ($lGroupToShow as $lgid => $larr) {
        $lRet.= 'GruMem["'.$lHtmlId.'"]['.$lgid.'] = new Array();'.LF;
        foreach ($larr as $lG => $lA) {
          $lRet.= 'GruMem["'.$lHtmlId.'"]['.$lgid.']["'.$lG.'"] = "'.$lA.'";'.LF;
        }
      }

      $lRet.= '//-->'.LF.'</script>'.LF;
    }



    $lRet.= $lTag -> getEndTag();

    return $lRet;
  }

  protected function getTypeTselect($aVal, $aState) { //Helptable
    if ($this->mText){ // Ausgabe als Text.
      return $aVal;
    }
    $lTag = new CHtm_Tag('select');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('size', 1);
    $lTag -> setAtt('class', 'w200 inp');

    # if ($this -> getDef('alias') == 'druck_beidseitig') {
    #   $lTag -> setAtt('onchange', 'Flow.Std.togColFrm(this.value)');
    # }
    if ($this -> getDef('alias') == 'region') {
      $lTag -> setAtt('onchange', 'Flow.onRegionSelect(this.value,"'.$this->getId('country').'","'.$this->getId('language').'")');
    }

    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dissel');
    }

    $lPar = $this -> getDef('param');
    $lPar = toArr($lPar);

    $lDom = (isset($lPar['dom'])) ? $lPar['dom'] : '';
    $lArr = CCor_Res::get('htb', $lDom);

    $lEmp = (isset($lPar['emp'])) ? $lPar['emp'] : '';
    if (bitset($aState, fsSearch)) $lEmp = FALSE;

    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));

    $lRet = $lTag -> getTag();
    if (!$lEmp) {
      $lRet.= '<option value="">&nbsp;</option>'.LF;
    }
    if (!isset($lArr[$aVal]) && !empty($aVal)) {
      $lRet.= '<option value="'.htm($aVal).'" selected="selected">';
      $lRet.= htm($aVal).'</option>'.LF;
    }
    if (!empty($lArr))
      foreach ($lArr as $lKey => $lVal) {
      $lRet.= '<option value="'.htm($lKey).'"';
      $lRet.= ((string)$lKey === (string)$aVal) ? ' selected="selected"' : '';
      $lRet.= '>'.htm($lVal).'</option>'.LF;
    }
    $lRet.= $lTag -> getEndTag();

    return $lRet;
  }

  protected function getTypeCselect($aVal, $aState) {
    $lTag = new CHtm_Tag('select');
    if (bitset($aState, fsDisabled)) {
      $lTag = new CHtm_Tag('input');
      $lTag -> setAtt('value', $aVal);
      $lTag -> setAtt('disabled', 'disabled');
    }
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('size', 1);
    $lTag -> setAtt('class', 'w200 inp');

    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('class', 'inp w200 dis');
      return $lTag->getContent();
    }

    $lPar = $this -> getDef('param');
    $lPar = toArr($lPar);

    $lDom = (isset($lPar['dom'])) ? $lPar['dom'] : '';


    $lEmp = (isset($lPar['emp'])) ? $lPar['emp'] : '';
    if (bitset($aState, fsSearch)) $lEmp = FALSE;

    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    $lRet = $lTag -> getTag();
    if (!$lEmp) {
      $lRet.= '<option value="">&nbsp;</option>'.LF;
    }
    $lRet.= '<option value="'.htm($aVal).'" selected="selected">';
    $lRet.= htm($aVal).'</option>'.LF;

    $lRet.= $lTag -> getEndTag();

    $lNumCols = 5;
    $lObserved = array();
    $lPar = $this -> getDef('param');
    if (NULL !== $lPar) {
      $lPar = toArr($lPar);
      if (!empty($lPar)) {
        for ($i=1; $i<= $lNumCols; $i++) {
          if (!empty($lPar['col_'.$i]) and !empty($lPar['fie_'.$i])) {
            $lFie = $this->getId($lPar['fie_'.$i]);
            $lObserved[$lPar['col_'.$i]] = $lFie;
          }
        }
      }
    }
    //cascade : function(aDest, aColumn, aPickList, aParents)
    $lJson = Zend_Json::encode($lObserved);
    $lId = $this->getId();
    $lJs = 'jQuery(function(){'.LF; //dom:loaded is executed before jQuery ready event
    $lJs.= 'Flow.cselect("'.$lId.'","'.$lPar['alias'].'","'.$lPar['dom'].'",\''.$lJson.'\',\''.htm($aVal).'\');'.LF;
    $lJsLine = 'Flow.cselect("'.$lId.'","'.$lPar['alias'].'","'.$lPar['dom'].'",\''.$lJson.'\',jQuery(\'#'.$lId.'\').val());'.LF;
    if (!empty($lObserved)) {
      foreach ($lObserved as $lKey => $lVal) {
        $lJs.= 'jQuery("#'.$lVal.'").change(function(){';
        $lJs.= $lJsLine;
        $lJs.= '});';
      }
    }
    $lJs.= '});'.LF.LF;

    $lPage = CHtm_Page::getInstance();
    $lPage->addJs($lJs);

    return $lRet;
  }

  protected function getTypeCcomplete($aVal, $aState) {
    if ($this->mText ){
      // Ausgabe als Text.
      return $aVal;
    }
    $lTag = new CHtm_Tag('input');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('type', 'text');
    $lTag -> setAtt('class', 'inp w200');
    $lTag -> setAtt('value', $aVal);
    $lTag -> setAtt('data-change', 'autocomplete');
    $lTag -> setAtt('data-source', 'ajx.ccomplete');
    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dis');
    }
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));

    $lPar = $this -> getDef('param');
    $lPar = toArr($lPar);

    $lDom = (isset($lPar['dom'])) ? $lPar['dom'] : '';
    $lNumCols = 4;
    $lObserved = array();
    $lPar = $this -> getDef('param');
    if (NULL !== $lPar) {
      $lPar = toArr($lPar);
      if (!empty($lPar)) {
        for ($i=1; $i<= $lNumCols; $i++) {
          if (!empty($lPar['col_'.$i]) and !empty($lPar['fie_'.$i])) {
            $lFie = $this->getId($lPar['fie_'.$i]);
            $lObserved[$lPar['col_'.$i]] = $lFie;
          }
        }
      }
    }
    $lParams = array(
        '_picklist' => $lDom,
        '_column' => $lPar['alias']
    );

    return $lTag -> getTag(TRUE);
  }

  function multiexplode($aDelimiters, $aString) {
      $lStrReplace = str_replace($aDelimiters, $aDelimiters[0], $aString);
      $lResult = explode($aDelimiters[0], $lStrReplace);
      return  $lResult;
  }

  protected function getTypeDate($aVal, $aState) {
    // START: TODO
    $lUsr = CCor_Usr::getInstance();
    $lMonths = $lUsr -> getPref('utl.months', 3);

    $lMonthsTag = new CHtm_Tag('input');
    $lId = $this -> getId();
    $lMonthsTag -> setAtt('id', $lId.'_months');
    $lMonthsTag -> setAtt('type', 'hidden');
    $lMonthsTag -> setAtt('value', $lMonths);
    // END:

    $lTag = new CHtm_Tag('input');
    // $lId = $this -> getId(); // see a couple of lines before this one
    $lTag -> setAtt('id', $lId);
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('type', 'text');
    $lTag -> setAtt('class', 'inp w70');
    $lVal = (NULL === $aVal) ? '' : $aVal;
    $lDat = new CCor_Date($lVal);
    $lVal = $lDat -> getFmt(lan('lib.date.long'));

    // START: TODO
    $lDateFormat = lan('lib.date.long');

    $lToken = $this -> multiexplode(array('-', '.', '/'), $lDateFormat);
    foreach ($lToken as $lKey => $lValue) {
      if (strtolower($lValue) == 'd') {
        $lToken[$lKey] = 'dd';
      } elseif (strtolower($lValue) == 'm') {
        $lToken[$lKey] = 'mm';
      } elseif (strtolower($lValue) == 'y') {
        $lToken[$lKey] = 'yy';
      }
    }

    if (strpos($lDateFormat, '-') != FALSE) {
      $lDelimiter = '-';
    } elseif (strpos($lDateFormat, '.') != FALSE) {
      $lDelimiter = '.';
    } elseif (strpos($lDateFormat, '/') != FALSE) {
      $lDelimiter = '/';
    }

    $lDateFormat = implode($lDelimiter, $lToken);

    $lFormatTag = new CHtm_Tag('input');
    $lId = $this -> getId();
    $lFormatTag -> setAtt('id', $lId.'_format');
    $lFormatTag -> setAtt('type', 'hidden');
    $lFormatTag -> setAtt('value', $lDateFormat);
    // END:

    $lCal = $lDat -> getFmt('W');
    if (!empty($lCal)) {
      $lCal = 'w'.$lCal.'&nbsp;';
    }
    if ($this -> mText) { // show text only
      return $lVal.' '.$lCal;
    }

    $lTag -> setAtt('value', $lVal);
    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    if (bitSet($aState, fsInvalid)) {
      $lTag -> addAtt('class', 'cr');
    }
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    if (bitSet($aState, fsDisabled)) {
      $lTag -> setAtt('readonly', 'readonly');
      $lTag -> addAtt('class', 'dis');

      $lRet = '<table cellpadding="0" cellspacing="0" border="0"><tr>';
      $lRet.= '<td>'.$lFormatTag -> getTag(TRUE).'</td>';
      $lRet.= '<td>'.$lMonthsTag -> getTag(TRUE).'</td>';
      $lRet.= '<td>'.$lTag -> getTag(TRUE).'</td>';
      // $lRet.= '<td>'.NB.'</td>';
      $lRet.= '<td>'.$lCal.'</td>';
      $lRet.= '</tr></table>';
      return $lRet;
    } else {
      $lTag -> addAtt('class', 'datepicker');

      $lRet = '<table cellpadding="0" cellspacing="0" border="0"><tr>';
      $lRet.= '<td>'.$lFormatTag -> getTag(TRUE).'</td>';
      $lRet.= '<td>'.$lMonthsTag -> getTag(TRUE).'</td>';
      $lRet.= '<td><label for="'.$lId.'">'.$lTag -> getTag(TRUE).'</label></td>';
      // $lRet.= '<td><a href="javascript:Flow.Std.cal(\''.$lId.'\')" class="nav2">'.img('img/ico/16/cal.gif').'</a></td>';
      $lRet.= '<td>'.$lCal.'</td>';
      $lRet.= '</tr></table>';
      return $lRet;
    }
  }

  protected function getTypePick($aVal, $aState) {
    if ($this->mText){ // Ausgabe als Text.
      return $aVal;
    }
    $lTag = new CHtm_Tag('input');
    $lId = $this -> getId();
    $lTag -> setAtt('id', $lId);
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('type', 'text');
    #$lTag -> setAtt('readonly', '');
    $lTag -> setAtt('class', 'inp w180');
    $lTag -> setAtt('value', $aVal);
    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));

    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dis');
      return $lTag -> getTag(TRUE);
    } else {
      $lPar = toArr($this -> getDef('param'));
      $lDom = (isset($lPar['dom'])) ? $lPar['dom'] : '';
      $lAdd = '';

      if ($this -> getDef('alias') == 'country') {
        if ($this->mSrc == "sec") {
          $lTag -> setAtt('class', 'inp w550');
        }
        $lTag -> setAtt('onchange', 'Flow.onCountrySelect(this.value,"'.$this->getId('language').'")');
      }
      $lLis = $this -> getDef('learn');
      $lRet = '<table cellpadding="0" cellspacing="0" border="0"><tr>';
      $lRet.= '<td>'.$lTag -> getTag(TRUE).'</td>';
      $lRet.= '<td><a href="javascript:Flow.Std.pick(\''.$lId.'\',\''.$lDom.'\',\''.$lLis.'\''.$lAdd.')" class="nav2">'.img('img/ico/16/pick.gif').'</a></td>';
      $lRet.= '</tr></table>';
      return $lRet;
    }
  }

  protected function getTypeBitset($aVal, $aState) {
    if ($this->mText){ // Ausgabe als Text.
      return $aVal;
    }
    $lId = $this -> getId();
    $lRet = '';
    $lRet.= '<input type="hidden" id="'.$lId.'" class="field_'.$this -> getDef('alias').'" name="'.$this -> getName().'" value="'.htm($aVal).'" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0" style="border:1px solid #ccc; width:200px">'.LF;

    $lPar = $this -> getDef('param');
    $lPar = toArr($lPar);

    $lDom = (isset($lPar['dom'])) ? $lPar['dom'] : '';
    $lArr = CCor_Res::get('htb', $lDom);

    foreach ($lArr as $lKey => $lVal) {
      $lKey2 = intval($lKey);
      $lRet.= '<tr>';
      $lRet.= '<td class="w16">';
      $lRet.= '<input type="checkbox"';
      if (bitset($aVal, $lKey2)) {
        $lRet.= ' checked="checked"';
      }
      $lRet.= ' onclick="Flow.Std.togBits(\''.$lId.'\','.$lKey2.')" data-key="'.$lKey2.'"/>';
      $lRet.= '</td>';
      $lRet.= '<td class="nw">';
      $lRet.= htm($lVal).NB;
      $lRet.= '</td>';
      $lRet.= '</tr>'.LF;
    }
    $lRet.= '</table>'.LF;
    return $lRet;
  }

  protected function getTypeCheckboxList($aVal, $aState) {
    if ($this -> mText) { // Ausgabe als Text.
      return $aVal;
    }

    $lId = $this -> getId();

    $lRet = '';
    $lRet.= '<input type="hidden" id="'.$lId.'" class="field_'.$this -> getDef('alias').'" name="'.$this -> getName().'" value="'.htm($aVal).'" />'.LF;
    $lRet.= '<table cellpadding="2" cellspacing="0" border="0" style="border:1px solid #ccc; width:200px">'.LF;

    $lArray = toArr($this -> getDef('param'));
    foreach ($lArray as $lKey => $lValue) {
      $lRet.= '<tr>';
      $lRet.= '<td class="w16">';
      $lRet.= '<input type="checkbox"';
      if (bitset($aVal, $lKey)) {
        $lRet.= ' checked="checked"';
      }
      $lRet.= ' onclick="Flow.Std.togCheckbox(\''.$lId.'\',\''.$lKey.'\')" data-key="'.$lKey.'"/>';
      $lRet.= '</td>';
      $lRet.= '<td class="nw">';
      $lRet.= htm($lValue).NB;
      $lRet.= '</td>';
      $lRet.= '</tr>'.LF;
    }
    $lRet.= '</table>'.LF;
    return $lRet;
  }

  protected function getTypeFile($aVal, $aState) {
    if (!CCor_Cfg::get('flink', FALSE)) {
      if ($this -> mText) { // Ausgabe als Text.
        return $aVal;
      }

      $lTag = new CHtm_Tag('input');
      $lTag -> setAtt('id', $this -> getId());
      $lTag -> setAtt('name', $this -> getName());
      $lTag -> setAtt('type', 'file');

      $lAtt = $this -> getDef('attr');
      if (NULL !== $lAtt) {
        $lAtt = toArr($lAtt);
        if (!empty($lAtt)) {
          foreach ($lAtt as $lKey => $lVal) {
            $lTag -> setAtt($lKey, $lVal);
          }
        }
      }

      if (bitset($aState, fsDisabled) || empty($this -> mJobId)) {
        $lTag -> setAtt('disabled', 'disabled');
        $lTag -> addAtt('class', 'dis');
      }

      $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));

      return $lTag -> getTag(TRUE);
    } else {
      // get alias (e.g. file_upload)
      $lFId = $this -> getDef('alias');

      // get sub (currently available: dalim, dms, doc, pdf, rtp, wec) from job field
      $lAlias = CCor_Res::extract('alias', 'param', 'fie', array('typ' => 'file'));
      $lParam = (isset($lAlias[$lFId])) ? toArr($lAlias[$lFId]) : NULL;
      $lSub = (isset($lParam['dest'])) ? $lParam['dest'] : 'doc';
      $lFiletype = (isset($lParam['filetype'])) ? $lParam['filetype'] : '';
      $lCategory = (isset($lParam['category'])) ? $lParam['category'] : '';
      $lUploadToProject = (isset($lParam['upload_to_project'])) ? $lParam['upload_to_project'] : '';

      // format file type
      $lFiletypeLst = array();
      if ($lFiletype) {
        $lFiletypeDotLst = preg_split("/(,|;)/", $lFiletype);
        foreach ($lFiletypeDotLst as $lKey => $lValue) {
          $lValue = trim($lValue, '\0\t\n\x0B\r *');
          $lValue = strtolower($lValue);
          $lFiletypeLst[] = $lValue;
          $lFiletypeDotLst[$lKey] = substr($lValue, 0, 1) == '.' ? $lValue : '.'.$lValue;
        }

        $lFiletype = implode(',', $lFiletypeDotLst);
        $lFiletype = 'accept="'.$lFiletype.'"';
      }
      $lFiletypeLst = implode(',', $lFiletypeLst);

      // get upload_max_filesize from php.ini
      $lMFS = getBytes(ini_get('upload_max_filesize'));
      $lMFS = $lMFS < 2097152 ? $lMFS : 2097152;

      // get all translations relating to flink.* from al_sys_lang as there is no filter for the language cache right now
      $lLan = array();
      $lSql = 'SELECT code,value_'.LAN.' AS value FROM al_sys_lang WHERE mand IN (0,'.MID.') AND code like "flink.%" ORDER BY code ASC;';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lLan[$lRow['code']] = $lRow['value'];
      }

      $lSrc = $this -> mSrc;
      $lJobID = $this -> mJobId;

      if ($lUploadToProject) {
        $lProjectID = NULL;
        $lProjectID = CCor_Qry::getInt('SELECT pro_id FROM al_job_sub_'.MID.' WHERE jobid_'.$this -> mSrc.' like "%'.ltrim($this -> mJobId, '0').'";');
        if (empty($lProjectID)) {
          $lProjectID = CCor_Qry::getInt('SELECT pro_id FROM al_job_sub_'.MID.' WHERE jobid_item like "%'.ltrim($this -> mJobId, '0').'";');
        }

        if (!empty($lProjectID)) {
          $lSrc = 'pro';
          $lJobID = $lProjectID;
        }
      }

      // get all mandatory information for the creation of the Flink add/upload buttons
      $lArgs = array(
        'fid' => $lFId,
        'src' => $lSrc,
        'jid' => $lJobID,
        'sub' => $lSub,
        'mfs' => $lMFS,
        'lan' => $lLan,
        'cat' => $lCategory,
        'fty' => $lFiletypeLst
      );
      $lArgsJSONEnc = json_encode($lArgs);

      $lEnabled = (empty($this -> mJobId)) ? FALSE : !bitset($aState, fsDisabled);

      $lRet = '<script type="text/javascript">';
      $lRet.= '  jQuery(function() {';
      $lRet.= '    Flow.flink.addSingle('.$lArgsJSONEnc.');';
      $lRet.= '  })';
      $lRet.= '</script>';

      $lRet.= '<table style="width: 100%;">';
      $lRet.= '  <tbody>';
      $lRet.= '    <tr>';
      $lRet.= '      <td colspan=2 style="width: 100%;">';
      $lRet.= '        <div id="'.$lFId.'_div_progress_value">';
      $lRet.= '          <div id="'.$lFId.'_div_progress_text"></div>';
      $lRet.= '        </div>';
      $lRet.= '      </td>';
      $lRet.= '    </tr>';
      $lRet.= '    <tr>';
      $lRet.= '      <td style="width: 50%;">';
      $lRet.= '        <div id="'.$lFId.'_container_add" class="flink_button">';
      $lRet.= $lEnabled ? '<input name="files[]" type="file" id="'.$lFId.'" '.$lFiletype.'/>' : '<input name="files[]" type="file" id="'.$lFId.'" disabled="disabled" '.$lFiletype.'/>';
      $lRet.= '        </div>';
      $lRet.= btn(lan('flink.add'), 'jQuery("#'.$lFId.'").trigger("click");', 'img/ico/16/new-hi.gif', 'button', array('id' => $lFId.'_button_add', 'disabled' => 'disabled', 'style' => 'width: 100%'));
      $lRet.= '      </td>';
      $lRet.= '      <td style="width: 50%;">';
      $lRet.= '        <div id="'.$lFId.'_container_upload" class="flink_button">';
      $lRet.= '        </div>';
      $lRet.= btn(lan('flink.upload'), 'Flow.flink.uploadSingle("'.$lFId.'");', 'img/ico/16/upload-hi.gif', 'button', array('id' => $lFId.'_button_upload', 'disabled' => 'disabled', 'style' => 'width: 100%'));
      $lRet.= '      </td>';
      $lRet.= '    </tr>';
      $lRet.= '  </tbody>';
      $lRet.= '</table>';

      return $lRet;
    }
  }

  protected function getTypeParams($aVal, $aState) {
    if ($this->mText){ // Ausgabe als Text.
      return $aVal;
    }
    $lTag = new CHtm_Tag('input');
    $lId = $this -> getId();
    $lTag -> setAtt('id', $lId);
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('type', 'text');
    $lTag -> setAtt('class', 'inp w200');
    $lTag -> setAtt('value', $aVal);
    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }

    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dis');
      return $lTag -> getTag(TRUE);
    } else {
      $lRet = '<table cellpadding="0" cellspacing="0" border="0"><tr>';
      $lRet.= '<td>'.$lTag -> getTag(TRUE).'</td>';
      $lRet.= '<td><a href="javascript:Flow.Std.paramPick(\''.$lId.'\')" class="nav2">'.img('img/ico/16/pick.gif').'</a></td>';
      $lRet.= '</tr></table>';
      return $lRet;
    }
  }

  protected function getTypeJobFieldParams($aVal, $aState) {
    if ($this -> mText){ // Ausgabe als Text.
      return $aVal;
    }

    $lTag = new CHtm_Tag('input');
    $lId = $this -> getId();
    $lTag -> setAtt('id', $lId);
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('type', 'text');
    $lTag -> setAtt('class', 'inp w200');
    $lTag -> setAtt('value', $aVal);
    $lTag -> setAtt('readonly', '');
    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }

    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dis');
      return $lTag -> getTag(TRUE);
    } else {
      $lRet = '<table cellpadding="0" cellspacing="0" border="0"><tr>';
      $lRet.= '<td>'.$lTag -> getTag(TRUE).'</td>';
      $lRet.= '<td><a href="javascript:Flow.Std.jobfieldparamPick(\''.$lId.'\')" class="nav2">'.img('img/ico/16/pick.gif').'</a></td>';
      $lRet.= '</tr></table>';
      return $lRet;
    }
  }

  public function getTypeCurrency($aVal, $aState) {
    if ($this->mText){ // Ausgabe als Text.
      return $aVal;
    }
    $lTag = new CHtm_Tag('input');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('type', 'text');
    $lTag -> setAtt('class', 'inp w200');
    $lTag -> setAtt('value', $aVal);
    $lTag -> setAtt('onkeyup', 'Flow.Std.currencytest(this.value,"'.$this -> getId().'")');

    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dis');
    }
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    return $lTag -> getTag(TRUE);
  }

  protected function getTypeEmail($aVal, $aState) {
    if ($this->mText){ // Ausgabe als Text.
      return $aVal;
    }
    $lTag = new CHtm_Tag('input');
    $lTag -> setAtt('id', $this -> getId());
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('type', 'text');
    $lTag -> setAtt('class', 'inp w200');
    $lTag -> setAtt('value', $aVal);
    $lTag -> setAtt('data-change', 'autocomplete');
    $lTag -> setAtt('data-source', 'ajx.usremail');
    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dis');
    }
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));

    return $lTag -> getTag(TRUE);
  }
  protected function getTypeDomain($aVal, $aState) {
    if ($this->mText){ // Ausgabe als Text.
      return $aVal;
    }
    $lTag = new CHtm_Tag('input');
    $lId = $this -> getId();
    $lTag -> setAtt('id', $lId);
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('type', 'text');
    $lTag -> setAtt('class', 'inp w200');
    $lTag -> setAtt('value', $aVal);
    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    $lNum = getNum('i');
    $lRet = '<table cellpadding="0" cellspacing="0" border="0"><tr><td>';
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dis');
    } else {
      $lTag -> setAtt('onkeyup', 'Flow.Std.chkDom(this,"'.$lNum.'", '.json_encode(CCor_Cfg::get('blacklisted_domains')).')');
      $lTag -> setAtt('onblur', 'Flow.Std.chkDom(this,"'.$lNum.'", '.json_encode(CCor_Cfg::get('blacklisted_domains')).')');
    }
    $lRet.= $lTag -> getTag(TRUE);
    $lRet.= '</td><td>';
    $lImg = 'flag-00.gif';
    $lVal = strtr($aVal, array(' ' => ''));
    if (!empty($lVal)) {
      $lDom = CApp_Valid::domain($lVal) ? true : false;
      if (true == $lDom) {
        $lImg = 'ok.gif';
      } else {
        $lImg = 'ml-4.gif';
      }
    }
    $lRet.= img('img/ico/16/'.$lImg, array('id' => $lNum));
    $lRet.= '</td></tr></table>';

    return $lRet;
  }
  protected function getTypeInt($aVal, $aState) {
    if ($this->mText){ // Ausgabe als Text.
      return $aVal;
    }
    $lTag = new CHtm_Tag('input');
    $lId = $this -> getId();
    $lTag -> setAtt('id', $lId);
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('type', 'text');
    $lTag -> setAtt('class', 'inp w200');
    $lTag -> setAtt('value', $aVal);
    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    $lNum = getNum('i');
    $lRet = '<table cellpadding="0" cellspacing="0" border="0"><tr><td>';
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dis');
    } else {
      $lTag -> setAtt('onkeyup', 'Flow.Std.chkInt(this,"'.$lNum.'")');
      $lTag -> setAtt('onblur', 'Flow.Std.chkInt(this,"'.$lNum.'")');
    }
    $lRet.= $lTag -> getTag(TRUE);
    $lRet.= '</td><td>';
    $lImg = 'flag-00.gif';
    $lVal = strtr($aVal, array(' ' => ''));
    if (!empty($lVal)) {
      $lInt = CApp_Valid::int($lVal) ? true : false;
      if (true == $lInt) {
        $lImg = 'ok.gif';
      } else {
        $lImg = 'ml-4.gif';
      }
    }
    $lRet.= img('img/ico/16/'.$lImg, array('id' => $lNum));
    $lRet.= '</td></tr></table>';

    return $lRet;
  }

  protected function getTypeEan($aVal, $aState) {
    if ($this->mText){ // Ausgabe als Text.
      return $aVal;
    }
    $lTag = new CHtm_Tag('input');
    $lId = $this -> getId();
    $lTag -> setAtt('id', $lId);
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('type', 'text');
    $lTag -> setAtt('class', 'inp w200');
    $lTag -> setAtt('value', $aVal);
    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    $lNum = getNum('i');
    $lRet = '<table cellpadding="0" cellspacing="0" border="0"><tr><td>';
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dis');
    } else {
      $lTag -> setAtt('onkeyup', 'Flow.Std.chkEan(this,"'.$lNum.'")');
      $lTag -> setAtt('onblur', 'Flow.Std.chkEan(this,"'.$lNum.'")');
    }
    $lRet.= $lTag -> getTag(TRUE);
    $lRet.= '</td><td>';
    $lImg = 'flag-00.gif';
    $lVal = strtr($aVal, array(' ' => ''));
    if (!empty($lVal)) {
      $lEAN8 = CApp_Valid::ean($lVal, 8) ? true : false;
      $lEAN13 = CApp_Valid::ean($lVal, 13) ? true : false;
      if (true == $lEAN8 || $lEAN13) {
        $lImg = 'ok.gif';
      } else {
        $lImg = 'ml-4.gif';
      }
    }
    $lRet.= img('img/ico/16/'.$lImg, array('id' => $lNum));
    $lRet.= '</td></tr></table>';
    return $lRet;
  }

  protected function getTypeNewpick($aVal, $aState) {
    if ($this->mText){ // Ausgabe als Text.
      return $aVal;
    }
    $lTag = new CHtm_Tag('input');
    $lId = $this -> getId();
    $lTag -> setAtt('dim', $lId); // CAD Dimension
    $lTag -> setAtt('name', $this -> getName());
    $lTag -> setAtt('type', 'text');
    #$lTag -> setAtt('readonly', '');
    $lTag -> setAtt('class', 'inp w180');
    $lTag -> setAtt('value', $aVal);
    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lTag -> setAtt($lKey, $lVal);
        }
      }
    }
    $lTag -> addAtt('class', 'field_'.$this -> getDef('alias'));
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dis');
      return $lTag -> getTag(TRUE);
    } elseif (bitset($aState, fsSearch)) {
      return $this -> getTypeString($aVal, $aState);
    } else {
      $lLis = $this -> getDef('learn');
      $lPar = toArr($this -> getDef('param'));
      $lDom = (isset($lPar['dom'])) ? $lPar['dom'] : '';
      // if Color Picklist get idx = last Number of alias, ie "col_nr_1", "col_nr_2"
      $lPickDom = CCor_Cfg::get('ColorPickerDom', 'col');
      $lPckMst = CCor_Res::get('pckmaster');
      $lWidth = $lPckMst[$lDom]['width'];
      $lHeight = $lPckMst[$lDom]['height'];

      $lCpp = '';
      if ($lPickDom == $lDom){
        $lAlias = trim($this -> getDef('alias'));
        $lIdx = substr ($lAlias, -2,2); // für 10, 11, 12, ...
        if (!is_numeric($lIdx)) $lIdx = substr($lAlias, -1,1);
        if (preg_match("([1-9]+)", $lAlias, $matches)) { // suche die 1. Zahl > 0 im String
          $lCpp = $matches[0]; //Color Printer Passes / Druckdurchgänge
        }
        $lLis = lan('lib.color');
      } else {
        $lIdx = '';
      }

      $lRet = '<table cellpadding="0" cellspacing="0" border="0"><tr>';
      $lRet.= '<td>'.$lTag -> getTag(TRUE).'</td>';

      #$lFeature = toArr($this -> getDef('feature')); //wird jetzt in cor/res/fie erledigt!
      $lWithSearchVar = (NULL !== $this->getDef('WithSearchVar')) ? $this->getDef('WithSearchVar') : '';
      #  $lRet.= '<td><a href="javascript:Flow.Std.newPick(\'index.php?act=pck-itm&id='.$lDom.'&idx='.$lIdx.'&sys=\',\'jobFrm\',\'750\',\'600\')" class="nav2">'.img('img/ico/16/pick.gif').'</a></td>';
      //<a  href="javascript:Flow.Std.newPick('index.php?act=pck-itm&id=10&idx=1&sys=','jobFrm','500','400')">Pick
      $lRet.= "<td>".$this -> pickLink('jobFrm', $lLis, $lWidth, $lHeight, $lDom, $lWithSearchVar, $lIdx, $lCpp)."</td>";

      $lRet.= '</tr></table>';
      return $lRet;
    }
  }

  protected function getTypeTradio($aVal, $aState) { //Radio Helptable
    if ($this->mText){ // Ausgabe als Text.
      return $aVal;
    }
    $lRoot = new CHtm_Tag('div');
    $lRoot->setAtt('class', 'yap-radio-group');
    $lBaseId = $this->getId();
    $lRoot->setAtt('id', $lBaseId);

    $lTag = new CHtm_Tag('input');
    $lTag -> setAtt('type', 'radio');
    $lTag -> setAtt('name', $this -> getName());

    $lAtt = $this -> getDef('attr');
    if (NULL !== $lAtt) {
      $lAtt = toArr($lAtt);
      if (!empty($lAtt)) {
        foreach ($lAtt as $lKey => $lVal) {
          $lRoot -> setAtt($lKey, $lVal);
        }
      }
    }
    if (bitset($aState, fsDisabled)) {
      $lTag -> setAtt('disabled', 'disabled');
      $lTag -> addAtt('class', 'dissel');
    }

    $lPar = $this -> getDef('param');
    $lPar = toArr($lPar);

    $lDom = (isset($lPar['dom'])) ? $lPar['dom'] : '';
    $lArr = CCor_Res::get('htb', $lDom);

    $lShowEmptyItem = (isset($lPar['emp'])) ? $lPar['emp'] : '';
    if (bitset($aState, fsSearch)) $lShowEmptyItem = true;

    $lItemSeparator = (isset($lPar['sep'])) ? $lPar['sep'] : '';

    $lRoot -> addAtt('class', 'field_'.$this -> getDef('alias'));

    $lRet = '';
    $lRet.= $lRoot->getTag();
    if ($lShowEmptyItem) {
      $lId = $lBaseId.'_null';
      $lRet.= '<div class="radio-item" style="white-space:nowrap;">';
      $lId = $lBaseId.'_null';
      $lTag->setAtt('value', $lKey);
      $lTag->setAtt('id', $lId);
      $lRet.=  $lTag -> getTag().NB;
      $lRet.= '<label for="'.$lId.'">'.htm(lan('lib.unknown')).'</label>';
      $lRet.= '</div>';
      $lRet.= $lItemSeparator;
    }
    $i = 0;
    if (!empty($lArr)) {
      foreach ($lArr as $lKey => $lVal) {
        $lCurTag = clone($lTag);
        $lRet.= '<div class="radio-item" style="white-space:nowrap;">';
        $lId = $lBaseId.'_'.($i++);
        $lCurTag->setAtt('value', $lKey);
        $lCurTag->setAtt('id', $lId);
        if ($aVal == $lKey) $lCurTag->setAtt('checked', 'checked');
        $lRet.=  $lCurTag -> getTag(true).NB;
        $lRet.= '<label for="'.$lId.'">'.htm($lVal).'</label>';
        $lRet.= '</div>';
        $lRet.= $lItemSeparator;
      }
    }
    $lRet.= $lRoot->getEndTag();
    return $lRet;
  }


  protected function pickLink($aForm, $aPickList, $aWidth, $aHeight, $aDom="", $aWithSearchVar = '', $aIndex= '', $aColorPrintPass = 1){
    $lUrl = '"index.php?act=pck-itm.ser';
    $lUrl.= '&amp;dom='.$aDom;
    if (!empty($aIndex)) {
      $lUrl.= '&amp;idx='.$aIndex;
      $lUrl.= '&amp;cpp='.$aColorPrintPass;
    }
    if(!empty($aWithSearchVar)) {
      $lUrl.= '&amp;val[name]="';
      $lUrl.= '+getJobFrmVal("'.$aWithSearchVar.'")';
    } else {
      $lUrl.= '"';
    }
    return $this -> getLink("javascript:Flow.Std.newPick($lUrl,\"$aForm\",\"$aWidth\",\"$aHeight\")","nav-dn",'','','','Picklist '.$aPickList,32767);
  }

  function getLink($aLink, $aImg, $aText='', $aClass='', $aTarget='', $aAlt='', $aTabIndex='') {
    $_target = (empty($aTarget)) ? '' : " target='$aTarget'";
    $_id = getNum('l');
    $_ret = "<a href='$aLink' ";
    if (!empty($aClass)) $_ret.= "class='$aClass' ";
    if (!empty($aTabIndex)) $_ret.= "tabindex='$aTabIndex' ";
    $_ret.= "onmouseover=\"Flow.Std.xg('$_id','img/ico/16/pick.gif')\" ";
    $_ret.= "onmouseout=\"Flow.Std.xg('$_id','img/ico/16/pick.gif')\"$_target>";
    if (empty($aAlt)) {
      $_alt = ' alt=""';
    } else {
      $_alt = ' alt="'.$aAlt.'"';
      $_alt.= ' title="'.$aAlt.'"';
    }
    $_ret.= img('img/ico/16/pick.gif', array('name'=>$_id, 'id'=>$_id, 'border'=>'0'.$_alt));
    if (!empty($aText)) $_ret.= '&nbsp;'.$aText;
    $_ret.= "</a>";
    return $_ret;
  }

  public function getInfoButton() {
    $lTip = '';
    $lUsr = CCor_Usr::getInstance();
    $lLan = $lUsr -> getPref('sys.lang', LANGUAGE);
    $lDesc = $this -> getDef('desc_'.$lLan);
    if ($lUsr -> getPref('job.feldtips', 'Y') == 'Y') {
      if (!empty($lDesc)) {
        $lDesc = htm(preg_replace("/[\n]/","<br/>",$lDesc));
        $lTitel= $this->getDef('name_'.$lLan);
        $lTitel = htm($lTitel);
        $lStyle = THEME === "wave8" ? 'style="background-color:black;border-radius:10px;"' : '';
        $lTip.= '<i class="ico-jfl ico-jfl-1024 info_button" '.$lStyle.' alt="info" data-toggle="tooltip" data-tooltip-head ="'.$lTitel.'" data-tooltip-body="'.$lDesc.'">';
      }
    }
    return $lTip;
  }
}
