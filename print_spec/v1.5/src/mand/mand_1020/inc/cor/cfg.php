<?php
/**
 * Main Config File
 *
 * Provides access to configuration parameters
 * like database connection details, mail server
 * and server specific file paths etc.
 * Read-only access for security reasons.
 *
 * @author Geoffrey Emmans <emmans@qbf.de>
 * @package core
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 10760 $
 * @date $Date: 2013-01-31 20:39:19 +0100 (Do, 31 Jan 2013) $
 * @author $Author: hoffmann $
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
  /*
   * Einrichten eines neuen Mandanten:
   * 1. neues Verzeichnis aus mand_999 kopieren. Umbenennen des neuen Verzeichnisses: Nur die Nummer ������������������ndern!
   *    Die neue Nummer (mand_...) wird unten in das define NEW_MID eingetragen.
   * 2. diese Datei anpassen (die folgenden 5 defines)
   * 3. die SQLs (unten im Kommentar) anpassen und in der Datenbank ausf������������������hren
   * 4. Die Class Names in den Dateien anpassen
   * 5. Die Auftragsfelder im Portal vervollst������������������ndigen, nicht gebrauchte l������������������schen oder durch neue ������������������berschreiben
   * 6. Strukturabgleich
   * 7. Anmeldung des Kunden in Networker (Eintrag in kunden, macros (Alink-User-Bedingungen) und allogin)
   * 8. neue Gruppen (User) anlegen
   * 9. Critical Path f������������������r d. Jobs kopieren, umbenennen
   *10. Rechte f������������������r Gruppen setzen
   *11. Cache l������������������schen
   */

   /*
   * MANDATOR: Dateienk������������������rzel (KLEINgeschrieben, genau 3 Buchstaben!)
   * Diese drei Buchstaben werden als Referenz auf diesen Mandanten in die
   * - Portaldatenbank: al_sys_mand eingetragen (s.u.)
   * - Networkerdatenbank: allogin unter sessionid eingetragen
   * - in den Dateien (mand-Verzeichnis) mit "class CNew_..." wird New
   * durch das K������������������rzel (jetzt nur 1. Buchstabe GROSSgeschrieben) ersetzt
   *
   * bei den folgenden 5 define ist NUR die rechte Seite zu ersetzen
   */
