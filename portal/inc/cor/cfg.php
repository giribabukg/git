<?php
/**
 * Core: Main Config File/Object
 *
 * SINGLETON
 * Provides access to configuration parameters
 * like database connection details, mail server
 * and server specific file paths etc.
 * Read-only access for security reasons.
 * Singleton providing either static or non-static
 * access to config values like database credentials,
 * server specific file paths etc.
 *
 * @package COR
 * @copyright  Copyright (c) 5Flow GmbH (http://www.5flow.eu)
 * @version $Rev: 15015 $
 * @date $Date: 2016-07-12 07:48:18 +0200 (Tue, 12 Jul 2016) $
 * @author $Author: gemmans $
 */


/**
 * Main Config Object
 *
 * Singleton providing either static or non-static
 * access to config values like database credentials,
 * server specific file paths etc.
 *
 * @package core
 */

class CCor_Cfg extends CCor_Obj {

  private static $mInstance = NULL;
  private $mVal = array();
#  private $mMandConfig = array();

  private function __construct() {

    if(extension_loaded('xdebug')){
      xdebug_disable();
      //xdebug_enable();
    }
/* 
    if (!defined('CUST_PORTAL')) {
      define('CUST',  							'cust'); // genutzt in fct.php als Verzeichnisname
      define('CUST_PORTAL', 				'projektportal');// Datenbankname, Verzeichnisname
      define('CUSTOMER_NAME', 			'THE QUICK BROWN FOX');
      define('CUSTOMER_NAME_LOGIN', 'THE QUICK BROWN FOX - ARAMIS');
      define('CUSTOMER_PRAEFIX',		'qbf');

    }

    $this -> mVal['cache.backend'] = 'File'; //'Apc';

    $this -> mVal['cust.usr'] = CUST_PORTAL;
    $this -> mVal['cust.pfx'] = CUSTOMER_PRAEFIX;
    $this -> mVal['default.lang'] = LANGUAGE;

    // Kuerzel fuer die Vorlage "NewMandant" und einem "DummyMandant"
    $this -> mVal['cust.NewMand'] = 'new';
    $this -> mVal['cust.DummyMand'] = 'dmy';

    // main database
    $this -> mVal['db.host'] = 'localhost';
    $this -> mVal['db.port'] = '3306';
    $this -> mVal['db.user'] = 'root';
    $this -> mVal['db.pass'] = 'e7fec3b7b50e91c008f07c8780a38170';
    $this -> mVal['db.name'] = 'alink_'.CUSTOMER_PRAEFIX.'_'.CUST_PORTAL;

    // Alink
    $this -> mVal['alink.host'] = '192.168.0.8';
    $this -> mVal['alink.port'] = '49160'; // networker -> ARAMIS !!!
*/

    // to use the mysql_connect client flag parameter, as an example to use SSL connection between wave and the DB.
    // $this -> mVal['db.client_flags'] = MYSQL_CLIENT_SSL; # this an example to set the value to 2048 which force the connection to use SSL
    $this -> mVal['db.client_flags'] = NULL;
    
    // Base encryption string for user passwords
    $this -> mVal['log.magic']  = '3739178e89c1233a600539c106755ff2';
    $this -> mVal['log.master'] = '29a578888d134e71e08f673a28536535';  // == CUST_PORTAL
    $this -> mVal['log.admin']  = 'b0d0a61ad87e6c574df76e2d646d88a7'; // == 'Adminpwd'

    // Logging of messages / events
    $this -> mVal['msg.log.mt'.mtUser]  = mlWarn + mlError + mlFatal;
    $this -> mVal['msg.log.mt'.mtDebug] = mlWarn + mlError + mlFatal;
    $this -> mVal['msg.log.mt'.mtPhp]   = mlWarn + mlError + mlFatal;
    $this -> mVal['msg.log.mt'.mtSql]   = mlError + mlFatal;
    $this -> mVal['msg.log.mt'.mtApi]   = mlWarn + mlError + mlFatal;
    $this -> mVal['msg.log.mt'.mtAdmin] = mlAll;

    // server specifics
#    $this -> mVal['file.dir'] = 'C:/Inetpub/private/'.CUST_PORTAL.'/files/';
#    $this -> mVal['base.url'] = 'http://www.st-packline.de/'.CUST_PORTAL.'/';

    // outgoing mail server
    $this -> mVal['smtp.host'] = '';#SuT:'mail.st-packline.de';
    $this -> mVal['smtp.port'] = 25;
    $this -> mVal['smtp.user'] = '';
    $this -> mVal['smtp.pass'] = '';
    $this -> mVal['smtp.MessageId'] = '@5flow.eu';

    $this -> mVal['smtp.test']  = FALSE;  //if True -> eMail.to = smtp.admin
    $this -> mVal['smtp.admin'] = 'admin@localhost';
    $this -> mVal['xml.test']  = FALSE;  //muss immer FALSE bleiben: if True -> xml-eMails werden NICHT geloescht;

    $this -> mVal['svc.uid']     = 1; //user_id von "Admin QBF", gebraucht in scr/cli.php

    // Networker datenbank fuer mig
    $this -> mVal['db.networker.name'] = 'networker';
    $this -> mVal['db.networker.ip'] = '192.168.0.76';
    $this -> mVal['db.networker.user'] = 'root';
    $this -> mVal['db.networker.pass'] = '';

    $this -> mVal['versioninfo'] = '';

    // Webcenter
    $this -> mVal['wec.host'] = '';
    $this -> mVal['wec.hostext'] = $this -> mVal['wec.host']; // Default If tehe is no Webcenter Extern Host.
    $this -> mVal['wec.useext']  = False;  //genutzt in utl/wec/cnt.php, do we differ in internal and external wec url?
    $this -> mVal['wec.user'] = '';
    $this -> mVal['wec.pass'] = '';
    $this -> mVal['wec.tpl']  = ''; //Webcenter Template Name
    $this -> mVal['wec.grp']  = '';
    $this -> mVal['wec.filestore'] = '';
    $this -> mVal['wec.prjprefix'] = '';
    $this -> mVal['wec.filelist.currentuser'] = FALSE; // if True, get Webcenter Filelist with current User. If False, get with 'wec.user'
    $this -> mVal['wec.testtemplate'] = '';

    // --- Annotationen ueber die WebCenter API lesen oder direkt ueber XFDF Dateien --------------------------------------------
    $this -> mVal['wec.api.annotation'] = False;
    // --- Deutsch ----------------------------------------------------------
    $this -> mVal['wec.annrep.comment'] = 'KOMMENTAR';
    $this -> mVal['wec.annrep.annotation'] = 'ANNOTATION';
    $this -> mVal['wec.annrep.No'] = 'Nr.';
    $this -> mVal['wec.pat.deleted.set'] = 'Gel�scht: ';
    $this -> mVal['wec.pat.deleted.search'] = 'scht von';
    // Annotation types which is taken in portal history.
    // E.g 'text','square','circle','ink','line','highlight'.
    // Webcenter 7 had only 'text' and 'square'
    $this -> mVal['wec.annotation.types'] = array('text','square');

    // --- English ----------------------------------------------------------
    // $this -> mVal['wec.annrep.comment'] = 'COMMENT';
    // $this -> mVal['wec.annrep.annotation'] = 'ANNOTATION';
    // $this -> mVal['wec.annrep.No'] = 'No.';
    // $this -> mVal['wec.pat.deleted.set'] = 'Deleted: ';
    // $this -> mVal['wec.pat.deleted.search'] = 'scht von';
    // -------------------------------------------------------------
	
	//password restriction configuation
	//'on' and 'off' case sensitive 
    $this -> mVal['hom-pwd.conditions'] = array('Length' => '8', 'LowerCase' => 'on','UpperCase' => 'on', 'Digit' => 'on','Special' => 'on');
    // number of old password that are not allowed to be reused, after password change.
    $this -> mVal['password.reuse.not.last'] = 3;
	
    // Memo Feld Rows-Length
    $this -> mVal['show_nr_rows'] = 25;  //Anzahl der Zeilen in textarea Zusatzabsprachen

    // Keywords
    $this -> mVal['job.keyw']     = array('marke','artikel','sorte','gewicht');
    $this -> mVal['job-pro.keyw'] = array('project_no','project_name');

    // Falls es mehrere Jobtypen: Artwork gibt
    $this -> mVal['code_artwork'] = 'art'; // es gibt nur einen "echten" ARTWORK-Workflow

    // Alle Jobformen == src:
    $this -> mVal['all-jobs_ALINK'] = array('art','rep','sec','mis','adm'); //werden in Networker gespeichert
    $this -> mVal['all-jobs_PDB']   = array('com','tra');  //werden in der PortalDatenBank gespeichert
    $this -> mVal['all-jobs'] = array_merge($this -> mVal['all-jobs_ALINK'], $this -> mVal['all-jobs_PDB']); // gebraucht in MID=0

    // Items fuer die Menus "Aktive Jobs", "Archiv", "Projekt Items" .
    $lAllJobs = $this -> mVal['all-jobs'];
    $this -> mVal['menu-projektitems'] = array();
    $this -> mVal['menu-aktivejobs'] = array('job-all');
    if (!empty($lAllJobs)) {
      foreach ($lAllJobs as $lTyp) {
        $this -> mVal['menu-projektitems'][] = 'job_'.$lTyp; // Muss mit '_' definiert werden!
        $this -> mVal['menu-aktivejobs'][] = 'job-'.$lTyp;
      }
    }

    $this -> mVal['menu-archivjobs'] = array('pro');
    $this -> mVal['menu-archivjobs'] = array_merge($this -> mVal['menu-archivjobs'], $this -> mVal['all-jobs']);


    // Anzeige der Projekt-Items=False oder als Jobliste=True
    $this -> mVal['view.projekt.joblist'] = true;
    $this -> mVal['view.projekt.joblist.tree'] = false;

    // Projektfelder, die beim Projektzuordnen im Job geschrieben werden.
    $this -> mVal['job-pro.fields'] = array('project_name','project_no');
    // Auftragsfelder, die beim Projekt in der Subliste (...) in den Links zum Job angezeigt werden.
    $this -> mVal['job-pro.subfields'] = array('artikel','sorte','gewicht','warengruppe'); //Aliase

    // SKU
    $this -> mVal['job-sku.subfields'] = array('artikel', 'sorte', 'gewicht', 'warengruppe'); // alias
    $this -> mVal['job-sku.keyw'] = array('sku_name');

    $lAllJobs = $this -> mVal['all-jobs'];
    $this -> mVal['menu-skuitems'] = array();
    if (!empty($lAllJobs)) {
      foreach ($lAllJobs as $lTyp) {
        $this -> mVal['menu-skutitems'][] = 'job_'.$lTyp;
      }
    }

    // Default Tabs in der Job-Maske:
    $this -> mVal['job.mask.tabs'] = array('job','det'); // job:Identifikation, det: Details
    $this -> mVal['arc.mask.tabs'] = $this -> mVal['job.mask.tabs']; // Archiv: Identifikation, det: Details

    // Default APL-Buttons:
    $this -> mVal['show.form.apl'] = false; // true=View APL-Buttons in Jobmask
    $this -> mVal['buttons.apl'] = array();
    $this -> mVal['buttons.apl'][1] = 'amendment';
    $this -> mVal['buttons.apl'][2] = 'conditional';
    $this -> mVal['buttons.apl'][3] = 'approval';

    // Auftragsfelder im MeineFreigabeliste
    // Jobid ist MUSS.
    // Alle Auftragsfelder in der Liste, muessen auch im al_job_shadow_MID agelegt sein
    // und das Auftragsfeldflag "Reporting" aktiviert damit in der Tabelle "Shadow" gespeichert wird.
    $this -> mVal['job.apl.freigabe'] = array('jobid');

    //user in role of per_prj_verantwortlich is not invited to apl as an active member - per default
    $this -> mVal['apl.noinvite.alias'] = array('per_prj_verantwortlich');
    // all members of the following Groups can be invited to APL additionally to the Group: MANDATOR!
    $this -> mVal['invite.apl'] = array();
    //Invite to APL all of these groups can be invited to the apl on the right side as a whole group
    $this -> mVal['job.apl.parent.invitedgroups'] = array();

    // APL welche Funktionalitaet man will => != oder <
    //true: $lMinPos != $lIds[$lUid]['pos'] zeigt die Buttons nur an, solange man keinen Button bestaetigt hat!
    //false: $lMinPos < $lIds[$lUid]['pos'] zeigt die Buttons, sobald man an der Reihe ist und auch weiter: Korrekturmoegl.
    $this -> mVal['job.apl.show.btn.untilconfirm'] = false;

    // Domain names used in database table al_pck_master:
    // - domain for Color Picker
    $this -> mVal['ColorPickerDom'] = 'col';
    // - domain for Printing machines: Printing Press data connection between Portal and Networker
    $this -> mVal['printing.data.picklist.domain'] = 'prnt';

    // START: quick & dirty: muss hier stehen, kann nicht in der Mandantenconfig stehen fuer die sys/queue (MID=0)!
  	// eMail-Adressumstellung
    // GPM POP3
    $this -> mVal['gpm.pop.host'] = '62.157.152.21';
    $this -> mVal['gpm.pop.port'] = 110;
    $this -> mVal['gpm.pop.user'] = 'customer xml';
    $this -> mVal['gpm.pop.pass'] = 'ST10XML&';

    //Adresse zur Bestaetigung: XML-Auftrag wird ein Job
    $this -> mVal['gpm.returnmail.from'] = 'customer@qbf.de';
    $this -> mVal['gpm.returnmail.to'] = 'edi-bis@qbf.de';
    // ENDE: quick & dirty:

	// My tasks
    // List all job fields that are needed for the deadline management
    $this -> mVal['my.tasks'] =  array();  // filled in mand_...
    $this -> mVal['my.task.past']   = 7;  // default: unfinished apl of the previous [...] days will be shown
    $this -> mVal['my.task.future'] = 7;  // default: unfinished apl of the next [...] days will be shown

    // show critical deadlines: Define the time slot
    $this -> mVal['ddl.past']   = -1; // -1: alle Termine der Vergangenheit | Anzahl der vergangenen Tage
    $this -> mVal['ddl.future'] =  1; // Anzahl der zukuenftigen Tage

    // job bar: view actual (step from -> to)-ddl-dates in 'FROM' OR 'TO'
    $this -> mVal['ddl.view']   = 'FROM';

    // Assets, Intouch Iframe : <iframe id="frame" src="http://assets.matthewsbrandsolutions.co.uk/CumulusE" width="1200" height="800" name="'.lan('hom-cc.menu').'">Currently there is nothing assigned!</iframe>
    $this -> mVal['hom.wel.assets']   = '';

    // Copy Job: Which Aliase sollen beim Kopieren immer geleert werden?
    $this -> mVal['job.cpy.set-empty'] = array();
    $this -> mVal['job.cpy.set-empty'][] = 'jobid';
    $this -> mVal['job.cpy.set-empty'][] = 'webstatus';
    $this -> mVal['job.cpy.set-empty'][] = 'wec_prj_id';

    // Number of email-templates, used without events
    $this -> mVal['tpl.email'] = array();
    $this -> mVal['tpl.email']['pwd'] = 1; //Passwort vergessen/forget
    $this -> mVal['tpl.email']['apl'] = 0; //allg. APL-mail: not_used=0
    $this -> mVal['tpl.email.apl.deleted.user'] = ''; // Mail to deleted APL user.
    
    $this -> mVal['login.password.expiry.active'] = FALSE;
    $this -> mVal['password.expire.days'] = 90; // 3 Month
    $this -> mVal['group.expiryDates'] = array('-1'=>'Disabled', 30=>'1 Month', 90=>'3 Months', 180=>'6 Months');
    // ???? Important need to change the templete ID
    $this -> mVal['tpl.email']['remainderpass'] = 290; //send email to users for changing password 
    $this -> mVal['tpl.email']['extusrtoken'] = 291; //External User Email Confirmation
    $this -> mVal['tpl.email']['ext.usr.pwd'] = 292; // send password and username to external user for first time
    $this -> mVal['tpl.email']['pwd.fgt.activate'] = 293; //password activation if he changed by himself
    $this -> mVal['tpl.email']['ext.usr.req.admin'] = 294; //admin user notified by external user for account activation
    //Prefix for auto generated conditions for external groups
    $this -> mVal['Ext.Group.Prefix'] = 'ext_';
	
    // MyTasks - Configure addColumn - Reihenfolge wichtig
    $this -> mVal['hom.wel.mytask.column']   = array();
    //Example: $this -> mVal['hom.wel.mytask.column'] = ('jobnr','project_name','artikel','start_date');
    $this -> mVal['hom.wel.mytask.flag.column'] = array();
    //Example: $this -> mVal['hom.wel.mytask.flag.column'] = array('jobnr','project_name','webstatus','start_date');
    $this -> mVal['hom.wel.mytask.url'] = 'apl';// possible one of {'job','det','his','apl','flag','fil'}
    $this -> mVal['hom.wel.mytask.flag.url'] = 'form';// possible one of {'job','det','his','apl','flag','fil'}

    // #23192 :Status Change request in My Task List
    $this -> mVal['hom.wel.mytask.role.column'] = array('jobnr','stichw','webstatus');
    $this -> mVal['hom.wel.mytask.role.url'] = 'job';// possible one of {'job','det','his','apl','flag','fil'}

    //CRP Flags in Joblists
    $this -> mVal['show.flawithflags'] = TRUE;
    $this -> mVal['show.ddlwithflags'] = TRUE;
    $this -> mVal['show.flagswithddl'] = TRUE;

    // Reporting: Standard-Start-Class
    $this -> mVal['rep.std-class'] = 'main';

    //Dashboard unter Home (hom-fla)
    $this -> mVal['hom.wel.dashboard'] = FALSE;

    //Delete Project Possible?
    $this -> mVal['job-pro.del'] = TRUE;

    //Extended Webstatus Filter
    $this -> mVal['extended.webstatus.filter'] = False; // If True, show Webstatus Filter with checkboxes
    // Var 'extended.webstatus.filter.maxcols' is not be used after JobId:22824
    // Webstatuslist is showed in Popupmenue.
    //$this -> mVal['extended.webstatus.filter.maxcols'] = 3; // Coloumn count to show extended Webstatus Filter

    //Highlight in the Joblist for whole Line?
    $this -> mVal['job.list.highlight.line'] = TRUE; // if True : Highlight all Line, if False:Highlight only DDL coloumn

    // Aliasname of special condition-function for supplier, first used by mand_1003
    $this -> mVal['cond.supplier'] = array();
    #$this -> mVal['cond.supplier']['pro'] = 'project_supplier';
    #$this -> mVal['cond.supplier']['job'] = 'supplier';

    #error_reporting(0);// nur wenn Notices nicht abgefangen werden koennen.

    // Time Out on Second
    // 3600 to set the expiration date to one hour
    // NULL to define no time out.

    #$this -> mVal['session_time.out'] = 108000 ; // 30Min.
    $this -> mVal['session_time.out'] = NULL;

    // JobId #23041 Master and Variant definition
    // On-off switch for all Master-Variant Bundle Functionality
    $this -> mVal['master.varaiant.bundle'] = FALSE;

    // JobId: #23229
    // Related menu to show projects, skus and jobs in one overview
    $this -> mVal['job-rel.projects'] = TRUE;
    $this -> mVal['job-rel.projects.fields']= Array();
    $this -> mVal['job-rel.projects.link'] = 'sub';

    $this -> mVal['job-rel.skus'] = FALSE;
    $this -> mVal['job-rel.skus.fields']= Array();
    $this -> mVal['job-rel.skus.link'] = 'sub';

    $this -> mVal['job-rel.jobs'] = TRUE;
    $this -> mVal['job-rel.jobs.fields']= Array();
    $this -> mVal['job-rel.jobs.link'] = 'job';

    $this -> mVal['job.writer.default'] = 'alink';
    #$this -> mVal['job.writer.default'] = 'mop';
    
    // TP ID #751, #6661
    // RabbitMQ for asynch messaging between WAVE instances
    $this -> mVal['rabbit.available'] = false;
    $this -> mVal['rabbit.host'] = 'rabbit.5flow.net';
    $this -> mVal['rabbit.port'] =  8080;

    // TP ID #751
    // Core | Create Jobs in Core 
    $this -> mVal['core.available'] = false;
    $this -> mVal['core.user'] = 'INT_WAVE_PD1';
    $this -> mVal['core.pass'] = 'Sch@Wk2Pd!';

    /**
       * JobId: 23398
       * Define a rout cause for amendments
       * Additional selection field with a helptable behind, to define route cause.
       * Combination of Field and Helptable Dom.
       */
    $this -> mVal['apl.amendment.causes'] = Array('apl_amendment_cause_1' => '',
    											  'apl_amendment_cause_2' => '',
    										      'apl_amendment_cause_3' => '');

    //User and Group Field Tooltips
    $this -> mVal['show.user.details'] = FALSE;
    $this -> mVal['show.group.details'] = FALSE;

    //Xchange JobId update on transition
    $this -> mVal['xchange.available'] = FALSE;
    $this -> mVal['xchange.in'] = '';
    $this -> mVal['xchange.error'] = '';
    $this -> mVal['xchange.parsed'] = '';
    
    $this -> mVal['theme.choice'] = 'default';
    $this -> mVal['theme.colours'] = Array('all' => 'blue', 
                                           'adm' => 'red',
                                           'art' => 'orange',
                                           'com' => 'purple', 
                                           'mis' => 'cyan',
                                           'pro' => 'purple',
                                           'rep' => 'red',
                                           'sec' => 'orange',
                                           'ser' => '',
                                           'sku' => 'cyan',
                                           'tra' => 'green');

    $this -> mVal['status.display'] = 'progressbar';
    $this -> mVal['report.jobs'] = array('art','rep','sec','mis','adm','com','tra');
    $this -> mVal['masterlanguage'] = 'EN';
    $this -> mVal['mass.file.extension'] = array('xlsx','csv','xls','xml');
    
    // Amount of the duration days, till the deep download link expire.
    $this -> mVal['downloadlink.duration'] = 100;
    
    // upload max file size.
    $this -> mVal['max.file.size'] = ini_get("upload_max_filesize");

    //Yellow Status for Job Questions
    $this -> mVal['questions-yellow'] = false;

    $this -> mVal['job-cms.fields'] = array(
      'client_key' => 'client_key',
      'languages' => 'languages',
      'template_name' => 'phrase_template_name',
      'packtype' => 'compo'
    );
    
    //Blocked Email Domains for External User Management
    $this->mVal['blacklisted_domains'] = array(
        'gmail.com',
        'googlemail.de',
        'googlemail.com',
        'gmail.de',
        'gmx.de');

    //New Ajax driven Group List
    $this -> mVal["ajxGroupList"] = false;
    
    ob_start();
    if(file_exists(CUST_PATH.'inc/cor/cfg.php')) include(CUST_PATH.'inc/cor/cfg.php');
    ob_end_clean();

   # if(file_exists('inc/cor/cfg-local.php')) include('inc/cor/cfg-local.php');

#echo '<pre>---src/inc/cor/cfg.php---'.CUST_ID;var_dump($this -> mVal,'#############');echo '</pre>';

  }

