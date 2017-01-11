<?php
/**
 * Jobs: Formular Templates
 *
 *  Description
 *  Diese Datei mu������ aufgrund der Templatedefinitionen mandantenspezifisch ������berschrieben werden!
 *
 * @package    JOB
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 4588 $
 * @date $Date: 2010-01-12 10:08:27 +0100 (Di, 12 Jan 2010) $
 * @author $Author: yildiz $
 */
class CJob_Formtpl extends CCor_Dat {

  public $mTemplates = array();
  public $mArchivTemplates = array();
  public $mDefaultTabs = '';
  
  public function __construct() {
    $this -> mDefaultTabs = CCor_Cfg::get('job.mask.tabs', array());

    // In der angegebenen REIHENFOLGE werden die Masken im Formular ausgegeben
    // Die Masken liegen unter mand/mand_Nr/mand/htm/job/...
 
    //-- START: Variant Job Maske
    $this -> mTemplates['rep'] = array(
      'job' => array(
        'route' => 'rep',
      )
    );
    // KOPIERE die bisherigen Masken auch in die anderen Jobtypen:
      //-- START: Master Job Maske
    $this -> mTemplates['art'] = array(
      'job' => array(
        'route' => 'art',
      ),
      'det' => array(
      	'qc' => 'art'
      ),
    		'pro' => array(
    				'qm' => 'art'
    		)
    ); 
	
    //-- START: GABA Master Job Maske
    $this -> mTemplates['com'] = array(
      'job' => array(
        'route' => 'com',
      )
    );
    // KOPIERE die bisherigen Masken auch in die anderen Jobtypen:
      //-- START: GABA Variant Job Maske
    $this -> mTemplates['mis'] = array(
      'job' => array(
        'route' => 'mis',
      )
    );
	
	// CCP Job Maske 
    $this -> mTemplates['adm'] = array(
      'job' => array(
        'route' => 'adm',
      )
    );
	
	// Combined Job Maske 
    $this -> mTemplates['sec'] = array(
      'job' => array(
        'route' => 'sec',
      )
    );

    ##################

    //-- START: Projekte - Job Maske
    $this -> mTemplates['pro'] = array(
      'job' => array(
        'ids' => 'pro',
        'ddl' => 'pro',
      ),
      'det' => array(
        'brf' => 'pro',
      )
    );
    //-- ENDE: Projekte - Job Maske

    ##################

    // im Archiv sollen die gleichen Masken erscheinen, wie in den aktiven Jobs
    $this -> mArchivTemplates  = $this -> mTemplates;

  }

}