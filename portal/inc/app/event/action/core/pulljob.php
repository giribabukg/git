<?php
class CInc_App_Event_Action_Core_Pulljob extends CApp_Event_Action
{

    public function execute()
    {
        $this->mJob = $this->mContext['job'];
        $lJid = $this->mJob['jobid'];
        $lSrc = $this->mJob['src'];

        $lSql = 'SELECT * FROM al_core_xml WHERE jobid=' . esc($lJid) . ' AND src=' . esc($lSrc) . ' AND mand=' . MID;
        $lSql .= ' ORDER BY id DESC LIMIT 1';
        $lQry = new CCor_Qry($lSql);
        $lRow = $lQry->getDat();
        if (!$lRow) {
            return; // no success or error event triggered deliberately
        }

        $lMapId = $this->mParams['map'];
        if ('all' == $lMapId) {
            $lMapCode = 'all';
        } else {
            $lMaps = self::getMaps();
            if (!isset($lMaps[$lMapId])) {
                $this->msg('Unknown map ' . $lMapId . ' for pulljob', mtAdmin, mlError);
                return false;
            }
            $lMapCode = $lMaps[$lMapId];
        }
        $lMap = new CApi_Core_Map($lRow['xml']);
        $lValues = $lMap->getMappedValues($lMapCode);
        unset($lValues['jobid']); // never ever want to update that
        unset($lValues['src']);

        $lFac = new CJob_Fac($lSrc);
        $lMod = $lFac->getMod($lJid);
        foreach ($lValues as $lKey => $lVal) {
            $lMod->forceVal($lKey, $lVal);
        }
        $lSuccess = $lMod->update();
        if ($lSuccess) {
            $lSql = 'UPDATE al_core_xml SET `status`="processed",action="update",process_time=NOW() ';
            $lSql.= 'WHERE id='.$lRow['id'];
            CCor_Qry::exec($lSql);
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

    protected function trigger($aEventId)
    {
        $lEve = intval($aEventId);
        if (empty($lEve)) return;
        $lJob = $this->mContext['job'];
        $lEve = new CJob_Event($lEve, $lJob);
        $lEve->execute();
    }

    protected static function getMaps()
    {
        $lMap = array();
        $lSql = 'SELECT id,name FROM al_fie_map_master ORDER BY name';
        $lQry = new CCor_Qry($lSql);
        foreach ($lQry as $lRow) {
            $lMap[$lRow['id']] = $lRow['name'];
        }
        return $lMap;
    }

    public static function getParamDefs($aType)
    {
        $lArr = array();
        $lMap = array('' => deHtm(NB), 'all' => '['.lan('lib.all').']') + self::getMaps();

        $lFie = fie('map', 'Filter Map', 'select', $lMap);
        $lArr[] = $lFie;

        $lResDef = array('res' => 'eve', 'key' => 'id', 'val' => 'name_' . LAN);
        $lFie = fie('event_ok', 'Event onSuccess', 'resselect', $lResDef);
        $lArr[] = $lFie;

        $lResDef = array('res' => 'eve', 'key' => 'id', 'val' => 'name_' . LAN);
        $lFie = fie('event_error', 'Event onError', 'resselect', $lResDef);
        $lArr[] = $lFie;

        return $lArr;
    }

    public static function paramToString($aParams)
    {
        $lRet = array();
        $lArr = self::getMaps();
        if (!empty($aParams['map'])) {
            $lMap = $aParams['map'];
            if (isset($lArr[$lMap])) {
                $lMap = $lArr[$lMap];
            }
            $lRet[] = 'Filter Map ' . $lMap;
        }
        return implode(', ', $lRet);
    }

}
