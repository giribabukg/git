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
 * @version $Rev: 43 $
 * @date $Date: 2012-03-27 09:07:13 +0100 (Tue, 27 Mar 2012) $
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
  /*
   * Einrichten eines neuen Mandanten:
   * 1. neues Verzeichnis aus mand_999 kopieren. Umbenennen des neuen Verzeichnisses: Nur die Nummer ändern!
   *    Die neue Nummer (mand_...) wird unten in das define NEW_MID eingetragen.
   * 2. diese Datei anpassen (die folgenden 5 defines)
   * 3. die SQLs (unten im Kommentar) anpassen und in der Datenbank ausführen
   * 4. Die Class Names in den Dateien anpassen
   * 5. Die Auftragsfelder im Portal vervollständigen, nicht gebrauchte löschen oder durch neue überschreiben
   * 6. Strukturabgleich
   * 7. Anmeldung des Kunden in Networker (Eintrag in kunden, macros (Alink-User-Bedingungen) und allogin)
   * 8. neue Gruppen (User) anlegen
   * 9. Critical Path für d. Jobs kopieren, umbenennen
   *10. Rechte für Gruppen setzen
   *11. Cache löschen
   */

   /*
   * MANDATOR: Dateienkürzel (KLEINgeschrieben, genau 3 Buchstaben!)
   * Diese drei Buchstaben werden als Referenz auf diesen Mandanten in die
   * - Portaldatenbank: al_sys_mand eingetragen (s.u.)
   * - Networkerdatenbank: allogin unter sessionid eingetragen
   * - in den Dateien (mand-Verzeichnis) mit "class CNew_..." wird New
   * durch das Kürzel (jetzt nur 1. Buchstabe GROSSgeschrieben) ersetzt
   *
   * bei den folgenden 5 define ist NUR die rechte Seite zu ersetzen
   */
    define('MANDATOR', 			'psp'); // Dateienkürzel (KLEINgeschrieben,genau 3 Buchstaben!)
    define('MANDATOR_NAME', 'MBC Global Print Spec Database'); // Mandantenname (EIN Wort, 1. Buchstabe GROSSgeschrieben)
    define('KNR_REP', 			'C011'); // KNR_REP: Kundennummer für Repro etc.
    define('KNR_ART', 			'C011'); // KNR_ART: Kundennummer für Artwork/Agentur
    define('NEW_MID', 		  3); // MID > 0 !!! MandantenID wird auch i.d. Datenbank al_sys_mand (s.u.) eingetragen
		
    // !!! Weitere MÖGLICHE Anpassungen: !!!

    //CCor_Cfg::set('smtp.test',     TRUE);  //if TRUE -> eMail.to = smtp.admin
    //CCor_Cfg::set('smtp.admin', 	'admin@localhost');
    //CCor_Cfg::set('smtp.disabled', TRUE);  //if TRUE -> kein eMail-Versand

    // Keywords / Stichwort Zusammenstellung für
    // Jobs
    CCor_Cfg::set('job.keyw', array('artikel','warengruppe','gewicht')); // Alias-Namen für d. Erstellung des Stichwortes.
    // Projekte
    CCor_Cfg::set('job-pro.keyw', array('project_no','pro_printer')); //Alias-Namen für d. Erstellung des Projekt-Stichwortes.
    
    // Projektfelder, die bei der Projektzuordnung i.d. Job geschrieben werden.
    CCor_Cfg::set('job-pro.fields', array('corecusid','pro_telephone','email','pro_contact','country','city','zip_code','house_no','pro_address','pro_printer'));
    
    // Projektfelder, die bei Zuordnung eines Jobs in ein Projekt geschrieben werden.
    CCor_Cfg::set('job-pro.fields.onassign', array('corecusid','pro_telephone','email','pro_contact','country','city','zip_code','house_no','pro_address','pro_printer'));
    CCor_Cfg::set('job-pro.fields.pro2item.ddl', false);
    
    //CCor_Cfg::set('job-pro.fields.onupdate-art', array());
    CCor_Cfg::set('job-pro.fields.onupdate-rep', array('corecusid','pro_telephone','email','pro_contact','country','city','zip_code','house_no','pro_address','pro_printer'));
    CCor_Cfg::set('job-pro.fields.onupdate-art', array('corecusid','pro_telephone','email','pro_contact','country','city','zip_code','house_no','pro_address','pro_printer'));
    
    // Auftragsfelder in "Meine Freigabeliste"
    // Jobid ist MUSS! Speicherung der Daten in der al_sys_shadow_MID.
    // Alle Auftragsfelder in der Liste müssen auch i.d. al_job_shadow_MID vorhanden und
    // das Auftragsfeldflag "Reporting" muß aktiviert sein (Aktiviert die Speicherung in al_sys_shadow_MID).
    CCor_Cfg::set('job.apl.freigabe', array('jobid','artikel','material_nr')); // Alias-Namen, die unter dem Menü "Meine Freigabe" angezeigt werden.
    
    //Invite to APL all of these groups can be invited to the apl on the right side as a whole group
    CCor_Cfg::set('job.apl.parent.invitedgroups', array());

    // APL welche Funktionalität man will => != oder <
    //true: $lMinPos != $lIds[$lUid]['pos'] zeigt die Buttons nur an, solange man keinen Button bestätigt hat!
    //false: $lMinPos < $lIds[$lUid]['pos'] zeigt die Buttons, sobald man an der Reihe ist und auch weiter: Korrekturmögl.
    CCor_Cfg::set('job.apl.show.btn.untilconfirm', false);
    
    // Default Tabs in der Job-Maske:
    CCor_Cfg::set('job.mask.tabs', array('job','det')); // Verfügbare Tab-Reiter in der Job-Maske: job=Identifikation, det=Details
    
    
    // Items für die Menus "Aktive Jobs" und "Archiv" .
    CCor_Cfg::set('menu-aktivejobs', array('job-all','job-rep','job-art')); // Verfügbare Items unter dem Menü "Aktive Jobs".
    CCor_Cfg::set('menu-archivjobs', array('pro','rep','art')); // Verfügbare Items unter dem Menü "Archiv".
    CCor_Cfg::set('menu-projektitems', array('job_rep','job_art')); // Verfügbare Job-Typen i. Projekt-Items um ein neu Job anzulegen
    
    /////////////////////////////////////////////////////////////////////////////////////
    // General
    CCor_Cfg::set('mand.key', MANDATOR);
    CCor_Cfg::set('mand.mid', NEW_MID);
    CCor_Cfg::set('mand.usr', MANDATOR_NAME);

    // Kundennummer
    CCor_Cfg::set(MANDATOR.'.def.knr', KNR_REP); // KNR: Repro etc.
    CCor_Cfg::set(MANDATOR.'.art.knr', KNR_ART); // KNR: Artwork/Agentur
    CCor_Cfg::set(MANDATOR.'.alink.knr',array(KNR_REP,KNR_ART)); // All customer number of Client to get JobList and JobDetails.

    // Webcenter
    CCor_Cfg::set('wec.tpl', 'Vorlage '.MANDATOR_NAME.' '.KNR_REP);
    CCor_Cfg::set('wec.grp', 'MITGLIEDER_'.MANDATOR_NAME);

    // zusätzliche Jobtypen:
    CCor_Cfg::set('all-jobs_PDB', array());
    CCor_Cfg::set('all-jobs', array_merge(CCor_Cfg::get('all-jobs_ALINK'), CCor_Cfg::get('all-jobs_PDB')));
    
    
    CCor_Cfg::set('view.projekt.joblist', false);
	
	CCor_Cfg::set('job.files.show.default','doc');
	
	// Flink
    CCor_Cfg::set('flink', TRUE);
	CCor_Cfg::set('flink.destination.doc.dir', ''); // destination of *.doc, previously managed by the portal (dir)
	CCor_Cfg::set('flink.destination.doc.url', ''); // destination of *.doc, previously managed by the portal (url)
    CCor_Cfg::set('wec.upload.alink', FALSE);
    
    // My tasks
    // job fields that can contain dates (for approval or else)
    //CCor_Cfg::set('my.tasks', array());
    
    // Take 'start_date' for approval loop start date
    // Take 'dates' for  the oldest deadline
    //CCor_Cfg::set('hom.wel.mytask.column', array());
   
    // unfinished apl of the previous [...] days will be shown
    //CCor_Cfg::set('my.task.past', 3);
    // unfinished apl of the next [...] days will be shown
    //CCor_Cfg::set('my.task.future', 2);
	
	//Set the Print Spec on PDB
	CCor_Cfg::set('job.writer.default', 'portal');
	
	CCor_Cfg::set('theme.colours', array('all' => 'blue',
			'adm' => 'red',
			'art' => 'cyan',
			'com' => 'purple',
			'mis' => 'orange',
			'pro' => 'purple',
			'rep' => 'red',
			'sec' => 'orange',
			'ser' => '',
			'sku' => 'cyan',
			'tra' => 'green'));
	
	CCor_Cfg::set('csv-exp.bymail', false);
    

    /*
     * es gibt noch cfg-local-test.php und cfg-local-stage.php. Diese beinhalten die Informationen
     * für die Portale auf TEST und STAGE. Sie enthalten im Moment nur den Namen für die Webcenter Vorlage.
     * Die cfg-local-test.php muß auf TEST in cfg-local.php umbenannt und
     * die cfg-local-stage.php muß auf STAGE in cfg-local.php umbenannt werden
     * In der eigenen lokalen Umgebung (Spielwiese/Entwicklungsumgebung) muß es auch
     * eine cfg-local.php geben, sonst wird mit LIVE-Daten/Vorlagen gearbeitet!
     */
    if(file_exists(MAND_PATH_INC.'/cor/cfg-local.php')) include(MAND_PATH_INC.'/cor/cfg-local.php');



