<?php
/**
 * Main Customer Config File
 *
 * Provides access to configuration parameters
 * like database connection details, mail server
 * and server specific file paths etc.
 * Read-only access for security reasons.
 *
 * @author Geoffrey Emmans <g.emmans@5flow.eu>
 * @package mand/core
 * @copyright  Copyright (c) 5Flow GmbH (http://www.5flow.eu)
 * @version $Rev: 14049 $
 * @date $Date: 2016-05-23 17:41:21 +0800 (Mon, 23 May 2016) $
 * @author $Author: jschneider $
 */

/**
 * Main Config Object
 *
 * Singleton providing either static or non-static
 * access to config values like database credentials,
 * server specific file paths etc.
 *
 * @package mand/core
 */

    // General
    #    CCor_Cfg::set('mand.key', MANDATOR);

    #define('CUST', 								'cust'); // genutzt in fct.php als Verzeichnisname
    define('CUST_PORTAL', 				'portal');// Datenbankname, Verzeichnisname
    define('CUSTOMER_NAME', 			'core');
    define('CUSTOMER_NAME_LOGIN', 'Matthews International');
    define('CUSTOMER_PRAEFIX', 		'core');

    $this -> mVal['cache.backend'] = 'File';

    $this -> mVal['cust.usr'] = CUST_PORTAL;
    $this -> mVal['cust.pfx'] = CUSTOMER_PRAEFIX;
    $this -> mVal['default.lang'] = 'en';

    // Kuerzel fuer die Vorlage "NewMandant" und einem "DummyMandant"
    $this -> mVal['cust.NewMand'] = 'new';
    $this -> mVal['cust.DummyMand'] = 'dmy';

    // main database
    $this -> mVal['db.host'] = 'localhost';
    $this -> mVal['db.port'] = '3306';
    $this -> mVal['db.user'] = 'root';
    $this -> mVal['db.pass'] = 'root';
    $this -> mVal['db.name'] = 'wave';

    // Alink
    $this -> mVal['alink.host'] = '127.0.0.1';
    $this -> mVal['alink.port'] = '49160'; // networker

    // Base encryption string for user passwords
    $this -> mVal['log.magic']  = '3739178e89c1233a600539c106755ff2';
    $this -> mVal['log.master'] = '52df4f1da08b6437eaf094e9a2dfbe65'; //fachpack
    $this -> mVal['log.admin']  = '475bfa90433a5bc20d42aa7f82f4ec33'; //globalwave
    //$this -> mVal['log.master'] = 'e809dc48fec77780285c17c8a0c322f4'; // == CUST_PORTAL

    // Logging of messages / events
    $this -> mVal['msg.log.mt'.mtUser]  = mlWarn + mlError + mlFatal;
    $this -> mVal['msg.log.mt'.mtDebug] = mlWarn + mlError + mlFatal;
    $this -> mVal['msg.log.mt'.mtPhp]   = mlWarn + mlError + mlFatal;
    $this -> mVal['msg.log.mt'.mtSql]   = mlError + mlFatal;
    $this -> mVal['msg.log.mt'.mtApi]   = mlWarn + mlError + mlFatal;
    $this -> mVal['msg.log.mt'.mtAdmin] = mlAll;

    // server specifics
    $this -> mVal['file.dir'] = '/home/doc/';
    $this -> mVal['base.url'] = 'http://10.140.200.11/portal/';

    // Services
    $this -> mVal['svc.url']  = 'http://10.140.200.11/'.CUST_PORTAL.DS; //'http://www.st-packline.de/'.CUST_PORTAL.DS; 217.7.140.35 alte IP Adresse
    // Errorlog for the LIVE-"Cronjob/Service"
    $this -> mVal['svc.error_log'] = '/home/www-data/mars/tmp/logs/svc_error_log.txt';
    $this -> mVal['svc.error_log.shadow'] = '/home/www-data/mars/tmp/logs/svc_error_log2.txt';
    // services log
    $this -> mVal['svc.dir'] = '/home/doc/tmp/logs/'; // directory where the logs are stored, default ''
    $this -> mVal['svc.filename'] = 'services_'; // filename, default 'services_'
    $this -> mVal['svc.fileadd'] = 'Y.m'; // fileadditon, default 'Y.m' (for year and month, see http://de2.php.net/manual/de/function.date.php for format)
    $this -> mVal['svc.fileext'] = 'txt'; // fileextention, default 'txt'

    // outgoing mail server
    $this -> mVal['smtp.host'] = '192.168.201.13';
    $this -> mVal['smtp.port'] = 25;
    $this -> mVal['smtp.user'] = 'wave@5flow.net';
    $this -> mVal['smtp.pass'] = '6G$nEU3hdK';
    $this -> mVal['smtp.MessageId'] = '@5flow.net';

    $this -> mVal['smtp.test']  = false;  //if True -> eMail.to = smtp.admin
    $this -> mVal['smtp.sendAs']  = 'wave@5flow.net';
    $this -> mVal['smtp.admin'] = 'wave@5flow.net';

    $this -> mVal['svc.uid']     = 1; //user_id von Emmans, gebraucht in scr/cli.php

    // Networker datenbank furr mig
    $this -> mVal['db.networker.name'] = 'networker';
    $this -> mVal['db.networker.ip'] = '192.168.0.76';
    $this -> mVal['db.networker.user'] = 'root';
    $this -> mVal['db.networker.pass'] = '';

    $this -> mVal['versioninfo'] = '';

    // Webcenter
    $this -> mVal['wec.host'] = 'http://webcenter.5flow.net/WebCenter_Inst/';
    $this -> mVal['wec.hostext'] = $this -> mVal['wec.host']; // Default If tehe is no Webcenter Extern Host.
    $this -> mVal['wec.useext']  = false;  //genutzt in utl/wec/cnt.php, do we differ in internal and external wec url?
    $this -> mVal['wec.user'] = 'admin';
    $this -> mVal['wec.pass'] = 'JZfd2ough1!';
    $this -> mVal['wec.filestore'] = '/FileStore/views/'; // NEU, ab 13.07.2011 san

    $this -> mVal['wec.tpl']  = 'Mars';      //Griesson 1131';
    $this -> mVal['wec.grp']  = 'MITGLIEDER_';  //.MANDATOR_NAME;
    $this -> mVal['wec.prjprefix'] = '';
    #$this -> mVal['wec.testtemplate'] = 'Testvorlage'; // SuT benutzt kein Test Vorlage mehr.
    $this -> mVal['wec.annotation.master'] = 'per_prj_verantwortlich'; // per_prj_verantwortlich
    $this -> mVal['wec.filelist.currentuser'] = FALSE; // if True, get Webcenter Filelist with current User. If False, get with 'wec.user'
    $this -> mVal['wec.ignore.folders'] = 'intern'; // The daten under this ordner are ignored.
    $this -> mVal['wec.defaulttemplate'] = 'Mars';
    $this -> mVal['wec.version'] = 10;
    $this -> mVal['wec.view'] = ''; // use either a new 'tab' or 'window' for WebCenter, '' uses the current tab for WebCenter
    $this -> mVal['wec.view.close'] = 'return'; // use either 'return' to go back to the job when closing WebCenter or use '' to close the window

    $this -> mVal['wec.utf8'] = true; // use urlencode(utf8_encode([...]))
    // Annotation types which is taken in portal history.
    // E.g 'text','square','circle','ink','line','highlight'.
    // Webcenter 7 had only 'text' and 'square'
    $this -> mVal['wec.annotation.types'] = array('text','square','circle','ink','line','highlight');
    $this -> mVal['wec.annrep.comment'] = 'COMMENTS';

    // WebCenter Thumbnail Storage
    $this -> mVal['wec.tns.active'] = TRUE; // TRUE uses WebCenter Thumbnail Storage, FALSE behaves like before
    $this -> mVal['wec.tns.path'] = 'tmp/'; // directory in which the thumbnails are stored
    $this -> mVal['wec.tns.limit'] = 20; // number of datasets to be handled per cycle

    // Memo Feld Rows-Length
    $this -> mVal['show_nr_rows'] = 25;  //Anzahl der Zeilen in textarea Zusatzabsprachen

    // Keywords
    $this -> mVal['job.keyw']     = array('marke','artikel','sorte','gewicht');
    $this -> mVal['job-pro.keyw'] = array('project_no','project_name');

    // Falls es mehrere Jobtypen: Artwork gibt
    $this -> mVal['code_artwork'] = 'art'; // es gibt nur einen "echten" ARTWORK-Workflow

    // Alle Jobformen == src:
    $this -> mVal['all-jobs_ALINK'] = array('art','rep','sec','mis','adm','com','tra'); //werden in Networker gespeichert
    $this -> mVal['all-jobs_PDB']   = array();  //werden in der PortalDatenBank gespeichert
    $this -> mVal['all-jobs'] = array_merge($this -> mVal['all-jobs_ALINK'], $this -> mVal['all-jobs_PDB']); // gebraucht in MID=0

    // in welcher Reihenfolge werden Jobs generiert wichtig unter Projekte
    $this -> mVal['job.items.order']   = array();
    $this -> mVal['job.items.order'][] = 'art';
    $this -> mVal['job.items.order'][] = 'rep';
    $this -> mVal['job.items.order'][] = 'sec';
    $this -> mVal['job.items.order'][] = 'mis';
    $this -> mVal['job.items.order'][] = 'adm';
    $this -> mVal['job.items.order'][] = 'com';
    $this -> mVal['job.items.order'][] = 'tra';

    // Items fuer die Menus "Aktive Jobs", "Archiv", "Projekt Items" .
    $this -> mVal['menu-aktivejobs'] = array('job-all','job-art','job-rep','job-sec','job-mis','job-adm','job-com','job-tra');
    $this -> mVal['menu-archivjobs'] = array('pro','art','rep','sec','mis','adm','com','tra');
    $this -> mVal['menu-projektitems'] = array('job_art','job_rep','job_sec','job_adm','job_mis','job_com','job_tra');

    // Anzeige der Projekt-Items wie bei S+T=False oder wie bei Intouch=True als Jobliste
    $this -> mVal['view.projekt.joblist'] = false;

    // Projektfelder, die beim Projektzuordnen im Job geschrieben werden.
    $this -> mVal['job-pro.fields'] = array('project_name','project_no');
    // Auftragsfelder, die beim Projekt in der Subliste (...) in den Links zum Job angezeigt werden.
    $this -> mVal['job-pro.subfields'] = array('artikel','sorte','gewicht','warengruppe'); //Aliase

    // SKU
    $this -> mVal['job-sku.subfields'] = array('artikel','sorte','gewicht','warengruppe'); //Aliase
    $this -> mVal['job-sku.keyw'] = array('sku_name');

    // Default Tabs in der Job-Maske:
    $this -> mVal['job.mask.tabs'] = array('job','det'); // job:Identifikation, det: Details

    // Default APL-Buttons:
    $this -> mVal['show.form.apl'] = false; // true=View APL-Buttons in Jobmask
    $this -> mVal['buttons.apl'] = array();
    $this -> mVal['buttons.apl'][1] = 'amendment';
    //$this -> mVal['buttons.apl'][2] = 'conditional';
    $this -> mVal['buttons.apl'][3] = 'approval';

    // Auftragsfelder im MeineFreigabeliste
    // Jobid ist MUSS.
    // Alle Auftragsfelder in der Liste, muessen auch im al_job_shadow_MID agelegt sein
    // und das Auftragsfeldflag "Reporting" aktiviert damit in der Tabelle "Shadow" gespeichert wird.
    $this -> mVal['job.apl.freigabe'] = array('jobid');

    //user in role of per_prj_verantwortlich is not invited to apl as an active member - per default
    $this -> mVal['apl.noinvite.alias'] = array('per_prj_verantwortlich');
    // all members of the following Groups can be invited to APL additionally to the Group: MANDATOR!
    $this -> mVal['invite.apl'] = array(); // built in, but not used
    //Invite to APL all of these groups can be invited to the apl on the right side as a whole group
    $this -> mVal['job.apl.parent.invitedgroups'] = array();

    // APL welche Funktionalitaet man will => != oder <
    //true: $lMinPos != $lIds[$lUid]['pos'] zeigt die Buttons nur an, solange man keinen Button bestätigt hat!
    //false: $lMinPos < $lIds[$lUid]['pos'] zeigt die Buttons, sobald man an der Reihe ist und auch weiter: Korrekturmögl.
    $this -> mVal['job.apl.show.btn.untilconfirm'] = true;

    // Domain names used in database table al_pck_master:
    // - domain for Color Picker
    $this -> mVal['ColorPickerDom'] = 'col';
    // - domain for Printing machines: Printing Press data connection between Portal and Networker
    $this -> mVal['printing.data.picklist.domain'] = 'prnt';

    // START: quick & dirty: muss hier stehen, kann nicht in der Mandantenconfig stehen für die sys/queue (MID=0)!
    // eMail-Adressumstellung
    // GPM POP3
    //$this -> mVal['gpm.pop.host'] = '212.66.144.86'; //62.157.152.21 alte IP Adresse
    //$this -> mVal['gpm.pop.port'] = 110;
    //$this -> mVal['gpm.pop.user'] = 'customer xml';
    //$this -> mVal['gpm.pop.pass'] = 'ST10XML&';
    //$this -> mVal['xml.test']  = FALSE;  //muss immer FALSE bleiben: if True -> xml-eMails werden NICHT gelöscht;

    //Adresse zur Bestaetigung: XML-Auftrag wird ein Job
    //$this -> mVal['gpm.returnmail.from'] = 'customer@st-packline.de';
    //$this -> mVal['gpm.returnmail.to'] = 'edi-bis@griesson.de';

    // ENDE: quick & dirty:

    // show critical deadlines: Define the time slot
    $this -> mVal['ddl.past']   = -1; // -1: all dates in past | >0: Amount of days in past
    $this -> mVal['ddl.future'] =  1; // Anzahl der zukuenftigen Tage; // >0: Amount of days in future

    // job bar: view actual (step from -> to)-ddl-dates in 'FROM' OR 'TO'
    $this -> mVal['ddl.view']   = 'FROM';

    // Copy Job: Which Aliase sollen beim Kopieren immer geleert werden?
    $this -> mVal['job.cpy.set-empty'] = array();
    $this -> mVal['job.cpy.set-empty'][] = 'jobid';
    $this -> mVal['job.cpy.set-empty'][] = 'webstatus';
    $this -> mVal['job.cpy.set-empty'][] = 'wec_prj_id';

    // Number of email-templates, used without events
    $this -> mVal['tpl.email'] = array();
    //$this -> mVal['tpl.email']['pwd'] = 310; //Passwort vergessen/forget
    //$this -> mVal['tpl.email']['apl'] = 0; //allg. APL-mail: not_used=0
    //$this -> mVal['tpl.email']['usr'] = 311; //Username  vergessen/forget


    $this -> mVal['tpl.email']['usr'] = 26; //forget Password
    $this -> mVal['tpl.email']['pwd'] = 27; //forget Password
    $this -> mVal['tpl.email']['apl'] = 12; //general APL-mail: not_used=0



    // My tasks
    // List all job fields that are needed for the deadline management
    $this -> mVal['my.tasks'] =  array();
    $this -> mVal['my.task.past']   = 7;  // unfinished apl of the previous [...] days will be shown
    $this -> mVal['my.task.future'] = 7;  // unfinished apl of the next [...] days will be shown

    // MyTasks - Configure addColumn - Reihenfolge wichtig
    // Take 'start_date' for approval loop start date
    // Take 'dates' for  the oldest deadline
    $this -> mVal['hom.wel.mytask.column']   = array('project_name','artikel','jobnr','start_date');
    $this -> mVal['hom.wel.mytask.flag.column'] = array('jobnr','project_name','webstatus','start_date');
    $this -> mVal['hom.wel.mytask.url'] = 'apl';// possible one of {'job','det','his','apl','fil'}
    $this -> mVal['hom.wel.mytask.flag.url'] = 'job';// possible one of {'job','det','his','apl','fil'}

    //CRP Flags in Joblists
    $this -> mVal['show.flawithflags'] = TRUE;
    $this -> mVal['show.ddlwithflags'] = TRUE;
    $this -> mVal['show.flagswithddl'] = FALSE;

    // Assets, Intouch Iframe : <iframe id="frame" src="http://assets.matthewsbrandsolutions.co.uk/CumulusE" width="1200" height="800" name="'.lan('hom-cc.menu').'">Currently there is nothing assigned!</iframe>
    $this -> mVal['hom.wel.assets']   = '';

    //Dashboard unter Home (hom-fla)
    $this -> mVal['hom.wel.dashboard'] = FALSE;

    //Delete Project Possible?
    $this -> mVal['job-pro.del'] = TRUE;

    //Extended Webstatus Filter
    $this -> mVal['extended.webstatus.filter'] = TRUE; // If True, show Webstatus Filter with checkboxes
    // Var 'extended.webstatus.filter.maxcols' is not be used after JobId:22824
    // Webstatuslist is showed in Popupmenue.
    //$this -> mVal['extended.webstatus.filter.maxcols'] = 3 ; // Column count to show extended Webstatus Filter

    //Highlight in the Joblist for whole Line?
    $this -> mVal['job.list.highlight.line'] = FALSE; // if True : Highlight all Line, if False:Highlight only DDL coloumn

    // Aliasname of special condition-function for supplier, first used by mand_1003
    $this -> mVal['cond.supplier'] = array();
    #$this -> mVal['cond.supplier']['pro'] = 'project_supplier';
    #$this -> mVal['cond.supplier']['job'] = 'supplier';

    $this -> mVal['extcnd'] = FALSE;

    // Notes fields in archive editable
    $this -> mVal['arc.edt.fie'] = array('notizen'); // which fields shall be editable when in archive status?
    $this -> mVal['arc.edt.fie.job'] = FALSE; // shall the additional tab be shown while in job status?
    $this -> mVal['arc.edt.fie.arc'] = TRUE; // shall the additional tab be shown while in archive status?

    // Reporting: Standard-Start-Class
    #$this -> mVal['rep.std-class'] = 'main';

    # Re-use job from archive:
    $this -> mVal['arc.reuse'] = array('art' => 80, 'rep' => 80, 'com' => 80);

    // Time Out on Second
    // 3600 to set the expiration date to one hour
    // NULL to define no time out.

    #$this -> mVal['session_time.out'] = 108000 ; // 30Min.
    $this -> mVal['session_time.out'] = NULL;

    # tabs
    $this -> mVal['tab'] = array('job', 'mainmenu');

    #error_reporting(0);// nur wenn Notices nicht abgefangen werden können.

    //related menu 23229
    $this -> mVal['job-rel.projects'] = TRUE;
    $this -> mVal['job-rel.projects.fields'] = array();
    $this -> mVal['job-rel.projects.link'] = 'sub';

    $this -> mVal['job-rel.skus'] = TRUE;
    $this -> mVal['job-rel.skus.fields'] = array();
    $this -> mVal['job-rel.skus.link'] = 'sub';

    $this -> mVal['job-rel.jobs'] = TRUE;
    $this -> mVal['job-rel.jobs.fields'] = array();
    $this -> mVal['job-rel.jobs.link'] = 'job';

    // Extended Reporting
    $this -> mVal['extended.reporting'] = FALSE;

    $this -> mVal['job.writer.default'] = 'mop';
	$this -> mVal['wave.global.id'] = '50';
    $this -> mVal['mop.db.host'] = '192.168.201.4';
    $this -> mVal['mop.db.user'] = 'portal';
    $this -> mVal['mop.db.pass'] = 'Porta741';
    $this -> mVal['mop.db.name'] = 'mop_global';
    $this -> mVal['moplink.host'] = 'http://mop2erp.matw-web/';
    $this -> mVal['mop.library'] = '/home/www-data/mop-library/';
    $this -> mVal['mop.synch.delayed'] = true;

    $this -> mVal['dalim.available'] = true;
    $this -> mVal['dalim.basedir'] = '/mnt/dalim_live/';
    $this -> mVal['dalim.baseurl'] = 'https://wave.5flow.net/DialogOEMServer/';
    $this -> mVal['dalim.internalurl'] = 'http://192.168.201.9:8080/DialogOEMServer/';
    $this -> mVal['dalim.volume'] = 'D';
    $this -> mVal['dalim.timestamp'] = '201207142055';
    $this -> mVal['dalim.download.previous'] = true;

    $this -> mVal['job.files.show.pdf'] = false;
    $this -> mVal['job.files.show.default'] = 'dalim';

    $this -> mVal['svc.uid'] = 425;

    $this -> mVal['job.notifytemplate'] = 0;
    $this -> mVal['job.notifytemplate.to'] = array('per_prj_verantwortlich');

    $this -> mVal['csv-exp.bymail'] = TRUE;
    $this -> mVal['csv-exp.separator'] = ';';
    $this -> mVal['csv-exp.tpl'] = 271;
    $this -> mVal['phpexcel.available'] = TRUE;

	$this -> mVal['flink'] = false; // TRUE uses Flink, FALSE uses Alink

	$this -> mVal['flink.destination.dalim.dir'] = ''; // destination of Dalim Dialogue documents (dir)
	$this -> mVal['flink.destination.dalim.url'] = ''; // destination of Dalim Dialogue documents (url)
	$this -> mVal['flink.destination.dalim.overwrite'] = FALSE; // TRUE: overwrites files of same name without asking, FALSE: asks politely whether to overwrite or not
	$this -> mVal['flink.destination.dalim.history'] = FALSE; // TRUE: writes to job history when upload successful, FALSE: does not write to job history
	$this -> mVal['flink.destination.dalim.softproof'] = 'wec'; // can be anything from inc/job/fil/src: dalim, dms, doc, pdf, rtp or wec

	$this -> mVal['flink.destination.dms.dir'] = '/media/dmsshare/'; // destination of Esko WebCenter documents (dir)
	$this -> mVal['flink.destination.dms.url'] = 'https://dms.5flow.net/'; // destination of Esko WebCenter documents (url)
	$this -> mVal['flink.destination.dms.overwrite '] = FALSE; // TRUE: overwrites files of same name without asking, FALSE: asks politely whether to overwrite or not
	$this -> mVal['flink.destination.dms.history '] = FALSE; // TRUE: writes to job history when upload successful, FALSE: does not write to job history
	$this -> mVal['flink.destination.dms.softproof'] = 'dms'; // can be anything from inc/job/fil/src: dalim, dms, doc, pdf, rtp or wec

	$this -> mVal['flink.destination.doc.dir'] = '/mnt/mbc_mars_doc/'; // destination of *.doc, previously managed by the portal (dir)
	$this -> mVal['flink.destination.doc.url'] = '/mnt/mbc_mars_doc/'; // destination of *.doc, previously managed by the portal (url)
	$this -> mVal['flink.destination.doc.overwrite '] = FALSE; // TRUE: overwrites files of same name without asking, FALSE: asks politely whether to overwrite or not
	$this -> mVal['flink.destination.doc.history '] = FALSE; // TRUE: writes to job history when upload successful, FALSE: does not write to job history
	$this -> mVal['flink.destination.doc.softproof'] = 'wec'; // can be anything from inc/job/fil/src: dalim, dms, doc, pdf, rtp or wec

	$this -> mVal['flink.destination.pdf.dir'] = ''; // destination of *.pdf, previously managed by Alink (dir)
	$this -> mVal['flink.destination.pdf.url'] = ''; // destination of *.pdf, previously managed by Alink (url)
	$this -> mVal['flink.destination.pdf.overwrite '] = FALSE; // TRUE: overwrites files of same name without asking, FALSE: asks politely whether to overwrite or not
	$this -> mVal['flink.destination.pdf.history '] = FALSE; // TRUE: writes to job history when upload successful, FALSE: does not write to job history
	$this -> mVal['flink.destination.pdf.softproof'] = 'wec'; // can be anything from inc/job/fil/src: dalim, dms, doc, pdf, rtp or wec

	$this -> mVal['flink.destination.rtp.dir'] = ''; // destination of Dalim Dialogue documents (dir)
	$this -> mVal['flink.destination.rtp.url'] = ''; // destination of Dalim Dialogue documents (url)
	$this -> mVal['flink.destination.rtp.overwrite '] = FALSE; // TRUE: overwrites files of same name without asking, FALSE: asks politely whether to overwrite or not
	$this -> mVal['flink.destination.rtp.history '] = FALSE; // TRUE: writes to job history when upload successful, FALSE: does not write to job history
	$this -> mVal['flink.destination.rtp.softproof'] = 'wec'; // can be anything from inc/job/fil/src: dalim, dms, doc, pdf, rtp or wec

	$this -> mVal['flink.destination.wec.dir'] = ''; // destination of Esko WebCenter documents (dir)
	$this -> mVal['flink.destination.wec.url'] = ''; // destination of Esko WebCenter documents (url)
	$this -> mVal['flink.destination.wec.overwrite '] = FALSE; // TRUE: overwrites files of same name without asking, FALSE: asks politely whether to overwrite or not
	$this -> mVal['flink.destination.wec.history '] = FALSE; // TRUE: writes to job history when upload successful, FALSE: does not write to job history
	$this -> mVal['flink.destination.wec.softproof'] = 'wec'; // can be anything from inc/job/fil/src: dalim, dms, doc, pdf, rtp or wec
	
	$this -> mVal['use-user-tracking'] = true; // activate onlineproof tool tracking
	
	$this -> mVal['theme.choice'] = 'wave8';
    $this -> mVal['status.display'] = 'activeonly';
    
    $this -> mVal['rep-exp.tpl'] = 414; //Template ID for CSV Export

    if(file_exists(CUST_PATH.'inc/cor/cfg-local.php')) include(CUST_PATH.'inc/cor/cfg-local.php');
