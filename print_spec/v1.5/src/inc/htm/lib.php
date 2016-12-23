<?php
/**
 * Html-Bausteine: Funktionen
 *
 * Description
 *
 * @package    HTM
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 11221 $
 * @date $Date: 2015-11-04 21:51:28 +0800 (Wed, 04 Nov 2015) $
 * @author $Author: pdohmen $
 */

/**
 * Convert a string with htmlentities
 * This may change in the future due to unicode stuff
 *
 * @param string $aStr String to convert
 * @return string Converted string
 */
function htm($aStr) {
  $lRet = htmlentities($aStr, ENT_QUOTES, mb_detect_encoding($aStr.'a' , 'UTF-8, ISO-8859-1'));
  $lRet = str_replace('&amp;#','&#',$lRet);
  return $lRet;
 }

/**
 * Converts all entities back to normal
 * This may change in the future due to unicode stuff
 *
 * @param string $aStr String to convert
 * @return string Converted string
 */
function deHtm($aStr) {
  return html_entity_decode($aStr, ENT_QUOTES);
 //return $aStr;
}

function esc($aValue) {
  return '"'.mysql_real_escape_string($aValue).'"';
}

function backtick($aValue) {
  return '`'.$aValue.'`';
}

function url($aStr) {
  return urlencode($aStr);
}

/**
 * Return str_replace(...), but replace only once
 *
 * @param string $lNeedle   String to look for
 * @param string $lReplace  String to replace
 * @param string $lHaystack String, where you want to replace one occurence
 * @return string
 */
function str_replace_once($lNeedle, $lReplace, $lHaystack) {
  // Looks for the first occurence of $lNeedle in $lHaystack
  // and replaces it with $lReplace.
  $lPos = strpos($lHaystack, $lNeedle);
  if (FALSE === $lPos) {
    // Nothing found
    return $lHaystack;
  }
  return substr_replace($lHaystack, $lReplace, $lPos, strlen($lNeedle));
}

/**
 * Return correct Path and HTML-tag of an image
 *
 * @param string $aSrc URI of the image file
 * //@param array $aAtt (optional) Key/Value array for HTML-Attributes, e.g. array('style' => 'float:left');
 * @return unknown
 */
function getImgPath($aSrc) {
  $lImgDir = array(MAND_PATH_IMG, CUST_PATH_IMG, THEME_PATH_IMG, 'img'.DS);
  $lFound = FALSE;
  $lSrc = $aSrc;
  $lext = strtolower(pathinfo($lSrc, PATHINFO_EXTENSION));
  if (in_array($lext, array('gif','png','ico'))) {
    if (0 === strpos($lSrc,'mand/mand_'.MID.'/')) {
      $lSrc = str_replace_once('mand/mand_'.MID.'/', '', $lSrc);
    }
    if (0 === strpos($lSrc,'cust/')) {
      $lSrc = str_replace_once('cust/', '', $lSrc);
    }
    if (0 === strpos($lSrc,'img/')) {
      $lSrc = str_replace_once('img/', '', $lSrc);
    }

    foreach($lImgDir as $lDir) {
      if (!$lFound) {
        $lFile = $lDir.$lSrc;
        if (FALSE !== strpos($lFile,'LAN/')) {
          $lFile = str_replace('LAN/', LAN.'/', $lFile);
        }
        if (file_exists($lFile)) {
          $lFound = TRUE;
        } else {
          $lFile = str_replace('/'.LAN.'/', '/', $lFile);
          if (file_exists($lFile)) {
            $lFound = TRUE;
          }
        }
      }//end_if (!$lFound)
    }//end_foreach($lImgDir as $lDir)

  }//end_if (in_array($lext, array('gif','png')))

  if ($lFound) {
    return $lFile;
  } else {
    return '';
  }
}

/**
 * Return correct Path and HTML-tag of an image
 *
 * @param string $aSrc URI of the image file
 * @param array $aAtt (optional) Key/Value array for HTML-Attributes, e.g. array('style' => 'float:left');
 * @return unknown
 */
