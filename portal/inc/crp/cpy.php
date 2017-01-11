<?php
/**
 * CriticalPath: Copy
 *
 *  Copy a Critical Path with all status, steps, eventnr & flags
 *
 * @package    CRP
 * @copyright  Copyright (c) 2004-2010 QBF GmbH (http://www.qbf.de)
 * @version $Rev: 9231 $
 * @date $Date: 2015-06-22 12:01:14 +0200 (Mon, 22 Jun 2015) $
 * @author $Author: ahanslik $
 */
class CInc_Crp_Cpy extends CCor_Qry {

  public function __construct($aId) {
    $this -> mId = intval($aId);

    $lSql = 'SELECT max(`id`) as id FROM `al_crp_mand` WHERE `mand` = 0';
    $lQry = new CCor_Qry($lSql);
    $lRow = $lQry -> getDat();
    $this -> mCpyId = $lRow['id'] + 1;

    $lSql = "INSERT INTO `al_crp_mand` (`id`, `mand`) VALUES ('".$this -> mCpyId."', '0')";
    if ($lQry -> query($lSql)) {
      try {
        // nur wenn fuer einen Mandanten dieser CRP kopiert werden soll, z.B. Griesson Projekt-CRP 4 -> 9
        $this -> mAvailLang = CCor_Res::get('languages');

        $lSql = "INSERT INTO `al_crp_master`";
        $lSql.= " (`id`, `code`";
        foreach ($this -> mAvailLang as $lLang => $lName) {
          $lSql.= ", ".backtick('name_'.$lLang);
        }
        $lSql.= ", `eve_draft`, `eve_comment`, `eve_jobchange`, `eve_upload`, `eve_onhold`, `eve_continue`, `eve_cancel`, `eve_revive`, `eve_archive`, `eve_archive_condition`, `eve_archive_numberofjobs`)";
        $lSql.= " SELECT '".$this -> mCpyId."' ,`code`";
        foreach ($this -> mAvailLang as $lLang => $lName) {
          $lSql.= " ,CONCAT('Kopie von ',".backtick('name_'.$lLang).")";
        }
        $lSql.= ", `eve_draft`, `eve_comment`, `eve_jobchange`, `eve_upload`, `eve_onhold`, `eve_continue`, `eve_cancel`, `eve_revive`, `eve_archive`, `eve_archive_condition`, `eve_archive_numberofjobs`";
        $lSql.= " FROM `al_crp_master`  WHERE `id`=".$this -> mId;
        var_dump($lRow['id'],$lSql);
      } catch (Exception $e) {
        //
      }
    }
  }
}