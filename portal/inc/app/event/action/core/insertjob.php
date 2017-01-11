<?php
class CInc_App_Event_Action_Core_Insertjob extends CApp_Event_Action {

    public function execute() {
        $this->mJob = $this->mContext['job'];
        $lJid = $this->mJob['jobid'];

        // init alink client
        $lClient = new CApi_Core_Client();
        $lClient->loadAuthFromConfig();
        $this->mQry = new CApi_Core_Query_Createsalesorder($lClient);

        $lMap = $this->mParams['map_base'];
        if (!empty($lMap)) {
            $this->applyMap($lMap);
        }
        $lMap = $this->mParams['map_add'];
        if (!empty($lMap)) {
            $this->applyMap($lMap);
        }
        $lMap = $this->mParams['map_char'];
        if (!empty($lMap)) {
            if ('all' == $lMap) {
                $lMapArr = $this->getCharMapFromJobFields();
            } else {
                $lMapArr = $this->getCharMapFromMapId($lMap);
            }
            $lExclude = $this->mParams['map_charex'];
            if (!empty($lExclude)) {
                $lMapArr = $this->excludeMap($lMapArr, $lExclude);
            }
            $lZlp = $this->prepareZlp($lMapArr);
            if (!empty($lZlp)) {
                foreach ($lZlp as $lItm) {
                    $this->mQry->addParamPath('/SalesOrder/LineItems/Characteristics', $lItm);
                }
            }
        }
        $lSuccess = false;
        $lRes = $this->mQry->create();
        if ($lRes && $lRes->SalesOrders && $lRes->SalesOrders->SalesorderNumber) {
            $lSalesorderNumber = $lRes->SalesOrders->SalesorderNumber;
            $lRefField = 'sales_order_id';
            if (!empty($lSalesorderNumber)) {
                $lSrc = $this->mJob['src'];
                $lFac = new CJob_Fac($lSrc);
                $lMod = $lFac->getMod($lJid);
                $lMod->forceVal($lRefField, $lSalesorderNumber);
                $lSuccess = $lMod->update();
            }
        }

        // trigger success / error events?
        if ($lSuccess) {
            $lEventOk = $this->mParams['event_ok'];
            $this->trigger($lEventOk);
        } else {
            $lEventError = $this->mParams['event_error'];
            $this->trigger($lEventError);
        }
        return true;
    }

    protected function getCharMapFromJobFields() {
        $lRet = array();
        $lWriteFilter = CCor_Res::extract('alias', 'write_filter', 'fiemap', 'core.xml');
        $lFie = CCor_Res::extract('alias', 'native_core', 'fie');
        foreach ($lFie as $lAlias => $lNative) {
            if (substr($lNative, 0, 4) != 'ZLP_') {
                continue;
            }
            $lItm = array('native' => $lNative);
            $lItm['write_filter'] = isset($lWriteFilter[$lNative]) ? $lWriteFilter[$lNative] : '';
            $lRet[$lAlias] = $lItm;
        }
        return $lRet;
    }

    protected function getCharMapFromMapId($aMapId) {
        $lRet = array();
        $lFie = CCor_Res::extract('alias', 'native_core', 'fie');
        $lMap = CCor_Res::getByKey('alias', 'fiemap', array('map_id' => $aMapId));
        if (!empty($lMap)) {
            foreach ($lMap as $lAlias => $lRow) {
                if (!isset($lFie[$lAlias])) {
                    continue;
                }
                $lItm = array();
                $lItm['native'] = $lFie[$lAlias];
                $lItm['write_filter'] = $lRow['write_filter'];
                $lRet[$lAlias] = $lItm;
            }
        }
        return $lRet;
    }

    protected function prepareZlp($aMapArr) {
        $lRet = array();
        foreach ($aMapArr as $lAlias => $lRow) {
            $lNative = $lRow['native'];
            if (substr($lNative, 0, 4) != 'ZLP_') {
                continue;
            }
            $lVal = isset($this->mJob[$lAlias]) ? $this->mJob[$lAlias] : '';
            if (0 == strlen($lVal)) { // cannot use empty as we need the value '0' sometimes
                continue;
            }
            $lFilter = $lRow['write_filter'];
            if (!empty($lFilter)) {
                $lVal = CApp_Filter::filter($lVal, $lFilter);
            }
            $lItem = new stdClass();
            $lItem->Name = $lNative;
            $lItem->Value = $lVal;
            $lRet[] = $lItem;
        }
        return $lRet;

    }

    protected function applyMap($aMapId) {
        $lMap = intval($aMapId);
        if (empty($lMap)) {
            return;
        }
        // get mapped fields
        $lMapArr = CCor_Res::get('fiemap', array('map_id' => $aMapId));
        $lRegex = '/^\/SalesOrder\/Partners\[(.*)\]$/';
        $lCharRegex = '/^\/Char\[(.*)\]$/';
        $lMatches = array();

        // apply the map
        foreach ($lMapArr as $lRow) {
            $lAlias = $lRow['alias'];
            $lNative = $lRow['native'];
            if (!empty($lRow['default_value'])) {
                $lVal = $lRow['default_value'];
            }
            if (!empty($lNative)) {
                $lValue = $this->mJob[$lNative];
                $lFilter = $lRow['write_filter'];
                if (!empty($lValue)) {
                    $lVal = $lValue;
                    if (!empty($lFilter)) {
                        $lVal = CApp_Filter::filter($lVal, $lFilter);
                    }
                }
            }
            $lVal = $this->handleSpecialValue($lVal);
            if (preg_match($lRegex, $lAlias, $lMatches)) {
                // special handling as we might need several and config should be like
                // /SalesOrder/Partners[AG] = 4711
                if (empty($lVal)) {
                    continue;
                }
                $lPartnerFunction = $lMatches[1];
                $lItm = new stdClass();
                $lItm->CustomerNumber = $lVal;
                $lItm->PartnerFunction = $lPartnerFunction;
                $this->mQry->addParamPath('/SalesOrder/Partners', $lItm);
            } elseif (preg_match($lCharRegex, $lAlias, $lMatches)) {
                // we may have to add several ZLP Sales Order Characteristics here
                $lItem = new stdClass();
                $lItem->Name = $lMatches[1];
                $lItem->Value = $lVal;
                $this->mQry->addParamPath('/SalesOrder/LineItems/Characteristics', $lItem);
            } else {
                //echo $lAlias.' to '.$lVal.BR;
                $this->mQry->setParamPath($lAlias, $lVal);
            }
        }
    }

