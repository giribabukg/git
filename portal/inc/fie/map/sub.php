<?php
class CInc_Fie_Map_Sub extends CCor_Ren {

  public function __construct($aMapId) {
    $this->mMapId = intval($aMapId);
    $this->loadMap();
    $this->loadItems();
    $this->mCol['alias'] = 'Alias';
    if ($this->mMap['has_native']) {
      $this->mCol['native'] = 'Native';
    }
    if ($this->mMap['has_default']) {
      $this->mCol['default_value'] = 'Default';
    }
    if ($this->mMap['has_read_filter']) {
      $this->mCol['read_filter'] = 'Read Filter';
    }
    if ($this->mMap['has_write_filter']) {
      $this->mCol['write_filter'] = 'Write Filter';
    }
    $this->mValidate = CCor_Cfg::get('validate.available');
    $this->mCore = CCor_Cfg::get('core.available');

    if ($this->mValidate &&  $this->mMap['has_validate_rule']) {
      $this->mCol['validate_rule'] = lan('validate.rule');
      $this->mValid = CCor_Res::extract('id', 'name', 'validate');
    }
  }

  protected function loadMap() {
    $lSql = 'SELECT * FROM al_fie_map_master WHERE id='.$this->mMapId;
    $lQry = new CCor_Qry($lSql);
    $this->mMap = $lQry->getDat();
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

    // add item buttons
    $lRet.= '<button onclick="Flow.FieldMap.newItem('.$this->mMapId.')">New Field</button>'.NB;
    $lRet.= '<button onclick="Flow.FieldMap.addJobFields('.$this->mMapId.',0)">Add Job Fields</button>'.NB;
    if ($this->mCore) {
      $lRet .= '<button onclick="Flow.FieldMap.addJobFields(' . $this->mMapId . ',1)">Add Core Fields</button>' . NB;
    }

    // js search form
    $lRet.= '<button onclick="Flow.FieldMap.loadSub('.$this->mMapId.')">Reload</button>';
    $lRet.= '<span class="nw" style="margin-left:2em; margin-right:2em">';
    $lRet.= '<input class="term w200" />'.NB;
    $lRet.= '<button onclick="Flow.FieldMap.search(this)">Search</button>'.NB;
    $lRet.= '<button onclick="Flow.FieldMap.clearSearch(this)">Show all</button>'.NB;
    $lRet.= '</span>';

    // ex/ import buttons
    if (CFie_Validate_Mod::areWeOnGlobal()) {
      $lRet .= '<button onclick="Flow.FieldMap.sendMap(' . $this->mMapId . ')">Send Map</button>'.NB;
    }
    $lRet .= '<button onclick="Flow.FieldMap.exportMap(' . $this->mMapId . ')">Export Map</button>'.NB;
    $lRet.= BR.BR;

    // main table
    $lRet.= '<table class="tbl" cellpadding="4">';

    $lRet.= '<tr>';
    foreach ($this->mCol as $lKey => $lVal) {
      $lCls = ($lKey == 'native') ? 'w400' : 'w150';
      $lRet.= '<td class="th2 '.$lCls.'">';
      $lRet.= htm($lVal);
      $lRet.= '</td>';
    }
    $lRet.='<td class="th2">&nbsp;</td>';
    $lRet.='<td class="th2">&nbsp;</td>';
    $lRet.= '</tr>'.LF;

    $lCls = 'td1';
    foreach ($this->mIte as $lRow) {
      $lId = $lRow['id'];
      $lRet.= '<tr data-id="'.$lId.'" class="hi val">';
      foreach ($this->mCol as $lKey => $lVal) {
        $lRet.= '<td class="cp '.$lCls.'" onclick="Flow.FieldMap.editItem('.$this->mMapId.','.$lId.')">';
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
      $lRet.= '<td class="w16 nav '.$lCls.'" onclick="Flow.FieldMap.copyItem('.$this->mMapId.','.$lId.')">Copy</a></td>';
      $lRet.= '<td class="w16 nav '.$lCls.'" onclick="Flow.FieldMap.deleteItem('.$this->mMapId.','.$lId.')">Delete</a></td>';
      $lRet.= '</tr>'.LF;
      $lCls = ($lCls == 'td1') ? 'td2' : 'td1';
    }
    $lRet.= '</table>';
    $lRet.= BR.BR;

    return $lRet;
  }


}
