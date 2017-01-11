<?php

class CInc_Job_Questions_Form extends CHtm_Form {

  private $mCurrDomain = '';
  private $mCurrName = '';
  private $mQCount = 0;
  private $mReadOnly = false;
  private $mCurrentMaster = null;
  private $mTabIndex = 1;

  public function __construct($aSrc, $aJobId, $aJob, $aAct = 'sedt') {
    parent::__construct('job-' . $aSrc . '-questions.' . $aAct, lan('job-questions.menu'), 'job-' . $aSrc . '-questions&jobid=' . $aJobId);
    $this->setParam('jobid', $aJobId);
    $this->setAtt('class', 'tbl w800');
    $this->mSrc = $aSrc;

    $this->addDef(fie('answer', lan('lib.answer'), 'memo', NULL, array('rows' => '6', 'class' => 'inp w800')));

    //Check for Rights
    $this->mUsr = CCor_Usr::getInstance();
    if (!$this->mUsr->canEdit('questions-job')) {
      $this->mReadOnly = true;
    }
  }

  //Set the current Job Id
  public function setJobId($aJobid) {
    $this->mJobId = intval($aJobid);
  }

  //Returns Search Bar
  protected function getControlBar() {
    $lRet = '<div class="frm w800" id="search" style="padding:10px;">';
    $lRet .= '<table><tr>';
    $lRet .= '<td>'.$this->getJsButton('Flow.checkAll(\'QL_Checkbox\')', 'ico-w16-plus', lan("lib.sel.all")).'</td>';
    $lRet .= '<td>'.$this->getJsButton('Flow.uncheckAll(\'QL_Checkbox\')', 'ico-w16-ml-8', lan("lib.desel.all")).'</td>';
    $lRet .= '<td> | </td>';
    $lRet .= '<td>'.$this->getSaveAllMenu().'</td>';
    $lRet .= '<td> | </td>';
    $lRet .= '<td>'.$this->getFilterMenu().'</td>';
    $lRet .= '<td> | '.lan("lib.search") . ': ';
    $lRet .= '<input type="text" name="srch" id="srch" class="inp w300"></td>';
    
    $lRet .= '</tr></table>';
    $lRet .= '</div>';
    return $lRet;
  }

  protected function getFilterMenu() {
    $lMenu = new CHtm_Menu(lan('lib.filter'));
    $lMenu->addJsItem('Flow.job.filterQuestions(3)', lan('questions.3'), '<i class="ico-w16 ico-w16-flag-03"></i>');
    if(CCor_Cfg::get("questions-yellow")) {
      $lMenu->addJsItem('Flow.job.filterQuestions(2)', lan('questions.2'), '<i class="ico-w16 ico-w16-flag-02"></i>');
    }
    $lMenu->addJsItem('Flow.job.filterQuestions(1)', lan('questions.1'), '<i class="ico-w16 ico-w16-flag-01"></i>');
    $lMenu->addJsItem('Flow.job.filterQuestions(-1)', lan('lib.show_all'), '<i class="ico-w16 ico-w16-mt-16"></i>');

    return $lMenu->getContent();
  }

  protected function getSaveAllMenu() {
    $lMenu = new CHtm_Menu(lan('lib.save_all'));
    $lMenu->addJsItem('Flow.job.saveAllQuestions(3, \''.$this->mSrc.'\',\''.$this->mJobId.'\');', lan('questions.allGreen'), '<i class="ico-w16 ico-w16-flag-03"></i>');
    $lMenu->addJsItem('Flow.job.saveAllQuestions(1, \''.$this->mSrc.'\',\''.$this->mJobId.'\');', lan('questions.allRed'), '<i class="ico-w16 ico-w16-flag-01"></i>');

    return $lMenu->getContent();
  }

  protected function getJsButton($aJs, $aIco, $aText) {
    $lRet = '<button type="button" class="btn" onclick=javascript:'.$aJs.'>';
    $lRet .= '<table>';
    $lRet .= '<tr>';
    $lRet .= '<td><i class="ico-w16 '.$aIco.'"></td>';
    $lRet .= '<td>'.$aText.'</td>';
    $lRet .= '</tr>';
    $lRet .= '</table>';
    $lRet .= '</button>';

    return $lRet;
  }

