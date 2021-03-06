<?php
/**
 * Jobs: Formular Templates
 *
 *  Description
 *  Diese Datei muß aufgrund der Templatedefinitionen mandantenspezifisch überschrieben werden!
 *
 * @package    JOB
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 6 $
 * @date $Date: 2012-02-21 08:50:56 +0000 (Tue, 21 Feb 2012) $
 * @author $Author: gemmans $
 */
class CJob_Formtpl extends CCor_Dat {

  public $mTemplates = array();
  public $mArchivTemplates = array();
  public $mDefaultTabs = '';
  
  public function __construct() {
    $this -> mDefaultTabs = CCor_Cfg::get('job.mask.tabs', array());

    // In der angegebenen REIHENFOLGE werden die Masken im Formular ausgegeben
    // Die Masken liegen unter mand/mand_Nr/mand/htm/job/...
 
    //-- START: Repro Job Maske
    $this -> mTemplates['rep'] = array(
      'job' => array(
        'ids' => 'rep',
		'ids2' => 'rep',
		'ids3' => 'rep',
		'ids5' => 'rep',
		'col' => 'rep',
		'ids10' => 'rep',
      ),
      'det' => array(
        'pri' => 'rep',
        'out' => 'rep',
        'co2' => 'rep',
        'co3' => 'rep',
        'co5' => 'rep',
        'co6' => 'rep',
        'co7' => 'rep',
		'co8' => 'rep',
      ),
    );
	
    //-- START: Repro Job Maske
    $this -> mTemplates['art'] = array(
      'job' => array(
        'ids' => 'art',
		'ids1' => 'art',
		'ids2' => 'art',
		'ids3' => 'art',

      ),
      'det' => array(

      ),
    );
    // KOPIERE die bisherigen Masken auch in die anderen Jobtypen:
    //-- Artwork Job Maske
    //$this -> mTemplates['art'] = $this -> mTemplates['rep'];
    //$this -> mTemplates['art']['job']['ddl'] = 'art';
    //-- ADM Job Maske
    $this -> mTemplates['adm'] = $this -> mTemplates['rep'];
    //-- MIS Job Maske
    $this -> mTemplates['mis'] = $this -> mTemplates['rep'];
    //-- SEC Job Maske
    $this -> mTemplates['sec'] = $this -> mTemplates['rep'];

    
    // Webcenter ProjektId nur für Jobs "art, rep" und mit das Recht "Job-wec-id" verknüpft.
    // Falls es in der Job-Maske keine Reiter "Details (det)" gibt, soll es unter "Identifikation (job)" angezeigt werden.
    $lUsr = CCor_Usr::getInstance();
    if($lUsr -> canEdit('job-wec-id')){
      if (in_array('det',$this -> mDefaultTabs)){
        //$this -> mTemplates['art']['det']['wec'] = 'rep';
        $this -> mTemplates['rep']['det']['wec'] = 'rep';
        } else{
        //$this -> mTemplates['art']['job']['wec'] = 'rep';
        $this -> mTemplates['rep']['job']['wec'] = 'rep';
        }
    }
    
    //$this -> mTemplates['art']['det']['des'] = 'rep';
    //$this -> mTemplates['rep']['det']['des'] = 'rep';
    //$this -> mTemplates['adm']['det']['des'] = 'rep';
    //$this -> mTemplates['mis']['det']['des'] = 'rep';
    //$this -> mTemplates['sec']['det']['des'] = 'rep';

    ##################

    //-- START: Projekte - Job Maske
    $this -> mTemplates['pro'] = array(
      'job' => array(
        'ids' => 'pro',
      ),
    );

    $this -> mTemplates['sub'] = array(
      'job' => array(
        'ids' => 'rep',
      ),
    );
    //-- ENDE: Projekte - Job Maske

    ##################

    // im Archiv sollen die gleichen Masken erscheinen, wie in den aktiven Jobs
    $this -> mArchivTemplates  = $this -> mTemplates;

  }

}