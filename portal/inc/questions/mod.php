<?php
class CInc_Questions_Mod extends CCor_Mod_Table {

  public function __construct() {
    parent::__construct('al_questions_master');

    $this -> addField(fie('id'));
    $this -> addField(fie('domain'));

    $this -> mAvailLang = CCor_Res::get('languages');
    foreach ($this -> mAvailLang as $lLang => $lName) {
      $this -> addField(fie('name_'.$lLang));
    }

    $this -> addField(fie('cnd_id'));
  }

  public static function clearCache() {
    $lCKey = 'cor_res_questionsmaster';
    CCor_Cache::clearStatic($lCKey);
  }

  protected function afterChange() {
    self::clearCache();
  }

  protected function beforePost($aNew = FALSE) {
    if ($aNew) {
      $this -> setVal('mand', MID);
    }
  }

  static function getCountUnanswered($aJobId) {
    $lNum = new CCor_Qry("SELECT COUNT(*) as cnt FROM al_job_questions_".MID." WHERE jobid = '".$aJobId."' AND hide = '0' AND (status <> 3)");
    $lNum = $lNum->getAssoc();

    return $lNum["cnt"];
  }

  public function getQuestions($aJobId, $aOrder = "status desc") {
    $lQry = new CCor_Qry("SELECT * FROM al_job_questions_".MID." WHERE jobid = ". $aJobId." AND hide = '0' ORDER BY ". $aOrder);

    return $lQry->getAssocs();
  }

  public function createQL($aJobId, $aSrc) {
    $lQuestQry ="";
    $this->mJobId = $aJobId;
    $this->mSrc = $aSrc;
    $lDate = date('Y-m-d G:i:s');
    $lUsr = CCor_Usr::getAuthId();

    //Load current Questions from the Job
    $lQry = "SELECT * FROM al_job_questions_".MID." WHERE jobid = ".$this->mJobId;
    $lJobQuestions = new CCor_Qry($lQry);
    $lJobQuestions = $lJobQuestions->getAssocs();

    //Load all matching Questions from DB
    $lQuestions = $this->loadQuestions();

    //Save questions to Job Question List
    $lNewQuestionIds = array();
    foreach($lQuestions as $lRow) {
      $lQuestQry = "INSERT INTO `al_job_questions_".MID."` (`jobid`, `src`, `question_list_id`, `question_item_id`, `question_type`, `question_en`, `question_de`, `datum`, `usr_id`, `status`, `hide`, `size`) VALUES (".$this->mJobId.", '".$this->mSrc."', ".$lRow["master_id"].", ".$lRow["id"].", '".$lRow["question_type"]."', '".$lRow["name_en"]."', '".$lRow["name_de"]."', '".$lDate."', ".$lUsr.", '1', '0', '".$lRow["size"]."')";
      $lQuestQry .= " ON DUPLICATE KEY UPDATE `hide` = '0';";
      CCor_Qry::exec($lQuestQry);
      array_push($lNewQuestionIds, $lRow["id"]);
    }

    //Check for deleted questions and hide them
    foreach($lJobQuestions as $lRow) {
      if(!in_array($lRow['question_item_id'], $lNewQuestionIds)) {
        $this->hideQuestion($lRow['id']);
      }
    }

    //Update Job His
    $lHis = new CJob_His($this->mSrc, $this->mJobId);
    $lHis ->add(htAnsweredQuestion, "Question List updated", "");
  }

