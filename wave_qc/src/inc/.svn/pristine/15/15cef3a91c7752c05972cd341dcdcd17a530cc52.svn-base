<?php
class CInc_Pck_Mod_Form extends CHtm_Form {
  
  public $colCount; // Column Count
  public $mId; //Pickliste Source
  public static $col = Array(); //Columns Info
    
  
  public function __construct($aAct, $aCaption, $aCancel = NULL,$aArrFields, $aArrFieldCaptions) {
    parent::__construct($aAct, $aCaption, $aCancel);
    $lFields = $aArrFields;
    $lCaptions = $aArrFieldCaptions;
    
    
    $lArr[0] = '[All]';
    $lArr[MID] = MANDATOR_NAME;
    
    $this -> addDef(fie('mand', lan('lib.mand'), 'select', $lArr ));
    
    foreach ($lFields as $lRow){
      $this -> addDef(fie('col'.$lRow['col'], $lCaptions[$lRow['alias']]));
    }
    $this -> setVal('mand', MID); // als Default bei neuem Eintrag
    
  }

  public function load($aId) {
    $lId = intval($aId);
    $this->mId = $lId;
    $lQry = new CCor_Qry('SELECT * FROM al_pck_items WHERE id='.$lId);
    if ($lRow = $lQry -> getAssoc()) {
      $this -> assignVal($lRow);
      $this -> setParam('sid', $lId);
      $this -> setParam('old[id]', $lId);
      $this -> setParam('val[id]', $lId);
    } else {
      $this -> msg('Record not found', mtUser, mlError);
    }
  }
  
  public function getColumn(){
    $this -> colCount = 0; # for counter;
    
    $lQry = new CCor_Qry('SELECT * FROM al_pck_columns WHERE pck_id=1 ORDER BY position');
    
    foreach ($lQry as $lRow) {
      $this-> col[] = $lRow;
      if ($lRow['hidden'] == 'N') $this -> colCount++;
      
    }
  }
  
  public function getFieldDefs() {
      $lCaption = 'name_'.LAN;
      $lSql = "SELECT alias,$lCaption FROM al_fie WHERE mand=".MID." AND alias IN(";
      foreach ($this -> col as $lCol) {
        $lSql.= '"'.$lCol['alias'].'",';
      }
      $lSql = substr($lSql, 0, -1);
      $lSql.= ')';
      $lQry = new CCor_Qry($lSql);
      foreach ($lQry as $lRow) {
         $this -> FieldCaption[$lRow['alias']] = $lRow[$lCaption];
      }
  }

}