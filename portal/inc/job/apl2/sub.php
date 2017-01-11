<?php
class CInc_Job_Apl2_Sub extends CCor_Ren {

  public function __construct($aSub) {
    $this->mSub = $aSub;
    $this->mSid = $aSub['id'];
    //var_dump($this->mSub);

    $this->mParent = $this->mSub->getParent();
    $this->mSrc = $this->mParent['src'];
    $this->mJid = $this->mParent['jobid'];

    $this->getPrefs();

    $this->mUid = CCor_Usr::getAuthId();
    $this->mCanSetStatus = true;
    $this->mGru = CCor_Res::extract('id', 'name', 'gru');
    $this->mStates = $this->loadStates();
    $this->mTaskNames = CCor_Res::get('htb', 'apl_task');
    $this->mMe = false;
  }

  protected function getPrefs() {
    $lUsr = CCor_Usr::getInstance();
    $this->mShowAllSubloops = $lUsr->getPref('job-apl.showallsub');
  }

  protected function getCont() {
    $lState = $this->mSub['subloop_state'];
    if ($lState == 'closed') {
      $this->mCanSetStatus = false;
    }

    $lRet = '';
    $lRet = '<div class="bc-apl-loop indent sid'.$this->mSid.'" data-sub="'.$this->mSid.'">';
    $lRet.= $this->getHeader();

    $lParent = $this->mSub->getParent();

    $lState = $this->mSub['subloop_state'];
    $lDis = ($lState == 'open') ? 'block' : 'none';
    if (!$this->mShowAllSubloops) {
      // parent is collapsed, could not open it otherwise as there are no headers
      $lDis = 'block';
    }

    $lTog = ($lState == 'open') ? '' : ' tg';
    $lRet.= '<div class="box'.$lTog.' bc-sub-'.$lState.' bc-sub-cont" id="sub-'.$this->mSid.'" style="padding:2em; display:'.$lDis.'">';

    $lTyp = $this->mParent['typ'];
    if (!in_array($lTyp, array('apl-phcoll','apl-phtra'))) {
      $lRet .= '<div class="fl">';
      $lRet .= $this->getImage();
      $lRet .= '</div>';
    }

    $lRet.= '<div class="fl indent">';
    $lRet.= $this->getStates();
    $lRet.= '</div>';

    if ($this->canAction()) {
      $lRet.= '<div class="fl indent">';
      $lRet.= $this->getApprovalButtonBox();
      $lRet.= '</div>';
    }

    $lRet.= '<div class="clr"></div>';

    $lRet.= '</div>';
    $lRet.= '</div>';
    return $lRet;
  }

  protected function canAction() {
    if (!$this->mCanSetStatus) return false;
    if (empty($this->mSetRow)) return false;
    return true;
  }

  protected function getImageAct($aUrl, $aCaption, $aIcon, $aTarget = '_blank') {
    $lRet = array();
    $lRet['url'] = $aUrl;
    $lRet['caption'] = $aCaption;
    $lRet['icon'] = $aIcon;
    $lRet['target'] = $aTarget;
    return $lRet;
  }

  protected function isFirstCountrySubLoop() {
    $lSql = 'SELECT COUNT(*) FROM al_job_apl_subloop ';
    $lSql.= 'WHERE loop_id='.intval($this->mSub['loop_id']).' ';
    $lSql.= 'AND prefix='.esc($this->mSub['prefix']);
    $lNum = CCor_Qry::getInt($lSql);
    return $lNum < 2;
  }

  protected function getImageActions($aTask = null) {
    $lRet = array();

    $lHasDms = !empty($this->mSub['file_name']);
    if ($lHasDms) {
      if ($aTask == 'add content') {
        $lRet['lock'] = $this->getImageAct('#', 'Download and lock', 'excel.gif');
      }
      $lUrl = 'index.php?act=utl-dms.open&docverid='.$this->mSub['file_version'].'&fn='.urlencode($this->mSub['file_name']);
      $lRet['down'] = $this->getImageAct($lUrl, 'Download', 'excel.gif');
    } else {
      if ($aTask == 'add content') {
        if ($this->isFirstCountrySubLoop()) {
          $lFiles = CJob_Apl2_Dmsfiles::getInstance($this->mSrc, $this->mJid);
          $lRow = $lFiles->getLatest(1); // get latest version of master
          $lUrl = 'index.php?act=utl-dms.open&docverid='.$lRow['fileversionid'].'&fn='.urlencode($lRow['filename']);
          $lRet['master'] = $this->getImageAct($lUrl, 'Download Master', 'excel.gif');
        }
      }
    }
    $lPdf = $this->mSub['file_secondary'];
    $lHasViewer = !empty($lPdf);
    if ($lHasViewer) {
      if ($aTask == 'approve') {
        $lLnk = 'index.php?act=utl-dalim.openapl&doc='.urlencode($lPdf).'&src='.$this->mSrc.'&jid='.$this->mJid;
        $lRet['view'] = $this->getImageAct($lLnk, 'View PDF', 'pdf.png');
      }
      $lPdfLast = substr(strrchr($lPdf, '/'),1);
      $lUrl = 'index.php?act=utl-fil.down&src='.$this->mSrc.'&jid='.$this->mJid.'&sub=dalim&fn='.$lPdfLast;
      $lRet['pdf']  = $this->getImageAct($lUrl, 'Download PDF', 'pdf.png', '_blank');
    }
    return $lRet;
  }

