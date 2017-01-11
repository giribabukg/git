<?php
class CInc_App_Event_Action_Registry extends CCor_Obj {

  protected $mReg = array();

  public function __construct() {
    // Alink
    $this -> addAction('alink_addtimesheet', 'MIS Create Timesheet Record', 'CApp_Event_Action_Alink_Addtimesheet');
    $this -> addAction('alink_callevent',    'MIS Call Event',              'CApp_Event_Action_Alink_Callevent');
    $this -> addAction('alink_insertsubjob', 'MIS Create Subjob',           'CApp_Event_Action_Alink_Insertsubjob');

    // Copy
    $this -> addAction('copy_file', 'Copy File', 'CApp_Event_Action_Copy_File');
    $this -> addAction('copy_job',  'Copy Job',  'CApp_Event_Action_Copy_Job');
    $this -> addAction('copy_task', 'Copy Task', 'CApp_Event_Action_Copy_Task');

    // Dalim
    $this -> addAction('dalim_approveversion', 'Dalim Approve Version', 'CApp_Event_Action_Dalim_Approveversion');
    $this -> addAction('dalim_lockversion',    'Dalim Lock Version',    'CApp_Event_Action_Dalim_Lockversion');
    $this -> addAction('dalim_unlockversion',  'Dalim Unlock Version',  'CApp_Event_Action_Dalim_Unlockversion');
    $this -> addAction('dalim_documentaction', 'Dalim Document Action', 'CApp_Event_Action_Dalim_DocumentAction');

    //dms
    $this -> addAction('dms_copytodalim', 'DMS: Copy File to Dalim', 'CApp_Event_Action_Dms_Copytodalim');

    // Email
    $this -> addAction('email_apl',       'Email to APL',           'CApp_Event_Action_Email_Apl');
    $this -> addAction('email_deferred',  'Send Deferred Emails',   'CApp_Event_Action_Email_Deferred');
//     $this -> addAction('email_gpm',       'Email to GPM',           'CApp_Event_Action_Email_Gpm');
    $this -> addAction('email_gru',       'Email to Group',         'CApp_Event_Action_Email_Group');
    $this -> addAction('email_gruasrole', 'Email to Group as Role', 'CApp_Event_Action_Email_Groupasrole');
    $this -> addAction('email_rol',       'Email to Role',          'CApp_Event_Action_Email_Role');
    $this -> addAction('email_usr',       'Email to User',          'CApp_Event_Action_Email_User');

    // Field
    $this -> addAction('field_dec',        'Decrease Job Value',             'CApp_Event_Action_Field_Dec');
    $this -> addAction('field_inc',        'Increase Job Value',             'CApp_Event_Action_Field_Inc');
    $this -> addAction('field_onchildren', 'Set Job Value in Children Jobs', 'CApp_Event_Action_Field_Onchildren');
    $this -> addAction('field_onparent',   'Set Job Value in Parent Jobs',   'CApp_Event_Action_Field_Onparent');
    $this -> addAction('field_set',        'Set Job Value',                  'CApp_Event_Action_Field_Set');

    // Flags
//     $this -> addAction('flag_set',    'Set Job Flag',    'CApp_Event_Action_Flag_Set');
//     $this -> addAction('flag_toggle', 'Toggle Job Flag', 'CApp_Event_Action_Flag_Toggle');
//     $this -> addAction('flag_unset',  'Clear Job Flag',  'CApp_Event_Action_Flag_Unset');

    // Others
    $this -> addAction('job_archive',  'Archive jobs',             'CApp_Event_Action_Job_Archive');
    $this -> addAction('add_js2btn',   'JavaScript to StepButton', 'CApp_Event_Action_Js_Btn');
    $this -> addAction('move_jobtype', 'Move Job Type',            'CApp_Event_Action_Move_Jobtype');
    $this -> addAction('insert',       'Insert New Reporting Row', 'CApp_Event_Action_Newreportrow_Insert');
    $this -> addAction('select_event', 'Select Event',             'CApp_Event_Action_Select_Event');

    // Wec
    $this -> addAction('wec_createproject',     'WebCenter Create Project',      'CApp_Event_Action_Wec_CreateProject'); // CreateProject.jsp: Creating a new project
    $this -> addAction('wec_documentaction',    'WebCenter Document Action',     'CApp_Event_Action_Wec_DocumentAction'); // DocumentAction.jsp: Document Action
    $this -> addAction('wec_createprojecttask', 'WebCenter Create Project Task', 'CApp_Event_Action_Wec_CreateProjectTask'); // CreateProjectTask.jsp: Creating a new project task
    $this -> addAction('wec_updateprojecttask', 'WebCenter Update Project Task', 'CApp_Event_Action_Wec_UpdateProjectTask'); // UpdateProjectTask.jsp: Updating a project task

    // Xchange export
    $this -> addAction('xchange_hotfolder', 'Xchange: Create hotfolder file', 'CApp_Event_Action_Xchange_Hotfolder');
    $this -> addAction('xchange_alink_insertjob', 'Xchange: Alink InsertJob', 'CApp_Event_Action_Xchange_Alink_Insertjob');
    $this -> addAction('xchange_alink_updatejob', 'Xchange: Alink UpdateJob', 'CApp_Event_Action_Xchange_Alink_Updatejob');
    $this -> addAction('xchange_alink_pulljob', 'Xchange: Alink PullJob', 'CApp_Event_Action_Xchange_Alink_Pulljob');

    if (CCor_Cfg::get('core.available')) {
      $this->addAction('core_insertjob', 'Core: Create SalesOrder', 'CApp_Event_Action_Core_Insertjob');
      $this->addAction('core_pulljob', 'Core: PullJob', 'CApp_Event_Action_Core_Pulljob');
    }

    //Phrase
    $this -> addAction('phrase_export', 'Phrase: Export Content', 'CApp_Event_Action_Phrase_Export');
  }