function img($aSrc, $aAtt = array(), $aIsImg = TRUE) {
  $lImg = getImgPath($aSrc);
  if (!empty($lImg)) {
    $lSrc = $lImg;
  } else {
    $lSrc = "img".DS."d.gif";
    $aAtt['width'] = '16';
    $aAtt['alt'] = '';
  }

  if (!isset($aAtt['alt'])) {
    $aAtt['alt'] = '';
  }
  $lRet = '<img src="'.$lSrc.'"';
  foreach ($aAtt as $lKey => $lVal) {
    $lRet.= ' '.$lKey.'="'.htm($lVal).'"';
  }
  $lRet.= ' />';
  return $lRet;
}
/*function img($aSrc, $aAtt = array(), $aIsImg = TRUE, $aSrc2 = '', $aDir = '') {
  if (empty($aSrc2) {
    $lImg = getImgPath($aSrc);
    if (!empty($lImg)) {
      $lSrc = $lImg;                 // -> gefunden
    } else {
      $lSrc = CUST_PATH_IMG."d.gif";  // default!! wenn nicht gefunden
      $aAtt['width'] = '16';
      $aAtt['alt'] = '';
    }

    if (!isset($aAtt['alt'])) {
      $aAtt['alt'] = '';
    }
    $lRet = '<img src="'.$lSrc.'"';
    foreach ($aAtt as $lKey => $lVal) {
      $lRet.= ' '.$lKey.'="'.htm($lVal).'"';
    }
    $lRet.= ' />';
    return $lRet;

  } else {
    if (!empty($aDir)) {
      $lSrc = $aSrc.$aDir.$aSrc2;
      #$lWithDir = TRUE;
      $lImg = getImgPath($lSrc);
      if (!empty($lImg)) {
        $lSrc = $lImg;            // -> gefunden
      } elseif ($lWithDir) {
        $lSrc = $aSrc.$aSrc2;
        $lImg = getImgPath($lSrc);
        if (!empty($lImg)) {
          $lSrc = $lImg;
        }
  $lImg = getImgPath($aSrc);
  if (!empty($lImg)) {
    $lSrc = $lImg;
  } else {
    $lSrc = CUST_PATH_IMG."d.gif";
    $aAtt['width'] = '16';
    $aAtt['alt'] = '';
  }

  if (!isset($aAtt['alt'])) {
    $aAtt['alt'] = '';
  }
  $lRet = '<img src="'.$lSrc.'"';
  foreach ($aAtt as $lKey => $lVal) {
    $lRet.= ' '.$lKey.'="'.htm($lVal).'"';
  }
  $lRet.= ' />';
  return $lRet;
}*/
/**
 * Return HTML-Tag of a button with caption, an action
 *
 * @param string $aCaption Buttoncaption. Will be htmlentity-converted
 * @param string $aAction (optional) JavaScript onclick-action
 * @param string $aImg (optional) URI of icon to display
 * @param string $aType (optional) HTML-type of button (e.g. button, reset, submit)
 * @return string The button's HTML-representation
 */
function btn($aCaption, $aAction = '', $aImg = '', $aType = 'button', $aAttr = array(), $aClass = 'btn') {
  $lTag = new CHtm_Tag('button');
  $lTag -> setAtt('class', $aClass);
  $lTag -> setAtt('type', $aType);
  if (!empty($aAction)) {
    $lTag -> setAtt('onclick', $aAction);
  }

  if (!empty($aAttr)) {
    foreach ($aAttr as $lKey => $lVal) {
      $lTag -> setAtt($lKey, $lVal);
    }
  }
  $lRet = $lTag -> getTag().LF;
  if (empty($aImg)) {
    $lRet.= htm($aCaption);
    $lRet.= '</button>'.LF;
    return $lRet;
  }
  $lRet.= '<div class="al">'.LF;
  $lRet.= '<table cellpadding="2" cellspacing="0" border="0" class="al w25p"><tr>'.LF;
  //Detect wether we have given a Path for an Image or a finished html Element
  //Just for the switch between Icon Based Buttons (CSS-Sprites) and Image Based Buttons
  if($aImg[0] !== "<") {
    $aImg = img($aImg);
  }
  $lRet.= '<td>'.$aImg.'</td>';
  $lRet.= '<td class="nw al" style="text-align:left">'.htm($aCaption).'</td>';
  $lRet.= '</tr></table>'.LF;
  $lRet.= '</div>'.LF;

  $lRet.= '</button>'.LF;
  return $lRet;
}

function hidden($aId, $aName, $aValue) {
  $lTag = new CHtm_Tag('input');
  $lTag -> setAtt('id', $aId);
  $lTag -> setAtt('name', $aName);
  $lTag -> setAtt('value', $aValue);
  $lTag -> setAtt('type', 'hidden');
  $lRet = $lTag -> getTag().LF;
  return $lRet;
}

/**
 * Concatenate two strings with optional separator
 * Will only add separator if both parameter strings are non-empty
 *
 * @param string $aTxt1 First part of string
 * @param string $aTxt2 Second part of string
 * @param string $aSep Separator string (could be ', ')
 * @return string Concatenated string
 */