  protected function getImage() {
    $lTask = '';
    if (isset($this->mSetRow)) {
      $lTask = $this->mSetRow['task'];
    }
    $lAct = $this->getImageActions($lTask);

    $lRet = '';
    if (empty($lAct)) {
      $lRet.= '<div class="weak" style="width:250px; min-width:250px">';
      $lRet.= '(no file yet)';
      $lRet.= '</div>';
      return $lRet;
    }

    $lRet.= '<table class="tbl" style="width:250px; min-width:250px">';

    $lRet.= '<tr>';
    $lName = $this->mSub['file_name'];
    if (empty($lName)) {
      $lName = 'File Actions';
    }
    $lRet.= '<td class="th3" colspan="2">'.htm($lName).'</td></tr>';
    foreach ($lAct as $lRow) {
      $lRet.= '<tr>';
      $lRet.= '<td class="td1 w16">'.img('ico/16/'.$lRow['icon']).'</td>';
      $lRet.= '<td class="td1"><a href="'.htm($lRow['url']).'"';
      if (!empty($lRow['target'])) {
        $lRet.= ' target="'.htm($lRow['target']).'"';
      }
      $lRet.= ' class="nav">';
      $lRet.= htm($lRow['caption']).'</a></td>';
      $lRet.= '</tr>';
    }
    /*
    $lRet.= '<tr>';
    $lRet.= '<td class="td1 w16">'.img('ico/16/excel.gif').'</td>';
    if ($this->mCanSetStatus) {
      $lRet.= '<td class="td1"><a href="#" class="nav">Download and Lock</a></td>';
    } else {
      $lRet.= '<td class="td1">&nbsp;</td>';
    }
    $lRet.= '<td class="td1"><a href="#" class="nav">Download Excel</a></td>';
    $lRet.= '<td class="td1 ac w16">V1</td>';
    $lRet.= '</tr>';

    $lRet.= '<tr>';
    $lRet.= '<td class="td1">'.img('ico/16/pdf.png').'</td>';
    $lRet.= '<td class="td1"><a href="#" class="nav">View File</a></td>';
    $lRet.= '<td class="td1"><a href="#" class="nav">Download PDF</a></td>';
    $lRet.= '<td class="td1 ac">V1</td>';
    $lRet.= '</tr>';

    */
    $lRet.= '</table>';
    return $lRet;
  }

  protected function getHeader() {
    if (!$this->mShowAllSubloops) {
      return '';
    }
    $lState = $this->mSub['subloop_state'];
    if ($lState == 'open') {
      return '';
    }
    $lRet = '';
    $lRet.= '<div class="th3 cp p4 bc-sub-row" onclick="Flow.Std.tog(\'sub-'.$this->mSid.'\')">';

    #$lRet.= 'Sub id '.$this->mSid.' '.$this->mSub['prefix'];

    $lDate = $this->mSub['start_date'];
    $lObj = new CCor_Date($this->mSub['start_date']);
    $lFmt = $lObj->getFmt('d.m.Y');

    $lRet.= 'Started '.$lObj->getFmt('d.m.Y');

    $lObj->setSql($this->mSub['close_date']);
    if (!$lObj->isEmpty()) {
      $lRet.= ', closed '.$lObj->getFmt('d.m.Y');
    }
    $lRet.= '</div>';
    return $lRet;
  }

