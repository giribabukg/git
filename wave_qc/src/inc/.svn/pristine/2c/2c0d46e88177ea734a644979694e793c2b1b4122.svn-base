<?php
/**
 * Invite a user to view / approve a project
 *
 * This query can be used either to add or remove users to a Webcenter project.
 * Users can be added as viewer only or as approvers with additional rights
 *
 * @package    API
 * @subpackage Webcenter
 * @copyright  Copyright (c) 2004-2009 QBF GmbH (http://www.qbf.de)
 */


class CInc_Api_Wec_Query_Invite extends CApi_Wec_Query {

  public function add($aWecUid, $aWecPrj, $aAsApprover = TRUE) {
    $this -> setParam('usermemberid', $aWecUid);
    $this -> setParam('projectname', $aWecPrj);
    if ($aAsApprover) {
      $this -> setParam('approver', 1);
    }
    $lXml = $this -> query('InviteMemberToProject.jsp');
    $lRes = new CApi_Wec_Response($lXml);
    return $lRes -> isSuccess();
  }

  public function remove($aWecUid, $aWecPrj) {
    $this -> setParam('remove', 1);
    $this -> setParam('usermemberid', $aWecUid);
    $this -> setParam('projectname', $aWecPrj);
    $lXml = $this -> query('InviteMemberToProject.jsp');
    $lRes = new CApi_Wec_Response($lXml);
    return $lRes -> isSuccess();
  }

}