  private final function __clone() {}

  /**
   * Singleton getInstance method
   *
   * @return CCor_Cfg
  **/
  public static function getInstance(){
    if (NULL === self::$mInstance) {
      self::$mInstance = new self();
    }
    return self::$mInstance;
  }

  /**
   * Get a configuration variable
   *
   * @param string $aKey Unique name (e.g. 'db.host') of config var
   * @param mixed $aStd Default value to return if config var does not exist
   * @return mixed Value of the config variable or null if key is not set
  **/
  public function getVal($aKey, $aStd = NULL) {
    return (isset($this -> mVal[$aKey])) ? $this -> mVal[$aKey] : $aStd;
  }

  /**
  * Get the configuration value based on a key
  *
  * @param string $aKey Unique name (e.g. 'db.host') of config var
  * @return mixed Value of the config variable or null if key is not set
  **/
  public static function get($aKey, $aStd = NULL) {
    $lCfg = self::getInstance();
    return $lCfg -> getVal($aKey, $aStd);
  }

  /**
   * Get a configuration value for a key, fallback if key is not set
   *
   * @param string $aKey Unique name (e.g. 'art.show.files') of config var
   * @param string $aFallback Unique name (e.g. 'show.files') of fallback var
   * @param mixed $aStd Default value if neither is set
   * @return mixed Value of the config variable or null if key is not set
   **/
  public static function getFallback($aKey, $aFallback, $aStd = NULL) {
    $lCfg = self::getInstance();
    $lRet = $lCfg -> getVal($aKey, null);
    if (!is_null($lRet)) return $lRet;

    $lRet = $lCfg -> getVal($aFallback, null);
    return (is_null($lRet)) ? $aStd : $lRet;
  }

  /**
   * Set a configuration variable
   *
   * @param string $aKey Unique name (e.g. 'db.host') of config var
   * @param mixed $aVal Default value to return if config var does not exist
  **/
  public function setVal($aKey, $aVal) {
    $this -> mVal[$aKey] = $aVal;
  }

  /**
   * Set the configuration value based on a key
   *
   * @param string $aKey Unique name (e.g. 'db.host') of config var
  **/
  public static function set($aKey, $aVal) {
    $lCfg = self::getInstance();
    $lCfg -> setVal($aKey, $aVal);
  }

  public function getValues() {
    return $this->mVal;
  }

}