  protected function loadStates() {
    if (isset($this->mStates)) {
      return $this->mStates;
    }
    $lHadUnfinished = false;
    $lOldPos = 1;
    $lCouldAction = true;
    $lRet = $this->mSub->loadDisplayStates($this->mUid);
    foreach ($lRet as $lKey => $lRow) {
      if ($lRow['pos'] != $lOldPos) {
        $lOldPos = $lRow['pos'];
        if ($lHadUnfinished) {
          $lCouldAction = false;
        }
      }
      $lMe = ($lRow['user_id'] == $this->mUid);
      if ($lMe && $lCouldAction && $lRow['status'] == 0) {
        if (!isset($this->mSetRow)) {
          $this->mSetRow = $lRow;
          $this->mSetId = $lRow['id'];
          $this->mMe = true;
        }
        if ($lCouldAction) {
          $lRow['canAction'] = true;
        } else {
          $lRow['canAction'] = false;
        }
        $lRet[$lKey] = $lRow;
      }
      if ($lRow['status'] == 0) {
        $lHadUnfinished = true;
      }
    }
    return $lRet;
  }

  protected function getStates() {
    $lRet = '';
    $lRows = $this->loadStates();
    if (empty($lRows)) {
      return '';
    }
    $lRet = '<table class="tbl">';
    $lRet.= $this->getStatesHeader();
    $lRet.= $this->getStatesRows($lRows);
    $lRet.= '</table>'.LF;

    return $lRet;
  }

  protected function getStatesHeader() {
  	$lRet = '<tr>';
  	$lRet.= '<td class="th3 w16">&nbsp;</td>';
  	$lRet.= '<td class="th3 w16">&nbsp;</td>';
  	$lRet.= '<td class="th3 w16">&nbsp;</td>';
  	$lRet.= '<td class="th3 w200">Name</td>';
  	$lRet.= '<td class="th3 w400">'.htm(lan('lib.msg')).'</td>';
  	$lRet.= '<td class="th3 w200">Task</td>';
  	#$lRet.= '<td class="th3 w80">Actions</td>';
  	$lRet.= '</tr>';
  	return $lRet;
  }

  protected function getAnnotationsForUser($aUid) {
    $lSecondaryFile = $this->mSub['file_secondary'];
    if (empty($lSecondaryFile)) {
      return array();
    }
    $lAnnot = CJob_Apl2_Annotations::getInstance($this->mSrc, $this->mJid);
    $lRet = $lAnnot->getByUser($aUid, null, $lSecondaryFile);
    return $lRet;
  }

  protected function getPhraseAnnotations($aUid) {
    $lAnnot = CJob_Apl2_Annotations::getInstance($this->mSrc, $this->mJid);
    $lRet = $lAnnot->getPhraseTableByUser($aUid, $this->mSid);
    return $lRet;
  }


