<?php
/*
 * Testaufruf: ../index.php?act=sys-svc.run&id=11
 * Approval Loop Reminder
 *
 *
 * @package    SVC
 * @version $Rev: 9682 $
 * @date $Date: 2012-06-20 09:59:12 +0200 (Mi, 20 Jun 2012) $
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 * @author $Author: hoffmann $
 */
class CSvc_Rem extends CSvc_Base {

  protected $mClients; // stores client numbers in an array

  protected function doExecute() {
    if (!defined(MID) OR (0 == MID)) {
      return false;
    }
    $lEmailTpl = CCor_Cfg::get('svc.apl.tpl');
    if (!empty($lEmailTpl)) {
      $lTpl = new CApp_Tpl();
      if (is_int($lEmailTpl)) {
        $lTpl -> loadTemplate($lEmailTpl);
      } else {
        $lTpl -> loadTemplate(0, $lEmailTpl, LAN);
      }
      $lTpl -> setPat('from.firstname', CCor_Cfg::get('svc.apl.firstname'));
      $lTpl -> setPat('from.lastname',  CCor_Cfg::get('svc.apl.lastname'));
      $lTpl -> setPat('from.email',     CCor_Cfg::get('svc.apl.email'));
      $lTpl -> setPat('from.phone',     CCor_Cfg::get('svc.apl.phone'));

      $lToday = date("Y-m-d");
      $lStartDate = Date("Y-m-d", strtotime("-".CCor_Cfg::get('svc.apl.from', 0)." days"));
      $lStartDate = esc($lStartDate);
      $lEndDate = Date("Y-m-d");#, strtotime("+".CCor_Cfg::get('svc.apl.to', 0)." days"));
      $lEndDate = esc($lEndDate);//heute! oder??

      $this -> mClients = MID;

      if (!empty($this -> mClients)) {
        $lTim = 'SELECT st1.`loop_id` AS id, st1.`ddl`, lp.`jobid`, lp.`src`, st1.`user_id`, st1.`mand`';
        $lTim.= ' FROM `al_job_apl_loop` AS lp, `al_job_apl_states` AS st1';
        $lTim.= ' WHERE st1.`loop_id`=lp.`id`';
        $lTim.= ' AND lp.`mand` IN ('.$this -> mClients.')';
        $lTim.= ' AND lp.`typ`="apl" AND lp.`status`="open" AND st1.`done`="N" AND st1.`del`="N"';
        #$lTim.= ' AND st1.`start_date`>='.$lStartDate.' AND lp.`start_date`e<='.$lEndDate;
        $lTim.= ' AND st1.`ddl`>='.$lStartDate.' AND st1.`ddl`<'.$lEndDate;
        $lTim.= ' AND st1.`pos`=(SELECT MIN(st2.`pos`) FROM `al_job_apl_states` AS st2 WHERE st2.`loop_id`=lp.`id` AND st2.`del`="N" GROUP BY st2.`loop_id`);';

        $this -> mUsrDef = CCor_Res::get('usr');

        $lQry = new CCor_Qry($lTim);
        foreach ($lQry as $lRow) {
          $lUid = $lRow['user_id'];

          if (isset($this -> mUsrDef[$lUid])) {
            $lUser = $this -> mUsrDef[$lUid];
            $lTpl -> setPat('to.firstname', $lUser['firstname']);
            $lTpl -> setPat('to.lastname',  $lUser['lastname']);

            $lBase = CCor_Cfg::get('base.url');
            $lTpl -> setPat('link',     $lBase.'index.php?act=job-'.$lRow['src'].'.edt&jobid='.$lRow['jobid'].'&_mid='.$lRow['mand']);
            $lTpl -> setPat('link.apl', $lBase.'index.php?act=job-apl-page&src='.$lRow['src'].'&jid='.$lRow['jobid'].'&prtid='.$lRow['id'].'&_mid='.$lRow['mand']);

            $lFrom     = CCor_Cfg::get('svc.apl.frommail', '');
            $lFromName = CCor_Cfg::get('svc.apl.fromname', '');

            $lSql = 'SELECT b.val FROM al_usr a, al_usr_pref b WHERE a.id='.esc($lUid).' AND a.id=b.uid AND b.code="usr.onholiday" AND b.mand='.$lRow['mand'];
            $lIsOnHoliday = CCor_Qry::getStr($lSql);
            $lBackupUid = $lUser['backup'];

            if (isset($lIsOnHoliday) AND $lIsOnHoliday == 'Y' AND 0 < $lBackupUid AND isset($this -> mUsrDef[$lBackupUid])) {
              $lBackupUser = $this -> mUsrDef[$lBackupUid];
              $lTo     = $lBackupUser['email'];
              $lToName = $lBackupUser['first_lastname'];
            } else {
              $lTo     = $lUser['email'];
              $lToName = $lUser['first_lastname'];
            }

            $lSubject = $lTpl -> getSubject();
            $lText    = $lTpl -> getBody();

            $lMai = new CApi_Mail_Item($lFrom, $lFromName, $lTo, $lToName, $lSubject, $lText);
            $lMai -> insert('', $lRow['mand']);
          }
        }
      }
    }

    return true;
  }

}