function cat($aTxt1, $aTxt2, $aSep = ' ') {
  $lRet = $aTxt1;
  if (('' != $aTxt1) and ('' != $aTxt2)) {
    $lRet.= $aSep;
  }
  $lRet.= $aTxt2;
  return $lRet;
}

/**
 * Delete last x characters of a string
 * Convenience string function
 *
 * @param string $aStr String to be stripped
 * @param int $aCount Number of characters to delete from tail of string
 * @return string
 */

function strip($aStr, $aCount = 1) {
  return substr($aStr, 0 , -$aCount);
}

function strip_job(&$aValue) {
  $aValue = ltrim($aValue, "job-");
}

/**
 * Format a float number to Currency format
 *
 * @param float $aFloat Float to be converted
 * @return string
 */

function fmtCur($aFloat, $aDecimals = 2) {
  return number_format($aFloat,$aDecimals,'.',',');
  //return number_format($aFloat,2,',','.');
}

function strToFloat($aStr) {
  $lRet = strtr($aStr,',','.');
  return floatval($lRet);
}


/**
 * Convenience function: create a field-definition on the fly
 * Used for admin-forms, data models etc.
 *
 * @param string $aAlias Unique internal name (used as array key in field lists, should be valid MySQL-fieldname)
 * @param string $aCaption Optional caption (for form and columnn header captions)
 * @param string $aType Internal input type (see CHtm_Fie_Reg for a list of available types)
 * @param mixed $aParam String, serialized array or array. Options, e.g. type and filter for ressource-selects
 * @param mixed $aAttr Serialized array or array: HTML-Attributes
 * @return CCor_Dat Field definition object
 */

function fie($aAlias, $aCaption = NULL, $aType = 'string', $aParam = NULL, $aAttr = NULL, $aAdd = NULL) {
  $lRet = new CCor_Dat();
  $lRet['alias']   = $aAlias;
  $lRet['typ']     = $aType;
  $lRet['name_'.LAN] = $aCaption;
  $lRet['param']   = $aParam;
  $lRet['attr']    = $aAttr;
  if (!empty($aAdd)) {
    $lRet -> addValues($aAdd);
  }
  return $lRet;
}

function __unserialize($aString) {
  $lRet = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $aString);
  return unserialize($lRet);
}

/**
 * Convert serialized string to Array
 *
 * @param mixed $aPar Either array or serialized array string
 * @return array
 */

function toArr($aPar) {
  if (empty($aPar)) {
    return array();
  }
  if (is_array($aPar)) {
    return $aPar;
  } else {
    if ($aPar instanceof CCor_Dat) {
      return $aPar;
    }
    $aPar = str_replace(array('\"',"\'"), array('"',"'"), $aPar);
    #echo '<pre>---lib.php---';var_dump($aPar,'#############');echo '</pre>';
    if (is_string($aPar)) {
      try {
        return __unserialize($aPar);//TROTZ try: Falls der übergebene String nicht deserialisierbar ist, wird FALSE zurück gegeben und E_NOTICE produziert.
      } catch (Exception $e) {
        CCor_Msg::add($e, mtPhp, mlError);
        return array();
      }
    }
  }
}

function collect($aArr, $aKey) {
  $lRet = array();
  if (empty($aArr)) return $lRet;
  foreach ($aArr as $lRow) {
    if (!isset($lRow[$aKey])) continue;
    $lRet[] = $lRow[$aKey];
  }
  return $lRet;
}

function collectVal($aArr, $aKeyField, $aValField) {
  $lRet = array();
  if (empty($aArr)) return $lRet;
  foreach ($aArr as $lRow) {
    if (!isset($lRow[$aKeyField])) continue;
    $lKey = $lRow[$aKeyField];
    $lVal = (isset($lRow[$aValField])) ? $lRow[$aValField] : NULL;
    $lRet[$lKey] = $lVal;
  }
  return $lRet;
}

/**
 * Pass either a string or CCor_Ren and always return a string
 *
 * @param mixed $aPar Either string or CCor_Ren descendant
 * @return string
 */

function toStr($aContent) {
  if ($aContent instanceof CCor_Ren) {
    return $aContent -> getContent();
  }
  return $aContent;
}


/**
 * Return new number with optional prefix
 * Mainly used for HTML-Tag-Ids
 *
 * @param string $aPrefix Optional string to use before the actual number
 * @return string The new number/ID-String
 */

function getNum($aPrefix = '') {
  global $gNum;
  if (!isset($gNum)) $gNum = 0;
  return $aPrefix.(++$gNum);
}

function incCtr($aKey) {
  global $gCtr;
  if (!isset($gCtr[$aKey])) $gCtr[$aKey] = 0;
  return ++$gCtr[$aKey];
}