  protected function getStatesRows($aRows) {
    $lRet = '';
    $lPrevFinished = true;
    foreach ( $aRows as $lRow ) {
      $lMe = ($lRow ['user_id'] == $this->mUid);
      $lRowClass = '';

      $lIsActive = false;

      $lAnnot = $this->getAnnotationsForUser($lRow['user_id']);
      $lPhraseTable = $this->getPhraseAnnotations($lRow['user_id']);
      $lComment =  $lRow['comment'];

      if ($lMe) {
        $lRowClass .= ' bc-state-mine';
      }
      if ($lRow ['canAction'] && $lPrevFinished && $lMe && ($lRow ['status'] == 0) && $this->mCanSetStatus) {
        $lRowClass .= ' bc-state-active';
        $lIsActive = true;
      }
      // $lGru = $lRow ['gru_id'];
      // if ($lGru != $lOldGroup) {
      //$lOldGroup = $lGru;
      $lRet .= '<tr class="' . $lRowClass . '">';

      $lRet.= '<td class="td1 ac">';
      $lMoreId = false;
      if (!empty($lAnnot) || !empty($lComment) || !empty($lPhraseTable)) {
        $lMoreId = getNum('t');
        $lRet.= '<a class="nav" onclick="Flow.Std.togTr(\''.$lMoreId.'\')">';
        $lRet.= '...</a>';
      }
      //$lRet.= $lRow['id'];
      $lRet.= '</td>';

      $lRet .= '<td class="td1 ac">' . $this->getStatusImage ( $lRow ['status'] ) . '</td>';
      $lRet .= '<td class="td1 ac">' . $lRow ['position'] . '</td>';

      $lCls = ($lIsActive) ? ' cy' : '';
      $lRet .= '<td class="td1' . $lCls . '">';

      $lStatus = $lRow['status'];
      $lIsUntouched = ($lStatus == CApp_Apl_Loop::APL_STATE_DEFAULT) || ($lStatus == CApp_Apl_Loop::APL_STATE_FORWARD);
      $lName = '';
      #if ($lIsUntouched) {
      $lGruId = $lRow['gru_id'];
      $lName = $lRow['name'];
      if (isset ( $this->mGru [$lGruId] )) {
        $lName = $this->mGru[$lGruId];
      }

      #}
      $lRet .= htm($lName);
      // $lRet.= ' '.$lRow['canAction'];
      if (!$lIsUntouched && $lGruId != 0) {
        $lRet.= ': '.htm($lRow ['name']);
      }
      $lRet .= '</td>';


      $lRet .= '<td class="td1">' . $lRow ['comment'];
      if (!empty($lAnnot)) {
        $lRet.= ' ('.count($lAnnot).' Annotations)';
      }
      //$lRet.= NB.$lRow['id'];
      $lTask = $lRow ['task'];
      $lTaskName = $this->getTaskName($lTask);

      $lRet.= '</td>';
      $lRet .= '<td class="td1">' . htm($lTaskName) . '</td>';
      // $lRet .= '<td class="td1">' . var_export($lRow->toArray(), true) . '</td>';
      // $lRet .= '<td class="td1">' . $this->getStatusActionMenu ( $lRow ) . '</td>';
      $lRet .= '</tr>' . LF;
      // }

      if ($lMoreId) {
        $lRet.= '<tr id="'.$lMoreId.'" class="togtr" style="display:none">'.LF;
        $lRet.= '<td class="td1 tg">&nbsp;</td>'.LF;
        $lRet.= '<td class="td1 p8" colspan="5">';
        if (!empty($lComment)) {
          $lRet.= '<b>Comment:</b> '.htm($lComment).BR.BR;
          //$lRet.= $this->mSid. var_export($lAnnot, true);
        }
        if (!empty($lAnnot)) {
          $lRet.= '<b>Annotations:</b>'.BR.BR;
          foreach ($lAnnot as $lRow) {
            $lRet.= '<span class="app-version">'.$lRow['num'].'</span>'.NB;
            $lRet.= htm($lRow['content']);
            $lRet.= ' (Page <b>1</b>)';
            $lRet.= BR.BR;
          }
        }
        if (!empty($lPhraseTable)) {
          $lRet.= $lPhraseTable;
        }
        $lRet.= '</td>';
        $lRet.= '</tr>'.LF;

      }

    }
    // $lRet.= var_export($this->mSetRow, true);
    return $lRet;
  }

  protected function getStatusImage($aState) {
    $lRet = img('img/ico/16/flag-0'.$aState.'.gif');
    return $lRet;
  }

  protected function getStatusActionMenu($aRow) {
    $lMen = new CHtm_Menu('Actions');
    $lMen->addTh2($aRow['name']);
    $lMen->addItem('#', 'Add user...');
    $lMen->addItem('#', 'Send Email...');
    #$lMen->addItem('#', 'Restart this Subloop');
    $lMen->addItem('javascript:Flow.apl.restartSubLoop('.$this->mSid.')', 'Restart Subloop');
    $lMen->addTh2('Admin [ID '.$aRow['id'].']');
    $lMen->addItem('#', 'Delete');
    $lMen->addItem('javascript:Flow.apl.resetStatus('.$aRow['id'].','.$this->mSid.')', 'Reset Status');
    $lMen->addItem('#', 'Replace user with...');
    $lMen->addItem('#', 'Edit raw data...');
    return $lMen->getContent();
  }

  protected function getApprovalButtonBox() {
    $lRet = '';
    if (!$this->mCanSetStatus) {
      return '';
    }
    if ($this->mMe) {
      return '';
    }

    $lRet.= '<div class="box">';
    if (isset($this->mSetRow)) {
      $lTask = $this->mSetRow['task'];
      $lTaskName = $this->getTaskName($lTask);
      $lRet.= '<div class="cap">'.htm($lTaskName).'</div>';
    }
    $lRet.= '<div class="p8">';
    $lRet.= $this->getApprovalButtons();
    $lRet.= '</div>';
    $lRet.= '</div>';

    return $lRet;
  }

  protected function getTaskName($aTask) {
    $lRet = $aTask;
    if (isset($this->mTaskNames[$lRet])) {
      $lRet = $this->mTaskNames[$lRet];
    }
    return $lRet;
  }