  //Runs over all Question and prints the main form
  protected function getForm() {
    $lRet = $this->getControlBar();
    $lRet .= '<div class="frm w800" style="padding:10px;">' . LF;
    //Display all Questions
    $lQry = new CCor_Qry('SELECT * FROM al_job_questions_' . MID . ' WHERE jobid=' . esc($this->mJobId) . ' AND hide = "0" order by question_list_id ASC, question_'.LAN);
    foreach ($lQry->getAssocs() as $lRow) {
      $this->assignVal($lRow);
      $lRet.= $this->getFieldForm();
    }
    $lRet.= '</div>';
    return $lRet;
  }

  //Prints the single item and its heading
  protected function getFieldForm() {
    $lRet = '';
    //Get Current Catalog
    if($this->mCurrentMaster['id'] != $this->getVal('question_list_id')) {
      $lQry = new CCor_Qry('SELECT * FROM al_questions_master WHERE id=' . esc($this->getVal('question_list_id')));
      $this->mCurrentMaster = $lQry->getAssoc();
    }

    if (!empty($this->mFie)) {
      foreach ($this->mFie as $lAlias => $lDefInfo) {
        $lReadOnly = false;
        //Get Field Size
        $lSize = $this->getVal('size') == 0 ? 6 : $this->getVal('size');
        //Check if the field is hided and skip
        if ($this->getVal('hide') === "1") {
          continue;
        }
        $this->mId = $this->getVal('id');
        //Print Domain/Name from QL
        if ($this->mCurrDomain != $this->mCurrentMaster["domain"] || $this->mCurrName != $this->mCurrentMaster["name_" . LAN]) {
          if($this->mCurrName !== '') {
            $lRet .= '</div>';
          }
          $lRet .= '<div class="th1 w800">'
                  . '<i class="ico-w16 ico-w16-cpl-collapse" id="qIco_'.$this->mQCount.'" style="float:left;"></i>'
                  . '<a id="qLnk_'.$this->mQCount.'" href="javascript:Flow.Std.togCpl(\'qArea_'.$this->mQCount.'\', \'qIco_'.$this->mQCount.'\', \'qLnk_'.$this->mQCount.'\',\'\')">'
                  . '' . $this->mCurrentMaster["domain"] . ' - ' . $this->mCurrentMaster["name_" . LAN] . '</a></div>';
          $lRet .= '<div id="qArea_'.$this->mQCount.'">';
          $this->mCurrDomain = $this->mCurrentMaster["domain"];
          $this->mCurrName = $this->mCurrentMaster["name_" . LAN];
          $this ->mQCount++;
        }

        //Print Question
        $lDef = $lDefInfo;
        $lRet.= '<div class="w800 quest_' . $this->getVal('id') . '">' . LF;

          //Print Head Section
          $lRet .= $this->printQuestionMenu($lAlias);

        //Print Input
        $lRet.= '<div>' . LF;
        $lRet .= '<table><tr>';
        if ($this->getVal($lAlias) == "") {
          $lDef["attr"] = array('style' => 'border:1px solid red;', 'rows' => $lSize, 'class' => 'inp w800', 'tabindex' => $this->mTabIndex++);
        }
        else {
          $lDef["attr"] = array('rows' => $lSize, 'class' => 'inp w800', 'tabindex' => $this->mTabIndex++);
        }
        if ($this->getVal('status') == '3' OR $this->mReadOnly) {
          $lReadOnly = true;
        }
        $lRet.= '<td>' . $this->mFac->getInput($lDef, $this->getVal($lAlias), $lReadOnly) . '</td>';
        $lRet.= '<td></td>';
        $lRet.= '</tr></table>';
      }
      $lRet.= '</div></div>' . LF;

    }
    return $lRet;
  }


  protected function getButtons($aBtnAtt = array(), $aBtnTyp = 'button') {
    $lRet = "</div>";

    $lRet .= btn(lan("questions.allGreen"), '#', '<i class="ico-w16 ico-w16-flag-03">', 'button', array('onclick' => 'javascript:Flow.job.saveAllQuestions(3,"'.$this->mSrc.'","'.$this->mJobId.'");', 'class' => 'btn', 'style' => 'margin:10px;'));
    $lRet .= btn(lan("questions.allRed"), '#', '<i class="ico-w16 ico-w16-flag-01">', 'button', array('onclick' => 'javascript:Flow.job.saveAllQuestions(1,"'.$this->mSrc.'","'.$this->mJobId.'");', 'class' => 'btn', 'style' => 'margin:10px;'));

    return $lRet;
  }

