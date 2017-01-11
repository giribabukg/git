<?php
/**
 * Jobs: Data
 *
 *  ABSTRACT! Description
 *
 * @package    JOB
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 9446 $
 * @date $Date: 2015-07-02 11:14:53 +0100 (Thu, 02 Jul 2015) $
 * @author $Author: jwetherill $
 */
class CInc_Cms_Job extends CCor_Dat {

  /**
   * Get Job which relates to the client_key and src arguments
   * @return array - list of jobid related to client_key and src
   */
  public static function getClientKeyJobs($aType = 'product', $aClientKey = '') {
    $lRes = array();
	$lPhraseTypes = CCor_Res::extract('code', 'eve_phrase', 'crpmaster');
	
    $lSources = array_keys($lPhraseTypes, $aType); //get src for product job type
    if($aClientKey == 'NULL' || empty($aClientKey) || $lSources == FALSE) return array();
    
    $lPhraseFields = CCor_Cfg::get('job-cms.fields');
    $lClientKeyAlias = $lPhraseFields['client_key'];
    
    if (CCor_Cfg::get('job.writer.default') == 'portal') {
      foreach($lSources as $lIdx => $lSrc) {
        $lIte = new CCor_TblIte('al_job_'.$lSrc.'_'.intval(MID), TRUE);
        $lIte -> addField('jobid');
        $lIte -> addCnd('webstatus >= 10');
        $lIte -> addCnd($lClientKeyAlias.' = '.esc($aClientKey));
        if($aType == 'product') $lIte -> setLimit('1');
        
        $lRes = $lRes + $lIte -> getArray('jobid');
      }
    } else {
      $lSql = 'SELECT `jobid` FROM `al_job_shadow_'.intval(MID).'` WHERE `src` IN ("'.implode('","', $lSources).'") AND `'.$lClientKeyAlias.'`='.esc($aClientKey);
      $lQry = new CCor_Qry($lSql);
      $lRes = $lQry -> getAssocs('jobid');
    }
  
    return $lRes;
  }

  /**
   * Check if product has changed since last saved job
   */
  public static function hasProductChanged($aJobId = 0, $aCode = '') {
    $lProductJobs = self::getClientKeyJobs('product', $aCode);
    $lProductLastChange = 0;
    foreach($lProductJobs as $lProdId => $lArr) {
      $lProductLastChange = self::getLastProductChange($lProdId);
    }
    if(empty($lProductLastChange)) return FALSE;
    
    $lJobLastChange = self::getLastProductChange($aJobId);
    if(empty($lJobLastChange) && !empty($lProductLastChange)) return FALSE;
    
    $lChange = $lProductLastChange - $lJobLastChange;
  
    return ($lChange > 0) ? TRUE : FALSE;
  }
  
  /**
   * Gets the last date a job reference has changed
   * @param number $aJobId
   */
  protected static function getLastProductChange($aJobId = 0) {
    $lSql = 'SELECT MAX(`lastchange`) as `time` FROM `al_cms_ref_job` WHERE `jobid`='.esc($aJobId).' AND `type`="product"';
    $lRes = CCor_Qry::getStr($lSql);
  
    return strtotime($lRes);
  }
  
  /**
   * Get information from al_cms_ref_job table for job and content_id
   * @param number $aCid - content_id
   * @param string $aType - type of content
   * @return string $lRes - imploded array of position and metadata
   */
  public static function getJobReferenceFields($aCid = 0, $aType = 'job', $aCode = '', $aJobId = 0) {
    $lSql = 'SELECT DISTINCT `position`, `metadata`, `ntn`, `packtypes`, `group`, `status` FROM `al_cms_ref_job` WHERE `content_id`='.esc($aCid).' AND `jobid`='.esc($aJobId);
    $lRes = CCor_Qry::getArrImp($lSql);
    $lRes = ($lRes !== FALSE) ? $lRes : ',,,,0,draft';
  
    return $lRes;
  }

  /**
   * Gather product content details for given jobid to be used in updateJobProduct() function
   * @param number $aJobId
   * @return array $lRet
   */
  public static function getProductRefs($aJobId = 0) {
    $lRet = array();
  
    $lIte = new CCor_TblIte('al_cms_ref_job'); //only store master language in job ref
    $lIte -> addCnd('`jobid`='.esc($aJobId));
    $lIte -> addCnd('`type`="product"');
    $lIte -> setOrder('id');
    $lIte -> set2Order('type');
    $lRes = $lIte -> getArray();
  
    foreach($lRes as $lIdx => $lCont){
      $lRet[ $lCont['content_id'] ] = array(
          'template_id' => $lCont['template_id'],
          'group' => $lCont['group'],
          'content_id' => $lCont['content_id'],
          'type' => $lCont['type'],
          'position' => $lCont['position'],
          'metadata' => $lCont['metadata'],
          'layout' => $lCont['layout']
      );
    }
  
    return $lRet;
  }
}