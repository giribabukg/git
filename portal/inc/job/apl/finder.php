<?php
/**
 * Job APL Finder
 *
 * Given a job and a country, find the corresponding approval loop event to trigger
 *
 * @package    JOB
 * @copyright  Copyright (c) 2012 5Flow GmbH (http://www.5flow.eu)
 * @version $Rev: 3 $
 * @date $Date: 2012-02-21 09:50:56 +0100 (Di, 21 Feb 2012) $
 * @author $Author: g.emmans $
 */

class CInc_Job_Apl_Finder extends CCor_Obj {

  protected $mEventType;
  protected $mEvents;

  public function __construct() {

  }

  /**
   * Set the job values
   * @param CCor_Dat|array $aJob Hash of alias => value
   */
  public function setJob($aJob) {
    $this->mJob = $aJob;
    return $this;
  }

  /**
   * Set the event type filter. Only events of this type will be returned.
   * @param string $aType
   */
  public function setEventType($aType) {
    if (!is_null($this->mEventType)) {
      if ($aType != $this->mEventType) {
        $this->mEvents = NULL;
      }
    }
    $this->mEventType = $aType;
    return $this;
  }

  /**
   * Set the country filter
   * @param unknown_type $aCountry
   */
  public function setCountry($aCountry) {
    $this->mCountry = $aCountry;
    return $this;
  }

  public function setIsInternational($aFlag = TRUE) {
    $this->mIsInternational = $aFlag;
    return $this;
  }

  public function getJobValue($aAlias, $aDefault = NULL) {
    return isset($this->mJob[$aAlias]) ? $this->mJob[$aAlias] : $aDefault;
  }

  protected function loadEvents($aEventType) {
    if (isset($this->mEvents[$aEventType])) {
      return $this->mEvents[$aEventType];
    }
    $lSql = 'SELECT id,name_en FROM al_eve WHERE typ='.esc($this->mEventType).' AND mand = '.MID;
    $lQry = new CCor_Qry($lSql);

    $lRet = array();
    foreach ($lQry as $lRow) {
      $lRet[$lRow['id']] = $lRow['name_en'];
    }
    $this->mEvents[$aEventType] = $lRet;

    return $lRet;
  }

  protected function getEvents() {
    $lEventNames = $this->loadEvents($this->mEventType);
    if (empty($lEventNames)) {
      return array();
    }
    $lIds = array_keys($lEventNames);

    $lEvents = array();
    foreach ($lIds as $lEveId) {
      $lEvents[$lEveId] = array();
    }

    if (isset($this->mEventInfos[$this->mEventType])) {
      $lEvents = $this->mEventInfos[$this->mEventType];
    } else {
      $lSql = 'SELECT * FROM al_eve_infos WHERE eve_id IN ('.implode(',', $lIds).')';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
        $lVal = $lRow['val'];
        $lAlias = $lRow['alias'];
        if ($lAlias == 'suffix') continue;
        if ($lAlias == 'prefix') continue;
        if ('' == $lVal) continue;
        $lEvents[$lRow['eve_id']][$lAlias] = $lVal;
      }
      $this->mEventInfos[$this->mEventType] = $lEvents;
    }
    #$this->dump($lEvents);

    $lValidIds = array();
    foreach ($lEvents as $lEveId => $lRow) {
      $lMatch = TRUE;
	  foreach ($lRow as $lAlias => $lVal) {
		$lVal = array_filter(explode(",", $lVal));
		$lVal = array_map('trim', $lVal);
		
		$lJobVal = array_filter(explode(",", $this->getJobValue($lAlias)));
		$lJobVal = array_map('trim', $lJobVal);
		
		$lIntersect = array_values(array_intersect($lVal, $lJobVal));
		if (sizeof($lIntersect) < 1) {
		  $lMatch = FALSE;
		  BREAK;
		}
	  }
      if ($lMatch) {
        $lValidIds[$lEveId] = $lEveId;
      }
    }

    $lRet = array();
    if (empty($lValidIds)) return $lRet;
    foreach ($lValidIds as $lEveId) {
      if (isset($lEventNames[$lEveId])) {
        $lRet[$lEveId] = $lEventNames[$lEveId];
      }
    }
    return $lRet;
  }

  public function getMatchingEvents() {
    return $this->getEvents();
  }

}