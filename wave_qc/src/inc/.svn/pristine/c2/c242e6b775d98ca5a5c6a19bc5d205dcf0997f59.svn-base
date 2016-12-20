<?php
class CInc_Fie_Map_Sub extends CCor_Ren {

  public function __construct($aMapId) {
    $this->mMapId = intval($aMapId);
    $this->loadItems();
    $this->mCol['alias'] = 'Alias';
    $this->mCol['native'] = 'Native';
    $this->mCol['default_value'] = 'Default';

    $this->mValidate = CCor_Cfg::get('validate.available');
    if ($this->mValidate) {
      $this->mCol['validate_rule'] = lan('validate.rule');
      $this->mValid = CCor_Res::extract('id', 'name', 'validate');
    }
  }

  protected function loadItems() {
    $lSql = 'SELECT * FROM al_fie_map_items WHERE map_id='.$this->mMapId;
    $lSql.= ' ORDER BY alias';
    $this->mIte = array();
    $lQry = new CCor_Qry($lSql);
    foreach ($lQry as $lRow) {
      $this->mIte[] = $lRow;
    }
  }

  protected function getCont() {
    $lRet = '';
    $lRet.= $this->getTable();
    return $lRet;
  }

  protected function getTable() {
    $lRet = '';
    $lRet.= '<button onclick="Flow.FieldMap.newItem('.$this->mMapId.')">New Field</button>'.NB;
    $lRet.= '<button onclick="Flow.FieldMap.addJobFields('.$this->mMapId.')">Add Job Fields</button>';
    $lRet.= BR.BR;
    $lRet.= '<table class="tbl" cellpadding="4">';

    $lRet.= '<tr>';
    foreach ($this->mCol as $lKey => $lVal) {
      $lRet.= '<td class="th2 w150">';
      $lRet.= htm($lVal);
      $lRet.= '</td>';
    }
    $lRet.='<td class="th2">&nbsp;</td>';
    $lRet.= '</tr>'.LF;

    foreach ($this->mIte as $lRow) {
      $lId = $lRow['id'];
      $lRet.= '<tr data-id="'.$lId.'" class="hi">';
      foreach ($this->mCol as $lKey => $lVal) {
        $lRet.= '<td class="cp" onclick="Flow.FieldMap.editItem('.$this->mMapId.','.$lId.')">';
        $lValue = $lRow[$lKey];
        if ($lKey == 'validate_rule') {
          if (empty($lValue)) {
            $lValue = '';
          } elseif (isset($this->mValid[$lValue])) {
            $lValue = $this->mValid[$lValue];
          }
        }
        $lRet.= htm($lValue);
        $lRet.= '</td>';
      }

      $lRet.= '<td class="w16 nav" onclick="Flow.FieldMap.deleteItem('.$this->mMapId.','.$lId.')">Delete</a></td>';
      $lRet.= '</tr>'.LF;
    }
    $lRet.= '</table>';
    return $lRet;
  }


}