define('MANDATOR', 			'cli');
define('MANDATOR_NAME', 'Corebrowser');
define('KNR_REP', 			'');
define('KNR_ART', 			'');
define('NEW_MID', 		  1020);

    CCor_Cfg::set('smtp.test',     FALSE);  //if TRUE -> eMail.to = smtp.admin
    CCor_Cfg::set('smtp.admin', 	'admin@localhost');
    CCor_Cfg::set('smtp.disabled', FALSE);  //if TRUE -> kein eMail-Versand
    CCor_Cfg::set('smtp.from', 'NO-REPLY@matthewsbrandsolutions.co.uk');

    // Keywords / Stichwort Zusammenstellung f������������������r
    // Jobs
    CCor_Cfg::set('job.keyw', array('brand','id_sorte','gaba_brand','gaba_subbrand','variante','gaba_packaging_type','col_sl','compo','prod_size','col_new_cp_ref')); // Alias-Namen f������������������r d. Erstellung des Stichwortes.
	CCor_Cfg::set('job-art.keyw', array('brand','id_sorte','gaba_brand','gaba_subbrand','variante','gaba_packaging_type','col_sl','compo','prod_size','col_new_cp_ref','col_no_lang'));
	CCor_Cfg::set('job-adm.keyw', array('cpp_brand','col_sl','cpp_packaging_type','prod_size','ccp_sap_ref'));
    /*CCor_Cfg::set('job.keyw', array()); // Alias-Namen f������������������r d. Erstellung des Stichwortes.
    CCor_Cfg::set('job-art.keyw', array('brand','id_sorte','variante','col_sl','compo','prod_size','col_new_cp_ref')); // Alias-Namen f������������������r d. Erstellung des Stichwortes.
    CCor_Cfg::set('job-rep.keyw', array('brand','id_sorte','variante','col_sl','compo','prod_size','col_new_cp_ref')); // Alias-Namen f������������������r d. Erstellung des Stichwortes.
    CCor_Cfg::set('job-mis.keyw', array('gaba_brand','gaba_subbrand','col_sl','gaba_packaging_type','prod_size','gaba_new_matno','col_no_lang')); // Alias-Namen f������������������r d. Erstellung des Stichwortes.
    CCor_Cfg::set('job-com.keyw', array('gaba_brand','gaba_subbrand','col_sl','gaba_packaging_type','prod_size','gaba_new_matno','col_no_lang')); // Alias-Namen f������������������r d. Erstellung des Stichwortes.*/
    // Projekte
    //CCor_Cfg::set('job-pro.keyw', array('projekt_nr','projekt_name')); //Alias-Namen f������������������r d. Erstellung des Projekt-Stichwortes.
    CCor_Cfg::set('job-pro.keyw', array('brand','id_sorte','variante','col_sl','gaba_packaging_type','prod_size','col_new_cp_ref')); //Alias-Namen f������������������r d. Erstellung des Projekt-Stichwortes.

    // Projektfelder, die bei der Projektzuordnung i.d. Job geschrieben werden.
    CCor_Cfg::set('job-pro.fields', array('project_name','project_number','per_07','col_project_type','ppm','per_09','packaging','legal','per_10','per_02','cms','project_initiation','artwork_approval','file_printer','alignment_meeting','project_deadline'));

    // Auftragsfelder in "Meine Freigabeliste"
    // Jobid ist MUSS! Speicherung der Daten in der al_sys_shadow_MID.
    // Alle Auftragsfelder in der Liste m������������������ssen auch i.d. al_job_shadow_MID vorhanden und
    // das Auftragsfeldflag "Reporting" mu������������������ aktiviert sein (Aktiviert die Speicherung in al_sys_shadow_MID).
    CCor_Cfg::set('job.apl.freigabe', array('jobid','artikel','material_nr')); // Alias-Namen, die unter dem Men������������������ "Meine Freigabe" angezeigt werden.
    
    CCor_Cfg::set('job.apl.adm.signature', TRUE); //Enable digital signature for CPP jobs
    
    //Invite to APL all of these groups can be invited to the apl on the right side as a whole group
    CCor_Cfg::set('job.apl.parent.invitedgroups', array('2345','2257','2368','2367','2366','2261','2264','2364','2363','2265','2269','2362','2361','2061','2059','2370','2359','2060','2358','2357','2356','2355','2268','2354','2270','2353','2352','2351','2350','2349','2348','2263','2346','2347','2436','2697',
	'3424','3421','3451','3419','3453','3455','3416','3423','3413','3420','3461','3463','3422','3465','3425','3417','3467','3481','3418','3415','3427','3469','3471','3414','3473','3426','3412','3475','3625'));//,'2790','2831'));

    // Default Tabs in der Job-Maske:
    CCor_Cfg::set('job.mask.tabs', array('job')); // Verf������������������gbare Tab-Reiter in der Job-Maske: job=Identifikation, det=Details

    CCor_Cfg::set('job-art.mask.tabs', array('job','det','pro'));

    // Aliasname of special condition-function for supplier, first used by mand_1003
    // it's not the same alias for projects and jobs for supplier: used EITHER 'project_supplier' OR 'supplier'
    CCor_Cfg::set('cond.supplier', array('job' => 'printer'));

    // Items f������������������r die Menus "Aktive Jobs" und "Archiv" .
    CCor_Cfg::set('menu-aktivejobs', array('job-rep','job-art','job-sec')); // Verf������������������gbare Items unter dem Men������������������ "Aktive Jobs".
    CCor_Cfg::set('menu-archivjobs', array('rep','art','sec')); // Verf������������������gbare Items unter dem Men������������������ "Archiv".
    CCor_Cfg::set('menu-projektitems', array('job_art','job_sec','job_rep')); // Verf������������������gbare Job-Typen i. Projekt-Items um ein neu Job anzulegen

    //////////////////////////////////////////////////////////////////////////////////
    // General
    CCor_Cfg::set('mand.key', MANDATOR);
    CCor_Cfg::set('mand.mid', NEW_MID);
    CCor_Cfg::set('mand.usr', MANDATOR_NAME);

    // Kundennummer
    CCor_Cfg::set(MANDATOR.'.def.knr', KNR_REP); // KNR: Repro etc.
    CCor_Cfg::set(MANDATOR.'.art.knr', KNR_ART); // KNR: Artwork/Agentur
    CCor_Cfg::set(MANDATOR.'.alink.knr', array("C12783","C10949","C13180")); // Condition f. Mandator KNr (Customerno) in Alink // only needed with more than 1 KNr

    // Webcenter
    CCor_Cfg::set('wec.tpl', 'Template '.MANDATOR_NAME.' '.KNR_REP);
    CCor_Cfg::set('wec.grp', 'MITGLIEDER_'.MANDATOR_NAME);
    CCor_Cfg::set('wec.jobs', array('art','rep','com','mis','adm','sec'));

    // zus������������������tzliche Jobtypen:
    CCor_Cfg::set('all-jobs_PDB', array());
	CCor_Cfg::set('all-jobs_ALINK', array('art','rep','com','mis','adm','sec'));
    CCor_Cfg::set('all-jobs', array_merge(CCor_Cfg::get('all-jobs_ALINK'), CCor_Cfg::get('all-jobs_PDB')));

	// My tasks
    // job fields that can contain dates (for approval or else)
    CCor_Cfg::set('my.tasks', array('ddl_01','ddl_02', 'ddl_03', 'ddl_04', 'ddl_05', 'ddl_06'));

    // Take 'start_date' for approval loop start date
    // Take 'dates' for  the oldest deadline
    CCor_Cfg::set('hom.wel.mytask.column', array('jobnr','apl','project_name','brand','id_sorte','variante','compo','prod_size','ddl_02','webstatus'));
    CCor_Cfg::set('hom.wel.mytask.flag.column', array('jobnr','project_name','brand','id_sorte','variante','compo','prod_size','ddl_02','webstatus'));
    //CCor_Cfg::set('hom.wel.mytask.role.column', array('project_name','brand','id_sorte','col_prod_site','ddl_02','ddl_04','ddl_06'));

    // unfinished apl of the previous [...] days will be shown
    CCor_Cfg::set('my.task.past', 60);
    // unfinished apl of the next [...] days will be shown
    CCor_Cfg::set('my.task.future', 60);

    // Re-use job from archive: set webstatus for reset
    CCor_Cfg::set('arc.reuse', array('art' => 30, 'rep' => 30, 'mis' => 30, 'com' => 30, 'adm' => 30, 'sec' => 20));

    CCor_Cfg::set('svc.apl.tpl', 12); // Email template for approval loop service
    CCor_Cfg::set('svc.apl.from', 60); // check for open apls from the last xxx days
    CCor_Cfg::set('svc.apl.to', 1); // check for open apls in the next xxx days
	
	// Extended (user) conditions
	CCor_Cfg::set('extcnd', true);
	
	#CCor_Cfg::set('show.user.details', FALSE);
    CCor_Cfg::set('show.group.details', TRUE);

	CCor_Cfg::set('theme.choice', 'wave8');
    
    CCor_Cfg::set('theme.colours', array('art' => 'red',
        'pro' => 'purple',
        'art' => 'blue',
        'rep' => 'green',
        'sec' => 'orange')
    );

    CCor_Cfg::set('extended.reporting', TRUE);
    CCor_Cfg::set('report.map', array(
      '' => '',
      'Artwork Approval' => 'art_app',
      'Artwork Production' => 'art_prod',
      'Awaiting Project Completion' => 'ap_comp',
      'Brief Approval' => 'brief_app',
      'Create Brief' => 'cbrief',
      'Create Brief (Approved Master)' => 'cbrief_am',
      'Data Delivered' => 'data_del',
      'Design Release' => 'des_rel',
      'Files Ready For Despatch' => 'files_ready',
      'Job Closed' => 'closed',
      'MBS Brief Approval' => 'mbs_app',
      'On Hold' => 'on_hold',
      'Print Download Data' => 'pri_down',
      'Print Trial' => 'pri_tri',
      'Print Trial Approval' => 'pri_tri_app',
      'Print Trial Pending' => 'pri_tri_pend',
      'Printer Accepted' => 'pri_acc',
      'Printer Download Data' => 'pri_down_data',
      'Regulatory Submission' => 'reg_sub',
      'Repro Production' => 'rep_prod',
      'Repro Proof Approval' => 'rep_proof',
      'Supply Data' => 'supp_data',
      'With MBS' => 'mbs',
      'With MBS CS' => 'mbscs'
    ));
	
	CCor_Cfg::set('master.varaiant.bundle', TRUE);
	CCor_Cfg::set('view.projekt.joblist', TRUE);
    
    CCor_Cfg::set('view.projekt.joblist', false);
    
    /*
     * es gibt noch cfg-local-test.php und cfg-local-stage.php. Diese beinhalten die Informationen
     * f������������������r die Portale auf TEST und STAGE. Sie enthalten im Moment nur den Namen f������������������r die Webcenter Vorlage.
     * Die cfg-local-test.php mu������������������ auf TEST in cfg-local.php umbenannt und
     * die cfg-local-stage.php mu������������������ auf STAGE in cfg-local.php umbenannt werden
     * In der eigenen lokalen Umgebung (Spielwiese/Entwicklungsumgebung) mu������������������ es auch
     * eine cfg-local.php geben, sonst wird mit LIVE-Daten/Vorlagen gearbeitet!
     */
    if(file_exists(MAND_PATH_INC.'/cor/cfg-local.php')) include(MAND_PATH_INC.'/cor/cfg-local.php');