  protected function getApprovalButtons() {
    $lTask = '';
    if (isset($this->mSetRow)) {
      $lTask = $this->mSetRow['task'];
    }
    if (substr($lTask, 0,3) == 'ph_') {
      return $this->getPhraseButtons($lTask);
    }
    $lMethod = 'getButtons'.$lTask;
    $lMethod = strtr($lMethod, ' -', '__');
    if ($this->hasMethod($lMethod)) {
      return $this->$lMethod();
    }
    return $this->getDefaultApprovalButtons();
  }

  protected function getPhraseButtons($aTask) {
    $lRet = '';
    $lArr = explode('_', $aTask, 4);
    $lPart = $lArr[2]; // master or content

    if (!isset($this->mSetRow)) return '';
    $lRow = $this->mSetRow;
    $lCat = $this->getPhraseCategoriesForTask($aTask);

    $lImg = 'img/ico/16/flag-03.gif';
    $lName = $this->getTaskName($aTask);
    $lJs = 'Flow.cmsApl.prepareDialog(';
    $lJs.= '\''.htm($aTask).'\',';
    $lJs.= '\''.htm($lPart).'\',';
    $lJs.= '\''.htm($lRow['prefix']).'\',';
    $lJs.= '\''.htm($lRow['id']).'\',';
    $lJs.= '\''.htm($lRow['sub_loop']).'\',';
    $lJs.= '\''.Zend_Json::encode($lCat).'\',';
    $lJs.= '\''.htm($this -> mJid).'\',';
    $lJs.= '\''.htm($this -> mSrc).'\'';
    $lJs.= ')';
    $lRet.= $this->getJsButton($lName, $lJs, $lImg);
    return $lRet;
  }

  protected function getPhraseCategoriesForTask($aTask) {
    if (isset($this->mPhraseCategoriesForTask[$aTask])) {
      return $this->mPhraseCategoriesForTask[$aTask];
    }
    $lRet = array();
    $lSql = 'SELECT DISTINCT(`category`) AS cat FROM `al_cms_categorytasks` ';
    $lSql.= 'WHERE `mand`='.intval(MID).' AND `task`='.esc($aTask);
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $lCat = $lRow['cat'];
      if (!empty($lCat)) {
        $lRet[] = $lRow['cat'];
      }
    }
    $this->mPhraseCategoriesForTask[$aTask] = $lRet;
    return $lRet;
  }

  protected function getDefaultApprovalButtons() {
    $lAplButtons = CCor_Cfg::get('buttons.apl', array());
    unset($lAplButtons[6]);
    $lBtn = array();
    foreach ($lAplButtons as $lKey => $lCode) {
      $lBtn[$lKey] = lan('apl.'.$lCode);
    }
    return $this->getButtonsFromArray($lBtn);
  }


  protected function getButtonsFromArray($aButtonArray) {
    $lRet = '';
    if (!empty($aButtonArray)) {
      //$lAtt = array('class' => 'btn w200');
      foreach ($aButtonArray as $lAplKey => $lName) {
        $lJs = 'Flow.apl.setStatus('.$this->mSetId.','.$lAplKey.',"'.$lName.'",'.$this->mSid.')';
        $lImg = 'img/ico/16/flag-0'.$lAplKey.'.gif';
        $lRet.= $this->getJsButton($lName, $lJs, $lImg);
      }
    }
    return $lRet;
  }

  protected function getJsButton($aCaption, $aJs, $aImg = '') {
    $lAtt = array('class' => 'btn w200');
    return btn($aCaption, $aJs, $aImg, 'button', $lAtt).BR.BR;
  }

  protected function getButtonsAdd_content() {
    $lBtn = array('3' => 'Finished');
    return $this->getButtonsFromArray($lBtn);
  }

  protected function getButtonsCheck() {
    $lRet = '';
    $lName = 'Translation approved';
    $lJs = 'Flow.apl.setStatus('.$this->mSetId.',3,"'.$lName.'",'.$this->mSid.')';
    $lImg = 'img/ico/16/flag-03.gif';
    $lRet.= $this->getJsButton($lName, $lJs, $lImg);

    $lName = 'Restart All';
    $lJs = 'Flow.apl.restartSubLoop('.$this->mSetId.','.$this->mSid.',1)';
    $lImg = 'img/ico/16/flag-01.gif';
    $lRet.= $this->getJsButton($lName, $lJs, $lImg);

    $lName = 'Restart Rejected';
    $lJs = 'Flow.apl.restartSubLoop('.$this->mSetId.','.$this->mSid.',0)';
    $lImg = 'img/ico/16/flag-01.gif';
    $lRet.= $this->getJsButton($lName, $lJs, $lImg);
    return $lRet;
  }


}
