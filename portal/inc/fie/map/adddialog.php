<?php
class CInc_Fie_Map_Adddialog extends CCor_Ren {

    public function __construct($aIsCore = 0, $aMapId = 0) {
        $this->mNativeField = $aIsCore ? 'native_core' : 'native';
        $lMapId = intval($aMapId);
        if (!empty($lMapId)) {
            $this->excludeMap($lMapId);
        }
    }

    protected function excludeMap($aMap) {
        $this->mAlreadySelected = CCor_Res::extract('alias', 'alias', 'fiemap', array('map_id' => $aMap));
    }

    protected function getCont()
    {
        $lRet = '';
        $lRet .= '<script>jQuery(function(){});</script>';
        $lRet .= '<div class="map-parent">';
        $lRet .= '<input class="term w200" />' . NB;
        $lRet .= '<button onclick="Flow.FieldMap.search(this)">Search</button>' . NB;
        $lRet .= '<button onclick="Flow.FieldMap.clearSearch(this)">Show all</button>' . NB;
        $lRet .= '<button onclick="Flow.FieldMap.togSearch(this,\'.bc-tg\')" data-cur="0">Toggle Natives</button>' . NB;
        $lRet .= '<span class="bc-togcap">all</span>';
        $lRet .= BR . BR;
        $lRet .= '<div style="width:100%; height:400px; overflow-y:auto; overflow-x:hidden">';
        $lRet .= '<table class="tbl" cellpadding="4" style="width:95%">';

        $lRet .= '<tr>';
        $lRet.= '<td class="th2 w16">&nbsp;</td>';
        $lRet.= '<td class="th2">Name</td>';
        $lRet.= '<td class="th2">Alias</td>';
        $lRet.= '<td class="th2">Native</td>';
        $lRet.= '<td class="th2">Type</td>';
        $lRet .= '</tr>';

        $lNum = 0;
        $lFie = CCor_Res::getByKey('alias', 'fie');
        //var_dump($lFie);
        foreach ($lFie as $lKey => $lRow) {
            if (in_array($lKey, $this->mAlreadySelected)) {
                continue;
            }
            $lNat = $lRow[$this->mNativeField];
            $lCls = empty($lNat) ? 'bc-tg1' : 'bc-tg2';
            $lRet.= '<tr class="hi val cp bc-tg '.$lCls.'">';
            $lRet.= '<td class="td2"><input class="bc-cb" type="checkbox" value="'.$lRow['id'].'"></td>';
            $lRet.= '<td class="td1">'.htm($lRow['name_'.LAN]).'</td>';
            $lRet.= '<td class="td2">'.htm($lRow['alias']).'</td>';
            $lRet.= '<td class="td1">'.htm($lNat).'</td>';
            $lRet.= '<td class="td1">'.htm($lRow['typ']).'</td>';
            $lRet.= '</tr>';
            $lNum++;
        }


        $lRet .= '</table>';
        $lRet .= '</div>';

        $lRet .= '</div>';
        return $lRet;
    }

}
