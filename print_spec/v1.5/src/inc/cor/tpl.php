<?php
/**
 * @package cor
 * @version $Rev: 11962 $
 * @date $Date: 2016-01-06 18:37:15 +0800 (Wed, 06 Jan 2016) $
 * @author $Author: gemmans $
 */
class CInc_Cor_Tpl extends CCor_Ren {

  protected $mDoc = '';
  protected $mLoaded   = FALSE;
  protected $mPat      = array();
  protected $mStartTag = '{';
  protected $mEndTag   = '}';

  public function open($aFilename) {
    if (is_file($aFilename)) { //Returns TRUE if the filename exists and is a regular file, FALSE otherwise.
      try {
        $this -> setDoc(file_get_contents($aFilename));
      } catch (Exception $lExc) {
        $this -> dbg($lExc -> getMessage(), mlError);
      }
    }  else {
      $this -> dbg("The file '".$aFilename."' doesn't exist or is no file!", mlError);
    }
  }

  public function getProjectFilename($aFilename) {
    $lUsr = CCor_Usr::getInstance();
    $lExt = strtolower(pathinfo($aFilename, PATHINFO_EXTENSION));
    $lMandIsDefined = defined('MAND_PATH_HTM');
    if(in_array($lExt, array('htm','css'))) {
      if ($lMandIsDefined) {
        $lRetMand    = MAND_PATH_HTM.$aFilename;
      }
      $lRetCust    = CUST_PATH_HTM.$aFilename;
      $lRetTheme   = THEME_PATH_HTM.$aFilename;
      $lRetDefault = 'htm/default/'.$aFilename;
      $lRet = 'htm/'.$aFilename;
    } else {
      if ($lMandIsDefined) {
        $lRetMand    = MAND_PATH_IMG.$aFilename;
      }
      $lRetCust    = CUST_PATH_IMG.$aFilename;
      $lRetTheme   = THEME_PATH_IMG.$aFilename;
      $lRetDefault = 'img/default/'.$aFilename;
      $lRet = 'img/'.$aFilename;
    }
    if ($lMandIsDefined && file_exists($lRetMand)) {
      $lRet = $lRetMand;
    } elseif (file_exists($lRetCust)) {
      $lRet = $lRetCust;
    } elseif(file_exists($lRetTheme)) {
      $lRet = $lRetTheme;
    } elseif (file_exists($lRetDefault)) {
      $lRet = $lRetDefault;
    }
    return $lRet;
  }

  public function openProjectFile($aFilename) {
    $this -> open($this -> getProjectFilename($aFilename));
  }

  public function setDoc($aCnt) {
    $this -> mDoc = $aCnt;
    $this -> mLoaded = TRUE;
  }

  protected function addTags($aPat) {
    return $this -> mStartTag.$aPat.$this -> mEndTag;
  }

  public function setRep($aPattern, $aReplacement) {
    $this -> mPat[$aPattern] = $aReplacement;
  }

  public function addRep($aPattern, $aReplacement) {
    $lRep = (isset($this -> mPat[$aPattern])) ? $this -> mPat[$aPattern] : '';
    $this -> setRep($aPattern, $lRep.$aReplacement);
  }

  public function setPat($aPattern, $aReplacement) {
    $aReplacement = $this -> getTimeModified($aReplacement);

    $this -> setRep($this -> addTags($aPattern), $aReplacement);
  }

  public function addPat($aPattern, $aReplacement) {
    $this -> addRep($this -> addTags($aPattern), $aReplacement);
  }

  protected function getTimeModified($aReplacement) {
    $lClient = array('js','css', 'json');
    $lExt = substr(strrchr($aReplacement,'.'),1);

    if(in_array($lExt, $lClient)) {
      $aReplacement = $aReplacement . "?" . filemtime($aReplacement);
    }

    return $aReplacement;
  }

  protected function getCont() {
    if (!empty($this -> mFpa)) {
      foreach($this -> mFpa as $lVal) {
        if (!isset($this -> mPat[$this -> addTags($lVal)]))
          $this -> dbg('Pattern '.$lVal.' not replaced', mlWarn);
      }
    }
    return strtr($this -> mDoc, $this -> mPat);
  }

  public function clear() {
    $this -> mPat = array();
    $this -> mFpa = array();
  }

  function preCompile() {
    $this -> mDoc = strtr($this -> mDoc, $this -> mPat);
    $this -> clear();
  }

  function findPatterns($aPref = '') {
    $lRet = array();
    $lOpen = FALSE;
    $lOld  = 0;
    $lPos = strpos($this -> mDoc, $this -> mStartTag.$aPref);
    $lLen = strlen($aPref);
    while ($lPos !== FALSE) {
      if ($lOpen) {
        $lPat = substr($this -> mDoc, $lOld + $lLen + 1, $lPos - $lOld - $lLen -1);
        $lRet[] = $lPat;
        $this -> onPattern(substr($this -> mDoc, $lOld + 1, $lPos - $lOld -1));
        $lPos = strpos($this -> mDoc, $this -> mStartTag.$aPref, $lPos);
      } else {
        $lOld = $lPos;
        $lPos = strpos($this -> mDoc, $this -> mEndTag, $lPos);
      }
      $lOpen = !$lOpen;
    }
    if ('' == $aPref) {
      $this -> mFpa = $lRet;
    }
    return $lRet;
  }

  /**
  * Callback function of findPatterns
  * Will be called by findPatterns(), whenever a pattern is encountered in the
  * template
  *
  * @param string $aPat Name of the pattern found
  */

  protected function onPattern($aPat) {
    # addDbgMsg($aPat);
  }

  public function patExists($aPat) {
    return in_array($aPat, $this -> mFpa);
  }

  function setLang($aLang = NULL) {
    $lLoc = (NULL == $aLang) ? LAN : $aLang;
    $lLan = CCor_Lang::getInstance($lLoc);
    $lArr = $this -> findPatterns('lan.');
    if (empty($lArr)) return;
    foreach ($lArr as $lVal) {
      $lKey = 'lan.'.$lVal;
      $this -> setPat($lKey, $lLan->get($lVal));
    }
  }

  function setImg($aImg = NULL) {
    $lArr = $this -> findPatterns('img/');
    if (empty($lArr)) {
      return;
    }
    foreach ($lArr as $lVal) {
      $lKey = 'img/'.$lVal;
      $this -> setPat($lKey, getImgPath($lKey));
    }
  }

  function setImage($aImg = NULL) {
    $lArr = $this -> findPatterns('job-image');
    if (empty($lArr)) {
      return;
    }
    foreach ($lArr as $lVal) {
      $lKey = 'job-image';
      $this -> setPat($lKey, img($this -> mImg, array('style' => 'float:left','alt' => $this -> mTitle)));
    }
  }
}