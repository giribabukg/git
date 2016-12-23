<?php
class CInc_Utl_Wec_Xfdf extends CCor_Dat {

  protected $mXml;
  protected $mSort;
  protected $mNotes;
  // Annotation types which is taken in portal history.
  // E.g 'text','square','circle','ink','line','highlight'.
  // Webcenter 7 had only 'text' and 'square'
  public    $mWecAnnotationTypes = Array();

  // dieses Objekt kann immer nur einmal pro XML-Quelle aufgerufen werden!
  public function __construct($aXml, $aDate, $aSort) {
    $this -> mXml = $aXml; // erhält zuerst die File-Adresse bzw. den String!
    $this -> mDate = $aDate; //Sendedatum d.eMail
    $this -> mSort = $aSort;
    $this -> mNotes = array();
    $this -> mWecAnnotationTypes = CCor_Cfg::get('wec.annotation.types',array('text','square','circle','ink','highlight','line'));
    # var_dump($this);
  }

  public function parse($aWoherXml) {
    if (empty($this -> mXml)) {
      $this -> dbg('Empty XML', mlWarn);
      return FALSE;
    }
    try {
      switch ($aWoherXml) {
        case 'file': // erhalte das xml als File: $this -> mXml enthält noch die Adresse!
          $lXml = simplexml_load_file($this -> mXml);
          break;
        case 'string': // erhalte das xml als String
          $lXml = simplexml_load_string($this -> mXml);
          break;
        default:
      }
      # var_dump($lXml);
      # echo '<br>';
      
      $this -> mXml = $lXml;// enthält erst jetzt das xml
      if($lXml !== false){
				$this -> insert(date('Y-m-d'));
				return $this -> mNotes;
			}else{
				$this -> dbg('XML not readable', mlWarn);
				return FALSE;
			}
    } catch (Exception $lExc) {
      $this -> msg($lExc -> getMessage(), mtUser, mlError);
      $this -> msg($lExc -> getMessage(), mtApi, mlError);
      return FALSE;
    }
  }

  public function checkAnnots($aXml, $aPos) {
    $aPos++;
    $lSp = str_repeat('&nbsp;', 3 * $aPos);
    foreach ($aXml->children() as $lVal) {
      if (is_object($lVal)) {
       # echo $lSp.'Classname: '.get_class($lVal).'<br>';
       $lName = &$lVal -> getName();
       # echo $lSp.'Name: '.$lName.'<br>';
       if (strcasecmp($lName, 'annots') == 0) {
         $this -> checkNote($lVal, $aPos + 5);
       } else {
         $this -> checkAnnots($lVal, $aPos);
       }
      }
    }
  }

  public function checkNote($aXml, $aPos) {
    $aPos++;
    $lSp = str_repeat('&nbsp;', 3*$aPos);
    foreach ($aXml->children() as $lVal) {
      
      if(is_object($lVal)) {
       # echo $lSp.'Classname: '.get_class($lVal).'<br>';
       $lName = &$lVal -> getName();
       # echo $lSp.'Name: '.$lName.'<br>';
       
       // Check Which Annotation Types are taken in Portal.
       if (in_Array($lName, $this -> mWecAnnotationTypes)){
         $this -> parseNote($lVal, $aPos+5);
       }else {
         $this -> checkAnnots($lVal, $aPos);
       }
              
       //if (strcasecmp($lName, 'text') == 0) {
         //$this -> parseNote($lVal, $aPos+5);
       //} elseif (strcasecmp($lName, 'square') == 0) {
         // Rechteck-Annotation
        // $this -> parseNote($lVal, $aPos+5);
       //} elseif (strcasecmp($lName, 'circle') == 0) {
         // Rechteck-Annotation
        // $this -> parseNote($lVal, $aPos+5);
       //} else {
         //$this -> checkAnnots($lVal, $aPos);
      // }
      }
    }
  }