  protected function printQuestionMenu($aAlias) {
    $lUsrId = $this->getVal('usr_id');
    $lUsr = CCor_Res::extract('id', 'fullname', 'usr', array('id' => $lUsrId));
    $lUsrName = $lUsr[10];

    $lRet = '<div style="padding-top:10px;">' . LF;
      $lRet .= '<table><tr>';
      $lRet .= '<td colspan="4" class="w800 questName"><strong>' . htm($this->getVal("question_".LAN)) . '</strong></td>';
      $lRet .= '</tr><tr>';
      $lRet .= '<td style="width:16px;"><i class="stateIco ico-w16 ico-w16-flag-0' . htm($this->getVal('status')) . '"></i></td>';
      if(($this->getVal('status') < 3 && $this->mUsr->canEdit('questions-yellow')) || ($this->getVal('status') && $this->mUsr->canEdit('questions-green'))) {
        $lRet .= '<td style="width:16px;"><input type="checkbox" class="QL_Checkbox" name="QL_check_'.$this->getVal('id').'"></td>';
      }
      $lRet .= '<td class="w50 saveButtons">' . $this->printButtons($this->mId, $this->mSrc, $this->mJobId, $this->getVal($aAlias)) . '</td>';
      $lRet .= '<td style="text-align:right" class="lastChanged"><span style="border-radius:5px;padding:5px;">' . lan("lib.change") . ': ' . $lUsrName . ' ' . lan('lib.on') . ' ' . htm($this->getVal('datum')) . '</span></td>';
      $lRet .= '</tr></table>';
    $lRet .= '</div>';

    return $lRet;
  }


  protected function printButtons($aId, $aSrc, $aJobId, $aQuest) {
    $lMenu = new CHtm_Menu(lan('lib.ok'));
    if ($this->mUsr->canEdit('questions-yellow') && $this->getVal('status') != '3') {
      $lMenu->addJsItem('Flow.job.saveJobQuestion(\'' . $aId . '\', \'3\',\'' . $aSrc . '\',\'' . $aJobId . '\',\'' . $aQuest . '\')', lan('questions.3'), '<i class="ico-w16 ico-w16-flag-03"></i>');
    }
    if (($this->mUsr->canEdit('questions-yellow') && $this->getVal('status') != '3') && CCor_Cfg::get("questions-yellow")) {
      $lMenu->addJsItem('Flow.job.saveJobQuestion(\'' . $aId . '\', \'2\',\'' . $aSrc . '\',\'' . $aJobId . '\',\'' . $aQuest . '\')', lan('questions.2'), '<i class="ico-w16 ico-w16-flag-02"></i>');
    }
    if (($this->mUsr->canEdit('questions-yellow') && $this->getVal('status') != '3') || $this->mUsr->canEdit('questions-green')) {
      $lMenu->addJsItem('Flow.job.saveJobQuestion(\'' . $aId . '\', \'1\',\'' . $aSrc . '\',\'' . $aJobId . '\',\'' . $aQuest . '\')', lan('questions.1'), '<i class="ico-w16 ico-w16-flag-01"></i>');
    }
    $lRet = $lMenu->getContent();
    return $lRet;
  }

  protected function getJs() {
    parent::getJs();
    $lRet = '<script type="text/javascript">';
      $lRet .= 'var lTimer = null;';
      $lRet .= 'jQuery("#srch").keyup(function(){';
        $lRet .= 'clearTimeout(lTimer);';
        $lRet .= 'lTimer = setTimeout(doSearch, 200);';
      $lRet .= '});';
      $lRet .= 'function doSearch() {';
        $lRet .= 'aElem = jQuery("#srch");';
        $lRet .= 'lSearch = jQuery(aElem).val().trim();';
        $lRet .= 'lQuests = jQuery("div[class*=\'quest_\']");';
        $lRet .= 'lQuestNames = jQuery("td[class*=\'questName\'] > strong");';
        $lRet .= 'if(lSearch.length > 0) {';
          $lRet .= 'jQuery.each(lQuests, function(lKey, lVal) {';
            $lRet .= 'if(jQuery(lVal).find(".questName").text().toLowerCase().indexOf(lSearch.toLowerCase()) < 0 && jQuery(lVal).find(".questName").text().length > 0) {';
              //Hide Questions
              $lRet .= 'jQuery(lVal).slideUp();';
            $lRet .= '}';
            $lRet .= 'else if(jQuery(lVal).find(".questName").text().length > 0){';
              //Show Questions
              $lRet .= 'jQuery(lVal).slideDown();';
            $lRet .= '}';
          $lRet .= '});';
        $lRet .= '}';
        $lRet .= 'else {';
          $lRet .= 'jQuery("div[class*=\'quest_\']").slideDown();';
        $lRet .= '}';
      $lRet .= '}';
    $lRet .= '</script>';
    return $lRet;
  }

}