function getCtr($aKey) {
  global $gCtr;
  return (isset($gCtr[$aKey])) ? $gCtr[$aKey] : 0;
}

function getSelect($aName, $aArr, $aVal = NULL, $aAttr = array()) {
  $lTag = new CHtm_Tag('select');
  $lTag -> setAtt('name', $aName);
  if (!empty($aAttr)) {
    $lTag -> addAttributes($aAttr);
  }
  $lRet = $lTag -> getTag();
  if (!empty($aArr)) {
    foreach ($aArr as $lKey => $lVal) {
      $lSel = ($lKey == $aVal) ? ' selected="selected"' : '';
      $lRet.= '<option value="'.htm($lKey).'"'.$lSel.'>';
      $lRet.= htm($lVal);
      $lRet.= '</option>'.LF;
    }
  }
  $lRet.= $lTag -> getEndTag();
  return $lRet;
}

function getGroups($aGid = ''){
  $lGru = array();
  if(!empty($aGid)) {
    $lSqlGid = ' AND a.parent_id='.$aGid;
  } else {
    $lSqlGid = '';
  }
  $lSql = 'SELECT a.id,a.parent_id,a.name FROM al_gru a WHERE a.mand IN(0,'.MID.')'.$lSqlGid.' ORDER BY a.name';
  $lQry = new CCor_Qry($lSql);

  foreach ($lQry as $lRow) {
    $lGru[$lRow['parent_id']][ $lRow['id'] ] = $lRow;
  }
  return $lGru;
}

function shortStr($aText, $aMaxChar = 30) {
  if (strlen($aText) <= $aMaxChar) return $aText;
  $lTxt = substr($aText, 0, $aMaxChar).'...';
  $aText = strtr($aText, array("\r" => '', "\n" => " "));
  return toolTip($aText).$lTxt.'</span>';
}
//Deprecated
function autoCpl($aInp, $aAct, $aParams = NULL) {
  $lRet = 'jQuery(function() {'.LF;
  $lRet .= "jQuery('#" . $aInp . "').autocomplete({";
  $lRet .= 'source:"index.php?act='.$aAct.'&dom=' . $aParams["dom"] . '",'.LF;
  $lRet .= 'minLength: 2,'.LF;
  $lRet .=  '}).data("ui-autocomplete")._renderItem = function(ul, item) {'.LF;
  $lRet .= 'return jQuery("<li></li>")'.LF;
  $lRet .= '.data("item.autocomplete", item)'.LF;
  if(isset($aParams["cust"])) {
    $lRet .= ".append(" . $aParams["cust"] . ")".LF;
  }
  else {
    $lRet .= ".append(item.value + '<span class=\"informal\" style=\"position:absolute; right:1px;\" onclick=\"Flow.Std.delChoice(' + item.label + ');\"><img src=\"img/ico/9/del.gif\">&nbsp;</span>')".LF;
  }
  $lRet .= ".appendTo(ul);".LF;
  $lRet .=  '};'.LF;
  $lRet .=  '});'.LF;

  return $lRet;
}

function jid($aJobId, $aShort = FALSE) {
  if (empty($aJobId)) return '';
  if (substr($aJobId,0,2) == 'AP') {
    return '['.intval(substr($aJobId,2)).']';
  } elseif (substr($aJobId,0,1) == 'A') {
    return '['.intval(substr($aJobId,1)).']';
  } elseif (substr($aJobId,0,1) == 'P') {
    return intval(substr($aJobId,1));
  }
  // as jid is a top level function and not a method, we cannot overwrite in cust
  // Henkel MBC specific jobid formatting:
  if (defined('CUST_PORTAL') && CUST_PORTAL == 'mbc') {
  	if (strlen($aJobId) > 8) {
  		if ($aShort) {
  			return 'H'.substr($aJobId,4,4).'-'.substr($aJobId,8);
  		}
  		return substr($aJobId,0,4).'-'.substr($aJobId,4,4).'-'.substr($aJobId,8);
  	}
  }
  return intval($aJobId);
}

// deprecated, but called in some cust/mand classes
function toolTip($aText, $aTitle='') {
  $lRet = '<span data-toggle="tooltip" data-tooltip-head="'.addslashes($aTitle).'" data-tooltip-body="'.addslashes($aText).'">';

  return $lRet;
}

function getBytes($aVal) {
  $lVal = trim($aVal);
  $lUnit = strtolower($lVal[strlen($lVal) - 1]);

  switch ($lUnit) {
    case 'g':
      $lVal *= 1024;
    case 'm':
      $lVal *= 1024;
    case 'k':
      $lVal *= 1024;
  }

  return $lVal;
}