  public function importQL($aJobId, $aRefJobId, $aSrc) {
    $lNewQuestions = $this->getQuestions($aRefJobId, 'question_list_id');
    $lOldQuestions = $this->getQuestions($aJobId, 'question_list_id');
    $lReg = new CApp_Condition_Registry();
    $lQry = new CCor_Qry;
    $lDate = date('Y-m-d G:i:s');
    $lUsr = CCor_Usr::getAuthId();
    $lFac = new CJob_Fac($aSrc, $aJobId);
    $lJob = $lFac -> getDat();
    $lMasterID = -1;
    $lSkip = false;

    foreach($lNewQuestions as $lRow) {
      if($lMasterID != $lRow['question_list_id']) {
        $lMasterID = $lRow['question_list_id'];
        $lSkip = false;

        $lSql = "SELECT * FROM al_questions_master WHERE id = ".$lMasterID;
        $lQry->query($lSql);
        $lList = $lQry->getAssoc();

        //Check Question List
        if($lList['cnd_id'] != "0" && $lList['cnd_id'] != "") {
          $lCnd = $lReg -> loadFromDb($lList['cnd_id']);
          $lCnd -> setContext('data', $lJob);
          //Check Item List for Condition
          if(!$lCnd->isMet()) {
            $lSkip = true;
            continue;
          }
        }
      }

      //Check Question
      if($lSkip === false) {
        $lSql = "SELECT * FROM al_questions_items WHERE id = ".$lRow['question_item_id'];
        $lQry->query($lSql);
        $lQuest = $lQry->getAssoc();
        if($lQuest['cnd_id'] != "0" && $lQuest['cnd_id'] != "") {
          $lCnd = $lReg -> loadFromDb($lQuest['cnd_id']);
          $lCnd -> setContext('data', $lJob);
          //Check Item for Condition
          if(!$lCnd->isMet()) {
            continue;
          }
        }
        
        //Check if Question is already answered in target Job
        foreach($lOldQuestions as $lOld) {
          if($lOld["question_item_id"] == $lRow["question_item_id"] && $lOld["status"] == "3") {
            $lSkip = true;
          }
        }
        if($lSkip === true) {
          continue;
        }
        $lQuestQry = "INSERT INTO `al_job_questions_".MID."` (`jobid`, `src`, `question_list_id`, `question_item_id`, `question_type`, `question_en`, `question_de`, `answer`, `datum`, `usr_id`, `status`, `hide`, `size`) VALUES ";
        $lQuestQry .= "(".$aJobId.", '".$aSrc."', ".$lQuest["master_id"].", ".$lQuest["id"].", '".$lQuest["question_type"]."', '".$lQuest["name_en"]."', '".$lQuest["name_de"]."', '".$lRow["answer"]."', '".$lDate."', ".$lUsr.", '1', '0', '".$lQuest["size"]."')";
        $lQuestQry .= " ON DUPLICATE KEY UPDATE `hide` = '0', `answer` = '".$lRow['answer']."', `status` = '1';";
        $lQry->exec($lQuestQry);
      }
    }
  }
  
  protected function hideQuestion($aId) {
    $lQry = "UPDATE `al_job_questions_".MID."` SET `hide`='1' WHERE  `id`=".$aId.";";
    CCor_Qry::exec($lQry);
  }
  protected function showQuestion($aId) {
    $lQry = "UPDATE `al_job_questions_".MID."` SET `hide`='0' WHERE  `id`=".$aId.";";
    CCor_Qry::exec($lQry);
  }
  protected function loadQuestions() {
    $lRet = array();
    $lReg = new CApp_Condition_Registry();
    $lFac = new CJob_Fac($this->mSrc, $this->mJobId);
    $lJob = $lFac -> getDat();

    //Search for matching questions
    $lQry = "SELECT * FROM al_questions_master WHERE active = 1 ORDER BY 'domain'";
    $lQry = new CCor_Qry($lQry);
    foreach($lQry as $lList) {
      if($lList['cnd_id'] != "0") {
        $lCnd = $lReg -> loadFromDb($lList['cnd_id']);
        $lCnd -> setContext('data', $lJob);
        //Check Item List for Condition
        if(!$lCnd->isMet()) {
          continue;
        }
      }

      $lQuest = 'SELECT * FROM al_questions_items WHERE master_id = '.$lList['id'].' AND active = 1 ORDER BY name_'.LAN;
      $lQuest = new CCor_Qry($lQuest);
      foreach($lQuest as $lRowQuest) {
        if($lRowQuest['cnd_id'] != "0") {
          if($lRowQuest['cnd_id'] != "") {
            $lCnd = $lReg -> loadFromDb($lRowQuest['cnd_id']);
            $lCnd -> setContext('data', $lJob);
            //Check Item List for Condition
            if(!$lCnd->isMet()) {
              continue;
            }
          }
        }
        //Check List Item for Condition
        array_push($lRet, $lRowQuest);
      }
    }

    return $lRet;
  }
}