    protected function handleSpecialValue($aValue) {
        if (substr($aValue, 0, 1) != '{') {
            // faster than regex, most values won't use a function
            return $aValue;
        }
        $lRet = $aValue;
        $lMatches = array();
        $lRegex = '/^\{([^\:]*)\:(.*)\}$/';
        if (preg_match($lRegex, $lRet, $lMatches)) {
            $lRet = $this->resolveSpecialValue($lRet, $lMatches[1], $lMatches[2]);
        }
        return $lRet;
    }

    protected function resolveSpecialValue($aVal, $aBase, $aParams) {
        $lRet = $aVal;
        if ('config' == $aBase) {
            return CCor_Cfg::get($aParams);
        }
        if ('date' == $aBase) {
            if ($aParams == 'today') {
                $lRet = date('Y-m-d');
            } elseif ('today.' == substr($aParams, 0, 6)) {
                $lArr = explode('.', $aParams);
                try {
                    $lInterval = $lArr[1];
                    $lDate = new DateTime();
                    $lDate->add(DateInterval::createFromDateString($lInterval));
                    return $lDate->format('Y-m-d');
                } catch (Exception $ex) {
                    $this->msg($ex->getMessage(), mtApi, mlError);
                    return null;
                }
            }
        }
        return $lRet;
    }

    protected function excludeMap($aArr, $aExMap) {
        if (empty($aArr)) {
            return $aArr;
        }
        $lMap = CCor_Res::extract('alias', 'alias', 'fiemap', array('map_id' => $aExMap));
        if (empty($lMap)) {
            return $aArr;
        }
        $lRet = $aArr;
        foreach ($lMap as $lAlias) {
            unset($lRet[$lAlias]);
        }
        return $lRet;
    }

    protected function trigger($aEventId) {
        $lEve = intval($aEventId);
        if (empty($lEve)) return;
        $lJob = $this->mContext['job'];
        $lEve = new CJob_Event($lEve, $lJob);
        $lEve -> execute();
    }

    protected static function getMaps() {
        $lMap = array();
        $lSql = 'SELECT id,name FROM al_fie_map_master ORDER BY name';
        $lQry = new CCor_Qry($lSql);
        foreach ($lQry as $lRow) {
            $lMap[$lRow['id']] = $lRow['name'];
        }
        return $lMap;
    }

    public static function getParamDefs($aType) {
        $lArr = array();
        $lMap = array('' => deHtm(NB)) + self::getMaps();
        $lFie = fie('map_base', 'BaseMap', 'select', $lMap);
        $lArr[] = $lFie;

        $lFie = fie('map_add', 'AddMap', 'select', $lMap);
        $lArr[] = $lFie;

        $lMap2 = array('' => deHtm(NB), 'all' => '['.lan('lib.all').']') + self::getMaps();
        $lFie = fie('map_char', 'CharMap', 'select', $lMap2);
        $lArr[] = $lFie;

        $lFie = fie('map_charex', 'CharExcludeMap', 'select', $lMap);
        $lArr[] = $lFie;

        $lResDef = array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN);
        $lFie = fie('event_ok', 'Event onSuccess', 'resselect', $lResDef);
        $lArr[] = $lFie;

        $lResDef = array('res' => 'eve', 'key' => 'id', 'val' => 'name_'.LAN);
        $lFie = fie('event_error', 'Event onError', 'resselect', $lResDef);
        $lArr[] = $lFie;

        return $lArr;
    }

    public static function paramToString($aParams) {
        $lRet = array();
        $lArr = self::getMaps();
        if (!empty($aParams['map_base'])) {
            $lMap = $aParams['map_base'];
            if (isset($lArr[$lMap])) {
                $lMap = $lArr[$lMap];
            }
            $lRet[] = 'BaseMap '. $lMap;
        }
        if (!empty($aParams['map_add'])) {
            $lMap = $aParams['map_add'];
            if (isset($lArr[$lMap])) {
                $lMap = $lArr[$lMap];
            }
            $lRet[]= 'AddMap '. $lMap;
        }
        if (!empty($aParams['map_char'])) {
            $lMap = $aParams['map_char'];
            if (isset($lArr[$lMap])) {
                $lMap = $lArr[$lMap];
            }
            $lRet[]= 'CharMap '. $lMap;
        }
        if (!empty($aParams['map_charex'])) {
            $lMap = $aParams['map_charex'];
            if (isset($lArr[$lMap])) {
                $lMap = $lArr[$lMap];
            }
            $lRet[]= 'CharExMap '. $lMap;
        }
        return implode(', ', $lRet);
    }

}