  public function parseNote($aXml, $aPos) {
    $aPos++;
    $lSp = str_repeat('&nbsp;', 3*$aPos);
    $lNote = array();
    $lNr = '';
    $lUser = '';
    $lCreateDate = '';
    $lDate = '';
    $lSortCreateDate = '';
    $lNoteKey = '';
    $lInReplyto = '';
    foreach($aXml->attributes() as $lName => $lVal) {
        # echo $lName.'="'.$lVal.'<br>';
        if (strcasecmp($lName, 'title') == 0) {
          # ----------------------------------------------
          # Erste Zahl ist die Nummerierung
          # letzter Text in KLammern ist der User
          # ----------------------------------------------
          $lp1 = strpos($lVal, ' ');
          $lx2 = strrpos($lVal, '(');
          $lx3 = strrpos($lVal, ')');
          
          $lNr = substr($lVal,0,$lp1);
          $lUser = substr($lVal,$lx2+1,$lx3-$lx2-1);
          
          $lNote['user'] = $lUser;
          $lQry = new CCor_Qry('SELECT uid FROM al_usr_info WHERE iid="wec_usr" and val="'.$lUser.'"');
          if ($lRow = $lQry -> getDat()) {
            $lNote['uid'] = $lRow['uid'];
            $lNote['wecuserid'] = $lUser;
            $lQry = new CCor_Qry('SELECT concat(lastname,", ",firstname) as user FROM al_usr WHERE id='.$lRow['uid']);
            if ($lRow = $lQry -> getDat()) {
              $lNote['user'] = $lRow['user'];
            }
          }
                    
          
          $lNote['nr'] = $lNr;
          
          # echo 'Nummer=--'.$lNr.'--<br>';
          # echo 'User=--'.$lUser.'--<br>';
          
        } elseif (strcasecmp($lName, 'creationdate') == 0) {
          # ----------------------------------------------
          # Erstellungsdatum/Zeit
          #  "D:20100401233806+02'00'
          # ----------------------------------------------
          $lSortCreateDate = substr($lVal,2,14);
          $lCreateDate = substr($lVal,2,4).'-'.substr($lVal,6,2).'-'.substr($lVal,8,2).' '.substr($lVal,10,2).':'.substr($lVal,12,2).':'.substr($lVal,14,2);
          $lNote['created'] = $lCreateDate;
          # echo 'CreateDate=--'.$lCreateDate.'--<br>';
        } elseif (strcasecmp($lName, 'date') == 0) {
          # ----------------------------------------------
          # Änderungsdatum/Zeit
          # ----------------------------------------------
          $lDate = substr($lVal,2,4).'-'.substr($lVal,6,2).'-'.substr($lVal,8,2).' '.substr($lVal,10,2).':'.substr($lVal,12,2).':'.substr($lVal,14,2);
          $lNote['date'] = $lDate;
          # echo 'Date=--'.$lDate.'--<br>';
        } elseif (strcasecmp($lName, 'name') == 0) {
          $lNoteKey = ''.$lVal;
          $lNote['name'] = $lNoteKey;
          # echo 'NoteKey=--'.$lNoteKey.'--<br>';
        } elseif (strcasecmp($lName, 'subject') == 0) {
          $lTyp = ''.$lVal;
          $lNote['typ'] = $lTyp;
          # echo 'Typ=--'.$lTyp.'--<br>';
        } elseif (strcasecmp($lName, 'inreplyto') == 0) {
          $lInReplyto = ''.$lVal;
          $lNote['referto'] = $lInReplyto;
          # echo 'InReplyto=--'.$lInReplyto.'--<br>';
        } else {
        }
    }
    
    $lSorter = 'N'.str_pad($lNr, 10 ,'0', STR_PAD_LEFT).'D'.$lSortCreateDate;
    if ($this -> mSort != '') {
      $lSorter = $lNote[$this -> mSort]. $lSorter;
    }
    $lNote['sorter'] = $lSorter;
    # echo 'Sorter=--'.$lSorter.'--<br>';
       
    foreach ($aXml->children() as $lVal) {
      
      if(is_object($lVal)) {
       # echo $lSp.'Classname: '.get_class($lVal).'<br>';
       $lName = &$lVal -> getName();
       # echo $lSp.'Name: '.$lName.'<br>';
       if (strcasecmp($lName, 'contents-richtext') == 0) {
         $lText = $lVal->asXML();
         // var_dump($lText);
         // $this -> dbg('<pre>'.$lText.'</pre>');
         $lText = str_replace('</p>', LF, $lText);
         // $lText = utf8_decode(strip_tags($lText));
         // $lText = utf8_encode(strip_tags($lText));
         $lText = strip_tags($lText);
         
         // var_dump($lText);
         $lNote['comment'] = $lText;
         # echo strip_tags($lText);
         # echo '<br>';
       
       } elseif (in_Array($lName, $this -> mWecAnnotationTypes)){
         $this -> parseNote($lVal, $aPos);
       } else {
         $this -> checkAnnots($lVal, $aPos);
       }
       //elseif (strcasecmp($lName, 'square') == 0) {
         // Rechteck-Annotation
         //$this -> parseNote($lVal, $aPos);
      // } elseif (strcasecmp($lName, 'circle') == 0) {
         // Rechteck-Annotation
        // $this -> parseNote($lVal, $aPos);
       //} else {
         //$this -> checkAnnots($lVal, $aPos);
       //}
      }
    }
    # echo 'Sorter=--'.$lSorter.'--<br>';
    $this -> mNotes[$lSorter] = $lNote;
  }

  public function insert($aDate) {
    # echo '<pre>OK<br>';
    $this -> checkAnnots($this -> mXml, 0);
    ksort($this -> mNotes);
    # var_dump($this -> mNotes);
    # echo '</pre>OK!<br>';
	}

}