/*
//#######################################################

// SQLs f������������������r die Einrichtung der neuen Informationen in der Portaldatenbank
 *
// mand=999  -> NewMandant = 'Vorlage'
// mand=1000 -> DummyMandant; (zum Testen) wird aus der 'Vorlage' kopiert

// Zur Anlage eines neuen Mandanten mu������������������ im 1. SQL zu al_sys_mand in der 3.ten Zeile
 * die Zahl 1000 durch die MandantenID,
 * das "code"-K������������������rzel durch das neue Mandanten-K������������������rzel (s.o. = 'MANDATOR') und
 * der deutsche und englische Name ersetzt werden. Diese Namen werden im Portal angezeigt!
// In den weiteren SQLs m������������������ssen nur die Zahl 1000 durch die MandantenID ersetzt werden.

INSERT INTO `al_sys_mand`
  (`id` , `code`, `name_en`     , `name_de`     ) VALUES
  (1012 , 'col' , 'Colgate', 'Colgate');



INSERT INTO `al_sys_pref` (`code`, `mand`, `grp`, `name_de`, `name_en`, `val`)
  SELECT  `code`, 1012, `grp`, `name_de`, `name_en`, `val` FROM `al_sys_pref` WHERE mand=999;



INSERT INTO `al_usr_rig` (`user_id`, `code`, `mand`, `right`, `level`)
  SELECT 1, `code`, 1012, `right`, `level` FROM `al_usr_rig` WHERE `mand`=999;



INSERT INTO al_fie (`mand`, `src`, `alias`, `native`, `name_en`, `name_de`, `typ`, `param`, `attr`, `learn`, `avail`, `flags`, `used`)
	SELECT  1012, `src`, `alias`, `native`, `name_en`, `name_de`, `typ`, `param`, `attr`, `learn`, `avail`, `flags`, `used` FROM al_fie WHERE mand=999;


// Nachdem die Auftragsfelder komplett eingerichtet sind: unter Auftragsfelder -> Strukturabgleich ausf������������������hren
// Dieser richtet weitere neue Tabellen mit den Auftragsfeldern an.
 *
//#######################################################
*/