  protected function & addAction($aType, $aName, $aClass) {
    $lAct = new CCor_Dat();
    $lAct['type']  = $aType;
    $lAct['name']  = $aName;
    $lAct['class'] = $aClass;
    $this -> mReg[$aType] = & $lAct;
    return $lAct;
  }

  public function getActions() {
    return $this -> mReg;
  }

  public function & getAction($aKey) {
    $lRet = NULL;
    if ($this -> isValid($aKey)) {
      $lRet = & $this -> mReg[$aKey];
    }
    return $lRet;
  }

  public function isValid($aType) {
    if (!isset($this -> mReg[$aType])) {
      $this -> dbg('Unknown Type '.$aType,mlWarn);
    }
    return isset($this -> mReg[$aType]);
  }

  public function getName($aType) {
    $lAct = & $this -> getAction($aType);
    if (NULL == $lAct) {
      return '[unknown type '.$aType.']';
    } else {
      return $lAct['name'];
    }
  }

  public function paramToString($aType, $aParams) {
    if (empty($aParams)) {
      return '';
    }
    $lAct = $this -> getAction($aType);
    if (NULL == $lAct) {
      return '';
    }
    $lCls = $lAct['class'];
    // ruft $lCls::paramToString($aParams) auf
    $lRet = call_user_func_array(array($lCls, 'paramToString'), array($aParams));
    return $lRet;
  }

  public function getParamDetails($aType, $aParams) {
    if (empty($aParams)) {
      return '';
    }
    $lAct = $this -> getAction($aType);
    if (NULL == $lAct) {
      return '';
    }
    $lCls = $lAct['class'];
    $lRet = call_user_func_array(array($lCls, 'getParamDetails'), array($aParams));
    $this -> dump($lRet, 'RET');
    return $lRet;
  }

  public function getParamDefs($aType) {
    $lAct = $this -> getAction($aType);
    if (NULL == $lAct) {
      return array();
    }
    $lCls = $lAct['class'];
    $lTyp = $lAct['type'];
    $lRet = call_user_func_array(array($lCls, 'getParamDefs'), array($aType));
    return $lRet;
  }

  /**
   * Create a CApp_Event_Action object according to type, passing context and parameters
   *
   * @return CApp_Event_Action
   */
  public function factory($aType, $aContext, $aParams) {
    $lAct = $this -> getAction($aType);
    if (NULL == $lAct) {
      return NULL;
    }
    $lCls = $lAct['class'];
    $lRet = new $lCls($aContext, $aParams);
    return $lRet;
  }
}