/*
//#######################################################

// SQLs für die Einrichtung der neuen Informationen in der Portaldatenbank
 *
// mand=999  -> NewMandant = 'Vorlage'
// mand=1000 -> DummyMandant; (zum Testen) wird aus der 'Vorlage' kopiert

// Zur Anlage eines neuen Mandanten muß im 1. SQL zu al_sys_mand in der 3.ten Zeile
 * die Zahl 1000 durch die MandantenID,
 * das "code"-Kürzel durch das neue Mandanten-Kürzel (s.o. = 'MANDATOR') und
 * der deutsche und englische Name ersetzt werden. Diese Namen werden im Portal angezeigt!
// In den weiteren SQLs müssen nur die Zahl 1000 durch die MandantenID ersetzt werden.

INSERT INTO `al_sys_mand`
  (`id` , `code`, `name_en`     , `name_de`     ) VALUES
  (1000 , 'dmy' , 'DummyMandant', 'DummyMandant');



INSERT INTO `al_sys_pref` (`code`, `mand`, `grp`, `name_de`, `name_en`, `val`)
  SELECT  `code`, 1000, `grp`, `name_de`, `name_en`, `val` FROM `al_sys_pref` WHERE mand=999;



INSERT INTO `al_usr_rig` (`user_id`, `code`, `mand`, `right`, `level`)
  SELECT 1, `code`, 1000, `right`, `level` FROM `al_usr_rig` WHERE `mand`=999;



INSERT INTO al_fie (`mand`, `src`, `alias`, `native`, `name_en`, `name_de`, `typ`, `param`, `attr`, `learn`, `avail`, `flags`, `used`)
	SELECT  1000, `src`, `alias`, `native`, `name_en`, `name_de`, `typ`, `param`, `attr`, `learn`, `avail`, `flags`, `used` FROM al_fie WHERE mand=999;


// Nachdem die Auftragsfelder komplett eingerichtet sind: unter Auftragsfelder -> Strukturabgleich ausführen
// Dieser richtet weitere neue Tabellen mit den Auftragsfeldern an.
 *
//#######################################################
*/