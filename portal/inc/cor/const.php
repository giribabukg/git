<?php
// Special characters and entities
define('HT', "\t");
define('LF', "\n");
define('CR', "\r");
define('BR', '<br />');
define('NB', '&nbsp;');
define('TR', '<tr>'.LF);
define('_TR', '</tr>'.LF);

define('ACTION_QUEUE', 9); // Number/Id of Service "Action Queue" in al_sys_svc

// Date
define('SECS_PER_DAY', 86400);

// Memory Sizes
define('MEM_KB', 1024);
define('MEM_MB', 1048576);
define('MEM_GB', 1073741824);

// Service Flags
define('sfActive',   1);

// Right definitions
define('rdNone',   0);
define('rdRead',   1);
define('rdEdit',   2);
define('rdIns',    4);
define('rdDel',    8);
define('rdAll',    15);

// Message types
define('mtNone',  0);
define('mtUser',  1);
define('mtDebug', 2);
define('mtPhp',   4);
define('mtSql',   8);
define('mtApi',   16);
define('mtAdmin', 32);
define('mtAll',   63);

// Message levels
define('mlNone',  0);
define('mlInfo',  1);
define('mlWarn',  2);
define('mlError', 4);
define('mlFatal', 8);
define('mlAll',   15);
define('mlNoSending', 16);
define('mlWaiting', 32);

// Message order
define('moLevel', 1);
define('moType',  2);
define('moTime',  3);

// Email states
define('msOkay',   1);
define('msQueued', 2);
define('msError',  4);

// Email Types
define('mailSys', 1);
define('mailJobEvents', 2);
define('mailAplInvite', 3);
define('mailAplReminder', 4);
define('mailJobNotification', 5);
define('mailUnread', 6);
define('mailRead', 7);
define('mailinactive', 8);
define('mailInformation', 9);

// Email Status
define('mailNew', 0);

// Email Active
define('mailActive', 0);
define('mailInactive', 1);


// Api Message errors
define('aeOkay',   0);
define('aeQueue',  1);

define('jfSrc', 'pro,art,rep,sec,mis,com,tra,sku');

define('fsPro', 1);
define('fsArt', 2);
define('fsRep', 4);
define('fsSec', 8);
define('fsAdm', 16);
define('fsMis', 32);
define('fsCom', 64);
define('fsTra', 128);
define('fsSub', 1024);
define('fsSku', 2048);

// Job Field Flags
define('ffRead',     1);   // read priv needed
define('ffEdit',     2);   // edit priv needed
define('ffList',     4);   // can be added to list
define('ffInput',    8);   // can be an input field
define('ffSearch',   16);  // can be a search field
define('ffSort',     32);  // can be sorted in a list
define('ffDefault',  63); // default flags for new fields
define('ffProtocol', 64);  // changes will be logged / added to history / reminder list
define('ffReport',   128); // is available in reports
define('ffCopy',     256); // field will be copied
define('ffMandatory',512); // field must be filled
define('ffMetadata', 1024); // field used for metadata
define('ffOnchange',  2048); // on changes if it is setted in the workflow to trigger event, the changes will be sent via email

// Form Field States
define('fsStandard', 0);
define('fsDisabled', 1);
define('fsInvalid',  2);
define('fsSearch',   4);
define('fsPrint',    8);

// Job History Types
define('htStatus',      1);
define('htComment',     2);
define('htMail',        3);
define('htChkOk',       4);
define('htChkReject',   5);
define('htEdit',        6);
define('htAplOk',       7);
define('htAplNok',      8);
define('htAplCond',     9);
define('htFlags',      10); //incl. 11 und 12!
// 11 : Activate Flag + Dectivate Flag
// 12 : flag.confirm
define('htReuse',      13);// war 11 -> falsch
define('htFileupload', 14);
#define('htFieldChange',15);//used in standardportal
define('htAplBackToGroup',     16);
define('htFiledelete', 17);
define('htAplUsrAdd', 18);
define('htAplUsrdelete', 19);
define('htWecComment',  1024);
define('htAnsweredQuestion',  2048);

// Webcenter APL status
define('wecAplOk',   'Genehmigt');
define('wecAplNok',  'Abgelehnt');
define('wecAplCond', 'Ausstehend');

// Job Flags
define('jfOnhold',        1);
define('jfCancelled',     2);
define('jfPrinter',       1024);
define('jfColorApproved', 2048);
define('jfPdfApproved',   4096);
define('jfDonotprint',    8192);
define('jfNetworking',    16384);
define('jfAll',           32767);

// CRP Step Flags
define('sfComment'    , 1);
define('sfCommentSkip', 2);
define('sfAmend',       4);
define('sfAmendDecide', 8);
define('sfUploadFile',  16);
define('sfStartApl',    32);
define('sfCloseApl',    64);
define('sfSelectAnnots',128);
define('sfSignature',   256);
define('sfAutomatic',   512);
define('sfJobQuestions',1024);

// CRP Flags
define('flagComment',      1);
define('flagCommentSkip',  2);
define('flagMandatory',    4);
define('flagBtnDisplay1',  8);
define('flagUploadFile',  16);
define('flagBtnAmend',    32);
define('flagBtnCondit',   64);
define('flagBtnApprov',  128);
define('flagJsAnyConf',256);

define('FLAG_STATE_ACTIVATE', 1);
define('FLAG_STATE_CONFIRMED', 2);
define('FLAG_STATE_CLOSED', 4);

define('FLAG_TYP' , 'XXX_FLAG_TYP_XXX');// wird genutzt als Alias! & in mytask: protected function getTdXXX_FLAG_TYP_XXX()
define('FLAG_STATE_AMENDMENT', 1);
define('FLAG_STATE_CONDITIONAL', 2);
define('FLAG_STATE_APPROVED', 3);

// CRP Status Flags
define('staflaColorApproval', 2);
define('staflaPdfApproval',   4);
define('staflaAddContent',   32);
define('staflaHariboStandard',   8192);

// System mail send Flags
define('smNoMail'     , 0);
define('smOncePerDay' , 1);
define('smEveryTime'  , 2);

// Todo Flags
define('tfActive',    1);
define('tfTodoList',  2);
define('tfInJobForm', 4);
define('tfDone',      8);
define('tfDefault',   7);

// Approval Types Flags
define('atInviteOnce', 1);
define('atAhead', 2);
define('atAfter', 4);
define('atChangeState', 8);
define('atButtonsAhead', 16);
define('atSubLoop', 32);
define('atAction', 64);

// Flags
define('flEve_act', 1);
define('flEve_conf', 2);

define('STATUS_DRAFT',     10);
define('STATUS_ARCHIV',   200);
define('STATUS_MonitorTest', 255);

define('MAX_SEQUENCE',  1024);// needed in sequentiell Apl

define('BLOCKSIZE_MIG',   100);  // Anzahl der gleichzeitig zu archivierenden Jobs

define('SSO_TOKENNAME', 'token');
define('DOWN_TOKENNAME', 'download');
define('sec_token', 'sec_token');
define('EVENT_DEFER_POSITION', 100);
